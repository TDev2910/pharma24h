<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_return_id',
        'product_type',
        'product_id',
        'quantity',
        'unit_price',
        'discount',
        'total_price',
        'note'
    ];

    // Relationships
    public function purchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class);
    }

    public function product()
    {
        if ($this->product_type === 'medicine') {
            return $this->belongsTo(\App\Models\Medicine::class, 'product_id');
        } else {
            return $this->belongsTo(\App\Models\Goods::class, 'product_id');
        }
    }

    // Accessors
    public function getProductNameAttribute()
    {
        if ($this->product_type === 'medicine') {
            return $this->product->ten_thuoc ?? 'N/A';
        } else {
            return $this->product->ten_hang_hoa ?? 'N/A';
        }
    }
}
