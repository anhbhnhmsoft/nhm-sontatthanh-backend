<?php

namespace App\Service;

use App\Core\Cache\CacheKey;
use App\Core\Cache\Caching;
use App\Core\LogHelper;
use App\Enums\ConfigKey;
use App\Service\ConfigService;
use App\Models\Camera;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class VideoLiveService
{
    public function __construct(
        protected Camera $cameraModel,
        protected ConfigService $configService,
    ) {}

    const TIME_CACHE = 60 * 60 * 24; // 1 ngày, accesssToken có thời lượng 3 ngày nhưng sét 1 ngày tránh lỗi

    const HOST = 'https://openapi-sg.easy4ip.com';

    const PATH_ACCESS_TOKEN = '/openapi/accessToken'; // lấy access token
    const PATH_BIND_DEVICE = '/openapi/bindDevice'; // bind thiết bị vào developer account ~ xác nhận thiết bị vào tổ chức / bắt buộc để LIVE
    const PATH_DEVICE_ONLINE = '/openapi/deviceOnline'; // kiểm tra thiết bị có online để truy cập không
    const PATH_BIND_DEVICE_CHANNEL_INFO = '/openapi/bindDeviceChannelInfo'; // lấy thông tin chi tiết thiết bị, có bao nhiêu luồng 
    /**
     * Set access token
     * @return void
     */
    public function setAccessToken(): void
    {
        $config = $this->configService->getConfigByKey(ConfigKey::APP_ID);
        if ($config->isError()) {
            return;
        };
        $appId = $config->getData()['config_value'];
        $config = $this->configService->getConfigByKey(ConfigKey::APP_SECRET);
        if ($config->isError()) {
            return;
        };
        $appKey = $config->getData()['config_value'];


        $time  = (string) time();
        $nonce = (string) Str::uuid();

        $originSign = "time:{$time},nonce:{$nonce},appSecret:{$appKey}";
        $sign = md5($originSign);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post(self::HOST . self::PATH_ACCESS_TOKEN, [
                'system' => [
                    'ver' => '1.0', // version
                    'appId' => $appId, // id ứng dụng
                    'time' => (string) $time, // thời gian
                    'sign' => $sign, // chữ ký
                    'nonce' => $nonce, // mã ngẫu nhiên
                ],
                'id' => uniqid('req_'), // id yêu cầu có hoặc không ( theo tài liệu mẫu)
                'params' => new \stdClass(), // chỉ cần tồn tại không cần mang giá trị khi truyền lên, có hoặc không theo tài liệu mẫu
            ]);
            $data = $response->json();
            $accessToken = $data['result']['data']['accessToken'];
            if ($response->successful()) {
                Caching::setCache(CacheKey::CACHE_ACCESS_TOKEN, $accessToken, null, self::TIME_CACHE);
            }
            LogHelper::debug(message: 'Set access token thành công');
        } catch (\Throwable $th) {
            LogHelper::error(message: 'Set access token thất bại: ' . $th->getMessage());
            return;
        }
    }

    /**
     * Lấy access token
     * @return ?string
     */
    public function getAccessToken(): ?string
    {
        for ($i = 0; $i < 5; $i++) {
            $accessToken = Caching::getCache(CacheKey::CACHE_ACCESS_TOKEN);

            if ($accessToken) {
                return $accessToken;
            }

            // Nếu chưa có, gọi hàm để fetch từ API và lưu vào cache
            $this->setAccessToken();

            // đợi 0.5 giây
            usleep(500000);
        }

        LogHelper::error("Không thể lấy Access Token sau 5 lần thử.");
        return null;
    }
}
