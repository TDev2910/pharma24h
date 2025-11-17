<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderCancellationProcessed extends Notification
{
    use Queueable;

    public function __construct(
        protected Order $order,
        protected string $resultStatus,
        protected ?string $adminNote = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $statusLabel = $this->resultStatus === Order::CANCELLATION_STATUS['APPROVED']
            ? 'Đã chấp nhận hủy'
            : 'Bị từ chối hủy';

        return [
            'type' => 'order_cancellation_processed',
            'order_id' => $this->order->id,
            'order_code' => $this->order->order_code,
            'status' => $this->resultStatus,
            'status_label' => $statusLabel,
            'admin_note' => $this->adminNote,
            'message' => "Yêu cầu hủy đơn #{$this->order->order_code}: {$statusLabel}.",
            'url' => "/user/orders/{$this->order->id}",
            'icon' => $this->resultStatus === Order::CANCELLATION_STATUS['APPROVED']
                ? 'fa-regular fa-circle-check'
                : 'fa-regular fa-circle-xmark',
        ];
    }
}

