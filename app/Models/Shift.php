<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'branch_id'
    ];

    /**
     * Quan hệ: N Shifts thuộc 1 Branch
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Quan hệ: 1 Shift có nhiều Schedules
     */
    public function schedules()
    {
        return $this->hasMany(EmployeeSchedule::class);
    }
}
