<?php

namespace App\Http\Controllers\Api;

use App\Core\Controller\BaseController;
use App\Service\CameraService;
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
        $request->validate([
            'camera_id' => 'required|integer|exists:cameras,id',
        ]);

        $result = $this->cameraService->startCameraLive(
            $request->input('camera_id')
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
