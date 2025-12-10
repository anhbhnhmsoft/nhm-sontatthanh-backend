<?php

namespace App\Core;

use Carbon\Carbon;
use Illuminate\Support\Str;

final class Helper
{

    /**
     * Tạo ID duy nhất dựa trên timestamp hiện tại.
     * @return int
     */
    public static function getTimestampAsId(): int
    {
        // Get microtime float
        $microFloat = microtime(true);
        $microTime = Carbon::createFromTimestamp($microFloat);
        $formatted = $microTime->format('ymdHisu');
        usleep(100);
        return (int)$formatted;
    }

    /**
     * Tạo mã tham gia ngẫu nhiên 8 ký tự in hoa.
     * @return string
     */
    public static function generateReferCode(): string
    {
        return strtoupper(substr(Str::uuid()->toString(), 0, 8));
    }
    /**
     * Tạo token ngẫu nhiên 60 ký tự.
     * @return string
     */
    public static function generateTokenRandom(): string
    {
        return Str::random(60);
    }

}
