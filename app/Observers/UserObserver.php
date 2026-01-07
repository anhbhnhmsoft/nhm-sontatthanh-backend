<?php

namespace App\Observers;

use App\Enums\UserNotificationType;
use App\Enums\UserRole;
use App\Http\DTO\NotificationPayload;
use App\Jobs\SendNotificationJob;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        if ($user->role == UserRole::CTV->value) {
            $sales = User::where('role', UserRole::SALE->value)->get();
            if ($sales->isNotEmpty()) {
                $randomSale = $sales->random();
                $user->sale_id = $randomSale->id;
                $user->save();
            }

            $sale = $user->sale;

        }
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
