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

    /**
     * Xuất file Excel cho một phiếu đặt hàng với chi tiết sản phẩm
     */
    public function downloadSingle(array $data, string $filename = null)
    {
        $filePath = $this->exportSingle($data, $filename);
        
        if (!file_exists($filePath)) {
            throw new \Exception('File Excel không được tạo thành công');
        }
        
        $filename = $filename ?: $this->getDefaultFilename();
        return response()->download($filePath, $filename . '.xlsx')
            ->deleteFileAfterSend(true);
    }

    public function exportSingle(array $data, string $filename = null): string
    {
        $this->data = $this->prepareSingleData($data);
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

    protected function prepareSingleData(array $data): array
    {
        $result = [];
        
        // Phần header - Thông tin phiếu đặt hàng
        $result[] = ['PHIẾU ĐẶT HÀNG'];
        $result[] = []; // Dòng trống
        
        $result[] = ['Mã đặt hàng:', $data['import_code'] ?? ''];
        $result[] = ['Nhà cung cấp:', $data['supplier_name'] ?? ''];
        $result[] = ['Mã NCC:', $data['ma_nha_cung_cap'] ?? ''];
        $result[] = ['Ngày đặt hàng:', $this->formatDate($data['created_at'] ?? '')];
        $result[] = ['Trạng thái:', $this->getStatusText($data['status'] ?? '')];
        $result[] = ['Ghi chú:', $data['note'] ?? 'Không có ghi chú'];
        $result[] = []; // Dòng trống
        
        // Phần chi tiết sản phẩm
        $result[] = ['CHI TIẾT SẢN PHẨM ĐẶT HÀNG'];
        $result[] = []; // Dòng trống
        
        // Tiêu đề cột cho chi tiết sản phẩm
        $result[] = [
            'STT',
            'Tên sản phẩm',
            'Loại',
            'Số lượng',
            'Đơn giá',
            'Giảm giá',
            'Thành tiền',
            'Ghi chú'
        ];
        
        // Dữ liệu sản phẩm
        $stt = 1;
        foreach ($data['items'] ?? [] as $item) {
            $result[] = [
                $stt++,
                $item['product_name'] ?? 'N/A',
                $item['product_type'] ?? '',
                $item['quantity'] ?? 0,
                $this->formatCurrency($item['unit_price'] ?? 0),
                $this->formatCurrency($item['discount'] ?? 0),
                $this->formatCurrency($item['total_price'] ?? 0),
                $item['note'] ?? ''
            ];
        }
        
        $result[] = []; // Dòng trống
        
        // Phần tổng kết
        $result[] = ['TỔNG KẾT'];
        $result[] = ['Tổng tiền hàng:', $this->formatCurrency($data['total_amount'] ?? 0)];
        $result[] = ['Tổng giảm giá:', $this->formatCurrency($data['discount'] ?? 0)];
        $result[] = ['Cần trả NCC:', $this->formatCurrency($data['supplier_pay'] ?? 0)];
        $result[] = ['NCC đã trả:', $this->formatCurrency($data['supplier_paid'] ?? 0)];
        
        return $result;
    }
}
