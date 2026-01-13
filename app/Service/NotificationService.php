<?php

namespace App\Service;

use App\Core\Controller\FilterDTO;
use App\Core\LogHelper;
use App\Core\Service\BaseService;
use App\Core\Service\ServiceException;
use App\Core\Service\ServiceReturn;
use App\Enums\UserNotificationStatus;
use App\Http\DTO\NotificationPayload;
use App\Models\Notification;
use App\Models\UserDevice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService extends BaseService
{
    protected mixed $nodeSendNotificationUrl;
    protected mixed $accessToken;

    public function __construct(
        protected UserDevice $userDeviceModel,
        protected Notification $notificationModel
    ) {
        $this->nodeSendNotificationUrl = config('services.node_server.notification_url');
        $this->accessToken = config('services.node_server.access_token');
    }

    /**
     * @param string $expoPushToken
     * @param ?string $deviceId
     * @param ?string $deviceType
     * @return ServiceReturn
     */
    public function deviceToken(string $expoPushToken, ?string $deviceId, ?string $deviceType): ServiceReturn
    {
        try {
            $user = Auth::user();
            if (!$user) return ServiceReturn::error('Không tìm thấy người dùng');

            $this->userDeviceModel->where('expo_push_token', $expoPushToken)
                ->where('user_id', '!=', $user->id)
                ->delete();

            $this->userDeviceModel->upsert(
                [
                    'user_id' => $user->id,
                    'device_id' => $deviceId,
                    'expo_push_token' => $expoPushToken,
                    'device_type' => $deviceType,
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
                ['user_id', 'device_id'],
                ['expo_push_token', 'device_type', 'updated_at']
            );

            $userDevice = $this->userDeviceModel
                ->where('user_id', $user->id)
                ->where('device_id', $deviceId)
                ->first();

            return ServiceReturn::success($userDevice);
        } catch (\Exception $th) {
            LogHelper::error('Xảy ra lỗi ở NotificationService@deviceToken: ' . $th->getMessage());
            return ServiceReturn::error('Xảy ra lỗi khi thêm device token');
        }
    }

    /**
     * @param FilterDTO $filterDTO
     * @return ServiceReturn
     */
    public function paginate(FilterDTO $filterDTO): ServiceReturn
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return ServiceReturn::error('Không tìm thấy người dùng');
            }

            $notifications = $this->notificationModel
                ->where('user_id', $user->id)
                ->orderBy($filterDTO->sortBy, $filterDTO->direction)
                ->paginate(perPage: $filterDTO->perPage, page: $filterDTO->page);

            return ServiceReturn::success($notifications);
        } catch (\Exception $th) {
            LogHelper::error('Xảy ra lỗi ở NotificationService@paginate: ' . $th->getMessage());
            return ServiceReturn::error('Xảy ra lỗi khi lấy danh sách thông báo');
        }
    }

    /**
     * @param int $id
     * @return ServiceReturn
     */
    public function markRead(int $id): ServiceReturn
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return ServiceReturn::error('Không tìm thấy người dùng');
            }

            $notification = $this->notificationModel->where('user_id', $user->id)->where('id', $id)->first();
            if (!$notification) {
                return ServiceReturn::error('Không tìm thấy thông báo');
            }

            $notification->read_at = now();
            $notification->save();

            return ServiceReturn::success($notification);
        } catch (\Exception $th) {
            LogHelper::error('Xảy ra lỗi ở NotificationService@markRead: ' . $th->getMessage());
            return ServiceReturn::error('Xảy ra lỗi khi đánh dấu thông báo đã đọc');
        }
    }

    /**
     * @return ServiceReturn
     */
    public function markAllRead(): ServiceReturn
    {
        try {
            $user = Auth::user();
            if (!$user) {
                throw new ServiceException('Không tìm thấy người dùng');
            }

            $notifications = $this->notificationModel->where('user_id', $user->id)->where('read_at', null)->update([
                'read_at' => now(),
            ]);

            return ServiceReturn::success([
                'success' => true,
            ]);
        } catch (\Exception $th) {
            LogHelper::error('Xảy ra lỗi ở NotificationService@markAllRead: ' . $th->getMessage());
            return ServiceReturn::error('Xảy ra lỗi khi đánh dấu tất cả thông báo đã đọc');
        }
    }

    /**
     * @return ServiceReturn
     */
    public function unreadCount(): ServiceReturn
    {
        try {
            $user = Auth::user();
            if (!$user) {
                throw new ServiceException('Không tìm thấy người dùng');
            }

            $notifications = $this->notificationModel->where('user_id', $user->id)->where('read_at', null)->count();

            return ServiceReturn::success([
                'count' => $notifications,
            ]);
        } catch (\Exception $th) {
            LogHelper::error('Xảy ra lỗi ở NotificationService@unreadCount: ' . $th->getMessage());
            return ServiceReturn::error('Xảy ra lỗi khi lấy số lượng thông báo chưa đọc');
        }
    }

    /**
     * Push thông báo
     * @param NotificationPayload $payload
     * @return ServiceReturn
     */
    public function pushNotification(NotificationPayload $payload, array $userIds): ServiceReturn
    {
        // 1. Tối ưu: Lấy tất cả token cần thiết trong 1 query
        $devices = $this->userDeviceModel->query()
            ->whereIn('user_id', $userIds)
            ->where('is_active', 1)
            ->get();
        $tokensByUser = $devices->groupBy('user_id')
            ->map(fn($group) => $group->pluck('expo_push_token')->toArray())
            ->toArray();
        DB::beginTransaction();
        try {
            $batch = array_reduce($userIds, function ($carry, $userId) use ($tokensByUser, $payload) {
                $tokens = $tokensByUser[$userId] ?? [];
                if (empty($tokens)) {
                    // Không có token, bỏ qua người dùng này
                    return $carry;
                }
                // Tạo notification trước
                $notification = $this->notificationModel->create([
                    'user_id' => $userId,
                    'title' => $payload->title,
                    'description' => $payload->description,
                    'data' => json_encode($payload->data),
                    'type' => $payload->type->value,
                ]);
                // Thêm vào Batch gửi đi
                $carry[] = [
                    'notification_id' => (string)$notification->id,
                    'user_id' => (string)$userId,
                    'tokens' => $tokens
                ];
                return $carry;
            }, []);
            if (empty($batch)) {
                DB::rollBack();
                Log::info('SendNotifications: Không có user nào có token để gửi.');
                return ServiceReturn::success(['message' => 'No tokens found for provided users']);
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('SendNotifications: Có lỗi xảy ra lúc insert noti ' . $exception->getMessage());
            return ServiceReturn::error('Lỗi khởi tạo thông báo: ' . $exception->getMessage());
        }
        $notificationIds = array_column($batch, 'notification_id');
        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'x-api-key-node' => $this->accessToken,
                ])
                ->post($this->nodeSendNotificationUrl, [
                    'common_payload' => [
                        'title' => $payload->title,
                        'description' => $payload->description,
                        'data' => $payload->data,
                        'notification_type' => $payload->type->value,
                    ],
                    'batch' => $batch,
                ]);

            if ($response->successful()) {
                $nodeResult = $response->json();
                $isSuccessful = $nodeResult['status'] ?? false;
                if ($isSuccessful) {
                    // ID bản ghi thành công/thất bại (chuỗi số)
                    $successIds = $nodeResult['success_notifications'] ?? [];
                    $errorIds = $nodeResult['error_notifications'] ?? [];
                    if (!empty($successIds)) {
                        $this->notificationModel->whereIn('id', $successIds)
                            ->update(['status' => UserNotificationStatus::SENT->value]);
                    }
                    if (!empty($errorIds)) {
                        // Cập nhật trạng thái thất bại (FAILED hoặc NO_TOKEN_PROVIDED)
                        $this->notificationModel->whereIn('id', $errorIds)
                            ->update(['status' => UserNotificationStatus::FAILED->value]);
                    }
                    Log::info('Gưi thông báo thành công.', [
                        'success_count' => count($successIds),
                        'error_count' => count($errorIds),
                        'total_sent' => count($successIds) + count($errorIds),
                    ]);
                    return ServiceReturn::success($nodeResult);
                } else {
                    Log::error('SendNotifications: Node.js service returned error status.', [
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);
                    // Nếu mà lỗi thì phải error luôn các notification
                    $this->notificationModel->whereIn('id', $notificationIds)->update(['status' => UserNotificationStatus::FAILED->value]);
                    return ServiceReturn::error('Node Service Error');
                }
            } else {
                Log::error('SendNotifications: Node.js service returned HTTP error.', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                $this->notificationModel->whereIn('id', $notificationIds)->update(['status' => UserNotificationStatus::FAILED->value]);
                return ServiceReturn::error('Node Service HTTP Error');
            }
        } catch (\Exception $exception) {
            Log::critical('SendNotifications: Connection or Timeout error.', ['error' => $exception->getMessage()]);
            $this->notificationModel->whereIn('id', $notificationIds)->update(['status' => UserNotificationStatus::FAILED->value]);
            return ServiceReturn::error('Connection Error: ' . $exception->getMessage());
        }
    }
}
