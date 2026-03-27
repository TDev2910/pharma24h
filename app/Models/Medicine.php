<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medicine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ma_hang',
        'ma_vach',
        'ten_thuoc',
        'ten_viet_tat',
        'nhom_hang_id',
        'gia_von',
        'ton_kho',
        'gia_ban',
        'gia_khuyen_mai',
        'ton_khuyen_mai',
        'so_dang_ky',
        'hoat_chat',
        'ham_luong',
        'drugusage_id',
        'slug',
        'quy_cach_dong_goi',
        'manufacturer_id',
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
    protected $dates = ['deleted_at'];

    protected $appends = ['image_url', 'gia_ban_formatted'];


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

    public function drugRoute()
    {
        return $this->belongsTo(DrugRoute::class, 'drugusage_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function reviews() //tạo quan hệ với bảng product_reviews
    {
        return $this->morphMany(ProductReview::class, 'product');
    }

    // Accessors for price formatting
    public function getGiaBanFormattedAttribute()
    {
        return number_format($this->gia_ban, 0, ',', '.') . ' VND';
    }

    public function getGiaVonFormattedAttribute()
    {
        return number_format($this->gia_von, 0, ',', '.') . ' VND';
    }

    public static function generateProductCode() //tao mã hàng ngẫu nhiên
    {
        do {
            $code = str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
        } while (self::where('ma_hang', $code)->exists());

        return $code;
    }

    // Auto generate 8-digit barcode
    public static function generateBarcode() //tao mã vạch ngẫu nhiên
    {
        do {
            $barcode = str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
        } while (self::where('ma_vach', $barcode)->exists());

        return $barcode;
    }
}
