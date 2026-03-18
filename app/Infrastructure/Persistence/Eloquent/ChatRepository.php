<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Core\Chat\Ports\Outbound\ChatRepositoryInterface;
use App\Core\Chat\Domain\DTOs\MessageData;
use App\Models\ChatSession;
use App\Models\ChatMessage;

class ChatRepository implements ChatRepositoryInterface
{
    public function saveMessage(MessageData $data)
    {
        $session = ChatSession::updateOrCreate(
            ['id' => $data->sessionId],
            [
                'user_id' => $data->userId,
                'type' => $data->customerType,
                'customer_name' => $data->senderName,
            ]
        );

        $message = ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender_type' => $data->senderType,
            'content' => $data->content,
            'is_read' => false,
        ]);

        return $message->load('session');
    }

    public function findSession(string $sessionId)
    {
        return ChatSession::find($sessionId);
    }

    public function getAllSessions()
    {
        return ChatSession::with(['messages' => function ($query) {
            $query->latest()->limit(1);
        }])->orderBy('updated_at', 'desc')->get();
    }

    public function getSessionMessages(string $sessionId)
    {
        return ChatMessage::where('chat_session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
