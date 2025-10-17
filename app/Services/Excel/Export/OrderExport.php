<?php

namespace App\Services\Excel\Export;

class OrderExport extends BaseExport
{
    protected function prepareData(array $data): array
    {
        $result = [];
        
        foreach ($data as $order) {
            $result[] = [
                'Mã đơn hàng' => $order->order_code ?? '',
                'Khách hàng' => $order->customer_name ?? '',
                'Số điện thoại' => $order->customer_phone ?? '',
                'Địa chỉ' => $order->customer_address ?? '',
                'Tổng tiền' => $this->formatCurrency($order->total_amount ?? 0),
                'Trạng thái' => $this->getStatusText($order->status ?? ''),
                'Ngày tạo' => $this->formatDate($order->created_at ?? ''),
                'Ghi chú' => $order->notes ?? '',
            ];
        }
        
        return $result;
    }

    protected function getHeaders(): array
    {
        return [
            'Mã đơn hàng',
            'Khách hàng', 
            'Số điện thoại',
            'Địa chỉ',
            'Tổng tiền',
            'Trạng thái',
            'Ngày tạo',
            'Ghi chú'
        ];
    }

    protected function getColumnWidths(): array
    {
        return [
            'A' => 15, // Mã đơn hàng
            'B' => 25, // Khách hàng
            'C' => 15, // Số điện thoại
            'D' => 30, // Địa chỉ
            'E' => 15, // Tổng tiền
            'F' => 15, // Trạng thái
            'G' => 20, // Ngày tạo
            'H' => 30, // Ghi chú
        ];
    }

    protected function getDefaultFilename(): string
    {
        return 'danh-sach-don-hang-' . date('Y-m-d-H-i-s');
    }

    private function getStatusText($status): string
    {
        $statusMap = [
            'pending' => 'Chờ xử lý',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
        ];
        
        return $statusMap[$status] ?? $status;
    }
}
