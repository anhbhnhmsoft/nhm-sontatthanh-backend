<?php

namespace App\Core\Cache;

enum CacheKey: string
{
    /**
     * Lưu mã OTP đăng ký.
     */
    case CACHE_KEY_OTP_REGISTER = 'CACHE_KEY_OTP_REGISTER';

    /**
     * Lưu số lần nhập sai OTP đăng ký.
     */
    case CACHE_KEY_OTP_REGISTER_ATTEMPTS = 'CACHE_KEY_OTP_REGISTER_ATTEMPTS';

    /**
     * Lưu số lần gửi lại OTP đăng ký.
     */
    case CACHE_KEY_RESEND_REGISTER_OTP = 'CACHE_KEY_RESEND_REGISTER_OTP';

    /**
     * Lưu token đăng ký tài khoản.
     */
    case CACHE_KEY_REGISTER_TOKEN = 'CACHE_KEY_REGISTER_TOKEN';

    /**
     * Lưu trạng thái block đăng ký otp
     */
    case CACHE_KEY_OTP_REGISTER_BLOCK = 'CACHE_KEY_OTP_REGISTER_BLOCK';

    /**
     * Lưu mã OTP quên mật khẩu.
     */
    case CACHE_KEY_OTP_FORGOT_PASSWORD = 'CACHE_KEY_OTP_FORGOT_PASSWORD';

    /**
     * Lưu số lần nhập sai OTP quên mật khẩu.
     */
    case CACHE_KEY_OTP_FORGOT_PASSWORD_ATTEMPTS = 'CACHE_KEY_OTP_FORGOT_PASSWORD_ATTEMPTS';

    /**
     * Lưu số lần gửi lại OTP quên mật khẩu.
     */
    case CACHE_KEY_RESEND_FORGOT_PASSWORD_OTP = 'CACHE_KEY_RESEND_FORGOT_PASSWORD_OTP';

    /**
     * Lưu trạng thái block gửi OTP quên mật khẩu.
     */
    case CACHE_KEY_OTP_FORGOT_PASSWORD_BLOCK = 'CACHE_KEY_OTP_FORGOT_PASSWORD_BLOCK';

    /**
     * Lưu token reset password sau khi verify OTP thành công.
     */
    case CACHE_KEY_FORGOT_PASSWORD_TOKEN = 'CACHE_KEY_FORGOT_PASSWORD_TOKEN';
    /**
     * Lưu cấu hình hệ thống.
     */
    case CACHE_KEY_CONFIG = 'CACHE_KEY_CONFIG';

    /**
     * Lưu thông tin online của người dùng.
     */
    case CACHE_USER_HEARTBEAT = 'CACHE_USER_HEARTBEAT';

    /**
     * Lưu thông tin file của người dùng.
     */
    case CACHE_USER_FILE = 'CACHE_USER_FILE';

    /**
     * Lưu lại thư viện camera của sale
     */
    case CACHE_SALE_CAMERA = 'CACHE_SALE_CAMERA';

    /**
     * Lưu lại danh sách showroom
     */
    case CACHE_SHOWROOM = 'CACHE_SHOWROOM';

    /**
     * Lưu lại danh sách banner
     */
    case CACHE_BANNER = 'CACHE_BANNER';

    /**
     * Lưu lại danh sách banner
     */
    case CACHE_BRAND = 'CACHE_BRAND';

    /**
     * Lưu lại danh sách line
     */
    case CACHE_LINE = 'CACHE_LINE';

    /**
     * Lưu lại danh sách product
     */
    case CACHE_PRODUCT = 'CACHE_PRODUCT';

    /**
     * Lưu lại cofig key
     */
    case CACHE_CONFIG_KEY = 'CACHE_CONFIG_KEY';

    /**
     * Lưu lại access token
     */
    case CACHE_ACCESS_TOKEN = 'CACHE_ACCESS_TOKEN';

    /**
     * Cache live stream info
     */
    case CACHE_LIVE_STREAM_INFO = 'CACHE_LIVE_STREAM_INFO';

    /**
     * Cache live list
     */
    case CACHE_LIVE_STREAM = 'CACHE_LIVE_STREAM';

    /**
     * Cache token tạm zalo auth
     */
    case CACHE_ZALO_AUTH_TOKEN = 'CACHE_ZALO_AUTH_TOKEN';

    /**
     * Cache token tạm zalo auth
     */
    case CACHE_ZALO_AUTH_TOKEN_VERIFY = 'CACHE_ZALO_AUTH_TOKEN_VERIFY';
    case CACHE_ZALO_AUTH_CODE_VERIFIER = 'CACHE_ZALO_AUTH_CODE_VERIFIER';
    case CACHE_ZALO_AUTH_STATE = 'CACHE_ZALO_AUTH_STATE';
    /**
     * Cache token tạm apple auth
     */
    case CACHE_APPLE_AUTH_TOKEN = 'CACHE_APPLE_AUTH_TOKEN';
    case CACHE_APPLE_AUTH_TOKEN_VERIFY = 'CACHE_APPLE_AUTH_TOKEN_VERIFY';
    case CACHE_APPLE_AUTH_CODE_VERIFIER = 'CACHE_APPLE_AUTH_CODE_VERIFIER';
    case CACHE_APPLE_AUTH_STATE = 'CACHE_APPLE_AUTH_STATE';
    case CACHE_KEY_APPLE_PUBLIC_KEYS = 'CACHE_KEY_APPLE_PUBLIC_KEYS';
    case CACHE_CATEGORY_NEWS = 'CACHE_CATEGORY_NEWS';
}
