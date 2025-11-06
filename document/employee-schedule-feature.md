# 📅 Tài liệu chi tiết: Chức năng Quản lý Lịch làm việc Nhân viên

## 📋 Mục lục

1. [Tổng quan](#tổng-quan)
2. [Kiến trúc hệ thống](#kiến-trúc-hệ-thống)
3. [Cấu trúc Database](#cấu-trúc-database)
4. [Backend Implementation](#backend-implementation)
5. [Frontend Implementation](#frontend-implementation)
6. [API Endpoints](#api-endpoints)
7. [Luồng hoạt động](#luồng-hoạt-động)
8. [Ví dụ sử dụng](#ví-dụ-sử-dụng)
9. [Troubleshooting](#troubleshooting)

---

## 🎯 Tổng quan

### Mô tả chức năng

Chức năng **Quản lý Lịch làm việc Nhân viên** cho phép:
- ✅ Tạo lịch làm việc cho nhân viên theo từng ca (Buổi Sáng, Buổi Chiều, Buổi Tối)
- ✅ Một nhân viên có thể làm nhiều ca trong cùng một ngày
- ✅ Xem lịch làm việc theo tuần (dạng grid như KiotViet)
- ✅ Cập nhật và xóa lịch làm việc
- ✅ Tính lương dự kiến dựa trên số ca làm việc

### Yêu cầu nghiệp vụ

1. **Một nhân viên có thể làm nhiều ca trong cùng ngày**
   - Ví dụ: NV0001 có thể làm cả ca Sáng (07:00-11:00) và ca Chiều (13:00-17:00) trong ngày 06/11/2025

2. **Không cho phép trùng lặp**
   - Một nhân viên không thể có cùng một ca trong cùng một ngày
   - Ví dụ: NV0001 không thể có 2 lịch "Buổi Sáng" trong ngày 06/11/2025

3. **Hiển thị theo tuần**
   - Lịch được hiển thị dạng grid: Nhân viên (cột dọc) × 7 ngày trong tuần (cột ngang)
   - Mỗi ô hiển thị các ca làm việc của nhân viên trong ngày đó

---

## 🏗️ Kiến trúc hệ thống

### Sơ đồ tổng quan

```
┌─────────────────┐
│   Frontend      │
│  (Vue.js)       │
│                 │
│  - Index.vue    │ ← Hiển thị grid lịch
│  - CreateModal  │ ← Form thêm/sửa
└────────┬────────┘
         │ HTTP Request
         ▼
┌─────────────────┐
│   Routes        │
│  (admin.php)    │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Controller    │
│ ScheduleController│
│                 │
│  - index()      │
│  - store()      │
│  - update()     │
│  - destroy()    │
│  - getWeekly()  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Request       │
│ StoreSchedule   │
│  Request        │ ← Validation
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Model         │
│ EmployeeSchedule│
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Database      │
│ employee_schedules│
└─────────────────┘
```

### Mối quan hệ giữa các bảng

```
employees (Nhân viên)
    │
    │ 1:N (một nhân viên có nhiều lịch)
    │
    ▼
employee_schedules (Lịch làm việc)
    │
    │ N:1 (nhiều lịch thuộc một ca)
    │
    ▼
shifts (Ca làm việc)
```

---

## 💾 Cấu trúc Database

### Bảng: `employee_schedules`

#### Schema

```sql
CREATE TABLE `employee_schedules` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `employee_id` BIGINT UNSIGNED NOT NULL,
  `shift_id` BIGINT UNSIGNED NOT NULL,
  `schedule_date` DATE NOT NULL,
  `notes` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  
  FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`shift_id`) REFERENCES `shifts`(`id`) ON DELETE CASCADE,
  UNIQUE KEY (`employee_id`, `shift_id`, `schedule_date`)
);
```

#### Giải thích các cột

| Cột | Kiểu dữ liệu | Mô tả | Ràng buộc |
|-----|--------------|-------|-----------|
| `id` | BIGINT UNSIGNED | Khóa chính, tự tăng | PRIMARY KEY |
| `employee_id` | BIGINT UNSIGNED | ID nhân viên | FOREIGN KEY → `employees.id` |
| `shift_id` | BIGINT UNSIGNED | ID ca làm việc | FOREIGN KEY → `shifts.id` |
| `schedule_date` | DATE | Ngày làm việc (YYYY-MM-DD) | NOT NULL |
| `notes` | TEXT | Ghi chú (tùy chọn) | NULL |
| `created_at` | TIMESTAMP | Thời gian tạo | NULL |
| `updated_at` | TIMESTAMP | Thời gian cập nhật | NULL |

#### Unique Constraint

```php
UNIQUE (`employee_id`, `shift_id`, `schedule_date`)
```

**Ý nghĩa:** Đảm bảo một nhân viên không thể có cùng một ca trong cùng một ngày.

**Ví dụ hợp lệ:**
```
✅ employee_id=1, shift_id=1 (Buổi Sáng), schedule_date=2025-11-06
✅ employee_id=1, shift_id=2 (Buổi Chiều), schedule_date=2025-11-06  ← Ca khác, OK
✅ employee_id=1, shift_id=1 (Buổi Sáng), schedule_date=2025-11-07  ← Ngày khác, OK
```

**Ví dụ không hợp lệ:**
```
❌ employee_id=1, shift_id=1 (Buổi Sáng), schedule_date=2025-11-06
❌ employee_id=1, shift_id=1 (Buổi Sáng), schedule_date=2025-11-06  ← TRÙNG!
```

#### Foreign Keys

1. **`employee_id` → `employees.id`**
   - `ON DELETE CASCADE`: Khi xóa nhân viên, tự động xóa tất cả lịch làm việc của nhân viên đó

2. **`shift_id` → `shifts.id`**
   - `ON DELETE CASCADE`: Khi xóa ca làm việc, tự động xóa tất cả lịch sử dụng ca đó

---

## 🔧 Backend Implementation

### 1. Migration

**File:** `database/migrations/YYYY_MM_DD_HHMMSS_create_employee_schedules_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_schedules', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('employee_id')
                ->constrained('employees')
                ->onDelete('cascade');
            
            $table->foreignId('shift_id')
                ->constrained('shifts')
                ->onDelete('cascade');
            
            $table->date('schedule_date');
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Unique: Một nhân viên không thể có cùng ca trong cùng ngày
            $table->unique(['employee_id', 'shift_id', 'schedule_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_schedules');
    }
};
```

**Chạy migration:**
```bash
php artisan migrate
```

---

### 2. Model

**File:** `app/Models/EmployeeSchedule.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeSchedule extends Model
{
    use HasFactory;

    /**
     * Các trường có thể được gán hàng loạt (mass assignment)
     */
    protected $fillable = [
        'employee_id',
        'shift_id',
        'schedule_date',
        'notes'
    ];
    
    /**
     * Chuyển đổi kiểu dữ liệu
     */
    protected $casts = [
        'schedule_date' => 'date', // Tự động convert string thành Carbon date
    ];

    /**
     * Quan hệ: N Schedules thuộc 1 Employee
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Quan hệ: N Schedules thuộc 1 Shift
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
```

**Ví dụ sử dụng Model:**

```php
// Lấy lịch làm việc với thông tin nhân viên và ca
$schedule = EmployeeSchedule::with(['employee', 'shift'])->find(1);

echo $schedule->employee->full_name; // "Nguyễn Văn A"
echo $schedule->shift->name; // "Buổi Sáng"
echo $schedule->schedule_date->format('d/m/Y'); // "06/11/2025"

// Tạo lịch mới
EmployeeSchedule::create([
    'employee_id' => 1,
    'shift_id' => 1,
    'schedule_date' => '2025-11-06',
    'notes' => 'Làm thêm giờ'
]);
```

---

### 3. Form Request Validation

**File:** `app/Http/Requests/StoreScheduleRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleRequest extends FormRequest
{
    /**
     * Xác định user có quyền thực hiện request này không
     */
    public function authorize(): bool
    {
        return true; // Có thể thêm logic kiểm tra quyền ở đây
    }

    /**
     * Các quy tắc validation
     */
    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'shift_id' => 'required|exists:shifts,id',
            'schedule_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Thông báo lỗi tùy chỉnh (tiếng Việt)
     */
    public function messages(): array
    {
        return [
            'employee_id.required' => 'Vui lòng chọn nhân viên',
            'employee_id.exists' => 'Nhân viên không tồn tại',
            'shift_id.required' => 'Vui lòng chọn ca làm việc',
            'shift_id.exists' => 'Ca làm việc không tồn tại',
            'schedule_date.required' => 'Vui lòng chọn ngày làm việc',
            'schedule_date.date' => 'Ngày làm việc không hợp lệ',
            'notes.max' => 'Ghi chú không được vượt quá 500 ký tự',
        ];
    }
}
```

**Giải thích các rule:**

| Rule | Ý nghĩa |
|------|---------|
| `required` | Trường bắt buộc phải có giá trị |
| `exists:employees,id` | Giá trị phải tồn tại trong bảng `employees`, cột `id` |
| `date` | Giá trị phải là định dạng ngày hợp lệ |
| `nullable` | Trường có thể để trống (NULL) |
| `string|max:500` | Phải là chuỗi và tối đa 500 ký tự |

---

### 4. Controller

**File:** `app/Http/Controllers/Admin/Employee/ScheduleController.php`

#### 4.1. Method `index()` - Hiển thị trang danh sách

```php
/**
 * Hiển thị danh sách lịch làm việc (dùng với Inertia)
 */
public function index(Request $request)
{
    $schedules = EmployeeSchedule::with(['employee.user', 'shift'])
        ->when($request->employee_id, function ($query, $employeeId) {
            $query->where('employee_id', $employeeId);
        })
        ->when($request->date_from, function ($query, $dateFrom) {
            $query->where('schedule_date', '>=', $dateFrom);
        })
        ->when($request->date_to, function ($query, $dateTo) {
            $query->where('schedule_date', '<=', $dateTo);
        })
        ->orderBy('schedule_date', 'desc')
        ->paginate(20);

    return Inertia::render('Admin/Employee/Schedules/Index', [
        'schedules' => $schedules,
        'employees' => Employee::with('user')->get(),
        'shifts' => Shift::all(),
    ]);
}
```

**Giải thích:**
- `with(['employee.user', 'shift'])`: Eager loading để tránh N+1 query problem
- `when()`: Điều kiện lọc động (chỉ áp dụng nếu có giá trị)
- `paginate(20)`: Phân trang, mỗi trang 20 records

#### 4.2. Method `getWeeklySchedules()` - API lấy lịch theo tuần

```php
/**
 * API: Lấy lịch làm việc theo tuần với thông tin nhân viên và lương dự kiến
 * 
 * @param Request $request
 * @return \Illuminate\Http\JsonResponse
 */
public function getWeeklySchedules(Request $request)
{
    // Tính tuần từ thứ 2 đến chủ nhật
    $weekStart = $request->get('week_start', date('Y-m-d', strtotime('monday this week')));
    $weekEnd = date('Y-m-d', strtotime($weekStart . ' +6 days'));
    
    // Lấy tất cả nhân viên với relationships
    $employees = Employee::with(['user', 'branch', 'department', 'jobTitle'])->get();
    
    // Lấy lịch làm việc trong tuần và group theo employee_id
    $schedules = EmployeeSchedule::with(['shift'])
        ->whereBetween('schedule_date', [$weekStart, $weekEnd])
        ->get()
        ->groupBy('employee_id');
    
    // Format dữ liệu cho frontend
    $result = $employees->map(function ($employee) use ($schedules) {
        $employeeSchedules = $schedules->get($employee->id, collect());
        
        // Tính số ca làm việc trong tuần
        $shiftCount = $employeeSchedules->count();
        
        // Tính lương dự kiến
        $estimatedSalary = 0;
        if ($employee->salary_type === 'fixed') {
            // Giả sử tháng có 22 ngày làm việc
            $estimatedSalary = ($employee->salary_level / 22) * ($shiftCount / 7) * 7;
        }
        
        return [
            'employee' => $employee,
            'schedules' => $employeeSchedules->groupBy(function($schedule) {
                return $schedule->schedule_date->format('Y-m-d');
            })->map(function ($daySchedules) {
                return $daySchedules->map(function ($schedule) {
                    return [
                        'id' => $schedule->id,
                        'shift' => $schedule->shift,
                        'schedule_date' => $schedule->schedule_date->format('Y-m-d'),
                        'notes' => $schedule->notes,
                    ];
                })->values();
            })->toArray(),
            'shift_count' => $shiftCount,
            'estimated_salary' => $estimatedSalary,
        ];
    });
    
    return response()->json([
        'week_start' => $weekStart,
        'week_end' => $weekEnd,
        'employees' => $result,
    ]);
}
```

**Format dữ liệu trả về:**

```json
{
  "week_start": "2025-11-06",
  "week_end": "2025-11-12",
  "employees": [
    {
      "employee": {
        "id": 1,
        "full_name": "Nguyễn Văn A",
        "employee_code": "NV0001",
        ...
      },
      "schedules": {
        "2025-11-06": [
          {
            "id": 1,
            "shift": {
              "id": 1,
              "name": "Buổi Sáng",
              "start_time": "07:00:00",
              "end_time": "11:00:00"
            },
            "schedule_date": "2025-11-06",
            "notes": null
          },
          {
            "id": 2,
            "shift": {
              "id": 2,
              "name": "Buổi Chiều",
              ...
            },
            "schedule_date": "2025-11-06",
            "notes": null
          }
        ],
        "2025-11-07": [...]
      },
      "shift_count": 5,
      "estimated_salary": 636363.64
    }
  ]
}
```

#### 4.3. Method `store()` - Tạo lịch mới

```php
/**
 * Lưu lịch làm việc mới
 * 
 * @param StoreScheduleRequest $request
 * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
 */
public function store(StoreScheduleRequest $request)
{
    try {
        // Tạo lịch mới với dữ liệu đã được validate
        $schedule = EmployeeSchedule::create($request->validated());
        
        // Load relationships để trả về đầy đủ thông tin
        $schedule->load(['employee.user', 'shift']);

        // Trả về JSON nếu là AJAX request
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Thêm lịch làm việc thành công!',
                'schedule' => $schedule
            ], 201);
        }

        // Trả về redirect nếu là form submit thông thường
        return redirect()->back()->with('success', 'Thêm lịch làm việc thành công!');
        
    } catch (Exception $e) {
        // Xử lý lỗi duplicate entry (unique constraint)
        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
            $message = 'Nhân viên đã có lịch làm việc cho ca này trong ngày đã chọn!';
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 422);
            }
            
            return redirect()->back()
                ->with('error', $message)
                ->withInput();
        }

        // Xử lý lỗi khác
        $message = 'Có lỗi xảy ra: ' . $e->getMessage();
        
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $message
            ], 500);
        }

        return redirect()->back()
            ->with('error', $message)
            ->withInput();
    }
}
```

**Luồng xử lý:**

1. Request đến → Laravel tự động validate qua `StoreScheduleRequest`
2. Nếu hợp lệ → Tạo record trong database
3. Nếu duplicate → Trả về lỗi 422 với thông báo
4. Nếu lỗi khác → Trả về lỗi 500

#### 4.4. Method `update()` - Cập nhật lịch

```php
/**
 * Cập nhật lịch làm việc
 * 
 * @param StoreScheduleRequest $request
 * @param int $id
 * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
 */
public function update(StoreScheduleRequest $request, $id)
{
    try {
        // Tìm lịch cần cập nhật (throw 404 nếu không tìm thấy)
        $schedule = EmployeeSchedule::findOrFail($id);
        
        // Cập nhật với dữ liệu đã được validate
        $schedule->update($request->validated());
        
        // Load relationships
        $schedule->load(['employee.user', 'shift']);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật lịch làm việc thành công!',
                'schedule' => $schedule
            ]);
        }

        return redirect()->back()->with('success', 'Cập nhật lịch làm việc thành công!');
        
    } catch (Exception $e) {
        $message = 'Có lỗi xảy ra: ' . $e->getMessage();
        
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $message
            ], 500);
        }

        return redirect()->back()
            ->with('error', $message)
            ->withInput();
    }
}
```

#### 4.5. Method `destroy()` - Xóa lịch

```php
/**
 * Xóa lịch làm việc
 * 
 * @param int $id
 * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
 */
public function destroy($id)
{
    try {
        $schedule = EmployeeSchedule::findOrFail($id);
        $schedule->delete();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Xóa lịch làm việc thành công!'
            ]);
        }

        return redirect()->back()->with('success', 'Xóa lịch làm việc thành công!');
        
    } catch (Exception $e) {
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
        
        return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
    }
}
```

---

### 5. Routes

**File:** `routes/admin.php`

```php
// Employee Schedules
Route::prefix('employee-schedules')->name('employee-schedules.')->group(function () {
    // Trang danh sách (Inertia)
    Route::get('/', [ScheduleController::class, 'index'])->name('index');
    
    // API endpoints
    Route::get('/api', [ScheduleController::class, 'getSchedules'])->name('api');
    Route::get('/api/weekly', [ScheduleController::class, 'getWeeklySchedules'])->name('api.weekly');
    Route::post('/', [ScheduleController::class, 'store'])->name('store');
    Route::put('/{schedule}', [ScheduleController::class, 'update'])->name('update');
    Route::delete('/{schedule}', [ScheduleController::class, 'destroy'])->name('destroy');
});
```

**Bảng routes:**

| Route Name | Method | URL | Controller Method | Mục đích |
|------------|--------|-----|-------------------|----------|
| `employee-schedules.index` | GET | `/admin/employee-schedules` | `index()` | Trang danh sách (Inertia) |
| `employee-schedules.api` | GET | `/admin/employee-schedules/api` | `getSchedules()` | API lấy lịch (có filter) |
| `employee-schedules.api.weekly` | GET | `/admin/employee-schedules/api/weekly` | `getWeeklySchedules()` | API lấy lịch theo tuần |
| `employee-schedules.store` | POST | `/admin/employee-schedules` | `store()` | Tạo lịch mới |
| `employee-schedules.update` | PUT | `/admin/employee-schedules/{id}` | `update()` | Cập nhật lịch |
| `employee-schedules.destroy` | DELETE | `/admin/employee-schedules/{id}` | `destroy()` | Xóa lịch |

---

## 🎨 Frontend Implementation

### 1. Component chính: Index.vue

**File:** `resources/js/Pages/Admin/Employee/Schedules/Index.vue`

#### Cấu trúc component

```vue
<template>
  <div class="schedule-page">
    <!-- Header với search và week navigation -->
    <div class="schedule-header">...</div>
    
    <!-- Schedule Grid -->
    <div class="schedule-grid-container">
      <div class="schedule-table">
        <!-- Header Row: Nhân viên | Thứ 2 | Thứ 3 | ... | Lương dự kiến -->
        <!-- Employee Rows: Hiển thị từng nhân viên và lịch làm việc -->
      </div>
    </div>
    
    <!-- Modal thêm/sửa -->
    <Dialog>
      <CreateScheduleModal />
    </Dialog>
  </div>
</template>
```

#### Data properties

```javascript
data() {
  return {
    loading: false,              // Trạng thái loading
    searchQuery: '',              // Từ khóa tìm kiếm
    selectedEmployeeId: null,     // ID nhân viên được chọn (filter)
    weekStart: null,              // Ngày bắt đầu tuần (YYYY-MM-DD)
    scheduleData: [],             // Dữ liệu lịch làm việc từ API
    allEmployees: [],             // Danh sách tất cả nhân viên (cho search)
    showScheduleModal: false,     // Hiển thị modal thêm/sửa
    selectedEmployee: null,       // Nhân viên được chọn trong modal
    selectedDate: null,            // Ngày được chọn trong modal
    editingSchedule: null,        // Lịch đang được sửa (null nếu thêm mới)
  }
}
```

#### Computed properties

```javascript
computed: {
  // Tính toán các ngày trong tuần (Thứ 2 - Chủ nhật)
  weekDays() {
    // Trả về array 7 ngày với thông tin: date, dayName, dayNumber, isToday
  },
  
  // Text hiển thị tuần: "Tuần 44 - Th. 11 2025"
  weekDisplayText() {
    // Tính số tuần và format text
  },
  
  // Filter dữ liệu theo search query
  filteredScheduleData() {
    // Lọc nhân viên theo searchQuery hoặc selectedEmployeeId
  }
}
```

#### Methods quan trọng

```javascript
methods: {
  // Khởi tạo tuần hiện tại (thứ 2 của tuần này)
  initializeWeek() {
    const today = new Date()
    const dayOfWeek = today.getDay()
    const diff = dayOfWeek === 0 ? -6 : 1 - dayOfWeek
    const monday = new Date(today)
    monday.setDate(today.getDate() + diff)
    this.weekStart = monday.toISOString().split('T')[0]
  },
  
  // Load dữ liệu lịch làm việc từ API
  async loadSchedules() {
    const response = await axios.get('/admin/employee-schedules/api/weekly', {
      params: { week_start: this.weekStart }
    })
    this.scheduleData = response.data.employees || []
  },
  
  // Lấy lịch làm việc cho một ngày cụ thể
  getSchedulesForDay(item, date) {
    const schedules = item.schedules[date] || []
    return Array.isArray(schedules) ? schedules : []
  },
  
  // Xác định màu sắc cho shift block
  getShiftColorClass(shift) {
    const name = shift.name.toLowerCase()
    if (name.includes('sáng') || name.includes('morning')) {
      return 'shift-morning'  // Màu cam
    } else if (name.includes('chiều') || name.includes('afternoon')) {
      return 'shift-afternoon' // Màu xanh dương
    } else if (name.includes('tối') || name.includes('evening')) {
      return 'shift-evening'   // Màu xanh lá
    }
    return 'shift-default'
  },
  
  // Mở modal thêm lịch
  openAddScheduleModal(employee, date) {
    this.selectedEmployee = employee
    this.selectedDate = date
    this.editingSchedule = null
    this.showScheduleModal = true
  },
  
  // Xử lý sau khi thêm/sửa thành công
  handleScheduleSaved() {
    this.closeScheduleModal()
    this.loadSchedules() // Reload lại dữ liệu
  }
}
```

---

### 2. Modal Component: CreateSchedule.vue

**File:** `resources/js/Pages/Admin/Employee/Schedules/Modals/CreateSchedule.vue`

#### Props

```javascript
props: {
  employee: {
    type: Object,
    required: true  // Nhân viên được chọn (read-only)
  },
  scheduleDate: {
    type: String,
    default: null   // Ngày mặc định khi thêm mới
  },
  schedule: {
    type: Object,
    default: null   // Dữ liệu lịch khi sửa (null khi thêm mới)
  }
}
```

#### Form data

```javascript
data() {
  return {
    formData: {
      employee_id: null,
      shift_id: null,
      schedule_date: null,
      notes: ''
    },
    shifts: [],      // Danh sách ca làm việc
    errors: {},      // Lỗi validation từ backend
    loading: false   // Trạng thái loading khi submit
  }
}
```

#### Method saveSchedule

```javascript
async saveSchedule() {
  this.loading = true
  this.errors = {}

  try {
    const payload = {
      employee_id: this.formData.employee_id,
      shift_id: this.formData.shift_id,
      schedule_date: this.formatDate(this.formData.schedule_date),
      notes: this.formData.notes || null
    }

    let response
    if (this.schedule && this.schedule.id) {
      // Update mode
      response = await axios.put(`/admin/employee-schedules/${this.schedule.id}`, payload)
    } else {
      // Create mode
      response = await axios.post('/admin/employee-schedules', payload)
    }

    // Hiển thị thông báo thành công
    this.$toast.add({
      severity: 'success',
      summary: 'Thành công',
      detail: response.data.message,
      life: 3000
    })

    // Emit event để parent component reload dữ liệu
    this.$emit('saved', response.data)
    
  } catch (error) {
    // Xử lý lỗi validation (422)
    if (error.response && error.response.status === 422) {
      this.errors = error.response.data.errors || {}
    }
  } finally {
    this.loading = false
  }
}
```

---

## 🔌 API Endpoints

### 1. GET `/admin/employee-schedules/api/weekly`

**Mục đích:** Lấy lịch làm việc theo tuần

**Query Parameters:**

| Parameter | Type | Required | Mô tả |
|-----------|------|----------|-------|
| `week_start` | string (YYYY-MM-DD) | No | Ngày bắt đầu tuần (mặc định: thứ 2 tuần này) |

**Ví dụ request:**

```javascript
axios.get('/admin/employee-schedules/api/weekly', {
  params: {
    week_start: '2025-11-06'
  }
})
```

**Response:**

```json
{
  "week_start": "2025-11-06",
  "week_end": "2025-11-12",
  "employees": [
    {
      "employee": {
        "id": 1,
        "full_name": "Nguyễn Văn A",
        "employee_code": "NV0001",
        "salary_type": "fixed",
        "salary_level": 7000000
      },
      "schedules": {
        "2025-11-06": [
          {
            "id": 1,
            "shift": {
              "id": 1,
              "name": "Buổi Sáng",
              "start_time": "07:00:00",
              "end_time": "11:00:00"
            },
            "schedule_date": "2025-11-06",
            "notes": null
          }
        ]
      },
      "shift_count": 5,
      "estimated_salary": 636363.64
    }
  ]
}
```

---

### 2. POST `/admin/employee-schedules`

**Mục đích:** Tạo lịch làm việc mới

**Request Body:**

```json
{
  "employee_id": 1,
  "shift_id": 1,
  "schedule_date": "2025-11-06",
  "notes": "Làm thêm giờ"
}
```

**Response (Success - 201):**

```json
{
  "success": true,
  "message": "Thêm lịch làm việc thành công!",
  "schedule": {
    "id": 1,
    "employee_id": 1,
    "shift_id": 1,
    "schedule_date": "2025-11-06",
    "notes": "Làm thêm giờ",
    "employee": {...},
    "shift": {...}
  }
}
```

**Response (Error - 422):**

```json
{
  "success": false,
  "message": "Nhân viên đã có lịch làm việc cho ca này trong ngày đã chọn!",
  "errors": {
    "shift_id": ["Nhân viên đã có lịch làm việc cho ca này trong ngày đã chọn!"]
  }
}
```

---

### 3. PUT `/admin/employee-schedules/{id}`

**Mục đích:** Cập nhật lịch làm việc

**Request Body:** (giống POST)

**Response (Success - 200):**

```json
{
  "success": true,
  "message": "Cập nhật lịch làm việc thành công!",
  "schedule": {...}
}
```

---

### 4. DELETE `/admin/employee-schedules/{id}`

**Mục đích:** Xóa lịch làm việc

**Response (Success - 200):**

```json
{
  "success": true,
  "message": "Xóa lịch làm việc thành công!"
}
```

---

## 🔄 Luồng hoạt động

### Luồng 1: Thêm lịch làm việc mới

```
1. User click "+ Thêm lịch" trong grid
   ↓
2. Frontend: openAddScheduleModal(employee, date)
   - Set selectedEmployee = employee
   - Set selectedDate = date
   - Set editingSchedule = null
   - Show modal
   ↓
3. User chọn ca làm việc và click "Thêm lịch"
   ↓
4. Frontend: saveSchedule()
   - Format payload
   - POST /admin/employee-schedules
   ↓
5. Backend: Route → ScheduleController@store
   ↓
6. Laravel: Auto validate qua StoreScheduleRequest
   - Kiểm tra employee_id exists
   - Kiểm tra shift_id exists
   - Kiểm tra schedule_date valid
   ↓
7. Backend: EmployeeSchedule::create()
   - Lưu vào database
   - Unique constraint kiểm tra duplicate
   ↓
8. Backend: Return JSON response
   {
     "success": true,
     "message": "Thêm lịch làm việc thành công!",
     "schedule": {...}
   }
   ↓
9. Frontend: handleScheduleSaved()
   - Close modal
   - loadSchedules() → Reload dữ liệu
   ↓
10. Frontend: Render lại grid với shift block mới
```

### Luồng 2: Xem lịch theo tuần

```
1. Component mount
   ↓
2. initializeWeek()
   - Tính thứ 2 của tuần hiện tại
   - Set weekStart = "2025-11-06"
   ↓
3. loadSchedules()
   - GET /admin/employee-schedules/api/weekly?week_start=2025-11-06
   ↓
4. Backend: ScheduleController@getWeeklySchedules()
   - Lấy tất cả nhân viên
   - Lấy lịch trong tuần (weekStart → weekEnd)
   - Group theo employee_id
   - Format dữ liệu theo ngày
   ↓
5. Backend: Return JSON
   {
     "employees": [
       {
         "employee": {...},
         "schedules": {
           "2025-11-06": [...],
           "2025-11-07": [...]
         }
       }
     ]
   }
   ↓
6. Frontend: Render grid
   - Vòng lặp employees
   - Vòng lặp weekDays (7 ngày)
   - getSchedulesForDay() → Lấy schedules cho ngày
   - Render shift blocks với màu sắc
```

### Luồng 3: Điều hướng tuần

```
1. User click "Tuần trước" (previousWeek)
   ↓
2. Frontend: previousWeek()
   - weekStart = weekStart - 7 days
   - loadSchedules()
   ↓
3. API call với week_start mới
   ↓
4. Render lại grid với dữ liệu tuần mới
```

---

## 💡 Ví dụ sử dụng

### Ví dụ 1: Tạo lịch làm việc qua API

```javascript
// Sử dụng axios
const response = await axios.post('/admin/employee-schedules', {
  employee_id: 1,
  shift_id: 1,  // Buổi Sáng
  schedule_date: '2025-11-06',
  notes: 'Làm thêm giờ'
})

console.log(response.data)
// {
//   "success": true,
//   "message": "Thêm lịch làm việc thành công!",
//   "schedule": {...}
// }
```

### Ví dụ 2: Lấy lịch theo tuần

```javascript
const response = await axios.get('/admin/employee-schedules/api/weekly', {
  params: {
    week_start: '2025-11-06'
  }
})

const employees = response.data.employees

// Lặp qua từng nhân viên
employees.forEach(emp => {
  console.log(`Nhân viên: ${emp.employee.full_name}`)
  console.log(`Số ca: ${emp.shift_count}`)
  
  // Lặp qua từng ngày
  Object.keys(emp.schedules).forEach(date => {
    console.log(`Ngày ${date}:`)
    emp.schedules[date].forEach(schedule => {
      console.log(`  - ${schedule.shift.name}`)
    })
  })
})
```

### Ví dụ 3: Sử dụng Model trong Controller

```php
// Lấy lịch làm việc của một nhân viên
$schedules = EmployeeSchedule::where('employee_id', 1)
    ->where('schedule_date', '>=', '2025-11-06')
    ->with(['shift'])
    ->get();

foreach ($schedules as $schedule) {
    echo $schedule->schedule_date->format('d/m/Y');
    echo $schedule->shift->name;
    echo $schedule->shift->start_time;
}
```

---

## 🐛 Troubleshooting

### Lỗi 1: "Duplicate entry" khi thêm lịch

**Nguyên nhân:** Nhân viên đã có cùng ca trong cùng ngày

**Giải pháp:**
- Kiểm tra unique constraint: `(employee_id, shift_id, schedule_date)`
- Đảm bảo không thêm trùng lặp
- Backend đã xử lý và trả về lỗi 422 với thông báo rõ ràng

### Lỗi 2: Không hiển thị shift blocks sau khi thêm

**Nguyên nhân:** 
- Frontend không reload dữ liệu sau khi thêm
- Format dữ liệu từ API không đúng

**Giải pháp:**
- Đảm bảo gọi `loadSchedules()` sau khi thêm thành công
- Kiểm tra format dữ liệu: `schedules` phải là object với key là date string

### Lỗi 3: Foreign key constraint fails

**Nguyên nhân:** 
- `employee_id` hoặc `shift_id` không tồn tại trong database

**Giải pháp:**
- Validation đã kiểm tra `exists:employees,id` và `exists:shifts,id`
- Đảm bảo nhân viên và ca làm việc đã được tạo trước

### Lỗi 4: Tính lương dự kiến sai

**Nguyên nhân:** 
- Logic tính toán trong `getWeeklySchedules()` chưa chính xác

**Giải pháp:**
- Kiểm tra lại công thức tính lương
- Có thể cần tính theo giờ làm việc thực tế từ shift

---

## 📝 Checklist triển khai

Khi triển khai chức năng này, đảm bảo:

- [ ] Migration đã chạy thành công
- [ ] Model `EmployeeSchedule` có đầy đủ relationships
- [ ] Request validation đã được cấu hình đúng
- [ ] Controller có đầy đủ các method: index, store, update, destroy, getWeeklySchedules
- [ ] Routes đã được định nghĩa trong `admin.php`
- [ ] Frontend component `Index.vue` đã được tạo
- [ ] Frontend modal `CreateSchedule.vue` đã được tạo
- [ ] API endpoints hoạt động đúng
- [ ] Unique constraint hoạt động (không cho phép duplicate)
- [ ] Foreign keys hoạt động (cascade delete)
- [ ] Hiển thị grid theo tuần đúng
- [ ] Tính lương dự kiến đúng

---

## 📚 Tài liệu tham khảo

- [Laravel Migrations](https://laravel.com/docs/migrations)
- [Laravel Eloquent Relationships](https://laravel.com/docs/eloquent-relationships)
- [Laravel Form Requests](https://laravel.com/docs/validation#form-request-validation)
- [Vue.js Documentation](https://vuejs.org/)
- [PrimeVue Components](https://primevue.org/)

---

**Tài liệu được tạo bởi:** AI Assistant  
**Ngày cập nhật:** 06/11/2025  
**Phiên bản:** 1.0

