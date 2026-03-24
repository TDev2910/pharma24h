<?php

namespace App\Core\Payment\Application\Services;

use App\Infrastructure\Payment\Adapters\VNPayAdapter;
use App\Infrastructure\Payment\Adapters\SePayAdapter;
use App\Core\Payment\Ports\Outbound\PaymentGatewayInterface;
use Exception;

class PaymentManager
{
    /**
     * Lấy gateway tương ứng với driver
     */
    public function driver(string $driver): PaymentGatewayInterface
    {
        return match ($driver) {
            'vnpay' => new VNPayAdapter(), //thanh toán vnpay
            'sepay' => new SePayAdapter(), //thanh toán sepay
            default => throw new Exception("Phương thức thanh toán $driver không hỗ trợ."),
        };
    }
}
