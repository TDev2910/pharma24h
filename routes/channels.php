<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{sessionId}.{userId}', function ($user, $sessionId, $userId) {
    return $user->isStaff() || $user->ownsChatSession($sessionId);
});
