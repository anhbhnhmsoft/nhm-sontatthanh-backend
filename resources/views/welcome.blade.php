<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SƠN TẤT THÀNH - Dịch Vụ Sơn Nhà Đẳng Cấp</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">

    <!-- SEO Meta Tags -->
    <meta name="description"
        content="Sơn Tất Thành - Đối tác tin cậy cho ngôi nhà hoàn hảo. Dịch vụ sơn nhà, chống thấm uy tín, chuyên nghiệp.">
    <meta name="keywords"
        content="sơn nhà, sơn nội thất, sơn ngoại thất, chống thấm, sơn tất thành, thợ sơn uy tín">
    <meta name="author" content="Sơn Tất Thành">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

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
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                        },
                        accent: '#f59e0b',
                    }
                }
            }
        }
    </script>
    @endif
    <style>
        .glass-nav {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }

        .blob {
            position: absolute;
            background: #3b82f6;
            filter: blur(80px);
            opacity: 0.4;
            animation: move 10s infinite alternate;
        }

        @keyframes move {
            from {
                transform: translate(0, 0) scale(1);
            }

            to {
                transform: translate(20px, -20px) scale(1.1);
            }
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 font-sans antialiased overflow-x-hidden">

    <!-- Navigation -->
    <nav class="fixed w-full z-50 glass-nav transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center gap-2 cursor-pointer" onclick="window.scrollTo(0,0)">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-xl shadow-lg">
                        S
                    </div>
                    <span class="font-bold text-xl tracking-wide text-slate-900">TẤT THÀNH</span>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="#about" class="text-slate-600 hover:text-blue-600 font-medium transition-colors">Về chúng tôi</a>
                    <a href="#services" class="text-slate-600 hover:text-blue-600 font-medium transition-colors">Dịch vụ</a>
                    <a href="#process" class="text-slate-600 hover:text-blue-600 font-medium transition-colors">Quy trình</a>
                    <a href="#projects" class="text-slate-600 hover:text-blue-600 font-medium transition-colors">Dự án</a>
                    @if (Route::has('login'))
                    @auth
                    <a href="{{ url('/dashboard') }}"
                        class="px-5 py-2.5 rounded-full bg-slate-100 text-slate-700 font-medium hover:bg-slate-200 transition-all">
                        Dashboard
                    </a>
                    @else
                    <div class="flex items-center gap-2 pl-4 border-l border-slate-200">
                        <a href="{{ route('login') }}" class="text-slate-600 hover:text-blue-600 font-medium px-3">Đăng nhập</a>
                        @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="px-5 py-2.5 rounded-full bg-blue-600 text-white font-medium hover:bg-blue-700 shadow-lg shadow-blue-500/30 transition-all transform hover:-translate-y-0.5">
                            Đăng ký
                        </a>
                        @endif
                    </div>
                    @endauth
                    @endif
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button class="text-slate-600 hover:text-blue-600 focus:outline-none">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-24 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="blob w-96 h-96 top-0 left-0 -z-10 rounded-full mix-blend-multiply bg-blue-400"></div>
        <div class="blob w-96 h-96 bottom-0 right-0 -z-10 rounded-full mix-blend-multiply bg-purple-400 animation-delay-2000"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="text-center lg:text-left">
                    <div class="inline-block px-4 py-1.5 mb-6 rounded-full bg-blue-50 text-blue-600 font-semibold text-sm tracking-wide uppercase border border-blue-100">
                        ✨ #1 Dịch vụ sơn nhà uy tín
                    </div>
                    <h1 class="text-5xl lg:text-7xl font-extrabold text-slate-900 leading-tight mb-6">
                        Đánh thức vẻ đẹp <br>
                        <span class="text-transparent bg-clip-text bg-linear-to-r from-blue-600 to-indigo-600">Ngôi nhà của bạn</span>
                    </h1>
                    <p class="text-lg text-slate-600 mb-8 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        Chúng tôi mang đến giải pháp sơn nhà hoàn hảo với công nghệ tiên tiến, đội ngũ chuyên gia tận tâm và cam kết chất lượng bền vững theo thời gian.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="{{ route('download') }}"
                            class="px-8 py-4 rounded-full bg-blue-600 text-white font-bold text-lg hover:bg-blue-700 shadow-xl shadow-blue-600/30 transition-all transform hover:-translate-y-1">
                            Tải App Ngay
                        </a>
                        <a href="#contact"
                            class="px-8 py-4 rounded-full bg-white text-slate-700 border border-slate-200 font-bold text-lg hover:bg-slate-50 hover:border-slate-300 shadow-sm transition-all">
                            Nhận Báo Giá
                        </a>
                    </div>

                    <div class="mt-10 flex items-center justify-center lg:justify-start gap-6 text-slate-500 text-sm font-medium">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Bảo hành 5 năm
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Thi công nhanh
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Giá tốt nhất
                        </div>
                    </div>
                </div>
                <div class="relative hidden lg:block">
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl border-4 border-white">
                        <img src="https://images.unsplash.com/photo-1589939705384-5185137a7f0f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="House Painting" class="w-full h-auto object-cover transform hover:scale-105 transition-transform duration-700">
                        <div class="absolute bottom-6 left-6 right-6 bg-white/95 backdrop-blur rounded-2xl p-6 shadow-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-slate-500 mb-1">Dự án tiêu biểu</p>
                                    <h3 class="font-bold text-slate-900">Biệt thự Vinhomes Riverside</h3>
                                </div>
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Decorative elements -->
                    <div class="absolute -top-10 -right-10 w-24 h-24 bg-yellow-400 rounded-full opacity-50 blur-xl"></div>
                    <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-blue-600 rounded-full opacity-30 blur-xl"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-16 items-center">
                <div class="order-2 md:order-1 relative">
                    <div class="grid grid-cols-2 gap-4">
                        <img src="https://images.unsplash.com/photo-1562259949-e8e7689d7828?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" class="rounded-2xl shadow-lg mt-12 mb-4 w-full h-64 object-cover" alt="Painter">
                        <img src="https://images.unsplash.com/photo-1595846519845-68e298c2edd8?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" class="rounded-2xl shadow-lg w-full h-64 object-cover" alt="Wall">
                    </div>
                    <div class="absolute -z-10 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[120%] h-[120%] bg-slate-50 rounded-full blur-3xl opacity-50"></div>
                </div>
                <div class="order-1 md:order-2">
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-6">Tại sao chọn <span class="text-blue-600">Sơn Tất Thành?</span></h2>
                    <p class="text-slate-600 text-lg mb-6 leading-relaxed">
                        Với hơn 10 năm kinh nghiệm trong lĩnh vực sơn nhà, chúng tôi tự hào là đơn vị tiên phong áp dụng công nghệ sơn mới nhất, mang lại vẻ đẹp bền lâu cho hàng nghìn ngôi nhà Việt.
                    </p>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="text-slate-700 font-medium">Đội ngũ thợ tay nghề cao, được đào tạo bài bản.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="text-slate-700 font-medium">Sử dụng sơn chính hãng 100% (Dulux, Jotun, Kova...).</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="text-slate-700 font-medium">Bảo hành dài hạn lên đến 10 năm.</span>
                        </li>
                    </ul>
                    <a href="#about" class="text-blue-600 font-bold hover:text-blue-700 inline-flex items-center gap-2">
                        Tìm hiểu thêm về chúng tôi
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-blue-600 font-semibold tracking-wider uppercase text-sm">Dịch vụ của chúng tôi</span>
                <h2 class="mt-2 text-3xl md:text-5xl font-bold text-slate-900">Giải pháp toàn diện cho ngôi nhà</h2>
                <p class="mt-4 text-slate-600 text-lg">Chúng tôi cung cấp đa dạng các gói dịch vụ phù hợp với mọi nhu cầu của bạn.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Service 1 -->
                <div class="bg-white rounded-3xl p-8 shadow-sm hover:shadow-xl transition-all duration-300 group cursor-pointer border border-slate-100">
                    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 mb-6 group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-3">Sơn Nội Thất</h3>
                    <p class="text-slate-600 leading-relaxed mb-6">Tô điểm không gian sống với màu sắc tươi mới, an toàn cho sức khỏe, dễ dàng lau chùi.</p>
                    <a href="#" class="text-blue-600 font-semibold group-hover:underline">Xem chi tiết &rarr;</a>
                </div>

                <!-- Service 2 -->
                <div class="bg-white rounded-3xl p-8 shadow-sm hover:shadow-xl transition-all duration-300 group cursor-pointer border border-slate-100">
                    <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 mb-6 group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-3">Sơn Ngoại Thất</h3>
                    <p class="text-slate-600 leading-relaxed mb-6">Bảo vệ ngôi nhà trước mọi thời tiết khắc nghiệt, chống thấm, chống rêu mốc vượt trội.</p>
                    <a href="#" class="text-blue-600 font-semibold group-hover:underline">Xem chi tiết &rarr;</a>
                </div>

                <!-- Service 3 -->
                <div class="bg-white rounded-3xl p-8 shadow-sm hover:shadow-xl transition-all duration-300 group cursor-pointer border border-slate-100">
                    <div class="w-16 h-16 bg-purple-50 rounded-2xl flex items-center justify-center text-purple-600 mb-6 group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-3">Chống Thấm</h3>
                    <p class="text-slate-600 leading-relaxed mb-6">Giải pháp chống thấm triệt để cho tường, trần, sàn, bể bơi với công nghệ Nano tiên tiến.</p>
                    <a href="#" class="text-blue-600 font-semibold group-hover:underline">Xem chi tiết &rarr;</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section id="process" class="py-24 bg-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20">
                <h2 class="text-3xl md:text-5xl font-bold text-slate-900 mb-4">Quy trình làm việc</h2>
                <p class="text-slate-600 text-lg">Đơn giản, minh bạch và chuyên nghiệp trong từng bước</p>
            </div>

            <div class="relative">
                <!-- Connecting Line (Desktop) -->
                <div class="hidden md:block absolute top-1/2 left-0 w-full h-1 bg-slate-100 -translate-y-1/2 z-0"></div>

                <div class="grid md:grid-cols-4 gap-8 relative z-10">
                    <!-- Step 1 -->
                    <div class="text-center group">
                        <div class="w-20 h-20 mx-auto bg-white border-4 border-blue-100 rounded-full flex items-center justify-center text-xl font-bold text-blue-600 mb-6 group-hover:border-blue-600 group-hover:scale-110 transition-all shadow-sm">
                            01
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Tư vấn & Khảo sát</h3>
                        <p class="text-slate-600 text-sm px-4">Tiếp nhận yêu cầu, khảo sát thực tế và tư vấn phương án tối ưu.</p>
                    </div>

                    <!-- Step 2 -->
                    <div class="text-center group">
                        <div class="w-20 h-20 mx-auto bg-white border-4 border-blue-100 rounded-full flex items-center justify-center text-xl font-bold text-blue-600 mb-6 group-hover:border-blue-600 group-hover:scale-110 transition-all shadow-sm">
                            02
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Báo giá & Ký HĐ</h3>
                        <p class="text-slate-600 text-sm px-4">Gửi báo giá chi tiết, thống nhất hạng mục và ký kết hợp đồng.</p>
                    </div>

                    <!-- Step 3 -->
                    <div class="text-center group">
                        <div class="w-20 h-20 mx-auto bg-white border-4 border-blue-100 rounded-full flex items-center justify-center text-xl font-bold text-blue-600 mb-6 group-hover:border-blue-600 group-hover:scale-110 transition-all shadow-sm">
                            03
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Thi công</h3>
                        <p class="text-slate-600 text-sm px-4">Thực hiện sơn theo đúng quy trình kỹ thuật và tiến độ cam kết.</p>
                    </div>

                    <!-- Step 4 -->
                    <div class="text-center group">
                        <div class="w-20 h-20 mx-auto bg-white border-4 border-blue-100 rounded-full flex items-center justify-center text-xl font-bold text-blue-600 mb-6 group-hover:border-blue-600 group-hover:scale-110 transition-all shadow-sm">
                            04
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Nghiệm thu</h3>
                        <p class="text-slate-600 text-sm px-4">Kiểm tra chất lượng, nghiệm thu và bàn giao công trình sạch đẹp.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl md:text-5xl font-bold text-center text-slate-900 mb-16">Khách hàng nói gì?</h2>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Review 1 -->
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
                    <div class="flex text-yellow-400 mb-4">
                        ★★★★★
                    </div>
                    <p class="text-slate-600 mb-6 italic">"Dịch vụ rất chuyên nghiệp, đội ngũ thợ làm việc cẩn thận, dọn dẹp sạch sẽ sau khi sơn. Màu sơn lên rất đẹp và chuẩn."</p>
                    <div class="flex items-center gap-4">
                        <img src="https://ui-avatars.com/api/?name=Nguyen+Van+A&background=random" alt="User" class="w-12 h-12 rounded-full">
                        <div>
                            <h4 class="font-bold text-slate-900">Anh Nguyễn Văn A</h4>
                            <p class="text-xs text-slate-500">Khách hàng tại Hà Nội</p>
                        </div>
                    </div>
                </div>

                <!-- Review 2 -->
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
                    <div class="flex text-yellow-400 mb-4">
                        ★★★★★
                    </div>
                    <p class="text-slate-600 mb-6 italic">"Tôi rất hài lòng với sơn chống thấm của Tất Thành. Nhà tôi hết hẳn tình trạng ẩm mốc. Giá cả cũng rất hợp lý."</p>
                    <div class="flex items-center gap-4">
                        <img src="https://ui-avatars.com/api/?name=Tran+Thi+B&background=random" alt="User" class="w-12 h-12 rounded-full">
                        <div>
                            <h4 class="font-bold text-slate-900">Chị Trần Thị B</h4>
                            <p class="text-xs text-slate-500">Khách hàng tại Hưng Yên</p>
                        </div>
                    </div>
                </div>

                <!-- Review 3 -->
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
                    <div class="flex text-yellow-400 mb-4">
                        ★★★★★
                    </div>
                    <p class="text-slate-600 mb-6 italic">"Tư vấn nhiệt tình, phối màu rất có gu. Nhà mới của mình nhìn sang trọng hơn hẳn nhờ màu sơn này."</p>
                    <div class="flex items-center gap-4">
                        <img src="https://ui-avatars.com/api/?name=Le+Van+C&background=random" alt="User" class="w-12 h-12 rounded-full">
                        <div>
                            <h4 class="font-bold text-slate-900">Anh Lê Văn C</h4>
                            <p class="text-xs text-slate-500">Khách hàng tại Nam Định</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section id="contact" class="py-24">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-linear-to-r from-blue-600 to-indigo-700 rounded-[2.5rem] p-8 md:p-16 text-center text-white relative overflow-hidden shadow-2xl">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>

                <h2 class="text-3xl md:text-5xl font-bold mb-6 relative z-10">Đừng để ngôi nhà xuống cấp!</h2>
                <p class="text-blue-100 text-lg md:text-xl mb-10 max-w-2xl mx-auto relative z-10">
                    Liên hệ ngay với Sơn Tất Thành để được tư vấn miễn phí và nhận ưu đãi giảm giá 10% cho dịch vụ trọn gói.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center relative z-10">
                    <button class="bg-white text-blue-700 font-bold py-4 px-10 rounded-full hover:bg-blue-50 transition-all shadow-lg transform hover:-translate-y-1">
                        Hotline: 0912.345.678
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
                <div>
                    <h3 class="text-white text-2xl font-bold mb-6">Sơn Tất Thành</h3>
                    <p class="text-sm mb-6 leading-relaxed opacity-80">
                        Chuyên gia hàng đầu về giải pháp sơn nhà và chống thấm. Mang lại vẻ đẹp hoàn mỹ cho mọi công trình.
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-blue-600 transition-colors">
                            <span class="sr-only">Facebook</span>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-blue-600 transition-colors">
                            <span class="sr-only">Zalo</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="text-white font-bold mb-6">Dịch vụ</h3>
                    <ul class="space-y-3 text-sm opacity-80">
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Sơn nội thất cao cấp</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Sơn ngoại thất bền màu</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Chống thấm chuyên sâu</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Thi công trọn gói</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-white font-bold mb-6">Về chúng tôi</h3>
                    <ul class="space-y-3 text-sm opacity-80">
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Giới thiệu</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Dự án đã thực hiện</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Tin tức & Sự kiện</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Tuyển dụng</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-white font-bold mb-6">Liên hệ</h3>
                    <ul class="space-y-4 text-sm opacity-80">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Số 123, Đường ABC, Quận XYZ, TP. Hà Nội</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span>0912.345.678</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>contact@sontatthanh.com</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm opacity-60">&copy; {{ date('Y') }} Sơn Tất Thành. All rights reserved.</p>
                <div class="flex gap-6 text-sm opacity-60">
                    <a href="#" class="hover:text-white transition-colors">Điều khoản</a>
                    <a href="#" class="hover:text-white transition-colors">Chính sách bảo mật</a>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>