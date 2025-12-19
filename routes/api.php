<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CameraController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ShowroomController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::middleware(['throttle:5,1'])->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
        Route::post('resend', [AuthController::class, 'resendOtp']);
        Route::post('verify', [AuthController::class, 'verifyOtp']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
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

Route::prefix('camera')->middleware('auth:sanctum')->group(function () {
    Route::get('list', [CameraController::class, 'list']);
    Route::post('start-live', [CameraController::class, 'startLive']);
    Route::post('stop-live', [CameraController::class, 'stopLive']);
});

Route::prefix('file')->group(function () {
    Route::get('/{path}', [FileController::class, 'download'])->where('path', '.*')->name('file.download');
});
