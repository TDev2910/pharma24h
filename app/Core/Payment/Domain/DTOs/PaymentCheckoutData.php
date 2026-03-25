<?php

namespace App\Core\Payment\Domain\DTOs;

readonly class PaymentCheckoutData
{
    public function __construct(
        public string $driver,
        public int $orderId,
    ) {}
}
