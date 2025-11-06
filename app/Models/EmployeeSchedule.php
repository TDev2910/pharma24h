<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'shift_id',
        'schedule_date',
        'notes'
    ];
    
    protected $casts = [
        'schedule_date' => 'date',
    ];

    /**
     * Quan hệ:  Một lịch làm việc thuộc về một nhân viên
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Quan hệ: Một lịch làm việc thuộc về một ca
     */
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
