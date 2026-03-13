<?php

namespace App\Http\Requests\Auth;

use App\Core\Auth\Domain\DTOs\RegisterData;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|regex:/[a-z]/|regex:/^[A-Z]/|regex:/[0-9]/',
            'confirm_password' => 'required|string|same:password',
            'phone' => 'nullable|string|regex:/^[0-9]{10,11}$/',
            'address' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập họ và tên',
            'email.required' => 'Vui lòng nhập địa chỉ email',
            'email.email' => 'Địa chỉ email không hợp lệ',
            'email.max' => 'Email không được vượt quá 255 ký tự',
            'email.unique' => 'Email này đã được sử dụng',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'password.regex' => 'Mật khẩu phải bắt đầu bằng chữ hoa, kèm chữ thường và số',
            'confirm_password.required' => 'Vui lòng xác nhận mật khẩu',
            'confirm_password.same' => 'Mật khẩu xác nhận không khớp',
        ];
    }

    public function toDTO(): RegisterData 
    {
        return new RegisterData(
            name: $this->validated('name'),
            email: $this->validated('email'),
            phone: $this->validated('phone'),
            address: $this->validated('address'),
            password: $this->validated('password'),
            confirm_password: $this->validated('confirm_password')
        );
    }
}
