# Database của dự án

# Tổng quan

## Các bảng mặc định Laravel

- `sessions`
- `cache`
- `cache_locks`
- `jobs`
- `job_batches`
- `failed_jobs`
- `notifications`
- `password_reset_tokens`

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

- Mô tả: Cửa hàng trưng bày.
- Cấu trúc:
  - `id`
  - `name` (string) — index
  - `address` (string)
  - `email` (string, nullable) — index
  - `logo` (string, nullable)
  - `description` (string, nullable)
  - `province_code` (string, nullable, FK) → `provinces.code`, nullOnDelete, index
  - `district_code` (string, nullable, FK) → `districts.code`, nullOnDelete, index
  - `ward_code` (string, nullable, FK) → `wards.code`, nullOnDelete, index
  - `weblink` (string, nullable)
  - `latitude` (decimal(10,8), default 0)
  - `longitude` (decimal(11,8), default 0)
  - `hotlines` (json, nullable) — ví dụ: [{"label":"Hotline 1","phone":"0123456789"}]
  - `deleted_at` (soft deletes)
  - `created_at`, `updated_at`
### `cameras`

- Mô tả: Camera gắn với showroom 
- Cấu trúc:
  - `id`
  - `name` (string) — index
  - `device_id` (string, nullable, unique) — SN (Serial Number) **[Refactored]**
  - `channel_id` (unsignedTinyInteger, default 0, nullable) — Thường = 0 **[Refactored]**
  - `device_model` (string, nullable) — Model camera **[Refactored]**
  - `bind_status` (boolean, default false) — 0/1 – bind status **[Refactored]**
  - `is_active` (boolean, default false) — 0/1 – active status **[Refactored]**
  - `enable` (boolean, default false) — 0/1 – enable status **[Refactored]**
  - `description` (string(255), nullable)
  - `showroom_id` (FK, nullable) → `showrooms.id`, nullOnDelete, index
  - `deleted_at` (soft deletes)
  - `created_at`, `updated_at`

### `brands`

-   Mô tả: Thương hiệu sản phẩm.
-   Cấu trúc:
    -   `id`
    -   `name` (string, unique)
    -   `logo` (string, nullable)
    -   `description` (string, nullable)
    -   `is_active` (boolean, default true) — Trạng thái 
    -   `deleted_at` (soft deletes)
    -   `created_at`, `updated_at`

### `lines`

-   Mô tả: Dòng sản phẩm.
-   Cấu trúc:
    -   `id`
    -   `name` (string, unique)
    -   `description` (string, nullable)
    -   `is_active` (boolean, default true) — Trạng thái
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
    -   `images` (json, nullable) — ví dụ: ["url1", "url2", ...] 
    -   `quantity` (integer, default 0)
    -   `price` (decimal(15,2), default 0)
    -   `sale_price` (decimal(15,2), default 0)
    -   `is_active` (boolean, default true) — index
    -   `deleted_at` (soft deletes)
    -   `created_at`, `updated_at`
-   **Lưu ý**: Bảng `product_images` đã bị xóa; ảnh lưu trữ dưới dạng JSON trong cột `images`.

### `product_images`

-   **[REMOVED]** — Bảng này đã bị xóa. Ảnh sản phẩm giờ lưu trữ dưới dạng JSON trong cột `images` của bảng `products`.

### `departments`
    
-   Mô tả: Phòng ban.
-   Cấu trúc:
    -   `id`
    -   `name` (string, unique)
    -   `deleted_at` (soft deletes)
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
    -   `deleted_at` (soft deletes)
    -   `created_at`, `updated_at`
