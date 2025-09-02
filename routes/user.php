<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\DashboardController;



Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard', [DashboardController::class, 'index'])->name('dashboard.update');
    
    // Cart & Orders
    Route::get('/cart', [DashboardController::class, 'cart'])->name('cart');
    Route::get('/orders', [DashboardController::class, 'orders'])->name('orders');
    
    // Photo Upload
    Route::post('/upload/avatar', [DashboardController::class, 'uploadAvatar'])->name('upload.avatar');
    Route::post('/remove/avatar', [DashboardController::class, 'removeAvatar'])->name('remove.avatar');
});
