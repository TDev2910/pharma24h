<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Store\CartController;
use App\Http\Controllers\Store\CheckoutController;
use App\Http\Controllers\Store\PaymentController;
use App\Http\Controllers\Admin\Order\GHNController;

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
// Luồng thanh toán tập trung (VNPay, SePay...)
Route::prefix('payment')->name('payment.')->group(function () {
    Route::get('/{driver}/checkout/{order_id}', [PaymentController::class, 'checkout'])->name('checkout');
    Route::get('/{driver}/return', [PaymentController::class, 'return'])->name('return');
    Route::post('/{driver}/ipn', [PaymentController::class, 'ipn'])->name('ipn');
});

// GHN API routes (public for checkout form)
Route::prefix('ghn')->name('ghn.')->group(function () {
    Route::get('provinces', [GHNController::class, 'getProvinces'])->name('provinces');
    Route::post('districts', [GHNController::class, 'getDistricts'])->name('districts');
    Route::post('wards', [GHNController::class, 'getWards'])->name('wards');
    Route::post('map-address', [GHNController::class, 'mapAddressToGHN'])->name('map-address');
    Route::get('test', [GHNController::class, 'testConnection'])->name('test');
});