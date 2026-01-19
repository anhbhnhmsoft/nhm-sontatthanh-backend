<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SƠN TẤT THÀNH - Dịch Vụ Sơn Nhà Trọn Gói Chuyên Nghiệp</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">

    <!-- SEO Meta Tags -->
    <meta name="description" content="Sơn Tất Thành chuyên cung cấp dịch vụ sơn nhà trọn gói, sơn nội thất, ngoại thất, chống thấm chuyên nghiệp. Đội ngũ tay nghề cao, uy tín, chất lượng, giá cả cạnh tranh.">
    <meta name="keywords" content="sơn nhà, dịch vụ sơn nhà, sơn tất thành, sơn nội thất, sơn ngoại thất, sơn chống thấm, thợ sơn nhà uy tín">
    <meta name="author" content="Sơn Tất Thành">
    <meta name="robots" content="index, follow">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="SƠN TẤT THÀNH - Dịch Vụ Sơn Nhà Trọn Gói Chuyên Nghiệp">
    <meta property="og:description" content="Mang đến vẻ đẹp hoàn hảo cho ngôi nhà của bạn với dịch vụ sơn chuyên nghiệp từ Sơn Tất Thành.">
    <meta property="og:image" content="{{ asset('images/favicon.png') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url('/') }}">
    <meta property="twitter:title" content="SƠN TẤT THÀNH - Dịch Vụ Sơn Nhà Trọn Gói Chuyên Nghiệp">
    <meta property="twitter:description" content="Mang đến vẻ đẹp hoàn hảo cho ngôi nhà của bạn với dịch vụ sơn chuyên nghiệp từ Sơn Tất Thành.">
    <meta property="twitter:image" content="{{ asset('images/favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <!-- Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Instrument Sans', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                        }
                    }
                }
            }
        }
    </script>
    @endif
</head>

<body class="bg-gray-50 text-gray-900 font-sans antialiased">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <span class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                            Sơn Tất Thành
                        </span>
                    </div>
                    <div class="hidden md:flex space-x-8">
                        <a href="#" class="text-gray-600 hover:text-blue-600 transition-colors font-medium">Trang chủ</a>
                        <a href="#services" class="text-gray-600 hover:text-blue-600 transition-colors font-medium">Dịch vụ</a>
                        <a href="#about" class="text-gray-600 hover:text-blue-600 transition-colors font-medium">Về chúng tôi</a>
                        <a href="#contact" class="text-gray-600 hover:text-blue-600 transition-colors font-medium">Liên hệ</a>
                    </div>
                    <div class="flex items-center space-x-4">
                        @if (Route::has('login'))
                        @auth
                        <a href="{{ url('/dashboard') }}" class="text-gray-600 hover:text-blue-600 font-medium">Dashboard</a>
                        @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 font-medium">Đăng nhập</a>
                        @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-4 py-2 rounded-full bg-blue-600 text-white hover:bg-blue-700 transition-colors font-medium shadow-md hover:shadow-lg">
                            Đăng ký
                        </a>
                        @endif
                        @endauth
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <main class="flex-grow">
            <!-- Hero -->
            <div class="relative overflow-hidden bg-white">
                <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=1973&q=80')] bg-cover bg-center opacity-5"></div>
                <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
                    <div class="text-center max-w-3xl mx-auto">
                        <h1 class="text-4xl md:text-6xl font-bold tracking-tight text-gray-900 mb-6">
                            Giải pháp sơn nhà <br>
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">toàn diện & chuyên nghiệp</span>
                        </h1>
                        <p class="mt-4 text-xl text-gray-600 mb-10 leading-relaxed">
                            Mang đến vẻ đẹp hoàn hảo cho ngôi nhà của bạn với dịch vụ sơn chuyên nghiệp, chất lượng cao và đội ngũ tận tâm từ Sơn Tất Thành.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="{{ route('download') }}" class="px-8 py-3 rounded-full bg-blue-600 text-white text-lg font-semibold hover:bg-blue-700 transition-colors shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                Download app
                            </a>
                            <a href="#services" class="px-8 py-3 rounded-full bg-white text-gray-700 border border-gray-200 text-lg font-semibold hover:bg-gray-50 transition-colors shadow-sm hover:shadow-md">
                                Xem dịch vụ
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features / Stats -->
            <div class="bg-gray-50 py-12 md:py-16">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="bg-white p-6 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4 text-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold mb-2 text-gray-900">Chất lượng đảm bảo</h3>
                            <p class="text-gray-600">Sử dụng các loại sơn chính hãng, bền màu và an toàn cho sức khỏe gia đình bạn.</p>
                        </div>
                        <div class="bg-white p-6 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mb-4 text-indigo-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold mb-2 text-gray-900">Thi công nhanh chóng</h3>
                            <p class="text-gray-600">Quy trình làm việc chuyên nghiệp, cam kết đúng tiến độ và dọn dẹp sạch sẽ.</p>
                        </div>
                        <div class="bg-white p-6 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-4 text-purple-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold mb-2 text-gray-900">Chi phí hợp lý</h3>
                            <p class="text-gray-600">Báo giá minh bạch, cạnh tranh và nhiều ưu đãi hấp dẫn cho khách hàng.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div id="contact" class="bg-white py-16 md:py-24">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-3xl p-8 md:p-16 text-center shadow-xl relative overflow-hidden">
                        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
                        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6 relative z-10">Sẵn sàng để làm mới không gian của bạn?</h2>
                        <p class="text-blue-100 text-lg mb-8 max-w-2xl mx-auto relative z-10">Liên hệ ngay hôm nay để nhận được tư vấn chuyên sâu và báo giá chi tiết cho công trình của bạn.</p>
                        <div class="flex justify-center gap-4 relative z-10">
                            <button class="bg-white text-blue-600 font-semibold py-3 px-8 rounded-full hover:bg-gray-50 transition-colors shadow-md">
                                Gọi ngay: 0912.345.678
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </main>

        <!-- Footer -->
        <footer class="bg-gray-900 text-gray-400 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                    <div class="col-span-1 md:col-span-1">
                        <span class="text-2xl font-bold text-white">Sơn Tất Thành</span>
                        <p class="mt-4 text-sm">Chuyên cung cấp dịch vụ sơn nhà trọn gói, uy tín và chất lượng hàng đầu.</p>
                    </div>
                    <div>
                        <h3 class="text-white font-semibold mb-4">Dịch vụ</h3>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="hover:text-white transition-colors">Sơn nội thất</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Sơn ngoại thất</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Sơn chống thấm</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-white font-semibold mb-4">Công ty</h3>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="hover:text-white transition-colors">Về chúng tôi</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Liên hệ</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Chính sách bảo hành</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-white font-semibold mb-4">Kết nối</h3>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="hover:text-white transition-colors">Facebook</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Zalo</a></li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-800 pt-8 text-sm text-center">
                    &copy; {{ date('Y') }} Sơn Tất Thành. All rights reserved.
                </div>
            </div>
        </footer>
    </div>
</body>

</html>