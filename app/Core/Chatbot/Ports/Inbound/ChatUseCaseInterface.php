<?php

namespace App\Core\Chatbot\Ports\Inbound;

use App\Core\Chatbot\Domain\DTOs\ChatRequestData;
use App\Core\Chatbot\Domain\DTOs\ChatResponsePart;

interface ChatUseCaseInterface
{
    /**
     * Xử lý yêu cầu chat và trả về một luồng dữ liệu (Stream)
     * 
     * @param ChatRequestData $data
     * @return \Generator<ChatResponsePart>
     */
    public function chat(ChatRequestData $data): \Generator;
}
