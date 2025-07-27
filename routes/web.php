<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\ProductController;


// Trang chủ
Route::get('/', function () {
    return view('user.home');
})->name('home');

// Auth routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout']); 

// Các route khác cho user
Route::get('co-so-kham-benh', function () {
    return view('user.cosokhambenh');
});


Route::get('/products', function () {
    return view('user.products');
})->name('products');

Route::get('/cart', function () {
    return view('user.cart');
})->name('cart');

Route::middleware('auth')->group(function () {
    Route::get('/profile', function () {
        return view('user.profile');
    })->name('profile');
    
    Route::get('/orders', function () {
        return view('user.orders');
    })->name('orders');
});

// Route cho admin dashboard, dùng middleware kiểm tra admin
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/admindashboard', function () {
        return view('admin.admindashboard');
    })->name('admin.dashboard');    
});

