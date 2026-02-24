<?php

namespace App\Enums;

enum ConfigKey: string
{
    case APP_ID = 'APP_ID_IMOUCAM';
    case APP_SECRET = 'APP_SECRET_IMOUCAM';
    case APP_ID_ZALO = 'APP_ID_ZALO';
    case APP_SECRET_ZALO = 'APP_SECRET_ZALO';
    case APP_REMINDER = 'APP_REMINDER';
    case APP_REMINDER_NAME = 'APP_REMINDER_NAME';
    case APP_REMINDER_POSITION = 'APP_REMINDER_POSITION';
    case APPLE_CLIENT_ID = 'APPLE_CLIENT_ID';
    case APPLE_TEAM_ID = 'APPLE_TEAM_ID';
    case APPLE_KEY_ID = 'APPLE_KEY_ID';
    case APPLE_PRIVATE_KEY = 'APPLE_PRIVATE_KEY';
    case APPLE_REDIRECT_URI = 'APPLE_REDIRECT_URI';
    case APP_AVATAR = 'APP_AVATAR';
    case APP_SALE_CODE = 'APP_SALE_CODE';

    public static function getConfigDirector(): array
    {
        return [
            self::APP_AVATAR,
            self::APP_REMINDER,
            self::APP_REMINDER_NAME,
            self::APP_REMINDER_POSITION,
        ];
    }
}
