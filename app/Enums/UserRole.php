<?php

namespace App\Enums;

/**
 * Enum cho các vai trò người dùng.
 * Dùng Backed Enum (int) để map 1:1 với database.
 */
enum UserRole: int
{
    case CTV = 1;
    case SALE = 2;
    case ADMIN = 3;

    public function label(): string
    {
        return match ($this) {
            self::CTV => 'CTV',
            self::SALE => 'Sale',
            self::ADMIN => 'Quản trị viên',
        };
    }

    public function prefix(): string
    {
        return match ($this) {
            self::CTV => 'CTV',
            self::SALE => 'SL',
            self::ADMIN => 'QTV',
        };
    }

    public static function toOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }
        return $options;
    }

    public static function getLabel(int $value): string
    {
        return self::tryFrom($value)?->label() ?? '';
    }
}
