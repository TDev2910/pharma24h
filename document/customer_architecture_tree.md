# Kiến Trúc Thư Mục Hexagonal Architecture - Module Customer

Tài liệu này giải thích chi tiết chức năng và vai trò của từng thư mục, từng file dựa trên sơ đồ cây kiến trúc tiêu chuẩn.

## Sơ đồ cây (File Tree)

```text
app/
├── Core/
│   └── Customer/
│       ├── Domain/
│       │   └── DTOs/
│       │       └── CustomerData.php
│       ├── Ports/
│       │   ├── Inbound/
│       │   │   └── CustomerUseCaseInterface.php
│       │   └── Outbound/
│       │       └── CustomerRepositoryInterface.php
│       └── Application/
│           └── Services/ (Hoặc UseCases)
│               └── CustomerService.php (Implement Inbound Port, dựa vào Outbound Port)
├── Infrastructure/
│   └── Persistence/
│       └── Eloquent/
│           └── CustomerRepository.php (Implement Outbound Port)
└── Http/
    └── Controllers/
        ├── Admin/Customer/CustomerController.php
        └── Staff/StaffCustomerController.php
```

---

## 1. Tầng Core (Cốt Lõi Nghiệp Vụ)

**Vị trí:** `app/Core/Customer/`

Đây là "bộ não" của ứng dụng. Tầng này **TUYỆT ĐỐI KHÔNG** được phép phụ thuộc vào bất kỳ framework nào (như Laravel), không gọi trực tiếp Database hay HTTP Request. Nó chỉ chứa logic nghiệp vụ thuần túy của PHP.

### a. `Domain/DTOs/CustomerData.php`

- **Tên gọi:** DTO (Data Transfer Object) - Đối tượng truyền dữ liệu.
- **Vai trò:** Đóng vai trò như một "chiếc hộp" (hay bưu kiện) dùng để vận chuyển dữ liệu từ lớp ngoài cùng (Controller) vào lớp Core một cách an toàn.
- **Tại sao cần thiết:** Thay vì truyền nguyên object `$request` của Laravel vào Service chứa đầy thập cẩm thông tin (IP, Headers, Session...), ta chỉ nhét những dữ liệu CẦN THIẾT (name, email, phone...) vào trong `CustomerData`. Khi Service nhận được hộp giao hàng này, nó chỉ quan tâm đến các trường dữ liệu bên trong.

### b. `Ports/Inbound/CustomerUseCaseInterface.php`

- **Tên gọi:** Inbound Port (Cửa vào).
- **Vai trò:** Là một **"Bảng Hợp Đồng"** quy định những hành động gì (Use cases) mà các thiết bị ngoại vi/framework (như Controller, Console Command, API) CÓ THỂ yêu cầu hệ thống Core thực hiện.
- **Ví dụ:** Hàm `getDashboardData()`, `createCustomer()`. Đầu vào của các hàm này thường là các file DTO (như `CustomerData`).
- **Ý nghĩa:** Mọi controller muốn thao tác với Customer đều bắt buộc phải thông qua cánh cửa này. Controller không quan tâm bên trong làm gì, chỉ biết gọi hàm này là xong.

### c. `Ports/Outbound/CustomerRepositoryInterface.php`

- **Tên gọi:** Outbound Port (Cửa ra).
- **Vai trò:** Là một **"Bảng Hợp Đồng"** quy định những hành động mà Core CẦN Database làm dùm nó.
- **Ví dụ:** Hàm `getPaginatedCustomers()`, `countTotalCustomers()`, `create()`. Tại đây không có một chữ Eloquent, SQL hay MySQL nào cả. Nó chỉ đặt yêu cầu: "Tôi cần đếm tổng khách hàng, hãy đếm đi và trả về số nguyên vô đây".

### d. `Application/Services/CustomerService.php`

- **Tên gọi:** Application Service / Use Case Interactor.
- **Vai trò:** "Trái Tim" xử lý nghiệp vụ. Nơi bạn viết logic (if, else, for, tính toán).
- **Cách thức hoạt động:**
    - Nó **Implement** (Ký hợp đồng với) `CustomerUseCaseInterface` (Inbound) để tuyên bố rằng nó cung cấp các tính năng như `createCustomer`.
    - Để có thể tương tác với DB, nó mượn sức mạnh từ `CustomerRepositoryInterface` (Outbound) thông qua method DI (Dependency Injection) trong hàm `__construct()`. Nhờ đó, nó có thể gọi ra lệnh `create()` cho DB.
    - Xử lý các nghiệp vụ nhỏ lẻ. Ví dụ hàm format ảnh avatar `getAvatarUrl` để đảm bảo chuỗi xuất ra luôn là URL hiển thị mượt mà trên browser.

---

## 2. Tầng Infrastructure (Cơ Sở Hạ Tầng)

**Vị trí:** `app/Infrastructure/`

Tầng này chứa những thứ liên quan trực tiếp tới công nghệ cụ thể: Cơ sở dữ liệu (MySQL), Gửi Mail (SMTP), Redis, Framework (Laravel Cache).

### a. `Persistence/Eloquent/CustomerRepository.php`

- **Tên gọi:** Secondary Adapter / Driven Adapter (Bộ Chuyển Đổi Phụ).
- **Vai trò:** Nó **Implement** (Thực thi) bảng hợp đồng `CustomerRepositoryInterface` từ màng Outbound của Core.
- **Cách thức hoạt động:**
    - ĐÂY LÀ NƠI DUY NHẤT LÀM VIỆC VỚI ELOQUENT `User::query()`, `where()`, `create()`.
    - Nó nhận dữ liệu từ Core, và dịch sang ngôn ngữ của Laravel Eloquent để cất vào MySQL.
- **Lợi ích:** Đổi DB? Không thành vấn đề! Nếu bạn chuyển sang MongoDB, bạn chỉ tạo file `MongoCustomerRepository` và Implement cùng interface. Phần Controller và Service KHÔNg CẦN SỬA một dòng code!

---

## 3. Tầng Http (Tương Tác Với Người Dùng)

**Vị trí:** `app/Http/Controllers/`

### a. `Admin/Customer/CustomerController.php` & `Staff/StaffCustomerController.php`

- **Tên gọi:** Primary Adapter / Driving Adapter (Bộ Chuyển Đổi Chính).
- **Vai trò:** Quản lý giao thức HTTP (Request & Response) của Laravel.
- **Cách thức hoạt động:**
    - Nhận `$request` (có chứa Form data).
    - Validation dữ liệu `$request`.
    - Móc các tham số ra, "đóng gói" vào chiếc túi DTO (`CustomerData`).
    - Đẩy chiếc túi chạy qua cánh cửa Inbound (`CustomerUseCaseInterface`). Service sẽ ẩn danh phía sau cánh cửa này để xử lý.
    - Lấy kết quả mà Service đẩy ngược ra, render lên JSON API hoặc trang Inertia.
- **Lợi ích tiêu biểu:** Cả Admin và Staff tuy là 2 giao diện khác nhau nhưng chỉ cần duy nhất 1 lớp mỏng logic để gọi đúng tài nguyên, xóa bỏ hoàn toàn code rác `User::where()` trùng lặp giống ngày xưa. Tái sử dụng logic nghiệp vụ 100%.
