<?php

namespace App\Http\Requests\Doctor;

use App\Core\Doctor\Domain\DTOs\DoctorData;
use App\Http\Requests\BaseRequest;

class UpdateDoctorRequest extends BaseRequest
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
        $doctorId = $this->route('doctor');
        
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $doctorId,
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'province' => 'nullable',
            'ward' => 'nullable',
            'gender' => 'nullable',
            'specialty' => 'nullable|string',
            'degree' => 'nullable|string',
            'notes' => 'nullable|string',
            'avatar' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên bác sĩ',
            'email.unique' => 'Email đã được sử dụng',
        ];
    }

    public function toDTO(): DoctorData
    {
        return new DoctorData(
            id: (int)$this->route('doctor'),
            name: $this->validated('name'),
            email: $this->validated('email'),
            phone: $this->validated('phone') ?? null,
            address: $this->validated('address') ?? null,
            province: $this->safeInput($this->input('province')),
            ward: $this->safeInput($this->input('ward')),
            avatar: $this->validated('avatar') ?? null,
            description: $this->validated('notes') ?? null,
            specialization: $this->validated('specialty') ?? null,
            education: $this->validated('degree') ?? null,
        );
    }
}
