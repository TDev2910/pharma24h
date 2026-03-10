<?php

namespace App\Http\Requests\Orders;

use App\Core\Order\Domain\DTOs\OrderData;

class UpdateOrderInfoRequest extends BaseOrderRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'required|string|max:20',
            'customer_email'   => 'nullable|email|max:255',
            'payment_method'   => 'nullable|string|max:50',
            'delivery_method'  => 'nullable|in:shipping,pickup',
            'shipping_address' => 'nullable|required_if:delivery_method,shipping|string|max:255',
            'province'         => 'nullable|string|max:255',
            'district'         => 'nullable|string|max:255',
            'ward'             => 'nullable|string|max:255',
            'pickup_location'  => 'nullable|required_if:delivery_method,pickup|string|max:255',
            'note'             => 'nullable|string|max:1000',
        ];
    }
    
    public function messages(): array
    {
        return [
            'customer_name.required' => 'Vui lòng nhập tên khách hàng.',
            'customer_phone.required'=> 'Vui lòng nhập số điện thoại.',
            'shipping_address.required_if' => 'Vui lòng nhập địa chỉ giao hàng.',
            'pickup_location.required_if'  => 'Vui lòng chọn địa điểm nhận hàng tại cửa hàng.',
        ];
    }
    
    public function toDTO(): OrderData
    {
        return new OrderData(
            customer_name: $this->validated('customer_name'),
            customer_phone: $this->validated('customer_phone'),
            customer_email: $this->validated('customer_email'),
            shipping_address: $this->validated('shipping_address'),
            province: $this->validated('province'),
            district: $this->validated('district'),
            ward: $this->validated('ward'),
            pickup_location: $this->validated('pickup_location'),
            note: $this->validated('note'),
            delivery_method: $this->validated('delivery_method'),
            payment_method: $this->validated('payment_method')
        );
    }
}
