# Zalo Authentication Flow - Hướng dẫn tích hợp

## Tổng quan

Hệ thống đã được cập nhật để hỗ trợ xác thực Zalo qua **Web OAuth Flow** thay vì xử lý trực tiếp trên mobile app. Flow mới này an toàn hơn và tuân thủ best practices của OAuth 2.0.

## Flow hoạt động

```
┌─────────────┐
│ Mobile App  │
└──────┬──────┘
       │ 1. User clicks "Đăng nhập với Zalo"
       │
       ▼
┌─────────────────────────────────────────┐
│ Open browser:                           │
│ https://your-domain.com/auth/zalo/redirect │
└──────┬──────────────────────────────────┘
       │ 2. Redirect to Zalo OAuth
       │
       ▼
┌─────────────────────┐
│ Zalo Login Page     │
│ (User authenticates)│
└──────┬──────────────┘
       │ 3. User approves
       │
       ▼
┌─────────────────────────────────────────┐
│ Callback:                               │
│ https://your-domain.com/auth/zalo/callback?code=xxx │
└──────┬──────────────────────────────────┘
       │ 4. Backend exchanges code for token
       │ 5. Get user info from Zalo
       │ 6. Create/Login user
       │
       ▼
┌─────────────────────────────────────────┐
│ Redirect to mobile via deeplink:       │
│ nhmapp://auth/zalo?token=xxx&success=true │
└──────┬──────────────────────────────────┘
       │ 7. Mobile app receives deeplink
       │
       ▼
┌─────────────┐
│ Mobile App  │
│ (Logged in) │
└─────────────┘
```

## Cấu hình

### 1. Backend Configuration

Thêm vào file `.env`:

```env
# Zalo OAuth Configuration
APP_URL=https://your-domain.com

# Mobile App Deeplink Scheme
MOBILE_DEEPLINK_SCHEME=nhmapp

# Zalo App Credentials (đã có trong database config)
# Đảm bảo đã cấu hình APP_ID_ZALO và APP_SECRET_ZALO trong admin panel
```

### 2. Zalo Developer Console

1. Truy cập [Zalo Developer Console](https://developers.zalo.me/)
2. Tạo hoặc chọn ứng dụng của bạn
3. Thêm **OAuth Redirect URI**:
    ```
    https://your-domain.com/auth/zalo/callback
    ```
4. Lưu App ID và App Secret vào database config

### 3. Mobile App Configuration

#### iOS (React Native / Flutter)

**Info.plist:**

```xml
<key>CFBundleURLTypes</key>
<array>
    <dict>
        <key>CFBundleURLSchemes</key>
        <array>
            <string>nhmapp</string>
        </array>
        <key>CFBundleURLName</key>
        <string>com.yourcompany.nhmapp</string>
    </dict>
</array>

<key>LSApplicationQueriesSchemes</key>
<array>
    <string>nhmapp</string>
</array>
```

#### Android (React Native / Flutter)

**AndroidManifest.xml:**

```xml
<activity android:name=".MainActivity">
    <intent-filter>
        <action android:name="android.intent.action.VIEW" />
        <category android:name="android.intent.category.DEFAULT" />
        <category android:name="android.intent.category.BROWSABLE" />
        <data
            android:scheme="nhmapp"
            android:host="auth" />
    </intent-filter>
</activity>
```

## Tích hợp Mobile App

### React Native Example

```javascript
import { Linking } from 'react-native';

// 1. Mở browser để đăng nhập Zalo
const loginWithZalo = async () => {
  const authUrl = 'https://your-domain.com/auth/zalo/redirect';

  // Mở browser
  await Linking.openURL(authUrl);
};

// 2. Lắng nghe deeplink callback
useEffect(() => {
  const handleDeepLink = (event) => {
    const url = event.url;

    // Parse URL: nhmapp://auth/zalo?token=xxx&success=true&message=...
    if (url.startsWith('nhmapp://auth/zalo')) {
      const params = new URLSearchParams(url.split('?')[1]);

      const success = params.get('success') === 'true';
      const token = params.get('token');
      const message = params.get('message');

      if (success && token) {
        // Lưu token và chuyển đến màn hình chính
        await AsyncStorage.setItem('auth_token', token);
        navigation.navigate('Home');
      } else {
        // Hiển thị lỗi
        Alert.alert('Đăng nhập thất bại', message);
      }
    }
  };

  // Lắng nghe deeplink
  const subscription = Linking.addEventListener('url', handleDeepLink);

  // Xử lý trường hợp app đã đóng
  Linking.getInitialURL().then((url) => {
    if (url) {
      handleDeepLink({ url });
    }
  });

  return () => subscription.remove();
}, []);

// Component UI
<TouchableOpacity onPress={loginWithZalo}>
  <Text>Đăng nhập với Zalo</Text>
</TouchableOpacity>
```

### Flutter Example

```dart
import 'package:uni_links/uni_links.dart';
import 'package:url_launcher/url_launcher.dart';

// 1. Mở browser để đăng nhập Zalo
Future<void> loginWithZalo() async {
  final authUrl = Uri.parse('https://your-domain.com/auth/zalo/redirect');

  if (await canLaunchUrl(authUrl)) {
    await launchUrl(authUrl, mode: LaunchMode.externalApplication);
  }
}

// 2. Lắng nghe deeplink callback
StreamSubscription? _sub;

@override
void initState() {
  super.initState();
  _initDeepLinkListener();
}

Future<void> _initDeepLinkListener() async {
  // Lắng nghe deeplink khi app đang chạy
  _sub = uriLinkStream.listen((Uri? uri) {
    if (uri != null) {
      _handleDeepLink(uri);
    }
  });

  // Xử lý trường hợp app đã đóng
  final initialUri = await getInitialUri();
  if (initialUri != null) {
    _handleDeepLink(initialUri);
  }
}

void _handleDeepLink(Uri uri) {
  // Parse: nhmapp://auth/zalo?token=xxx&success=true
  if (uri.scheme == 'nhmapp' && uri.host == 'auth' && uri.path == '/zalo') {
    final success = uri.queryParameters['success'] == 'true';
    final token = uri.queryParameters['token'];
    final message = uri.queryParameters['message'];

    if (success && token != null) {
      // Lưu token và chuyển màn hình
      _saveToken(token);
      Navigator.pushReplacementNamed(context, '/home');
    } else {
      // Hiển thị lỗi
      _showError(message ?? 'Đăng nhập thất bại');
    }
  }
}

@override
void dispose() {
  _sub?.cancel();
  super.dispose();
}
```

## API Endpoints

### Web Routes (Mới)

#### 1. Redirect to Zalo OAuth

```
GET /auth/zalo/redirect
```

Chuyển hướng user đến trang đăng nhập Zalo.

#### 2. OAuth Callback

```
GET /auth/zalo/callback?code={authorization_code}
```

Xử lý callback từ Zalo, exchange code thành token, và redirect về mobile app.

**Deeplink Response:**

```
nhmapp://auth/zalo?token={jwt_token}&success=true&message=Đăng%20nhập%20thành%20công&user_id={id}&user_name={name}
```

### API Routes (Backward Compatible)

#### Zalo Authentication

```
POST /api/auth/zalo-authenticate
```

**Request Body (Option 1 - Recommended):**

```json
{
    "code": "zalo_authorization_code"
}
```

**Request Body (Option 2 - Deprecated):**

```json
{
    "access_token": "zalo_access_token"
}
```

**Response:**

```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "Nguyễn Văn A",
            "zalo_id": "1234567890",
            "avatar": "https://...",
            "role": "CTV"
        },
        "token": "jwt_token_here"
    },
    "message": "Xác thực Zalo thành công"
}
```

## Deeplink Parameters

Khi redirect về mobile app, các parameters sau được truyền:

| Parameter   | Type    | Description                         |
| ----------- | ------- | ----------------------------------- |
| `success`   | boolean | `true` nếu đăng nhập thành công     |
| `token`     | string  | JWT token (chỉ có khi success=true) |
| `message`   | string  | Thông báo kết quả                   |
| `user_id`   | integer | ID của user (optional)              |
| `user_name` | string  | Tên của user (optional)             |

## Testing

### Test trên Local

1. **Sử dụng ngrok để expose local server:**

    ```bash
    ngrok http 8000
    ```

2. **Cập nhật .env:**

    ```env
    APP_URL=https://your-ngrok-url.ngrok.io
    ```

3. **Cập nhật Zalo OAuth Redirect URI:**

    ```
    https://your-ngrok-url.ngrok.io/auth/zalo/callback
    ```

4. **Test flow:**
    - Mở browser: `https://your-ngrok-url.ngrok.io/auth/zalo/redirect`
    - Đăng nhập Zalo
    - Kiểm tra deeplink redirect

## Troubleshooting

### 1. Deeplink không hoạt động

**iOS:**

-   Kiểm tra `Info.plist` đã cấu hình đúng scheme
-   Rebuild app sau khi thay đổi config
-   Test bằng Safari: `nhmapp://auth/zalo?token=test`

**Android:**

-   Kiểm tra `AndroidManifest.xml`
-   Rebuild app
-   Test bằng ADB:
    ```bash
    adb shell am start -W -a android.intent.action.VIEW -d "nhmapp://auth/zalo?token=test"
    ```

### 2. Zalo OAuth Error

-   Kiểm tra App ID và App Secret trong database config
-   Đảm bảo Redirect URI khớp với Zalo Developer Console
-   Kiểm tra logs: `storage/logs/laravel.log`

### 3. Token Exchange Failed

-   Kiểm tra network connectivity
-   Verify Zalo API endpoints
-   Check logs cho chi tiết lỗi

## Security Notes

1. **HTTPS Required:** Production phải sử dụng HTTPS
2. **Validate Deeplink:** Mobile app nên validate token trước khi lưu
3. **Token Expiry:** JWT token có thời hạn 30 ngày
4. **State Parameter:** Có thể thêm state parameter để prevent CSRF (tùy chọn)

## Migration từ Old Flow

Nếu bạn đang sử dụng flow cũ (mobile tự xử lý Zalo SDK):

1. **Không cần thay đổi ngay:** API vẫn hỗ trợ `access_token` parameter
2. **Migrate dần:** Implement deeplink handler trước
3. **Test thoroughly:** Test cả 2 flows song song
4. **Deprecate old flow:** Sau khi stable, remove Zalo SDK khỏi mobile app

## Files Changed

-   `app/Http/Controllers/Web/ZaloAuthController.php` - Web OAuth controller (NEW)
-   `app/Service/ZaloService.php` - Added OAuth methods
-   `app/Service/AuthService.php` - Added code exchange method
-   `app/Http/Controllers/Api/AuthController.php` - Updated to support both flows
-   `routes/web.php` - Added OAuth routes
-   `resources/views/zalo-callback.blade.php` - Callback page (NEW)
-   `config/app.php` - Added deeplink scheme config

## Support

Nếu có vấn đề, kiểm tra:

1. Laravel logs: `storage/logs/laravel.log`
2. Zalo Developer Console
3. Mobile app deeplink configuration
