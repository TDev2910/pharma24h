# Workflow Triển khai Module Login/Register (Laravel + Vue.js + Inertia.js)

Tài liệu này mô tả lộ trình từng bước để chuyển đổi hệ thống xác thực từ Blade sang Vue.js bằng Inertia, áp dụng Real-time Validation với `computed`, và thiết lập Rate Limit chuẩn SaaS.

---

## Giai đoạn 1: Nâng cấp Security & Rate Limit (Backend - Laravel)

**Mục tiêu:** Chống Brute-force nâng cao bằng cách kết hợp `IP` và `Email`.

1. **Định nghĩa Rate Limiter:**
    - Mở file `app/Providers/AppServiceProvider.php` (hoặc `RouteServiceProvider.php`).
    - Thêm logic giới hạn 5 requests / phút:

        ```php
        use Illuminate\Cache\RateLimiting\Limit;
        use Illuminate\Support\Facades\RateLimiter;
        use Illuminate\Http\Request;
        use Illuminate\Support\Str;

        RateLimiter::for('login-saas', function (Request $request) {
            $key = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());
            return Limit::perMinute(5)->by($key);
        });
        ```

2. **Gắn Middleware vào Route:**
    - Mở `routes/web.php`.
    - Áp dụng limiter vừa tạo:
        ```php
        Route::post('/login', [LoginController::class, 'authenticate'])
             ->middleware('throttle:login-saas');
        ```

---

## Giai đoạn 2: Chuyển đổi Controller (Backend - Laravel)

**Mục tiêu:** Thay thế view Blade bằng giao diện Inertia.

1. **Cập nhật `LoginController.php`:**
    - Đổi `return view('auth.login');` thành `return Inertia::render('Auth/Login');`.
    - Giữ nguyên logic `Auth::attempt()`, session regeneration và redirect.

    ```php
    use Inertia\Inertia;

    public function create()
    {
        return Inertia::render('Auth/Login');
    }
    ```
