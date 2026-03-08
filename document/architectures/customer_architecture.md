            # Hướng dẫn chi tiết Kiến trúc Hexagonal (Ports & Adapters) - Module Customer

Tài liệu này giải thích chi tiết vai trò của từng thư mục, từng file và luồng dữ liệu của kiến trúc Hexagonal vừa được áp dụng cho module Customer.

---

## 1. Bức tranh tổng thể (Luồng đi của dữ liệu)

Khi người dùng thao tác trên giao diện (ví dụ: nhấn "Lưu Khách Hàng", dữ liệu chạy qua các lớp sau:

1. **Giao diện (Vue/Inertia)** gửi HTTP Request chứa dữ liệu (tên, email, sđt) xuống backend.
2. **Controller (Adapter Chính)** tiếp nhận Request. Thay vì ghi thẳng vào DB, nó gói dữ liệu này thành một chiếc túi (gọi là **DTO**).
3. Controller gọi một **Interface (Inbound Port)** để yêu cầu **Service (Core)** xử lý cái túi DTO kia.
4. **Service (Core)** nhận DTO, kiểm tra logic nghiệp vụ (VD: định dạng sđt, ghép chuỗi địa chỉ). Core hoàn toàn **MÙ**, nó không biết Laravel là gì, không biết MySQL là gì.
5. Khi xử lý xong, Core muốn lưu vào DB. Nó lại gọi một **Interface (Outbound Port)** để ra lệnh "Ê, lưu cho tao dòng dữ liệu này".
6. **Repository (Adapter Phụ)** lắng nghe lệnh từ Outbound Port, và bắt đầu dùng `Eloquent (User::create)` để ghi thật vào MySQL.

=> **Quy tắc vàng:** Các lớp bên ngoài (Controller, Repository/Eloquent) phải phụ thuộc vào lớp bên trong (Core). Lớp Core (Service, DTO) không bao giờ được phép gọi ngược ra ngoài (Không được `use Illuminate\Http\Request`, không được `use App\Models\User` bên trong thư mục `Domain` hoặc `Ports`).

---

## 2. Giải thích chi tiết từng Thư mục & File

### A. Tầng `Core` (Trái tim của hệ thống)

_Thư mục: `app/Core/Customer/`_

Tầng này chứa "Luật chơi" của doanh nghiệp. Nó phải thuần PHP nhất có thể, không dính líu bám rễ vào bất kì framework nào.

#### `Domain/CustomerData.php` (DTO - Data Transfer Object)

- **Vai trò:** Là một "cấu trúc dữ liệu" thuần túy.
- **Tại sao cần nó?** Trong Controller cũ của bạn, dữ liệu nằm trong `$request->name`, `$request->email`. Nếu truyền thẳng `$request` vào Service, thì Service bị bám dính vào cái Request của Laravel. DTO sinh ra để giải quyết việc này. Nó chỉ là một object chứa các thuộc tính (name, email). Bạn lấy dữ liệu từ Request đập vào DTO, rồi mới ném DTO vào Service.

#### `Ports/` (Các Cánh Cửa - Interfaces)

Ports định nghĩa **"Những gì có thể làm"** chứ không định nghĩa **"Làm như thế nào"**.

1. **`Inbound/CustomerUseCaseInterface.php` (Cửa vào):**
    - **Vai trò:** Bản hợp đồng quy định Controller được phép gọi những hàm gì của Core. (VD: `createCustomer`, `updateCustomer`).
    - Giúp Controller chỉ nhìn thấy Interface, Controller không biết bên trong Core xử lý ra sao.

2. **`Outbound/CustomerRepositoryInterface.php` (Cửa ra):**
    - **Vai trò:** Bản hợp đồng quy định Core muốn thao tác những gì với Database (VD: `create()`, `countActiveCustomers()`).
    - Tại đây bạn **KHÔNG THẤY** chữ `Eloquent` hay `where()`, `get()`. Bạn chỉ định nghĩa hàm lấy dữ liệu là gì.

#### `Application/Services/CustomerService.php` (Luật chơi)

- **Vai trò:** Nơi chứa **Logic Nghiệp Vụ** (Business Logic).
- **Hành động:**
    - Nó `implements CustomerUseCaseInterface` (Xác nhận nó sẽ nhận lệnh từ màng Controller).
    - Nó được tiêm (inject) `CustomerRepositoryInterface` qua file `__construct`. Nó dùng interface này để gọi DB.
    - Vị trí ở hàm `getDashboardData`: Có bước lấy collection về, map lại thành array chuẩn tắc và gọi `getAvatarUrl` để biến đường dẫn ảo thành link thật.

---

### B. Tầng `Infrastructure` (Cơ sở hạ tầng)

_Thư mục: `app/Infrastructure/Persistence/`_

Tầng này chứa "đồ công nghệ": Kết nối MySQL, Eloquent, thao tác với File system, gửi Email bằng Mailgun, Redis.

#### `Eloquent/CustomerRepository.php` (Adapter Phụ)

- **Vai trò:** Người thực thi bản hợp đồng của `CustomerRepositoryInterface`.
- **Hành động:**
    - Là nơi **DUY NHẤT** được phép gọi `App\Models\User` và các hàm `where`, `find()`, `create()` của Laravel Eloquent.
    - Phục vụ tận tâm mọi yêu cầu từ thằng `CustomerService` đưa xuống.
- **Lợi ích:** Ngày mai sếp bảo: "Đổi DB từ MySQL sang MongoDB!", bạn chỉ việc tạo 1 file `MongoCustomerRepository` mới. Các file khác trong `Core` không cần sửa 1 dòng nào!

---

### C. Tầng Http (Giao tiếp Web)

_Thư mục: `app/Http/Controllers/`_

#### `Admin/Customer/CustomerController.php` & `Staff/StaffCustomerController.php`

- **Vai trò:** Giao tiếp với Browser / Inertia.js. Là người môi giới (Adapter Chính).
- **Hành động:**
    - Khai báo inject `CustomerUseCaseInterface` ở hàm `__construct`. Nhờ `AppServiceProvider` bóp méo, nó thực chất sẽ luôn nhận vào một instance của lớp `CustomerService`.
    - Validate dữ liệu đầu vào `$request`.
    - Trích xuất tham số từ `$request`, đẩy vào DTO (`CustomerData`).
    - Giao việc cho Service (gọi `this->useCase`).
    - Lấy kết quả trả ra JSON hoặc Inertia Render. Không xử lý bất kì số liệu tính toán hay format nào ở đây cả.

---

### D. Keo dán các lớp

#### `app/Providers/AppServiceProvider.php`

- **Vai trò:** Nơi khai báo luật kết dính cho Laravel (Dependency Injection - DI).
- **Hành động:**
    - `bind(CustomerRepositoryInterface::class, CustomerRepository::class)`: Dặn Laravel rằng: "Bất kì ai cần Interface này, hãy đưa cho họ cái file Eloquent kia".
    - Nhờ file này, hệ thống biết tự động liên kết Interface giả với class có code thật khi chạy lệnh.
