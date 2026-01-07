<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chính sách bảo mật - Sơn Tất Thành</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-700 font-sans antialiased">
    <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0 bg-gray-50">
        <div class="w-full max-w-4xl mt-6 p-8 bg-white shadow-md overflow-hidden sm:rounded-lg prose prose-indigo">
            <div class="flex flex-col items-center mb-8">
                <img src="{{ asset('images/logo.png') }}" alt="Sơn Tất Thành Logo" class="h-20 w-auto mb-4">
                <h1 class="text-3xl font-bold text-center text-gray-900">Chính sách bảo mật</h1>
                <p class="text-gray-500 text-sm mt-2">Cập nhật lần cuối: {{ date('d/m/Y') }}</p>
            </div>

            <div class="space-y-6">
                <section>
                    <h2 class="text-xl font-semibold text-gray-800">1. Giới thiệu</h2>
                    <p>Chào mừng bạn đến với ứng dụng Sơn Tất Thành. Chúng tôi cam kết bảo vệ thông tin cá nhân của bạn và tôn trọng quyền riêng tư của bạn. Chính sách bảo mật này giải thích cách chúng tôi thu thập, sử dụng, tiết lộ và bảo vệ thông tin của bạn khi bạn sử dụng ứng dụng di động của chúng tôi.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-800">2. Thu thập thông tin</h2>
                    <p>Chúng tôi có thể thu thập các loại thông tin sau:</p>
                    <ul class="list-disc ml-5 mt-2">
                        <li><strong>Thông tin cá nhân:</strong> Tên, số điện thoại, địa chỉ email, và ảnh đại diện khi bạn đăng ký hoặc cập nhật hồ sơ.</li>
                        <li><strong>Thông tin thiết bị:</strong> Thông tin về thiết bị di động của bạn, bao gồm ID thiết bị, hệ điều hành và phiên bản.</li>
                        <li><strong>Thông tin vị trí:</strong> Chúng tôi có thể thu thập thông tin vị trí của bạn nếu bạn cho phép để cung cấp các dịch vụ dựa trên vị trí.</li>
                        <li><strong>Thông tin sử dụng:</strong> Thông tin về cách bạn sử dụng ứng dụng, các tính năng bạn truy cập và thời gian hoạt động.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-800">3. Sử dụng thông tin</h2>
                    <p>Chúng tôi sử dụng thông tin thu thập được để:</p>
                    <ul class="list-disc ml-5 mt-2">
                        <li>Cung cấp và duy trì dịch vụ của chúng tôi.</li>
                        <li>Thông báo cho bạn về các thay đổi trong dịch vụ.</li>
                        <li>Cho phép bạn tham gia vào các tính năng tương tác.</li>
                        <li>Hỗ trợ khách hàng.</li>
                        <li>Thu thập thông tin phân tích để cải thiện ứng dụng.</li>
                        <li>Phát hiện, ngăn chặn và giải quyết các vấn đề kỹ thuật.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-800">4. Chia sẻ thông tin</h2>
                    <p>Chúng tôi không bán, trao đổi hoặc chuyển giao thông tin cá nhân của bạn cho bên thứ ba ngoại trừ các trường hợp sau:</p>
                    <ul class="list-disc ml-5 mt-2">
                        <li><strong>Nhà cung cấp dịch vụ:</strong> Chúng tôi có thể chia sẻ thông tin với các bên thứ ba đáng tin cậy giúp chúng tôi vận hành ứng dụng, miễn là các bên này đồng ý giữ bí mật thông tin này.</li>
                        <li><strong>Tuân thủ pháp luật:</strong> Chúng tôi có thể tiết lộ thông tin của bạn khi chúng tôi tin rằng việc tiết lộ là phù hợp để tuân thủ pháp luật, thực thi chính sách của chúng tôi hoặc bảo vệ quyền, tài sản hoặc sự an toàn của chúng tôi hoặc của người khác.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-800">5. Bảo mật dữ liệu</h2>
                    <p>Chúng tôi thực hiện các biện pháp an ninh thích hợp để bảo vệ chống lại việc truy cập trái phép, thay đổi, tiết lộ hoặc phá hủy thông tin cá nhân của bạn. Tuy nhiên, không có phương thức truyền tải qua Internet hoặc phương thức lưu trữ điện tử nào là an toàn 100%, vì vậy chúng tôi không thể đảm bảo an ninh tuyệt đối.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-800">6. Quyền của bạn</h2>
                    <p>Bạn có quyền truy cập, chỉnh sửa hoặc xóa thông tin cá nhân của mình bất cứ lúc nào thông qua cài đặt trong ứng dụng hoặc bằng cách liên hệ với chúng tôi.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-800">7. Xóa tài khoản</h2>
                    <p>Nếu bạn muốn xóa tài khoản và dữ liệu liên quan, vui lòng sử dụng chức năng "Xóa tài khoản" trong phần cài đặt của ứng dụng hoặc liên hệ với bộ phận hỗ trợ của chúng tôi.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-800">8. Thay đổi chính sách</h2>
                    <p>Chúng tôi có thể cập nhật Chính sách bảo mật này theo thời gian. Chúng tôi sẽ thông báo cho bạn về bất kỳ thay đổi nào bằng cách đăng Chính sách bảo mật mới trên trang này. Bạn nên xem lại Chính sách bảo mật này định kỳ để biết các thay đổi.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-800">9. Liên hệ</h2>
                    <p>Nếu bạn có bất kỳ câu hỏi nào về Chính sách bảo mật này, vui lòng liên hệ với chúng tôi:</p>
                    <ul class="list-none mt-2">
                        <li><strong>Số điện thoại:</strong> 0907.012.345</li>
                        <li><strong>Email:</strong> support@sontatthanh.com</li>
                        <li><strong>Địa chỉ:</strong> Số 123, Đường ABC, Phường XYZ, Quận 1, TP. Hồ Chí Minh</li>
                    </ul>
                </section>
            </div>

            <div class="mt-8 pt-8 border-t border-gray-200 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} Sơn Tất Thành. All rights reserved.
            </div>
        </div>
    </div>
</body>

</html>