<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Goods;
use App\Models\ProductCategory;
use App\Models\Manufacturer;
use App\Models\Position;

class GoodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo categories nếu chưa có
        $categories = [
            'Thực phẩm chức năng',
            'Thiết bị y tế',
            'Vật tư y tế',
            'Dụng cụ y tế'
        ];

        foreach ($categories as $categoryName) {
            ProductCategory::firstOrCreate(['name' => $categoryName]);
        }

        // Tạo manufacturers nếu chưa có
        $manufacturers = [
            'Công ty TNHH Dược phẩm ABC',
            'Công ty CP Thiết bị Y tế XYZ',
            'Công ty TNHH Vật tư Y tế DEF'
        ];

        foreach ($manufacturers as $manufacturerName) {
            Manufacturer::firstOrCreate(['name' => $manufacturerName]);
        }

        // Tạo positions nếu chưa có
        $positions = [
            'Kệ A - Tầng 1',
            'Kệ B - Tầng 1',
            'Kệ C - Tầng 2',
            'Tủ lạnh - Tầng 1'
        ];

        foreach ($positions as $positionName) {
            Position::firstOrCreate(['name' => $positionName]);
        }

        // Dữ liệu mẫu cho hàng hóa
        $goodsData = [
            [
                'ma_hang' => 'TPCN-001',
                'ten_hang_hoa' => 'Vitamin C 1000mg',
                'ma_vach' => '8934567890123',
                'gia_ban' => 150000,
                'gia_von' => 120000,
                'ton_kho' => 50,
                'ton_thap_nhat' => 10,
                'ton_cao_nhat' => 100,
                'trong_luong' => 100,
                'mo_ta' => 'Vitamin C tăng cường sức đề kháng',
                'quy_cach_dong_goi' => 'Hộp 30 viên',
                'don_vi_tinh' => 'Viên',
                'ban_truc_tiep' => true,
                'nuoc_san_xuat' => 'Việt Nam',
                'nhom_hang_id' => ProductCategory::where('name', 'Thực phẩm chức năng')->first()->id,
                'manufacturer_id' => Manufacturer::where('name', 'Công ty TNHH Dược phẩm ABC')->first()->id,
                'position_id' => Position::where('name', 'Kệ A - Tầng 1')->first()->id,
            ],
            [
                'ma_hang' => 'TBYT-001',
                'ten_hang_hoa' => 'Máy đo huyết áp điện tử',
                'ma_vach' => '8934567890124',
                'gia_ban' => 850000,
                'gia_von' => 700000,
                'ton_kho' => 15,
                'ton_thap_nhat' => 5,
                'ton_cao_nhat' => 30,
                'trong_luong' => 500,
                'mo_ta' => 'Máy đo huyết áp tự động, dễ sử dụng',
                'quy_cach_dong_goi' => 'Hộp 1 cái',
                'don_vi_tinh' => 'Cái',
                'ban_truc_tiep' => true,
                'nuoc_san_xuat' => 'Trung Quốc',
                'nhom_hang_id' => ProductCategory::where('name', 'Thiết bị y tế')->first()->id,
                'manufacturer_id' => Manufacturer::where('name', 'Công ty CP Thiết bị Y tế XYZ')->first()->id,
                'position_id' => Position::where('name', 'Kệ B - Tầng 1')->first()->id,
            ],
            [
                'ma_hang' => 'VTYT-001',
                'ten_hang_hoa' => 'Băng gạc y tế 10cm x 5m',
                'ma_vach' => '8934567890125',
                'gia_ban' => 25000,
                'gia_von' => 20000,
                'ton_kho' => 200,
                'ton_thap_nhat' => 50,
                'ton_cao_nhat' => 500,
                'trong_luong' => 50,
                'mo_ta' => 'Băng gạc y tế chất lượng cao',
                'quy_cach_dong_goi' => 'Cuộn 5m',
                'don_vi_tinh' => 'Cuộn',
                'ban_truc_tiep' => true,
                'nuoc_san_xuat' => 'Việt Nam',
                'nhom_hang_id' => ProductCategory::where('name', 'Vật tư y tế')->first()->id,
                'manufacturer_id' => Manufacturer::where('name', 'Công ty TNHH Vật tư Y tế DEF')->first()->id,
                'position_id' => Position::where('name', 'Kệ C - Tầng 2')->first()->id,
            ],
            [
                'ma_hang' => 'DDYT-001',
                'ten_hang_hoa' => 'Nhiệt kế điện tử',
                'ma_vach' => '8934567890126',
                'gia_ban' => 180000,
                'gia_von' => 150000,
                'ton_kho' => 25,
                'ton_thap_nhat' => 10,
                'ton_cao_nhat' => 50,
                'trong_luong' => 100,
                'mo_ta' => 'Nhiệt kế điện tử đo nhanh, chính xác',
                'quy_cach_dong_goi' => 'Hộp 1 cái',
                'don_vi_tinh' => 'Cái',
                'ban_truc_tiep' => true,
                'nuoc_san_xuat' => 'Trung Quốc',
                'nhom_hang_id' => ProductCategory::where('name', 'Dụng cụ y tế')->first()->id,
                'manufacturer_id' => Manufacturer::where('name', 'Công ty CP Thiết bị Y tế XYZ')->first()->id,
                'position_id' => Position::where('name', 'Kệ A - Tầng 1')->first()->id,
            ],
            [
                'ma_hang' => 'TPCN-002',
                'ten_hang_hoa' => 'Omega-3 1000mg',
                'ma_vach' => '8934567890127',
                'gia_ban' => 320000,
                'gia_von' => 280000,
                'ton_kho' => 30,
                'ton_thap_nhat' => 10,
                'ton_cao_nhat' => 80,
                'trong_luong' => 150,
                'mo_ta' => 'Omega-3 tốt cho tim mạch và não bộ',
                'quy_cach_dong_goi' => 'Hộp 60 viên',
                'don_vi_tinh' => 'Viên',
                'ban_truc_tiep' => true,
                'nuoc_san_xuat' => 'Mỹ',
                'nhom_hang_id' => ProductCategory::where('name', 'Thực phẩm chức năng')->first()->id,
                'manufacturer_id' => Manufacturer::where('name', 'Công ty TNHH Dược phẩm ABC')->first()->id,
                'position_id' => Position::where('name', 'Kệ B - Tầng 1')->first()->id,
            ]
        ];

        foreach ($goodsData as $data) {
            Goods::firstOrCreate(
                ['ma_hang' => $data['ma_hang']],
                $data
            );
        }
    }
}
