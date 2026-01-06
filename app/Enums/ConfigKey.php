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
}
