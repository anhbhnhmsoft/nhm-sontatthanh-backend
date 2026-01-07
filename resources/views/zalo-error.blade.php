<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Lỗi xác thực - Zalo Authentication</title>

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

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-red-50 via-white to-orange-50 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full">
        <!-- Card Container -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden slide-up">

            <!-- Header with gradient -->
            <div class="bg-gradient-to-r from-red-500 to-orange-600 p-8 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-full mb-4 shadow-lg">
                    <!-- Error Icon -->
                    <svg class="w-12 h-12 text-red-500 error-shake" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                <h1 class="text-2xl font-bold text-white mb-2">
                    Xác thực thất bại
                </h1>

                <p class="text-red-100 text-sm">
                    Đã có lỗi xảy ra trong quá trình xác thực
                </p>
            </div>

            <!-- Content -->
            <div class="p-8">
                <!-- Error Message -->
                <div class="mb-6">
                    <div class="flex items-start space-x-3 p-4 rounded-xl bg-red-50 border border-red-200">
                        <div class="flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-red-800">
                                {{ $message ?? 'Có lỗi xảy ra. Vui lòng thử lại sau.' }}
                            </p>
                            @if(isset($error_code))
                            <p class="text-xs text-red-600 mt-1">
                                Mã lỗi: {{ $error_code }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Error Details (if provided) -->
                @if(isset($details))
                <div class="mb-6">
                    <details class="group">
                        <summary class="cursor-pointer text-sm text-gray-600 hover:text-gray-800 flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="font-medium">Chi tiết lỗi</span>
                            <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <div class="mt-2 p-3 bg-gray-50 rounded-lg text-xs text-gray-700 font-mono">
                            {{ $details }}
                        </div>
                    </details>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <a
                        href="{{ route('zalo.redirect') }}{{ isset($retry_token) ? '?token=' . $retry_token : '' }}"
                        class="block w-full bg-gradient-to-r from-red-500 to-orange-600 hover:from-red-600 hover:to-orange-700 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        <span class="flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span>Thử lại</span>
                        </span>
                    </a>

                    @if(isset($show_close_button) && $show_close_button)
                    <button
                        onclick="window.close()"
                        class="block w-full bg-white hover:bg-gray-50 text-gray-700 font-semibold py-3 px-6 rounded-xl border-2 border-gray-300 transition-all duration-300 text-center focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                        <span class="flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Đóng cửa sổ</span>
                        </span>
                    </button>
                    @endif
                </div>

                <!-- Help Text -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-xs text-gray-500 text-center">
                        Nếu vấn đề vẫn tiếp diễn, vui lòng liên hệ bộ phận hỗ trợ
                    </p>
                </div>
            </div>
        </div>

        <!-- Powered by -->
        <div class="text-center mt-6">
            <p class="text-sm text-gray-500">
                Powered by
                <span class="font-semibold text-red-600">Zalo Authentication</span>
            </p>
        </div>
    </div>

</body>

</html>
