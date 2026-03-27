<?php

namespace App\Infrastructure\Chatbot;

use App\Core\Chatbot\Ports\Outbound\AiProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiAiProvider implements AiProviderInterface
{
    private string $apiKey;
    private array $models = ['gemini-3.0-flash', 'gemini-2.5-flash', 'gemini-2.0-flash', 'gemini-1.5-flash'];
    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }
    public function generateStream(string $prompt): \Generator
    {
        foreach ($this->models as $modelName) {
            try {
                $response = Http::timeout(30)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key=" . $this->apiKey,
                    [
                        'contents' => [['parts' => [['text' => $prompt]]]]
                    ]
                );
                if ($response->successful()) {
                    $content = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
                    $words = explode(' ', $content);
                    foreach ($words as $word) {
                        yield $word . ' ';
                    }
                    return;
                }

                $errorDetail = $response->body();
                Log::warning("Model {$modelName} failed. Response: {$errorDetail}");
            } catch (\Exception $e) {
                Log::error("Error with model {$modelName}: " . $e->getMessage());
                continue;
            }
        }
        yield "Dạ, hiện tại hệ thống tư vấn đang bận một chút, bạn vui lòng nhắn lại sau giây lát nhé!";
    }
}
