<?php

namespace App\Core\Payment\Ports\Outbound;

use App\Models\Order;
use Illuminate\Http\Request;

/**
 * Port đại diện cho một cổng thanh toán (VNPay, SePay, MoMo...)
 */
interface PaymentGatewayInterface
{
    /**
     * Khởi tạo thanh toán và trả về URL redirect hoặc dữ liệu QR
     */
    public function generatePaymentData(Order $order): array;

    /**
     * Xử lý dữ liệu phản hồi (Return/IPN)
     */
    public function verifyPayment(array $data): array;
}
