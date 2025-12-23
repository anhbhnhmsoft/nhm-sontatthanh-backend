<?php

namespace App\Http\DTO;

use App\Enums\UserNotificationType;

class NotificationPayload
{
    public function __construct(
        public string $title,
        public string $description,
        public UserNotificationType $type,
        public array $data,
    ) {}
}
