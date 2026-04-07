# 🛡️ Tài liệu Xử lý Triệt để Lỗi Nạp chồng Giao diện (Layout Duplication) - Pharma24h

## 1. Tóm tắt Vấn đề (The Problem)
Trang web Pharma24h gặp lỗi nghiêm trọng khi điều hướng (SPA Navigation):
- **Hiện tượng**: Header và Footer bị nạp lặp lại (2-3 cái lồng nhau như búp bê Nga).
- **Phạm vi**: Xảy ra chủ yếu trên trang Home, Login và khi nhấn vào Logo hoặc Mobile Menu.
- **Hệ quả**: Trang web bị chậm, hiển thị sai lệch giao diện và gây khó chịu cho người dùng.

---

## 2. "Tứ đại hung thủ" gây lỗi (Core Causes)

### 🕵️‍♂️ Nguyên nhân 1: Hàm Resolve nạp trang không chuẩn
Việc nạp Component bằng tay (`manual async resolve`) trong `app.js` khiến **Vite** và **Inertia** thỉnh thoảng bị nạp lỡ cỡ hoặc nạp lặp trước khi xác định được Layout.

### 🕵️‍♂️ Nguyên nhân 2: Gán Layout sai đối tượng
Trước đây chúng ta gán trực tiếp vào `page.layout`. Trong Vue 3 + Vite, đối tượng render thực tế nằm trong `page.default`. Việc gán sai chỗ khiến Inertia không nhận diện được đã có Layout rồi, dẫn đến việc bọc thêm một cái nữa.

### 🕵️‍♂️ Nguyên nhân 3: Xung đột từ Legacy JS & Blade Wrapper
File `app.blade.php` cũ chứa thẻ `<main class="main-content">` bọc ngoài `@inertia` và nạp các file JS cũ (`cart.js`, `user.js`). Những file này nhảy vào can thiệp DOM khiến Vue bị "loạn nhịp".

### 🕵️‍♂️ Nguyên nhân 4: "Bóng ma" Cache (Server & CDN)
Cloudflare và cPanel lưu lại các bản HTML/JS lỗi từ trước đó. Khi bạn nạp code mới, trình duyệt vẫn cố nạp code cũ gây ra sự lệch pha (Sync Mismatch).

---

## 3. Giải pháp "Khóa vạn năng" (The Final Fix)

### ✅ Bước 1: Chuẩn hóa `app.js` (Architecture Standard)
Đưa `app.js` về chuẩn công nghiệp của Laravel Inertia:
- Sử dụng hàm chính chủ **`resolvePageComponent`** từ `laravel-vite-plugin`.
- Gán Layout vào **`page.default.layout`** để khóa chặt cơ chế nạp lặp.
- Sử dụng `window.scrollTo` với `behavior: 'instant'` để mượt mà hơn.

### ✅ Bước 2: Dọn sạch "Bãi rác" Blade
Loại bỏ hoàn toàn wrapper `<main>` và các file JS legacy (`cart.js`, `user.js`) trong `app.blade.php`. Chỉ để lại một điểm mount `@inertia` duy nhất và sạch sẽ.

### ✅ Bước 3: Đồng bộ hóa Link & Interceptor
- Thay đổi Logo và Menu Mobile về thẻ `<a>` phối hợp với thuộc tính `data-inertia`.
- Thêm **Bộ đánh chặn Click (Global Interceptor)** vào `app.js` để biến các thẻ `<a>` này thành SPA navigation mượt mà nhưng ổn định hơn thẻ `<Link>` truyền thống trên Mobile.

---

## 4. Quy trình Triển khai (Deployment Checklist)

Mỗi khi cập nhật giao diện, hãy thực hiện ĐÚNG và ĐỦ các bước sau:

1. **Local Build**:
   ```bash
   npm run build
   ```
2. **Git Commit & Push**:
   ```bash
   git add .
   git commit -m "Standardize architecture"
   git push origin main
   ```
3. **Server Update (cPanel Terminal)**:
   ```bash
   git pull origin main
   php artisan optimize:clear
   php artisan view:clear
   ```
4. **Cache Purge (BẮT BUỘC)**:
   - **Cloudflare**: Truy cập Dashboard -> Caching -> **Purge Everything**.
   - **Trình duyệt**: Mở Tab ẩn danh (Incognito) hoặc xóa **Storage Data** trong F12 Application.

---

## 💡 Bài học rút ra
Để một hệ thống SPA (Inertia) chạy ổn định:
- Luôn tuân theo **Official Standard** (Chuẩn chính thức) thay vì code thủ công.
- Giữ cho file **Blade** khởi đầu thật sạch sẽ.
- **Xóa Cache** là thói quen sống còn để tiêu diệt các "bóng ma" lỗi cũ.

**Pharma24h - Hoạt động ổn định, mượt mà và chuyên nghiệp!**
