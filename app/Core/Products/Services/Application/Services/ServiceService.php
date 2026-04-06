<?php

namespace App\Core\Products\Services\Application\Services;

use App\Core\Products\Services\Domain\DTOs\ServiceData;
use App\Core\Products\Services\Ports\Inbound\ServiceUseCaseInterface;
use App\Core\Products\Services\Ports\Outbound\ServiceRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceService implements ServiceUseCaseInterface
{
    public function __construct(
        private readonly ServiceRepositoryInterface $repository
    ) {}

    public function getServiceList(?string $search, int $perPage): array
    {
        $paginator = $this->repository->getPaginatedServices($search, $perPage);

        $paginator->through(fn($service) => tap($service, function ($s) {
            $s->image_url = $s->image ? Storage::url($s->image) : null;
        }));

        return ['services' => $paginator];
    }

    public function getFilteredServices(array $filters, int $perPage): array
    {
        $paginator = $this->repository->getFilteredServices($filters, $perPage);

        $paginator->through(fn($service) => tap($service, function ($s) {
            $s->image_url = $s->image ? Storage::url($s->image) : null;
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

    public function getServiceById(int|string $id): array
    {
        $service = $this->repository->findByIdWithRelations($id);

        if (!$service) {
            throw new \RuntimeException("Không tìm thấy dịch vụ với ID: {$id}");
        }

        $service->image_url = $service->image ? Storage::url($service->image) : null;

        return [
            'success' => true,
            'product' => $service,
        ];
    }

    public function createService(ServiceData $data): array
    {
        $imagePath = $this->uploadImage($data->image);

        // Auto generate ma_dich_vu if not provided
        $maDichVu = $data->ma_dich_vu;
        if (empty($maDichVu)) {
            $maDichVu = 'DV' . date('Ymd') . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        }

        $readyData = new ServiceData(
            ma_dich_vu:          $maDichVu,
            ten_dich_vu:         $data->ten_dich_vu,
            nhom_hang_id:        $data->nhom_hang_id,
            doctor_id:           $data->doctor_id,
            gia_dich_vu:         $data->gia_dich_vu,
            mo_ta:               $data->mo_ta,
            image:               $imagePath,
            ban_truc_tiep:       $data->ban_truc_tiep,
            hinh_thuc:           $data->hinh_thuc,
            thoi_gian_thuc_hien: $data->thoi_gian_thuc_hien,
            trang_thai:          $data->trang_thai,
            ghi_chu:             $data->ghi_chu,
            created_by:          Auth::id(),
            updated_by:          Auth::id(),
        );

        $service = $this->repository->create($readyData);

        return [
            'success' => true,
            'message' => 'Dịch vụ đã được tạo thành công!',
            'data'    => $service->load('category'),
        ];
    }

    public function updateService(int $id, ServiceData $data): array
    {
        $service = $this->repository->findById($id);

        if (!$service) {
            throw new \RuntimeException("Không tìm thấy dịch vụ với ID: {$id}");
        }

        // Xử lý ảnh
        $imagePath = $service->image;
        if ($data->image instanceof UploadedFile) {
            $this->deleteImage($service->image);
            $imagePath = $this->uploadImage($data->image);
        }

        $readyData = new ServiceData(
            ma_dich_vu:          $data->ma_dich_vu          ?? $service->ma_dich_vu,
            ten_dich_vu:         $data->ten_dich_vu         ?? $service->ten_dich_vu,
            nhom_hang_id:        $data->nhom_hang_id        ?? $service->nhom_hang_id,
            doctor_id:           $data->doctor_id           ?? $service->doctor_id,
            gia_dich_vu:         $data->gia_dich_vu         ?? $service->gia_dich_vu,
            mo_ta:               $data->mo_ta               ?? $service->mo_ta,
            image:               $imagePath,
            ban_truc_tiep:       $data->ban_truc_tiep,
            hinh_thuc:           $data->hinh_thuc           ?? $service->hinh_thuc,
            thoi_gian_thuc_hien: $data->thoi_gian_thuc_hien ?? $service->thoi_gian_thuc_hien,
            trang_thai:          $data->trang_thai          ?? $service->trang_thai,
            ghi_chu:             $data->ghi_chu             ?? $service->ghi_chu,
            updated_by:          Auth::id(),
        );

        $this->repository->update($service, $readyData);

        return [
            'success' => true,
            'message' => 'Dịch vụ đã được cập nhật thành công!',
            'data'    => $service->fresh()->load(['category', 'doctor']),
        ];
    }

    public function deleteService(int $id): array
    {
        $service = $this->repository->findById($id);

        if (!$service) {
            throw new \RuntimeException("Không tìm thấy dịch vụ với ID: {$id}");
        }

        $this->deleteImage($service->image);
        $this->repository->delete($service);

        return [
            'success' => true,
            'message' => 'Dịch vụ đã được xóa thành công!',
        ];
    }

    public function updateServiceStatus(int $id, string $status): array
    {
        $service = $this->repository->findById($id);

        if (!$service) {
            throw new \RuntimeException("Không tìm thấy dịch vụ với ID: {$id}");
        }

        $service->update([
            'trang_thai' => $status,
            'updated_by' => Auth::id()
        ]);

        return [
            'success' => true,
            'message' => 'Trạng thái dịch vụ đã được cập nhật!',
            'service' => $service->fresh()
        ];
    }

    private function uploadImage(mixed $image): ?string
    {
        if ($image instanceof UploadedFile) {
            $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            return $image->storeAs('services', $imageName, 'public');
        }

        return null;
    }

    private function deleteImage(?string $imagePath): void
    {
        if ($imagePath && Storage::disk('public')->exists($imagePath)) {
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
