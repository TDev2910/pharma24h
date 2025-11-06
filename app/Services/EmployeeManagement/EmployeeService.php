<?php

namespace App\Services\EmployeeManagement;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;

class EmployeeService
{
    /**
     * Tạo nhân viên mới (với Transaction)
     */
    public function createEmployee(array $data): Employee
    {
        DB::beginTransaction();
        
        try {
            //Tạo User (Tài khoản đăng nhập)
            $user = User::create([
                'name' => $data['full_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'] ?? '123456'),
                'role' => 'staff',
            ]);

            //Tạo Employee Code nếu chưa có
            if (empty($data['employee_code'])) {
                $data['employee_code'] = Employee::generateEmployeeCode();
            }

            //Tạo Employee
            $employee = Employee::create([
                'user_id' => $user->id,
                'full_name' => $data['full_name'],
                'phone_number' => $data['phone_number'] ?? null,
                'employee_code' => $data['employee_code'],
                'salary_type' => $data['salary_type'],
                'salary_level' => $data['salary_level'],
                'department_id' => $data['department_id'] ?? null,
                'job_title_id' => $data['job_title_id'] ?? null,
                'branch_id' => $data['branch_id'] ?? null,
                'start_date' => $data['start_date'] ?? null,
                'dob' => $data['dob'] ?? null,
                'gender' => $data['gender'] ?? null,
                'address' => $data['address'] ?? null,
                'id_card_number' => $data['id_card_number'] ?? null,
            ]);

            //Tạo Phụ cấp
            if (!empty($data['allowances']) && is_array($data['allowances'])) {
                $employee->allowances()->createMany($data['allowances']);
            }

            //Tạo Chỉ tiêu
            if (!empty($data['targets']) && is_array($data['targets'])) {
                $employee->targets()->createMany($data['targets']);
            }

            //Tạo Giảm trừ
            if (!empty($data['deductions']) && is_array($data['deductions'])) {
                $employee->deductions()->createMany($data['deductions']);
            }

            DB::commit();

            $employee->load([
                'user', 
                'department', 
                'jobTitle', 
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
     * Cập nhật nhân viên
     */
    public function updateEmployee(Employee $employee, array $data): Employee
    {
        DB::beginTransaction();
        
        try {
            // Cập nhật User
            $employee->user->update([
                'name' => $data['full_name'],
                'email' => $data['email'],
            ]);

            if (!empty($data['password'])) {
                $employee->user->update([
                    'password' => Hash::make($data['password']),
                ]);
            }

            // Cập nhật Employee
            $employee->update([
                'full_name' => $data['full_name'],
                'phone_number' => $data['phone_number'] ?? null,
                'employee_code' => $data['employee_code'] ?? $employee->employee_code,
                'salary_type' => $data['salary_type'],
                'salary_level' => $data['salary_level'],
                'department_id' => $data['department_id'] ?? null,
                'job_title_id' => $data['job_title_id'] ?? null,
                'branch_id' => $data['branch_id'] ?? null,
                'start_date' => $data['start_date'] ?? null,
                'dob' => $data['dob'] ?? null,
                'gender' => $data['gender'] ?? null,
                'address' => $data['address'] ?? null,
                'id_card_number' => $data['id_card_number'] ?? null,
            ]);

            // Cập nhật các mảng động (xóa và tạo lại)
            $employee->allowances()->delete();
            if (!empty($data['allowances']) && is_array($data['allowances'])) {
                $employee->allowances()->createMany($data['allowances']);
            }

            $employee->targets()->delete();
            if (!empty($data['targets']) && is_array($data['targets'])) {
                $employee->targets()->createMany($data['targets']);
            }

            $employee->deductions()->delete();
            if (!empty($data['deductions']) && is_array($data['deductions'])) {
                $employee->deductions()->createMany($data['deductions']);
            }

            DB::commit();

            $employee->refresh();
            $employee->load([
                'user', 
                'department', 
                'jobTitle', 
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
     * Xóa nhân viên
     */
    public function deleteEmployee(Employee $employee): bool
    {
        DB::beginTransaction();
        
        try {
            $employee->user->delete();
            DB::commit();
            return true;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Lấy danh sách nhân viên
     */
    public function getEmployees(array $filters = [], int $perPage = 15)
    {
        $query = Employee::with(['user', 'department', 'jobTitle', 'branch']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('employee_code', 'LIKE', "%{$search}%")
                  ->orWhere('phone_number', 'LIKE', "%{$search}%");
            });
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['job_title_id'])) {
            $query->where('job_title_id', $filters['job_title_id']);
        }

        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Lấy chi tiết nhân viên
     */
    public function getEmployeeDetail(int $employeeId): Employee
    {
        return Employee::with([
            'user',
            'department',
            'jobTitle',
            'branch',
            'allowances',
            'targets',
            'deductions',
            'schedules.shift'
        ])->findOrFail($employeeId);
    }
}