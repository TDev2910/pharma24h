<?php

namespace App\Core\Auth\Domain\DTOs;

class LoginData
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly bool $remember = false
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            email: $request->input('email'),
            password: $request->input('password'),
            remember: $request->boolean('remember')
        );
    }
}
