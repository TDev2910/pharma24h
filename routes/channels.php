<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{sessionId}.{userId}', function ($user, $sessionId, $userId) {
    return $user->isStaff() || $user->ownsChatSession($sessionId);
});

Broadcast::channel('staff.chats', function ($user) {
    // Chỉ cho phép nhân viên (staff) hoặc quản trị viên (admin) nghe kênh này
    return $user->role === 'staff' || $user->role === 'admin';
});
