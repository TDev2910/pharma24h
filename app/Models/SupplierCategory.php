<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relationship với Supplier
     */
    public function suppliers()
    {
        return $this->hasMany(Supplier::class, 'category_id');
    }

    public function scopeActive($query) //lấy category đang hoạt động
    {
        return $query->where('status', 'active');
    }

    public function scopeOrdered($query) //sắp xếp theo tên
    {
        return $query->orderBy('name');
    }

    /**
     * Accessor cho status label
     */
    public function getStatusLabelAttribute()
    {
        return $this->status === 'active' ? 'Kích hoạt' : 'Tạm ngưng';
    }

    /**
     * Accessor cho status badge class
     */
    public function getStatusBadgeAttribute()
    {
        return $this->status === 'active' ? 'bg-success' : 'bg-secondary';
    }
}
