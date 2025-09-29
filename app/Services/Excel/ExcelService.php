<?php

namespace App\Services\Excel;

use Shuchkin\SimpleXLSX; //sử dụng thư viện excel

class ExcelService
{
    //read file excel
    public function readRows(string $filePath, array $options = []): array
    {
        $sheetIndex = $options['sheetIndex'] ?? 0;

        if ($xlsx = SimpleXLSX::parse($filePath)) {
            $sheets = $xlsx->sheetNames(); //lấy danh sách sheet có trong file excel
            if (!isset($sheets[$sheetIndex])) {
                throw new \RuntimeException('Sheet index does not exist');
            }
            return $xlsx->rows($sheetIndex);
        }

        // Parsing failed, include library error for easier debugging
        $error = SimpleXLSX::parseError();
        throw new \RuntimeException('Failed to parse Excel file: ' . $error);
    }

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

    public function combineWithHeader(array $header, array $rows): array
    {
        $dataRows = array_slice($rows, 1);
        $result = [];

        foreach ($dataRows as $row) {
            // Align row size to header length to avoid combine warnings
            $row = array_slice(array_pad($row, count($header), null), 0, count($header));
            $assoc = array_combine($header, $row);
            $result[] = $this->normalizeAssocRow($assoc);
        }

        return $result;
    }

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
}


