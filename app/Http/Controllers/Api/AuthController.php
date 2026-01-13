<?php

namespace App\Http\Controllers\Api;
use App\Core\Controller\BaseController;
use App\Core\LogHelper;
use App\Http\Requests\Auth\EditProfileRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Resources\UserResource;
use App\Service\AuthService;
use App\Service\AppleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseController
{
    protected AuthService $authService;
    protected AppleService $appleService;

    public function __construct(AuthService $authService, AppleService $appleService)
    {
        $this->authService = $authService;
        $this->appleService = $appleService;
    }

    /**
     * Đăng nhập
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(
            $request->validated('phone'),
            $request->validated('password')
        );

        if ($result->isError()) {
            return $this->sendError(
                $result->getMessage(),
            );
        }

        $user = $result->getData()['user'];
        $token = $result->getData()['token'];
        return $this->sendSuccess(
            [
                'user' => UserResource::make($user),
                'token' => $token,
            ],
            $result->getMessage()
        );
    }

    /**
     * Đăng ký tài khoản
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register(
            $request->validated('phone'),
            $request->validated('password'),
            $request->validated('name')
        );

        if ($result->isError()) {
            return $this->sendError(
                $result->getMessage(),
            );
        }

        return $this->sendSuccess(
            $result->getMessage()
        );
    }

    /**
     * Gửi lại OTP
     */
    public function resendOtp(ResendOtpRequest $request): JsonResponse
    {
        $result = $this->authService->resendRegisterOtp(
            $request->validated('phone')
        );

        if ($result->isError()) {
            return $this->sendError(
                $result->getMessage(),
            );
        }

        return $this->sendSuccess(
            $result->getMessage()
        );
    }

    /**
     * Xác thực OTP
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $result = $this->authService->verifyOtpRegister(
            $request->validated('phone'),
            $request->validated('otp')
        );

        if ($result->isError()) {
            return $this->sendError(
                $result->getMessage(),
            );
        }
        $user = $result->getData()['user'];
        return $this->sendSuccess(
            [
                'user' => UserResource::make($user),
            ],
            $result->getMessage()
        );
    }

    /**
     * Đăng xuất
     */
    public function logout(): JsonResponse
    {
        $result = $this->authService->logout();

        if ($result->isError()) {
            return $this->sendError(
                $result->getMessage(),
            );
        }

        return $this->sendSuccess(
            $result->getMessage()
        );
    }

    /**
     * Lấy thông tin user
     */
    public function me(): JsonResponse
    {
        $result = Auth::user();

        return $this->sendSuccess(
            [
                'user' => UserResource::make($result),
            ]
        );
    }

    public function editProfile(EditProfileRequest $request): JsonResponse
    {
        $result = $this->authService->editProfile(
            $request->validated('name'),
            $request->validated('avatar'),
            $request->validated('old_password'),
            $request->validated('new_password'),
            $request->validated('email'),
            $request->validated('phone'),
        );

        if ($result->isError()) {
            return $this->sendError(
                $result->getMessage(),
            );
        }

        return $this->sendSuccess(
            data: [
                'user' => UserResource::make(Auth::user()),
            ],
            message: $result->getMessage()
        );
    }

    /**
     * Quên mật khẩu
     */
    /**
     * Gửi OTP quên mật khẩu
     */
    public function sendForgotPasswordOtp(ResendOtpRequest $request): JsonResponse
    {
        $result = $this->authService->sendForgotPasswordOtp(
            $request->validated('phone')
        );

        if ($result->isError()) {
            return $this->sendError(
                $result->getMessage(),
            );
        }

        return $this->sendSuccess(
            $result->getMessage()
        );
    }

    /**
     * Xác thực OTP quên mật khẩu
     */
    public function verifyForgotPasswordOtp(VerifyOtpRequest $request): JsonResponse
    {
        $result = $this->authService->verifyForgotPasswordOtp(
            $request->validated('phone'),
            $request->validated('otp')
        );

        if ($result->isError()) {
            return $this->sendError(
                $result->getMessage(),
            );
        }

        $token = $result->getData()['reset_token'];
        return $this->sendSuccess(
            [
                'reset_token' => $token,
            ],
            $result->getMessage()
        );
    }

    /**
     * Quên mật khẩu
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $result = $this->authService->forgotPassword(
            $request->validated('phone'),
            $request->validated('password'),
            $request->validated('token'),
        );

        if ($result->isError()) {
            return $this->sendError(
                $result->getMessage(),
            );
        }

        return $this->sendSuccess(
            $result->getMessage()
        );
    }

    /**
     * Xác thực bằng Zalo (Unified Login/Register)
     * Supports both:
     * - access_token: Direct token from mobile (deprecated)
     * - code: OAuth authorization code (recommended)
     */
    public function zaloAuthenticate(Request $request): JsonResponse
    {
        $request->validate([
            'access_token' => 'required_without:code|string',
            'code' => 'required_without:access_token|string',
        ], [
            'access_token.required_without' => 'Access token hoặc code không được để trống',
            'code.required_without' => 'Access token hoặc code không được để trống',
        ]);

        $accessToken = $request->input('access_token');
        $code = $request->input('code');

        // If code is provided, exchange it for access_token
        if ($code && !$accessToken) {
            $accessToken = $this->authService->getAccessTokenFromCode($code, $request->ip());
            if (!$accessToken) {
                return $this->sendError(
                    'Không thể lấy access token từ Zalo',
                    400,
                );
            }
        }

        $result = $this->authService->authenticateWithZalo($accessToken, $request->ip(), $accessToken);

        if ($result->isError()) {
            return $this->sendError(
                $result->getMessage(),
                400,
            );
        }

        $user = $result->getData()['user'];
        $token = $result->getData()['token'];

        return $this->sendSuccess(
            [
                'user' => UserResource::make($user),
                'token' => $token,
            ],
            $result->getMessage()
        );
    }

    /**
     * Xác thực bằng Apple (Native SDK)
     */
    public function appleAuthenticate(Request $request): JsonResponse
    {
        $request->validate([
            'identityToken' => 'required|string',
            'fullName' => 'nullable|array',
            'email' => 'nullable|string|email',
        ], [
            'identityToken.required' => 'Identity Token không được để trống',
            'fullName.array' => 'Full Name phải là mảng',
            'email.email' => 'Email không hợp lệ',
        ]);

        $identityToken = $request->input('identityToken');
        $fullName = $request->input('fullName');
        $email = $request->input('email');

        $payload = $this->appleService->verifyIdentityToken($identityToken);
        if (!$payload) {
            return $this->sendError(
                'Identity Token không hợp lệ',
                400,
            );
        }
        $result = $this->authService->authenticateWithApple(['id_token' => $identityToken], $request->ip(), $identityToken, $fullName);

        if ($result->isError()) {
            return $this->sendError(
                $result->getMessage(),
                400,
            );
        }

        $user = $result->getData()['user'];
        $token = $result->getData()['token'];

        return $this->sendSuccess(
            [
                'user' => UserResource::make($user),
                'token' => $token,
            ],
            $result->getMessage()
        );
    }

    public function editAvatar(Request $request): JsonResponse
    {
        $data = $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg|max:102400'
        ], [
            'file.required' => 'Hình ảnh avatar không được để trống',
            'file.image' => 'Hình ảnh avatar phải là file hình ảnh',
            'file.mimes' => 'Hình ảnh avatar phải có định dạng jpeg, png, jpg',
            'file.max' => 'Hình ảnh avatar không được vượt quá 10MB',
        ]);
        $result = $this->authService->editInfoAvatar(
            file: $data['file'],
        );
        if ($result->isError()) {
            return $this->sendError(
                message: $result->getMessage(),
            );
        }
        return $this->sendSuccess(
            data: [
                'user' => new UserResource($result->getData()),
            ],
        );
    }

    /**
     * Xóa avatar người dùng.
     * @return JsonResponse
     */
    public function deleteAvatar(): JsonResponse
    {
        $result = $this->authService->deleteAvatar();
        if ($result->isError()) {
            return $this->sendError(
                message: $result->getMessage(),
            );
        }
        return $this->sendSuccess(
            data: [
                'user' => new UserResource($result->getData()),
            ],
        );
    }

    public function getError(Request $request): JsonResponse
    {
        LogHelper::error('Get Error: ',null ,$request->all());
        return $this->sendError(
            message: 'Lỗi không xác định',
        );
    }
}
