<?php
namespace App\Core\Customer\Domain\DTOs;

class CustomerData
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $phone = null,
        public readonly ?string $address = null,
        public readonly ?string $province = null,
        public readonly ?string $district = null,
        public readonly ?string $ward = null,
        public readonly ?string $password = null
    ) {}
}
