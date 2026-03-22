<?php

namespace App\Core\Chat\Application\Services;

use App\Core\Chat\Ports\Inbound\ChatPortInterface;
use App\Core\Chat\Ports\Outbound\ChatRepositoryInterface;
use App\Core\Chat\Ports\Outbound\BroadcastNotificationPortInterface;
use App\Core\Chat\Domain\DTOs\MessageData;

class ChatService implements ChatPortInterface
{
    public function __construct(
        private ChatRepositoryInterface $repository,
        private BroadcastNotificationPortInterface $broadcaster
    ) {}

    public function sendMessage(MessageData $data)
    {
        $message = $this->repository->saveMessage($data);

        $this->broadcaster->broadcastMessage($message);

        return $message;
    }

    public function syncSession(array $data)
    {
        
    }

    public function getSessions()
    {
        return $this->repository->getAllSessions();
    }

    public function getMessages(string $sessionId)
    {
        return $this->repository->getSessionMessages($sessionId);
    }

    public function getSessionById(string $sessionId)
    {
        return $this->repository->findSession($sessionId);
    }
}
