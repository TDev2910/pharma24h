<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $employeeId = $this->route('employee');

        return [
            // Thông tin cơ bản
            'full_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->employee?->user_id)
            ],
            'phone_number' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('employees', 'phone_number')->ignore($employeeId)
            ],
            'employee_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('employees', 'employee_code')->ignore($employeeId)
            ],
            
            // Thông tin công việc
            'department_id' => 'nullable|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'branch_id' => 'nullable|exists:branches,id',
            'start_date' => 'nullable|date',
            
            // Thông tin lương
            'salary_type' => 'required|in:fixed,per_hour',
            'salary_level' => 'required|numeric|min:0',
            
            // Thông tin cá nhân
            'dob' => 'nullable|date|before:today',
            'gender' => 'nullable|in:nam,nữ',
            'address' => 'nullable|string',
            'id_card_number' => 'nullable|string|max:25',
            
            // Các mảng động
            'allowances' => 'nullable|array',
            'allowances.*.name' => 'required_with:allowances|string|max:255',
            'allowances.*.amount' => 'required_with:allowances|numeric|min:0',
            'allowances.*.type' => 'required_with:allowances|in:fixed_daily,fixed_monthly,percent_salary',
            
            'targets' => 'nullable|array',
            'targets.*.activity_type' => 'required_with:targets|string|max:255',
            'targets.*.target_amount' => 'required_with:targets|numeric|min:0',
            'targets.*.bonus_type' => 'required_with:targets|in:fixed,percent',
            'targets.*.bonus_value' => 'required_with:targets|numeric|min:0',
            
            'deductions' => 'nullable|array',
            'deductions.*.reason' => 'required_with:deductions|string|max:255',
            'deductions.*.amount' => 'required_with:deductions|numeric|min:0',
            'deductions.*.frequency' => 'required_with:deductions|in:one-time,monthly',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'full_name.required' => 'Họ tên là bắt buộc',
            'email.required' => 'Email là bắt buộc',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email đã tồn tại trong hệ thống',
            'phone_number.unique' => 'Số điện thoại đã được sử dụng',
            'employee_code.unique' => 'Mã nhân viên đã tồn tại',
            'salary_type.required' => 'Loại lương là bắt buộc',
            'salary_level.required' => 'Mức lương là bắt buộc',
            'salary_level.min' => 'Mức lương phải lớn hơn hoặc bằng 0',
            'dob.before' => 'Ngày sinh phải trước ngày hiện tại',
        ];
    }
}
