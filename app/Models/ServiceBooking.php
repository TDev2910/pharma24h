<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ServiceBooking extends Model
{
    protected $fillable = [
        'service_id',
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'booking_date',
        'booking_time',
        'price',
        'payment_method',
        'payment_status',
        'status',
        'notes'
    ];

    protected $casts = [
        'booking_date' => 'date',
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    //tạo mối quan hệ với bảng services
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    //tạo mối quan hệ với bảng users
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending($query)
    {
        return $this->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $this->where('status', 'confirmed');
    }
    
    public function scopeCompleted($query)
    {
        return $this->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $this->where('status', 'cancelled');
    }

    public function scopeUnpaid($query)
    {
        return $this->where('payment_status', 'unpaid');
    }

    public function scopePaid($query)
    {
        return $this->where('payment_status', 'paid');
    }

    // Lấy lịch cần gọi điện xác nhận (buổi sáng)
    public function scopeNeedConfirmation($query)
    {
        return $query->where('status', 'pending');
    }
 
    // Lấy lịch đã xác nhận, chờ khách đến
    public function scopeWaitingForCustomer($query)
    {
        return $query->where('status', 'confirmed');
    }

    // Lấy lịch cần thanh toán (khách đã đến)
    public function scopeNeedPayment($query)
    {
        return $query->where('status', 'confirmed')
        ->where('payment_status', 'unpaid');
    }

    // Lấy lịch đã thanh toán, chờ thực hiện dịch vụ
    public function scopeReadyForService($query)
    {
        return $query->where('status', 'confirmed')
        ->where('payment_status', 'paid');
    }

    public function canConfirm(): bool
    {
        return $this->status === 'pending';
    }

    public function canComplete(): bool
    {
        return $this->status === 'confirmed' && $this->payment_status === 'paid';
    }

    public function canCancel(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    // trạng thái hiển thị 
    public function getStatusDisplayAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'completed' => 'Đã hoàn thành',
            'cancelled' => 'Đã hủy',
            default => 'Không xác định'
        };
    }

    public function getPaymentStatusDisplayAttribute(): string
    {
        return match($this->payment_status) {
            'unpaid' => 'Chưa thanh toán',
            'paid' => 'Đã thanh toán',
            default => 'Không xác định'
        };
    }
}
