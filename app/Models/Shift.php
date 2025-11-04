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
     * Quan hệ với Branch
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Quan hệ với Employee Schedule
     */
    public function schedules()
    {
        return $this->hasMany(EmployeeSchedule::class);
    }
}
