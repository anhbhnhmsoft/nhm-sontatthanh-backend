<?php

namespace App\Http\Controllers\Api;

use App\Core\Controller\BaseController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Resources\UserResource;
use App\Service\AuthService;
use Illuminate\Http\JsonResponse;

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
}
