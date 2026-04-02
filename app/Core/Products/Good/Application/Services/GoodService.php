<?php

namespace App\Core\Products\Good\Application\Services;

use App\Core\Products\Good\Domain\DTOs\GoodData;
use App\Core\Products\Good\Ports\Inbound\GoodUseCaseInterface;
use App\Core\Products\Good\Ports\Outbound\GoodRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GoodService implements GoodUseCaseInterface
{
    public function __construct(
        private readonly GoodRepositoryInterface $repository
    ) {}

    public function getGoodList(?string $search, int $perPage): array
    {
        $paginator = $this->repository->getPaginatedGoods($search, $perPage);

        $paginator->through(fn($good) => tap($good, function ($g) {
            $g->image_url = $g->image ? Storage::url($g->image) : null;
        }));

        return ['goods' => $paginator];
    }

    public function getFilteredGoods(array $filters, int $perPage): array
    {
        $paginator = $this->repository->getFilteredGoods($filters, $perPage);

        $paginator->through(fn($good) => tap($good, function ($g) {
            $g->image_url = $g->image ? Storage::url($g->image) : null;
        }));

        return [
            'success'    => true,
            'data'       => $paginator->items(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
            ],
        ];
    }

    public function getGoodById(int|string $id): array
    {
        $good = $this->repository->findByIdWithRelations($id);

        if (!$good) {
            throw new \RuntimeException("Không tìm thấy hàng với ID: {$id}");
        }

        $good->image_url = $good->image ? Storage::url($good->image) : null;

        return [
            'success' => true,
            'good'    => $good,
        ];
    }

    public function createGood(GoodData $data): array
    {
        $imagePath = $this->uploadImage($data->image);

        $slug = Str::slug($data->ten_hang_hoa) . '-' . time();

        $readyData = new GoodData(
            ten_hang_hoa: $data->ten_hang_hoa,
            ma_hang: $data->ma_hang,
            ma_vach: $data->ma_vach,
            ten_viet_tat: $data->ten_viet_tat,
            nhom_hang_id: $data->nhom_hang_id,
            gia_von: $data->gia_von,
            ton_kho: $data->ton_kho,
            gia_ban: $data->gia_ban,
            quy_cach_dong_goi: $data->quy_cach_dong_goi,
            manufacturer_id: $data->manufacturer_id,
            nuoc_san_xuat: $data->nuoc_san_xuat,
            ton_thap_nhat: $data->ton_thap_nhat,
            ton_cao_nhat: $data->ton_cao_nhat,
            position_id: $data->position_id,
            trong_luong: $data->trong_luong,
            don_vi_tinh: $data->don_vi_tinh,
            ban_truc_tiep: $data->ban_truc_tiep,
            mo_ta: $data->mo_ta,
            image: $imagePath,
            slug: $slug,
        );

        $good = $this->repository->create($readyData);

        return [
            'success' => true,
            'message' => 'Hàng đã được thêm thành công.',
            'data'    => $good,
        ];
    }

    public function updateGood(int $id, GoodData $data): array
    {
        $good = $this->repository->findById($id);

        if (!$good) {
            throw new \RuntimeException("Không tìm thấy hàng với ID: {$id}");
        }

        // Kiểm tra logic nghiệp vụ
        $tonKho = $data->ton_kho ?? $good->ton_kho;
        if (!is_null($data->ton_khuyen_mai) && $data->ton_khuyen_mai > $tonKho) {
            throw new \InvalidArgumentException('Tồn khuyến mãi không được lớn hơn tổng tồn kho.');
        }

        //Tạo Slug
        $slug = ($data->ten_hang_hoa && $data->ten_hang_hoa !== $good->ten_hang_hoa)
            ? Str::slug($data->ten_hang_hoa) . '-' . time()
            : $good->slug;

        //Xử lý ảnh
        $imagePath = $good->image;
        if ($data->image instanceof UploadedFile) {
            $this->deleteImage($good->image);
            $imagePath = $this->uploadImage($data->image);
        }

        $readyData = new GoodData(
            ten_hang_hoa: $data->ten_hang_hoa         ?? $good->ten_hang_hoa,
            ma_hang: $data->ma_hang           ?? $good->ma_hang,
            ma_vach: $data->ma_vach           ?? $good->ma_vach,
            ten_viet_tat: $data->ten_viet_tat      ?? $good->ten_viet_tat,
            nhom_hang_id: $data->nhom_hang_id      ?? $good->nhom_hang_id,
            gia_von: $data->gia_von           ?? $good->gia_von,
            ton_kho: $data->ton_kho           ?? $good->ton_kho,
            gia_ban: $data->gia_ban           ?? $good->gia_ban,
            ton_thap_nhat: $data->ton_thap_nhat     ?? $good->ton_thap_nhat,
            ton_cao_nhat: $data->ton_cao_nhat      ?? $good->ton_cao_nhat,
            position_id: $data->position_id       ?? $good->position_id,
            trong_luong: $data->trong_luong       ?? $good->trong_luong,
            don_vi_tinh: $data->don_vi_tinh       ?? $good->don_vi_tinh,
            ban_truc_tiep: $data->ban_truc_tiep     ?? $good->ban_truc_tiep,
            mo_ta: $data->mo_ta             ?? $good->mo_ta,
            image: $imagePath,
            slug: $slug,
        );

        $updated = $this->repository->update($good, $readyData);

        if (!$updated) {
            throw new \RuntimeException('Không thể cập nhật hàng.');
        }

        return [
            'success' => true,
            'message' => 'Hàng đã được cập nhật thành công.',
            'data'    => $this->repository->findById($id),
        ];
    }

    public function deleteGood(int $id): array
    {
        $good = $this->repository->findById($id);

        if (!$good) {
            throw new \RuntimeException("Không tìm thấy hàng với ID: {$id}");
        }

        $this->deleteImage($good->image);

        $deleted = $this->repository->delete($good);

        if (!$deleted) {
            throw new \RuntimeException('Không thể xóa hàng.');
        }

        return [
            'success' => true,
            'message' => 'Hàng đã được xóa thành công.',
        ];
    }

    private function deleteImage(?string $imagePath): void
    {
        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }
    }

    private function uploadImage(mixed $image): ?string
    {
        if ($image instanceof UploadedFile) {
            return $image->store('products', 'public');
        }

        return null;
    }

    public function getFormData(): array
    {
        return $this->repository->getFormData();
    }

    public function generateCodes(): array
    {
        return $this->repository->generateCodes();
    }
}
