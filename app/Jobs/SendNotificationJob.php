<?php

namespace App\Jobs;

use App\Enums\NotificationType;
use App\Enums\QueueKey;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendNotificationJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct
    (
        protected int|string $userId,
        protected NotificationType $type,
        protected array $data = [],
    ) {
        $this->onQueue(QueueKey::NOTIFICATIONS->value);
    }

    /**
     * Execute the job.
     */
    public function handle(): void {
        
    }   
}
