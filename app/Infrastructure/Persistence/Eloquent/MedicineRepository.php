<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Models\Medicine;
use App\Core\Products\Medicine\Ports\Outbound\MedicineRepositoryInterface;
use App\Core\Products\Medicine\Domain\DTOs\MedicineData;
use App\Models\ProductCategory;
use App\Models\Manufacturer;
use App\Models\DrugRoute;
use App\Models\Position;

class MedicineRepository implements MedicineRepositoryInterface
{
    public function getPaginatedMedicines(?string $search, int $perPage)
    {
        $query = Medicine::with(['category', 'manufacturer', 'drugRoute', 'position']);

        $query->when($search, function ($q, $search) {
            $q->where(function ($subQ) use ($search) {
                $subQ->where('ten_thuoc', 'like', "%{$search}%")
                    ->orWhere('ma_hang', 'like', "%{$search}%")
                    ->orWhere('hoat_chat', 'like', "%{$search}%")
                    ->orWhere('so_dang_ky', 'like', "%{$search}%");
            });
        });

        return $query->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getFilteredMedicines(array $filters, int $perPage)
    {
        $query = Medicine::with(['category', 'manufacturer', 'drugRoute', 'position']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('ten_thuoc', 'LIKE', "%{$search}%")
                  ->orWhere('ma_hang', 'LIKE', "%{$search}%")
                  ->orWhere('hoat_chat', 'LIKE', "%{$search}%");
            });
        }
        if (!empty($filters['category_id'])) {
            $query->where('nhom_hang_id', $filters['category_id']);
        }
        if (!empty($filters['manufacturer_id'])) {
            $query->where('manufacturer_id', $filters['manufacturer_id']);
        }
        if (!empty($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }
        if (!empty($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function findById(int|string $id): ?Medicine
    {
        return Medicine::find($id);
    }

    public function findByIdWithRelations(int|string $id): ?Medicine
    {
        return Medicine::with(['category', 'manufacturer', 'drugRoute', 'position'])->find($id);
    }

    public function create(MedicineData $data): Medicine
    {
        return Medicine::create([
            'ten_thuoc'         => $data->ten_thuoc,
            'ma_hang'           => $data->ma_hang,
            'ma_vach'           => $data->ma_vach,
            'ten_viet_tat'      => $data->ten_viet_tat,
            'nhom_hang_id'      => $data->nhom_hang_id,
            'gia_von'           => $data->gia_von,
            'ton_kho'           => $data->ton_kho,
            'gia_ban'           => $data->gia_ban,
            'so_dang_ky'        => $data->so_dang_ky,
            'hoat_chat'         => $data->hoat_chat,
            'ham_luong'         => $data->ham_luong,
            'drugusage_id'      => $data->drugusage_id,
            'slug'              => $data->slug,
            'quy_cach_dong_goi' => $data->quy_cach_dong_goi,
            'manufacturer_id'   => $data->manufacturer_id,
            'nuoc_san_xuat'     => $data->nuoc_san_xuat,
            'ton_thap_nhat'     => $data->ton_thap_nhat,
            'ton_cao_nhat'      => $data->ton_cao_nhat,
            'position_id'       => $data->position_id,
            'trong_luong'       => $data->trong_luong,
            'don_vi_tinh'       => $data->don_vi_tinh,
            'ban_truc_tiep'     => $data->ban_truc_tiep,
            'mo_ta'             => $data->mo_ta,
            'image'             => $data->image,
            'khach_dat'         => $data->khach_dat,
            'gia_khuyen_mai'    => $data->gia_khuyen_mai,
            'ton_khuyen_mai'    => $data->ton_khuyen_mai,
        ]);
    }

    public function update(Medicine $medicine, MedicineData $data): bool
    {
        $updateData = [
            'ten_thuoc'         => $data->ten_thuoc,
            'ma_hang'           => $data->ma_hang,
            'ma_vach'           => $data->ma_vach,
            'ten_viet_tat'      => $data->ten_viet_tat,
            'nhom_hang_id'      => $data->nhom_hang_id,
            'gia_von'           => $data->gia_von,
            'ton_kho'           => $data->ton_kho,
            'gia_ban'           => $data->gia_ban,
            'so_dang_ky'        => $data->so_dang_ky,
            'hoat_chat'         => $data->hoat_chat,
            'ham_luong'         => $data->ham_luong,
            'drugusage_id'      => $data->drugusage_id,
            'slug'              => $data->slug,
            'quy_cach_dong_goi' => $data->quy_cach_dong_goi,
            'manufacturer_id'   => $data->manufacturer_id,
            'nuoc_san_xuat'     => $data->nuoc_san_xuat,
            'ton_thap_nhat'     => $data->ton_thap_nhat,
            'ton_cao_nhat'      => $data->ton_cao_nhat,
            'position_id'       => $data->position_id,
            'trong_luong'       => $data->trong_luong,
            'don_vi_tinh'       => $data->don_vi_tinh,
            'ban_truc_tiep'     => $data->ban_truc_tiep,
            'mo_ta'             => $data->mo_ta,
            'khach_dat'         => $data->khach_dat,
            'gia_khuyen_mai'    => $data->gia_khuyen_mai,
            'ton_khuyen_mai'    => $data->ton_khuyen_mai,
        ];

        if ($data->image) {
            $updateData['image'] = $data->image;
        }

        return $medicine->update(array_filter($updateData, fn($value) => !is_null($value)));
    }

    public function delete(Medicine $medicine): bool
    {
        return $medicine->delete();
    }

    public function getFormData(): array
    {
        return [
            'categories'       => ProductCategory::getAllCategoriesWithDepth(),
            'parentCategories' => ProductCategory::getAllCategoriesWithDepth(),
            'manufacturers'    => Manufacturer::all(),
            'drugRoutes'       => DrugRoute::all(),
            'positions'        => Position::all(),
        ];
    }

    public function generateCodes(): array
    {
        return [
            'ma_hang' => Medicine::generateProductCode(),
            'ma_vach' => Medicine::generateBarcode(),
        ];
    }
}
