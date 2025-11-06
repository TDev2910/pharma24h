<?php

namespace Database\Seeders;

use App\Models\Shift;
use App\Models\Branch;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy các chi nhánh
        $branches = Branch::all();
        
        $shiftTemplates = [
            [
                'name' => 'Buổi Sáng',
                'start_time' => '07:00:00',
                'end_time' => '11:00:00',
            ],
            [
                'name' => 'Buổi Chiều',
                'start_time' => '13:00:00',
                'end_time' => '17:00:00',
            ],
            [
                'name' => 'Buổi Tối',
                'start_time' => '18:00:00',
                'end_time' => '22:00:00',
            ]
        ];

        // Tạo ca cho mỗi chi nhánh
        foreach ($branches as $branch) {
            foreach ($shiftTemplates as $template) {
                Shift::firstOrCreate(
                    [
                        'name' => $template['name'],
                        'branch_id' => $branch->id
                    ],
                    [
                        'start_time' => $template['start_time'],
                        'end_time' => $template['end_time'],
                    ]
                );
            }
        }

        // Tạo ca chung (không thuộc chi nhánh nào)
        foreach ($shiftTemplates as $template) {
            Shift::firstOrCreate(
                [
                    'name' => $template['name'],
                    'branch_id' => null
                ],
                [
                    'start_time' => $template['start_time'],
                    'end_time' => $template['end_time'],
                ]
            );
        }

        $this->command->info('Đã tạo thành công ca làm việc cho tất cả chi nhánh!');
    }
}
