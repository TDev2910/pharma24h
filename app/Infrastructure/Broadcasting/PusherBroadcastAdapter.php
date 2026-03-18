<?php

namespace App\Infrastructure\Broadcasting;

use App\Core\Chat\Ports\Outbound\BroadcastNotificationPortInterface;
use App\Events\MessageSent;
use App\Core\Chat\Domain\DTOs\MessageData;

class PusherBroadcastAdapter implements BroadcastNotificationPortInterface
{
    public function broadcastMessage($message)
    {
        $session = $message->session; 

        event(new MessageSent(new MessageData(
            sessionId: $message->chat_session_id,
            senderType: $message->sender_type,
            content: $message->content,
            customerType: $session?->type ?? 'guest',
            userId: $session?->user_id,
            senderName: $message->sender_type === 'staff' ? 'Staff' : ($session?->customer_name ?? 'Guest')
        )));
    }
}
