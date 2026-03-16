<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginGoogleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'idToken' => 'required|string',
            'uid' => 'required|string',
            'email' => 'required|email',
            'name' => 'required|string',
            'photoURL' => 'nullable|string',
        ];
    }
}