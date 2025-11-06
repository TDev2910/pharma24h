<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobTitle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Quan hệ: 1 JobTitle có nhiều Employees
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
