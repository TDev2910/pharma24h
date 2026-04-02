<?php

namespace App\Core\Products\Good\Domain\DTOs;

class GoodData
{
    public function __construct(
        public ?string $ma_hang = null,
        public ?string $ma_vach = null,
        public ?string $ten_hang_hoa = null,
        public ?string $ten_viet_tat = null,
        public ?int $nhom_hang_id = null,
        public ?float $gia_von = 0,
        public ?float $gia_ban = 0,
        public ?float $gia_khuyen_mai = 0,
        public ?int $ton_khuyen_mai = 0,
        public ?int $manufacturer_id = null,
        public ?string $quy_cach_dong_goi = null,   
        public ?string $nuoc_san_xuat = null,
        public ?int $ton_kho = 0,
        public ?int $ton_cao_nhat = 0,
        public ?int $ton_thap_nhat = 0,
        public ?int $position_id = null,
        public ?float $trong_luong = 0,
        public ?string $don_vi_tinh = null,
        public bool $ban_truc_tiep = true,
        public ?string $slug = null,
        public ?string $mo_ta = null,
        public mixed $image = null,
    ) {}
}