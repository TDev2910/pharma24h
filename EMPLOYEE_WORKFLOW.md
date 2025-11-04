# 🗺️ LUỒNG TRIỂN KHAI HỆ THỐNG QUẢN LÝ NHÂN VIÊN

## 📌 TỔNG QUAN

Hệ thống quản lý nhân viên bao gồm **9 bảng** trong database, được chia thành 3 nhóm chính:

### 🔵 Nhóm 1: CÁC BẢNG PHỤ TRỢ (Foundation Tables)
Các bảng này chứa **dữ liệu tham chiếu** - phải tạo TRƯỚC.

### 🟢 Nhóm 2: BẢNG NHÂN VIÊN CHÍNH (Core Employee Table)
Bảng trung tâm, liên kết với tất cả bảng khác.

### 🟡 Nhóm 3: CÁC BẢNG CON (Child Tables)
Các bảng phụ thuộc vào bảng nhân viên.

---

## 📊 SƠ ĐỒ QUAN HỆ TỔNG QUÁT

```
┌─────────────────────────────────────────────────────────────────┐
│                      HỆ THỐNG QUẢN LÝ NHÂN VIÊN                  │
└─────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────┐
│  NHÓM 1: CÁC BẢNG PHỤ TRỢ (Phải tạo TRƯỚC)                       │
├──────────────────────────────────────────────────────────────────┤
│                                                                   │
│  ┌────────────┐    ┌────────────┐    ┌──────────┐               │
│  │  users     │    │ positions  │    │ branches │               │
│  │ (Tài khoản)│    │ (Chức danh)│    │(Chi nhánh│               │
│  └─────┬──────┘    └─────┬──────┘    └────┬─────┘               │
│        │                 │                 │                     │
│        │   ┌──────────┐  │                 │                     │
│        │   │departments│  │                 │                     │
│        │   │(Phòng ban)│  │                 │                     │
│        │   └─────┬─────┘  │                 │                     │
│        │         │        │                 │                     │
└────────┼─────────┼────────┼─────────────────┼─────────────────────┘
         │         │        │                 │
         │         │        │                 │
         ▼         ▼        ▼                 ▼
┌────────────────────────────────────────────────────────────────┐
│  NHÓM 2: BẢNG NHÂN VIÊN CHÍNH (Bảng trung tâm)                 │
├────────────────────────────────────────────────────────────────┤
│                                                                 │
│                    ┌──────────────────┐                        │
│                    │    employees     │                        │
│                    │   (Nhân viên)    │                        │
│                    └────────┬─────────┘                        │
│                             │                                  │
└─────────────────────────────┼──────────────────────────────────┘
                              │
            ┌─────────────────┼─────────────────┬────────────┐
            │                 │                 │            │
            ▼                 ▼                 ▼            ▼
┌───────────────────────────────────────────────────────────────┐
│  NHÓM 3: CÁC BẢNG CON (Phụ thuộc vào employees)               │
├───────────────────────────────────────────────────────────────┤
│                                                                │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐       │
│  │  allowances  │  │   targets    │  │  deductions  │       │
│  │  (Phụ cấp)   │  │(Thưởng chỉ   │  │  (Giảm trừ)  │       │
│  │              │  │    tiêu)     │  │              │       │
│  └──────────────┘  └──────────────┘  └──────────────┘       │
│                                                                │
│                    ┌──────────┐                                │
│                    │  shifts  │                                │
│                    │(Ca làm   │                                │
│                    │  việc)   │                                │
│                    └────┬─────┘                                │
│                         │                                      │
│                         ▼                                      │
│                  ┌─────────────┐                               │
│                  │  schedules  │                               │
│                  │ (Lịch làm   │                               │
│                  │    việc)    │                               │
│                  └─────────────┘                               │
└───────────────────────────────────────────────────────────────┘
```

---

## 🎯 GIAI ĐOẠN 1: TẠO MIGRATIONS (Database Schema)

### Mục tiêu
Tạo cấu trúc các bảng trong database theo **đúng thứ tự dependencies**.

---

## 📋 CHI TIẾT TỪNG BẢNG

### 🔵 **NHÓM 1: BẢNG PHỤ TRỢ** (Tạo trước)

---

#### **Bảng 1: `users`** ⚙️
**📌 Trạng thái:** ĐÃ CÓ SẴN (không cần tạo migration)

**🎯 Vai trò:**
- Lưu thông tin **tài khoản đăng nhập** của tất cả người dùng trong hệ thống
- Quản lý **phân quyền** (admin/staff/user)
- Mỗi nhân viên sẽ có 1 tài khoản để đăng nhập

**📊 Cấu trúc quan trọng:**
- `id`: Mã định danh tài khoản
- `name`: Tên người dùng
- `email`: Email đăng nhập (duy nhất)
- `password`: Mật khẩu đã mã hóa
- `role`: Vai trò (admin, staff, user)

**🔗 Quan hệ:**
- **1 user** → **1 employee** (Một tài khoản chỉ cho một nhân viên)

**💡 Ví dụ thực tế:**
```
Email: nguyenvana@company.com
Password: ******
Role: staff
→ Dùng để đăng nhập vào hệ thống
```

---

#### **Bảng 2: `positions`** 👔
**📌 Migration:** `create_positions_table.php` (CẦN TẠO)

**🎯 Vai trò:**
- Lưu danh sách **chức danh/vị trí công việc** trong công ty
- Giống như "danh mục nghề nghiệp"

**📊 Cấu trúc:**
- `id`: Mã chức danh
- `name`: Tên chức danh
- `description`: Mô tả công việc

**🔗 Quan hệ:**
- **1 position** → **N employees** (Một chức danh có nhiều nhân viên)

**💡 Ví dụ thực tế:**
```
ID 1: Dược sĩ bán thuốc
ID 2: Thu ngân
ID 3: Nhân viên kho
ID 4: Quản lý
```

**❓ Tại sao cần bảng này?**
- Để nhóm nhân viên theo nghề nghiệp
- Dễ tìm kiếm: "Tìm tất cả dược sĩ"
- Thống kê: "Có bao nhiêu nhân viên kho?"

---

#### **Bảng 3: `departments`** 🏢
**📌 Migration:** `create_departments_table.php` (CẦN TẠO)

**🎯 Vai trò:**
- Lưu danh sách **phòng ban** trong công ty
- Tổ chức cơ cấu quản lý theo bộ phận

**📊 Cấu trúc:**
- `id`: Mã phòng ban
- `name`: Tên phòng ban
- `description`: Mô tả nhiệm vụ

**🔗 Quan hệ:**
- **1 department** → **N employees** (Một phòng ban có nhiều nhân viên)

**💡 Ví dụ thực tế:**
```
ID 1: Phòng Kinh doanh
ID 2: Phòng Kỹ thuật
ID 3: Phòng Hành chính
ID 4: Phòng Kế toán
```

**❓ Tại sao cần bảng này?**
- Phân nhóm nhân viên theo bộ phận
- Dễ quản lý: "Phòng Kinh doanh có ai?"
- Báo cáo: "Chi phí lương của từng phòng ban"

---

#### **Bảng 4: `branches`** 🏪
**📌 Migration:** `create_branches_table.php` (CẦN TẠO)

**🎯 Vai trò:**
- Lưu danh sách **chi nhánh/cửa hàng** của công ty
- Quản lý nhân sự theo địa điểm

**📊 Cấu trúc:**
- `id`: Mã chi nhánh
- `name`: Tên chi nhánh
- `address`: Địa chỉ
- `phone_number`: Số điện thoại

**🔗 Quan hệ:**
- **1 branch** → **N employees** (Một chi nhánh có nhiều nhân viên)
- **1 branch** → **N shifts** (Một chi nhánh có nhiều ca làm việc)

**💡 Ví dụ thực tế:**
```
ID 1: Chi nhánh Hà Nội - 123 Đường ABC, HN
ID 2: Chi nhánh TP.HCM - 456 Đường XYZ, HCM
ID 3: Chi nhánh Đà Nẵng - 789 Đường DEF, DN
```

**❓ Tại sao cần bảng này?**
- Quản lý nhân viên theo vị trí địa lý
- Phân ca làm việc riêng cho mỗi chi nhánh
- Báo cáo: "Doanh số của từng chi nhánh"

---

### 🟢 **NHÓM 2: BẢNG NHÂN VIÊN CHÍNH**

---

#### **Bảng 5: `employees`** 👤 (QUAN TRỌNG NHẤT!)
**📌 Migration:** `create_employees_table.php` (CẦN TẠO)

**🎯 Vai trò:**
- Bảng **TRUNG TÂM** của toàn bộ hệ thống
- Lưu **hồ sơ nhân sự** đầy đủ của mỗi nhân viên
- Liên kết với **TẤT CẢ** các bảng khác

**📊 Cấu trúc:**
Chia làm 4 nhóm thông tin:

**1️⃣ Liên kết với tài khoản:**
- `user_id`: Tham chiếu đến bảng `users` (tài khoản đăng nhập)

**2️⃣ Thông tin cơ bản:**
- `full_name`: Họ tên đầy đủ
- `phone_number`: Số điện thoại (duy nhất)
- `employee_code`: Mã nhân viên (VD: NV0001)
- `dob`: Ngày sinh
- `gender`: Giới tính (nam/nữ)
- `address`: Địa chỉ
- `id_card_number`: Số CMND/CCCD

**3️⃣ Thông tin công việc:**
- `department_id`: Thuộc phòng ban nào?
- `position_id`: Chức danh gì?
- `branch_id`: Làm việc ở chi nhánh nào?
- `start_date`: Ngày bắt đầu làm việc

**4️⃣ Thông tin lương:**
- `salary_type`: Loại lương (fixed=cố định, per_hour=theo giờ)
- `salary_level`: Mức lương cơ bản (số tiền)

**🔗 Quan hệ:**
- **N employees** → **1 user** (Nhiều nhân viên thuộc một tài khoản) ❌ SAI
- **1 employee** → **1 user** (Một nhân viên một tài khoản) ✅ ĐÚNG
- **N employees** → **1 position** (Nhiều nhân viên cùng chức danh)
- **N employees** → **1 department** (Nhiều nhân viên cùng phòng ban)
- **N employees** → **1 branch** (Nhiều nhân viên cùng chi nhánh)
- **1 employee** → **N allowances** (Một nhân viên có nhiều phụ cấp)
- **1 employee** → **N targets** (Một nhân viên có nhiều chỉ tiêu)
- **1 employee** → **N deductions** (Một nhân viên có nhiều khoản giảm trừ)
- **1 employee** → **N schedules** (Một nhân viên có nhiều lịch làm việc)

**💡 Ví dụ thực tế:**
```
Nhân viên: Nguyễn Văn A
- Mã NV: NV0001
- Tài khoản: nguyenvana@company.com
- Chức danh: Dược sĩ bán thuốc
- Phòng ban: Kinh doanh
- Chi nhánh: Hà Nội
- Lương cố định: 10.000.000 VNĐ/tháng
```

**❓ Tại sao cần bảng này?**
- Lưu trữ TẤT CẢ thông tin nhân viên
- Trung tâm kết nối toàn bộ hệ thống
- Tính lương, chấm công, báo cáo dựa trên bảng này

---

### 🟡 **NHÓM 3: CÁC BẢNG CON** (Phụ thuộc vào employees)

---

#### **Bảng 6: `employee_allowances`** 💰
**📌 Migration:** `create_employee_allowances_table.php` (CẦN TẠO)

**🎯 Vai trò:**
- Lưu các khoản **PHỤ CẤP** của nhân viên
- MỘT nhân viên có thể có NHIỀU loại phụ cấp

**📊 Cấu trúc:**
- `employee_id`: Nhân viên nào? (tham chiếu `employees`)
- `name`: Tên phụ cấp (VD: Ăn trưa)
- `amount`: Số tiền
- `type`: Loại phụ cấp
  - `fixed_daily`: Cố định theo ngày (VD: 30.000đ/ngày)
  - `fixed_monthly`: Cố định theo tháng (VD: 1.000.000đ/tháng)
  - `percent_salary`: Theo % lương (VD: 10% lương cơ bản)

**🔗 Quan hệ:**
- **N allowances** → **1 employee** (Nhiều phụ cấp của một nhân viên)

**💡 Ví dụ thực tế:**
```
Nhân viên NV0001 có 3 phụ cấp:
1. Ăn trưa: 30.000đ/ngày (fixed_daily)
2. Xăng xe: 1.000.000đ/tháng (fixed_monthly)
3. Hiệu quả: 10% lương (percent_salary)
```

**❓ Tại sao cần bảng riêng?**
- Mỗi nhân viên có thể có nhiều loại phụ cấp khác nhau
- Dễ thêm/xóa/sửa từng loại phụ cấp
- Tính lương linh hoạt

---

#### **Bảng 7: `employee_targets`** 🎯
**📌 Migration:** `create_employee_targets_table.php` (CẦN TẠO)

**🎯 Vai trò:**
- Lưu các **CHỈ TIÊU THƯỞNG** của nhân viên
- Mô hình: "Nếu đạt chỉ tiêu X thì được thưởng Y"

**📊 Cấu trúc:**
- `employee_id`: Nhân viên nào?
- `activity_type`: Loại hoạt động (VD: Bán hàng, Dịch vụ spa)
- `target_amount`: Chỉ tiêu cần đạt (X) - số tiền
- `bonus_type`: Loại thưởng
  - `fixed`: Thưởng cố định
  - `percent`: Thưởng theo % doanh thu
- `bonus_value`: Giá trị thưởng (Y)

**🔗 Quan hệ:**
- **N targets** → **1 employee** (Nhiều chỉ tiêu của một nhân viên)

**💡 Ví dụ thực tế:**
```
Nhân viên NV0001 có 2 chỉ tiêu:
1. Bán hàng:
   - Chỉ tiêu: 50.000.000đ
   - Thưởng: 5% doanh thu (percent)
   
2. Dịch vụ spa:
   - Chỉ tiêu: 20.000.000đ
   - Thưởng: 2.000.000đ (fixed)
```

**❓ Tại sao cần bảng riêng?**
- Một nhân viên có thể có nhiều loại chỉ tiêu
- Linh hoạt trong việc thiết lập KPI
- Dễ theo dõi hiệu suất làm việc

---

#### **Bảng 8: `employee_deductions`** 📉
**📌 Migration:** `create_employee_deductions_table.php` (CẦN TẠO)

**🎯 Vai trò:**
- Lưu các khoản **GIẢM TRỪ** (khấu trừ) vào lương
- VD: Tạm ứng, trả góp, phạt...

**📊 Cấu trúc:**
- `employee_id`: Nhân viên nào?
- `reason`: Lý do giảm trừ (VD: Tạm ứng tiền)
- `amount`: Số tiền giảm trừ
- `frequency`: Tần suất
  - `one-time`: Một lần duy nhất
  - `monthly`: Hàng tháng

**🔗 Quan hệ:**
- **N deductions** → **1 employee** (Nhiều khoản giảm trừ của một nhân viên)

**💡 Ví dụ thực tế:**
```
Nhân viên NV0001 có 2 khoản giảm trừ:
1. Tạm ứng: 5.000.000đ (trừ một lần)
2. Trả góp laptop: 1.000.000đ/tháng (hàng tháng)
```

**❓ Tại sao cần bảng riêng?**
- Quản lý công nợ của nhân viên
- Tự động trừ lương hàng tháng
- Lịch sử minh bạch

---

#### **Bảng 9: `shifts`** ⏰
**📌 Migration:** `create_shifts_table.php` (CẦN TẠO)

**🎯 Vai trò:**
- Lưu danh sách **CA LÀM VIỆC** của công ty
- Định nghĩa giờ làm việc

**📊 Cấu trúc:**
- `id`: Mã ca
- `name`: Tên ca (VD: Ca sáng)
- `start_time`: Giờ bắt đầu (VD: 08:00:00)
- `end_time`: Giờ kết thúc (VD: 17:00:00)
- `branch_id`: Thuộc chi nhánh nào? (có thể NULL = áp dụng chung)

**🔗 Quan hệ:**
- **N shifts** → **1 branch** (Nhiều ca của một chi nhánh)
- **1 shift** → **N schedules** (Một ca có nhiều lịch làm việc)

**💡 Ví dụ thực tế:**
```
Chi nhánh Hà Nội có 3 ca:
1. Ca sáng: 08:00 - 12:00
2. Ca chiều: 13:00 - 17:00
3. Ca tối: 18:00 - 22:00
```

**❓ Tại sao cần bảng riêng?**
- Tái sử dụng ca làm việc cho nhiều nhân viên
- Dễ quản lý: Thay đổi giờ ca → tự động cập nhật toàn bộ
- Linh hoạt thiết lập ca theo chi nhánh

---

#### **Bảng 10: `employee_schedules`** 📅
**📌 Migration:** `create_employee_schedules_table.php` (CẦN TẠO)

**🎯 Vai trò:**
- Lưu **LỊCH LÀM VIỆC CỤ THỂ** của từng nhân viên
- Phân công ca làm việc theo ngày

**📊 Cấu trúc:**
- `employee_id`: Nhân viên nào?
- `shift_id`: Ca nào?
- `schedule_date`: Ngày nào? (VD: 2025-11-05)
- `notes`: Ghi chú (nếu có)
- **UNIQUE**: Không cho phép trùng lặp (cùng nhân viên, cùng ca, cùng ngày)

**🔗 Quan hệ:**
- **N schedules** → **1 employee** (Nhiều lịch của một nhân viên)
- **N schedules** → **1 shift** (Nhiều lịch sử dụng một ca)

**💡 Ví dụ thực tế:**
```
Lịch làm việc của Nguyễn Văn A (NV0001):
- 05/11/2025: Ca sáng (08:00-12:00)
- 06/11/2025: Ca chiều (13:00-17:00)
- 07/11/2025: Nghỉ
- 08/11/2025: Ca sáng (08:00-12:00)
```

**❓ Tại sao cần bảng riêng?**
- Lập lịch linh hoạt cho từng nhân viên
- Chống trùng lặp: 1 người không thể làm 2 ca cùng lúc
- Chấm công, tính lương dựa trên lịch này

---

## ⚠️ THỨ TỰ TẠO MIGRATIONS (CỰC KỲ QUAN TRỌNG!)

### ❌ Lỗi phổ biến:
Nếu tạo sai thứ tự → Laravel sẽ báo lỗi:
```
SQLSTATE[42S02]: Base table or view not found: 'positions'
```

### ✅ Thứ tự ĐÚNG:

```
📋 THỨ TỰ CHẠY MIGRATIONS

BƯỚC 1: Bảng PHỤ TRỢ (không phụ thuộc gì)
  ├── users (đã có sẵn)
  ├── positions ← TẠO TRƯỚC
  ├── departments
  └── branches

BƯỚC 2: Bảng NHÂN VIÊN CHÍNH (phụ thuộc BƯỚC 1)
  └── employees ← Cần: users, positions, departments, branches

BƯỚC 3: Bảng CON LƯƠNG (phụ thuộc employees)
  ├── employee_allowances
  ├── employee_targets
  └── employee_deductions

BƯỚC 4: Bảng CA LÀM VIỆC (phụ thuộc branches)
  └── shifts

BƯỚC 5: Bảng LỊCH LÀM VIỆC (phụ thuộc employees + shifts)
  └── employee_schedules
```

### 🔑 Nguyên tắc vàng:
> **"Bảng CHA phải được tạo TRƯỚC bảng CON"**

---

## 📝 CÁC BƯỚC THỰC HIỆN (GIAI ĐOẠN 1)

### ✅ Checklist tổng quan:

```
□ CHUẨN BỊ
  □ Đọc và hiểu sơ đồ quan hệ
  □ Đọc vai trò của từng bảng
  □ Hiểu rõ thứ tự dependencies

□ TẠO MIGRATION FILES (8 files)
  □ Tạo theo đúng thứ tự
  □ Đặt tên đúng format
  □ Kiểm tra timestamp

□ VIẾT CODE TRONG MIGRATIONS
  □ Định nghĩa cấu trúc bảng
  □ Thiết lập foreign keys
  □ Thiết lập unique constraints

□ CHẠY MIGRATIONS
  □ Chạy php artisan migrate
  □ Kiểm tra không có lỗi
  □ Verify trong database

□ TẠO DỮ LIỆU MẪU
  □ Insert positions (5 records)
  □ Insert departments (4 records)
  □ Insert branches (3 records)
  □ Insert shifts (4 records)

□ KIỂM TRA KẾT QUẢ
  □ 9 bảng đã xuất hiện trong database
  □ Dữ liệu mẫu đã có
  □ Foreign keys hoạt động đúng
```

---

## 🎯 KẾT QUẢ MONG ĐỢI SAU GIAI ĐOẠN 1

Sau khi hoàn thành, bạn sẽ có:

✅ **9 bảng trong database:**
```
1. users (đã có sẵn)
2. positions
3. departments
4. branches
5. employees
6. employee_allowances
7. employee_targets
8. employee_deductions
9. shifts
10. employee_schedules
```

✅ **Dữ liệu mẫu:**
- 5 chức danh (positions)
- 4 phòng ban (departments)
- 3 chi nhánh (branches)
- 4 ca làm việc (shifts)

✅ **Quan hệ giữa các bảng đã được thiết lập**

---

## 🔜 GIAI ĐOẠN TIẾP THEO

Sau khi xong Giai đoạn 1, chúng ta sẽ tiếp tục:

**GIAI ĐOẠN 2: TẠO MODELS**
- Tạo 8 Model classes
- Định nghĩa Relationships (hasMany, belongsTo)
- Test relationships bằng Tinker

**GIAI ĐOẠN 3: REQUESTS + SERVICE**
- Tạo Validation Rules
- Tạo Service xử lý logic với Transaction

**GIAI ĐOẠN 4: CONTROLLERS**
- Tạo EmployeeController
- Tạo ScheduleController
- Tạo ShiftController

**GIAI ĐOẠN 5: ROUTES**
- Đăng ký routes trong admin.php

**GIAI ĐOẠN 6: VUE COMPONENTS**
- Tạo giao diện quản lý

---

## 📌 LƯU Ý QUAN TRỌNG

### 🔴 KHÔNG ĐƯỢC:
- ❌ Tạo bảng `employees` trước bảng `positions`
- ❌ Tạo bảng `employee_schedules` trước bảng `shifts`
- ❌ Xóa migration sau khi đã chạy (dùng rollback)

### 🟢 NÊN:
- ✅ Đọc kỹ tài liệu này TRƯỚC KHI CODE
- ✅ Tạo migrations theo đúng thứ tự
- ✅ Test từng bước nhỏ
- ✅ Backup database trước khi migrate

---

## ❓ CÂU HỎI THƯỜNG GẶP

**Q: Tại sao phải tách thành nhiều bảng, không gộp chung vào `employees`?**
A: 
- Tránh lặp dữ liệu (VD: tên phòng ban lặp lại 100 lần)
- Dễ cập nhật (Đổi tên phòng ban → chỉ sửa 1 chỗ)
- Linh hoạt (1 nhân viên có thể có nhiều phụ cấp)

**Q: Foreign key `onDelete('cascade')` nghĩa là gì?**
A:
- Khi xóa bản ghi CHA → tự động xóa các bản ghi CON
- VD: Xóa employee → tự động xóa allowances, targets, deductions, schedules

**Q: `unique(['employee_id', 'shift_id', 'schedule_date'])` để làm gì?**
A:
- Chống trùng lặp: 1 nhân viên không thể có 2 lịch cùng ca trong 1 ngày
- Đảm bảo tính toàn vẹn dữ liệu

---

## 📢 SẴN SÀNG BẮT ĐẦU?

Sau khi đọc xong tài liệu này, bạn hãy:

1. ✅ Đọc lại sơ đồ quan hệ
2. ✅ Hiểu rõ vai trò từng bảng
3. ✅ Nắm vững thứ tự thực hiện
4. ✅ Báo tôi: **"Đã hiểu rõ, bắt đầu code Giai đoạn 1!"**

→ Tôi sẽ hướng dẫn chi tiết từng bước code!

---

**📚 Tài liệu này chỉ MÔ TẢ LUỒNG - chưa có CODE.**
**Khi sẵn sàng code, tôi sẽ hướng dẫn từng dòng lệnh cụ thể!**
