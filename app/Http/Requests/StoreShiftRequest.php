<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShiftRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'branch_id' => 'nullable|exists:branches,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên ca làm việc là bắt buộc',
            'start_time.required' => 'Giờ bắt đầu là bắt buộc',
            'start_time.date_format' => 'Giờ bắt đầu không đúng định dạng (HH:mm)',
            'end_time.required' => 'Giờ kết thúc là bắt buộc',
            'end_time.date_format' => 'Giờ kết thúc không đúng định dạng (HH:mm)',
            'end_time.after' => 'Giờ kết thúc phải sau giờ bắt đầu',
        ];
    }
}
