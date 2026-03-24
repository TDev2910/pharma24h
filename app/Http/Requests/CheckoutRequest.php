<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Cho phép tất cả người dùng (kể cả chưa đăng nhập)
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'delivery_method' => 'required|in:shipping,pickup',
            'payment_method' => 'required|in:cod,vnpay,sepay',
            'note' => 'nullable|string',
            'customer_email' => 'nullable|email|max:255',
            'shipping_fee' => 'nullable|integer|min:0', // Phí hiển thị cho khách
            'ghn_fee'      => 'nullable|integer|min:0', // Phí gốc GHN (để đối soát)
        ];

        // Thêm rules tùy thuộc vào phương thức giao hàng
        if ($this->input('delivery_method') === 'shipping') {
            $rules['customer_name'] = 'required|string|max:255';
            $rules['customer_phone'] = 'required|string|max:20';
            $rules['shipping_address'] = 'required|string|max:255';
            $rules['province'] = 'required|string|max:100';
            $rules['district'] = 'required|string|max:100';
            $rules['ward'] = 'required|string|max:100';
            // Thêm district_id và ward_code (nullable vì có thể map sau)
            $rules['district_id'] = 'nullable|integer';
            $rules['ward_code'] = 'nullable|string|max:50';
        }
        else
        {
            $rules['customer_name'] = 'required|string|max:255';
            $rules['customer_phone'] = 'required|string|max:20';
            $rules['pickup_location'] = 'required|string|max:255';
        }

        return $rules;
    }
    //Hiển thị lỗi thông báo
    public function messages(): array
    {
        return [
            'customer_name.required' => 'Vui lòng nhập họ tên người nhận hàng',
            'customer_phone.required' => 'Vui lòng nhập số điện thoại',
            'customer_phone.max' => 'Số điện thoại không hợp lệ',
            'shipping_address.required' => 'Vui lòng nhập địa chỉ giao hàng',
            'province.required' => 'Vui lòng chọn tỉnh/thành phố',
            'district.required' => 'Vui lòng chọn quận/huyện',
            'ward.required' => 'Vui lòng chọn phường/xã',
            'pickup_location.required' => 'Vui lòng chọn địa điểm nhận hàng',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ',
        ];
    }
}
