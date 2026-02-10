<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\GHNController;

Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    // Dashboard Overview
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile Settings (Account Settings)
    Route::get('/profile-settings', [DashboardController::class, 'profileSettings'])->name('profile.settings');
    Route::post('/profile-settings', [DashboardController::class, 'updateProfileSettings'])->name('profile.settings.update');
    
    // Payment
    Route::get('/payment', [DashboardController::class, 'payment'])->name('payment');
    
    // Other Features
    Route::get('/orders', [DashboardController::class, 'orders'])->name('orders');
    Route::get('/orders/{orderId}', [DashboardController::class, 'orderDetails'])->name('orders.details');
    Route::get('/orders/{order}/tracking', [DashboardController::class, 'orderTracking'])->name('orders.tracking');
    Route::post('/orders/{order}/request-cancel', [DashboardController::class, 'requestCancel'])
        ->name('orders.request-cancel');
    
    Route::get('/notifications', [DashboardController::class, 'notifications'])->name('notifications');
    Route::get('/notifications/unread-count', [DashboardController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::post('/notifications/{notificationId}/read', [DashboardController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [DashboardController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notificationId}', [DashboardController::class, 'deleteNotification'])->name('notifications.delete');
    Route::get('/services', [DashboardController::class, 'services'])->name('services');
    Route::get('/services/{bookingId}', [DashboardController::class, 'serviceDetails'])->name('services.details');
    
    // Photo Upload
    Route::post('/upload/avatar', [DashboardController::class, 'uploadAvatar'])->name('upload.avatar');
    Route::post('/remove/avatar', [DashboardController::class, 'removeAvatar'])->name('remove.avatar');
});