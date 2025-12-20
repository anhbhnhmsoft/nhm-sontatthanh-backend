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
            );

            if (!$liveResult->isSuccess()) {
                return ServiceReturn::error($liveResult->getMessage());
            }

            $camera->update([
                'live_status' => true,
            ]); 


            return ServiceReturn::success(data: $liveResult->getData(), message: 'Khởi động live thành công');
        } catch (\Throwable $th) {
            LogHelper::error('Lỗi khi khởi động live camera: ' . $th->getMessage());
            return ServiceReturn::error('Lỗi hệ thống: ' . $th->getMessage());
        }
    }
}
