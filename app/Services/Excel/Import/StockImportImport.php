<?php

namespace App\Services\Excel\Import;

use App\Models\StockImport;
use App\Models\StockImportItem;
use App\Models\Supplier;

class StockImportImport extends BaseImport
{
    public function model(array $row)
    {
        // Validate dữ liệu
        if (!$this->validateData($row)) {
            return null;
        }

        // Transform dữ liệu
        $data = $this->transformData($row);

        // Tạo StockImport
        $import = StockImport::create([
            'import_code' => $data['import_code'],
            'supplier_id' => $data['supplier_id'],
            'import_date' => $data['import_date'],
            'status' => $data['status'] ?? 'pending',
            'total_amount' => $data['total_amount'],
            'note' => $data['note'] ?? '',
        ]);

        return $import;
    }

    public function rules(): array
    {
        return [
            'import_code' => 'required|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
            'import_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ];
    }

    protected function validateData(array $row): bool
    {
        // Kiểm tra supplier có tồn tại không
        if (isset($row['supplier_id'])) {
            $supplier = Supplier::find($row['supplier_id']);
            if (!$supplier) {
                $this->errors[] = [
                    'row' => $row['row'] ?? 0,
                    'message' => 'Nhà cung cấp không tồn tại'
                ];
                return false;
            }
        }

        return true;
    }

    protected function transformData(array $row): array
    {
        //format dữ liệu vào
        return [
            'import_code' => $row['ma_phieu_nhap'] ?? $row['import_code'] ?? '',
            'supplier_id' => $row['nha_cung_cap_id'] ?? $row['supplier_id'] ?? null,
            'import_date' => $row['ngay_nhap'] ?? $row['import_date'] ?? now(),
            'status' => $row['trang_thai'] ?? $row['status'] ?? 'pending',
            'total_amount' => $row['tong_tien'] ?? $row['total_amount'] ?? 0,
            'note' => $row['ghi_chu'] ?? $row['note'] ?? '',
        ];
    }
}
