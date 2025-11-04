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
     * Quan hệ với Employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Quan hệ với Shift
     */
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
