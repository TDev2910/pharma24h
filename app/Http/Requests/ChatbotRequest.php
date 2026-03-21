<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatbotRequest extends FormRequest
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
            'message' => 'required|string|max:1000',
        ];
    }
    
    public function messages(): array
    {
        return [
            'message.required' => 'Vui lòng nhập tin nhắn.',
            'message.string' => 'Tin nhắn phải là một chuỗi.',
            'message.max' => 'Tin nhắn không được vượt quá 1000 ký tự.',
        ];
    }
}
