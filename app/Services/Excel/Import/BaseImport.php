<?php

namespace App\Services\Excel\Import;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Collection;
use Illuminate\Http\UploadedFile;

abstract class BaseImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    protected $errors = [];
    protected $successCount = 0;
    protected $errorCount = 0;

    /**
     * Import dữ liệu từ file Excel
     *
     * @param UploadedFile $file File Excel
     * @return array Kết quả import
     */
    public function import(UploadedFile $file): array
    {
        try {
            Excel::import($this, $file);
            
            return [
                'success' => true,
                'message' => 'Import thành công!',
                'success_count' => $this->successCount,
                'error_count' => $this->errorCount,
                'errors' => $this->errors
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Import thất bại: ' . $e->getMessage(),
                'success_count' => $this->successCount,
                'error_count' => $this->errorCount,
                'errors' => $this->errors
            ];
        }
    }

    /**
     * Tạo model từ dữ liệu Excel - override trong class con
     */
    abstract public function model(array $row);

    /**
     * Validation rules - override trong class con
     */
    abstract public function rules(): array;

    /**
     * Custom validation messages - override trong class con
     */
    public function customValidationMessages(): array
    {
        return [];
    }

    /**
     * Batch size cho insert
     */
    public function batchSize(): int
    {
        return 1000;
    }

    /**
     * Chunk size cho reading
     */
    public function chunkSize(): int
    {
        return 1000;
    }

    /**
     * Xử lý khi có lỗi validation
     */
    public function onFailure($failures)
    {
        foreach ($failures as $failure) {
            $this->errors[] = [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors()
            ];
            $this->errorCount++;
        }
    }

    /**
     * Xử lý khi import thành công
     */
    public function onSuccess($model)
    {
        $this->successCount++;
    }

    /**
     * Validate dữ liệu trước khi import
     */
    protected function validateData(array $row): bool
    {
        // Override trong class con nếu cần validation phức tạp
        return true;
    }

    /**
     * Transform dữ liệu trước khi lưu
     */
    protected function transformData(array $row): array
    {
        // Override trong class con nếu cần transform dữ liệu
        return $row;
    }
}
