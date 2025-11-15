<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification
{
    use Queueable;

    protected $order; 
    protected $oldStatus;
    protected $newStatus;
    /**
     * Create a new notification instance.
     */
    public function __construct($order, $oldStatus, $newStatus)
    {
        $this->order = $order; //lấy model Order
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array //lấy channel để gửi notification
    {
        return ['database'];
        
    }

    /**
     * Get the mail representation of the notification.
     */
    
     //Chuyển notification thành mảng để lưu vào database và hiển thị trên trang user
     public function toArray($notifiable)
     {
         return [
             'type' => 'order_status_updated',
             'order_id' => $this->order->id, //Id đơn hàng
             'order_code' => $this->order->order_code, //Mã đơn hàng
             'old_status' => $this->oldStatus, //Trạng thái cũ
             'new_status' => $this->newStatus, //Trạng thái mới
             'message' => "Đơn hàng #{$this->order->order_code} đã được cập nhật từ '{$this->getStatusLabel($this->oldStatus)}' sang '{$this->getStatusLabel($this->newStatus)}'", //Thông báo
             'url' => "/user/orders/{$this->order->id}", //Url để chuyển hướng đến trang đơn hàng
             'icon' => 'fas fa-clipboard-list', //Icon để hiển thị trên trang user
             'created_at' => now()->toDateTimeString(), //Thời gian tạo notification
         ];
     }

     public function toMail($notifiable): MailMessage
     {
         $order = $this->order;
         
         return (new MailMessage)
             ->subject('Xác nhận đơn hàng #' . $order->order_code)
             ->greeting('Xin chào ' . $order->customer_name . '!')
             ->line('Cảm ơn bạn đã đặt hàng tại Sức Khỏe 24h.')
             ->line('Chúng tôi đã nhận được đơn hàng của bạn với thông tin sau:')
             ->line('**Mã đơn hàng:** #' . $order->order_code)
             ->line('**Tổng tiền:** ' . number_format($order->total_amount, 0, ',', '.') . ' VNĐ')
             ->line('**Phương thức thanh toán:** ' . $this->getPaymentMethodLabel($order->payment_method))
             ->line('**Phương thức giao hàng:** ' . $this->getDeliveryMethodLabel($order->delivery_method))
             ->action('Xem chi tiết đơn hàng', url('/user/orders/' . $order->id))
             ->line('Chúng tôi sẽ xử lý đơn hàng của bạn trong thời gian sớm nhất.')
             ->line('Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi.');
     }
 
     //Chuyển trạng thái thành text để hiển thị trên trang user
     private function getStatusLabel($status)
     {
         $labels = [
             'new' => 'Chờ xử lý',
             'processing' => 'Đang xử lý',
             'confirmed' => 'Đã xác nhận',
             'completed' => 'Hoàn thành',
             'cancelled' => 'Đã hủy',
         ];
         return $labels[$status] ?? $status;
     }
}
