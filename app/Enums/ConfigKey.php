<?php

namespace App\Enums;

enum ConfigKey: string
{
    case APP_ID = 'APP_ID';
    case APP_SECRET = 'APP_SECRET';
    case APP_ID_ZALO = 'APP_ID_ZALO';
    case APP_SECRET_ZALO = 'APP_SECRET_ZALO';
}