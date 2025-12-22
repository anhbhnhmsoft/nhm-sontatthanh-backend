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
}
