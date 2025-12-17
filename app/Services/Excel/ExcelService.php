<?php

namespace App\Services\Excel;

use Shuchkin\SimpleXLSX; // sử dụng thư viện excel

class ExcelService
{
    /**
     * Đọc file excel và trả về các dòng.
     */
    public function readRows(string $filePath, array $options = []): array
    {
        $sheetIndex = $options['sheetIndex'] ?? 0;

        if ($xlsx = SimpleXLSX::parse($filePath)) {
            $sheets = $xlsx->sheetNames(); // lấy danh sách sheet có trong file excel
            if (!isset($sheets[$sheetIndex])) {
                throw new \RuntimeException('Sheet index does not exist');
            }
            return $xlsx->rows($sheetIndex);
        }

        // Parsing failed, include library error for easier debugging
        $error = SimpleXLSX::parseError();
        throw new \RuntimeException('Failed to parse Excel file: ' . $error);
    }

    /**
     * Trích xuất header từ dữ liệu các dòng.
     */
    public function extractHeader(array $rows): array
    {
        $header = $rows[0] ?? [];

        return array_map(function ($value) {
            if (!is_string($value)) {
                return $value;
            }
            $value = preg_replace('/\x{FEFF}|\x{200B}|\x{200C}|\x{200D}/u', '', $value ?? '');
            $value = trim((string) $value);
            // Ensure UTF-8
            if (!mb_check_encoding($value, 'UTF-8')) {
                $value = mb_convert_encoding($value, 'UTF-8', 'auto');
            }
            return $value;
        }, $header);
    }

    /**
     * Ghép các row dữ liệu với header (trả về mảng kết hợp).
     */
    public function combineWithHeader(array $header, array $rows): array
    {
        $dataRows = array_slice($rows, 1);
        $result = [];

        foreach ($dataRows as $row) {
            // Căn chỉnh kích thước row với header để tránh lỗi combine
            $row = array_slice(array_pad($row, count($header), null), 0, count($header));
            $assoc = array_combine($header, $row);
            $result[] = $this->normalizeAssocRow($assoc);
        }

        return $result;
    }

    /**
     * Chuẩn hoá giá trị các phần tử trong row (xoá BOM, trim, utf-8, ...).
     */
    private function normalizeAssocRow(array $row): array
    {
        foreach ($row as $key => $value) {
            if (is_string($value)) {
                $value = preg_replace('/\x{FEFF}|\x{200B}|\x{200C}|\x{200D}/u', '', $value);
                $value = trim($value);
                if (!mb_check_encoding($value, 'UTF-8')) {
                    $value = mb_convert_encoding($value, 'UTF-8', 'auto');
                }
            }
            $row[$key] = $value;
        }
        return $row;
    }

    // ===== CÁC METHOD MỚI CHO CẤU TRÚC EXPORT/IMPORT =====

    /**
     * Export dữ liệu ra file Excel.
     */
    public function export(string $type, array $data, string $filename = null)
    {
        $exporter = $this->getExporter($type);
        return $exporter->export($data, $filename);
    }

    /**
     * Import dữ liệu từ file Excel.
     */
    public function import(string $type, \Illuminate\Http\UploadedFile $file): array
    {
        $importer = $this->getImporter($type);
        return $importer->import($file);
    }

    /**
     * Lấy exporter theo type.
     */
    private function getExporter(string $type)
    {
        switch ($type) {
            case 'order':
                return new \App\Services\Excel\Export\OrderExport();
            case 'import':
                return new \App\Services\Excel\Export\StockImportExport();
            case 'product':
                return new \App\Services\Excel\Export\ProductExport();
            default:
                throw new \InvalidArgumentException("Export type '{$type}' không được hỗ trợ");
        }
    }

    /**
     * Lấy importer theo type.
     */
    private function getImporter(string $type)
    {
        switch ($type) {
            case 'order':
                return new \App\Services\Excel\Import\OrderImport(); // nhập đơn hàng
            case 'import':
                return new \App\Services\Excel\Import\StockImportImport(); // nhập hàng
            case 'product':
                return new \App\Services\Excel\Import\ProductImport(); // nhập sản phẩm
            case 'purchase-return':
                return new \App\Services\Excel\Import\PurchaseReturnImport(); // nhập trả hàng
            default:
                throw new \InvalidArgumentException("Import type '{$type}' không được hỗ trợ");
        }
    }

    /**
     * Lấy danh sách các loại export/import được hỗ trợ.
     */
    public function getSupportedTypes(): array
    {
        return [
            'order'           => 'Đơn hàng',
            'import'          => 'Nhập hàng',
            'product'         => 'Sản phẩm',
            'purchase-return' => 'Trả hàng',
        ];
    }

    /**
     * Validate file Excel.
     */
    public function validateFile(\Illuminate\Http\UploadedFile $file): array
    {
        $errors = [];

        // Kiểm tra extension
        $allowedExtensions = ['xlsx', 'xls', 'csv'];
        if (!in_array($file->getClientOriginalExtension(), $allowedExtensions)) {
            $errors[] = 'File phải có định dạng Excel (.xlsx, .xls) hoặc CSV';
        }

        // Kiểm tra size (max 10MB)
        if ($file->getSize() > 10 * 1024 * 1024) {
            $errors[] = 'File không được vượt quá 10MB';
        }

        return $errors;
    }
}