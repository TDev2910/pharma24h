<?php

namespace App\Core\Auth\Domain\DTOs;

readonly class ResetPasswordData
{
    public function __construct(
        public string $email,
        public string $otp,
        public string $password,
        public string $password_confirmation,
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            email: $request->input('email'),
            otp: $request->input('otp'),
            password: $request->input('password'),
            password_confirmation: $request->input('password_confirmation'),
        );
    }
}
