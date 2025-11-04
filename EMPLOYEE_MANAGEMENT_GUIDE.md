# 📚 HƯỚNG DẪN SỬ DỤNG CHỨC NĂNG QUẢN LÝ NHÂN VIÊN

## ✅ Tổng Quan Triển Khai

Chức năng **Quản lý Nhân viên** đã được triển khai hoàn chỉnh với đầy đủ các tính năng:

### 🎯 Các Tính Năng Đã Triển Khai

1. ✅ **Quản lý thông tin nhân viên**
   - Thêm, sửa, xóa nhân viên
   - Tìm kiếm và lọc nhân viên
   - Phân quyền: Admin/Staff/User

2. ✅ **Quản lý lương & phụ cấp**
   - Lương cố định hoặc theo giờ
   - Phụ cấp động (theo ngày/tháng/% lương)
   - Thưởng chỉ tiêu (cố định hoặc theo %)
   - Giảm trừ (một lần hoặc hàng tháng)

3. ✅ **Lập lịch làm việc**
   - Quản lý ca làm việc
   - Phân công ca cho nhân viên
   - Chống trùng lặp ca

---

## 📁 Cấu Trúc Files Đã Tạo

### 🗄️ Database (Migrations & Models)

#### Migrations (8 files)
```
database/migrations/
├── 2025_11_04_173512_create_departments_table.php
├── 2025_11_04_173513_create_branches_table.php
├── 2025_11_04_173514_create_employees_table.php
├── 2025_11_04_173515_create_employee_allowances_table.php
├── 2025_11_04_173516_create_employee_targets_table.php
├── 2025_11_04_173517_create_employee_deductions_table.php
├── 2025_11_04_173518_create_shifts_table.php
└── 2025_11_04_173519_create_employee_schedules_table.php
```

#### Models (8 files)
```
app/Models/
├── Department.php
├── Branch.php
├── Employee.php
├── EmployeeAllowance.php
├── EmployeeTarget.php
├── EmployeeDeduction.php
├── Shift.php
└── EmployeeSchedule.php
```

### 🔧 Backend (Services, Controllers, Requests)

#### Service Layer
```
app/Services/
└── EmployeeService.php (Xử lý logic với Transaction)
```

#### Controllers (3 files)
```
app/Http/Controllers/Admin/Employee/
├── EmployeeController.php
├── ScheduleController.php
└── ShiftController.php
```

#### Request Validation (4 files)
```
app/Http/Requests/
├── StoreEmployeeRequest.php
├── UpdateEmployeeRequest.php
├── StoreScheduleRequest.php
└── StoreShiftRequest.php
```

### 🎨 Frontend (Vue Components)

```
resources/js/Pages/Admin/Employees/
├── Index.vue (Danh sách nhân viên)
└── Modals/
    ├── Create.vue (Form thêm nhân viên - 2 tabs)
    ├── Edit.vue (Form sửa nhân viên)
    └── Schedule.vue (Form lập lịch làm việc)
```

### 🛣️ Routes

Routes đã được thêm vào `routes/admin.php`:

```php
// Employees
admin/employees/*
admin/employees/api
admin/employees/generate/code
admin/employees/resources/data

// Schedules
admin/employee-schedules/*
admin/employee-schedules/api

// Shifts
admin/shifts/*
admin/shifts/api
```

---

## 🚀 Hướng Dẫn Cài Đặt

### Bước 1: Chạy Migrations

```bash
php artisan migrate
```

### Bước 2: Tạo Dữ Liệu Mẫu (Optional)

Tạo một số phòng ban, chức vụ, chi nhánh mẫu:

```php
// Trong tinker hoặc seeder
php artisan tinker

// Tạo phòng ban
\App\Models\Department::create(['name' => 'Kinh doanh', 'description' => 'Phòng kinh doanh']);
\App\Models\Department::create(['name' => 'Kỹ thuật', 'description' => 'Phòng kỹ thuật']);
\App\Models\Department::create(['name' => 'Hành chính', 'description' => 'Phòng hành chính']);

// Tạo chi nhánh
\App\Models\Branch::create(['name' => 'Chi nhánh Hà Nội', 'address' => 'Hà Nội']);
\App\Models\Branch::create(['name' => 'Chi nhánh Hồ Chí Minh', 'address' => 'TP.HCM']);

// Tạo ca làm việc
\App\Models\Shift::create(['name' => 'Ca sáng', 'start_time' => '08:00', 'end_time' => '12:00']);
\App\Models\Shift::create(['name' => 'Ca chiều', 'start_time' => '13:00', 'end_time' => '17:00']);
\App\Models\Shift::create(['name' => 'Ca tối', 'start_time' => '18:00', 'end_time' => '22:00']);
```

### Bước 3: Build Frontend

```bash
npm install
npm run dev
# hoặc
npm run build
```

---

## 📖 Hướng Dẫn Sử Dụng

### 1️⃣ Thêm Nhân Viên Mới

**Bước 1:** Truy cập trang Quản lý nhân viên
```
http://your-domain/admin/employees
```

**Bước 2:** Click nút "➕ Nhân viên"

**Bước 3:** Điền thông tin trong 2 tabs:

#### Tab "Thông tin cơ bản"
- Họ tên (*)
- Email (*) - Sẽ tạo tài khoản đăng nhập
- Số điện thoại
- Mã nhân viên (tự động hoặc nhập tay)
- Phòng ban, Chức vụ, Chi nhánh
- Ngày bắt đầu, Ngày sinh, Giới tính, CMND/CCCD

#### Tab "Thiết lập lương"
- **Lương cơ bản**: Chọn loại (Cố định/Theo giờ) và mức lương
- **Phụ cấp** (Bật/Tắt):
  - Thêm nhiều phụ cấp: Tên, Số tiền, Loại (Cố định ngày/tháng hoặc % lương)
- **Thưởng chỉ tiêu** (Bật/Tắt):
  - Loại hoạt động, Chỉ tiêu (X), Loại thưởng, Giá trị thưởng (Y)
- **Giảm trừ** (Bật/Tắt):
  - Lý do, Số tiền, Tần suất (Một lần/Hàng tháng)

**Bước 4:** Click "Lưu"

✅ **Kết quả:**
- Tạo User với role='staff', password mặc định '123456'
- Tạo Employee với đầy đủ thông tin
- Tạo các phụ cấp, thưởng, giảm trừ nếu có
- Tất cả được xử lý trong 1 Transaction (đảm bảo tính toàn vẹn dữ liệu)

---

### 2️⃣ Lập Lịch Làm Việc

**Bước 1:** Tại danh sách nhân viên, click icon "📅" của nhân viên

**Bước 2:** Chọn:
- Ca làm việc (với thời gian hiển thị)
- Ngày làm việc (chỉ được chọn từ hôm nay trở đi)
- Ghi chú (optional)

**Bước 3:** Click "Lưu"

✅ **Lưu ý:** Hệ thống tự động chống trùng lặp (1 nhân viên không thể có 2 ca giống nhau trong cùng 1 ngày)

---

### 3️⃣ Sửa/Xóa Nhân Viên

- **Sửa:** Click icon "✏️" → Cập nhật thông tin → Lưu
- **Xóa:** Click icon "🗑️" → Xác nhận
  - ⚠️ Khi xóa nhân viên sẽ cascade xóa: User account, Allowances, Targets, Deductions, Schedules

---

## 🔒 Phân Quyền

Trong bảng `users`, cột `role` có 3 giá trị:
- `admin`: Quản trị viên (toàn quyền)
- `staff`: Nhân viên (quyền hạn chế)
- `user`: Khách hàng (chỉ xem)

Kiểm tra quyền trong Controller hoặc Middleware:
```php
if (auth()->user()->isAdmin()) {
    // Cho phép thao tác
}
```

---

## 🧪 Test API với Postman/Insomnia

### Lấy danh sách nhân viên
```
GET /admin/employees
```

### Lấy resources (Phòng ban, Chức vụ, Chi nhánh)
```
GET /admin/employees/resources/data
```

### Tạo nhân viên mới
```
POST /admin/employees
Content-Type: application/json

{
  "full_name": "Nguyễn Văn A",
  "email": "nguyenvana@example.com",
  "phone_number": "0123456789",
  "salary_type": "fixed",
  "salary_level": 10000000,
  "department_id": 1,
  "position_id": 1,
  "allowances": [
    {
      "name": "Ăn trưa",
      "amount": 30000,
      "type": "fixed_daily"
    }
  ],
  "targets": [
    {
      "activity_type": "Bán hàng",
      "target_amount": 50000000,
      "bonus_type": "percent",
      "bonus_value": 5
    }
  ]
}
```

### Lấy danh sách ca làm việc
```
GET /admin/shifts/api
```

### Tạo lịch làm việc
```
POST /admin/employee-schedules
Content-Type: application/json

{
  "employee_id": 1,
  "shift_id": 1,
  "schedule_date": "2025-11-05",
  "notes": "Ghi chú"
}
```

---

## 🎯 Các Tính Năng Nổi Bật

### ⚡ Transaction Safety
Service `EmployeeService` sử dụng `DB::transaction()` để đảm bảo:
- Tạo User thất bại → Không tạo Employee
- Tạo Employee thất bại → Rollback User
- Tạo Allowances thất bại → Rollback tất cả

### 🔍 Tìm kiếm & Lọc
- Tìm theo tên, mã nhân viên, số điện thoại
- Lọc theo phòng ban, chức vụ, chi nhánh
- Pagination 15 items/page

### 📊 Quan hệ dữ liệu đầy đủ
Mọi relationship được load sẵn:
```php
$employee->user
$employee->department
$employee->position
$employee->branch
$employee->allowances
$employee->targets
$employee->deductions
$employee->schedules
```

---

## 🐛 Troubleshooting

### Lỗi: "Email đã tồn tại"
→ Email đã được đăng ký cho user khác. Sử dụng email khác.

### Lỗi: "Mã nhân viên đã tồn tại"
→ Click nút "Tạo mã tự động" hoặc nhập mã khác.

### Lỗi: "Nhân viên đã có lịch làm việc cho ca này"
→ Unique constraint: 1 nhân viên không thể có 2 ca giống nhau trong 1 ngày.

### Không thấy Phòng ban/Chức vụ trong dropdown
→ Chạy seeder hoặc tạo dữ liệu mẫu (xem Bước 2 ở trên).

---

## 📞 Liên Hệ & Hỗ Trợ

Nếu gặp vấn đề, vui lòng:
1. Kiểm tra Laravel logs: `storage/logs/laravel.log`
2. Kiểm tra browser console (F12) cho lỗi JavaScript
3. Test API endpoint với Postman để xác định lỗi backend/frontend

---

## 🎉 Kết Luận

Chức năng **Quản lý Nhân viên** đã được triển khai hoàn chỉnh với:
- ✅ 8 Migrations
- ✅ 8 Models với relationships
- ✅ 1 Service layer với Transaction
- ✅ 3 Controllers
- ✅ 4 Request validations
- ✅ 4 Vue components (Index + 3 Modals)
- ✅ Routes đầy đủ

Chúc bạn sử dụng hiệu quả! 🚀
