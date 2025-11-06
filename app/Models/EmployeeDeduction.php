<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeDeduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'reason',
        'amount',
        'frequency'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Quan hệ: N Deductions thuộc 1 Employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
