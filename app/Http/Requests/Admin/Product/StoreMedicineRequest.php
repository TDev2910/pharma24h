<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;
use App\Core\Products\Medicine\Domain\DTOs\MedicineData;

class StoreMedicineRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ten_thuoc'         => 'required|string|max:255',
            'ma_hang'           => 'nullable|string|max:50|unique:medicines,ma_hang',
            'ten_viet_tat'      => 'nullable|string|max:100',
            'ton_kho'           => 'required|integer|min:0',
            'gia_ban'           => 'nullable|numeric|min:0',
            'gia_von'           => 'nullable|numeric|min:0',
            'ton_thap_nhat'     => 'nullable|integer|min:0',
            'mo_ta'             => 'nullable|string',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'nhom_hang_id'      => 'nullable|exists:product_categories,id',
            'manufacturer_id'   => 'nullable|exists:manufacturers,id',
            'drugusage_id'      => 'nullable|exists:drug_routes,id',
            'position_id'       => 'nullable|exists:positions,id',
            'ma_vach'           => 'nullable|string|unique:medicines,ma_vach',
            'so_dang_ky'        => 'nullable|string|unique:medicines,so_dang_ky',
            'hoat_chat'         => 'nullable|string',
            'ham_luong'         => 'nullable|string',
            'nuoc_san_xuat'     => 'nullable|string',
            'quy_cach_dong_goi' => 'nullable|string',
            'ton_cao_nhat'      => 'nullable|integer|min:0',
            'trong_luong'       => 'nullable|numeric|min:0',
            'don_vi_tinh'       => 'nullable|string',
            'ban_truc_tiep'     => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'ma_hang.unique'    => 'Mã hàng bạn nhập đang trùng với một sản phẩm khác.',
            'ma_vach.unique'    => 'Mã vạch bạn nhập đang trùng với một sản phẩm khác.',
            'so_dang_ky.unique' => 'Số đăng ký bạn nhập đang trùng với một sản phẩm khác.',
        ];
    }

    public function toDTO(): MedicineData
    {
        return new MedicineData(
            ten_thuoc: $this->validated('ten_thuoc'),
            ma_hang: $this->validated('ma_hang'),
            ten_viet_tat: $this->validated('ten_viet_tat'),
            ton_kho: $this->validated('ton_kho'),
            gia_ban: $this->validated('gia_ban'),
            gia_von: $this->validated('gia_von'),
            ton_thap_nhat: $this->validated('ton_thap_nhat'),
            mo_ta: $this->validated('mo_ta'),
            image: $this->validated('image'),
            nhom_hang_id: $this->validated('nhom_hang_id'),
            manufacturer_id: $this->validated('manufacturer_id'),
            drugusage_id: $this->validated('drugusage_id'),
            position_id: $this->validated('position_id'),
            ma_vach: $this->validated('ma_vach'),
            so_dang_ky: $this->validated('so_dang_ky'),
            hoat_chat: $this->validated('hoat_chat'),
            ham_luong: $this->validated('ham_luong'),
            nuoc_san_xuat: $this->validated('nuoc_san_xuat'),
            quy_cach_dong_goi: $this->validated('quy_cach_dong_goi'),
            ton_cao_nhat: $this->validated('ton_cao_nhat'),
            trong_luong: $this->validated('trong_luong'),
            don_vi_tinh: $this->validated('don_vi_tinh'),
            ban_truc_tiep: $this->boolean('ban_truc_tiep'),
            slug: $this->validated('slug'),
        );
    }
}
