<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\Staff\StaffStockController;
use App\Http\Controllers\Staff\StaffServiceBookingController;

Route::middleware(['auth', 'staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('dashboard');
    Route::get('/my-schedule', [StaffController::class, 'mySchedule'])->name('my-schedule');
    Route::get('/my-schedule/api/weekly', [StaffController::class, 'getMyWeeklySchedule'])->name('my-schedule.api');
    
    // Stock/Inventory routes
    Route::prefix('products/stock')->name('products.stock.')->group(function () {
        Route::get('/', [StaffStockController::class, 'index'])->name('index');
        Route::get('/api', [StaffStockController::class, 'apiIndex'])->name('api');
    });

    // Service Bookings
    Route::prefix('service-bookings')->name('service-bookings.')->group(function () {
        Route::get('/', [StaffServiceBookingController::class, 'index'])->name('index');
        Route::get('/api', [StaffServiceBookingController::class, 'apiIndex'])->name('api');
        Route::get('/{id}', [StaffServiceBookingController::class, 'show'])->name('show');
        Route::post('/{id}/confirm', [StaffServiceBookingController::class, 'confirm'])->name('confirm');
        Route::post('/{id}/cancel', [StaffServiceBookingController::class, 'cancel'])->name('cancel');
        Route::post('/{id}/mark-paid', [StaffServiceBookingController::class, 'markAsPaid'])->name('mark-paid');
        Route::post('/{id}/complete', [StaffServiceBookingController::class, 'complete'])->name('complete');
    })->name('staff.services.dashboard');
});

