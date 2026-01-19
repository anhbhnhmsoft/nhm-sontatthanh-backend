<?php

use App\Http\Controllers\Web\LandingPagecontroller;
use App\Http\Controllers\Web\ZaloAuthController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
})->name('welcome');
// Zalo OAuth Routes
Route::prefix('auth/zalo')->group(function () {
    Route::get('redirect', [ZaloAuthController::class, 'redirect'])->name('zalo.redirect');
    Route::get('callback', [ZaloAuthController::class, 'callback'])->name('zalo.callback');
});
Route::get('download', [LandingPagecontroller::class, 'download'])->name('download');

Route::view('/privacy-policy', 'privacy-policy')->name('privacy-policy');
