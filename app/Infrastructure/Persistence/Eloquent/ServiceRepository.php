<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Models\Service;
use App\Core\Products\Services\Ports\Outbound\ServiceRepositoryInterface;
use App\Core\Products\Services\Domain\DTOs\ServiceData;
use App\Models\ProductCategory;
use App\Models\Doctor;

class ServiceRepository implements ServiceRepositoryInterface
{
    public function getPaginatedServices(?string $search, int $perPage)
    {
        $query = Service::with(['category', 'doctor']);

        $query->when($search, function ($q, $search) {
            $q->where(function ($subQ) use ($search) {
                $subQ->where('ten_dich_vu', 'like', "%{$search}%")
                    ->orWhere('ma_dich_vu', 'like', "%{$search}%");
            });
        });

        return $query->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getFilteredServices(array $filters, int $perPage)
    {
        $query = Service::with(['category', 'doctor']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('ten_dich_vu', 'LIKE', "%{$search}%")
                  ->orWhere('ma_dich_vu', 'LIKE', "%{$search}%");
            });
        }
        if (!empty($filters['category_id'])) {
            $query->where('nhom_hang_id', $filters['category_id']);
        }
        if (!empty($filters['doctor_id'])) {
            $query->where('doctor_id', $filters['doctor_id']);
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function findById(int|string $id): ?Service
    {
        return Service::find($id);
    }

    public function findByIdWithRelations(int|string $id): ?Service
    {
        return Service::with(['category', 'doctor'])->find($id);
    }

    public function create(ServiceData $data): Service
    {
        return Service::create([
            'ma_dich_vu'          => $data->ma_dich_vu,
            'ten_dich_vu'         => $data->ten_dich_vu,
            'nhom_hang_id'        => $data->nhom_hang_id,
            'doctor_id'           => $data->doctor_id,
            'gia_dich_vu'         => $data->gia_dich_vu,
            'mo_ta'               => $data->mo_ta,
            'image'               => $data->image,
            'ban_truc_tiep'       => $data->ban_truc_tiep,
            'hinh_thuc'           => $data->hinh_thuc,
            'thoi_gian_thuc_hien' => $data->thoi_gian_thuc_hien,
            'trang_thai'          => $data->trang_thai,
            'ghi_chu'             => $data->ghi_chu,
            'created_by'          => $data->created_by,
            'updated_by'          => $data->updated_by,
        ]);
    }

    public function update(Service $service, ServiceData $data): bool
    {
        $updateData = [
            'ma_dich_vu'          => $data->ma_dich_vu,
            'ten_dich_vu'         => $data->ten_dich_vu,
            'nhom_hang_id'        => $data->nhom_hang_id,
            'doctor_id'           => $data->doctor_id,
            'gia_dich_vu'         => $data->gia_dich_vu,
            'mo_ta'               => $data->mo_ta,
            'ban_truc_tiep'       => $data->ban_truc_tiep,
            'hinh_thuc'           => $data->hinh_thuc,
            'thoi_gian_thuc_hien' => $data->thoi_gian_thuc_hien,
            'trang_thai'          => $data->trang_thai,
            'ghi_chu'             => $data->ghi_chu,
            'updated_by'          => $data->updated_by,
        ];

        if ($data->image) {
            $updateData['image'] = $data->image;
        }

        return $service->update(array_filter($updateData, fn($value) => !is_null($value)));
    }

    public function delete(Service $service): bool
    {
        return $service->delete();
    }

    public function getFormData(): array
    {
        return [
            'categories' => ProductCategory::getAllCategoriesWithDepth(),
            'doctors'    => Doctor::all(),
        ];
    }

    public function generateCodes(): array
    {
        return [
            'ma_dich_vu' => 'DV' . date('Ymd') . str_pad(Service::count() + 1, 4, '0', STR_PAD_LEFT),
        ];
    }
}
