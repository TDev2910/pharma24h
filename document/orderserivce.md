# Luồng Đặt Lịch Dịch Vụ (Thanh Toán Tại Quầy)

Đây là tài liệu mô tả luồng nghiệp vụ đầy đủ cho chức năng đặt lịch dịch vụ và thanh toán tại nhà thuốc "PCT Pharma".

## 1. Bảng Dữ Liệu Chính

Bảng `service_bookings` (Các lượt đặt lịch dịch vụ)

| Tên cột | Kiểu dữ liệu | Chú thích |
| :--- | :--- | :--- |
| `id` | bigInteger (PK) | Khóa chính |
| `service_id` | foreignId | Khóa ngoại đến bảng `services` |
| `user_id` | foreignId (nullable) | Khóa ngoại đến bảng `users` (nếu khách đã đăng nhập) |
| `customer_name` | varchar | Tên khách hàng (bắt buộc) |
| `customer_phone` | varchar | SĐT khách hàng (bắt buộc, dùng để liên hệ) |
| `customer_email` | varchar (nullable) | Email khách hàng |
| `booking_date` | date | Ngày khách hẹn đến |
| `booking_time` | time | Giờ khách hẹn đến |
| `price` | decimal / int | Giá dịch vụ tại thời điểm đặt |
| `payment_method` | varchar | Phương thức thanh toán (Giá trị: `pay_at_pharmacy`) |
| `payment_status` | varchar / enum | Trạng thái thanh toán (Mặc định: `unpaid`) |
| `status` | varchar / enum | Trạng thái lịch hẹn (Mặc định: `pending`) |
| `notes` | text (nullable) | Ghi chú của khách hàng |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

---

## 2. Luồng Nghiệp Vụ Chi Tiết

### Giai đoạn 1: Khách hàng đặt lịch (Online)

1.  **Khách hàng** truy cập trang chi tiết dịch vụ (ví dụ: "Tiêm vacxin Covid-19").
2.  **Khách hàng** nhấn vào nút "Đặt dịch vụ ngay".
3.  **Hệ thống (Frontend)** hiển thị một Form Modal yêu cầu nhập thông tin:
    * Họ và tên (bắt buộc)
    * Số điện thoại (bắt buộc)
    * Email (không bắt buộc)
    * Ngày đặt lịch (bắt buộc - Dùng Date Picker)
    * Giờ đặt lịch (bắt buộc - Dùng Time Picker hoặc danh sách khung giờ)
    * Ghi chú (không bắt buộc)
4.  **Khách hàng** nhấn nút "Xác nhận đặt lịch" (Submit form).
5.  **Hệ thống (Backend - Laravel)** nhận request:
    * Validate (kiểm tra) dữ liệu đầu vào.
    * Tạo một bản ghi mới trong bảng `service_bookings` với các giá trị:
        * `service_id`: (ID của dịch vụ đang xem)
        * `customer_name`, `customer_phone`, `customer_email`, `booking_date`, `booking_time`, `notes`: (Lấy từ form)
        * `price`: (Lấy giá từ bảng `services`)
        * `payment_method`: Gán cứng giá trị `'pay_at_pharmacy'`
        * `payment_status`: Gán cứng giá trị `'unpaid'`
        * `status`: Gán cứng giá trị `'pending'`
6.  **Hệ thống (Frontend)**:
    * Đóng Modal.
    * Hiển thị thông báo thành công: "Đặt lịch thành công! Nhân viên nhà thuốc sẽ gọi điện cho bạn để xác nhận lịch hẹn. Vui lòng thanh toán tại quầy khi đến."

### Giai đoạn 2: Nhân viên xác nhận (Admin Panel)

*Đây là bước nghiệp vụ quan trọng để giảm tỷ lệ "hẹn ảo".*

1.  **Nhân viên** (Admin/Quản lý) đăng nhập vào trang quản trị.
2.  **Nhân viên** thấy một lịch hẹn mới với `status` là `'pending'`.
3.  **Nhân viên** gọi điện cho khách hàng qua `customer_phone` để xác nhận lại (thông tin dịch vụ, ngày, giờ).
4.  Sau khi xác nhận qua điện thoại, **Nhân viên** cập nhật trạng thái lịch hẹn trong hệ thống:
    * `status`: `'pending'` $\rightarrow$ `'confirmed'` (Đã xác nhận)
    * *(Nếu khách hàng hủy qua điện thoại)*: `status`: `'pending'` $\rightarrow$ `'cancelled'` (Đã hủy)

### Giai đoạn 3: Khách hàng đến nhà thuốc (Thanh toán & Dùng dịch vụ)

1.  **Khách hàng** đến nhà thuốc vào đúng `booking_date` và `booking_time`.
2.  **Khách hàng** đến quầy thu ngân, báo Tên hoặc SĐT.
3.  **Nhân viên** (Thu ngân) tìm lịch hẹn (booking) của khách trong admin panel (lọc theo SĐT hoặc ngày, trạng thái là `'confirmed'`).
4.  **Nhân viên** xác nhận đúng dịch vụ và số tiền.
5.  **Khách hàng** thực hiện thanh toán (tiền mặt, thẻ, v.v.).
6.  Sau khi nhận tiền thành công, **Nhân viên** (Thu ngân) cập nhật hệ thống:
    * `payment_status`: `'unpaid'` $\rightarrow$ `'paid'` (Đã thanh toán)
7.  **Nhân viên** hướng dẫn khách hàng di chuyển đến khu vực/phòng để thực hiện dịch vụ (ví dụ: phòng tiêm).

### Giai đoạn 4: Hoàn thành dịch vụ

1.  **Khách hàng** được nhân viên y tế thực hiện dịch vụ (ví dụ: tiêm).
2.  Sau khi dịch vụ hoàn tất, **Nhân viên** (Y tế hoặc Thu ngân) cập nhật lần cuối vào hệ thống:
    * `status`: `'confirmed'` $\rightarrow$ `'completed'` (Đã hoàn thành)

Luồng kết thúc.

---

## 3. Tóm Tắt Các Trạng Thái

Quản lý lịch hẹn chủ yếu dựa vào 2 cột trạng thái:

#### `status` (Trạng thái lịch hẹn)
* `pending`: Mới đặt, chờ nhân viên gọi điện xác nhận.
* `confirmed`: Đã xác nhận, chờ khách đến.
* `completed`: Khách đã đến và hoàn thành dịch vụ.
* `cancelled`: Khách hủy (qua điện thoại hoặc không đến).

#### `payment_status` (Trạng thái thanh toán)
* `unpaid`: Chưa thanh toán (mặc định khi mới đặt).
* `paid`: Đã thanh toán tại quầy.