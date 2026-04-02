<?php

namespace App\Core\Products\Good\Ports\Outbound;

use App\Models\Goods;
use App\Core\Products\Good\Domain\DTOs\GoodData;

interface GoodRepositoryInterface
{
    public function getPaginatedGoods(?string $search, int $perPage);

    public function getFilteredGoods(array $filters, int $perPage);

    public function findById(int|string $id): ?Goods;

    public function findByIdWithRelations(int|string $id): ?Goods;

    public function create(GoodData $data): Goods;

    public function update(Goods $good, GoodData $data): bool;

    public function delete(Goods $good): bool;

    public function getFormData(): array;

    public function generateCodes(): array;
}
