# Zalo Authentication - Summary of Changes

## Tổng quan thay đổi

Đã chuyển đổi flow xác thực Zalo từ **mobile-based** sang **web-based OAuth flow** để tăng tính bảo mật và tuân thủ OAuth 2.0 best practices.

## Flow cũ (Deprecated)

```
Mobile App → Zalo SDK → Access Token → Backend API
```

## Flow mới (Recommended)

```
Mobile App → Browser → Zalo OAuth → Backend Callback → Deeplink → Mobile App
```

## Files đã thay đổi

### 1. New Files

-   ✅ `app/Http/Controllers/Web/ZaloAuthController.php` - Web OAuth controller
-   ✅ `resources/views/zalo-callback.blade.php` - Callback page với deeplink redirect
-   ✅ `ZALO_AUTH_INTEGRATION.md` - Hướng dẫn chi tiết
-   ✅ `test-zalo-oauth.sh` - Testing script

### 2. Modified Files

-   ✅ `app/Service/ZaloService.php`

    -   Added `getAuthorizationUrl()` - Tạo Zalo OAuth URL
    -   Added `getAccessTokenFromCode()` - Exchange code → access token

-   ✅ `app/Service/AuthService.php`

    -   Added `getAccessTokenFromCode()` - Wrapper method

-   ✅ `app/Http/Controllers/Api/AuthController.php`

    -   Updated `zaloAuthenticate()` - Hỗ trợ cả `code` và `access_token`

-   ✅ `routes/web.php`

    -   Added `/auth/zalo/redirect` - Redirect to Zalo
    -   Added `/auth/zalo/callback` - OAuth callback

-   ✅ `config/app.php`

    -   Added `mobile_deeplink_scheme` config

-   ✅ `.env.example`
    -   Added `MOBILE_DEEPLINK_SCHEME`

## Cấu hình cần thiết

### Backend (.env)

```env
APP_URL=https://your-domain.com
MOBILE_DEEPLINK_SCHEME=nhmapp
```

### Zalo Developer Console

-   Redirect URI: `https://your-domain.com/auth/zalo/callback`

### Mobile App

-   Configure deeplink scheme: `nhmapp://`
-   Handle deeplink: `nhmapp://auth/zalo?token=xxx&success=true`

## API Endpoints

### Web Routes (Mới)

```
GET  /auth/zalo/redirect  - Redirect to Zalo OAuth
GET  /auth/zalo/callback  - OAuth callback handler
```

### API Routes (Backward Compatible)

```
POST /api/auth/zalo-authenticate
Body: { "code": "..." }  // Recommended
Body: { "access_token": "..." }  // Deprecated but still works
```

## Mobile Integration

### Button Click

```javascript
const loginWithZalo = () => {
    Linking.openURL("https://your-domain.com/auth/zalo/redirect");
};
```

### Deeplink Handler

```javascript
Linking.addEventListener("url", (event) => {
    const url = event.url; // nhmapp://auth/zalo?token=xxx&success=true
    const params = parseURL(url);

    if (params.success && params.token) {
        saveToken(params.token);
        navigateToHome();
    }
});
```

## Testing

### Local Testing với ngrok

```bash
# 1. Start ngrok
ngrok http 8000

# 2. Update .env
APP_URL=https://your-ngrok-url.ngrok.io

# 3. Update Zalo Developer Console
Redirect URI: https://your-ngrok-url.ngrok.io/auth/zalo/callback

# 4. Test
./test-zalo-oauth.sh
```

### Test Deeplink

```bash
# iOS (Safari)
nhmapp://auth/zalo?token=test&success=true

# Android (ADB)
adb shell am start -W -a android.intent.action.VIEW \
  -d "nhmapp://auth/zalo?token=test&success=true"
```

## Migration Path

1. ✅ **Phase 1**: Implement deeplink handler trong mobile app
2. ✅ **Phase 2**: Test với new flow
3. ⏳ **Phase 3**: Deprecate old flow (remove Zalo SDK from mobile)
4. ⏳ **Phase 4**: Remove `access_token` support from API

## Backward Compatibility

API endpoint vẫn hỗ trợ cả 2 cách:

-   ✅ `code` parameter (new, recommended)
-   ✅ `access_token` parameter (old, deprecated)

## Security Improvements

1. ✅ Không expose App Secret trên mobile
2. ✅ OAuth flow chuẩn
3. ✅ HTTPS required cho production
4. ✅ Token validation trên backend

## Next Steps

1. Configure Zalo Developer Console
2. Update mobile app với deeplink handler
3. Test flow trên staging
4. Deploy to production
5. Monitor logs và user feedback

## Support & Troubleshooting

-   📖 Chi tiết: `ZALO_AUTH_INTEGRATION.md`
-   🧪 Testing: `./test-zalo-oauth.sh`
-   📝 Logs: `storage/logs/laravel.log`
-   🔧 Routes: `php artisan route:list | grep zalo`

## Questions?

Contact: [Your Team Contact]
