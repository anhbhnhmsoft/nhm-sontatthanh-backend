<?php

namespace App\Service;

use App\Core\LogHelper;
use App\Core\Service\BaseService;
use App\Core\Service\ServiceReturn;
use App\Enums\ConfigKey;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Zalo\Zalo;
use Zalo\ZaloEndPoint;

class ZaloService extends BaseService
{
    protected Zalo $zalo;
    public function __construct(protected ConfigService $configService)
    {
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
     * Get Zalo OAuth authorization URL
     * @return string|null
     */
    public function getAuthorizationUrl(): ?string
    {
        try {
            $callbackUrl = config('app.url') . '/auth/zalo/callback';
            $appId = $this->configService->getConfigByKey(ConfigKey::APP_ID_ZALO);

            if ($appId->isError()) {
                LogHelper::error('Cannot get Zalo App ID from config');
                return null;
            }

            $params = [
                'app_id' => $appId->getData()['config_value'],
                'redirect_uri' => $callbackUrl,
            ];

            return 'https://oauth.zaloapp.com/v4/permission?' .     ($params);
        } catch (\Exception $e) {
            LogHelper::error('Zalo Authorization URL Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Exchange authorization code for access token
     * @param string $code
     * @return string|null
     */
    public function getAccessTokenFromCode(string $code): ?string
    {
        try {
            $idRes = $this->configService->getConfigByKey(ConfigKey::APP_ID_ZALO);
            $secretRes = $this->configService->getConfigByKey(ConfigKey::APP_SECRET_ZALO);

            if ($idRes->isError() || $secretRes->isError()) {
                LogHelper::error('Cannot get Zalo credentials from config');
                return null;
            }

            $appId = $idRes->getData()['config_value'];
            $appSecret = $secretRes->getData()['config_value'];
            $callbackUrl = config('app.url') . '/auth/zalo/callback';

            $response = Http::asForm()->post('https://oauth.zaloapp.com/v4/access_token', [
                'app_id' => $appId,
                'app_secret' => $appSecret,
                'code' => $code,
                'grant_type' => 'authorization_code',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['access_token'] ?? null;
            }

            Log::error('Zalo Access Token Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Zalo Access Token Exception: ' . $e->getMessage());
            return null;
        }
    }
}
