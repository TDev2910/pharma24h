# Kế hoạch chuyển đổi Auth sang Vue.js (Kiến trúc Hexagonal)

Chuyển đổi module xác thực (Auth) hiện tại từ Blade sang Vue.js + Inertia, đồng thời áp dụng mô hình kiến trúc Hexagonal để chuẩn hóa code và tăng cường bảo mật.

## Nội dung cần người dùng xác nhận

> [!IMPORTANT]
> - **Kiến trúc**: Logic xác thực sẽ được chuyển vào `app/Core/Auth` và `app/Infrastructure/Persistence/Eloquent/AuthRepository.php` để đồng bộ với mô hình của module Customer.
> - **Xác thực dựa trên Session**: Chúng ta sẽ tiếp tục dùng Session (Laravel Sanctum) vì đây là phương thức tối ưu nhất cho Inertia.js (không dùng JWT như định hướng ban đầu).
> - **Giới hạn truy cập (Rate Limiting)**: Triển khai bộ giới hạn `login-saas` để chống tấn công brute-force (5 lần thử/phút dựa trên Email + IP).

## Các thay đổi dự kiến

### 1. Backend: Bảo mật & Lõi (Core)

#### [THAY ĐỔI] [AppServiceProvider.php](file:///d:/laragon/laragon/www/suckhoe24h/app/Providers/AppServiceProvider.php)
- Định nghĩa rate limiter `login-saas`.
- Đăng ký (Bind) các Interface của Auth vào Service và Repository tương ứng.

#### [MỚI] Cấu trúc Tầng Core
- `app/Core/Auth/Domain/DTOs/LoginData.php`: Đối tượng vận chuyển dữ liệu đăng nhập.
- `app/Core/Auth/Ports/Inbound/AuthUseCaseInterface.php`: Giao diện điều hướng luồng xử lý (Inbound Port).
- `app/Core/Auth/Ports/Outbound/AuthRepositoryInterface.php`: Giao diện thao tác dữ liệu (Outbound Port).
- `app/Core/Auth/Application/Services/AuthService.php`: Logic nghiệp vụ (xử lý đăng nhập, đăng ký).

#### [MỚI] Tầng Infrastructure
- `app/Infrastructure/Persistence/Eloquent/AuthRepository.php`: Thực thi các truy vấn Database thông qua Eloquent.

#### [THAY ĐỔI] [AuthController.php](file:///d:/laragon/laragon/www/suckhoe24h/app/Http/Controllers/Auth/AuthController.php)
- Inject `AuthUseCaseInterface` qua constructor.
- Cập nhật `showLoginForm` để trả về `Inertia::render('Auth/Login')`.
- Cập nhật hàm `login` sử dụng `AuthService` và phản hồi lỗi qua Inertia.
- Gắn middleware `throttle:login-saas` cho route login.

### 2. Frontend: Vue.js

#### [MỚI] [Login.vue](file:///d:/laragon/laragon/www/suckhoe24h/resources/js/Pages/Auth/Login.vue)
- Tái tạo lại giao diện Login hiện tại bằng Vue/PrimeVue.
- Sử dụng `computed` để kiểm tra lỗi nhập liệu của Email và Password theo thời gian thực (real-time).
- Sử dụng `useForm` của Inertia để quản lý trạng thái form và gửi dữ liệu.

## Kế hoạch kiểm tra (Verification Plan)

### Kiểm tra tự động
- Thử nghiệm rate limit bằng cách đăng nhập sai liên tục hơn 5 lần để kiểm tra tính năng bảo mật.
- Viết Unit test cho `AuthService`.

### Kiểm tra thủ công
1.  **Validate Real-time**: Kiểm tra xem thông báo lỗi có xuất hiện ngay khi đang gõ phím không.
2.  **Luồng Đăng nhập**: Xác nhận đăng nhập thành công sẽ chuyển hướng đúng về dashboard tương ứng (Admin/Staff/User).
3.  **Bảo mật**: Xác nhận tính năng chống Brute-force hoạt động như mong đợi.
