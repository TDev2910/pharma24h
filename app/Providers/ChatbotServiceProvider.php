<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Core\Chatbot\Ports\Inbound\ChatUseCaseInterface;
use App\Core\Chatbot\Application\Services\ChatService;
use App\Core\Chatbot\Ports\Outbound\AiProviderInterface;
use App\Infrastructure\Chatbot\GeminiAiProvider;
use App\Core\Chatbot\Ports\Outbound\ProductRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\EloquentProductRepository;

class ChatbotServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // 1. Bind Inbound Port với Application Service
        $this->app->bind(ChatUseCaseInterface::class, ChatService::class);

        // 2. Bind Outbound Ports với Adapters cụ thể
        $this->app->bind(AiProviderInterface::class, GeminiAiProvider::class);
        $this->app->bind(ProductRepositoryInterface::class, EloquentProductRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
