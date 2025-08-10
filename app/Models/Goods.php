<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Goods extends Model
{
    use HasFactory;

    protected $fillable = [
        'ma_hang',
        'ma_vach',
        'ten_hang_hoa',
        'nhom_hang_id',
        'gia_von',
        'gia_ban',
        'ton_kho',
        'ton_thap_nhat',
        'ton_cao_nhat',
        'quan_ly_theo_lo',
        'quy_cach_dong_goi',
        'manufacturer_id',
        'nuoc_san_xuat',
        'position_id',
        'trong_luong',
        'don_vi_tinh',
        'ban_truc_tiep',
        'mo_ta',
        'image',
        'khach_dat',
    ];

    // Get image URL - giống như Post model
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/products/đạt.jpg');
    }
    // Relationships
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'nhom_hang_id');
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    // Accessors
    public function getGiaBanFormattedAttribute()
    {
        return number_format($this->gia_ban) . ' VNĐ';
    }

    public function getGiaVonFormattedAttribute()
    {
        return number_format($this->gia_von) . ' VNĐ';
    }

    public function getTonKhoFormattedAttribute()
    {
        return number_format($this->ton_kho);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('ban_truc_tiep', true);
    }

    public function scopeSearchByName($query, $name)
    {
        return $query->where('ten_hang_hoa', 'like', '%' . $name . '%');
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('nhom_hang_id', $categoryId);
    }
}