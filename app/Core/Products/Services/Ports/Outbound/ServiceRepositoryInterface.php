<?php

namespace App\Core\Products\Services\Ports\Outbound;

use App\Models\Service;
use App\Core\Products\Services\Domain\DTOs\ServiceData;

interface ServiceRepositoryInterface
{
    public function getPaginatedServices(?string $search, int $perPage);

    public function getFilteredServices(array $filters, int $perPage);

    public function findById(int|string $id): ?Service;

    public function findByIdWithRelations(int|string $id): ?Service;

    public function create(ServiceData $data): Service;

    public function update(Service $service, ServiceData $data): bool;

    public function delete(Service $service): bool;

    public function getFormData(): array;

    public function generateCodes(): array;
}
