<?php

namespace App\Http\Controllers\Web;

use App\Core\Controller\BaseController;
use App\Service\AuthService;
use App\Service\ZaloService;
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
    public function redirect()
    {
        $redirectUrl = $this->zaloService->getAuthorizationUrl();

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
        $error = $request->query('error');

        // Nếu user từ chối
        if ($error) {
            Log::warning('Zalo OAuth Error: ' . $error);
            return $this->redirectToMobileApp(null, 'Bạn đã từ chối đăng nhập với Zalo');
        }

        // Nếu không có code
        if (!$code) {
            return $this->redirectToMobileApp(null, 'Không nhận được mã xác thực từ Zalo');
        }

        // Exchange code for access token
        $accessToken = $this->zaloService->getAccessTokenFromCode($code);

        if (!$accessToken) {
            return $this->redirectToMobileApp(null, 'Không thể lấy access token từ Zalo');
        }

        // Authenticate with Zalo
        $result = $this->authService->authenticateWithZalo($accessToken);

        if ($result->isError()) {
            return $this->redirectToMobileApp(null, $result->getMessage());
        }

        $token = $result->getData()['token'];
        $user = $result->getData()['user'];

        // Redirect về mobile app với token
        return $this->redirectToMobileApp($token, 'Đăng nhập thành công', $user);
    }

    /**
     * Redirect to mobile app via deeplink
     */
    protected function redirectToMobileApp(?string $token, string $message, $user = null)
    {
        // Deep link format: myapp://auth/zalo?token=xxx&message=xxx
        $deeplink = config('app.mobile_deeplink_scheme', 'nhmapp') . '://auth/zalo';

        $params = [
            'message' => $message,
        ];

        if ($token) {
            $params['token'] = $token;
            $params['success'] = 'true';
            if ($user) {
                $params['user_id'] = $user->id;
                $params['user_name'] = $user->name;
            }
        } else {
            $params['success'] = 'false';
        }

        $deeplink .= '?' . http_build_query($params);

        // Tạo HTML redirect page với fallback
        return view('zalo-callback', [
            'deeplink' => $deeplink,
            'success' => $token !== null,
            'message' => $message,
        ]);
    }
}
