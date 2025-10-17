<?php

namespace App\Services\Excel\Export;

use Shuchkin\SimpleXLSXGen; //thư viện simplexlsxgen dùng xuất file excel

class StockImportExport
{
    protected $data = [];
    protected $filename = '';

    public function export(array $data, string $filename = null): string
    {
        $this->data = $this->prepareData($data);
        $this->filename = $filename ?: $this->getDefaultFilename();
        
        $filePath = public_path('exports/' . $this->filename . '.xlsx');
        
        // Tạo thư mục nếu chưa có
        $exportDir = dirname($filePath);
        if (!is_dir($exportDir)) {
            mkdir($exportDir, 0755, true);
        }
        
        // Tạo Excel với SimpleXLSXGen
        SimpleXLSXGen::fromArray($this->data)->saveAs($filePath);
        
        return $filePath;
    }

    public function download(array $data, string $filename = null)
    {
        $filePath = $this->export($data, $filename);
        
        if (!file_exists($filePath)) {
            throw new \Exception('File Excel không được tạo thành công');
        }
        
        return response()->download($filePath, $this->filename . '.xlsx')
            ->deleteFileAfterSend(true);
    }

    protected function prepareData(array $data): array
    {
        $result = [];
        
        // Tiêu đề cột (theo datatable)
        $result[] = [
            'Mã đặt hàng',
            'Thời gian',
            'Nhà cung cấp', 
            'Mã NCC',
            'Cần trả NCC',
            'Trạng thái'
        ];
        
        // dữ liệu truyền vào (theo datatable)
        foreach ($data as $import) {
            $result[] = [
                $import['import_code'] ?? '',                    // Mã đặt hàng
                $this->formatDate($import['created_at'] ?? ''),  // Thời gian
                $import['supplier_name'] ?? '',                  // Nhà cung cấp
                $import['ma_nha_cung_cap'] ?? '',                // Mã NCC
                $this->formatCurrency($import['total_amount'] ?? 0), // Cần trả NCC
                $this->getStatusText($import['status'] ?? '')    // Trạng thái
            ];
        }
        
        return $result;
    }

    protected function getDefaultFilename(): string 
    {
        return 'phieu_nhap_hang_' . date('Y_m_d_H_i_s');
    }

    protected function formatCurrency($amount): string
    {
        return number_format((float)$amount, 0, ',', '.') . ' VND';
    }

    protected function formatDate($date): string
    {
        if (!$date) return '';
        return \Carbon\Carbon::parse($date)->format('d/m/Y H:i');
    }

    protected function getStatusText($status): string
    {
        $statusMap = [
            'pending' => 'Chờ xử lý',
            'temp' => 'Phiếu tạm',
            'ordered' => 'Đã đặt hàng',
            'imported' => 'Đã đặt hàng',  // Map imported thành "Đã đặt hàng" như datatable
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
        ];
        
        return $statusMap[$status] ?? $status;
    }
}
