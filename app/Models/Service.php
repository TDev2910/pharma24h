<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'ma_dich_vu',    
        'ten_dich_vu',          
        'nhom_dich_vu_id',      
        'mo_ta',               
        'hinh_thuc',            
        'thoi_gian_thuc_hien', 
        'trang_thai',        // Status: 'kich_hoat', 'tam_ngung', 'luu_tam'
        'image',             
        'ghi_chu',           
        'created_by',          
        'updated_by'            
    ];

    protected $casts = [
        'gia_ban' => 'decimal:2',
        'thoi_gian_thuc_hien' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relationship to ProductCategory (Nhóm dịch vụ)
     */
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'nhom_dich_vu_id');
    }

    /**
     * Relationship to User who created the service
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship to User who last updated the service
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope for active services only
     */
    public function scopeActive($query)
    {
        return $query->where('trang_thai', 'kich_hoat');
    }

    /**
     * Scope for services by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('nhom_dich_vu_id', $categoryId);
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->gia_ban, 0, ',', '.') . ' đ';
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        $statuses = [
            'kich_hoat' => 'Kích hoạt',
            'tam_ngung' => 'Tạm ngưng',
            'luu_tam' => 'Lưu tạm'
        ];

        return $statuses[$this->trang_thai] ?? 'Không xác định';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'kich_hoat' => 'bg-success',
            'tam_ngung' => 'bg-warning',
            'luu_tam' => 'bg-secondary'
        ];

        return $badges[$this->trang_thai] ?? 'bg-secondary';
    }

    /**
     * Get service type label
     */
    public function getServiceTypeLabelAttribute()
    {
        $types = [
            'tai_nha_thuoc' => 'Tại nhà thuốc',
            'tai_nha_khach' => 'Tại nhà khách'
        ];

        return $types[$this->hinh_thuc] ?? 'Không xác định';
    }
}