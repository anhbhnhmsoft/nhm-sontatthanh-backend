<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ... (Nội dung của public function up() giữ nguyên như code bạn cung cấp)
        // custom schema

        /**
         * Bảng provinces
         * note: bảng tỉnh thành
         */
        Schema::create('provinces', function (Blueprint $table) {;
            $table->id();
            $table->string('name')->comment('Tên');
            $table->string('code')->unique()->comment('Mã');
            $table->string('division_type')->nullable()->comment('Cấp hành chính');
            $table->timestamps();
        });

        /**
         * Bảng districts
         * note: bảng quận huyện
         */
        Schema::create('districts', function (Blueprint $table) {;
            $table->id();
            $table->string('name')->comment('Tên');
            $table->string('code')->unique()->comment('Mã');
            $table->string('division_type')->nullable()->comment('Cấp hành chính');
            $table->string('province_code')->nullable()->comment('Tỉnh thành')->index();
            $table->timestamps();
            $table->foreign('province_code')->references('code')->on('provinces')->nullOnDelete();
        });

        /**
         * Bảng wards
         * note: bảng phường xã
         */
        Schema::create('wards', function (Blueprint $table) {;
            $table->id();
            $table->string('name')->comment('Tên');
            $table->string('code')->unique()->comment('Mã');
            $table->string('division_type')->nullable()->comment('Cấp hành chính');
            $table->string('district_code')->nullable()->comment('Quận huyện')->index();
            $table->timestamps();
            $table->foreign('district_code')->references('code')->on('districts')->nullOnDelete();
        });

        /**
         * Bảng configs
         * note: bảng cấu hình hệ thống
         */
        Schema::create('configs', function (Blueprint $table) {
            $table->id();
            $table->string('config_key')->unique()->comment('Khóa cấu hình');
            $table->smallInteger('config_type')->comment('Kiểu cấu hình (trong enum ConfigType)');
            $table->text('config_value')->comment('Giá trị cấu hình');
            $table->text('description')->nullable()->comment('Mô tả cấu hình');
            $table->softDeletes();
            $table->timestamps();
        });

        /**
         * Bảng showrooms
         * note: bảng cửa hàng trưng bày
         */
        Schema::create('showrooms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Tên cửa hàng')->index();
            $table->string('address')->comment('Địa chỉ cửa hàng');
            $table->string('email')->nullable()->comment('Email cửa hàng')->index();
            $table->string('logo')->nullable()->comment('Logo cửa hàng');
            $table->string('description')->nullable()->comment('Mô tả cửa hàng');
            $table->string('province_code')->nullable()->comment('Mã tỉnh')->index();
            $table->string('district_code')->nullable()->index();
            $table->string('ward_code')->nullable()->index();
            $table->string('weblink')->nullable();
            $table->decimal('latitude', 10, 8)->default(0);
            $table->decimal('longitude', 11, 8)->default(0);
            $table->json('hotlines')->nullable()->comment('Số điện thoại hotline, struct: [{"label": "Tên hotline", "phone": "Số điện thoại"}]');
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('province_code')->references('code')->on('provinces')->nullOnDelete();
            $table->foreign('district_code')->references('code')->on('districts')->nullOnDelete();
            $table->foreign('ward_code')->references('code')->on('wards')->nullOnDelete();
        });

        /**
         * Bảng cameras
         * note: bảng lưu thông tin camera ở showrooms
         */
        Schema::create('cameras', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Tên camera')->index();
            $table->string('device_id')->nullable()->comment('SN – bắt buộc')->unique();
            $table->unsignedTinyInteger('channel_id')->default(0)->nullable()->comment('số luồng camera ~ số luồng stream được ~ số  mắt của thiết bị: 0 ~ 1 mắt và tăng dần 1 ~ 2 mắt 2 luồng ');
            $table->string('image')->nullable()->comment('Hình ảnh mặc định camera');
            $table->boolean('bind_status')->default(false)->comment('0/1 – bind hay chưa - bind này là bind và account developer chưa ');
            $table->boolean('is_active')->default(false)->comment('0/1 – active hay chưa - active để dừng trả ra dữ liệu cho phé mobile truy cập');
            $table->boolean('enable')->default(false)->comment('0/1 – enable hay chưa ~ enable là trạng thái thực tế của camera, còn sử dụng được hay không');
            $table->string('description', 255)->nullable()->comment('Mô tả camera');
            $table->foreignId('showroom_id')->nullable()->constrained('showrooms')->nullOnDelete()->comment('Cửa hàng trưng bày')->index();
            $table->string('security_code')->nullable()->comment('Mã bảo mật');
            $table->softDeletes();
            $table->timestamps();
        });

        /**
         * Bảng channels
         * note: bảng kênh video
         */
        Schema::create('channels', function (Blueprint $table) {
            $table->id();
            $table->comment('Kênh video của camera');
            $table->foreignId('camera_id')->constrained('cameras')->cascadeOnDelete();
            $table->tinyInteger('status')->comment('Trạng thái');
            $table->string('name')->comment('Tên kênh');
            $table->softDeletes();
            $table->timestamps();
        });
        /**
         * Bảng brands 
         * note: bảng thương hiệu sản phẩm
         */
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Tên thương hiệu')->unique(); // Unique Index: Tên thương hiệu là duy nhất
            $table->string('logo')->nullable()->comment('Logo thương hiệu');
            $table->string('description')->nullable()->comment('Mô tả thương hiệu');
            $table->boolean('is_active')->default(true)->comment('Trạng thái thương hiệu');
            $table->softDeletes();
            $table->timestamps();
        });

        /**
         * Bảng lines
         * note: bảng lưu thông tin dòng sản phẩm
         */
        Schema::create('lines', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Tên dòng sản phẩm')->unique(); // Unique Index: Tên dòng sản phẩm là duy nhất
            $table->string('description')->nullable()->comment('Mô tả dòng sản phẩm');
            $table->boolean('is_active')->default(true)->comment('Trạng thái dòng sản phẩm');
            $table->softDeletes();
            $table->timestamps();
        });

        /**
         * Bảng products 
         * note: bảng sản phẩm
         */
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index(); // Index: Tìm kiếm theo tên
            $table->string('description')->nullable();
            $table->foreignId('brand_id')->nullable()->constrained('brands', 'id', 'fk_products_brand_id')->nullOnDelete();
            $table->foreignId('line_id')->nullable()->constrained('lines', 'id', 'fk_products_line_id')->nullOnDelete();
            $table->json('colors')->nullable()->comment('Màu sắc sản phẩm, cấu trúc: [ { "name": "Màu sắc", "code": "Màu sắc" } ]');
            $table->json('specifications')->nullable()->comment('Thông số sản phẩm, cấu trúc: [ { "name": "Thông số", "value": "Giá trị" } ]');
            $table->json('features')->nullable()->comment('Tính năng sản phẩm, cấu trúc: [ { "title": "Tính năng", "description": "Giá trị" } ]');
            $table->json('images')->nullable()->comment('Ảnh sản phẩm, cấu trúc: [url1, url2, ...]');
            $table->integer('quantity')->default(0)->comment('Số lượng sản phẩm');
            $table->decimal('price', 15, 2)->default(0)->comment('Giá sản phẩm');
            $table->decimal('sale_price', 15, 2)->default(0)->comment('Giá sale sản phẩm');
            $table->integer('discount_percent')->default(0)->nullable()->comment('Phần trăm chiết khấu');
            $table->decimal('sell_price', 15, 2)->default(0)->comment('Giá bán');
            $table->decimal('price_discount', 15, 2)->default(0)->comment('Phần sau chiết khấu');
            $table->boolean('is_active')->default(true)->comment('Trạng thái sản phẩm ~ lock / unlock - tính năng khóa sản phẩm')->index(); // Index: Lọc theo trạng thái
            $table->softDeletes();
            $table->timestamps();
        });

        // product_images table removed - images are now stored as json in products table

        /**
         * Bảng Deparments
         * note: bảng phòng ban của hệ thống
         */
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Unique Index: Tên phòng ban là duy nhất
            $table->softDeletes();
            $table->timestamps();
        });

        /**
         * Bảng users
         * note: bảng người dùng của hệ thống
         */
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Tên người dùng')->index();
            $table->string('email')->unique()->nullable();
            $table->string('phone')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('avatar')->nullable();
            $table->string('referral_code', 40)->nullable()->index();
            $table->unsignedTinyInteger('role')->default(0)->comment('Vai trò người dùng trong enum UserRole')->index();
            $table->timestamp('joined_at')->default(now())->comment('Thời gian tham vào công ty ~ thời gian bắt đầu sử dụng hệ thống');
            $table->boolean('is_active')->default(true)->comment('Trạng thái người dùng ~ lock / unlock - tính năng khóa tài khoản')->index(); // Index: Lọc theo trạng thái
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete()->comment('Phòng ban');
            $table->foreignId('showroom_id')->nullable()->constrained('showrooms')->nullOnDelete()->comment('Showroom làm việc');
            $table->unsignedBigInteger('sale_id')->nullable()->comment('Người bán quản lý - tham chiếu đến user khác');
            $table->string('password');
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('sale_id')->references('id')->on('users')->nullOnDelete();
        });

        /**
         * Bảng camera_user
         * note: Gán quyền truy cập camera cho người dùng 
         */
        Schema::create('camera_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('camera_id')->nullable()->constrained('cameras')->nullOnDelete()->comment('Camera');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('Người dùng');
            $table->timestamps();
        });

        /**
         * Bảng news
         * note: bảng tin tức
         */
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('Tiêu đề tin tức')->index(); // Index: Tìm kiếm theo tiêu đề
            $table->string('description')->nullable()->comment('Mô tả tin tức');
            $table->text('content')->nullable()->comment('Nội dung tin tức');
            $table->string('image')->nullable()->comment('Ảnh tin tức');
            $table->string('type')->nullable()->comment('Danh mục tin tức')->index(); // Index: Lọc theo danh mục
            $table->timestamp('published_at')->nullable()->comment('Thời gian đăng tin tức')->index(); // Index: Sắp xếp theo thời gian đăng
            $table->string('source')->nullable()->comment('Nguồn tin tức');
            $table->boolean('is_active')->default(true)->comment('Trạng thái tin tức ~ lock / unlock - tính năng khóa tin tức')->index(); // Index: Lọc theo trạng thái
            $table->bigInteger('view_count')->default(0)->comment('Số lượt xem tin tức')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->comment('Người tạo');
            $table->softDeletes();
            $table->timestamps();
        });

        /**
         * Bảng banners
         * note: bảng banner
         */
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Tên banner')->index();
            $table->string('image')->nullable()->comment('Hình ảnh banner');
            $table->boolean('is_active')->default(false)->comment('0/1 – active status');
            $table->tinyInteger('position')->default(0)->comment('Vị trí banner');
            $table->softDeletes();
            $table->timestamps();
        });

        // default schema

        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->text('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('phone')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });


        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->foreignId('user_id')->constrained('users')->nullOnDelete();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('expo_push_token')->unique();
            $table->string('device_id')->nullable();
            $table->string('device_type', 20)->nullable();
            $table->dateTime('last_seen_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Default Laravel Schema - Xóa trước tiên vì ít phụ thuộc
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('user_devices');   
        Schema::dropIfExists('notifications');

        // Custom Schema - Xóa theo thứ tự phụ thuộc (bảng con trước, bảng cha sau)

        // Bảng có khóa ngoại tham chiếu đến users, cameras, products, departments, showrooms, brands, lines
        Schema::dropIfExists('channels');
        Schema::dropIfExists('news'); // Tham chiếu đến users
        Schema::dropIfExists('banners');
        Schema::dropIfExists('camera_user'); // Tham chiếu đến users và cameras
        Schema::dropIfExists('products'); // Tham chiếu đến brands và lines (product_images đã được merge vào products)
        Schema::dropIfExists('cameras'); // Tham chiếu đến showrooms
        Schema::dropIfExists('users'); // Tham chiếu đến departments
        Schema::dropIfExists('showrooms'); // Tham chiếu đến provinces, districts, wards

        // Bảng danh mục đơn giản/bảng cha
        Schema::dropIfExists('departments');
        Schema::dropIfExists('lines');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('configs');

        // Bảng Địa lý (Xóa sau cùng vì các bảng showrooms phụ thuộc vào chúng)
        Schema::dropIfExists('wards');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('provinces');
    }
};
