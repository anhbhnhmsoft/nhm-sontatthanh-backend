<?php

namespace App\Enums;

enum NotificationSatus: int
{
    case PENDING = 0;      // Chờ gửi
    case SENT = 1;        // Đã gửi
    case FAILED = 2;      // Gửi thất bại
    case READ = 3;        // Đã đọc
}
