<?php

namespace App\Core\Products\Good\Ports\Inbound;

use App\Core\Products\Good\Domain\DTOs\GoodData;

interface GoodUseCaseInterface
{
    // Lấy danh sách phân trang (dùng cho Inertia list page)
    public function getGoodList(?string $search, int $perPage): array;

    // Lấy danh sách có lọc nâng cao (dùng cho API/Datatable)
    public function getFilteredGoods(array $filters, int $perPage): array;

    // Lấy chi tiết một vật tư y tế theo ID
    public function getGoodById(int|string $id): array;

    public function createGood(GoodData $data): array;

    public function updateGood(int $id, GoodData $data): array;

    public function deleteGood(int $id): array;

    public function getFormData(): array;

    public function generateCodes(): array;
}
