<?php

namespace App\Services\Excel\Export;

use Shuchkin\SimpleXLSXGen;

class PurchaseReturnExport
{
    protected $data = [];
    protected $filename = '';

    /**
     * Xuất dữ liệu phiếu trả hàng ra file Excel
     *
     * @param array $data Dữ liệu phiếu trả hàng
     * @param string $filename Tên file (không cần extension)
     * @return string Đường dẫn file Excel đã tạo
     */
    public function export(array $data, string $filename = null): string
    {
        $this->data = $this->prepareData($data);
        $this->filename = $filename ?: $this->getDefaultFilename();
        
        // Tạo file Excel trong thư mục public/exports
        $filePath = public_path('exports/' . $this->filename . '.xlsx');
        
        // Đảm bảo thư mục tồn tại
        $exportDir = dirname($filePath);
        if (!is_dir($exportDir)) {
            mkdir($exportDir, 0755, true);
        }
        
        // Tạo Excel với SimpleXLSXGen
        SimpleXLSXGen::fromArray($this->data)->saveAs($filePath);
        
        return $filePath;
    }

    /**
     * Chuẩn bị dữ liệu cho export
     */
    protected function prepareData(array $data): array
    {
        $result = [];
        
        // Thêm header
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
        
        // Thêm dữ liệu
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

    /**
     * Lấy tên file mặc định
     */
    protected function getDefaultFilename(): string
    {
        return 'phieu_tra_hang_' . date('Y_m_d_H_i_s');
    }

    /**
     * Format số tiền
     */
    protected function formatCurrency($amount): string
    {
        if (is_null($amount) || $amount === '') {
            return '0 VND';
        }
        return number_format((float)$amount, 0, ',', '.') . ' VND';
    }

    /**
     * Format ngày tháng
     */
    protected function formatDate($date): string
    {
        if (!$date) return '';
        
        try {
            return \Carbon\Carbon::parse($date)->format('d/m/Y H:i');
        } catch (\Exception $e) {
            return $date;
        }
    }

    /**
     * Lấy text trạng thái
     */
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

    /**
     * Xuất file Excel và trả về response download
     *
     * @param array $data
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(array $data, string $filename = null)
    {
        $filePath = $this->export($data, $filename);
        
        // Kiểm tra file có tồn tại không
        if (!file_exists($filePath)) {
            throw new \Exception('File Excel không được tạo thành công');
        }
        
        // Trả về file từ public folder
        return response()->download($filePath, $this->filename . '.xlsx')
            ->deleteFileAfterSend(true);
    }
}