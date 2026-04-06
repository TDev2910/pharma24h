<?php

namespace App\Core\Products\Services\Ports\Inbound;

use App\Core\Products\Services\Domain\DTOs\ServiceData;

interface ServiceUseCaseInterface
{
    /** Lấy danh sách phân trang (dùng cho Inertia list page) */
    public function getServiceList(?string $search, int $perPage): array;

    /** Lấy danh sách có lọc nâng cao (dùng cho API/Datatable) */
    public function getFilteredServices(array $filters, int $perPage): array;

    /** Lấy chi tiết một dịch vụ theo ID */
    public function getServiceById(int|string $id): array;

    public function createService(ServiceData $data): array;

    public function updateService(int $id, ServiceData $data): array;

    public function deleteService(int $id): array;

    public function updateServiceStatus(int $id, string $status): array;

    public function getFormData(): array;

    public function generateCodes(): array;
}
