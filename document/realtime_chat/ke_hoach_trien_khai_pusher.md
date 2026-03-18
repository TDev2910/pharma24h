# Kế hoạch triển khai: Chat Real-time với Pusher

Tài liệu này hướng dẫn cách tích hợp Pusher để nhắn tin thời gian thực giữa nhân viên và khách hàng trong ứng dụng Laravel 11 và Vue.js.

## Yêu cầu xem xét từ người dùng

> [!IMPORTANT]
> **Tài khoản Pusher**: Bạn cần đăng ký tại [Pusher.com](https://pusher.com/), tạo một ứng dụng "Channels" và lấy các thông tin: `APP_ID`, `APP_KEY`, `APP_SECRET`, và `APP_CLUSTER`. Hãy chọn cluster gần khu vực của bạn (ví dụ: `ap1` cho Singapore/Việt Nam).

## Các thay đổi đề xuất

---

### Giai đoạn 1: Cấu hình Backend & Cơ sở dữ liệu

#### 1. Cải tiến Database Schema
Chúng ta cần các bảng để quản lý phiên chat (sessions) và tin nhắn (messages).

**Bảng `chat_sessions`:**
- `id`: UUID (Primary Key)
- `user_id`: BigInt, Nullable (Liên kết với bảng `users` nếu là Member)
- `customer_name`: String (Tên khách hàng)
- `customer_email`: String (Email khách hàng)
- `type`: Enum (`guest`, `member`)
- `session_token`: String, Nullable (Định danh Guest qua Cookie)
- `timestamps`

**Bảng `chat_messages`:**
- `id`: BigInt
- `chat_session_id`: UUID (Khóa ngoại)
- `sender_type`: Enum (`staff`, `customer`)
- `content`: Text
- `is_read`: Boolean
- `timestamps`

#### 2. Cài đặt Pusher Server SDK
Chạy lệnh sau để cài đặt PHP SDK cần thiết:
```bash
composer require pusher/pusher-php-server
```

#### 3. Tạo DTO `MessageData.php`
Lưu tại: `app/Core/Chat/Domain/MessageData.php`
```php
<?php

namespace App\Core\Chat\Domain;

readonly class MessageData
{
    public function __construct(
        public string $sessionId,
        public string $senderType, // 'customer' hoặc 'staff'
        public string $content,
        public string $customerType, // 'guest' hoặc 'member'
        public ?int $userId = null,   // ID người dùng nếu là member
        public ?string $senderName = null
    ) {}

    public function isPersistent(): bool
    {
        return $this->customerType === 'member';
    }
}
```

#### 4. Cấu hình môi trường (`.env`)
Cập nhật driver broadcasting và thêm thông tin tài khoản Pusher của bạn:
```env
BROADCAST_CONNECTION=pusher

PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_cluster
```

#### 3. Tạo Event Broadcasting
Tạo một event mới cho tin nhắn chat:
```bash
php artisan make:event MessageSent
```
Event này phải `implements ShouldBroadcast`.

#### 6. Định nghĩa Private Channels
Bảo mật phòng chat bằng cách định nghĩa channel riêng tư trong `routes/channels.php`:
```php
Broadcast::channel('chat.{sessionId}', function ($user, $sessionId) {
    // Logic: Nhân viên có thể vào mọi channel, khách hàng chỉ vào đúng sessionId của họ
    return $user->hasRole('staff') || $user->ownsChatSession($sessionId);
});
```

#### 7. Luồng xử lý (Workflow Logic)
- **Member**: Khi mở Chat, kiểm tra `Auth::check()`. Nếu có, tự động lấy `user_id` và `chat_session` cũ.
- **Guest**: Yêu cầu nhập Form. Sinh `session_token` lưu vào Cookie.
- **Tự động dọn dẹp (Cleanup)**: Chạy cronjob hàng ngày để xóa Guest sessions cũ:
```php
// Console/Kernel.php
$schedule->command('chat:cleanup-guests')->daily();
```

---

### Giai đoạn 2: Cấu hình Frontend (Vue.js & Laravel Echo)

#### 1. Cài đặt Echo và Pusher-JS
Chạy lệnh sau trong thư mục dự án:
```bash
npm install --save-dev laravel-echo pusher-js
```

#### 2. Khởi tạo Echo (`resources/js/bootstrap.js`)
Thêm hoặc bỏ comment cấu hình Echo:
```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});
```

#### 3. Lắng nghe Event trong Vue
Ví dụ cách lắng nghe trong một Vue component:
```javascript
window.Echo.private(`chat.${userId}`)
    .listen('MessageSent', (e) => {
        console.log(e.message);
        this.messages.push(e.message);
    });
```

## Kế hoạch kiểm tra (Verification)

### Kiểm tra tự động
- Tạo một feature test để đảm bảo event được broadcast khi gửi tin nhắn qua API.
- Sử dụng `Event::fake()` để xác nhận việc truyền tin.

### Kiểm tra thủ công
- Mở hai trình duyệt khác nhau (một cho nhân viên, một cho khách hàng).
- Sử dụng **Pusher Debug Console** để theo dõi các tin nhắn được gửi đi theo thời gian thực.
- Xác nhận tin nhắn xuất hiện trên màn hình mà không cần tải lại trang.
