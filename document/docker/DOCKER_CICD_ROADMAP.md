# Lộ trình Triển khai Docker & CI/CD cho Dự án Sức Khỏe 24h

Tài liệu này vạch ra quy trình chuyển đổi chuyên nghiệp từ môi trường Laragon sang Docker và thiết lập hệ thống CI/CD tự động cho hosting cPanel hiện tại.

## 📋 Mục tiêu dự án
1. **Chuẩn hóa Local**: Loại bỏ Laragon, dùng Docker để team code đồng bộ 100%.
2. **Kiểm soát Chất lượng (CI)**: Tự động chạy Test và Build Assets trên GitHub.
3. **Triển khai An toàn (CD)**: Tự động hóa quy trình đẩy code lên cPanel mà không gây hỏng hệ thống đang chạy.

---

## 🗺️ Lộ trình triển khai (Roadmap)

### 🟢 Giai đoạn 1: Docker hóa Dự án (Phát triển Local)
*Mục tiêu: Xây dựng nền tảng máy chủ ảo tại máy cá nhân.*
- **Khởi tạo Docker**: Tạo `Dockerfile` tối ưu cho PHP 8.2 và các thư viện Firebase, Gemini API.
- **Dàn dựng Service**: Thiết lập `docker-compose.yml` gồm PHP-FPM, Nginx, MySQL (Port 3307 để tránh trùng Laragon).
- **Phân quyền & Tối ưu**: Xử lý quyền ghi file cho `storage` và `cache` trong môi trường Docker.

### 🟡 Giai đoạn 2: GitHub Actions CI (Tự động hóa kiểm tra)
*Mục tiêu: Đảm bảo code "sạch" trước khi được phép đưa lên server.*
- **Linting & Testing**: Tự động kiểm tra lỗi cú pháp và chạy Unit Test của Laravel.
- **Build Assets (Quan trọng)**: GitHub sẽ tự chạy `npm run build` để tạo ra các file giao diện đã nén (Vite), giúp giảm tải cho server cPanel.
- **Docker Build Check**: Thử nghiệm build Docker Image ngay trên GitHub để đảm bảo cấu hình luôn đúng.

### 🔴 Giai đoạn 3: Continuous Deployment cho Hosting cPanel
*Mục tiêu: Thay thế việc Pull Git thủ công bằng quy trình tự động, chuyên nghiệp.*
- **Lựa chọn Phương án**: 
    - *Ưu tiên:* Sử dụng GitHub Actions tự động đẩy code đã build qua **SSH/SSH Keys** hoặc **FTP Deploy**.
    - *Phụ trợ:* Tinh chỉnh file `.cpanel.yml` nếu tiếp tục dùng tính năng Git của cPanel để tự động hóa các lệnh sau khi pull.
- **Post-Deploy**: Tự động hóa việc chạy `php artisan migrate` và `php artisan optimize` sau khi code được đẩy lên thành công.
- **Môi trường Stagging**: Thiết lập triển khai thử nghiệm lên một thư mục phụ (dev.suckhoe24h.com) trước khi đẩy lên domain chính.

---

## 🛠️ Quy tắc An toàn (Golden Rules)
1. **Tách biệt .env**: Tuyệt đối không để mật khẩu production trên GitHub. Sử dụng **GitHub Secrets** để lưu trữ các thông tin này.
2. **Không ghi đè Database**: Quy trình migration trên production phải được kiểm soát chặt chẽ, không chạy tự động lệnh `migrate:fresh`.
3. **Rollback nhanh**: Luôn duy trì lịch sử các bản build trên GitHub để có thể quay về phiên bản cũ ngay lập tức nếu có lỗi.

---

## 📖 Hướng dẫn cho Thành viên Team
*(Phần này sẽ được cập nhật chi tiết sau khi hoàn thành Giai đoạn 1)*
- **Lệnh bật máy chủ**: `docker-compose up -d --build`
- **Lệnh cập nhật**: `git pull` -> `docker-compose exec app composer install`
- **Địa chỉ truy cập**: `http://localhost:8000` (Local)
