<?php
namespace App\Core\Doctor\Ports\Outbound;

use App\Models\Doctor;
use App\Core\Doctor\Domain\DTOs\DoctorData;

interface DoctorRepositoryInterface
{
    public function getPaginatedDoctors(?string $search, int $perPage);
    public function findById(int|string $id): ?Doctor;
    public function create(DoctorData $data): Doctor;
    public function update(Doctor $doctor, DoctorData $data): bool;
    public function delete(Doctor $doctor): bool;
    public function generateUniqueCode(): string;
}
