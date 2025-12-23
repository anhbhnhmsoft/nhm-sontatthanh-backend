<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Zalo</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
        }

        .icon.success {
            background: #10b981;
            color: white;
        }

        .icon.error {
            background: #ef4444;
            color: white;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #1f2937;
        }

        p {
            color: #6b7280;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .loader {
            border: 4px solid #f3f4f6;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
    </style>
</head>

<body>
    <div class="container">
        @if($success)
        <div class="icon success">✓</div>
        <h1>Đăng nhập thành công!</h1>
        <p>{{ $message }}</p>
        <div class="loader"></div>
        <p style="font-size: 14px; margin-top: 20px;">Đang chuyển về ứng dụng...</p>
        @else
        <div class="icon error">✕</div>
        <h1>Đăng nhập thất bại</h1>
        <p>{{ $message }}</p>
        <a href="#" onclick="closeWindow()" class="btn">Đóng</a>
        @endif
    </div>

    <script>
        // Attempt to redirect via deeplink
        const deeplink = "{{ $deeplink }}";

        @if($success)
        // Try to open the app
        window.location.href = deeplink;

        // Fallback: close window after 3 seconds if app doesn't open
        setTimeout(() => {
            window.close();
        }, 3000);
        @endif

        function closeWindow() {
            // Try to close the window
            window.close();

            // If that doesn't work (some browsers prevent it), show a message
            setTimeout(() => {
                alert('Vui lòng đóng cửa sổ này và quay lại ứng dụng');
            }, 100);
        }
    </script>
</body>

</html>