# Kiến trúc hệ thống thanh toán (Payment Architecture)

## Tổng quan
Tài liệu này mô tả kiến trúc được đề xuất cho hệ thống thanh toán của ứng dụng SucKhoe24h, tập trung vào việc tách biệt logic thanh toán khỏi quy trình checkout để dễ bảo trì và mở rộng.

## Cấu trúc thư mục

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Store/
│   │       ├── CheckoutController.php    # Xử lý checkout cơ bản
│   │       └── PaymentController.php     # Xử lý thanh toán VNPAY
│   └── Requests/
│       └── PaymentRequest.php            # Validation cho request thanh toán
├── Services/
│   ├── CheckoutService.php               # Logic checkout cơ bản
│   └── Payment/
│       ├── PaymentServiceInterface.php   # Interface cho các phương thức thanh toán
│       ├── VNPayService.php              # Xử lý logic VNPAY
│       └── CODService.php                # Xử lý logic thanh toán khi nhận hàng
├── Models/
│   └── Payment.php                       # Model lưu thông tin thanh toán (nếu cần)
└── config/
    └── payment.php                       # Cấu hình các cổng thanh toán
```

## Luồng xử lý thanh toán

1. **Tạo đơn hàng trong CheckoutController**
   - Người dùng điền form checkout và chọn phương thức thanh toán
   - CheckoutController gọi CheckoutService để tạo đơn hàng
   - Nếu chọn VNPAY, chuyển hướng đến PaymentController

2. **Xử lý thanh toán trong PaymentController**
   - PaymentController nhận order_id và gọi VNPayService
   - VNPayService tạo URL thanh toán và chuyển hướng người dùng đến cổng VNPAY
   - Sau khi thanh toán, VNPAY chuyển hướng về endpoint return trong PaymentController
   - PaymentController gọi VNPayService để xử lý kết quả thanh toán

3. **Callback và IPN**
   - VNPAY gửi IPN (Instant Payment Notification) đến endpoint webhook
   - PaymentController xử lý IPN và cập nhật trạng thái đơn hàng

## Ưu điểm của kiến trúc

1. **Tách biệt quan tâm (Separation of Concerns)**
   - CheckoutController: Xử lý form và quy trình đặt hàng
   - PaymentController: Chỉ tập trung vào xử lý thanh toán
   - VNPayService: Encapsulate toàn bộ logic giao tiếp với VNPAY

2. **Dễ mở rộng**
   - Để thêm phương thức thanh toán mới (MoMo, ZaloPay...), chỉ cần:
     - Tạo service mới implements PaymentServiceInterface
     - Thêm controller method và routes tương ứng

3. **Dễ bảo trì và test**
   - Mỗi thành phần có trách nhiệm riêng biệt, dễ dàng unit test
   - Logic thanh toán được đóng gói, dễ debug và sửa lỗi

4. **Cấu hình tập trung**
   - Tất cả cấu hình thanh toán được tập trung trong file config/payment.php
   - Dễ dàng thay đổi cấu hình giữa môi trường test và production

## Triển khai

### Routes

```php
// Checkout routes
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/failed', [CheckoutController::class, 'failed'])->name('checkout.failed');

// Payment routes
Route::prefix('payment')->name('payment.')->group(function () {
    Route::prefix('vnpay')->name('vnpay.')->group(function () {
        Route::get('/checkout/{order_id}', [PaymentController::class, 'vnpayCheckout'])->name('checkout');
        Route::get('/return', [PaymentController::class, 'vnpayReturn'])->name('return');
        Route::post('/ipn', [PaymentController::class, 'vnpayIpn'])->name('ipn');
    });
    
    // Chuẩn bị cho các cổng thanh toán khác trong tương lai
    // Route::prefix('momo')->name('momo.')->group(function () { ... });
});
```

### Cấu hình

```php
// .env
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
VNPAY_TMN_CODE=DEMO
VNPAY_HASH_SECRET=FVEHQVMRRZSUMIMZMGTVRBSMPTGNGPPO

// config/payment.php
return [
    'vnpay' => [
        'url' => env('VNPAY_URL'),
        'tmn_code' => env('VNPAY_TMN_CODE'),
        'hash_secret' => env('VNPAY_HASH_SECRET'),
    ],
    // Các cổng thanh toán khác
];
```

## Lưu ý khi triển khai

1. **Bảo mật**
   - Luôn xác thực chữ ký trong callback
   - Lưu log đầy đủ các giao dịch thanh toán
   - Xử lý các trường hợp timeout, giao dịch trùng lặp

2. **Xử lý lỗi**
   - Cần có cơ chế xử lý lỗi kết nối đến cổng thanh toán
   - Có flow để người dùng thử lại thanh toán khi thất bại

3. **Môi trường test**
   - Sử dụng sandbox của VNPAY cho môi trường development và testing
   - Chuyển sang môi trường production chỉ khi đã test kỹ

## Kết luận

Kiến trúc này giúp tách biệt logic thanh toán khỏi quy trình checkout, giúp code dễ bảo trì và mở rộng. Khi cần thêm các phương thức thanh toán mới, chỉ cần triển khai interface PaymentServiceInterface và thêm các routes tương ứng mà không ảnh hưởng đến code hiện có.
