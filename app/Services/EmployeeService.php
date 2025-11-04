<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;

class EmployeeService
{
    /**
     * Tạo nhân viên mới (với Transaction)
     * 
     * @param array $data
     * @return Employee
     * @throws Exception
     */
    public function createEmployee(array $data): Employee
    {
        DB::beginTransaction();
        
        try {
            // BƯỚC 1: Tạo User (Tài khoản đăng nhập)
            $user = User::create([
                'name' => $data['full_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'] ?? '123456'), // Mật khẩu mặc định
                'role' => 'staff', // Vai trò nhân viên
            ]);

            // BƯỚC 2: Tạo Employee Code nếu chưa có
            if (empty($data['employee_code'])) {
                $data['employee_code'] = Employee::generateEmployeeCode();
            }

            // BƯỚC 3: Tạo Employee (Hồ sơ nhân sự)
            $employee = Employee::create([
                'user_id' => $user->id,
                'full_name' => $data['full_name'],
                'phone_number' => $data['phone_number'] ?? null,
                'employee_code' => $data['employee_code'],
                'salary_type' => $data['salary_type'],
                'salary_level' => $data['salary_level'],
                'department_id' => $data['department_id'] ?? null,
                'position_id' => $data['position_id'] ?? null,
                'branch_id' => $data['branch_id'] ?? null,
                'start_date' => $data['start_date'] ?? null,
                'dob' => $data['dob'] ?? null,
                'gender' => $data['gender'] ?? null,
                'address' => $data['address'] ?? null,
                'id_card_number' => $data['id_card_number'] ?? null,
            ]);

            // BƯỚC 4: Tạo Phụ cấp (nếu có)
            if (!empty($data['allowances']) && is_array($data['allowances'])) {
                $employee->allowances()->createMany($data['allowances']);
            }

            // BƯỚC 5: Tạo Chỉ tiêu thưởng (nếu có)
            if (!empty($data['targets']) && is_array($data['targets'])) {
                $employee->targets()->createMany($data['targets']);
            }

            // BƯỚC 6: Tạo Giảm trừ (nếu có)
            if (!empty($data['deductions']) && is_array($data['deductions'])) {
                $employee->deductions()->createMany($data['deductions']);
            }

            DB::commit();

            // Load relationships trước khi trả về
            $employee->load([
                'user', 
                'department', 
                'position', 
                'branch',
                'allowances',
                'targets',
                'deductions'
            ]);

            return $employee;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Cập nhật thông tin nhân viên (với Transaction)
     * 
     * @param Employee $employee
     * @param array $data
     * @return Employee
     * @throws Exception
     */
    public function updateEmployee(Employee $employee, array $data): Employee
    {
        DB::beginTransaction();
        
        try {
            // BƯỚC 1: Cập nhật User
            $employee->user->update([
                'name' => $data['full_name'],
                'email' => $data['email'],
            ]);

            // Nếu có password mới thì cập nhật
            if (!empty($data['password'])) {
                $employee->user->update([
                    'password' => Hash::make($data['password']),
                ]);
            }

            // BƯỚC 2: Cập nhật Employee
            $employee->update([
                'full_name' => $data['full_name'],
                'phone_number' => $data['phone_number'] ?? null,
                'employee_code' => $data['employee_code'] ?? $employee->employee_code,
                'salary_type' => $data['salary_type'],
                'salary_level' => $data['salary_level'],
                'department_id' => $data['department_id'] ?? null,
                'position_id' => $data['position_id'] ?? null,
                'branch_id' => $data['branch_id'] ?? null,
                'start_date' => $data['start_date'] ?? null,
                'dob' => $data['dob'] ?? null,
                'gender' => $data['gender'] ?? null,
                'address' => $data['address'] ?? null,
                'id_card_number' => $data['id_card_number'] ?? null,
            ]);

            // BƯỚC 3: Cập nhật Phụ cấp (Xóa hết và tạo lại)
            $employee->allowances()->delete();
            if (!empty($data['allowances']) && is_array($data['allowances'])) {
                $employee->allowances()->createMany($data['allowances']);
            }

            // BƯỚC 4: Cập nhật Chỉ tiêu thưởng (Xóa hết và tạo lại)
            $employee->targets()->delete();
            if (!empty($data['targets']) && is_array($data['targets'])) {
                $employee->targets()->createMany($data['targets']);
            }

            // BƯỚC 5: Cập nhật Giảm trừ (Xóa hết và tạo lại)
            $employee->deductions()->delete();
            if (!empty($data['deductions']) && is_array($data['deductions'])) {
                $employee->deductions()->createMany($data['deductions']);
            }

            DB::commit();

            // Load lại relationships
            $employee->refresh();
            $employee->load([
                'user', 
                'department', 
                'position', 
                'branch',
                'allowances',
                'targets',
                'deductions'
            ]);

            return $employee;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Xóa nhân viên (sẽ cascade xóa user và các dữ liệu liên quan)
     * 
     * @param Employee $employee
     * @return bool
     * @throws Exception
     */
    public function deleteEmployee(Employee $employee): bool
    {
        DB::beginTransaction();
        
        try {
            // Xóa User (sẽ cascade xóa Employee do foreign key)
            $employee->user->delete();
            
            DB::commit();
            return true;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Lấy danh sách nhân viên với filter và pagination
     * 
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getEmployees(array $filters = [], int $perPage = 15)
    {
        $query = Employee::with(['user', 'department', 'position', 'branch']);

        // Filter theo tên hoặc mã nhân viên
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('employee_code', 'LIKE', "%{$search}%")
                  ->orWhere('phone_number', 'LIKE', "%{$search}%");
            });
        }

        // Filter theo phòng ban
        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        // Filter theo vị trí
        if (!empty($filters['position_id'])) {
            $query->where('position_id', $filters['position_id']);
        }

        // Filter theo chi nhánh
        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Lấy chi tiết nhân viên với tất cả relationships
     * 
     * @param int $employeeId
     * @return Employee
     */
    public function getEmployeeDetail(int $employeeId): Employee
    {
        return Employee::with([
            'user',
            'department',
            'position',
            'branch',
            'allowances',
            'targets',
            'deductions',
            'schedules.shift'
        ])->findOrFail($employeeId);
    }
}
