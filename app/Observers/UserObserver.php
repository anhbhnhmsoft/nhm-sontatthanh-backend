<?php

namespace App\Observers;

use App\Enums\UserNotificationType;
use App\Enums\UserRole;
use App\Http\DTO\NotificationPayload;
use App\Jobs\SendNotificationJob;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {

        $payload = new NotificationPayload(
            title: 'Xác thực thành công',
            description: 'Chào mừng ' . $user->name . ' đến với ứng dụng!',
            type: UserNotificationType::ZALO_AUTH_SUCCESS,
            data: [],
        );

        SendNotificationJob::dispatch(
            [$user->id],
            $payload,
        )->delay(now()->addSeconds(6));
    }
}
