<?php

namespace App\Services\Excel;

use Illuminate\Support\Facades\Validator;

class ImportService
{
    private ExcelService $excel;

    public function __construct(ExcelService $excel)
    {
        $this->excel = $excel;
    }
    //import file excel
    public function import(string $filePath, array $config): array
    {
        $rows = $this->excel->readRows($filePath);
        $header = $this->excel->extractHeader($rows);
        $assocRows = $this->excel->combineWithHeader($header, $rows);

        $headerMap = $config['headerMap'] ?? [];
        $baseRules = $config['baseRules'] ?? [];
        $rowNormalizer = $config['rowNormalizer'] ?? null;
        $rowFilter = $config['rowFilter'] ?? null;
        $rowResolver = $config['rowResolver'] ?? null;
        $accumulate = $config['accumulate'] ?? null;

        $items = [];
        $errors = [];

        foreach ($assocRows as $index => $assoc) 
        {
            $mapped = $this->applyHeaderMap($assoc, $headerMap);
            
            if (is_callable($rowNormalizer)) {
                $mapped = $rowNormalizer($mapped);
            }

            if (is_callable($rowFilter) && !$rowFilter($mapped)) {
                continue;
            }

            // Validate
            if (!empty($baseRules)) {
                $validator = Validator::make($mapped, $baseRules);
                if ($validator->fails()) {
                    $errors[] = [
                        'row' => $index + 2, // +2 because header is row 1
                        'messages' => $validator->errors()->all(),
                    ];
                    continue;
                }
            }

            $resolved = is_callable($rowResolver) ? $rowResolver($mapped) : $mapped;

            $items[] = is_callable($accumulate) ? $accumulate($resolved) : $resolved;
        }

        return [
            'items' => $items,
            'errors' => $errors,
            'meta' => [
                'total' => count($assocRows),
                'success' => count($items),
                'failed' => count($errors),
            ],
        ];
    }

    private function applyHeaderMap(array $assoc, array $map): array
    {
        if (empty($map)) {
            return $assoc;
        }

        $result = [];
        foreach ($map as $excelHeader => $internalField) {
            $result[$internalField] = $assoc[$excelHeader] ?? null;
        }
        return $result;
    }
}


