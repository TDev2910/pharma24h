<?php

namespace App\Services\Excel\Import;

use App\Models\Inventory\PurchaseReturn;
use App\Models\Inventory\PurchaseReturnItem;
use App\Models\Supplier;
use App\Models\Medicine;
use App\Models\Goods;

class PurchaseReturnImport extends BaseImport
{
    public function model(array $row)
    {
        if (!$this->validateData($row)) {
            return null;
        }

        $data = $this->transformData($row);

        $purchaseReturn = PurchaseReturn::create([
            'return_code' => $data['return_code'],
            'supplier_id' => $data['supplier_id'],
            'return_date' => $data['return_date'],
            'status' => 'pending',
            'total_amount' => 0,
            'total_discount' => 0,
            'note' => $data['note'] ?? ''
        ]);

        return $purchaseReturn;
    }

    public function rules(): array
    {
        return [
            'return_code' => 'required|string|max:255|unique:purchase_returns,return_code',
            'supplier_id' => 'required|exists:suppliers,id',
            'return_date' => 'required|date',
            'note' => 'nullable|string',
        ];
    }

    protected function validateData(array $row): bool
    {
        if (isset($row['supplier_id'])) {
            $supplier = Supplier::find($row['supplier_id']);
            if (!$supplier) {
                $this->errors[] = [
                    'row' => $row['row'] ?? 0,
                    'message' => 'Nhà cung cấp không tồn tại'
                ];
                return false;
            }
        }

        if (isset($row['return_code'])) {
            $existingReturn = PurchaseReturn::where('return_code', $row['return_code'])->first();
            if ($existingReturn) {
                $this->errors[] = [
                    'row' => $row['row'] ?? 0,
                    'message' => 'Mã phiếu trả đã tồn tại'
                ];
                return false;
            }
        }

        return true;
    }

    protected function transformData(array $row): array
    {
        return [
            'return_code' => $row['ma_phieu_tra'] ?? $row['return_code'] ?? '',
            'supplier_id' => $row['nha_cung_cap_id'] ?? $row['supplier_id'] ?? null,
            'return_date' => $row['ngay_tra'] ?? $row['return_date'] ?? now(),
            'note' => $row['ghi_chu'] ?? $row['note'] ?? '',
        ];
    }

    public function processItems($path, $supplierId, $returnDate, $returnCode)
    {
        /** @var ExcelService $excel */
        $excel = app(ExcelService::class);
        /** @var ImportService $importer */
        $importer = app(ImportService::class);

        try {
            $result = $importer->import($path, [
                'headerMap' => [],
                'baseRules' => [
                    'so_luong' => ['required', 'integer', 'min:1'],
                    'gia_tra_lai' => ['required', 'numeric', 'min:0'],
                ],
                'rowNormalizer' => function(array $row) {
                    $get = function(array $keys, $default = null) use ($row) {
                        foreach ($keys as $k) {
                            if (array_key_exists($k, $row) && $row[$k] !== null && $row[$k] !== '') {
                                return $row[$k];
                            }
                        }
                        return $default;
                    };

                    $cleanString = function($v) {
                        if (!is_string($v)) return $v;
                        $v = preg_replace('/\x{FEFF}|\x{200B}|\x{200C}|\x{200D}/u', '', $v);
                        $v = trim($v);
                        if (!mb_check_encoding($v, 'UTF-8')) {
                            $v = mb_convert_encoding($v, 'UTF-8', 'auto');
                        }
                        return $v;
                    };

                    $maHang  = $cleanString($get(['Mã hàng', 'Ma hang', 'ma_hang']));
                    $tenHang = $cleanString($get(['Tên hàng', 'Ten hang', 'ten_hang']));
                    $dvt     = $cleanString($get(['ĐVT', 'DVT', 'dvt'], 'Cái'));
                    $soLuong = (int) ($get(['Số lượng', 'So luong', 'so_luong'], 0));
                    $giaTraLai = (float) ($get(['Giá trả lại', 'Gia tra lai', 'gia_tra_lai'], 0));

                    return [
                        'ma_hang'  => $maHang,
                        'ten_hang' => $tenHang,
                        'dvt'      => $dvt,
                        'so_luong' => $soLuong,
                        'gia_tra_lai' => $giaTraLai,
                    ];
                },
                'rowFilter' => function(array $row) {
                    return !(empty($row['ma_hang']) && empty($row['ten_hang']));
                },
                'rowResolver' => function(array $row) {
                    $maHang = $row['ma_hang'];
                    $tenHang = $row['ten_hang'];

                    $product = null;
                    $productType = null;

                    $medicine = Medicine::where('ma_hang', $maHang)
                        ->orWhere('ten_thuoc', 'like', '%' . $tenHang . '%')
                        ->first();

                    if ($medicine) {
                        $product = $medicine;
                        $productType = 'medicine';
                    } else {
                        $goods = Goods::where('ma_hang', $maHang)
                            ->orWhere('ten_hang_hoa', 'like', '%' . $tenHang . '%')
                            ->first();
                        if ($goods) {
                            $product = $goods;
                            $productType = 'goods';
                        }
                    }

                    $row['__product'] = $product;
                    $row['__product_type'] = $productType;
                    return $row;
                },
                'accumulate' => function(array $row) {
                    $product = $row['__product'];
                    $productType = $row['__product_type'];

                    if ($product) {
                        return [
                            'product_type' => $productType,
                            'product_id' => $product->id,
                            'ma_hang' => $product->ma_hang,
                            'ten_hang' => $productType === 'medicine' ? $product->ten_thuoc : $product->ten_hang_hoa,
                            'don_vi_tinh' => $product->don_vi_tinh ?? $row['dvt'],
                            'so_luong' => $row['so_luong'],
                            'gia_tra_lai' => $row['gia_tra_lai'],
                            'thanh_tien' => $row['so_luong'] * $row['gia_tra_lai'],
                        ];
                    }

                    throw new \RuntimeException("Không tìm thấy sản phẩm: {$row['ma_hang']} - {$row['ten_hang']}");
                },
            ]);

            return [
                'success' => true,
                'items' => $result['items'],
                'errors' => $result['errors']
            ];

        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Lỗi khi đọc/nhập Excel: ' . $e->getMessage()
            ];
        }
    }
}
