<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\Goods;
use App\Services\Excel\ExcelService;
use App\Services\Excel\ImportService;

class ImportController extends Controller
{
    /**
     * Import file Excel cho nhập hàng
     */
    public function processStockImportExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx|max:5120'
        ]);

        $path = $request->file('excel_file')->getRealPath();

        /** @var ExcelService $excel */
        $excel = app(ExcelService::class);
        /** @var ImportService $importer */
        $importer = app(ImportService::class);

        try {
            $result = $importer->import($path, [
                'headerMap' => [],
                'baseRules' => [
                    'so_luong' => ['required', 'integer', 'min:1'],
                    'don_gia'  => ['required', 'numeric', 'min:0'],
                ],
                'rowNormalizer' => function(array $row) {
                    // Chuẩn hoá và thống nhất key nội bộ
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

                    $maHang  = $cleanString($get(['Ma hang', 'Mã hàng', 'ma_hang']));
                    $tenHang = $cleanString($get(['Ten hang', 'Tên hàng', 'ten_hang']));
                    $dvt     = $cleanString($get(['DVT', 'ĐVT', 'dvt'], 'Cái'));
                    $soLuong = (int) ($get(['So luong', 'Số lượng', 'so_luong'], 0));
                    $donGia  = (float) ($get(['Don gia', 'Đơn giá', 'don_gia'], 0));

                    return [
                        'ma_hang'  => $maHang,
                        'ten_hang' => $tenHang,
                        'dvt'      => $dvt,
                        'so_luong' => $soLuong,
                        'don_gia'  => $donGia,
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
                            'don_gia' => $row['don_gia'],
                            'thanh_tien' => $row['so_luong'] * $row['don_gia'],
                        ];
                    }

                    // Nếu không tìm thấy sản phẩm, ném ra để ghi ở errors
                    throw new \RuntimeException("Không tìm thấy sản phẩm: {$row['ma_hang']} - {$row['ten_hang']}");
                },
            ]);

            // Chuyển các exception của accumulate thành errors giống format cũ
            $items = [];
            $errors = [];
            foreach ($result['items'] as $maybeItem) {
                // Items đã qua accumulate, luôn hợp lệ
                $items[] = $maybeItem;
            }
            foreach ($result['errors'] as $err) {
                $errors[] = implode('; ', $err['messages']);
            }

            return response()->json([
                'success' => true,
                'items' => $items,
                'errors' => $errors,
                'message' => 'Import thành công ' . count($items) . ' sản phẩm'
            ], 200, [], JSON_INVALID_UTF8_SUBSTITUTE|JSON_UNESCAPED_UNICODE);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi đọc/nhập Excel: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Import file Excel cho đơn hàng
     */
    // public function processOrderExcel(Request $request)
    // {
    //     // TODO: Implement order import logic
    //     return response()->json([
    //         'success' => false,
    //         'message' => 'Chức năng import đơn hàng chưa được triển khai'
    //     ], 501);
    // }

    // /**
    //  * Import file Excel cho sản phẩm
    //  */
    // public function processProductExcel(Request $request)
    // {
    //     // TODO: Implement product import logic
    //     return response()->json([
    //         'success' => false,
    //         'message' => 'Chức năng import sản phẩm chưa được triển khai'
    //     ], 501);
    // }

    /**
     * Import file Excel cho trả hàng
     */
    public function processPurchaseReturnExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx|max:5120'
        ]);

        $path = $request->file('excel_file')->getRealPath();

        /** @var ExcelService $excel */
        $excel = app(ExcelService::class);
        /** @var ImportService $importer */
        $importer = app(ImportService::class);

        try {
            $result = $importer->import($path, [
                'headerMap' => [],
                'baseRules' => [
                    'so_luong' => ['required', 'integer', 'min:1'],
                    'don_gia' => ['required', 'numeric', 'min:0'],
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
                    $donGia  = (float) ($get(['Giá trả lại', 'Gia tra lai', 'gia_tra_lai', 'Don gia', 'Đơn giá', 'don_gia'], 0));

                    return [
                        'ma_hang'  => $maHang,
                        'ten_hang' => $tenHang,
                        'dvt'      => $dvt,
                        'so_luong' => $soLuong,
                        'don_gia'  => $donGia,
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
                            'don_gia' => $row['don_gia'],
                            'thanh_tien' => $row['so_luong'] * $row['don_gia'],
                        ];
                    }

                    throw new \RuntimeException("Không tìm thấy sản phẩm: {$row['ma_hang']} - {$row['ten_hang']}");
                },
            ]);

            $items = [];
            $errors = [];
            foreach ($result['items'] as $maybeItem) {
                $items[] = $maybeItem;
            }
            foreach ($result['errors'] as $err) {
                $errors[] = implode('; ', $err['messages']);
            }

            return response()->json([
                'success' => true,
                'items' => $items,
                'errors' => $errors,
                'message' => 'Import thành công ' . count($items) . ' sản phẩm'
            ], 200, [], JSON_INVALID_UTF8_SUBSTITUTE|JSON_UNESCAPED_UNICODE);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi đọc/nhập Excel: ' . $e->getMessage()
            ], 422);
        }
    }
}
