<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Sơn Tất Thành - Giải pháp quản lý sản phẩm chuyên nghiệp</title>
    <meta name="description" content="Ứng dụng di động dành cho đại lý và khách hàng của Sơn Tất Thành. Quản lý đơn hàng, xem báo giá và cập nhật khuyến mãi nhanh chóng.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* --- ROOT VARIABLES --- */
        :root {
            --primary-blue: #0a2540;
            /* Xanh đậm doanh nghiệp */
            --accent-orange: #f37021;
            /* Cam điểm nhấn */
            --light-gray: #f4f7fa;
            /* Xám nhạt nền */
            --dark-gray: #4a4a4a;
            /* Xám chữ */
            --white: #ffffff;
            --max-width: 1200px;
            --transition: all 0.3s ease;
        }

        /* --- GLOBAL STYLES --- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--dark-gray);
            background-color: var(--white);
            overflow-x: hidden;
        }

        h1,
        h2,
        h3 {
            color: var(--primary-blue);
            line-height: 1.2;
        }

        a {
            text-decoration: none;
            transition: var(--transition);
        }

        ul {
            list-style: none;
        }

        img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        .container {
            max-width: var(--max-width);
            margin: 0 auto;
            padding: 0 20px;
        }

        .btn-group {
            display: flex;
            gap: 15px;
        }

        .btn-group .btn img {
            max-width: 150px;

        }

        .btn {
            padding: 10px;
            border-radius: 8px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 180px;
        }

        .btn-download {
            background-color: var(--primary-blue);
            color: var(--white);
            border: 2px solid var(--primary-blue);
        }

        .btn-download:hover {
            background-color: transparent;
            color: var(--primary-blue);
        }

        .btn-orange {
            background-color: var(--accent-orange);
            color: var(--white);
            border: 2px solid var(--accent-orange);
        }

        .btn-orange:hover {
            background-color: transparent;
            color: var(--accent-orange);
        }

        section {
            padding: 80px 0;
        }

        /* --- SECTION 1: HERO --- */
        .hero {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 120px 0 80px;
            overflow: hidden;
        }

        .hero-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            gap: 40px;
        }

        .hero-content h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 20px;
        }

        .hero-content p {
            font-size: 1.1rem;
            margin-bottom: 35px;
            color: #555;
        }

        .hero-image {
            position: relative;
            display: flex;
            justify-content: center;
        }

        .mockup-container {
            width: 300px;
            height: 600px;
            background: #333;
            border: 12px solid #1a1a1a;
            border-radius: 36px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .mockup-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* --- SECTION 2: FEATURES --- */
        .features {
            background-color: var(--white);
            text-align: center;
        }

        .section-title {
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-size: 2.2rem;
            margin-bottom: 15px;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .feature-card {
            padding: 40px 30px;
            border-radius: 15px;
            background: var(--white);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border-bottom: 4px solid transparent;
            transition: var(--transition);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            border-bottom: 4px solid var(--accent-orange);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: rgba(243, 112, 33, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 24px;
            color: var(--accent-orange);
        }

        .feature-card h3 {
            margin-bottom: 15px;
            font-size: 1.25rem;
        }

        /* --- SECTION 3: BENEFITS --- */
        .benefits {
            background-color: var(--light-gray);
        }

        .benefits-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            gap: 60px;
        }

        .benefit-item {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }

        .benefit-check {
            color: #28a745;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .benefit-image {
            text-align: right;
        }

        .benefit-image img {
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            margin-left: auto;
            max-width: 300px;
        }

        /* --- SECTION 4: CTA --- */
        .cta {
            background-color: var(--primary-blue);
            color: var(--white);
            text-align: center;
            padding: 100px 0;
        }

        .cta h2 {
            color: var(--white);
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .cta p {
            margin-bottom: 40px;
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .btn-white {
            background-color: #fff;
        }

        .cta .btn-group {
            justify-content: center;
        }

        /* --- FOOTER (Small) --- */
        footer {
            padding: 30px 0;
            text-align: center;
            font-size: 0.9rem;
            border-top: 1px solid #eee;
        }

        /* --- RESPONSIVE --- */
        @media (max-width: 992px) {

            .hero-wrapper,
            .benefits-wrapper {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .hero-content h1 {
                font-size: 20px;
            }

            .btn-group {
                justify-content: center;
            }

            .hero-image {
                order: -1;
            }

            .benefits-image {
                display: none;
            }

            .btn {
                min-width: 150px;
            }

            .btn-group .btn img {
                max-width: 140px;
            }

            .hero {
                padding: 30px 0px;
            }

            .benefit-image img {
                max-width: 100%;
            }
        }

        @media (max-width: 480px) {
            .btn {
                width: 100%;
            }

            section {
                padding: 60px 0;
            }
        }
    </style>
</head>

<body>

    <section class="hero">
        <div class="container">
            <div class="hero-wrapper">
                <div class="hero-content">
                    <h1>Ứng dụng quản lý & giới thiệu sản phẩm Sơn Tất Thành</h1>
                    <p>Giải pháp công nghệ đột phá dành riêng cho đối tác và khách hàng của Sơn Tất Thành. Tra cứu sản phẩm, đặt hàng và quản lý doanh số chỉ với một chạm.</p>
                    <div class="btn-group">
                        <a href="https://apps.apple.com/us/app/s%C6%A1n-t%E1%BA%A5t-th%C3%A0nh/id6756988687" class="btn btn-white" title="Tải cho IOS (Iphone)"><img src="images/appstore.png"></a>
                        <a href="download/application-81e2c5b6-74b3-45c5-bc2c-b24fb61e0cc3.apk" class="btn btn-white" title="Tải cho CHPlay"><img src="images/chplay.png"></a>
                    </div>
                </div>
                <div class="hero-image">
                    <div class="mockup-container">
                        <img src="images/son2.jpg" alt="Sơn Tất Thành App Mockup">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="features">
        <div class="container">
            <div class="section-title">
                <h2>Tính năng nổi bật</h2>
                <p>Mọi công cụ bạn cần để tối ưu hóa kinh doanh</p>
            </div>
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon">📦</div>
                    <h3>Danh mục sản phẩm</h3>
                    <p>Xem chi tiết thông số kỹ thuật, màu sắc và hướng dẫn sử dụng của hàng trăm loại sơn.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3>Quản lý đơn hàng</h3>
                    <p>Theo dõi trạng thái đơn hàng từ lúc đặt đến khi giao hàng thành công theo thời gian thực.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💰</div>
                    <h3>Cập nhật báo giá</h3>
                    <p>Nhận bảng giá mới nhất và các chính sách chiết khấu dành riêng cho đại lý nhanh chóng.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔔</div>
                    <h3>Thông báo khuyến mãi</h3>
                    <p>Không bỏ lỡ bất kỳ chương trình ưu đãi hay quà tặng hấp dẫn nào từ Sơn Tất Thành.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="benefits">
        <div class="container">
            <div class="benefits-wrapper">
                <div class="benefits-content">
                    <h2>Lợi ích dành cho khách hàng & Đại lý</h2>
                    <br>
                    <div class="benefit-item">
                        <span class="benefit-check">✓</span>
                        <div>
                            <strong>Tiết kiệm thời gian:</strong> Đặt hàng mọi lúc mọi nơi.
                        </div>
                    </div>
                    <div class="benefit-item">
                        <span class="benefit-check">✓</span>
                        <div>
                            <strong>Xem cơ cở:</strong> Xem cơ sở thông qua hệ thống Camera của chúng tôi.
                        </div>
                    </div>
                    <div class="benefit-item">
                        <span class="benefit-check">✓</span>
                        <div>
                            <strong>Minh bạch thông tin:</strong> Lịch sử giao dịch và công nợ được thống kê rõ ràng, chính xác.
                        </div>
                    </div>
                    <div class="benefit-item">
                        <span class="benefit-check">✓</span>
                        <div>
                            <strong>Hỗ trợ kỹ thuật 24/7:</strong> Kết nối trực tiếp với đội ngũ kỹ thuật của Tất Thành qua ứng dụng.
                        </div>
                    </div>
                </div>
                <div class="benefit-image">
                    <img src="images/son1.jpg" alt="Lợi ích khi dùng App Sơn Tất Thành">
                </div>
            </div>
        </div>
    </section>

    <section class="cta">
        <div class="container">
            <h2>Sẵn sàng trải nghiệm ngay hôm nay?</h2>
            <p>Tải ứng dụng Sơn Tất Thành để nhận ưu đãi chiết khấu 5% cho đơn hàng đầu tiên trên App.</p>
            <div class="btn-group">
                <a href="https://apps.apple.com/us/app/s%C6%A1n-t%E1%BA%A5t-th%C3%A0nh/id6756988687" class="btn btn-white" title="Tải cho IOS (Iphone)"><img src="images/appstore.png"></a>
                <a href="download/sontatthanhmobile.apk" download="" class="btn btn-white" title="Tải cho CHPlay"><img src="images/chplay.png"></a>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2026 DM – Sơn Tất Thành. Bảo lưu mọi quyền.</p>
            <p><small>Website: sontatthanh.vn</small></p>
        </div>
    </footer>

</body>

</html>