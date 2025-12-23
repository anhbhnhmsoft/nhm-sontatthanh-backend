<?php

namespace App\Core;

use App\Enums\UserRole;
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
    public static function generateReferCode($prefix): string
    {
        return $prefix.'-'.strtoupper(substr(Str::uuid()->toString(), 0, 5));
    }
    /**
     * Tạo token ngẫu nhiên 60 ký tự.
     * @return string
     */
    public static function generateTokenRandom(): string
    {
        return Str::random(60);
    }

    public static function generateCodeVerifier(): string
    {
        return bin2hex(random_bytes(32)); // ~64 ký tự (OK > 43)
    }

    public static function generateCodeChallenge(string $codeVerifier): string
    {
        return rtrim(
            strtr(
                base64_encode(hash('sha256', $codeVerifier, true)),
                '+/',
                '-_'
            ),
            '='
        );
    }


}
