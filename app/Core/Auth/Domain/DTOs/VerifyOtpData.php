<?php

namespace App\Core\Auth\Domain\DTOs;

readonly class VerifyOtpData
{
    public function __construct(
        public string $email,
        public string $otp,
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            email: $request->input('email'),
            otp: $request->input('otp'),
        );
    }
}
