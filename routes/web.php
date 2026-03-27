<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ServiceBookingController;
use App\Http\Controllers\Public\ReviewController;
use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\Store\CheckoutController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\ChatController;
use Inertia\Inertia;

//public routes
Route::get('/', [HomeController::class, 'homeInertia'])->name('home');
Route::post('/auth/google', [AuthController::class, 'googleLogin'])->name('auth.google');
//csrf token refresh route
Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
})->name('csrf-cookie');

Route::get('/medical-team', [HomeController::class, 'medicalTeam'])->name('medical-team');

Route::get('/products', [HomeController::class, 'products'])->name('products');
Route::get('/san-pham/{slug}', [HomeController::class, 'productDetailBySlug'])->name('products.detail');
Route::get('/services', [HomeController::class, 'services'])->name('services');
Route::get('/services/{id}', [HomeController::class, 'serviceDetail'])->name('services.detail');
// posts
Route::get('/posts', [HomeController::class, 'posts'])->name('posts');
Route::get('/bai-viet/{slug}', [HomeController::class, 'detailsPost'])->name('posts.details');
// 3. Trang liên hệ
Route::get('/contact', fn() => Inertia::render('Public/Contact'))->name('contact');
// 4. Gửi liên hệ
Route::post('/contact', [SupportTicketController::class, 'store'])->name('contact.store');
// Review routes
Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store')->middleware('auth');
Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->name('reviews.destroy')->middleware('auth');
// Auth routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login-saas');

Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
Route::get('/chat/messages/{sessionId}', [ChatController::class, 'getMessages'])->name('chat.messages');

// Route::get('/logout', [AuthController::class, 'logout']);

//forgot password routes (email + phone)
Route::prefix('password')->name('password.')->group(function () {
    Route::get('/reset', [PasswordResetController::class, 'showEmailForm'])->name('request');
    Route::post('/email', [PasswordResetController::class, 'sendOtp'])->name('email');
    Route::get('/verify', [PasswordResetController::class, 'showVerifyForm'])->name('verify');
    Route::post('/verify', [PasswordResetController::class, 'verifyOtp'])->name('verify.post');
    Route::get('/reset-password', [PasswordResetController::class, 'showResetForm'])->name('reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('reset.post');

    //phone verification routes
    Route::get('/verify-phone', [PasswordResetController::class, 'showPhoneVerifyForm'])->name('verify.phone');
    Route::post('/verify-phone', [PasswordResetController::class, 'verifyPhoneOtp'])->name('verify.phone.post');
    Route::post('/auth/phone-verify', [PasswordResetController::class, 'handlePhoneVerification'])->name('phone.verify');
    Route::post('/save-phone', [PasswordResetController::class, 'savePhoneToSession'])->name('save.phone');
    Route::post('/reset-phone-otp-attempts', [PasswordResetController::class, 'resetPhoneOtpAttempts'])->name('phone.otp.reset.attempts');
});


//user routes
require __DIR__ . '/user.php';

//store routes
Route::post('/checkout/shipping-fee', [CheckoutController::class, 'getShippingFee'])
    ->name('checkout.get_shipping_fee');
require __DIR__ . '/store.php';

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
require __DIR__ . '/admin.php';

// Include staff routes
require __DIR__ . '/staff.php';
