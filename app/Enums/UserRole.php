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
}
