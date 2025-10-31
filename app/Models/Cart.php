<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'item_id',
        'item_type',
        'quantity',
        'price',
        'name',
        'image',
        'is_promotion',
    ];
    
    // Quan hệ với user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Lấy thông tin đầy đủ của sản phẩm
    public function getItemDetails()
    {
        if ($this->item_type === 'medicine') {
            return Medicine::find($this->item_id);
        } elseif ($this->item_type === 'goods') {
            return Goods::find($this->item_id);
        }
        return null;
    }
    
    // Tính tổng tiền của item
    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->price;
    }
    
    // Lấy URL của ảnh
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/products/đạt.jpg');
    }
}
