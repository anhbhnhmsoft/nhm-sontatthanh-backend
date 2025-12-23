<!DOCTYPE html>
<html lang="vi">

<head>
    @vite(['resources/css/app.css'])
</head>

<body>
    <div class="container"
        data-deeplink="{{ $deeplink }}"
        data-success="{{ $success ? 'true' : 'false' }}">
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
        // Get configuration from data attributes
        const container = document.querySelector('.container');
        const deeplink = container.dataset.deeplink;
        const success = container.dataset.success === 'true';

        if (success) {
            // Try to open the app
            window.location.href = deeplink;

            // Fallback: close window after 3 seconds if app doesn't open
            setTimeout(() => {
                window.close();
            }, 3000);
        }

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