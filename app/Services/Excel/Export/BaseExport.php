<?php

namespace App\Services\Excel\Export;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class BaseExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    protected $data = [];
    protected $filename = '';

    /**
     * Xuất dữ liệu ra file Excel
     *
     * @param array $data Dữ liệu cần xuất
     * @param string $filename Tên file (không cần extension)
     * @return \Illuminate\Http\Response
     */
    public function export(array $data, string $filename = null)
    {
        $this->data = $this->prepareData($data);
        $this->filename = $filename ?: $this->getDefaultFilename();
        
        return Excel::download($this, $this->filename . '.xlsx');
    }

    /**
     * Trả về dữ liệu cho Excel
     */
    public function array(): array
    {
        return $this->data;
    }

    /**
     * Trả về headers - override trong class con
     */
    public function headings(): array
    {
        return $this->getHeaders();
    }

    /**
     * Áp dụng styles cho Excel
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style cho header
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'E3F2FD',
                    ],
                ],
            ],
        ];
    }

    /**
     * Thiết lập độ rộng cột - override trong class con
     */
    public function columnWidths(): array
    {
        return $this->getColumnWidths();
    }

    /**
     * Chuẩn bị dữ liệu cho export - override trong class con
     */
    abstract protected function prepareData(array $data): array;

    /**
     * Lấy headers - override trong class con
     */
    abstract protected function getHeaders(): array;

    /**
     * Lấy độ rộng cột - override trong class con
     */
    abstract protected function getColumnWidths(): array;

    /**
     * Lấy tên file mặc định - override trong class con
     */
    abstract protected function getDefaultFilename(): string;

    /**
     * Format số tiền
     */
    protected function formatCurrency($amount): string
    {
        return number_format($amount, 0, ',', '.') . ' VND';
    }

    /**
     * Format ngày tháng
     */
    protected function formatDate($date): string
    {
        if (!$date) return '';
        return \Carbon\Carbon::parse($date)->format('d/m/Y H:i');
    }

    /**
     * Format số
     */
    protected function formatNumber($number): string
    {
        return number_format($number, 0, ',', '.');
    }
}
