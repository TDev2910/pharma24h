<?php

namespace App\Core\Products\Medicine\Ports\Inbound;

use App\Core\Products\Medicine\Domain\DTOs\MedicineData;

interface MedicineUseCaseInterface
{
    /** Lấy danh sách phân trang (dùng cho Inertia list page) */
    public function getMedicineList(?string $search, int $perPage): array;

    /** Lấy danh sách có lọc nâng cao (dùng cho API/Datatable) */
    public function getFilteredMedicines(array $filters, int $perPage): array;

    /** Lấy chi tiết một thuốc theo ID */
    public function getMedicineById(int|string $id): array;

    public function createMedicine(MedicineData $data): array;

    public function updateMedicine(int $id, MedicineData $data): array;

    public function deleteMedicine(int $id): array;

    public function getFormData(): array;

    public function generateCodes(): array;
}
