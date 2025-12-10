<?php

namespace App\Enums;

enum NewsType: int
{
    case OUTSTANDING = 1; // Nổ i bật 
    case EVENT = 2; // Sự kiện 
    case PROJECT = 3; // Dự án 

    public function label(): string
    {
        return match ($this) {
            self::OUTSTANDING => 'Nổ i bật',
            self::EVENT => 'Sự kiện',
            self::PROJECT => 'Dự án',
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
