<?php

namespace App\Core\Auth\Domain\DTOs;

class SocialAuthData
{
    public function __construct(
        public readonly string $uid,
        public readonly string $email,
        public readonly string $name,
        public readonly ?string $photoURL,
        public readonly string $provider = 'google'
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            uid: $data['uid'],
            email: $data['email'],
            name: $data['name'],
            photoURL: $data['photoURL'] ?? null
        );
    }
}
