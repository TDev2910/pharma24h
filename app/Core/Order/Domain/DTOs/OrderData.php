<?php

namespace App\Core\Order\Domain\DTOs;

readonly class OrderData
{
    public function __construct(
        public ?string $customer_name = null,
        public ?string $customer_phone = null,
        public ?string $customer_email = null,
        public ?string $shipping_address = null,
        public ?string $province = null,
        public ?string $district = null,
        public ?string $ward = null,
        public ?string $pickup_location = null,
        public ?string $note = null,
        public ?string $delivery_method = null,
        public ?string $payment_method = null
    ) {}

    public function toArray(): array
    {
        $data = [
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'customer_email' => $this->customer_email,
            'shipping_address' => $this->shipping_address,
            'province' => $this->province,
            'district' => $this->district,
            'ward' => $this->ward,
            'pickup_location' => $this->pickup_location,
            'note' => $this->note,
            'delivery_method' => $this->delivery_method,
            'payment_method' => $this->payment_method,
        ];

        return array_filter($data, function ($value) {
            return $value !== null;
        });
    }
}
