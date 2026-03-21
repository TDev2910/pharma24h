<?php
namespace App\Core\Chatbot\Domain\DTOs;

readonly class ChatResponsePart
{
    public function __construct(
        public string $type,
        public string $content,
        public bool $isFinal = false
    ) {}
}
