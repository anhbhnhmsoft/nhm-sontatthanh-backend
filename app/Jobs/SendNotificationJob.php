<?php

namespace App\Jobs;

use App\Enums\QueueKey;
use App\Http\DTO\NotificationPayload;
use App\Service\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendNotificationJob implements ShouldQueue
{
    use Queueable;

    private NotificationPayload $payload;
    private array $userIds;

    /**
     * Create a new job instance.
     */
    public function __construct(
        array $userIds,
        NotificationPayload $payload,
    ) {
        $this->onQueue(QueueKey::NOTIFICATIONS->value);
        $this->userIds = $userIds;
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     */
    public function handle(NotificationService $notificationService): void
    {
        try {
            Log::info('Start SendNotificationJob', ['count_users' => count($this->userIds)]);
            $notificationService->pushNotification($this->payload, $this->userIds);
            Log::info('End SendNotificationJob');
        } catch (\Throwable $th) {
            Log::error('Job SendNotificationJob Failed: ' . $th->getMessage());
            // Optionally release the job back to the queue or fail it
            $this->fail($th);
        }
    }
}
