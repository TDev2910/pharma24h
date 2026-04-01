<?php

namespace App\Core\Products\Medicine\Application\Services;

use App\Core\Products\Medicine\Domain\DTOs\MedicineData;
use App\Core\Products\Medicine\Ports\Inbound\MedicineUseCaseInterface;
use App\Core\Products\Medicine\Ports\Outbound\MedicineRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MedicineService implements MedicineUseCaseInterface
{
    public function __construct(
        private readonly MedicineRepositoryInterface $repository
    ) {}

    /**
     * Lấy danh sách thuốc phân trang, đã được định dạng URL ảnh cho Frontend.
     */
    public function getMedicineList(?string $search, int $perPage): array
    {
        $paginator = $this->repository->getPaginatedMedicines($search, $perPage);

        $paginator->through(fn($medicine) => tap($medicine, function ($m) {
            $m->image_url = $m->image
                ? Storage::url($m->image)
                : null;
        }));

        return ['medicines' => $paginator];
    }

    public function getFilteredMedicines(array $filters, int $perPage): array
    {
        $paginator = $this->repository->getFilteredMedicines($filters, $perPage);

        $paginator->through(fn($medicine) => tap($medicine, function ($m) {
            $m->image_url = $m->image ? Storage::url($m->image) : null;
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

    public function getMedicineById(int|string $id): array
    {
        $medicine = $this->repository->findByIdWithRelations($id);

        if (!$medicine) {
            throw new \RuntimeException("Không tìm thấy thuốc với ID: {$id}");
        }

        $medicine->image_url = $medicine->image ? Storage::url($medicine->image) : null;

        return [
            'success' => true,
            'product' => $medicine,
        ];
    }

    public function createMedicine(MedicineData $data): array
    {
        $imagePath = $this->uploadImage($data->image);

        $slug = Str::slug($data->ten_thuoc) . '-' . time();

        $readyData = new MedicineData(
            ten_thuoc:         $data->ten_thuoc,
            ma_hang:           $data->ma_hang,
            ma_vach:           $data->ma_vach,
            ten_viet_tat:      $data->ten_viet_tat,
            nhom_hang_id:      $data->nhom_hang_id,
            gia_von:           $data->gia_von,
            ton_kho:           $data->ton_kho,
            gia_ban:           $data->gia_ban,
            so_dang_ky:        $data->so_dang_ky,
            hoat_chat:         $data->hoat_chat,
            ham_luong:         $data->ham_luong,
            drugusage_id:      $data->drugusage_id,
            quy_cach_dong_goi: $data->quy_cach_dong_goi,
            manufacturer_id:   $data->manufacturer_id,
            nuoc_san_xuat:     $data->nuoc_san_xuat,
            ton_thap_nhat:     $data->ton_thap_nhat,
            ton_cao_nhat:      $data->ton_cao_nhat,
            position_id:       $data->position_id,
            trong_luong:       $data->trong_luong,
            don_vi_tinh:       $data->don_vi_tinh,
            ban_truc_tiep:     $data->ban_truc_tiep,
            mo_ta:             $data->mo_ta,
            image:             $imagePath,
            slug:              $slug,
        );

        $medicine = $this->repository->create($readyData);

        return [
            'success' => true,
            'message' => 'Thuốc đã được thêm thành công.',
            'data'    => $medicine,
        ];
    }

    public function updateMedicine(int $id, MedicineData $data): array
    {
        $medicine = $this->repository->findById($id);

        if (!$medicine) {
            throw new \RuntimeException("Không tìm thấy thuốc với ID: {$id}");
        }

        // Kiểm tra logic nghiệp vụ
        $tonKho = $data->ton_kho ?? $medicine->ton_kho;
        if (!is_null($data->ton_khuyen_mai) && $data->ton_khuyen_mai > $tonKho) {
            throw new \InvalidArgumentException('Tồn khuyến mãi không được lớn hơn tổng tồn kho.');
        }

        //Tạo Slug
        $slug = ($data->ten_thuoc && $data->ten_thuoc !== $medicine->ten_thuoc)
            ? Str::slug($data->ten_thuoc) . '-' . time()
            : $medicine->slug;

        //Xử lý ảnh
        $imagePath = $medicine->image; // Giữ ảnh cũ theo mặc định
        if ($data->image instanceof UploadedFile) {
            $this->deleteImage($medicine->image); // Xóa ảnh cũ
            $imagePath = $this->uploadImage($data->image);
        }

        $readyData = new MedicineData(
            ten_thuoc:         $data->ten_thuoc         ?? $medicine->ten_thuoc,
            ma_hang:           $data->ma_hang           ?? $medicine->ma_hang,
            ma_vach:           $data->ma_vach           ?? $medicine->ma_vach,
            ten_viet_tat:      $data->ten_viet_tat      ?? $medicine->ten_viet_tat,
            nhom_hang_id:      $data->nhom_hang_id      ?? $medicine->nhom_hang_id,
            gia_von:           $data->gia_von           ?? $medicine->gia_von,
            ton_kho:           $data->ton_kho           ?? $medicine->ton_kho,
            gia_ban:           $data->gia_ban           ?? $medicine->gia_ban,
            so_dang_ky:        $data->so_dang_ky        ?? $medicine->so_dang_ky,
            hoat_chat:         $data->hoat_chat         ?? $medicine->hoat_chat,
            ham_luong:         $data->ham_luong         ?? $medicine->ham_luong,
            drugusage_id:      $data->drugusage_id      ?? $medicine->drugusage_id,
            quy_cach_dong_goi: $data->quy_cach_dong_goi ?? $medicine->quy_cach_dong_goi,
            manufacturer_id:   $data->manufacturer_id   ?? $medicine->manufacturer_id,
            nuoc_san_xuat:     $data->nuoc_san_xuat     ?? $medicine->nuoc_san_xuat,
            ton_thap_nhat:     $data->ton_thap_nhat     ?? $medicine->ton_thap_nhat,
            ton_cao_nhat:      $data->ton_cao_nhat      ?? $medicine->ton_cao_nhat,
            position_id:       $data->position_id       ?? $medicine->position_id,
            trong_luong:       $data->trong_luong       ?? $medicine->trong_luong,
            don_vi_tinh:       $data->don_vi_tinh       ?? $medicine->don_vi_tinh,
            ban_truc_tiep:     $data->ban_truc_tiep,
            mo_ta:             $data->mo_ta             ?? $medicine->mo_ta,
            gia_khuyen_mai:    $data->gia_khuyen_mai,
            ton_khuyen_mai:    $data->ton_khuyen_mai,
            image:             $imagePath,
            slug:              $slug,
        );

        $this->repository->update($medicine, $readyData);

        return [
            'success' => true,
            'message' => 'Thông tin thuốc đã được cập nhật thành công.',
            'data'    => $medicine->fresh()->load(['category', 'manufacturer', 'drugRoute', 'position']),
        ];
    }

    public function deleteMedicine(int $id): array
    {
        $medicine = $this->repository->findById($id);

        if (!$medicine) {
            throw new \RuntimeException("Không tìm thấy thuốc với ID: {$id}");
        }

        $this->deleteImage($medicine->image);
        $this->repository->delete($medicine);

        return [
            'success' => true,
            'message' => 'Xóa thuốc thành công!',
        ];
    }

    /**
     * Lưu file ảnh vào Storage.
     */
    private function uploadImage(mixed $image): ?string
    {
        if ($image instanceof UploadedFile) {
            return $image->store('products', 'public');
        }

        return null;
    }

    /**
     * Xóa file ảnh khỏi Storage nếu tồn tại.
     */
    private function deleteImage(?string $imagePath): void
    {
        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }
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
