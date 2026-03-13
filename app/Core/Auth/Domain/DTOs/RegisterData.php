<?php

namespace App\Core\Auth\Domain\DTOs;

readonly class RegisterData 
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone = null,
        public ?string $address = null,
        public string $password,
        public string $confirm_password,
    ) {}
}