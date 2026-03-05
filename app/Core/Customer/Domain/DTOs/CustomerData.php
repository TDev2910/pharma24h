<?php
namespace App\Core\Customer\Domain\DTOs;

readonly class CustomerData
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone = null,
        public ?string $address = null,
        public ?string $province = null,
        public ?string $district = null,
        public ?string $ward = null,
        public ?string $password = null
    ) {}
}
