<?php

namespace App\Core\Products\Services\Domain\DTOs;

readonly class ServiceData
{
    public function __construct(
        public ?string $ma_dich_vu = null,
        public string $ten_dich_vu,
        public ?int $nhom_hang_id = null,
        public ?int $doctor_id = null,
        public float $gia_dich_vu = 0,
        public ?string $mo_ta = null,
        public mixed $image = null,
        public bool $ban_truc_tiep = false,
        public string $hinh_thuc = 'tai_nha_thuoc',
        public ?int $thoi_gian_thuc_hien = null,
        public string $trang_thai = 'kich_hoat',
        public ?string $ghi_chu = null,
        public ?int $created_by = null,
        public ?int $updated_by = null,
    ) {}
}
