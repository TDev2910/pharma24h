<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceBookingController;
use App\Http\Controllers\Api\ChatbotController;
use Inertia\Inertia;
 
// 🌐 PUBLIC ROUTES
Route::get('/',[HomeController::class,'homeInertia'])->name('home');

// CSRF Token refresh route
Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
})->name('csrf-cookie');
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
Route::get('/products/{type}/{id}', [HomeController::class, 'productDetail'])->name('products.detail');
Route::get('/services', [HomeController::class, 'services'])->name('services');
Route::get('/services/{id}', [HomeController::class, 'serviceDetail'])->name('services.detail');
Route::get('/contact', fn () => Inertia::render('Public/Contact'))->name('contact');

// Review routes
Route::post('/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store')->middleware('auth');
Route::delete('/reviews/{id}', [App\Http\Controllers\ReviewController::class, 'destroy'])->name('reviews.destroy')->middleware('auth');
// Auth routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout']); 

// Forgot Password Routes (Email + Phone)
Route::prefix('password')->name('password.')->group(function () {
    Route::get('/reset', [ForgotPasswordController::class, 'showEmailForm'])->name('request');
    Route::post('/email', [ForgotPasswordController::class, 'sendOtp'])->name('email');
    Route::get('/verify', [ForgotPasswordController::class, 'showVerifyForm'])->name('verify');
    Route::post('/verify', [ForgotPasswordController::class, 'verifyOtp'])->name('verify.post');
    Route::get('/reset-password', [ForgotPasswordController::class, 'showResetForm'])->name('reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('reset.post');
    
    // Phone verification routes
    Route::get('/verify-phone', [ForgotPasswordController::class, 'showPhoneVerifyForm'])->name('verify.phone');
    Route::post('/verify-phone', [ForgotPasswordController::class, 'verifyPhoneOtp'])->name('verify.phone.post');
    Route::post('/auth/phone-verify', [ForgotPasswordController::class, 'handlePhoneVerification'])->name('phone.verify');
    Route::post('/save-phone', [ForgotPasswordController::class, 'savePhoneToSession'])->name('save.phone');
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

Route::post('/bookings', [ServiceBookingController::class, 'store'])->name('bookings.store');

// Chatbot routes
Route::get('/chatbot', [ChatbotController::class, 'index'])->name('chatbot');
Route::post('/api/chatbot/chat', [ChatbotController::class, 'chat'])->name('chatbot.chat');

// Include admin routes
require __DIR__.'/admin.php';
