<?php

namespace App\Http\Controllers\Api;

use App\Core\Controller\BaseController;
use App\Service\CameraService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request; 
class CameraController extends BaseController
{
    protected CameraService $cameraService;

    public function __construct(CameraService $cameraService)
    {
        $this->cameraService = $cameraService;
    }
    /**
     * Khởi động live stream cho camera
     */
    public function startLive(Request $request): JsonResponse
    {
        $now = Carbon::now();
        if($now->hour < 7 || $now->hour > 21){
            return $this->sendError("Hệ thống camera chỉ hoạt động từ 7h đến 21h");
        }
        $request->validate(
            [
                'device_id' => 'required|string|exists:cameras,device_id',
                'channel_no' => 'nullable|integer',
            ],
            [
                'device_id.required' => 'Vui lòng nhập device_id',
                'device_id.exists' => 'Device_id không tồn tại',
                'channel_no.integer' => 'Channel_no phải là số nguyên',
            ]
        );

        $result = $this->cameraService->startCameraLive(
            $request->input('device_id'),
            channelNo: $request->input('channel_no')
        );

        if ($result->isError()) {
            return $this->sendError($result->getMessage());
        }

        return $this->sendSuccess(
            $result->getData(),
            $result->getMessage()
        );
    }
}
