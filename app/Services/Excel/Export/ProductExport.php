<?php

namespace App\Services\Excel\Export;

class ProductExport extends BaseExport
{
    protected function prepareData(array $data): array
    {
        $result = [];
        
        foreach ($data as $product) {
            $result[] = [
                'Mã sản phẩm' => $product->ma_hang ?? $product->ma_thuoc ?? '',
                'Tên sản phẩm' => $product->ten_hang_hoa ?? $product->ten_thuoc ?? '',
                'Loại' => $this->getProductType($product),
                'Nhóm hàng' => $product->category->ten_nhom ?? '',
                'Nhà sản xuất' => $product->manufacturer->ten_nha_san_xuat ?? '',
                'Giá vốn' => $this->formatCurrency($product->gia_von ?? 0),
                'Giá bán' => $this->formatCurrency($product->gia_ban ?? 0),
                'Tồn kho' => $this->formatNumber($product->ton_kho ?? 0),
                'Đơn vị' => $product->don_vi_tinh ?? '',
                'Trạng thái' => $this->getStatusText($product->ban_truc_tiep ?? false),
            ];
        }
        
        return $result;
    }

    protected function getHeaders(): array
    {
        return [
            'Mã sản phẩm',
            'Tên sản phẩm',
            'Loại',
            'Nhóm hàng',
            'Nhà sản xuất',
            'Giá vốn',
            'Giá bán',
            'Tồn kho',
            'Đơn vị',
            'Trạng thái'
        ];
    }

    protected function getColumnWidths(): array
    {
        return [
            'A' => 15, // Mã sản phẩm
            'B' => 30, // Tên sản phẩm
            'C' => 12, // Loại
            'D' => 20, // Nhóm hàng
            'E' => 20, // Nhà sản xuất
            'F' => 15, // Giá vốn
            'G' => 15, // Giá bán
            'H' => 12, // Tồn kho
            'I' => 10, // Đơn vị
            'J' => 12, // Trạng thái
        ];
    }

    protected function getDefaultFilename(): string
    {
        return 'danh-sach-san-pham-' . date('Y-m-d-H-i-s');
    }

    private function getProductType($product): string
    {
        if (isset($product->ten_thuoc)) {
            return 'Thuốc';
        } elseif (isset($product->ten_hang_hoa)) {
            return 'Hàng hóa';
        } else {
            return 'Dịch vụ';
        }
    }

    private function getStatusText($isActive): string
    {
        return $isActive ? 'Đang bán' : 'Ngừng bán';
    }
}
