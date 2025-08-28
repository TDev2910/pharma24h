<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'ten_nha_cung_cap',
        'ma_nha_cung_cap',
        'dien_thoai',
        'email',
        'dia_chi',
        'khu_vuc',         // Tỉnh/Thành phố
        'phuong_xa',       // Quận/Huyện  
        'nhom_nha_cung_cap_id',
        'ghi_chu',
        'ten_cong_ty',
        'ma_so_thue',
        'trang_thai',
    ];

    public function category()
    {
        return $this->belongsTo(SupplierCategory::class, 'nhom_nha_cung_cap_id');
    }
    
    //scope tim kiem va loc
    public function scopeActive($query)
    {
        return $query->where('trang_thai', 'hoat_dong');
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('nhom_nha_cung_cap_id', $categoryId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('ten_nha_cung_cap', 'like', "%{$keyword}%");
    }

    public function getTenDayDuAttribute()
    {
        return $this->ten_nha_cung_cap . ' (' . $this->ma_nha_cung_cap . ')';
    }

    public function getDiaChiDayDuAttribute()
    {
        return implode(', ', array_filter([
            $this->dia_chi, $this->phuong_xa, $this->khu_vuc
        ]));
    }
}
