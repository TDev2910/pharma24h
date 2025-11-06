<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeAllowance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'name',
        'amount',
        'type'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Quan hệ: N Allowances thuộc 1 Employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
