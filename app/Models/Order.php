<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\OrderItem;

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
        'order_code',
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (!$order->order_code) {
                do {
                    $randomCode = sprintf('%04d', rand(1000, 9999));
                    $exists = static::where('order_code', $randomCode)->exists();
                } while ($exists);
                
                $order->order_code = $randomCode;
            }
        });
    }

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

    
    public function scopeFilterByDate($query,$from,$to)
    {
        if($from && $to)
        {
            return $query->whereDate('created_at','>=',$from)
                ->whereDate('created_at','<=',$to);
            //Lọc tất cả đơn hàng ví dụ 13/09 đến 15/09 (bao gồm cả ngày 13 và 15).
        }
        elseif($from)
        {
            return $query->whereDate('created_at', '>=', $from);
            //lấy tất cả đơn hàng ví dụ từ ngày 13/09 trở về sau
        }
        elseif($to)
        {
            return $query->whereDate('created_at', '<=', $to);
            //lấy tất cả đơn hàng ví dụ từ ngày 15/09 trở về trước
        }
        return $query;
    }

    public function scopeFilterByStatus($query,$status)
    {
        if($status && in_array($status, ['new','pending','completed','cancelled']))
        {
            return $query->where('order_status',$status);
        }
        return $query;
    }
}
