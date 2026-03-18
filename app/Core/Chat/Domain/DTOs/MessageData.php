<?php

namespace App\Core\Chat\Domain\DTOs;

readonly class MessageData
{
    public function __construct(
        public string $sessionId,
        public string $senderType, 
        public string $content,
        public string $customerType, 
        public ?int $userId = null,   
        public ?string $senderName = null
    ) {}

    public function isPersistent(): bool
    {
        return $this->customerType === 'member';
    }
}
