<?php

namespace App\Enums;

enum UserNotificationType: int
{
    case WELCOME = 1;
    public function label(): string
    {
        return match ($this) {
            self::WELCOME => 'Chào mừng bạn đến với hệ thống phân phối SƠN TÂT THÀNH',
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
