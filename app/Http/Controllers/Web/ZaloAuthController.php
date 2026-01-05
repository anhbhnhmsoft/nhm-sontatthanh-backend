<?php

namespace App\Http\Controllers\Web;

use App\Core\Cache\CacheKey;
use App\Core\Cache\Caching;
use App\Core\Controller\BaseController;
use App\Core\LogHelper;
use App\Service\AuthService;
use App\Service\ZaloService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ZaloAuthController extends BaseController
{
    public function __construct(
        protected ZaloService $zaloService,
        protected AuthService $authService
    ) {}

    /**
     * Redirect to Zalo OAuth page
     */
    public function redirect(Request $request)
    {
        $token =  (string) $request->get('token');
        $ip = $request->ip();

        if (!Caching::hasCache(CacheKey::CACHE_ZALO_AUTH_TOKEN_VERIFY, $ip . $token)) {
            return view('zalo-error', [
                'message' => 'Token xác thực không hợp lệ hoặc đã hết hạn',
                'error_code' => 'INVALID_TOKEN',
                'details' => 'Vui lòng yêu cầu token mới từ ứng dụng di động',
                'show_close_button' => true,
            ]);
        }

        $redirectUrl = $this->zaloService->getAuthorizationUrl($ip, $token);

        if (!$redirectUrl) {
            return view('zalo-error', [
                'message' => 'Không thể tạo URL xác thực Zalo',
                'error_code' => 'AUTH_URL_FAILED',
                'details' => 'Hệ thống không thể kết nối với Zalo. Vui lòng kiểm tra cấu hình App ID và App Secret.',
                'retry_token' => $token,
                'show_close_button' => false,
            ]);
        }

        return redirect($redirectUrl);
    }

    /**
     * Handle Zalo OAuth callback
     */
    public function callback(Request $request)
    {
        $code = (string) $request->query('code');
        $state = (string) $request->query('state');
        $error = (string) $request->query('error');
        $token = (string) $request->query('token', '');
        $ip = $request->ip();
        // Nếu user từ chối
        if ($error) {
            Log::warning('Zalo OAuth Error: ' . $error);
            return $this->redirectToMobileApp(null, 'Bạn đã từ chối đăng nhập với Zalo', null, $ip, $token);
        }

        // Validate state to prevent CSRF attacks
        $expectedState = Caching::getCache(CacheKey::CACHE_ZALO_AUTH_STATE, $ip . $token);
        if (!$state || !$expectedState || $state !== $expectedState) {
            Log::warning('Zalo OAuth State Mismatch', [
                'expected' => $expectedState,
                'received' => $state
            ]);
            return $this->redirectToMobileApp(null, 'Xác thực không hợp lệ. Vui lòng thử lại.', null, $ip, $token);
        }

        // Nếu không có code
        if (!$code) {
            return $this->redirectToMobileApp(null, 'Không nhận được mã xác thực từ Zalo', null, $ip, $token);
        }

        // Exchange code for access token
        $accessToken = $this->zaloService->getAccessTokenFromCode($ip, $token);

        if (!$accessToken) {
            return $this->redirectToMobileApp(null, 'Không thể lấy access token từ Zalo', null, $ip, $token);
        }

        // Authenticate with Zalo
        $result = $this->authService->authenticateWithZalo($accessToken, $ip, $token);

        if ($result->isError()) {
            return $this->redirectToMobileApp(null, $result->getMessage(), null, $ip, $token);
        }

        $tokenUser = $result->getData()['token'];
        $user = $result->getData()['user'];

        // Redirect về mobile app với token
        return $this->redirectToMobileApp($tokenUser, 'Đăng nhập thành công', $user, $ip, $token);
    }

    /**
     * Redirect to mobile app via deeplink
     *
     * @param string|null $token Authentication token if successful
     * @param string $message Message to display to user
     * @param mixed $user User object if authentication successful
     * @return \Illuminate\View\View
     */
    protected function redirectToMobileApp(?string $token, string $message, $user = [], string $ip, string $tokenVerify)
    {
        if (!Caching::hasCache(CacheKey::CACHE_ZALO_AUTH_TOKEN_VERIFY, $ip . $tokenVerify)) {
            return view('zalo-callback', [
                'deeplink' => '',
                'success' => $token !== null,
                'message' => $message,
                'user' => $user,
            ]);
        }

        $data = Caching::getCache(CacheKey::CACHE_ZALO_AUTH_TOKEN_VERIFY, $ip . $tokenVerify);
        $deeplink = $data['deeplink'] ?? '';
        LogHelper::debug('Zalo Auth Callback', compact('token', 'message', 'user', 'ip', 'deeplink'));
        // Return view with all necessary data
        return view('zalo-callback', [
            'deeplink' => $deeplink,
            'success' => $token !== null,
            'message' => $message,
            'user' => $user,
        ]);
    }

    /**
     * Giữ token auth zalo
     */
    public function keepZaloAuthToken(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'deeplink' => 'required|string',
        ], [
            'token.required' => 'Token không được để trống',
            'deeplink.required' => 'Deeplink không được để trống',
        ]);

        $token = $request->input('token');
        $deeplink = $request->input('deeplink');
        $ip = $request->ip();

        if (Caching::hasCache(
            key: CacheKey::CACHE_ZALO_AUTH_TOKEN_VERIFY,
            uniqueKey: $ip . $token,
        )) {
            return $this->sendSuccess(
                message: 'Token đã được lưu trước đó',
            );
        }

        Caching::setCache(
            key: CacheKey::CACHE_ZALO_AUTH_TOKEN_VERIFY,
            value: ['token' => $token, 'deeplink' => $deeplink],
            uniqueKey: $ip . $token,
            expire: 60 * 5,
        );

        return $this->sendSuccess(
            message: 'Lưu token auth zalo thành công'
        );
    }

    /**
     * Xác thực trả toke auth thực
     */
    public function verifyZaloAuthToken(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
        ], [
            'token.required' => 'Token không được để trống',
        ]);

        $token = $request->input('token');
        $ip = $request->ip();

        if (!Caching::hasCache(CacheKey::CACHE_ZALO_AUTH_TOKEN_VERIFY, $ip . $token)) {
            return $this->sendError(
                'Phiên làm việc đã hết hạn',
                400,
            );
        }

        if (!Caching::hasCache(CacheKey::CACHE_ZALO_AUTH_TOKEN, $ip . $token)) {
            return $this->sendError(
                'IP này chưa xác thực token auth zalo',
                400,
            );
        }

        $cachedTokenVerify = Caching::getCache(CacheKey::CACHE_ZALO_AUTH_TOKEN, $ip . $token);
        return $this->sendSuccess(
            ['token' => $cachedTokenVerify['token'], 'user' => $cachedTokenVerify['user']],
            'Xác thực token auth zalo thành công'
        );
    }
}
