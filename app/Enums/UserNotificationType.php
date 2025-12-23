<?php

namespace App\Enums;

enum UserNotificationType: int
{
    case WELCOME = 1;
    public static function label(int $value): string
    {
        return match ($value) {
            self::WELCOME->value => 'Chào mừng',
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label($case->value)])
            ->toArray();
    }
}
