<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\HomeController;
use Inertia\Inertia;
 
// 🌐 PUBLIC ROUTES
Route::get('/',[HomeController::class,'homeInertia'])->name('home');
Route::get('/co-so-kham-benh', function () {
    return Inertia::render('Public/Facilities', [
        'auth' => [
            'user' => auth()->user() ? [
                'id' => auth()->user()->id,
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'role' => auth()->user()->role,
            ] : null,
        ],
    ]);
})->name('cosokhambenh');
Route::get('/products', [HomeController::class, 'products'])->name('products');
Route::get('/services', fn () => Inertia::render('Public/Services'))->name('services');

// Route::get('/about', [HomeController::class, 'about'])->name('about');
// Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

// Auth routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout']); 

// Forgot Password Routes
Route::prefix('password')->name('password.')->group(function () {
    Route::get('/reset', [ForgotPasswordController::class, 'showEmailForm'])->name('request');
    Route::post('/email', [ForgotPasswordController::class, 'sendOtp'])->name('email');
    Route::get('/verify', [ForgotPasswordController::class, 'showVerifyForm'])->name('verify');
    Route::post('/verify', [ForgotPasswordController::class, 'verifyOtp'])->name('verify.post');
    Route::get('/reset-password', [ForgotPasswordController::class, 'showResetForm'])->name('reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('reset.post');
});

// Include user routes
require __DIR__.'/user.php';

// Include store routes
require __DIR__.'/store.php';

// Admin routes
Route::middleware(['auth', 'admin'])->group(function () {   
    Route::get('/admin/admindashboard', function () {
        return view('admin.admindashboard');
    })->name('admin.dashboard');    
});

// Include admin routes
require __DIR__.'/admin.php';

 