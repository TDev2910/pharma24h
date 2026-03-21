<?php
namespace App\Core\Chatbot\Domain\DTOs;

readonly class ChatRequestData
{
    public function __construct(
        public string $message,
        public ?string $sessionId,
        public ?array $context
    ) {}
}
