<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone_number',
        'description'
    ];

    /**
     * Quan hệ: 1 Branch có nhiều Employees
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Quan hệ: 1 Branch có nhiều Shifts
     */
    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }
}
