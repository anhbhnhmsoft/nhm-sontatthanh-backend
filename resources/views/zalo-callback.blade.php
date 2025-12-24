<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $success ? 'Đăng nhập thành công' : 'Đăng nhập thất bại' }} - Zalo Authentication</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-up {
            animation: slideUp 0.6s ease-out forwards;
        }

        @keyframes checkmark {
            0% {
                stroke-dashoffset: 100;
            }

            100% {
                stroke-dashoffset: 0;
            }
        }

        .checkmark {
            stroke-dasharray: 100;
            stroke-dashoffset: 100;
            animation: checkmark 0.8s ease-out 0.3s forwards;
        }

        @keyframes errorShake {

            0%,
            100% {
                transform: translateX(0);
            }

            10%,
            30%,
            50%,
            70%,
            90% {
                transform: translateX(-10px);
            }

            20%,
            40%,
            60%,
            80% {
                transform: translateX(10px);
            }
        }

        .error-shake {
            animation: errorShake 0.6s ease-out;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-50 via-white to-indigo-50 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full">
        <!-- Card Container -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden slide-up">

            <!-- Header with gradient -->
            <div class="bg-gradient-to-r from-zalo-blue to-blue-600 p-8 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-full mb-4 shadow-lg">
                    @if($success)
                    <!-- Success Icon -->
                    <svg class="w-12 h-12" viewBox="0 0 52 52">
                        <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none" stroke="#10B981" stroke-width="2" />
                        <path class="checkmark" fill="none" stroke="#10B981" stroke-width="3" stroke-linecap="round" d="M14 27l7 7 16-16" />
                    </svg>
                    @else
                    <!-- Error Icon -->
                    <svg class="w-12 h-12 text-red-500 error-shake" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    @endif
                </div>

                <h1 class="text-2xl font-bold text-white mb-2">
                    {{ $success ? 'Đăng nhập thành công!' : 'Đăng nhập thất bại' }}
                </h1>

                <p class="text-blue-100 text-sm">
                    {{ $success ? 'Chào mừng bạn quay trở lại' : 'Đã có lỗi xảy ra' }}
                </p>
            </div>

            <!-- Content -->
            <div class="p-8">
                <!-- Message -->
                <div class="mb-6">
                    <div class="flex items-start space-x-3 p-4 rounded-xl {{ $success ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
                        <div class="flex-shrink-0 mt-0.5">
                            @if($success)
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            @else
                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium {{ $success ? 'text-green-800' : 'text-red-800' }}">
                                {{ $message }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Redirect Status -->
                <div class="text-center mb-6">
                    <div class="inline-flex items-center space-x-2 text-gray-600">
                        <svg class="w-5 h-5 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span class="text-sm font-medium">Đang chuyển hướng về ứng dụng...</span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mt-4 w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                        <div id="progressBar" class="bg-gradient-to-r from-zalo-blue to-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>

                    <p class="text-xs text-gray-500 mt-2">
                        Tự động chuyển hướng sau <span id="countdown" class="font-semibold text-zalo-blue">3</span> giây
                    </p>
                </div>

                <!-- Manual Actions -->
                <div class="space-y-3">
                    <button
                        onclick="redirectToApp()"
                        class="w-full bg-gradient-to-r from-zalo-blue to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <span class="flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <span>Mở ứng dụng ngay</span>
                        </span>
                    </button>

                    @if(!$success)
                    <a
                        href="{{ route('zalo.redirect') }}"
                        class="block w-full bg-white hover:bg-gray-50 text-gray-700 font-semibold py-3 px-6 rounded-xl border-2 border-gray-300 transition-all duration-300 text-center focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                        <span class="flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span>Thử lại</span>
                        </span>
                    </a>
                    @endif
                </div>

                <!-- Footer Info -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-xs text-gray-500 text-center">
                        Nếu ứng dụng không tự động mở, vui lòng nhấn nút "Mở ứng dụng ngay" ở trên
                    </p>
                </div>
            </div>
        </div>

        <!-- Powered by -->
        <div class="text-center mt-6">
            <p class="text-sm text-gray-500">
                Powered by
                <span class="font-semibold text-zalo-blue">Zalo Authentication</span>
            </p>
        </div>
    </div>

    <script>
        // Deeplink URL from server
        const deeplinkUrl = @json($deeplink);
        const isSuccess = @json($success);

        // Countdown timer
        let countdown = 3;
        const countdownElement = document.getElementById('countdown');
        const progressBar = document.getElementById('progressBar');

        // Update progress bar
        function updateProgress() {
            const progress = ((3 - countdown) / 3) * 100;
            progressBar.style.width = progress + '%';
        }

        // Countdown function
        const countdownInterval = setInterval(() => {
            countdown--;
            if (countdownElement) {
                countdownElement.textContent = countdown;
            }
            updateProgress();

            if (countdown <= 0) {
                clearInterval(countdownInterval);
                redirectToApp();
            }
        }, 1000);

        // Redirect to mobile app
        function redirectToApp() {
            console.log('Redirecting to:', deeplinkUrl);

            // Try to open the deep link
            window.location.href = deeplinkUrl;

            // Fallback: If deep link doesn't work after 2 seconds, show instruction
            setTimeout(() => {
                if (document.hasFocus()) {
                    // Still on this page, deep link might not have worked
                    console.log('Deep link may not have worked');

                    // For iOS, try using a different approach
                    if (/iPhone|iPad|iPod/i.test(navigator.userAgent)) {
                        const iframe = document.createElement('iframe');
                        iframe.style.display = 'none';
                        iframe.src = deeplinkUrl;
                        document.body.appendChild(iframe);

                        setTimeout(() => {
                            document.body.removeChild(iframe);
                        }, 1000);
                    }
                }
            }, 2000);
        }

        // Try to redirect immediately on page load for better UX
        window.addEventListener('load', () => {
            // Start progress animation
            updateProgress();
        });

        // Handle page visibility change (when user comes back to this tab)
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden && countdown <= 0) {
                // User came back, app might not have opened
                console.log('User returned to page');
            }
        });
    </script>
</body>

</html>
