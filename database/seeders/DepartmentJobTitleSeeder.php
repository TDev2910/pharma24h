<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\JobTitle;
use Illuminate\Database\Seeder;

class DepartmentJobTitleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo dữ liệu mẫu cho Departments (Phòng ban)
        $departments = [
            [
                'name' => 'Phòng Kinh doanh',
                'description' => 'Phụ trách các hoạt động kinh doanh, bán hàng và chăm sóc khách hàng'
            ],
            [
                'name' => 'Phòng Kỹ thuật',
                'description' => 'Phụ trách các dịch vụ kỹ thuật, tư vấn và hỗ trợ khách hàng'
            ],
            [
                'name' => 'Phòng Hành chính',
                'description' => 'Phụ trách các công việc hành chính, nhân sự và quản lý nội bộ'
            ],
            [
                'name' => 'Phòng Kế toán',
                'description' => 'Phụ trách kế toán, tài chính và quản lý ngân sách'
            ],
            [
                'name' => 'Phòng Kho',
                'description' => 'Phụ trách quản lý kho hàng, nhập xuất và kiểm kê'
            ]
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(
                ['name' => $department['name']],
                $department
            );
        }

        // Tạo dữ liệu mẫu cho Job Titles (Chức danh)
        $jobTitles = [
            [
                'name' => 'Dược sĩ bán thuốc',
                'description' => 'Tư vấn và bán thuốc cho khách hàng, đảm bảo an toàn sử dụng thuốc'
            ],
            [
                'name' => 'Thu ngân',
                'description' => 'Thực hiện thanh toán, quản lý tiền mặt và giao dịch với khách hàng'
            ],
            [
                'name' => 'Nhân viên kho',
                'description' => 'Quản lý kho hàng, nhập xuất hàng hóa và kiểm kê tồn kho'
            ],
            [
                'name' => 'Quản lý',
                'description' => 'Quản lý và điều hành hoạt động của cửa hàng/chi nhánh'
            ],
            [
                'name' => 'Nhân viên',
                'description' => 'Nhân viên làm việc tại cửa hàng, hỗ trợ khách hàng và các công việc khác'
            ],
            [
                'name' => 'Kỹ thuật viên',
                'description' => 'Thực hiện các dịch vụ kỹ thuật, tư vấn và hỗ trợ khách hàng'
            ],
            [
                'name' => 'Kế toán viên',
                'description' => 'Thực hiện các công việc kế toán, quản lý tài chính và báo cáo'
            ]
        ];

        foreach ($jobTitles as $jobTitle) {
            JobTitle::firstOrCreate(
                ['name' => $jobTitle['name']],
                $jobTitle
            );
        }

        $this->command->info('Đã tạo thành công ' . count($departments) . ' phòng ban và ' . count($jobTitles) . ' chức danh!');
    }
}
