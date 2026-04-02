<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Models\Goods;
use App\Core\Products\Good\Ports\Outbound\GoodRepositoryInterface;
use App\Core\Products\Good\Domain\DTOs\GoodData;
use App\Models\ProductCategory;
use App\Models\Manufacturer;
use App\Models\Position;

class GoodRepository implements GoodRepositoryInterface
{
    public function getPaginatedGoods(?string $search, int $perPage)
    {
        $query = Goods::with(['category', 'manufacturer', 'position']);

        $query->when($search, function ($q, $search) {
            $q->where(function ($subQ) use ($search) {
                $subQ->where('ten_hang_hoa', 'like', "%{$search}%")
                    ->orWhere('ma_hang', 'like', "%{$search}%")
                    ->orWhere('ma_vach', 'like', "%{$search}%");
            });
        });

        return $query->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getFilteredGoods(array $filters, int $perPage)
    {
        $query = Goods::with(['category', 'manufacturer', 'position']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('ten_hang_hoa', 'LIKE', "%{$search}%")
                    ->orWhere('ma_hang', 'LIKE', "%{$search}%")
                    ->orWhere('ma_vach', 'LIKE', "%{$search}%");
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

    public function findById(int|string $id): ?Goods
    {
        return Goods::find($id);
    }

    public function findByIdWithRelations(int|string $id): ?Goods
    {
        return Goods::with(['category', 'manufacturer', 'position'])->find($id);
    }

    public function create(GoodData $data): Goods
    {
        return Goods::create([
            'ten_hang_hoa'      => $data->ten_hang_hoa,
            'ma_hang'           => $data->ma_hang,
            'ma_vach'           => $data->ma_vach,
            'ten_viet_tat'      => $data->ten_viet_tat,
            'nhom_hang_id'      => $data->nhom_hang_id,
            'gia_von'           => $data->gia_von,
            'ton_kho'           => $data->ton_kho,
            'gia_ban'           => $data->gia_ban,
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
            'gia_khuyen_mai'    => $data->gia_khuyen_mai ?? 0,
            'ton_khuyen_mai'    => $data->ton_khuyen_mai ?? 0,
        ]);
    }

    public function update(Goods $good, GoodData $data): bool
    {
        $updateData = [
            'ten_hang_hoa'      => $data->ten_hang_hoa,
            'ma_hang'           => $data->ma_hang,
            'ma_vach'           => $data->ma_vach,
            'ten_viet_tat'      => $data->ten_viet_tat,
            'nhom_hang_id'      => $data->nhom_hang_id,
            'gia_von'           => $data->gia_von,
            'ton_kho'           => $data->ton_kho,
            'gia_ban'           => $data->gia_ban,
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
            'gia_khuyen_mai'    => $data->gia_khuyen_mai ?? 0,
            'ton_khuyen_mai'    => $data->ton_khuyen_mai ?? 0,
        ];

        if ($data->image) {
            $updateData['image'] = $data->image;
        }

        return $good->update(array_filter($updateData, fn($value) => !is_null($value)));
    }

    public function delete(Goods $good): bool
    {
        return $good->delete();
    }

    public function getFormData(): array
    {
        return [
            'categories'       => ProductCategory::getAllCategoriesWithDepth(),
            'parentCategories' => ProductCategory::getAllCategoriesWithDepth(),
            'manufacturers'    => Manufacturer::all(),
            'positions'        => Position::all(),
        ];
    }

    public function generateCodes(): array
    {
        return [
            'ma_hang' => Goods::generateProductCode(),
            'ma_vach' => Goods::generateBarcode(),
        ];
    }
}
