<?php

namespace App\Http\Controllers\Api;

use App\Core\Controller\BaseController;
use Illuminate\Support\Facades\Storage;

class FileController extends BaseController
{
    public function download(string $path)
    {
        // 1. Loại bỏ các dấu gạch chéo ở đầu
        $file_path = ltrim(trim($path), '/');

        // 2. Chuẩn hóa path: thay \ thành / 
        $file_path = str_replace('\\', '/', $file_path);

        // 3. Kiểm tra bảo mật (ngăn ../../)
        if (str_contains($file_path, '..') || str_contains($file_path, '~')) {
            abort(403, 'Đường dẫn không hợp lệ');
        }

        if (!Storage::disk('public')->exists($file_path)) {
            abort(404, 'File không tồn tại');
        }

        return response()->download(Storage::disk('public')->path($file_path));
    }
}
