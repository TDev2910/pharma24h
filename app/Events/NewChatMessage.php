<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChatMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chatSession;
    public $message;
    public $queue = 'high';

    /**
     * Create a new event instance.
     */
    public function __construct($chatSession, $message)
    {
        $this->chatSession = $chatSession;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            //chỉ dành riêng cho nhân viên
            new PrivateChannel('staff.chats')
        ];
    }

    public function broadcastWith()
    {
        return [
            'session' => $this->chatSession,
            'message' => $this->message,
        ];
    }

    public function broadcastAs(): string
    {
        return 'new.chat.message';
    }
}
