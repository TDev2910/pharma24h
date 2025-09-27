<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockImportPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_import_id',
        'payment_method',
        'amount',
        'note'
    ];

    // Relationships
    public function stockImport()
    {
        return $this->belongsTo(StockImport::class);
    }

    // Accessors
    public function getPaymentMethodTextAttribute()
    {
        switch ($this->payment_method) {
            case 'cash':
                return 'Tiền mặt';
            case 'card':
                return 'Thẻ';
            case 'transfer':
                return 'Chuyển khoản';
            default:
                return 'Không xác định';
        }
    }

    public function getAmountFormattedAttribute()
    {
        return number_format($this->amount, 0, ',', '.') . ' VND';
    }
}
