# Database của dự án

# Tổng quan

-- Các bảng mặc định Laravel (đã tạo trong migration):

-   `sessions`
-   `cache`
-   `cache_locks`
-   `jobs`
-   `job_batches`
-   `failed_jobs`
-   `notifications`
-   `password_reset_tokens`

## Bảng chi tiết

### `provinces`

-   Mô tả: Lưu tỉnh/thành.
-   Cấu trúc:
    -   `id` (bigint, PK, auto-increment)
    -   `name` (string) — Tên
    -   `code` (string, unique) — Mã (unique)
    -   `division_type` (string, nullable) — Cấp hành chính
    -   `created_at`, `updated_at`

### `districts`

-   Mô tả: Quận/huyện.
-   Cấu trúc:
    -   `id` (bigint, PK, auto-increment)
    -   `name` (string)
    -   `code` (string, unique)
    -   `division_type` (string, nullable)
    -   `province_code` (foreignId, nullable) — tham chiếu `provinces.code`, `nullOnDelete()`, có index
    -   `created_at`, `updated_at`

### `wards`

-   Mô tả: Phường/xã.
-   Cấu trúc:
    -   `id` (bigint, PK, auto-increment)
    -   `name` (string)
    -   `code` (string, unique)
    -   `division_type` (string, nullable)
    -   `district_code` (foreignId, nullable) — tham chiếu `districts.code`, `nullOnDelete()`, có index
    -   `created_at`, `updated_at`

### `configs`

-   Mô tả: Cấu hình hệ thống.
-   Cấu trúc:
    -   `id`
    -   `config_key` (string, unique)
    -   `config_type` (smallInteger) — enum `ConfigType` (tham chiếu logic ứng dụng)
    -   `config_value` (text)
    -   `description` (text, nullable)
    -   `deleted_at` (soft deletes)
    -   `created_at`, `updated_at`

### `showrooms`

-   Mô tả: Cửa hàng trưng bày.
-   Cấu trúc:
    -   `id`
    -   `name` (string) — index
    -   `address` (string)
    -   `phone` (string, nullable) — index
    -   `email` (string, nullable) — index
    -   `logo` (string, nullable)
    -   `description` (string, nullable)
    -   `province_code` (foreignId, nullable) — tham chiếu `provinces.code`, `nullOnDelete()`, index
    -   `district_code` (foreignId, nullable) — tham chiếu `districts.code`, `nullOnDelete()`, index
    -   `ward_code` (foreignId, nullable) — tham chiếu `wards.code`, `nullOnDelete()`, index
    -   `weblink` (string, nullable)
    -   `latitude` (decimal(10,8), default 0)
    -   `longitude` (decimal(11,8), default 0)
    -   `hotlines` (json, nullable) — ví dụ: [{"label":"Tên hotline","phone":"Số điện thoại"}]
    -   `deleted_at` (soft deletes)
    -   `created_at`, `updated_at`

### `cameras`

-   Mô tả: Camera gắn với showroom.
-   Cấu trúc:
    -   `id`
    -   `name` (string) — index
    -   `ip_address` (string)
    -   `image` (string)
    -   `port` (string)
    -   `app_id` (string)
    -   `api_key` (string)
    -   `api_token` (string)
    -   `description` (string, nullable)
    -   `showroom_id` (foreignId, nullable) — tham chiếu `showrooms.id`, `nullOnDelete()`, index
    -   `deleted_at` (soft deletes)
    -   `created_at`, `updated_at`

### `brands`

-   Mô tả: Thương hiệu sản phẩm.
-   Cấu trúc:
    -   `id`
    -   `name` (string, unique)
    -   `logo` (string, nullable)
    -   `description` (string, nullable)
    -   `deleted_at` (soft deletes)
    -   `created_at`, `updated_at`

### `lines`

-   Mô tả: Dòng sản phẩm.
-   Cấu trúc:
    -   `id`
    -   `name` (string, unique)
    -   `description` (string, nullable)
    -   `deleted_at` (soft deletes)
    -   `created_at`, `updated_at`

### `products`

-   Mô tả: Sản phẩm.
-   Cấu trúc:
    -   `id`
    -   `name` (string) — index
    -   `description` (string, nullable)
    -   `brand_id` (foreignId, nullable) — tham chiếu `brands.id`, `nullOnDelete()`, index
    -   `line_id` (foreignId, nullable) — tham chiếu `lines.id`, `nullOnDelete()`, index
    -   `colors` (json, nullable) — ví dụ: [{"name":"Màu","code":"#fff"}]
    -   `specifications` (json, nullable) — ví dụ: [{"name":"Thông số","value":"Giá trị"}]
    -   `features` (json, nullable) — ví dụ: [{"title":"Tính năng","description":"Giá trị"}]
    -   `quantity` (integer, default 0)
    -   `price` (decimal(10,2), default 0)
    -   `sale_price` (decimal(10,2), default 0)
    -   `is_active` (boolean, default true) — index
    -   `deleted_at` (soft deletes)
    -   `created_at`, `updated_at`

### `product_images`

-   Mô tả: Ảnh sản phẩm.
-   Cấu trúc:
    -   `id`
    -   `product_id` (foreignId, nullable) — tham chiếu `products.id`, `nullOnDelete()`, index
    -   `path` (string)
    -   `created_at`, `updated_at`

### `departments`

-   Mô tả: Phòng ban.
-   Cấu trúc:
    -   `id`
    -   `name` (string, unique)
    -   `created_at`, `updated_at`

### `users`

-   Mô tả: Người dùng hệ thống.
-   Cấu trúc:
    -   `id`
    -   `name` (string) — index
    -   `email` (string, unique, nullable)
    -   `phone` (string, unique)
    -   `email_verified_at` (timestamp, nullable)
    -   `phone_verified_at` (timestamp, nullable)
    -   `avatar` (string, nullable)
    -   `referral_code` (string, length 40, nullable) — index
    -   `role` (unsignedTinyInteger, default 0) — enum `UserRole`, index
    -   `joined_at` (timestamp, default now())
    -   `is_active` (boolean, default true) — index
    -   `department_id` (foreignId, nullable) — tham chiếu `departments.id`, `nullOnDelete()`, index
    -   `password` (string)
    -   `remember_token`
    -   `created_at`, `updated_at`

### `camera_user`

-   Mô tả: Bảng pivot phân quyền người dùng truy cập camera.
-   Cấu trúc:
    -   `id`
    -   `camera_id` (foreignId, nullable) — tham chiếu `cameras.id`, `nullOnDelete()`, index
    -   `user_id` (foreignId, nullable) — tham chiếu `users.id`, `nullOnDelete()`, index
    -   `created_at`, `updated_at`

### `news`

-   Mô tả: Tin tức / bài viết.
-   Cấu trúc:
    -   `id`
    -   `title` (string) — index
    -   `description` (string, nullable)
    -   `content` (text, nullable)
    -   `image` (string, nullable)
    -   `type` (string, nullable) — danh mục, index
    -   `published_at` (timestamp, nullable) — index
    -   `source` (string, nullable)
    -   `is_active` (boolean, default true) — index
    -   `created_by` (foreignId, nullable) — tham chiếu `users.id`, `nullOnDelete()`, index
    -   `deleted_at` (soft deletes)
    -   `created_at`, `updated_at`
