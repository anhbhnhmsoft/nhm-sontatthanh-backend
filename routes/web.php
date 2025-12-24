<?php

use App\Http\Controllers\Web\ZaloAuthController;
use Illuminate\Support\Facades\Route;

// Zalo OAuth Routes
Route::prefix('auth/zalo')->group(function () {
    Route::get('redirect', [ZaloAuthController::class, 'redirect'])->name('zalo.redirect');
    Route::get('callback', [ZaloAuthController::class, 'callback'])->name('zalo.callback');
});
