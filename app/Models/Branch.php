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
     * Quan hệ với Employee
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Quan hệ với Shift
     */
    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }
}
