<?php

namespace App\Services\EmailSMTP;

use App\Models\Order;
use App\Services\EmailSMTP\OrderConfirmationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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
            Log::info('Order confirmation email skipped: No customer email', [
                'order_id' => $order->id,
                'order_code' => $order->order_code
            ]);
            return false;
        }

        try {
            Mail::to($order->customer_email)
                ->send(new OrderConfirmationMail($order));

            Log::info('Order confirmation email sent successfully', [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'email' => $order->customer_email
            ]);

            return true;
        } catch (\Exception $e) {
            // Log error nhưng không throw exception để không làm gián đoạn flow
            Log::error('Failed to send order confirmation email', [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'email' => $order->customer_email,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }
}