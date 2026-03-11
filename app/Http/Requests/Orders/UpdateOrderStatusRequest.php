<?php

namespace App\Http\Requests\Orders;

use App\Http\Requests\BaseRequest;
 
class UpdateOrderStatusRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'required|in:pending,confirmed,delivering,completed,cancelled',
            'note'   => 'nullable|string|max:1000'
        ];
    }
    
    public function messages(): array
    {
        return [
            'status.required' => 'Vui lòng chọn trạng thái đơn hàng.',
            'status.in'       => 'Trạng thái đơn hàng không hợp lệ.',
            'note.max'        => 'Ghi chú không được vượt quá 1000 ký tự.'
        ];
    }
}
