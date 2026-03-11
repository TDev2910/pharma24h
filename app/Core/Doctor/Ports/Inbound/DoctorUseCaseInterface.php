<?php

namespace App\Core\Doctor\Ports\Inbound;
use App\Core\Doctor\Domain\DTOs\DoctorData;

interface DoctorUseCaseInterface
{
    public function getDashboardData(?string $search , int $perPage) : array;
    public function createDoctor(DoctorData $data);
    public function updateDoctor(int|string $id , DoctorData $data);
    public function deleteDoctor(int|string $id);
    public function generateDoctorCode(): string;
}