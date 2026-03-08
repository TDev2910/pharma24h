<?php

namespace App\Http\Requests\Customer;

use App\Core\Customer\Domain\DTOs\CustomerData;

class StoreCustomerRequest extends BaseCustomerRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'ward' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên khách hàng',
            'email.unique' => 'Email đã được sử dụng',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
        ];
    }

    /**
     * Convert the validated request to a Domain DTO.
     */
    public function toDTO(): CustomerData
    {
        return new CustomerData(
            name: $this->validated('name'),
            email: $this->validated('email'),
            phone: $this->validated('phone') ?? null,
            address: $this->validated('address') ?? null,
            province: $this->safeInput($this->validated('province') ?? null),
            district: $this->safeInput($this->validated('district') ?? null),
            ward: $this->safeInput($this->validated('ward') ?? null),
            password: $this->validated('password') ?? null
        );
    }
}
