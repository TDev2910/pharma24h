<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Core\Chatbot\Ports\Outbound\ProductRepositoryInterface;
use App\Services\Chatbot\ProductSearchService;

class EloquentProductRepository implements ProductRepositoryInterface
{
    private ProductSearchService $searchService;

    public function __construct()
    {
        $this->searchService = new ProductSearchService();
    }

    public function searchProducts(string $query): array
    {
        return $this->searchService->search($query);
    }
}
