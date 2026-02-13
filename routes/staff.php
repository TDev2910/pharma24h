<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\Staff\StaffStockController;
use App\Http\Controllers\Staff\StaffServiceBookingController;
use App\Http\Controllers\Staff\StaffCustomerController;
use App\Http\Controllers\Staff\StaffOrderController;
use App\Http\Controllers\Staff\StafGHNController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\Staff\PostController;
use App\Http\Controllers\Staff\CategoryController;

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

    // Customers
    Route::prefix('customers')->name('customers.')->controller(StaffCustomerController::class)->group(function () {
        // 1. Danh sách (GET)
        Route::get('/', 'index')->name('index');

        // 2. Thêm mới (POST) - Bạn đang thiếu cái này nên chức năng Thêm sẽ lỗi
        Route::post('/', 'store')->name('store');

        // 3. API Lấy dữ liệu 1 khách hàng để sửa (GET)
        Route::get('/{id}/edit', 'edit')->name('edit'); // Hoặc get('/{id}') tùy logic frontend

        // 4. Cập nhật (PUT) - SỬA LỖI 405 TẠI ĐÂY
        // Frontend gửi PUT thì Backend phải hứng bằng PUT
        Route::put('/{id}', 'update')->name('update');

        // 5. Xóa (DELETE)
        Route::delete('/{id}', 'destroy')->name('destroy');

        // Các route phụ khác (nếu cần)
        Route::get('/api', 'apiIndex')->name('api');
        Route::post('/{id}/confirm', 'confirm')->name('confirm');
        Route::post('/{id}/cancel', 'cancel')->name('cancel');
    });

    // Orders
    Route::get('orders/transport', [StaffOrderController::class, 'transport'])->name('orders.transport');
    Route::resource('orders', StaffOrderController::class)->names('orders');
    Route::post('orders/{order}/update-status', [StaffOrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::get('orders/{order}/invoice', [StaffOrderController::class, 'printInvoice'])->name('orders.invoice');
    Route::post('/orders/{id}/complete', [StaffOrderController::class, 'markCompleted'])->name('orders.complete');
    Route::post('orders/{order}/cancellations/approve', [StaffOrderController::class, 'approveCancellation'])
        ->name('orders.cancellations.approve');
    Route::post('orders/{order}/cancellations/reject', [StaffOrderController::class, 'rejectCancellation'])
        ->name('orders.cancellations.reject');
    Route::post('orders/{order}/update-info', [StaffOrderController::class, 'update'])
        ->name('orders.update-info');

    // GHN routes - Thêm nhóm routes này
    Route::prefix('ghn')->name('ghn.')->group(function () {
        Route::post('orders/{order}/create', [StafGHNController::class, 'createShippingOrder'])->name('orders.create');
        Route::post('shipping-fee', [StafGHNController::class, 'getShippingFee'])->name('shipping-fee');
        Route::get('provinces', [StafGHNController::class, 'getProvinces'])->name('provinces');
        Route::post('districts', [StafGHNController::class, 'getDistricts'])->name('districts');
        Route::post('wards', [StafGHNController::class, 'getWards'])->name('wards');
        Route::get('orders/{order}/track', [StafGHNController::class, 'trackOrder'])->name('orders.track');
        Route::post('orders/{order}/sync-status', [StafGHNController::class, 'syncGhnStatus'])->name('orders.sync-status');
    });
    // Tickets
    Route::prefix('tickets')->name('tickets.')->middleware(['auth', 'staff'])->group(function () {
        Route::get('/', [SupportTicketController::class, 'index'])->name('index');
        Route::get('/api', [SupportTicketController::class, 'getTickets'])->name('api');
        Route::get('/{id}', [SupportTicketController::class, 'show'])->name('show');
        Route::post('/{id}/reply', [SupportTicketController::class, 'reply'])->name('reply');
    });

    //Category
    Route::resource('categories', CategoryController::class);

    //Post
    Route::delete('posts/images/{id}', [PostController::class, 'deleteImage']);
    Route::resource('posts', PostController::class);
});
