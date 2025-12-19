<?php

namespace App\Http\Controllers\Api;

use App\Core\Controller\BaseController;
use App\Core\Controller\ListRequest;
use App\Http\Requests\Auth\PushTokenRequest;
use App\Http\Resources\NotificationResource;
use App\Service\NotificationService;
use Illuminate\Http\JsonResponse;

class NotificationController extends BaseController
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}
    /**
     * @param PushTokenRequest $request
     * @return JsonResponse
     */
    public function deviceToken(PushTokenRequest $request): JsonResponse
    {
        $result = $this->notificationService->deviceToken(
            $request->validated('expo_push_token'),
            $request->validated('device_id'),
            $request->validated('device_type')
        );
        if ($result->isSuccess()) {
            return $this->sendSuccess(
                data: $result->getData(),
            );
        }
        return $this->sendError(
            message: $result->getMessage(),
        );
    }

    /**
     * @return JsonResponse
     */
    public function paginate(ListRequest $request): JsonResponse
    {
        $result = $this->notificationService->paginate($request->getFilterOptions());
        if ($result->isSuccess()) {
            return $this->sendSuccess(
                data: NotificationResource::collection($result->getData())->response()->getData(true),
            );
        }
        return $this->sendError(
            message: $result->getMessage(),
        );
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function markRead(int $id): JsonResponse
    {
        $result = $this->notificationService->markRead($id);
        if ($result->isSuccess()) {
            return $this->sendSuccess(
                data: NotificationResource::make($result->getData()),
            );
        }
        return $this->sendError(
            message: $result->getMessage(),
        );
    }

    /**
     * @return JsonResponse
     */
    public function markAllRead(): JsonResponse
    {
        $result = $this->notificationService->markAllRead();
        if ($result->isSuccess()) {
            return $this->sendSuccess(
                data: $result->getData(),
            );
        }
        return $this->sendError(
            message: $result->getMessage(),
        );
    }

    /**
     * @return JsonResponse
     */
    public function unreadCount(): JsonResponse
    {
        $result = $this->notificationService->unreadCount();
        if ($result->isSuccess()) {
            return $this->sendSuccess(
                data: $result->getData(),
            );
        }
        return $this->sendError(
            message: $result->getMessage(),
        );
    }
}
