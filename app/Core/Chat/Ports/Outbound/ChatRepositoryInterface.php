<?php

namespace App\Core\Chat\Ports\Outbound;

use App\Core\Chat\Domain\DTOs\MessageData;

interface ChatRepositoryInterface
{
    public function saveMessage(MessageData $data);
    public function findSession(string $sessionId);
    public function getAllSessions();
    public function getSessionMessages(string $sessionId);
}
