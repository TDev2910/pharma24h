<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            [
                'name' => 'Chi nhánh Vũng Tàu',
                'address' => '123 Đường Trần Phú, Phường 1, Thành phố Vũng Tàu, Tỉnh Bà Rịa - Vũng Tàu',
                'phone_number' => '0254.123.4567',
                'description' => 'Chi nhánh chính tại thành phố Vũng Tàu, phục vụ khách hàng khu vực miền Nam'
            ],
            [
                'name' => 'Chi nhánh Hồ Chí Minh',
                'address' => '456 Đường Nguyễn Huệ, Phường Bến Nghé, Quận 1, Thành phố Hồ Chí Minh',
                'phone_number' => '028.1234.5678',
                'description' => 'Chi nhánh tại trung tâm thành phố Hồ Chí Minh, phục vụ khách hàng khu vực nội thành'
            ]
        ];

        foreach ($branches as $branch) {
            Branch::firstOrCreate(
                ['name' => $branch['name']],
                $branch
            );
        }

        $this->command->info('Đã tạo thành công ' . count($branches) . ' chi nhánh!');
    }
}
