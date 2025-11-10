<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\DashboardController;

Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    // Dashboard Overview
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile Settings (Account Settings)
    Route::get('/profile-settings', [DashboardController::class, 'profileSettings'])->name('profile.settings');
    Route::post('/profile-settings', [DashboardController::class, 'updateProfileSettings'])->name('profile.settings.update');
    
    // Other Features
    Route::get('/orders', [DashboardController::class, 'orders'])->name('orders');
    Route::get('/orders/{orderId}', [DashboardController::class, 'orderDetails'])->name('orders.details');
    Route::get('/notifications', [DashboardController::class, 'notifications'])->name('notifications');
    Route::get('/services', [DashboardController::class, 'services'])->name('services');
    Route::get('/services/{bookingId}', [DashboardController::class, 'serviceDetails'])->name('services.details');
    
    // Photo Upload
    Route::post('/upload/avatar', [DashboardController::class, 'uploadAvatar'])->name('upload.avatar');
    Route::post('/remove/avatar', [DashboardController::class, 'removeAvatar'])->name('remove.avatar');
});
