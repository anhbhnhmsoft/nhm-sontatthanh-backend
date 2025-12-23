# 🔐 Zalo OAuth Web Authentication

## 📋 Tổng quan

Hệ thống xác thực Zalo đã được **nâng cấp** từ mobile-based sang **web-based OAuth flow** để:

-   ✅ Tăng tính bảo mật (không expose App Secret trên mobile)
-   ✅ Tuân thủ OAuth 2.0 best practices
-   ✅ Dễ bảo trì và mở rộng
-   ✅ Tương thích ngược với flow cũ

## 🚀 Quick Start

### 1. Backend (5 phút)

```bash
# Add to .env
echo "MOBILE_DEEPLINK_SCHEME=nhmapp" >> .env

# Clear cache
php artisan config:clear

# Test routes
php artisan route:list | grep zalo
```

### 2. Mobile App (10 phút)

```javascript
// Login button
<TouchableOpacity
    onPress={() =>
        Linking.openURL("https://your-domain.com/auth/zalo/redirect")
    }
>
    <Text>Đăng nhập với Zalo</Text>
</TouchableOpacity>;

// Deeplink handler
Linking.addEventListener("url", ({ url }) => {
    const params = new URLSearchParams(url.split("?")[1]);
    if (params.get("success") === "true") {
        saveToken(params.get("token"));
        navigateToHome();
    }
});
```

### 3. Test (2 phút)

```bash
# Run test script
./test-zalo-oauth.sh

# Or manually test
open https://your-domain.com/auth/zalo/redirect
```

## 📚 Tài liệu

| File                                                 | Mô tả                   | Thời gian đọc |
| ---------------------------------------------------- | ----------------------- | ------------- |
| [QUICK_START_ZALO.md](QUICK_START_ZALO.md)           | Hướng dẫn bắt đầu nhanh | 5 phút        |
| [ZALO_AUTH_SUMMARY.md](ZALO_AUTH_SUMMARY.md)         | Tổng quan thay đổi      | 3 phút        |
| [ZALO_AUTH_INTEGRATION.md](ZALO_AUTH_INTEGRATION.md) | Hướng dẫn chi tiết      | 15 phút       |
| [ZALO_FLOW_DIAGRAM.md](ZALO_FLOW_DIAGRAM.md)         | Sơ đồ flow              | 5 phút        |
| [ZALO_CHECKLIST.md](ZALO_CHECKLIST.md)               | Checklist triển khai    | 10 phút       |

## 🎯 Flow mới

```
Mobile App → Browser → Zalo OAuth → Backend → Deeplink → Mobile App
     ↓          ↓          ↓            ↓          ↓          ↓
  [Button]  [Login]  [Approve]    [Exchange]  [Redirect]  [Login]
```

**Chi tiết:**

1. User click "Đăng nhập với Zalo"
2. Mở browser với URL: `/auth/zalo/redirect`
3. Redirect đến Zalo OAuth page
4. User đăng nhập và approve
5. Zalo callback với code
6. Backend exchange code → access token
7. Get user info từ Zalo
8. Create/login user trong database
9. Redirect về mobile app qua deeplink
10. Mobile app lưu token và navigate

## 🔧 Cấu hình

### Backend (.env)

```env
APP_URL=https://your-domain.com
MOBILE_DEEPLINK_SCHEME=nhmapp
```

### Zalo Developer Console

```
Redirect URI: https://your-domain.com/auth/zalo/callback
```

### Mobile App

**iOS (Info.plist):**

```xml
<key>CFBundleURLSchemes</key>
<array>
    <string>nhmapp</string>
</array>
```

**Android (AndroidManifest.xml):**

```xml
<data android:scheme="nhmapp" android:host="auth" />
```

## 📡 API Endpoints

### Web Routes

```
GET  /auth/zalo/redirect  → Redirect to Zalo OAuth
GET  /auth/zalo/callback  → Handle OAuth callback
```

### API Routes

```
POST /api/auth/zalo-authenticate
Body: { "code": "..." }           // New (recommended)
Body: { "access_token": "..." }   // Old (deprecated)
```

## 🧪 Testing

### Test Routes

```bash
php artisan route:list | grep zalo
```

### Test Flow

```bash
./test-zalo-oauth.sh
```

### Test Deeplink

```bash
# iOS
open "nhmapp://auth/zalo?token=test&success=true"

# Android
adb shell am start -W -a android.intent.action.VIEW \
  -d "nhmapp://auth/zalo?token=test&success=true"
```

## 📦 Files Changed

### New Files

-   ✅ `app/Http/Controllers/Web/ZaloAuthController.php`
-   ✅ `resources/views/zalo-callback.blade.php`
-   ✅ Documentation files (5 files)
-   ✅ `test-zalo-oauth.sh`

### Modified Files

-   ✅ `app/Service/ZaloService.php`
-   ✅ `app/Service/AuthService.php`
-   ✅ `app/Http/Controllers/Api/AuthController.php`
-   ✅ `routes/web.php`
-   ✅ `config/app.php`
-   ✅ `.env.example`

## ✅ Checklist

### Backend

-   [x] Code implementation
-   [x] Routes registered
-   [x] Documentation created
-   [ ] .env configured
-   [ ] Zalo Developer Console configured
-   [ ] Testing completed

### Mobile App

-   [ ] Deeplink configured
-   [ ] Login button implemented
-   [ ] Deeplink handler implemented
-   [ ] Testing completed

### Deployment

-   [ ] Staging tested
-   [ ] Production deployed
-   [ ] Monitoring setup

## 🐛 Troubleshooting

### Deeplink không hoạt động

```bash
# iOS: Rebuild app
cd ios && pod install && cd ..
npx react-native run-ios

# Android: Rebuild app
npx react-native run-android
```

### Zalo OAuth Error

```bash
# Check logs
tail -f storage/logs/laravel.log

# Check config
php artisan tinker
>>> config('app.url')
>>> config('app.mobile_deeplink_scheme')
```

### Routes không tìm thấy

```bash
# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Verify routes
php artisan route:list | grep zalo
```

## 📞 Support

-   📖 **Documentation**: Xem các file .md trong thư mục
-   🧪 **Testing**: Chạy `./test-zalo-oauth.sh`
-   📝 **Logs**: `tail -f storage/logs/laravel.log`
-   🔍 **Debug**: `php artisan tinker`

## 🎓 Learning Resources

1. **Quick Start** → `QUICK_START_ZALO.md` (5 phút)
2. **Flow Diagram** → `ZALO_FLOW_DIAGRAM.md` (5 phút)
3. **Full Guide** → `ZALO_AUTH_INTEGRATION.md` (15 phút)
4. **Checklist** → `ZALO_CHECKLIST.md` (theo dõi tiến độ)

## 🔄 Migration Path

1. ✅ **Phase 1**: Implement new flow (DONE)
2. ⏳ **Phase 2**: Test và deploy
3. ⏳ **Phase 3**: User adoption
4. ⏳ **Phase 4**: Deprecate old flow

## 🎯 Next Steps

1. [ ] Configure `.env` với APP_URL
2. [ ] Configure Zalo Developer Console
3. [ ] Implement mobile app deeplink
4. [ ] Test end-to-end flow
5. [ ] Deploy to staging
6. [ ] Test with real users
7. [ ] Deploy to production

---

**Status**: ✅ Backend Complete | ⏳ Configuration Pending | ⏳ Mobile Pending

**Last Updated**: 2025-12-23

**Version**: 1.0.0
