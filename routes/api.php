<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CameraController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ShowroomController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::middleware(['throttle:5,1'])->group(function () {
            Route::post('login', [AuthController::class, 'login']);
            Route::post('register', [AuthController::class, 'register']);
            Route::post('resend', [AuthController::class, 'resendOtp']);
            Route::post('verify', [AuthController::class, 'verifyOtp']);
            Route::post('forgot-password/send', [AuthController::class, 'sendForgotPasswordOtp']);
            Route::post('forgot-password/verify', [AuthController::class, 'verifyForgotPasswordOtp']);
            Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
            Route::post('zalo-authenticate', [AuthController::class, 'zaloAuthenticate']);
            Route::post('keep-zalo-auth-token', [\App\Http\Controllers\Web\ZaloAuthController::class, 'keepZaloAuthToken']);
            Route::post('verify-zalo-auth-token', [\App\Http\Controllers\Web\ZaloAuthController::class, 'verifyZaloAuthToken']);
        });
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('edit-profile', [AuthController::class, 'editProfile']);
    });
});

Route::prefix('showroom')->group(function () {
    Route::get('list', [ShowroomController::class, 'list']);
    Route::middleware('auth:sanctum')->get('camera-library', [ShowroomController::class, 'cameraLibrary']);
    Route::get('{id}', [ShowroomController::class, 'detail']);
});

Route::prefix('brand')->group(function () {
    Route::get('list', [BrandController::class, 'list']);
    Route::get('banner', [BrandController::class, 'banner']);
    Route::get('line', [BrandController::class, 'line']);
});

Route::prefix('product')->group(function () {
    Route::get('list', [ProductController::class, 'list']);
    Route::get('{id}', [ProductController::class, 'detail']);
});

Route::prefix('news')->group(function () {
    Route::get('list', [NewsController::class, 'list']);
    Route::get('{id}', [NewsController::class, 'detail']);
});

Route::prefix('service')->middleware('auth:sanctum')->group(function () {
    Route::post('start-live', [CameraController::class, 'startLive']);
});

Route::prefix('file')->group(function () {
    Route::get('/{path}', [FileController::class, 'download'])->where('path', '.*')->name('file.download');
});

Route::prefix('notification')->middleware(['auth:sanctum'])->group(function () {
    // Lấy danh sách notifications
    Route::get('list', [NotificationController::class, 'paginate']);
    // Đánh dấu đã đọc
    Route::post('read/{id}', [NotificationController::class, 'markRead'])->where('id', '[0-9]+');
    // Đánh dấu tất cả đã đọc
    Route::post('read-all', [NotificationController::class, 'markAllRead']);
    // Lấy số lượng chưa đọc
    Route::get('unread-count', [NotificationController::class, 'unreadCount']);
    // Lấy device token
    Route::post('device-token', [NotificationController::class, 'deviceToken']);
});
