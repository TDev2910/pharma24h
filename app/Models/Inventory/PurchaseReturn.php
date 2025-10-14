<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_code',
        'supplier_id',
        'return_date',
        'status',
        'total_amount',
        'total_discount',
        'paid_amount',
        'remaining_amount',
        'note'
    ];

    public function supplier()
    {
        return $this->belongsTo(\App\Models\Supplier::class);
    }

    public function items() 
    {
        return $this->hasMany(PurchaseReturnItem::class);
    }

    public function payments()
    {
        return $this->hasMany(PurchaseReturnPayment::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeReturned($query)
    {
        return $query->where('status', 'returned');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
}
