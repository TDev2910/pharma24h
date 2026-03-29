<?php

namespace App\Core\Products\Medicine\Domain\DTOs;

readonly class MedicineData
{
    public function __construct(
        public ?string $ten_thuoc = null,
        public ?string $ma_hang = null,
        public ?string $ten_viet_tat = null,
        public ?int $ton_kho = 0,
        public ?float $gia_ban = 0,
        public ?float $gia_von = 0,
        public ?int $ton_thap_nhat = 0,
        public ?int $khach_dat = 0,
        public ?string $mo_ta = null,
        public mixed $image = null,
        public ?int $nhom_hang_id = null,
        public ?int $manufacturer_id = null,
        public ?int $drugusage_id = null,
        public ?int $position_id = null,
        public ?string $ma_vach = null,
        public ?string $so_dang_ky = null,
        public ?string $hoat_chat = null,
        public ?string $ham_luong = null,
        public ?string $nuoc_san_xuat = null,
        public ?string $quy_cach_dong_goi = null,
        public ?int $ton_cao_nhat = 0,
        public ?float $trong_luong = 0,
        public ?string $don_vi_tinh = null,
        public bool $ban_truc_tiep = true,
        public ?float $gia_khuyen_mai = null,
        public ?int $ton_khuyen_mai = null,
        public ?string $slug = null,
    ) {}
}
