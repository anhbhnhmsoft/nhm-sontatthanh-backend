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
        $redirectUrl = $this->zaloService->getAuthorizationUrl($request->ip());

        if (!$redirectUrl) {
            return response()->json([
                'error' => 'Không thể tạo URL xác thực Zalo'
            ], 500);
        }

        return redirect($redirectUrl);
    }

    /**
     * Handle Zalo OAuth callback
     */
    public function callback(Request $request)
    {
        $code = $request->query('code');
        $state = $request->query('state');
        $error = $request->query('error');
        Log::info('Zalo OAuth Callback', compact('code', 'state', 'error'));
        // Nếu user từ chối
        if ($error) {
            Log::warning('Zalo OAuth Error: ' . $error);
            return $this->redirectToMobileApp(null, 'Bạn đã từ chối đăng nhập với Zalo', null, $request->ip());
        }

        // Validate state to prevent CSRF attacks
        $expectedState = Caching::getCache(CacheKey::CACHE_ZALO_AUTH_STATE, $request->ip());
        if (!$state || !$expectedState || $state !== $expectedState) {
            Log::warning('Zalo OAuth State Mismatch', [
                'expected' => $expectedState,
                'received' => $state
            ]);
            return $this->redirectToMobileApp(null, 'Xác thực không hợp lệ. Vui lòng thử lại.', null, $request->ip());
        }

        // Nếu không có code
        if (!$code) {
            return $this->redirectToMobileApp(null, 'Không nhận được mã xác thực từ Zalo', null, $request->ip());
        }

        // Exchange code for access token
        $accessToken = $this->zaloService->getAccessTokenFromCode($code, $request->ip());

        if (!$accessToken) {
            return $this->redirectToMobileApp(null, 'Không thể lấy access token từ Zalo', null, $request->ip());
        }

        // Authenticate with Zalo
        $result = $this->authService->authenticateWithZalo($accessToken, $request->ip());

        if ($result->isError()) {
            return $this->redirectToMobileApp(null, $result->getMessage(), null, $request->ip());
        }

        $token = (string) $result->getData()['token'];
        $user =  $result->getData()['user'];

        // Redirect về mobile app với token
        return $this->redirectToMobileApp($token, 'Đăng nhập thành công', $user, $request->ip());
    }

    /**
     * Redirect to mobile app via deeplink
     *
     * @param string|null $token Authentication token if successful
     * @param string $message Message to display to user
     * @param mixed $user User object if authentication successful
     * @return \Illuminate\View\View
     */
    protected function redirectToMobileApp(?string $token, string $message, $user = [], string $ip)
    {
        if(!Caching::hasCache(CacheKey::CACHE_ZALO_AUTH_TOKEN_VERIFY, $ip)) {
            return view('zalo-callback', [
                'deeplink' => '',
                'success' => $token !== null,
                'message' => $message,
                'user' => $user,
            ]);
        }

        $data = Caching::getCache(CacheKey::CACHE_ZALO_AUTH_TOKEN_VERIFY, $ip);
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
        ],[
            'token.required' => 'Token không được để trống',
            'deeplink.required' => 'Deeplink không được để trống',
        ]);

        $token = (string) $request->input('token');
        $deeplink = (string) $request->input('deeplink');
        $ip = $request->ip();
        LogHelper::debug('Zalo Auth Keep Token', compact('token', 'deeplink', 'ip'));
        if(Caching::hasCache(CacheKey::CACHE_ZALO_AUTH_TOKEN_VERIFY, $ip)) {
            return $this->sendSuccess(
                message:'Token đã được lưu trước đó',
            );
        }

        Caching::setCache(
            CacheKey::CACHE_ZALO_AUTH_TOKEN_VERIFY,
            [ 'token' => $token, 'deeplink' => $deeplink],
            $ip,
            60 * 5,
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
            'deeplink' => 'required|string',
        ],[
            'token.required' => 'Token không được để trống',
            'deeplink.required' => 'Deeplink không được để trống',
        ]);

        $token = (string) $request->input('token');
        $deeplink = (string) $request->input('deeplink');
        $ip = $request->ip();

        if(!Caching::hasCache(CacheKey::CACHE_ZALO_AUTH_TOKEN_VERIFY, $ip)) {
            return $this->sendError(
                'Phiên làm việc đã hết hạn',
                400,
            );
        }

        $cachedToken = Caching::getCache(CacheKey::CACHE_ZALO_AUTH_TOKEN_VERIFY, $ip);
        if($cachedToken['token'] !== $token) {
            return $this->sendError(
                'Token auth zalo không đúng',
                400,
            );
        }

        $ip = $request->ip();
        if($cachedToken['deeplink'] !== $deeplink) {
            return $this->sendError(
                'Deeplink không đúng',
                400,
            );
        }

        if(!Caching::hasCache(CacheKey::CACHE_ZALO_AUTH_TOKEN, $ip)) {
            return $this->sendError(
                'IP này chưa xác thực token auth zalo',
                400,
            );
        }

        $cachedTokenVerify = Caching::getCache(CacheKey::CACHE_ZALO_AUTH_TOKEN, $ip);
        return $this->sendSuccess(
            ['token' => $cachedTokenVerify],
            'Xác thực token auth zalo thành công'
        );
    }
}
