<?php

namespace App\Service;

use App\Core\Service\ServiceReturn;
use App\Models\Camera;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Core\LogHelper;

class CameraService
{
    public function __construct(
        protected VideoLiveService $videoLiveService,
    ) {}

    /**
     * Lấy danh sách camera mà user có quyền xem
     * @param User|null $user
     * @return ServiceReturn
     */
    public function getUserCameras(?User $user = null): ServiceReturn
    {
        try {
            /** @var User $user */
            $user = $user ?? Auth::user();

            if (!$user) {
                return ServiceReturn::error('Người dùng chưa đăng nhập');
            }

            // Lấy danh sách camera mà user có quyền xem
            // Camera phải is_active = true và enable = true
            $cameras = $user->cameras()
                ->where('is_active', true)
                ->where('enable', true)
                ->with('showroom:id,name,address')
                ->get()
                ->map(function ($camera) {
                    return [
                        'id' => $camera->id,
                        'name' => $camera->name,
                        'description' => $camera->description,
                        'image' => $camera->image,
                        'device_id' => $camera->device_id,
                        'channel_id' => $camera->channel_id,
                        'device_model' => $camera->device_model,
                        'showroom' => $camera->showroom ? [
                            'id' => $camera->showroom->id,
                            'name' => $camera->showroom->name,
                            'address' => $camera->showroom->address,
                        ] : null,
                    ];
                });

            return ServiceReturn::success([
                'cameras' => $cameras,
            ], 'Lấy danh sách camera thành công');
        } catch (\Throwable $th) {
            LogHelper::error('Lỗi khi lấy danh sách camera: ' . $th->getMessage());
            return ServiceReturn::error('Lỗi hệ thống: ' . $th->getMessage());
        }
    }

    /**
     * Khởi động live stream cho camera
     * @param int $cameraId
     * @param User|null $user
     * @return ServiceReturn
     */
    public function startCameraLive(int $cameraId, ?User $user = null): ServiceReturn
    {
        try {
            /** @var User $user */
            $user = $user ?? Auth::user();  

            if (!$user) {
                return ServiceReturn::error('Người dùng chưa đăng nhập');
            }

            // Kiểm tra quyền truy cập camera
            $camera = $user->cameras()
                ->where('cameras.id', $cameraId)
                ->where('is_active', true)
                ->where('enable', true)
                ->first();

            if (!$camera) {
                return ServiceReturn::error('Không tìm thấy camera hoặc bạn không có quyền truy cập');
            }

            // Kiểm tra camera đã bind chưa
            if (!$camera->bind_status) {
                return ServiceReturn::error('Camera chưa được kết nối với hệ thống. Vui lòng liên hệ quản trị viên.');
            }

            // Khởi động live
            $liveResult = $this->videoLiveService->startLive(
                $camera->device_id,
                $camera->channel_id ?? 0,
                1 // SD stream
            );

            if (!$liveResult['success']) {
                return ServiceReturn::error($liveResult['message']);
            }

            $liveToken = $liveResult['liveToken'];

            // Lấy HLS URL
            $streamInfo = $this->videoLiveService->getLiveStreamInfo($liveToken);

            if (!$streamInfo['success']) {
                // Nếu không lấy được HLS URL, dừng live
                $this->videoLiveService->stopLive($liveToken);
                return ServiceReturn::error($streamInfo['message']);
            }

            return ServiceReturn::success([
                'liveToken' => $liveToken,
                'hlsUrl' => $streamInfo['hlsUrl'],
                'camera' => [
                    'id' => $camera->id,
                    'name' => $camera->name,
                    'device_id' => $camera->device_id,
                    'channel_id' => $camera->channel_id,
                ],
            ], 'Khởi động live thành công');
        } catch (\Throwable $th) {
            LogHelper::error('Lỗi khi khởi động live camera: ' . $th->getMessage());
            return ServiceReturn::error('Lỗi hệ thống: ' . $th->getMessage());
        }
    }

    /**
     * Dừng live stream
     * @param string $liveToken
     * @param User|null $user
     * @return ServiceReturn
     */
    public function stopCameraLive(string $liveToken, ?User $user = null): ServiceReturn
    {
        try {
            $user = $user ?? Auth::user();

            if (!$user) {
                return ServiceReturn::error('Người dùng chưa đăng nhập');
            }

            // Dừng live
            $result = $this->videoLiveService->stopLive($liveToken);

            if (!$result['success']) {
                return ServiceReturn::error($result['message']);
            }

            return ServiceReturn::success([], 'Dừng live thành công');
        } catch (\Throwable $th) {
            LogHelper::error('Lỗi khi dừng live camera: ' . $th->getMessage());
            return ServiceReturn::error('Lỗi hệ thống: ' . $th->getMessage());
        }
    }
}
