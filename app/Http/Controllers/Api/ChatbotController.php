<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Http\Requests\ChatbotRequest;
use App\Core\Chatbot\Ports\Inbound\ChatUseCaseInterface;
use App\Core\Chatbot\Domain\DTOs\ChatRequestData;

class ChatbotController extends Controller
{
    public function __construct(
        private ChatUseCaseInterface $chatUseCase
    ) {}

    public function chat(ChatbotRequest $request): StreamedResponse
    {
        $userMessage = $request->input('message');

        $chatRequest = new ChatRequestData(
            message: $userMessage,
            sessionId: $request->session()->getId(),
            context: []
        );

        return response()->stream(
            function () use ($chatRequest) {
                try {
                    foreach ($this->chatUseCase->chat($chatRequest) as $part) {
                        if ($part->type === 'images') {
                            echo "event: images\n";
                            echo "data: " . $part->content . "\n\n";
                        } else {
                            // Text part
                            echo "event: update\n";
                            echo "data: " . $part->content . "\n\n";
                        }

                        if (ob_get_level() > 0) ob_flush();
                        flush();

                        if ($part->type === 'text' && !empty($part->content)) {
                            usleep(10000);
                        }
                    }
                } catch (\Exception $e) {
                    echo "event: update\n";
                    echo "data: Có lỗi xảy ra trong quá trình xử lý: " . $e->getMessage() . "\n\n";

                    if (ob_get_level() > 0) ob_flush();
                    flush();
                }
            },
            200,
            [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'X-Accel-Buffering' => 'no',
            ]
        );
    }
}
