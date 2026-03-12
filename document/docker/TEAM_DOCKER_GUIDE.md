# Hướng dẫn Chạy Docker cho Dự án Sức Khỏe 24h

Tài liệu này hướng dẫn cách thiết lập môi trường chạy Docker để lập trình Local, kết nối trực tiếp với Database hiện tại của bạn.

## 🚀 Quy trình thiết lập

### 1. Cấu hình file .env
Mở file `.env` tại thư mục gốc của dự án và đảm bảo các thông số Database khớp với cấu hình sau (Để Docker có thể kết nối được với MySQL trên máy thật):

```env
DB_CONNECTION=mysql
DB_HOST=host.docker.internal
DB_PORT=3306
DB_DATABASE=suckhoe24h
DB_USERNAME=root
DB_PASSWORD=
```
*(Lưu ý: Nếu mật khẩu MySQL của bạn khác trống, hãy điền đúng mật khẩu vào mục `DB_PASSWORD`)*

---

### 2. Khởi động Docker
Mở PowerShell hoặc Command Prompt tại thư mục dự án và chạy:

```powershell
docker-compose up -d --build
```

---

### 3. Cài đặt các thư viện (Chỉ chạy lần đầu)
Chạy lệnh sau để Docker cài đặt đầy đủ các thư viện PHP cần thiết:

```powershell
docker-compose exec app composer install
```

---

### 4. Truy cập dự án
Sau khi Docker báo chạy thành công, bạn có thể truy cập web tại địa chỉ:
👉 **[http://localhost:8000](http://localhost:8000)**

---

## 🛠 Các lệnh thường dùng trong Docker

| Lệnh | Tác dụng |
| :--- | :--- |
| `docker-compose up -d` | Bật máy chủ ảo |
| `docker-compose stop` | Dừng máy chủ (không xóa container) |
| `docker-compose down` | Tắt và xóa container (giải phóng RAM) |
| `docker-compose logs -f app` | Xem log của ứng dụng Laravel |
| `docker-compose exec app php artisan ...` | Chạy các lệnh Artisan bên trong Docker |

## 💡 Lưu ý quan trọng
- **Database**: Docker đang dùng chung Database với Laragon và MySQL Workbench của bạn thông qua địa chỉ `host.docker.internal`. Mọi thay đổi dữ liệu ở Docker sẽ hiển thị ngay ở Workbench và ngược lại.
- **Frontend**: Bạn vẫn chạy `npm run dev` ở máy thật để có tốc độ tốt nhất.
