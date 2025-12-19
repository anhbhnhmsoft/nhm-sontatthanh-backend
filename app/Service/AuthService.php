<?php

namespace App\Service;

use App\Core\Cache\CacheKey;
use App\Core\Cache\Caching;
use App\Core\LogHelper;
use App\Core\Service\BaseService;
use App\Core\Service\ServiceException;
use App\Core\Service\ServiceReturn;
use App\Enums\DirectFile;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService extends BaseService
{
    protected int $otpTtl = 60;     // OTP tồn tại 1 phút
    protected int $blockTime = 60 * 60;   // Khóa 60 phút (tránh gửi OTP quá nhiều)
    protected int $maxAttempts = 5;  // Tối đa 5 lần thử sai
    protected int $maxResendOtp = 3;  // Tối đa 3 lần gửi OTP
    protected int $registerTimeout = 60 * 60;  // Thời gian chờ sau khi đăng ký

    public function __construct(
        protected User $userModel,
    ) {}

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
                'user' => $user->load(['department', 'managedSales', 'cameras']),
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
                    'Bạn đã gửi quá nhiều OTP. Vui lòng thử lại sau ' . $this->blockTime . ' phút.'
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
                    'Bạn đã gửi quá nhiều OTP. Vui lòng thử lại sau ' . $this->blockTime . ' phút.'
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
                    expire: $this->blockTime * 60, // phút -> giây
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
                'user' => $user->load(['department', 'managedSales', 'cameras']),
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
     */
    public function editProfile(?string $name, ?UploadedFile $avatar, ?string $oldPassword, ?string $newPassword): ServiceReturn
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
    public function forgotPassword(string $phone, string $password): ServiceReturn
    {
        try {
            
            $user = $this->userModel->where('phone', $phone)->first();
            if (!$user) {
                return ServiceReturn::error('Số điện thoại không tồn tại');
            }

            $user->password = Hash::make($password);
            $user->save();

            return ServiceReturn::success($user, 'Cập nhật mật khẩu thành công');
        } catch (\Throwable $th) {
            LogHelper::error(
                'Lỗi xảy ra ở AuthService@forgotPassword :',
                $th
            );
            return ServiceReturn::error('Có lỗi xảy ra. Vui lòng thử lại sau');
        }
    }
}
