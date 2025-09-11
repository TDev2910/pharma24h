<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Store\CartController;
use App\Http\Controllers\Store\CheckoutController;
use App\Http\Controllers\Store\PaymentController;

//giỏ hàng
Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
Route::get('/cart', [CartController::class, 'getCart'])->name('cart.index');
Route::post('/cart/update', [CartController::class, 'updateQuantity'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'removeItem'])->name('cart.remove');
//thanh toán
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/failed', [CheckoutController::class, 'failed'])->name('checkout.failed');
//thanh toán vnpay
Route::prefix('payment')->name('payment.')->group(function () {
    Route::prefix('vnpay')->name('vnpay.')->group(function () {
        Route::get('/checkout/{order_id}', [PaymentController::class, 'vnpayCheckout'])->name('checkout');
        Route::get('/return', [PaymentController::class, 'vnpayReturn'])->name('return');
        Route::post('/ipn', [PaymentController::class, 'vnpayIpn'])->name('ipn');
    });
});
