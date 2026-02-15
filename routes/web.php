<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ServiceBookingController;
use App\Http\Controllers\Public\ReviewController;
use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\Store\CheckoutController;
use App\Http\Controllers\SupportTicketController;
use Inertia\Inertia;

//public routes
Route::get('/',[HomeController::class,'homeInertia'])->name('home');
Route::post('/auth/google', [AuthController::class, 'googleLogin'])->name('auth.google');
//csrf token refresh route
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
// posts
Route::get('/posts', [HomeController::class, 'posts'])->name('posts');
// 3. Trang liên hệ
Route::get('/contact', fn () => Inertia::render('Public/Contact'))->name('contact');
// 4. Gửi liên hệ
Route::post('/contact', [SupportTicketController::class, 'store'])->name('contact.store');
// Review routes
Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store')->middleware('auth');
Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->name('reviews.destroy')->middleware('auth');
// Auth routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout']);

//forgot password routes (email + phone)
Route::prefix('password')->name('password.')->group(function () {
    Route::get('/reset', [ForgotPasswordController::class, 'showEmailForm'])->name('request');
    Route::post('/email', [ForgotPasswordController::class, 'sendOtp'])->name('email');
    Route::get('/verify', [ForgotPasswordController::class, 'showVerifyForm'])->name('verify');
    Route::post('/verify', [ForgotPasswordController::class, 'verifyOtp'])->name('verify.post');
    Route::get('/reset-password', [ForgotPasswordController::class, 'showResetForm'])->name('reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('reset.post');

    //phone verification routes
    Route::get('/verify-phone', [ForgotPasswordController::class, 'showPhoneVerifyForm'])->name('verify.phone');
    Route::post('/verify-phone', [ForgotPasswordController::class, 'verifyPhoneOtp'])->name('verify.phone.post');
    Route::post('/auth/phone-verify', [ForgotPasswordController::class, 'handlePhoneVerification'])->name('phone.verify');
    Route::post('/save-phone', [ForgotPasswordController::class, 'savePhoneToSession'])->name('save.phone');
    Route::post('/reset-phone-otp-attempts', [ForgotPasswordController::class, 'resetPhoneOtpAttempts'])->name('phone.otp.reset.attempts');
});


//user routes
require __DIR__.'/user.php';

//store routes
Route::post('/checkout/shipping-fee', [CheckoutController::class, 'getShippingFee'])
    ->name('checkout.get_shipping_fee');
require __DIR__.'/store.php';

//admin routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/admindashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
});

//staff routes
Route::middleware(['auth', 'staff'])->group(function () {
    Route::get('/staff/dashboard', [StaffController::class, 'dashboard'])->name('staff.dashboard');
});

Route::post('/bookings', [ServiceBookingController::class, 'store'])->name('bookings.store');

// Chatbot routes
Route::post('/api/chatbot/chat', [ChatbotController::class, 'chat'])->name('chatbot.chat');

Route::post('/api/ghn/webhook', [\App\Http\Controllers\Api\GHNWebhookController::class, 'handleWebhook'])
    ->name('ghn.webhook')
    ->withoutMiddleware(['web']);
// Include admin routes
require __DIR__.'/admin.php';

// Include staff routes
require __DIR__.'/staff.php';
