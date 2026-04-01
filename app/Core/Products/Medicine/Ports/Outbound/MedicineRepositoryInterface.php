<?php

namespace App\Core\Products\Medicine\Ports\Outbound;

use App\Models\Medicine;
use App\Core\Products\Medicine\Domain\DTOs\MedicineData;

interface MedicineRepositoryInterface
{
    public function getPaginatedMedicines(?string $search, int $perPage);

    public function getFilteredMedicines(array $filters, int $perPage);

    public function findById(int|string $id): ?Medicine;

    public function findByIdWithRelations(int|string $id): ?Medicine;

    public function create(MedicineData $data): Medicine;

    public function update(Medicine $medicine, MedicineData $data): bool;

    public function delete(Medicine $medicine): bool;

    public function getFormData(): array;

    public function generateCodes(): array;
}
