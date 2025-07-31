<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'ma_hang',
        'ma_vach',
        'ten_thuoc',
        'ten_viet_tat',
        'nhom_hang_id',
        'gia_von',
        'gia_ban',
        'so_dang_ky',
        'hoat_chat',
        'ham_luong',
        'drugusage_id',
        'quy_cach_dong_goi',
        'manufacturer',
        'nuoc_san_xuat',
        'ton_thap_nhat',
        'ton_cao_nhat',
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
        return $this->belongsTo(Manufacturer::class, 'manufacturer');
    }

    public function drugRoute()
    {
        return $this->belongsTo(DrugRoute::class, 'drugusage_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }
}
