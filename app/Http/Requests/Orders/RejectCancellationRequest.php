<?php

namespace App\Http\Requests\Orders;

use App\Http\Requests\BaseRequest;
 
class RejectCancellationRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'note' => 'required|string|max:1000'
        ];
    }
    
    public function messages(): array
    {
        return [
            'note.required' => 'Vui lòng nhập lý do từ chối hủy đơn.',
            'note.max'      => 'Lý do từ chối không được vượt quá 1000 ký tự.'
        ];
    }
}
