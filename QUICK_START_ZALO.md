# Quick Start - Zalo OAuth Integration

## 🚀 Bắt đầu nhanh

### 1. Cấu hình Backend (5 phút)

```bash
# 1. Thêm vào .env
echo "MOBILE_DEEPLINK_SCHEME=nhmapp" >> .env

# 2. Clear cache
php artisan config:clear
php artisan cache:clear

# 3. Test routes
php artisan route:list | grep zalo
```

**Kết quả mong đợi:**

```
POST   api/auth/zalo-authenticate
GET    auth/zalo/redirect
GET    auth/zalo/callback
```

### 2. Cấu hình Zalo Developer Console (2 phút)

1. Truy cập: https://developers.zalo.me/
2. Chọn app của bạn
3. Thêm **OAuth Redirect URI**:
    ```
    https://your-domain.com/auth/zalo/callback
    ```
4. Lưu lại

### 3. Test trên Browser (1 phút)

Mở browser và truy cập:

```
https://your-domain.com/auth/zalo/redirect
```

**Flow:**

1. Redirect đến Zalo login
2. Đăng nhập Zalo
3. Redirect về callback page
4. Hiển thị deeplink: `nhmapp://auth/zalo?token=xxx`

### 4. Mobile App - React Native (10 phút)

#### Step 1: Install dependencies

```bash
npm install react-native-linking
```

#### Step 2: Configure deeplink

**iOS - Info.plist:**

```xml
<key>CFBundleURLTypes</key>
<array>
    <dict>
        <key>CFBundleURLSchemes</key>
        <array>
            <string>nhmapp</string>
        </array>
    </dict>
</array>
```

**Android - AndroidManifest.xml:**

```xml
<intent-filter>
    <action android:name="android.intent.action.VIEW" />
    <category android:name="android.intent.category.DEFAULT" />
    <category android:name="android.intent.category.BROWSABLE" />
    <data android:scheme="nhmapp" android:host="auth" />
</intent-filter>
```

#### Step 3: Add login button

```javascript
import { Linking, TouchableOpacity, Text } from "react-native";

const ZaloLoginButton = () => {
    const handleLogin = () => {
        Linking.openURL("https://your-domain.com/auth/zalo/redirect");
    };

    return (
        <TouchableOpacity onPress={handleLogin}>
            <Text>Đăng nhập với Zalo</Text>
        </TouchableOpacity>
    );
};
```

#### Step 4: Handle deeplink

```javascript
import { useEffect } from "react";
import { Linking } from "react-native";
import AsyncStorage from "@react-native-async-storage/async-storage";

const App = () => {
    useEffect(() => {
        // Handle deeplink when app is running
        const subscription = Linking.addEventListener("url", handleDeepLink);

        // Handle deeplink when app is closed
        Linking.getInitialURL().then((url) => {
            if (url) handleDeepLink({ url });
        });

        return () => subscription.remove();
    }, []);

    const handleDeepLink = async ({ url }) => {
        // Parse: nhmapp://auth/zalo?token=xxx&success=true
        if (url.includes("auth/zalo")) {
            const params = new URLSearchParams(url.split("?")[1]);
            const success = params.get("success") === "true";
            const token = params.get("token");

            if (success && token) {
                await AsyncStorage.setItem("auth_token", token);
                // Navigate to home screen
                navigation.navigate("Home");
            } else {
                alert("Đăng nhập thất bại");
            }
        }
    };

    return <YourApp />;
};
```

### 5. Test End-to-End (2 phút)

1. **Rebuild mobile app** (sau khi config deeplink)

    ```bash
    # iOS
    cd ios && pod install && cd ..
    npx react-native run-ios

    # Android
    npx react-native run-android
    ```

2. **Click "Đăng nhập với Zalo"**
3. **Đăng nhập trên Zalo**
4. **App tự động mở và đăng nhập**

### 6. Test Deeplink riêng (Optional)

**iOS:**

```
# Mở Safari và gõ:
nhmapp://auth/zalo?token=test123&success=true
```

**Android:**

```bash
adb shell am start -W -a android.intent.action.VIEW \
  -d "nhmapp://auth/zalo?token=test123&success=true"
```

## 🔧 Troubleshooting

### ❌ Deeplink không hoạt động

**iOS:**

```bash
# Rebuild app
cd ios && pod install && cd ..
npx react-native run-ios
```

**Android:**

```bash
# Rebuild app
npx react-native run-android

# Test deeplink
adb shell am start -W -a android.intent.action.VIEW \
  -d "nhmapp://auth/zalo?token=test"
```

### ❌ Zalo OAuth Error

1. Kiểm tra Redirect URI trong Zalo Developer Console
2. Kiểm tra APP_URL trong .env
3. Xem logs: `tail -f storage/logs/laravel.log`

### ❌ Token không hợp lệ

1. Kiểm tra APP_ID_ZALO và APP_SECRET_ZALO trong database config
2. Test API endpoint:
    ```bash
    curl -X POST https://your-domain.com/api/auth/zalo-authenticate \
      -H 'Content-Type: application/json' \
      -d '{"code":"test_code"}'
    ```

## 📱 Mobile App Checklist

-   [ ] Deeplink scheme configured (iOS & Android)
-   [ ] Login button implemented
-   [ ] Deeplink handler implemented
-   [ ] Token storage implemented
-   [ ] Navigation after login implemented
-   [ ] Error handling implemented
-   [ ] App rebuilt after config changes

## 🌐 Backend Checklist

-   [ ] .env configured with APP_URL
-   [ ] MOBILE_DEEPLINK_SCHEME configured
-   [ ] Zalo Developer Console configured
-   [ ] Routes working (test with browser)
-   [ ] Logs checked for errors

## 📚 Next Steps

1. ✅ Test trên staging environment
2. ✅ Test với real Zalo accounts
3. ✅ Monitor logs và errors
4. ✅ Deploy to production
5. ✅ Update mobile app on stores

## 📖 Tài liệu đầy đủ

-   **Chi tiết**: `ZALO_AUTH_INTEGRATION.md`
-   **Summary**: `ZALO_AUTH_SUMMARY.md`
-   **Testing**: `./test-zalo-oauth.sh`

## ⏱️ Tổng thời gian: ~20 phút

-   Backend: 5 phút
-   Zalo Console: 2 phút
-   Mobile App: 10 phút
-   Testing: 3 phút

---

**Hoàn thành!** 🎉

Bây giờ bạn đã có Zalo authentication hoạt động qua web OAuth flow!
