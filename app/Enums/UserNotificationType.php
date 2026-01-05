<?php

namespace App\Enums;

enum UserNotificationType: int
{
    case WELCOME = 1;
    case ZALO_AUTH_SUCCESS = 2;

    case SYSTEM_NOTICE = 3;
    public static function label(int $value): string
    {
        return match ($value) {
            self::WELCOME->value => 'Chào mừng',
            self::ZALO_AUTH_SUCCESS->value => 'Xác thực thành công',
            self::SYSTEM_NOTICE->value => 'Thông báo hệ thống'
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label($case->value)])
            ->toArray();
    }
}
