<?php

namespace App\Services\Excel\Import;

use App\Models\Medicine;
use App\Models\Goods;
use App\Models\ProductCategory;
use App\Models\Manufacturer;

class ProductImport extends BaseImport
{
    public function model(array $row)
    {
        // Validate dữ liệu
        if (!$this->validateData($row)) {
            return null;
        }

        // Transform dữ liệu
        $data = $this->transformData($row);

        // Tạo Product dựa trên loại
        if ($data['product_type'] === 'medicine') {
            $product = Medicine::create($data);
        } else {
            $product = Goods::create($data);
        }

        return $product;
    }

    public function rules(): array
    {
        return [
            'ma_hang' => 'required|string|max:255|unique:medicines,ma_hang|unique:goods,ma_hang',
            'ten_san_pham' => 'required|string|max:255',
            'product_type' => 'required|string|in:medicine,goods',
            'nhom_hang_id' => 'required|exists:product_categories,id',
            'manufacturer_id' => 'nullable|exists:manufacturers,id',
            'gia_von' => 'required|numeric|min:0',
            'gia_ban' => 'required|numeric|min:0',
            'ton_kho' => 'required|numeric|min:0',
            'don_vi_tinh' => 'required|string|max:50',
            'ban_truc_tiep' => 'nullable|boolean',
        ];
    }

    protected function validateData(array $row): bool
    {
        // Kiểm tra mã hàng có trùng không
        if (isset($row['ma_hang'])) {
            $existingMedicine = Medicine::where('ma_hang', $row['ma_hang'])->first();
            $existingGoods = Goods::where('ma_hang', $row['ma_hang'])->first();
            
            if ($existingMedicine || $existingGoods) {
                $this->errors[] = [
                    'row' => $row['row'] ?? 0,
                    'message' => 'Mã hàng đã tồn tại'
                ];
                return false;
            }
        }

        // Kiểm tra nhóm hàng có tồn tại không
        if (isset($row['nhom_hang_id'])) {
            $category = ProductCategory::find($row['nhom_hang_id']);
            if (!$category) {
                $this->errors[] = [
                    'row' => $row['row'] ?? 0,
                    'message' => 'Nhóm hàng không tồn tại'
                ];
                return false;
            }
        }

        return true;
    }

    protected function transformData(array $row): array
    {
        $data = [
            'ma_hang' => $row['ma_hang'] ?? '',
            'ten_hang_hoa' => $row['ten_san_pham'] ?? '',
            'ten_thuoc' => $row['ten_san_pham'] ?? '',
            'nhom_hang_id' => $row['nhom_hang_id'] ?? null,
            'manufacturer_id' => $row['manufacturer_id'] ?? null,
            'gia_von' => $row['gia_von'] ?? 0,
            'gia_ban' => $row['gia_ban'] ?? 0,
            'ton_kho' => $row['ton_kho'] ?? 0,
            'don_vi_tinh' => $row['don_vi_tinh'] ?? '',
            'ban_truc_tiep' => $row['ban_truc_tiep'] ?? true,
        ];

        // Thêm các trường riêng cho Medicine
        if ($row['product_type'] === 'medicine') {
            $data['so_dang_ky'] = $row['so_dang_ky'] ?? '';
            $data['hoat_chat'] = $row['hoat_chat'] ?? '';
            $data['ham_luong'] = $row['ham_luong'] ?? '';
        }

        return $data;
    }
}
