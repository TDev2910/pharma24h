<?php

namespace App\Services\Excel\Import;

use App\Models\Order;
use App\Models\OrderItem;

class OrderImport extends BaseImport
{
    public function model(array $row)
    {
        // Validate dữ liệu
        if (!$this->validateData($row)) {
            return null;
        }

        // Transform dữ liệu
        $data = $this->transformData($row);

        // Tạo Order
        $order = Order::create([
            'order_code' => $data['order_code'],
            'customer_name' => $data['customer_name'],
            'customer_phone' => $data['customer_phone'],
            'customer_address' => $data['customer_address'],
            'total_amount' => $data['total_amount'],
            'status' => $data['status'] ?? 'pending',
            'notes' => $data['notes'] ?? '',
        ]);

        return $order;
    }

    public function rules(): array
    {
        return [
            'order_code' => 'required|string|max:255|unique:orders,order_code',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'required|string|max:500',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'nullable|string|in:pending,processing,completed,cancelled',
            'notes' => 'nullable|string',
        ];
    }

    protected function validateData(array $row): bool
    {
        // Kiểm tra mã đơn hàng có trùng không
        if (isset($row['order_code'])) {
            $existingOrder = Order::where('order_code', $row['order_code'])->first();
            if ($existingOrder) {
                $this->errors[] = [
                    'row' => $row['row'] ?? 0,
                    'message' => 'Mã đơn hàng đã tồn tại'
                ];
                return false;
            }
        }

        return true;
    }

    protected function transformData(array $row): array
    {
        return [
            'order_code' => $row['ma_don_hang'] ?? $row['order_code'] ?? '',
            'customer_name' => $row['ten_khach_hang'] ?? $row['customer_name'] ?? '',
            'customer_phone' => $row['so_dien_thoai'] ?? $row['customer_phone'] ?? '',
            'customer_address' => $row['dia_chi'] ?? $row['customer_address'] ?? '',
            'total_amount' => $row['tong_tien'] ?? $row['total_amount'] ?? 0,
            'status' => $row['trang_thai'] ?? $row['status'] ?? 'pending',
            'notes' => $row['ghi_chu'] ?? $row['notes'] ?? '',
        ];
    }
}
