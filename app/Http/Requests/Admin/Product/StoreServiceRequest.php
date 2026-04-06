<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;
use App\Core\Products\Services\Domain\DTOs\ServiceData;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_dich_vu'         => 'required|string|max:255',
            'ma_dich_vu'          => 'nullable|string|max:255|unique:services,ma_dich_vu',
            'nhom_hang_id'        => 'nullable|exists:product_categories,id',
            'doctor_id'           => 'nullable|exists:doctors,id',
            'gia_dich_vu'         => 'required|numeric|min:0',
            'mo_ta'               => 'nullable|string',
            'image'               => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ban_truc_tiep'       => 'nullable|boolean',
            'hinh_thuc'           => 'required|in:tai_nha_thuoc,tai_phong_kham,tai_nha_khach',
            'thoi_gian_thuc_hien' => 'nullable|integer|min:1',
            'trang_thai'          => 'required|in:kich_hoat,tam_ngung,luu_tam',
            'ghi_chu'             => 'nullable|string'
        ];
    }

    public function messages(): array
    {
        return [
            'ten_dich_vu.required' => 'Vui lòng nhập tên dịch vụ',
            'gia_dich_vu.required' => 'Vui lòng nhập giá dịch vụ',
            'gia_dich_vu.numeric'  => 'Giá dịch vụ phải là số',
            'hinh_thuc.required'    => 'Vui lòng chọn hình thức dịch vụ',
            'ma_dich_vu.unique'    => 'Mã dịch vụ này đã tồn tại',
        ];
    }

    public function toDTO(): ServiceData
    {
        return new ServiceData(
            ma_dich_vu:          $this->ma_dich_vu,
            ten_dich_vu:         $this->ten_dich_vu,
            nhom_hang_id:        $this->nhom_hang_id ? (int)$this->nhom_hang_id : null,
            doctor_id:           $this->doctor_id ? (int)$this->doctor_id : null,
            gia_dich_vu:         (float)$this->gia_dich_vu,
            mo_ta:               $this->mo_ta,
            image:               $this->file('image'),
            ban_truc_tiep:       $this->boolean('ban_truc_tiep', true),
            hinh_thuc:           $this->hinh_thuc,
            thoi_gian_thuc_hien: $this->thoi_gian_thuc_hien ? (int)$this->thoi_gian_thuc_hien : null,
            trang_thai:          $this->trang_thai,
            ghi_chu:             $this->ghi_chu
        );
    }
}

