<?php

namespace App\Service;

use App\Core\Cache\CacheKey;
use App\Core\Cache\Caching;
use App\Core\LogHelper;
use App\Core\Service\BaseService;
use App\Core\Service\ServiceReturn;
use App\Enums\ConfigKey;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Zalo\Zalo;
use Zalo\ZaloEndPoint;
use Zalo\Util\PKCEUtil;

class ZaloService extends BaseService
{
    protected Zalo $zalo;
    public function __construct(protected ConfigService $configService)
    {
        parent::__construct();
        $idRes = $this->configService->getConfigByKey(ConfigKey::APP_ID_ZALO);
        $secretRes = $this->configService->getConfigByKey(ConfigKey::APP_SECRET_ZALO);
        if ($idRes->isError() || $secretRes->isError()) {
            LogHelper::error('Có lỗi xảy ra khi lấy thông tin từ Zalo');
            return ServiceReturn::error('Có lỗi xảy ra khi lấy thông tin từ Zalo');
        }
        $this->zalo = new Zalo(['app_id' => $idRes->getData()['config_value'], 'app_secret' => $secretRes->getData()['config_value']]);
    }

    /**
     * Get user profile from Zalo Graph API
     * @param string $accessToken
     * @return array|null
     */
    public function getUserProfile(string $accessToken): ?array
    {
        try {
            $response = Http::withHeaders(
                [
                    'access_token' => $accessToken
                ]
            )->get(ZaloEndPoint::API_GRAPH_ME, [
                'fields' => 'id,name,picture'
            ]);
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'avatar' => $data['picture']['data']['url']
                ];
            }

            Log::error('Zalo Profile Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Zalo Profile Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get Zalo OAuth authorization URL with PKCE
     * @param string $ip
     * @return string|null
     */
    public function getAuthorizationUrl(string $ip): ?string
    {
        try {
            $appIdConfig = $this->configService->getConfigByKey(ConfigKey::APP_ID_ZALO);
            if ($appIdConfig->isError()) {
                LogHelper::error('Cannot get Zalo App ID from config');
                return null;
            }

            LogHelper::debug('Ip ' . $ip);
            // Generate PKCE parameters
            $codeVerifier = PKCEUtil::genCodeVerifier();
            $codeChallenge = PKCEUtil::genCodeChallenge($codeVerifier);

            // Store code_verifier in cache for later use in callback
            Caching::setCache(CacheKey::CACHE_ZALO_AUTH_CODE_VERIFIER, $codeVerifier, $ip, 60);

            // Generate random state for CSRF protection
            $state = Str::random(40);
            Caching::setCache(CacheKey::CACHE_ZALO_AUTH_STATE, $state, $ip, 60);

            // Get callback URL from route
            $callbackUrl = route('zalo.callback');

            // Get login URL using SDK helper
            $helper = $this->zalo->getRedirectLoginHelper();
            $loginUrl = $helper->getLoginUrl($callbackUrl, $codeChallenge, $state);

            LogHelper::debug('Generated Zalo authorization URL', [
                'callback_url' => $callbackUrl,
                'state' => $state,
                'login_url' => $loginUrl
            ]);

            return $loginUrl;
        } catch (\Throwable $e) {
            LogHelper::error('Zalo Authorization URL Exception: ' . $e->getMessage());
            return null;
        }
    }



    /**
     * Exchange authorization code for access token using PKCE
     * @param string $code
     * @return string|null
     */
    public function getAccessTokenFromCode(string $code, string $ip): ?string
    {
        try {
            LogHelper::debug('Ip ' . $ip);
            // Retrieve code_verifier from cache
            $codeVerifier = Caching::getCache(CacheKey::CACHE_ZALO_AUTH_CODE_VERIFIER, $ip);

            if (!$codeVerifier) {
                LogHelper::error('Code verifier not found in cache');
                return null;
            }

            // Use SDK helper to get access token
            $helper = $this->zalo->getRedirectLoginHelper();
            $zaloToken = $helper->getZaloToken($codeVerifier);

            if (!$zaloToken) {
                LogHelper::error('Failed to get Zalo token');
                return null;
            }

            $accessToken = $zaloToken->getAccessToken();

            // Clear session data after successful token exchange
            Caching::deleteCache(CacheKey::CACHE_ZALO_AUTH_CODE_VERIFIER, $ip);
            Caching::deleteCache(CacheKey::CACHE_ZALO_AUTH_STATE, $ip);
            LogHelper::debug('Successfully exchanged code for access token');

            return $accessToken;
        } catch (\Exception $e) {
            LogHelper::error('Zalo Access Token Exception: ' . $e->getMessage());

            // Clear session data on error
            Caching::deleteCache(CacheKey::CACHE_ZALO_AUTH_CODE_VERIFIER, $ip);
            Caching::deleteCache(CacheKey::CACHE_ZALO_AUTH_STATE, $ip);

            return null;
        }
    }
}
