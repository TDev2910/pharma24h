<?php

namespace App\Core\Doctor\Application\Services;

use App\Core\Doctor\Ports\Inbound\DoctorUseCaseInterface;
use App\Core\Doctor\Ports\Outbound\DoctorRepositoryInterface;
use App\Core\Doctor\Domain\DTOs\DoctorData;
use Illuminate\Support\Facades\Storage;

class DoctorService implements DoctorUseCaseInterface
{
    public function __construct(
        private DoctorRepositoryInterface $repository
    ) {}

    public function getDashboardData(?string $search, int $perPage): array
    {
        $doctors = $this->repository->getPaginatedDoctors($search, $perPage);
        
        $doctors->through(function ($doctor) {
            return [
                'id' => $doctor->id,
                'doctor_code' => $doctor->doctor_code,
                'name' => $doctor->name,
                'gender' => $doctor->gender,
                'email' => $doctor->email,
                'phone' => $doctor->phone,
                'address' => $doctor->address,
                'province' => $doctor->province_district,
                'district' => $doctor->district, 
                'ward' => $doctor->ward_commune,
                'specialty' => $doctor->specialty,
                'qualification' => $doctor->qualification,
                'note' => $doctor->note,
                'status' => $doctor->status,
                'avatar' => $doctor->avatar,
                'avatar_url' => $this->getAvatarUrl($doctor->avatar),
                'created_at' => $doctor->created_at,
            ];
        });

        return [
            'doctors' => $doctors,
        ];
    }

    public function createDoctor(DoctorData $data)
    {
        return $this->repository->create($data);
    }

    public function updateDoctor(int|string $id, DoctorData $data)
    {
        $user = $this->repository->findById($id);
        return $this->repository->update($user, $data);
    }

    public function deleteDoctor(int|string $id)
    {
        $user = $this->repository->findById($id);
        return $this->repository->delete($user);
    }

    public function getAvatarUrl($avatarPath)
    {
        if(!$avatarPath) return null;
        if(str_starts_with($avatarPath, 'http')) return $avatarPath;

        $path = str_starts_with($avatarPath, 'avatars/') ? $avatarPath : 'avatars/' . $avatarPath;

        if(file_exists(public_path('storage/' . $path))) {
            return url('storage/' . $path);
        }

        return Storage::url($path);
    }

    public function generateDoctorCode(): string
    {
        return $this->repository->generateUniqueCode();
    }
}