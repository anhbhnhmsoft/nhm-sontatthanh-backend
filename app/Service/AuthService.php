<?php

namespace App\Service;

use App\Core\Cache\CacheKey;
use App\Core\Cache\Caching;
use App\Core\LogHelper;
use App\Core\Service\BaseService;
use App\Core\Service\ServiceException;
use App\Core\Service\ServiceReturn;
use App\Enums\DirectFile;
use App\Enums\UserNotificationType;
use App\Enums\UserRole;
use App\Http\DTO\NotificationPayload;
use App\Http\Resources\UserResource;
use App\Jobs\SendNotificationJob;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AuthService extends BaseService
{
    protected int $otpTtl = 60;     // OTP tồn tại 1 phút
    protected int $blockTime = 60 * 60;   // Khóa 60 phút (tránh gửi OTP quá nhiều)
    protected int $maxAttempts = 5;  // Tối đa 5 lần thử sai
    protected int $maxResendOtp = 3;  // Tối đa 3 lần gửi OTP
    protected int $registerTimeout = 60 * 60;  // Thời gian chờ sau khi đăng ký

    public function __construct(
        protected User        $userModel,
        protected ZaloService $zaloService,
    ) {}

    /**
     * Authenticate with Zalo (Unified Login/Register)
     * @param string $accessToken - token của Zalo
     * @param string $ip - ip của client
     * @param string $token - token xác thực của client
     * @return ServiceReturn
     */
    public function authenticateWithZalo(string $accessToken, string $ip, string $token): ServiceReturn
    {
        try {
            // Step 1: Get Zalo Profile
            $zaloProfile = $this->zaloService->getUserProfile($accessToken);
            if (!$zaloProfile || !isset($zaloProfile['id'])) {
                return ServiceReturn::error('Không thể lấy thông tin từ Zalo. Token có thể đã hết hạn.');
            }

            $zaloId = $zaloProfile['id'];
            $name = $zaloProfile['name'] ?? '';
            $avatarUrl = $zaloProfile['avatar'] ?? null;

            $user = $this->userModel->where('zalo_id', $zaloId)->first();
            if (!$user) {
                $sales = $this->userModel->where('role', UserRole::SALE->value)->get();
                $data = [
                    'zalo_id' => $zaloId,
                    'phone' => null,
                    'name' => $name,
                    'role' => UserRole::CTV->value,
                    'joined_at' => now(),
                    'is_active' => true,
                    'avatar' => $avatarUrl,
                ];
                if ($sales->isNotEmpty()) {
                    $randomSale = $sales->random();
                    $data['sale_id'] = $randomSale->id;
                }

                $user = $this->userModel->create($data);
            }

            if (!$user->is_active) {
                return ServiceReturn::error('Tài khoản của bạn đang bị khóa');
            }
            $tokenAuth = $this->createTokenAuth($user);
            Caching::setCache(
                key: CacheKey::CACHE_ZALO_AUTH_TOKEN,
                value: [
                    'token' => $tokenAuth,
                    'user' => UserResource::make($user),
                ],
                uniqueKey: $ip . $token,
                expire: 60 * 5,
            );
            return ServiceReturn::success([
                'user' => UserResource::make($user),
                'token' => $tokenAuth,
            ], 'Xác thực Zalo thành công');
        } catch (\Throwable $th) {
            LogHelper::error('AuthService@authenticateWithZalo error: ' . $th->getMessage());
            return ServiceReturn::error('Có lỗi xảy ra khi xác thực Zalo');
        }
    }

    /**
     * Authenticate with Apple (Unified Login/Register)
     * @param array $tokenData - Response from Apple (contains id_token, etc.)
     * @param string $ip
     * @param string $token - token xác thực của client
     * @param array $fullName - mảng chứa thông tin tên (givenName, familyName)
     * @return ServiceReturn
     */
    public function authenticateWithApple(array $tokenData, string $ip, string $token, array $fullName): ServiceReturn
    {
        try {
            $idToken = $tokenData['id_token'];
            $parts = explode('.', $idToken);
            if (count($parts) != 3) {
                return ServiceReturn::error('Apple ID Token không hợp lệ');
            }

            $payload = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', $parts[1]))), true);
            LogHelper::debug('Apple ID Token Payload: ', $payload);
            if (!$payload) {
                return ServiceReturn::error('Không thể phân tích Apple ID Token');
            }

            $appleId = $payload['sub'];
            $email = $payload['email'] ?? null;

            $user = $this->userModel->where('apple_id', $appleId)->first();

            if (!$user && $email) {
                $user = $this->userModel->where('email', $email)->first();
                if ($user) {
                    // Link existing user
                    $user->apple_id = $appleId;
                    $user->save();
                }
            }

            if (!$user) {
                $sales = $this->userModel->where('role', UserRole::SALE->value)->get();
                $name = 'Apple User';
                if (!empty($fullName) && isset($fullName['givenName']) && isset($fullName['familyName'])) {
                    $name = $fullName['givenName'] . ' ' . $fullName['familyName'];
                } elseif (!empty($fullName) && isset($fullName['givenName'])) {
                    $name = $fullName['givenName'];
                } elseif ($email) {
                    $name = strstr($email, '@', true);
                }

                $data = [
                    'apple_id' => $appleId,
                    'email' => $email,
                    'phone' => null,
                    'name' => $name,
                    'role' => UserRole::CTV->value,
                    'joined_at' => now(),
                    'is_active' => true,
                    'avatar' => null,
                ];

                if ($sales->isNotEmpty()) {
                    $randomSale = $sales->random();
                    $data['sale_id'] = $randomSale->id;
                }

                $user = $this->userModel->create($data);
            }

            if (!$user->is_active) {
                return ServiceReturn::error('Tài khoản của bạn đang bị khóa');
            }

            $tokenAuth = $this->createTokenAuth($user);
            // Hash the token to prevent "value too long" error in cache key
            $hashedTokenKey = md5($token);

            Caching::setCache(
                key: CacheKey::CACHE_APPLE_AUTH_TOKEN,
                value: [
                    'token' => $tokenAuth,
                    'user' => $user,
                ],
                uniqueKey: $ip . '_' . $hashedTokenKey,
                expire: 60 * 5,
            );

            // Return the Sanctum token (tokenAuth), not the Identity Token
            return ServiceReturn::success([
                'user' => $user,
                'token' => $tokenAuth
            ], 'Xác thực Apple thành công');
        } catch (\Throwable $th) {
            LogHelper::error('AuthService@authenticateWithApple error: ' . $th->getMessage());
            return ServiceReturn::error('Có lỗi xảy ra khi xác thực Apple');
        }
    }

    /**
     * Get access token from Zalo authorization code
     * @param string $code
     * @param string $ip
     * @return string|null
     */
    public function getAccessTokenFromCode(string $code, string $ip): ?string
    {
        $accessToken = $this->zaloService->getAccessTokenFromCode($code, $ip);
        if (!$accessToken) {
            return null;
        }

        return $accessToken;
    }

    /**
     * Đăng nhập
     * @param string $phone
     * @param string $password
     * @return ServiceReturn
     */
    public function login(string $phone, string $password): ServiceReturn
    {
        try {
            // xác định thông tin đăng nhập
            if (!$phone || !$password) {
                return ServiceReturn::error('Yêu cầu nhập số điện thoại và mật khẩu', null);
            }
            // xác định unique số điện thoại
            $user = $this->userModel->where('phone', $phone)->first();
            if (!$user) {
                return ServiceReturn::error('Số điện thoại không tồn tại', null);
            }
            // xác định tài khoản đã khóa
            if ($user->is_active == false) {
                return ServiceReturn::error('Tài khoản của bạn đang bị khóa', null);
            }
            // xác định số điện thoại chưa được xác minh
            if ($user->phone_verified_at == null) {
                return ServiceReturn::error('Số điện thoại chưa được xác minh', null);
            }
            // xác định mật khẩu không chính xác
            if (!Hash::check($password, $user->password)) {
                return ServiceReturn::error('Mật khẩu không chính xác', null);
            }
            // tạo token
            $token = $this->createTokenAuth($user);
            return ServiceReturn::success([
                'user' => $user->load(['department', 'collaborators', 'cameras']),
                'token' => $token
            ], 'Đăng nhập thành công');
        } catch (\Throwable $th) {
            LogHelper::error(
                'Lỗi xảy ra ở AuthService@loginNormal :',
                $th
            );
            return ServiceReturn::error('Có lỗi xảy ra. Vui lòng thử lại sau', null);
        }
    }

    /**
     * Tạo token đăng nhập cho user.
     * @param User $user
     * @return string
     */
    protected function createTokenAuth(User $user): string
    {
        return $user->createToken(
            name: 'api-token',
            abilities: ['*'],
            expiresAt: now()->addDays(30),
        )->plainTextToken;
    }


    /**
     * Đăng ký tài khoản
     * @param string $phone
     * @param string $password
     * @param string $name
     * @return ServiceReturn
     */
    public function register(
        string $phone,
        string $password,
        string $name,
    ): ServiceReturn {
        try {
            if (!$phone || !$password || !$name) {
                return ServiceReturn::error(message: 'Nhập đầy đủ thông tin');
            }

            $user = $this->userModel->where('phone', $phone)->first();
            if ($user) {
                return ServiceReturn::error(message: 'Số điện thoại đã tồn tại');
            }

            $user = [
                'phone' => $phone,
                'password' => Hash::make($password),
                'name' => $name,
                'role' => UserRole::CTV->value,
                'joined_at' => now(),
                'is_active' => true,
                'phone_verified_at' => now(),
            ];

            // Lưu thông tin user chờ xác thực
            Caching::setCache(
                key: CacheKey::CACHE_KEY_REGISTER_TOKEN,
                value: [
                    $user,
                    request()->ip()
                ],
                uniqueKey: $phone,
                expire: $this->registerTimeout,
            );

            // Gửi OTP
            $sendOtp = $this->sendRegisterOtp($phone);
            if ($sendOtp->isError()) {
                return $sendOtp;
            }

            return ServiceReturn::success([
                'user' => $user,
            ], 'Vui lòng xác thực mã OTP');
        } catch (\Throwable $th) {
            LogHelper::error(
                'Lỗi xảy ra ở AuthService@register :',
                $th
            );
            return ServiceReturn::error('Có lỗi xảy ra. Vui lòng thử lại sau');
        }
    }

    /**
     * Gửi OTP đăng ký
     * @param string $phone
     * @return ServiceReturn
     */
    public function sendRegisterOtp(string $phone): ServiceReturn
    {
        try {

            // CHECK: Phone đã tồn tại chưa?
            $existingUser = $this->userModel->where('phone', $phone)->first();
            if ($existingUser) {
                return ServiceReturn::error('Số điện thoại đã được đăng ký');
            }
            // xác định trạng thái block gửi otp
            $isBlocked = Caching::getCache(
                key: CacheKey::CACHE_KEY_OTP_REGISTER_BLOCK,
                uniqueKey: $phone
            );
            // nếu block
            if ($isBlocked) {
                return ServiceReturn::error(
                    'Bạn đã gửi quá nhiều OTP. Vui lòng thử lại sau ' . ($this->blockTime / 60) . ' phút.'
                );
            }
            // Lấy OTP đã gửi để kiểm tra
            $subotp = Caching::getCache(
                key: CacheKey::CACHE_KEY_OTP_REGISTER,
                uniqueKey: $phone
            );
            // nếu đã gửi otp
            if ($subotp) {
                return ServiceReturn::error('OTP đã được gửi, vui lòng kiểm tra lại');
            }

            // $otp = rand(100000, 999999);
            $otp = 888888;
            // lưu otp vào cache
            Caching::setCache(
                key: CacheKey::CACHE_KEY_OTP_REGISTER,
                value: [
                    $otp,
                    request()->ip()
                ],
                uniqueKey: $phone,
                expire: $this->otpTtl,
            );
            return ServiceReturn::success(null, 'Gửi OTP thành công');
        } catch (\Throwable $th) {
            LogHelper::error(
                'Lỗi xảy ra ở AuthService@sendRegisterOtp :',
                $th
            );
            return ServiceReturn::error('Có lỗi xảy ra. Vui lòng thử lại sau');
        }
    }

    /**
     * Gửi lại  OTP đăng ký
     * @param string $phone
     * @return ServiceReturn
     */
    public function resendRegisterOtp(string $phone): ServiceReturn
    {
        try {
            // xác định trạng thái block gửi otp
            $isBlocked = Caching::getCache(
                key: CacheKey::CACHE_KEY_OTP_REGISTER_BLOCK,
                uniqueKey: $phone
            );
            // nếu block
            if ($isBlocked) {
                return ServiceReturn::error(
                    'Bạn đã gửi quá nhiều OTP. Vui lòng thử lại sau ' . ($this->blockTime / 60) . ' phút.'
                );
            }
            // xác định số lần gửi otp
            $timesSend = $this->countCacheRegisterSendOtp($phone);
            // nếu số lần gửi otp vượt quá số lần cho phép
            if ($timesSend > $this->maxResendOtp) {
                Caching::setCache(
                    key: CacheKey::CACHE_KEY_OTP_REGISTER_BLOCK,
                    value: true,
                    uniqueKey: $phone,
                    expire: $this->blockTime, // phút -> giây
                );

                return ServiceReturn::error('Đã gửi quá số lần cho phép');
            }

            // Xóa OTP cũ để gửi cái mới
            Caching::deleteCache(
                key: CacheKey::CACHE_KEY_OTP_REGISTER,
                uniqueKey: $phone
            );

            return $this->sendRegisterOtp($phone);
        } catch (\Throwable $th) {
            LogHelper::error(
                'Lỗi xảy ra ở AuthService@resendRegisterOtp :',
                $th
            );
            return ServiceReturn::error('Có lỗi xảy ra. Vui lòng thử lại sau');
        }
    }

    /**
     * Tạo cache đếm số lần gửi otp
     * @param string $phone
     * @throws ServiceException
     */
    protected function countCacheRegisterSendOtp(string $phone): int
    {
        // xác định trạng thái cache số lần gửi otp
        $hasCacheCount = Caching::hasCache(
            key: CacheKey::CACHE_KEY_RESEND_REGISTER_OTP,
            uniqueKey: $phone,
        );
        if ($hasCacheCount) {
            // tăng số lần gửi otp
            $count = Caching::incrementCache(
                key: CacheKey::CACHE_KEY_RESEND_REGISTER_OTP,
                uniqueKey: $phone,
            );
        } else {
            // tạo cache số lần gửi otp
            $count = Caching::setCache(
                key: CacheKey::CACHE_KEY_RESEND_REGISTER_OTP,
                value: 1,
                uniqueKey: $phone,
                expire: $this->registerTimeout,
            );
        }
        return $count;
    }

    /**
     * Tạo cache đếm số lần xác thực đăng ký bằng otp
     * @param string $phone
     * @throws ServiceException
     */
    protected function countCacheVerifyOtpRegister(string $phone): int
    {
        // xác định trạng thái cache số  nhập opt
        $hasCacheCount = Caching::hasCache(
            key: CacheKey::CACHE_KEY_OTP_REGISTER_ATTEMPTS,
            uniqueKey: $phone,
        );
        if ($hasCacheCount) {
            // tăng số lần nhập opt
            $count = Caching::incrementCache(
                key: CacheKey::CACHE_KEY_OTP_REGISTER_ATTEMPTS,
                uniqueKey: $phone,
            );
        } else {
            // tạo cache số lần nhập opt
            $count = Caching::setCache(
                key: CacheKey::CACHE_KEY_OTP_REGISTER_ATTEMPTS,
                value: 1,
                uniqueKey: $phone,
                expire: $this->registerTimeout,
            );
        }
        return $count;
    }

    /**
     * Xác thực  đăng ký tài khoản bằng OTP.
     * @param string $phone
     * @param string $otp
     * @return ServiceReturn
     */
    public function verifyOtpRegister(string $phone, string $otp): ServiceReturn
    {
        try {

            $count = $this->countCacheVerifyOtpRegister($phone);
            if ($count > $this->maxAttempts) {
                return ServiceReturn::error('Đã thử quá số lần cho phép');
            }

            // lấy OTP đã gửi
            $cache = Caching::getCache(
                key: CacheKey::CACHE_KEY_OTP_REGISTER,
                uniqueKey: $phone,
            );

            if (!$cache) {
                return ServiceReturn::error('Mã OTP đã hết hạn hoặc không tồn tại');
            }

            $otpSended = $cache[0];
            $ipSended = $cache[1];

            // Kiểm tra OTP và IP khớp
            if ($otpSended != $otp) {
                return ServiceReturn::error('Mã OTP không chính xác');
            }

            if ($ipSended != request()->ip()) {
                return ServiceReturn::error('Phiên làm việc không hợp lệ (IP)');
            }

            // lấy User temp
            $pseudoUserCache = Caching::getCache(
                key: CacheKey::CACHE_KEY_REGISTER_TOKEN,
                uniqueKey: $phone,
            );

            if (!$pseudoUserCache) {
                return ServiceReturn::error('Thông tin đăng ký không tìm thấy hoặc đã hết hạn');
            }

            // lây thông tin user đăng ký
            $userReal = $pseudoUserCache[0];

            // tiến hành ghi vào db
            $user = $this->userModel->create($userReal);


            // Xóa cache
            Caching::deleteCache(key: CacheKey::CACHE_KEY_OTP_REGISTER, uniqueKey: $phone);
            Caching::deleteCache(key: CacheKey::CACHE_KEY_REGISTER_TOKEN, uniqueKey: $phone);
            Caching::deleteCache(key: CacheKey::CACHE_KEY_RESEND_REGISTER_OTP, uniqueKey: $phone);
            Caching::deleteCache(key: CacheKey::CACHE_KEY_OTP_REGISTER_ATTEMPTS, uniqueKey: $phone);

            return ServiceReturn::success([
                'user' => $user->load(['department', 'collaborators', 'cameras']),
                'token' => $this->createTokenAuth($user),
            ], 'Xác thực và đăng ký thành công');
        } catch (\Throwable $th) {
            LogHelper::error(
                'Lỗi xảy ra ở AuthService@verifyOtpRegister :',
                $th
            );
            return ServiceReturn::error('Có lỗi xảy ra. Vui lòng thử lại sau');
        }
    }

    /**
     * Logout
     */
    public function logout(): ServiceReturn
    {
        try {
            /**
             *Xóa token của user
             * @var \App\Models\User $user
             */
            $user = Auth::user();
            $user->tokens()->delete();

            return ServiceReturn::success(null, 'Đăng xuất thành công');
        } catch (\Throwable $th) {
            LogHelper::error(
                'Lỗi xảy ra ở AuthService@logout :',
                $th
            );
            return ServiceReturn::error('Có lỗi xảy ra. Vui lòng thử lại sau');
        }
    }

    /**
     * Cập nhật thông tin user
     * @param ?string $name
     * @param ?UploadedFile $avatar
     * @param ?string $oldPassword
     * @param ?string $newPassword
     * @param ?string $email
     * @param ?string $phone
     * @return ServiceReturn
     */
    public function editProfile(?string $name, ?UploadedFile $avatar, ?string $oldPassword, ?string $newPassword, ?string $email, ?string $phone): ServiceReturn
    {
        try {
            /**
             * @var \App\Models\User $user
             */
            $user = Auth::user();
            if ($oldPassword && $newPassword) {
                if (!Hash::check($oldPassword, $user->password)) {
                    return ServiceReturn::error('Mật khẩu cũ không chính xác');
                }
                $user->password = $newPassword;
            }
            if ($name && isset($name)) {
                $user->name = $name;
            }
            if ($avatar && $avatar->isValid()) {
                $user->avatar = $avatar->store(DirectFile::AVATARS->value, 'public');
            }
            if ($email && isset($email)) {
                $user->email = $email;
            }
            if ($phone && isset($phone)) {
                $user->phone = $phone;
            }
            $user->save();

            return ServiceReturn::success($user, 'Cập nhật thông tin thành công');
        } catch (\Throwable $th) {
            LogHelper::error(
                'Lỗi xảy ra ở AuthService@editProfile :',
                $th
            );
            return ServiceReturn::error('Có lỗi xảy ra. Vui lòng thử lại sau');
        }
    }

    /**
     * Quên mật khẩu
     * @param string $phone
     * @param string $password
     * @return ServiceReturn
     */

    /**
     * Gửi OTP quên mật khẩu
     * @param string $phone
     * @return ServiceReturn
     */
    public function sendForgotPasswordOtp(string $phone): ServiceReturn
    {
        try {
            // CHECK: Phone đã tồn tại chưa?
            $user = $this->userModel->where('phone', $phone)->first();
            if (!$user) {
                return ServiceReturn::error('Số điện thoại chưa được đăng ký');
            }

            // xác định trạng thái block gửi otp
            $isBlocked = Caching::getCache(
                key: CacheKey::CACHE_KEY_OTP_FORGOT_PASSWORD_BLOCK,
                uniqueKey: $phone
            );
            // nếu block
            if ($isBlocked) {
                return ServiceReturn::error(
                    'Bạn đã gửi quá nhiều OTP. Vui lòng thử lại sau ' . ($this->blockTime / 60) . ' phút.'
                );
            }
            // Lấy OTP đã gửi để kiểm tra
            $subotp = Caching::getCache(
                key: CacheKey::CACHE_KEY_OTP_FORGOT_PASSWORD,
                uniqueKey: $phone
            );
            // nếu đã gửi otp
            if ($subotp) {
                return ServiceReturn::error('OTP đã được gửi, vui lòng kiểm tra lại');
            }

            // $otp = rand(100000, 999999);
            $otp = 888888;
            // lưu otp vào cache
            Caching::setCache(
                key: CacheKey::CACHE_KEY_OTP_FORGOT_PASSWORD,
                value: [
                    $otp,
                    request()->ip()
                ],
                uniqueKey: $phone,
                expire: $this->otpTtl,
            );
            return ServiceReturn::success(null, 'Gửi OTP thành công');
        } catch (\Throwable $th) {
            LogHelper::error(
                'Lỗi xảy ra ở AuthService@sendForgotPasswordOtp :',
                $th
            );
            return ServiceReturn::error('Có lỗi xảy ra. Vui lòng thử lại sau');
        }
    }

    /**
     * Gửi lại OTP quên mật khẩu
     * @param string $phone
     * @return ServiceReturn
     */
    public function resendForgotPasswordOtp(string $phone): ServiceReturn
    {
        try {
            // xác định trạng thái block gửi otp
            $isBlocked = Caching::getCache(
                key: CacheKey::CACHE_KEY_OTP_FORGOT_PASSWORD_BLOCK,
                uniqueKey: $phone
            );
            // nếu block
            if ($isBlocked) {
                return ServiceReturn::error(
                    'Bạn đã gửi quá nhiều OTP. Vui lòng thử lại sau ' . ($this->blockTime / 60) . ' phút.'
                );
            }
            // xác định số lần gửi otp
            $timesSend = $this->countCacheForgotPasswordSendOtp($phone);
            // nếu số lần gửi otp vượt quá số lần cho phép
            if ($timesSend > $this->maxResendOtp) {
                Caching::setCache(
                    key: CacheKey::CACHE_KEY_OTP_FORGOT_PASSWORD_BLOCK,
                    value: true,
                    uniqueKey: $phone,
                    expire: $this->blockTime, // phút -> giây
                );

                return ServiceReturn::error('Đã gửi quá số lần cho phép');
            }

            // Xóa OTP cũ để gửi cái mới
            Caching::deleteCache(
                key: CacheKey::CACHE_KEY_OTP_FORGOT_PASSWORD,
                uniqueKey: $phone
            );

            return $this->sendForgotPasswordOtp($phone);
        } catch (\Throwable $th) {
            LogHelper::error(
                'Lỗi xảy ra ở AuthService@resendForgotPasswordOtp :',
                $th
            );
            return ServiceReturn::error('Có lỗi xảy ra. Vui lòng thử lại sau');
        }
    }

    /**
     * Tạo cache đếm số lần gửi otp quên mật khẩu
     * @param string $phone
     * @throws ServiceException
     */
    protected function countCacheForgotPasswordSendOtp(string $phone): int
    {
        // xác định trạng thái cache số lần gửi otp
        $hasCacheCount = Caching::hasCache(
            key: CacheKey::CACHE_KEY_RESEND_FORGOT_PASSWORD_OTP,
            uniqueKey: $phone,
        );
        if ($hasCacheCount) {
            // tăng số lần gửi otp
            $count = Caching::incrementCache(
                key: CacheKey::CACHE_KEY_RESEND_FORGOT_PASSWORD_OTP,
                uniqueKey: $phone,
            );
        } else {
            // tạo cache số lần gửi otp
            $count = Caching::setCache(
                key: CacheKey::CACHE_KEY_RESEND_FORGOT_PASSWORD_OTP,
                value: 1,
                uniqueKey: $phone,
                expire: $this->registerTimeout,
            );
        }
        return $count;
    }

    /**
     * Tạo cache đếm số lần xác thực quên mật khẩu bằng otp
     * @param string $phone
     * @throws ServiceException
     */
    protected function countCacheVerifyOtpForgotPassword(string $phone): int
    {
        // xác định trạng thái cache số  nhập opt
        $hasCacheCount = Caching::hasCache(
            key: CacheKey::CACHE_KEY_OTP_FORGOT_PASSWORD_ATTEMPTS,
            uniqueKey: $phone,
        );
        if ($hasCacheCount) {
            // tăng số lần nhập opt
            $count = Caching::incrementCache(
                key: CacheKey::CACHE_KEY_OTP_FORGOT_PASSWORD_ATTEMPTS,
                uniqueKey: $phone,
            );
        } else {
            // tạo cache số lần nhập opt
            $count = Caching::setCache(
                key: CacheKey::CACHE_KEY_OTP_FORGOT_PASSWORD_ATTEMPTS,
                value: 1,
                uniqueKey: $phone,
                expire: $this->registerTimeout,
            );
        }
        return $count;
    }

    /**
     * Xác thực OTP quên mật khẩu
     * @param string $phone
     * @param string $otp
     * @return ServiceReturn
     */
    public function verifyForgotPasswordOtp(string $phone, string $otp): ServiceReturn
    {
        try {
            $count = $this->countCacheVerifyOtpForgotPassword($phone);
            if ($count > $this->maxAttempts) {
                return ServiceReturn::error('Đã thử quá số lần cho phép');
            }

            // lấy OTP đã gửi
            $cache = Caching::getCache(
                key: CacheKey::CACHE_KEY_OTP_FORGOT_PASSWORD,
                uniqueKey: $phone,
            );

            if (!$cache) {
                return ServiceReturn::error('Mã OTP đã hết hạn hoặc không tồn tại');
            }

            $otpSended = $cache[0];
            $ipSended = $cache[1];

            // Kiểm tra OTP và IP khớp
            if ($otpSended != $otp) {
                return ServiceReturn::error('Mã OTP không chính xác');
            }

            if ($ipSended != request()->ip()) {
                return ServiceReturn::error('Phiên làm việc không hợp lệ (IP)');
            }

            // Tạo token reset password
            $resetToken = Str::random(60);
            Caching::setCache(
                key: CacheKey::CACHE_KEY_FORGOT_PASSWORD_TOKEN,
                value: $resetToken,
                uniqueKey: $phone,
                expire: 10 * 60 // 10 phút
            );

            // Xóa cache OTP
            Caching::deleteCache(key: CacheKey::CACHE_KEY_OTP_FORGOT_PASSWORD, uniqueKey: $phone);
            Caching::deleteCache(key: CacheKey::CACHE_KEY_RESEND_FORGOT_PASSWORD_OTP, uniqueKey: $phone);
            Caching::deleteCache(key: CacheKey::CACHE_KEY_OTP_FORGOT_PASSWORD_ATTEMPTS, uniqueKey: $phone);

            return ServiceReturn::success([
                'reset_token' => $resetToken
            ], 'Xác thực thành công');
        } catch (\Throwable $th) {
            LogHelper::error(
                'Lỗi xảy ra ở AuthService@verifyForgotPasswordOtp :',
                $th
            );
            return ServiceReturn::error('Có lỗi xảy ra. Vui lòng thử lại sau');
        }
    }

    /**
     * Quên mật khẩu (Reset Password)
     * @param string $phone
     * @param string $password
     * @param string|null $token
     * @return ServiceReturn
     */
    public function forgotPassword(string $phone, string $password, ?string $token = null): ServiceReturn
    {
        try {
            // Check token
            $cachedToken = Caching::getCache(
                key: CacheKey::CACHE_KEY_FORGOT_PASSWORD_TOKEN,
                uniqueKey: $phone
            );

            if (!$token || !$cachedToken || $token !== $cachedToken) {
                // Nếu không có token truyền vào, thử kiểm tra xem có phải gọi từ flow cũ không hoặc trả lỗi
                // Ở đây strict: Phải có token hợp lệ
                return ServiceReturn::error('Phiên thay đổi mật khẩu không hợp lệ hoặc đã hết hạn');
            }

            $user = $this->userModel->where('phone', $phone)->first();
            if (!$user) {
                return ServiceReturn::error('Số điện thoại không tồn tại');
            }

            $user->password = Hash::make($password);
            $user->save();

            // Xóa token sau khi dùng
            Caching::deleteCache(key: CacheKey::CACHE_KEY_FORGOT_PASSWORD_TOKEN, uniqueKey: $phone);

            return ServiceReturn::success($user, 'Cập nhật mật khẩu thành công');
        } catch (\Throwable $th) {
            LogHelper::error(
                'Lỗi xảy ra ở AuthService@forgotPassword :',
                $th
            );
            return ServiceReturn::error('Có lỗi xảy ra. Vui lòng thử lại sau');
        }
    }

    /**
     * Chỉnh sửa avatar người dùng.
     * @param  $file
     * @return ServiceReturn
     */
    public function editInfoAvatar($file): ServiceReturn
    {
        try {
            $user = $this->userModel->find(Auth::id());
            if (!$user) {
                return ServiceReturn::error(message: 'Người dùng không tồn tại');
            }
            if (!$file instanceof UploadedFile) {
                return ServiceReturn::error(message: 'Hình ảnh avatar không hợp lệ');
            }
            $avatarPathNew = $file->store(DirectFile::makePathById(
                type: DirectFile::AVATARS,
                id: $user->id
            ), 'public');
            if (!$avatarPathNew) {
                return ServiceReturn::error(message: 'Lỗi khi lưu hình ảnh avatar');
            }
            // Xóa avatar cũ nếu có
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            // Cập nhật avatar_path trong bảng users
            $user->avatar = $avatarPathNew;
            $user->save();
            return ServiceReturn::success(
                data: $user
            );
        } catch (\Throwable $exception) {
            LogHelper::error(
                message: "Lỗi AuthService@editInfoAvatar",
                ex: $exception
            );
            return ServiceReturn::error(message: $exception->getMessage());
        }
    }

    /**
     * Xóa avatar người dùng.
     * @return ServiceReturn
     */
    public function deleteAvatar(): ServiceReturn
    {
        try {
            $user = $this->userModel->find(Auth::id());
            if (!$user) {
                return ServiceReturn::error(message: 'Người dùng không tồn tại');
            }
            // Xóa avatar cũ nếu có
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = null;
            $user->save();
            return ServiceReturn::success(
                data: $user
            );
        } catch (\Throwable  $exception) {
            LogHelper::error(
                message: "Lỗi AuthService@deleteAvatar",
                ex: $exception
            );
            return ServiceReturn::error(message: 'Có lỗi xảy ra. Vui lòng thử lại sau');
        }
    }

    public function deleteAccount()
    {
        try {
            $user = $this->userModel->find(Auth::id());
            if (!$user) {
                return ServiceReturn::error(message: 'Người dùng không tồn tại');
            }
            // Xóa avatar cũ nếu có
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->is_active = false;
            $user->save();
            Auth::user()->tokens()->delete();
            // Auth::logout();
            return ServiceReturn::success(
                message: 'Xóa tài khoản thành công'
            );
        } catch (\Throwable  $exception) {
            LogHelper::error(
                message: "Lỗi AuthService@deleteAccount",
                ex: $exception
            );
            return ServiceReturn::error(message: 'Có lỗi xảy ra. Vui lòng thử lại sau');
        }
    }
}
