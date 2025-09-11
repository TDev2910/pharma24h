<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'delivery_method',
        'pickup_location',
        'shipping_address',
        'province',
        'district',
        'ward',
        'total_amount',
        'payment_method',
        'payment_status',
        'order_status',
        'transaction_id',
        'note',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isPickup(): bool
    {
        return $this->delivery_method === 'pickup';
    }

    public function isShipping(): bool
    {
        return $this->delivery_method === 'shipping';
    }
}
