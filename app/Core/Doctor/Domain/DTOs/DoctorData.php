<?php

namespace App\Core\Doctor\Domain\DTOs;

readonly class DoctorData
{
    public function __construct(
        public ?int $id = null,
        public string $name,
        public string $email,
        public ?string $gender = null,
        public ?string $phone = null,
        public ?string $address = null,
        public ?string $province = null,
        public ?string $district = null,
        public ?string $ward = null,
        public ?string $avatar = null,
        public ?string $description = null,
        public ?string $specialization = null,
        public ?string $experience = null,
        public ?string $education = null,
        public ?string $license = null,
        public ?string $status = 'active',
    ) {
    }
}