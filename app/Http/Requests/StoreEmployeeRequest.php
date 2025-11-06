<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
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
            // Thông tin cơ bản
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|string|min:6|max:255',
            'phone_number' => 'nullable|string|max:20|unique:employees,phone_number',
            'employee_code' => 'nullable|string|max:50|unique:employees,employee_code',
            // Thông tin công việc
            'department_id' => 'nullable|exists:departments,id',
            'job_title_id' => 'nullable|exists:job_titles,id',
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

    //Thông báo lỗi
    public function messages(): array
    {
        return [
            'full_name.required' => 'Họ tên là bắt buộc',
            'email.required' => 'Email là bắt buộc',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email đã tồn tại trong hệ thống',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'password.max' => 'Mật khẩu không được vượt quá 255 ký tự',
            'phone_number.unique' => 'Số điện thoại đã được sử dụng',
            'employee_code.unique' => 'Mã nhân viên đã tồn tại',
            'salary_type.required' => 'Loại lương là bắt buộc',
            'salary_level.required' => 'Mức lương là bắt buộc',
            'salary_level.min' => 'Mức lương phải lớn hơn hoặc bằng 0',
            'dob.before' => 'Ngày sinh phải trước ngày hiện tại',
        ];
    }
}
