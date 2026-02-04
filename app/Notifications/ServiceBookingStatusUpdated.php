<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\ServiceBooking;

class ServiceBookingStatusUpdated extends Notification
{
    use Queueable;

    protected $booking;
    protected $oldStatus;
    protected $newStatus;

    public function __construct(ServiceBooking $booking, $oldStatus, $newStatus)
    {
        $this->booking = $booking;
        $this->oldStatus = $oldStatus; 
        $this->newStatus = $newStatus; 
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'service_booking_status_updated',
            'booking_id' => $this->booking->id,
            'service_name' => $this->booking->service->ten_dich_vu ?? 'Dịch vụ',
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => "Dịch vụ '{$this->booking->service->ten_dich_vu}' đã được cập nhật từ '{$this->getStatusLabel($this->oldStatus)}' sang '{$this->getStatusLabel($this->newStatus)}'",
            'url' => "/user/services/{$this->booking->id}",
            'icon' => 'fas fa-file-medical',
            'created_at' => now()->toDateTimeString(),
        ];
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
        ];
        return $labels[$status] ?? $status;
    }
}