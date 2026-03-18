<?php

namespace App\Core\Chat\Ports\Outbound;

interface BroadcastNotificationPortInterface
{
    public function broadcastMessage($message);
}
