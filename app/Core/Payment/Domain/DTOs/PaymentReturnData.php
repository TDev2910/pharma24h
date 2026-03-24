<?php

namespace App\Core\Payment\Domain\DTOs;

/**
 * DTO chứa dữ liệu phản hồi từ cổng thanh toán (Return hoặc IPN)
 */
readonly class PaymentReturnData
{
    public function __construct(
        public string $driver,
        public array $data
    ) {}
}
