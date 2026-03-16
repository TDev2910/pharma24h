<?php

namespace App\Core\Auth\Domain\DTOs;

readonly class ForgetPasswordData
{
    public function __construct(
        public string $email,
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            email: $request->input('email'),
        );
    }
}
