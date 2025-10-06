<?php

namespace App\Services\Excel\Export;

class StockImportExport extends BaseExport
{
    protected function prepareData(array $data): array
    {
        $result = [];
        
        foreach ($data as $import) {
            $result[] = [
                'Mã phiếu nhập' => $import->import_code ?? '',
                'Nhà cung cấp' => $import->supplier->ten_nha_cung_cap ?? '',
                'Mã NCC' => $import->supplier->ma_nha_cung_cap ?? '',
                'Ngày nhập' => $this->formatDate($import->import_date ?? $import->created_at),
                'Tổng tiền' => $this->formatCurrency($import->total_amount ?? 0),
                'Đã trả' => $this->formatCurrency($import->paid_amount ?? 0),
                'Còn lại' => $this->formatCurrency($import->remaining_amount ?? 0),
                'Trạng thái' => $this->getStatusText($import->status ?? ''),
                'Ghi chú' => $import->note ?? '',
            ];
        }
        
        return $result;
    }

    protected function getHeaders(): array
    {
        return [
            'Mã phiếu nhập',
            'Nhà cung cấp',
            'Mã NCC',
            'Ngày nhập',
            'Tổng tiền',
            'Đã trả',
            'Còn lại',
            'Trạng thái',
            'Ghi chú'
        ];
    }

    protected function getColumnWidths(): array
    {
        return [
            'A' => 15, // Mã phiếu nhập
            'B' => 25, // Nhà cung cấp
            'C' => 12, // Mã NCC
            'D' => 20, // Ngày nhập
            'E' => 15, // Tổng tiền
            'F' => 15, // Đã trả
            'G' => 15, // Còn lại
            'H' => 15, // Trạng thái
            'I' => 30, // Ghi chú
        ];
    }

    protected function getDefaultFilename(): string
    {
        return 'danh-sach-nhap-hang-' . date('Y-m-d-H-i-s');
    }

    private function getStatusText($status): string
    {
        $statusMap = [
            'imported' => 'Đã nhập hàng',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            'pending' => 'Chờ xử lý',
        ];
        
        return $statusMap[$status] ?? $status;
    }
}
