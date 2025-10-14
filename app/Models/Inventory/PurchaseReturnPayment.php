<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_return_id',
        'payment_method',
        'amount',
        'note'
    ];

    // Relationships
    public function purchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class);
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
