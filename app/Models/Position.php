<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    /**
     * Scope để lấy các vị trí đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('name', '!=', '');
    }

    /**
     * Scope để sắp xếp theo tên
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Lấy tên vị trí
     */
    public function getDisplayNameAttribute()
    {
        return $this->name;
    }

    /**
     * Kiểm tra xem vị trí có hợp lệ không
     */
    public function isValid()
    {
        return !empty($this->name);
    }
}
