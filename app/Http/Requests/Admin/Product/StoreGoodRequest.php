<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;
use App\Core\Products\Good\Domain\DTOs\GoodData;

class StoreGoodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_hang_hoa'      => 'required|string|max:255',
            'ma_hang'           => 'nullable|string|max:50|unique:goods,ma_hang',
            'ma_vach'           => 'nullable|string|unique:goods,ma_vach',
            'ten_viet_tat'      => 'nullable|string|max:100',
            'ton_kho'           => 'required|integer|min:0',
            'gia_ban'           => 'nullable|numeric|min:0',
            'gia_von'           => 'nullable|numeric|min:0',
            'ton_thap_nhat'     => 'nullable|integer|min:0',
            'ton_cao_nhat'      => 'nullable|integer|min:0',
            'mo_ta'             => 'nullable|string',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'nhom_hang_id'      => 'nullable|exists:product_categories,id',
            'manufacturer_id'   => 'nullable|exists:manufacturers,id',
            'position_id'       => 'nullable|exists:positions,id',
            'nuoc_san_xuat'     => 'nullable|string',
            'quy_cach_dong_goi' => 'nullable|string',
            'trong_luong'       => 'nullable|numeric|min:0',
            'don_vi_tinh'       => 'nullable|string|max:50',
            'ban_truc_tiep'     => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'ma_hang.unique'    => 'Mã hàng bạn nhập đang trùng với một sản phẩm khác.',
            'ma_vach.unique'    => 'Mã vạch bạn nhập đang trùng với một sản phẩm khác.',
        ];
    }

    public function toDTO(): GoodData
    {
        return new GoodData(
            ten_hang_hoa:      $this->validated('ten_hang_hoa'),
            ma_hang:           $this->validated('ma_hang'),
            ma_vach:           $this->validated('ma_vach'),
            ten_viet_tat:      $this->validated('ten_viet_tat'),
            ton_kho:           $this->validated('ton_kho'),
            gia_ban:           $this->validated('gia_ban'),
            gia_von:           $this->validated('gia_von'),
            ton_thap_nhat:     $this->validated('ton_thap_nhat'),
            ton_cao_nhat:      $this->validated('ton_cao_nhat'),
            mo_ta:             $this->validated('mo_ta'),
            image:             $this->validated('image'),
            nhom_hang_id:      $this->validated('nhom_hang_id'),
            manufacturer_id:   $this->validated('manufacturer_id'),
            position_id:       $this->validated('position_id'),
            nuoc_san_xuat:     $this->validated('nuoc_san_xuat'),
            quy_cach_dong_goi: $this->validated('quy_cach_dong_goi'),
            trong_luong:       $this->validated('trong_luong'),
            don_vi_tinh:       $this->validated('don_vi_tinh'),
            ban_truc_tiep:     $this->has('ban_truc_tiep') ? (bool)$this->ban_truc_tiep : true,
        );
    }
}
