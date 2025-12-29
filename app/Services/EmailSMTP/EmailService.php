<?php

namespace App\Services\EmailSMTP;

use App\Models\Order;
use App\Models\SupportTicket;
use App\Models\ServiceBooking;
use App\Services\EmailSMTP\ServiceBookingConfirmationMail;
use App\Services\EmailSMTP\OrderConfirmationMail;
use App\Services\EmailSMTP\TicketReplyMail;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Gửi email xác nhận đơn hàng cho khách hàng
     *
     * @param Order $order
     * @return bool
     */
    public function sendOrderConfirmation(Order $order): bool
    {
        // Chỉ gửi nếu có email
        if (empty($order->customer_email)) {
            return false;
        }

        try {
            Mail::to($order->customer_email)
                ->send(new OrderConfirmationMail($order));

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // Gửi email phản hồi yêu cầu hỗ trợ cho khách hàng
    public function sendTicketReply(SupportTicket $ticket, string $replyContent): bool
    {
        // Kiểm tra email khách hàng
        if (empty($ticket->email)) {
            return false;
        }

        try {
            // Sử dụng queue để gửi ngầm (tối ưu tốc độ phản hồi Modal)
            // Nếu chưa cấu hình queue, bạn đổi ->queue() thành ->send()
            Mail::to($ticket->email)
                ->send(new TicketReplyMail($ticket, $replyContent));

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    /**
     * Gửi email xác nhận đặt lịch dịch vụ cho khách hàng
     *
     * @param ServiceBooking $booking
     * @return bool
     */
    public function sendServiceBookingConfirmation(ServiceBooking $booking): bool
    {
        if(empty($booking->customer_email)) {
            return false;
        }

        try {
            Mail::to($booking->customer_email)
                ->send(new ServiceBookingConfirmationMail($booking));

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
