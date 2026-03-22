<?php

namespace App\Core\Chat\Ports\Inbound;

use App\Core\Chat\Domain\DTOs\MessageData;

interface ChatPortInterface
{
    /**
     * Gửi tin nhắn và lưu vào database
     */
    public function sendMessage(MessageData $data);

    /**
     * Đồng bộ hóa session cho Guest/Member
     */
    public function syncSession(array $data);

    public function getSessions();

    public function getMessages(string $sessionId);

    public function getSessionById(string $sessionId);
}
