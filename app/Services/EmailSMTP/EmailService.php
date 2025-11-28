<?php

namespace App\Services\EmailSMTP;

use App\Models\Order;
use App\Services\EmailSMTP\OrderConfirmationMail;
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
}