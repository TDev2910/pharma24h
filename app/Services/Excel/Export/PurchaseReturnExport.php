<?php

namespace App\Services\Excel\Export;

use Shuchkin\SimpleXLSXGen; //thư viện simplexlsxgen dùng xuất file excel

class PurchaseReturnExport
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
        
        // Tiêu đề cột
        $result[] = [
            'Mã phiếu trả',
            'Nhà cung cấp', 
            'Ngày trả hàng',
            'Trạng thái',
            'Tổng tiền',
            'Tổng giảm giá',
            'Số tiền còn lại',
            'Đã thanh toán',
            'Số mặt hàng',
            'Lý do trả hàng',
            'Ngày tạo'
        ];
        
        // dữ liệu truyền vào 
        foreach ($data as $return) {
            $result[] = [
                $return['return_code'] ?? '',
                $return['supplier_name'] ?? '',
                $this->formatDate($return['return_date'] ?? ''),
                $this->getStatusText($return['status'] ?? ''),
                $this->formatCurrency($return['total_amount'] ?? 0),
                $this->formatCurrency($return['total_discount'] ?? 0),
                $this->formatCurrency($return['remaining_amount'] ?? 0),
                $this->formatCurrency($return['supplier_paid'] ?? 0),
                $return['items_count'] ?? 0,
                $return['reason'] ?? '',
                $this->formatDate($return['created_at'] ?? '')
            ];
        }
        
        return $result;
    }


    protected function getDefaultFilename(): string 
    {
        return 'phieu_tra_hang_' . date('Y_m_d_H_i_s');
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
            'returned' => 'Đã trả hàng',
            'pending' => 'Chờ xử lý',
            'cancelled' => 'Đã hủy',
            'partial' => 'Trả một phần'
        ];
        
        return $statusMap[$status] ?? $status;
    }
}