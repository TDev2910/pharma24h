<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'phone_number',
        'employee_code',
        'salary_type',
        'salary_level',
        'department_id',
        'job_title_id',
        'branch_id',
        'start_date',
        'dob',
        'gender',
        'address',
        'id_card_number'
    ];

    protected $casts = [
        'salary_level' => 'decimal:2',
        'start_date' => 'date',
        'dob' => 'date',
    ];

    /**
     * Quan hệ với User (Tài khoản đăng nhập)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Quan hệ với Department
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Quan hệ với JobTitle (Chức danh)
     */
    public function jobTitle()
    {
        return $this->belongsTo(JobTitle::class);
    }

    /**
     * Quan hệ với Branch
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Quan hệ với Phụ cấp
     */
    public function allowances()
    {
        return $this->hasMany(EmployeeAllowance::class);
    }

    /**
     * Quan hệ với Chỉ tiêu thưởng
     */
    public function targets()
    {
        return $this->hasMany(EmployeeTarget::class);
    }

    /**
     * Quan hệ với Giảm trừ
     */
    public function deductions()
    {
        return $this->hasMany(EmployeeDeduction::class);
    }

    /**
     * Quan hệ với Lịch làm việc
     */
    public function schedules()
    {
        return $this->hasMany(EmployeeSchedule::class);
    }

    /**
     * Tạo mã nhân viên tự động
     */
    public static function generateEmployeeCode()
    {
        $prefix = 'NV';
        $lastEmployee = self::where('employee_code', 'LIKE', $prefix . '%')
            ->orderBy('employee_code', 'desc')
            ->first();

        if (!$lastEmployee) {
            return $prefix . '0001';
        }

        $lastNumber = (int) substr($lastEmployee->employee_code, strlen($prefix));
        $newNumber = $lastNumber + 1;

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
