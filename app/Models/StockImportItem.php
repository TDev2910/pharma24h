<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockImportItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_import_id',
        'product_type',
        'product_id',
        'quantity',
        'unit_price',
        'discount',
        'total_price',
        'note'
    ];

    // Relationships
    public function stockImport()
    {
        return $this->belongsTo(StockImport::class);
    }

    public function product()
    {
        if ($this->product_type === 'medicine') {
            return $this->belongsTo(Medicine::class, 'product_id');
        } else {
            return $this->belongsTo(Goods::class, 'product_id');
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
