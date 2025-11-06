<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'activity_type',
        'target_amount',
        'bonus_type',
        'bonus_value'
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'bonus_value' => 'decimal:2',
    ];

    /**
     * Quan hệ: N Targets thuộc 1 Employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
