<?php

namespace App\Service;

use App\Core\Cache\CacheKey;
use App\Core\Cache\Caching;
use App\Core\LogHelper;
use App\Core\Service\ServiceReturn;
use App\Enums\ConfigKey;
use App\Enums\StatusChannel;
use App\Models\Camera;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class VideoLiveService
{
    public function __construct(
        protected Camera $cameraModel,
        protected ConfigService $configService,
    ) {}

    const TIME_CACHE_TOKEN = 60 * 60 * 24; // 1 ngày
    const HOST = 'https://openapi-sg.easy4ip.com';

    // API Endpoints
    const PATH_ACCESS_TOKEN = '/openapi/accessToken';
    const PATH_BIND_DEVICE = '/openapi/bindDevice';
    const PATH_UNBIND_DEVICE = '/openapi/unBindDevice';
    const PATH_DEVICE_ONLINE = '/openapi/deviceOnline';
    const PATH_BIND_DEVICE_CHANNEL_INFO = '/openapi/queryOpenDeviceChannelInfo';
    const PATH_START_LIVE = '/openapi/bindDeviceLive';
    const PATH_LIVE_CHECK = '/openapi/getLiveStreamInfo';

    // ------------ PRIVATE HELPERS ------------

    /**
     * Lấy system parameters với signature
     * @return array
     * @throws \Exception
     */
    protected function getSystemParams(): array
    {
        $appIdConfig = $this->configService->getConfigByKey(ConfigKey::APP_ID);
        if ($appIdConfig->isError()) {
            throw new \Exception('Không thể lấy app id');
        }
        $appId = $appIdConfig->getData()['config_value'];

        $appSecretConfig = $this->configService->getConfigByKey(ConfigKey::APP_SECRET);
        if ($appSecretConfig->isError()) {
            throw new \Exception('Không thể lấy app secret');
        }
        $appKey = $appSecretConfig->getData()['config_value'];

        $time  = (string) time();
        $nonce = (string) Str::uuid();
        $sign = md5("time:{$time},nonce:{$nonce},appSecret:{$appKey}");

        return [
            'ver' => '1.0',
            'appId' => $appId,
            'time' => $time,
            'sign' => $sign,
            'nonce' => $nonce,
        ];
    }

    /**
     * Gửi API request (không cần authentication)
     * @param string $path
     * @param array $params
     * @return array
     */
    protected function sendRequest(string $path, array $params = []): array
    {
        try {
            $systemParams = $this->getSystemParams();
        } catch (\Throwable $e) {
            LogHelper::error("Get system params failed: " . $e->getMessage());
            return [
                'result' => [
                    'code' => 'CONFIG_ERROR',
                    'msg' => $e->getMessage()
                ]
            ];
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post(self::HOST . $path, [
                'system' => $systemParams,
                'id' => uniqid('req_'),
                'params' => empty($params) ? new \stdClass() : $params,
            ]);

            return $response->json();
        } catch (\Throwable $th) {
            LogHelper::error("Request to {$path} exception: " . $th->getMessage());
            return [
                'result' => [
                    'code' => 'EXCEPTION',
                    'msg' => 'Lỗi kết nối: ' . $th->getMessage()
                ]
            ];
        }
    }

    /**
     * Lấy access token từ cache hoặc API
     * @return ?string
     */
    protected function getAccessToken(): ?string
    {
        $accessToken = Caching::getCache(CacheKey::CACHE_ACCESS_TOKEN);
        if ($accessToken) {
            return $accessToken;
        }

        // Fetch new token
        $response = $this->sendRequest(self::PATH_ACCESS_TOKEN);
        if (isset($response['result']['code']) && $response['result']['code'] === '0') {
            $token = $response['result']['data']['accessToken'];
            Caching::setCache(CacheKey::CACHE_ACCESS_TOKEN, $token, null, self::TIME_CACHE_TOKEN);
            LogHelper::debug('Set access token thành công');
            return $token;
        }

        LogHelper::error("Không thể lấy Access Token: " . json_encode($response));
        return null;
    }

    /**
     * Gửi authenticated API request
     * @param string $path
     * @param array $params
     * @return array
     */
    protected function sendAuthRequest(string $path, array $params = []): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return [
                'result' => [
                    'code' => 'AUTH_ERROR',
                    'msg' => 'Không thể lấy access token'
                ]
            ];
        }

        $params['token'] = $token;
        return $this->sendRequest($path, $params);
    }

    /**
     * Tìm camera theo device ID
     * @param string $deviceId
     * @return Camera|null
     */
    protected function findCamera(string $deviceId): ?Camera
    {
        return $this->cameraModel->where('device_id', $deviceId)->first();
    }

    /**
     * Kiểm tra response có thành công không
     * @param array $response
     * @return bool
     */
    protected function isSuccessResponse(array $response): bool
    {
        return isset($response['result']['code']) && $response['result']['code'] === '0';
    }

    /**
     * Lấy error message từ response
     * @param array $response
     * @param string $default
     * @return string
     */
    protected function getErrorMessage(array $response, string $default = 'Có lỗi xảy ra'): string
    {
        return $response['result']['msg'] ?? $default;
    }

    // ------------ PUBLIC METHODS ------------

    /**
     * Force refresh access token
     * @return void
     */
    public function setAccessToken(): void
    {
        Caching::deleteCache(CacheKey::CACHE_ACCESS_TOKEN);
        $this->getAccessToken();
    }

    /**
     * Bind thiết bị vào developer account
     * @param string $deviceId
     * @param string $code Safety code / Password của thiết bị
     * @return ServiceReturn
     */
    public function bindDevice(string $deviceId, string $code): ServiceReturn
    {
        $camera = $this->findCamera($deviceId);
        if (!$camera) {
            return ServiceReturn::error('Thiết bị không tồn tại');
        }

        $response = $this->sendAuthRequest(self::PATH_BIND_DEVICE, [
            'deviceId' => $deviceId,
            'code' => $code,
        ]);

        $code = $response['result']['code'] ?? 'UNKNOWN';

        // Thiết bị đã được bind trước đó
        if ($code === 'DV1003') {
            $camera->update(['bind_status' => true, 'is_active' => true]);
            return ServiceReturn::success(null, 'Thiết bị đã được kết nối trước đó');
        }

        // Bind thành công
        if ($code === '0') {
            $camera->update(['bind_status' => true, 'is_active' => true]);
            LogHelper::debug("Bind device {$deviceId} thành công");
            return ServiceReturn::success(null, 'Bind thiết bị thành công');
        }

        // Bind thất bại
        LogHelper::error("Bind device {$deviceId} thất bại: " . json_encode($response));
        return ServiceReturn::error($this->getErrorMessage($response, 'Bind thiết bị thất bại'));
    }

    /**
     * Xóa quyền sở hữu thiết bị khỏi tổ chức
     * @param string $deviceId
     * @return ServiceReturn
     */
    public function unbindDevice(string $deviceId): ServiceReturn
    {
        $camera = $this->findCamera($deviceId);
        if (!$camera) {
            return ServiceReturn::error('Thiết bị không tồn tại');
        }

        $response = $this->sendAuthRequest(self::PATH_UNBIND_DEVICE, [
            'deviceId' => $deviceId,
        ]);

        if ($this->isSuccessResponse($response)) {
            $camera->update([
                'is_active' => false,
                'bind_status' => false,
                'enable' => false,
            ]);
            LogHelper::debug("Unbind device {$deviceId} thành công");
            return ServiceReturn::success([
                'success' => true,
                'message' => 'Unbind thiết bị thành công',
                'data' => $response['result']['data'] ?? []
            ]);
        }

        LogHelper::error("Unbind device {$deviceId} thất bại: " . json_encode($response));
        return ServiceReturn::error($this->getErrorMessage($response, 'Unbind thiết bị thất bại'));
    }

    /**
     * Kiểm tra trạng thái thiết bị online
     * @param string $deviceId
     * @return ServiceReturn
     */
    public function checkDeviceOnline(string $deviceId): ServiceReturn
    {
        $camera = $this->findCamera($deviceId);
        if (!$camera) {
            return ServiceReturn::error('Thiết bị không tồn tại');
        }

        $response = $this->sendAuthRequest(self::PATH_DEVICE_ONLINE, [
            'deviceId' => $deviceId,
        ]);

        if ($this->isSuccessResponse($response)) {
            LogHelper::debug("Check device {$deviceId} online thành công");
            return ServiceReturn::success([
                'success' => true,
                'message' => 'Check device online thành công',
                'data' => $response['result']['data'] ?? []
            ]);
        }

        LogHelper::error("Check device {$deviceId} online thất bại: " . json_encode($response));
        return ServiceReturn::error($this->getErrorMessage($response, 'Check device online thất bại'));
    }

    /**
     * Lấy thông tin kênh video của thiết bị
     * @param string $deviceId
     * @return ServiceReturn
     */
    public function getDeviceChannelInfo(string $deviceId): ServiceReturn
    {
        DB::beginTransaction();
        try {
            $camera = $this->findCamera($deviceId);
            if (!$camera) {
                DB::rollBack();
                return ServiceReturn::error('Thiết bị không tồn tại');
            }

            $response = $this->sendAuthRequest(self::PATH_BIND_DEVICE_CHANNEL_INFO, [
                'deviceIds' => $deviceId,
            ]);

            if ($this->isSuccessResponse($response)) {
                $devices = $response['result']['data']['devices'] ?? [];

                if (!empty($devices)) {
                    $deviceInfo = $devices[0];
                    $channels = $deviceInfo['channels'] ?? [];

                    // Update camera info
                    $camera->update([
                        'enable' => $deviceInfo['status'] == 'online',
                        'channel_id' => count($channels),
                    ]);

                    // Recreate channels
                    $camera->channels()->delete();
                    foreach ($channels as $channel) {
                        $camera->channels()->create([
                            'name' => $channel['name'] ?? '',
                            'status' => $channel['status'] == 'online'
                                ? StatusChannel::ONLINE->value
                                : StatusChannel::OFFLINE->value,
                            'position' => $channel['channelId'],
                        ]);
                    }
                }

                DB::commit();
                LogHelper::debug("Check thông tin kênh device {$deviceId} thành công");
                return ServiceReturn::success([
                    'success' => true,
                    'message' => 'Check thông tin kênh device thành công',
                    'data' => $response['result']['data'] ?? []
                ]);
            }

            DB::rollBack();
            LogHelper::error("Check thông tin kênh device {$deviceId} thất bại: " . json_encode($response));
            return ServiceReturn::error($this->getErrorMessage($response, 'Check thông tin kênh device thất bại'));
        } catch (\Throwable $th) {
            DB::rollBack();
            LogHelper::error("Check thông tin kênh device {$deviceId} exception: " . $th->getMessage());
            return ServiceReturn::error('Lỗi kết nối: ' . $th->getMessage());
        }
    }

    /**
     * Khởi động live stream
     * @param string $deviceId
     * @return ServiceReturn
     */
    public function startLive(string $deviceId): ServiceReturn
    {
        // Kiểm tra cache trước
        if (Caching::hasCache(CacheKey::CACHE_LIVE_STREAM, $deviceId)) {
            return ServiceReturn::success([
                'success' => true,
                'message' => 'Live stream đang hoạt động',
                'data' => Caching::getCache(CacheKey::CACHE_LIVE_STREAM, $deviceId)
            ]);
        }
        
        $device = $this->findCamera($deviceId);
        if (!$device) {
            return ServiceReturn::error('Thiết bị không tồn tại');
        }

        $channel = $device->channels()->where('status', StatusChannel::ONLINE->value)->first();
        if (!$channel) {
            return ServiceReturn::error('Không có kênh nào đang hoạt động');
        }

        $response = $this->sendAuthRequest(self::PATH_START_LIVE, [
            'streamId' => 1,
            'channelId' => $channel->position,
            'deviceId' => $deviceId,
        ]);

        $code = $response['result']['code'] ?? 'UNKNOWN';

        // Live stream đã được kích hoạt trước đó
        if ($code === 'LV1001') {
            $device->update(['is_active' => true]);
            return ServiceReturn::error('Thiết bị đã được kích hoạt');
        }

        // Khởi động thành công
        if ($code === '0') {
            $resultData = $response['result']['data'];
            $stream = $resultData['streams'][0];

            $device->update(['is_active' => true]);

            $broadcast = [
                'coverUrl'   => $stream['coverUrl'],
                'streamId'   => $stream['streamId'],
                'hls'        => $stream['hls'],
                'liveToken'  => $resultData['liveToken'],
                'channelId'  => $resultData['channelId'],
                'liveStatus' => $resultData['liveStatus'],
            ];

            Caching::setCache(CacheKey::CACHE_LIVE_STREAM, $broadcast, $deviceId, 60 * 60);
            LogHelper::debug("Khởi động live device {$deviceId} thành công");

            return ServiceReturn::success(
                $resultData,
                'Khởi động live device thành công'
            );
        }

        LogHelper::error("Khởi động live device {$deviceId} thất bại: " . json_encode($response));
        return ServiceReturn::error($this->getErrorMessage($response, 'Khởi động live device thất bại'));
    }

    /**
     * Xem trực tiếp camera
     * @param string $deviceId
     * @return ServiceReturn
     */
    public function viewLive(string $deviceId): ServiceReturn
    {
        $device = $this->findCamera($deviceId);
        if (!$device) {
            return ServiceReturn::error('Thiết bị không tồn tại');
        }

        // Kiểm tra cache trước
        $broadcast = Caching::getCache(CacheKey::CACHE_LIVE_STREAM, $deviceId);
        if ($broadcast) {
            return ServiceReturn::success($broadcast);
        }

        $channel = $device->channels()->where('status', StatusChannel::ONLINE->value)->first();
        if (!$channel) {
            return ServiceReturn::error('Không có kênh nào đang hoạt động');
        }

        $response = $this->sendAuthRequest(self::PATH_LIVE_CHECK, [
            'channelId' => $channel->position,
            'deviceId' => $deviceId,
        ]);

        if ($this->isSuccessResponse($response)) {
            $resultData = $response['result']['data'];
            $stream = $resultData['streams'][0];

            $broadcast = [
                'coverUrl'   => $stream['coverUrl'],
                'streamId'   => $stream['streamId'],
                'hls'        => $stream['hls'],
                'liveToken'  => $stream['liveToken'],
                'channelId'  => $channel->position,
                'liveStatus' => $stream['status'] == 'online'
                    ? StatusChannel::ONLINE->value
                    : StatusChannel::OFFLINE->value,
            ];

            Caching::setCache(CacheKey::CACHE_LIVE_STREAM, $broadcast, $deviceId, 60 * 60);
            return ServiceReturn::success($broadcast);
        }

        LogHelper::error("Xem trực tiếp camera {$deviceId} thất bại: " . json_encode($response));
        return ServiceReturn::error($this->getErrorMessage($response, 'Live stream không tồn tại'));
    }
}
