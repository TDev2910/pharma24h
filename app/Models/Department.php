<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Quan hệ: 1 Department có nhiều Employees
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
