<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Helper: Tránh lỗi truy cập mảng trên null hoặc chuỗi
     */
    protected function safeInput($input)
    {
        if (is_array($input)) {
            return $input['name'] ?? null;
        }
        return $input;
    }
}
