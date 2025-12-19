<?php

namespace App\Service;

use App\Core\Cache\CacheKey;
use App\Core\Cache\Caching;
use App\Core\LogHelper;
use App\Core\Service\ServiceReturn;
use App\Enums\ConfigKey;
use App\Enums\StatusChannel;
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

    const TIME_CACHE = 60 * 60 * 24; // 1 ngàgetLiveStreamInfoy, accesssToken có thời lượng 3 ngày nhưng sét 1 ngày tránh lỗi

    const HOST = 'https://openapi-sg.easy4ip.com';

    const PATH_ACCESS_TOKEN = '/openapi/accessToken'; // lấy access token
    const PATH_BIND_DEVICE = '/openapi/bindDevice'; // bind thiết bị vào developer account ~ xác nhận thiết bị vào tổ chức / bắt buộc để LIVE
    const PATH_UNBIND_DEVICE = '/openapi/unBindDevice'; // unbind thiết bị khỏi tài khoản
    const PATH_DEVICE_ONLINE = '/openapi/deviceOnline'; // kiểm tra thiết bị có online để truy cập không
    const PATH_BIND_DEVICE_CHANNEL_INFO = '/openapi/queryOpenDeviceChannelInfo'; // lấy thông tin chi tiết thiết bị, có bao nhiêu luồng 

    const PATH_START_LIVE = '/openapi/bindDeviceLive'; // khởi động live

    const PATH_LIVE_CHECK = '/openapi/getLiveStreamInfo'; // kiểm tra live

    // ------------ PRIVATE FUNCTION ------------
    /**
     * Lấy sign
     * @return array{success: bool, message: string, data: array|null}
     */
    protected function getSign(): array
    {

        $config = $this->configService->getConfigByKey(ConfigKey::APP_ID);
        if ($config->isError()) {
            return [
                'success' => false,
                'message' => 'Không thể lấy app id',
                'data' => null
            ];
        };
        $appId = $config->getData()['config_value'];
        $config = $this->configService->getConfigByKey(ConfigKey::APP_SECRET);
        if ($config->isError()) {
            return [
                'success' => false,
                'message' => 'Không thể lấy app secret',
                'data' => null
            ];
        };
        $appKey = $config->getData()['config_value'];


        $time  = (string) time();
        $nonce = (string) Str::uuid();

        $originSign = "time:{$time},nonce:{$nonce},appSecret:{$appKey}";
        $sign = md5($originSign);

        return [
            'success' => true,
            'message' => 'Lấy sign thành công',
            'data' => [
                'time' => $time,
                'nonce' => $nonce,
                'sign' => $sign,
                'appId' => $appId,
            ]
        ];
    }


    /**
     * Set access token
     * @return void
     */
    protected function setAccessToken(): void
    {
        $sign = $this->getSign();
        if (!$sign['success']) {
            return;
        }

        $time = $sign['data']['time'];
        $nonce = $sign['data']['nonce'];
        $sign = $sign['data']['sign'];
        $appId = $sign['data']['appId'];

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
    protected function getAccessToken(): ?string
    {
        for ($i = 0; $i < 5; $i++) {
            $accessToken = Caching::getCache(CacheKey::CACHE_ACCESS_TOKEN);

            if ($accessToken) {
                return $accessToken;
            }

            $this->setAccessToken();
            // Nếu chưa có, gọi hàm để fetch từ API và lưu vào cache

            // đợi 5 giây
            usleep(5000000);
        }

        LogHelper::error("Không thể lấy Access Token sau 5 lần thử.");
        return null;
    }

    // ------------ PUBLIC FUNCTION ------------
    /**
     * Bind thiết bị vào developer account
     * Link: https://open.imoulife.com/book/http/device/manage/bind/bindDevice.html
     * @param string $deviceId
     * @param string $code  Safety code / Password của thiết bị
     * @return ServiceReturn
     */
    public function bindDevice(string $deviceId, string $code): ServiceReturn
    {
        $camera = $this->cameraModel->where('device_id', $deviceId)->first();
        if (!$camera) {
            return ServiceReturn::error('Thiết bị không tồn tại');
        }
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return ServiceReturn::error('Không thể lấy access token');
        }

        $signature = $this->getSign();
        if (!$signature['success']) {
            return ServiceReturn::error('Không thể lấy signature');
        }

        $time = $signature['data']['time'];
        $nonce = $signature['data']['nonce'];
        $sign = $signature['data']['sign'];
        $appId = $signature['data']['appId'];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post(self::HOST . self::PATH_BIND_DEVICE, [
                'system' => [
                    'ver' => '1.0',
                    'time' => $time,
                    'sign' => $sign,
                    'nonce' => $nonce,
                    'appId' => $appId,
                ],
                'id' => uniqid('req_'),
                'params' => [
                    'deviceId' => $deviceId,
                    'code' => $code,
                    'token' => $accessToken,
                ],
            ]);

            $data = $response->json();
            if ($response->successful() && isset($data['result']['code']) && $data['result']['code'] === '0') {
                $camera->update([
                    'bind_status' => true,
                ]);
                LogHelper::debug("Bind device {$deviceId} thành công");
                return ServiceReturn::success([
                    'success' => true,
                    'message' => 'Bind thiết bị thành công',
                    'data' => $data['result']['data'] ?? []
                ]);
            }

            LogHelper::error("Bind device {$deviceId} thất bại: " . json_encode($data));
            return ServiceReturn::error($data['result']['msg'] ?? 'Bind thiết bị thất bại');
        } catch (\Throwable $th) {
            LogHelper::error("Bind device {$deviceId} exception: " . $th->getMessage());
            return ServiceReturn::error('Lỗi kết nối: ' . $th->getMessage());
        }
    }

    /**
     * Kiểm tra trạng thái thiết bị online
     * https://open.imoulife.com/book/http/device/manage/query/deviceOnline.html
     * @param string $deviceId
     * @return ServiceReturn
     * `
     */
    public function checkDeviceOnline(string $deviceId): ServiceReturn
    {
        $camera = $this->cameraModel->where('device_id', $deviceId)->first();
        if (!$camera) {
            return ServiceReturn::error('Thiết bị không tồn tại');
        }
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return ServiceReturn::error('Không thể lấy access token');
        }

        $signature = $this->getSign();
        if (!$signature['success']) {
            return ServiceReturn::error('Không thể lấy signature');
        }

        $time = $signature['data']['time'];
        $nonce = $signature['data']['nonce'];
        $sign = $signature['data']['sign'];
        $appId = $signature['data']['appId'];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post(self::HOST . self::PATH_DEVICE_ONLINE, [
                'system' => [
                    'ver' => '1.0',
                    'time' => $time,
                    'sign' => $sign,
                    'nonce' => $nonce,
                    'appId' => $appId,
                ],
                'id' => uniqid('req_'),
                'params' => [
                    'deviceId' => $deviceId,
                    'token' => $accessToken,
                ],
            ]);

            $data = $response->json();

            if ($response->successful() && isset($data['result']['code']) && $data['result']['code'] === '0') {

                $camera->update([
                    'enable' => $data['result']['data']['onLine'] == '1',
                    'channel_id' => $data['result']['data']['channelId'] - 1 ?? null,
                ]);
                LogHelper::debug("Check device {$deviceId} online thành công");
                return ServiceReturn::success([
                    'success' => true,
                    'message' => 'Check device online thành công',
                    'data' => $data['result']['data'] ?? []
                ]);
            }

            LogHelper::error("Check device {$deviceId} online thất bại: " . json_encode($data));
            return ServiceReturn::error($data['result']['msg'] ?? 'Check device online thất bại');
        } catch (\Throwable $th) {
            LogHelper::error("Check device {$deviceId} online exception: " . $th->getMessage());
            return ServiceReturn::error('Lỗi kết nối: ' . $th->getMessage());
        }
    }

    /**
     * Lấy thông tin kênh video của thiết bị
     * https://open.imoulife.com/book/http/device/manage/query/queryOpenDeviceChannelInfo.html
     * @param string $deviceId
     * @return ServiceReturn
     */
    public function getDeviceChannelInfo(string $deviceId): ServiceReturn
    {

        $camera = $this->cameraModel->where('device_id', $deviceId)->first();
        if (!$camera) {
            return ServiceReturn::error('Thiết bị không tồn tại');
        }
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return ServiceReturn::error('Không thể lấy access token');
        }

        $signature = $this->getSign();
        if (!$signature['success']) {
            return ServiceReturn::error('Không thể lấy signature');
        }

        $time = $signature['data']['time'];
        $nonce = $signature['data']['nonce'];
        $sign = $signature['data']['sign'];
        $appId = $signature['data']['appId'];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post(self::HOST . self::PATH_BIND_DEVICE_CHANNEL_INFO, [
                'system' => [
                    'ver' => '1.0',
                    'time' => $time,
                    'sign' => $sign,
                    'nonce' => $nonce,
                    'appId' => $appId,
                ],
                'id' => uniqid('req_'),
                'params' => [
                    'deviceIds' => $deviceId,
                    'token' => $accessToken,
                ],
            ]);

            $data = $response->json();
            if ($response->successful() && isset($data['result']['code']) && $data['result']['code'] === '0') {

                $channels = $data['result']['devices'][0]['channels'];
                foreach ($channels as $channel) {
                    $camera->channels()->create([
                        'name' => $channel['name'],
                        'status' => $channel['status'] == 'online' ? StatusChannel::ONLINE->value : StatusChannel::OFFLINE->value,
                        'position' => $channel['channelId'],
                    ]);
                }

                $camera->update([
                    'channel_id' => count($channels),
                ]);

                LogHelper::debug("Check thông tin kênh device {$deviceId} thành công");
                return ServiceReturn::success([
                    'success' => true,
                    'message' => 'Check thông tin kênh device thành công',
                    'data' => $data['result']['data'] ?? []
                ]);
            }

            LogHelper::error("Check thông tin kênh device {$deviceId} thất bại: " . json_encode($data));
            return ServiceReturn::error($data['result']['msg'] ?? 'Check thông tin kênh device thất bại');
        } catch (\Throwable $th) {
            LogHelper::error("Check thông tin kênh device {$deviceId} exception: " . $th->getMessage());
            return ServiceReturn::error('Lỗi kết nối: ' . $th->getMessage());
        }
    }

    /**
     * Khởi động live 
     * https://open.imoulife.com/book/http/device/live/bindDeviceLive.html
     * @param string $deviceId
     * @return ServiceReturn
     */
    public function startLive(string $deviceId): ServiceReturn
    {
        if(Caching::hasCache(CacheKey::CACHE_LIVE_STREAM, $deviceId)) {
            return ServiceReturn::success([
                'success' => true,
                'message' => 'Live stream đang hoạt động',
                'data' => Caching::getCache(CacheKey::CACHE_LIVE_STREAM, $deviceId)
            ]);
        }

        $device = $this->cameraModel->where('device_id', $deviceId)->first();
        if (!$device) {
            return ServiceReturn::error(
                'Thiết bị không tồn tại'
            );
        }

        $chanel = $device->channels()->where('status', StatusChannel::ONLINE->value)->first();
        if (!$chanel) {
            return ServiceReturn::error(
                'Không có kênh nào đang hoạt động'
            );
        }

        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return ServiceReturn::error(
                'Không thể lấy access token'
            );
        }

        $signature = $this->getSign();
        if (!$signature['success']) {
            return ServiceReturn::error(
                'Không thể lấy signature'
            );
        }

        $time = $signature['data']['time'];
        $nonce = $signature['data']['nonce'];
        $sign = $signature['data']['sign'];
        $appId = $signature['data']['appId'];
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post(self::HOST . self::PATH_START_LIVE, [
                'system' => [
                    'ver' => '1.0',
                    'time' => $time,
                    'sign' => $sign,
                    'nonce' => $nonce,
                    'appId' => $appId,
                ],
                'id' => uniqid('req_'),
                'params' => [
                    'streamId' => 1,
                    'channelId' => $chanel->position,
                    'deviceId' => $deviceId,
                    'token' => $accessToken,
                ],
            ]);

            $data = $response->json();
            if ($response->successful() && isset($data['result']['code']) && $data['result']['code'] === '0') {

                $data = $data['result']['data'];
                $stream = $data['streams'];

                $broadcast = [
                    'coverUrl'   =>  $stream['coverUrl'],
                    'streamId'   => $stream['streamId'],
                    'hls'        => $stream['hls'],
                    'liveToken'  => $data['liveToken'],
                    'channelId'  => $data['channelId'],
                    'liveStatus' => $data['liveStatus'],
                ];

                Caching::setCache(CacheKey::CACHE_LIVE_STREAM, $broadcast, $deviceId, 60 * 60);
                LogHelper::debug("Khởi động live device {$deviceId} thành công");
                return ServiceReturn::success(
                    'Khởi động live device thành công',
                    $data['result']['data'] ?? []
                );
            }

            LogHelper::error("Khởi động live device {$deviceId} thất bại: " . json_encode($data));
            return ServiceReturn::error(
                $data['result']['msg'] ?? 'Khởi động live device thất bại'
            );
        } catch (\Throwable $th) {
            LogHelper::error("Khởi động live device {$deviceId} exception: " . $th->getMessage());
            return ServiceReturn::error(
                'Lỗi kết nối: ' . $th->getMessage()
            );
        }
    }
}
