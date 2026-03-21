<?php

namespace App\Core\Chatbot\Ports\Outbound;

interface AiProviderInterface
{
    /**
     * @param string $prompt
     * @return \Generator<string> Trả về một luồng các từ (strings)
     */
    public function generateStream(string $prompt): \Generator;
}
