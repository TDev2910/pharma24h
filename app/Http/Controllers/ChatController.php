<?php

namespace App\Http\Controllers;

use App\Core\Chat\Ports\Inbound\ChatPortInterface;
use App\Core\Chat\Domain\DTOs\MessageData;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct(
        private ChatPortInterface $chatService
    ) {}

    public function index()
    {
        return Inertia::render('Staff/Chat/Index');
    }

    public function getSessions()
    {
        return response()->json($this->chatService->getSessions());
    }

    public function getMessages($sessionId)
    {
        return response()->json($this->chatService->getMessages($sessionId));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'session_id' => 'required|string',
        ]);

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $senderType = ($user && ($user->isStaff() || $user->isAdmin())) ? 'staff' : 'customer';
        $messageData = new MessageData(
            sessionId: $request->input('session_id'),
            senderType: $senderType,
            content: $request->input('content'),
            customerType: $user ? 'member' : 'guest',
            userId: Auth::id(),
            senderName: $user?->name ?? 'Guest'
        );

        $message = $this->chatService->sendMessage($messageData);

        return response()->json($message);
    }
}
