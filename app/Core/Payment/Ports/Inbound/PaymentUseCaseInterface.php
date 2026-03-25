<?php

namespace App\Core\Payment\Ports\Inbound;

use App\Core\Payment\Domain\DTOs\PaymentCheckoutData;
use App\Core\Payment\Domain\DTOs\PaymentReturnData;

/**
 * Inbound Port cho luồng Thanh toán
 * Quy định các hành động mà "Thế giới bên ngoài" (như Controller) 
 * có thể gọi vào hệ thống lõi.
 */
interface PaymentUseCaseInterface
{
    /**
     * Xử lý khởi tạo thanh toán
     */
    public function handleCheckout(PaymentCheckoutData $data): array;

    /**
     * Xử lý kết quả trả về từ cổng thanh toán cho người dùng
     */
    public function handleReturn(PaymentReturnData $data): array;

    /**
     * Xử lý cập nhật trạng thái ngầm (IPN) từ cổng thanh toán
     */
    public function handleIpn(PaymentReturnData $data): array;
}
