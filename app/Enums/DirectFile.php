<?php

namespace App\Enums;

enum DirectFile: string
{
    case AVATARS = 'avatars';
    case BANNERS = 'banners';
    case NEWS = 'news';
    case PRODUCTS = 'products';
    case SHOWROOMS = 'showrooms';
    case BRANDS = 'brands';
    case CATEGORIES = 'categories';
    case CAMERAS = 'cameras';

    public static function makePathById(DirectFile $type, string $id): string
    {
        return $type->value . "/" . $id;
    }
}
