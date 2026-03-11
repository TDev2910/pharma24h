<?php
namespace App\Infrastructure\Persistence\Eloquent;

use App\Models\Doctor;
use App\Core\Doctor\Ports\Outbound\DoctorRepositoryInterface;
use App\Core\Doctor\Domain\DTOs\DoctorData;
use Illuminate\Support\Facades\Storage;

class DoctorRepository implements DoctorRepositoryInterface
{
    public function getPaginatedDoctors(?string $search, int $perPage)
    {
        $query = Doctor::query()
            ->when($search, function ($q, $search) {
                $q->where(function ($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%")
                        ->orWhere('doctor_code', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            });

        return $query->latest()
            ->paginate($perPage)
            ->withQueryString(); 
    }

    public function findById(int|string $id): ?Doctor
    {
        return Doctor::find($id);
    }

    public function create(DoctorData $data) : Doctor {
        return Doctor::create([
            'doctor_code' => $this->generateUniqueCode(),
            'name' => $data->name,
            'gender' => $data->gender ?? 'Male',
            'email' => $data->email,
            'phone' => $data->phone,
            'address' => $data->address,
            'province_district' => $data->province,
            'ward_commune' => $data->ward,
            'avatar' => $data->avatar,
            'specialty' => $data->specialization,
            'qualification' => $data->education,
            'note' => $data->description,
            'status' => $data->status ?? 'active',
        ]);
    }

    public function update(Doctor $doctor, DoctorData $data): bool {
        $updateData = array_filter([
            'name' => $data->name,
            'gender' => $data->gender,
            'email' => $data->email,
            'phone' => $data->phone,
            'address' => $data->address,
            'province_district' => $data->province,
            'ward_commune' => $data->ward,
            'avatar' => $data->avatar,
            'specialty' => $data->specialization,
            'qualification' => $data->education,
            'note' => $data->description,
            'status' => $data->status,
        ], fn($val) => !is_null($val)); 
        return $doctor->update($updateData);
    }

    public function delete(Doctor $doctor): bool {
        return $doctor->delete();
    }

    public function generateUniqueCode(): string {
        return Doctor::generateDoctorCode();
    }
}