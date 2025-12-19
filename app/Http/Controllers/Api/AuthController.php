<?php

namespace App\Http\Controllers\Api;

use App\Core\Controller\BaseController;
use App\Http\Requests\Auth\EditProfileRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Resources\UserResource;
use App\Service\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseController
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
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
}
