<?php

namespace App\Http\Requests\Orders;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
