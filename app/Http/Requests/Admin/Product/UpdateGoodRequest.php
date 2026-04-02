<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;
use App\Core\Products\Good\Domain\DTOs\GoodData;

class UpdateGoodRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Lấy ID của sản phẩm đang cập nhật
        $id = $this->route('good') ?: $this->route('id') ?: $this->id;

        return [
            'ten_hang_hoa'      => 'nullable|string|max:255',
            'ten_viet_tat'      => 'nullable|string|max:100',
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
            'gia_khuyen_mai'    => 'nullable|numeric|min:0',
            'ton_khuyen_mai'    => 'nullable|integer|min:0',
        ];
    }

    public function toDTO(): GoodData
    {
        return new GoodData(
            ten_hang_hoa:      $this->validated('ten_hang_hoa'),
            ten_viet_tat:      $this->validated('ten_viet_tat'),
            nhom_hang_id:      $this->validated('nhom_hang_id'),
            gia_von:           $this->validated('gia_von'),
            gia_ban:           $this->validated('gia_ban'),
            ton_thap_nhat:     $this->validated('ton_thap_nhat'),
            ton_cao_nhat:      $this->validated('ton_cao_nhat'),
            mo_ta:             $this->validated('mo_ta'),
            image:             $this->validated('image'),
            manufacturer_id:   $this->validated('manufacturer_id'),
            position_id:       $this->validated('position_id'),
            nuoc_san_xuat:     $this->validated('nuoc_san_xuat'),
            quy_cach_dong_goi: $this->validated('quy_cach_dong_goi'),
            trong_luong:       $this->validated('trong_luong'),
            don_vi_tinh:       $this->validated('don_vi_tinh'),
            ban_truc_tiep:     $this->has('ban_truc_tiep') ? (bool)$this->ban_truc_tiep : true,
            gia_khuyen_mai:    $this->validated('gia_khuyen_mai'),
            ton_khuyen_mai:    $this->validated('ton_khuyen_mai'),
        );
    }
}
