<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderCancellationRequested extends Notification
{
    use Queueable;

    public function __construct(protected Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order_cancellation_requested',
            'order_id' => $this->order->id,
            'order_code' => $this->order->order_code,
            'customer_name' => $this->order->customer_name,
            'reason' => $this->order->cancellation_reason,
            'note' => $this->order->cancellation_user_note,
            'requested_at' => optional($this->order->cancellation_requested_at)->toDateTimeString(),
            'message' => "Đơn hàng #{$this->order->order_code} vừa được yêu cầu hủy. Vui lòng kiểm tra.",
            'url' => route('admin.orders.index', ['status' => Order::STATUS['CANCELLATION_REQUESTED']]),
            'icon' => 'fa-regular fa-circle-question',
        ];
    }
}

