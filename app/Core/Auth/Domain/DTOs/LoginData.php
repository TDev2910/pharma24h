<?php

namespace App\Core\Auth\Domain\DTOs;

readonly class LoginData
{
    public function __construct(
        public string $email,
        public string $password,
        public bool $remember = false
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
