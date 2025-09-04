<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'ma_hang',    
        'ma_vach',
        'ten_dich_vu',          
        'nhom_hang_id',      
        'gia_dich_vu',
        'mo_ta',               
        'image',             
        'ban_truc_tiep'            
    ];

    protected $casts = [
        'gia_dich_vu' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relationship to ProductCategory (Nhóm dịch vụ)
     */
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'nhom_hang_id');
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
        return $query->where('nhom_hang_id', $categoryId);
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->gia_dich_vu, 0, ',', '.') . ' VND';
    }
    
    /**
     * Accessor for backward compatibility - map gia_ban to gia_dich_vu
     */
    public function getGiaBanAttribute()
    {
        return $this->gia_dich_vu;
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