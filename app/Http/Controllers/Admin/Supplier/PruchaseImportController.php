<?php

namespace App\Http\Controllers\Admin\Supplier;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\StockImport;
use App\Models\StockImportItem;
use App\Models\StockImportPayment;
use App\Models\Medicine;
use App\Models\Goods;
use App\Services\Excel\ExcelService;
use App\Services\Excel\ImportService;

class PruchaseImportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $imports = StockImport::with(['supplier', 'items'])->get();
        return view('admin.products.Nhaphang.Import.index', compact('imports'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::orderBy('ten_nha_cung_cap')
            ->get(['id','ten_nha_cung_cap','ma_nha_cung_cap']);
        
        $medicines = Medicine::all();
        $goods = Goods::all();

        return view('admin.products.Nhaphang.Import.create', compact('suppliers', 'medicines', 'goods'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'import_code' => 'required|unique:stock_imports,import_code',
            'supplier_id' => 'required|exists:suppliers,id',
            'import_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_type' => 'required|in:medicine,goods',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        // Tạo phiếu nhập hàng
        $stockImport = StockImport::create([
            'import_code' => $request->import_code,
            'supplier_id' => $request->supplier_id,
            'import_date' => $request->import_date,
            'status' => 'imported',
            'total_amount' => 0, //tổng tiền mặc định là 0 đồng
            'note' => $request->note
        ]);

        $totalAmount = 0;

        // Tạo chi tiết nhập hàng
        foreach ($request->items as $item) {
            $totalPrice = $item['quantity'] * $item['unit_price']; //tính tổng tiền cho từng sản phẩm
            $totalAmount += $totalPrice; //tính tổng tiền cho tất cả sản phẩm

            StockImportItem::create([
                'stock_import_id' => $stockImport->id,
                'product_type' => $item['product_type'],
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount' => $item['discount'] ?? 0,
                'total_price' => $totalPrice,
                'note' => $item['note'] ?? null
            ]);

            if($item['product_type'] === 'medicine')
            {
                $medicine = Medicine::find($item['product_id']);
                $medicine->ton_kho += (int)$item['quantity'];
                $medicine->save();
            }
            else
            {
                $goods = Goods::find($item['product_id']);
                $gooods->ton_kho += (int)$item['quantity'];
                $goods->save();
            }
        }

        // Cập nhật tổng tiền
        $stockImport->update([
            'total_amount' => $totalAmount,
            'remaining_amount' => $totalAmount
        ]);

        return redirect()->route('admin.import.index')
            ->with('success', 'Phiếu nhập hàng đã được tạo thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(StockImport $stockImport)
    {
        $stockImport->load(['supplier', 'items.product', 'payments']);
        return view('admin.products.Nhaphang.Import.show', compact('stockImport'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockImport $stockImport)
    {
        $suppliers = Supplier::orderBy('ten_nha_cung_cap')
            ->get(['id','ten_nha_cung_cap','ma_nha_cung_cap']);
        
        $medicines = Medicine::all();
        $goods = Goods::all();
        
        $stockImport->load(['items']);
        
        return view('admin.products.Nhaphang.Import.edit', compact('stockImport', 'suppliers', 'medicines', 'goods'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockImport $stockImport)
    {
        // Logic cập nhật phiếu nhập hàng
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockImport $stockImport)
    {
        $stockImport->delete();
        return redirect()->route('admin.import.index')
            ->with('success', 'Phiếu nhập hàng đã được xóa thành công!');
    }

    /**
     * Generate random import code
     */
    public function generateImportCode() //tạo mã nhập hàng ngẫu nhiên
    {
        do {
            $code = str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT);
        } while (StockImport::where('import_code', $code)->exists());
        
        return response()->json(['code' => $code]);
    }

    /**
     * Process payment
     */
    public function payment(Request $request, StockImport $stockImport)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,transfer',
            'note' => 'nullable|string'
        ]);

        // Tạo thanh toán
        StockImportPayment::create([
            'stock_import_id' => $stockImport->id,
            'payment_method' => $request->payment_method,
            'amount' => $request->amount,
            'note' => $request->note
        ]);

        // Cập nhật số tiền đã trả
        $totalPaid = $stockImport->payments()->sum('amount');
        $stockImport->update([
            'paid_amount' => $totalPaid,
            'remaining_amount' => $stockImport->total_amount - $totalPaid
        ]);

        return response()->json(['success' => true, 'message' => 'Thanh toán thành công!']);
    }

    /**
     * Complete import (mark as completed)
     */
    public function complete(StockImport $stockImport)
    {
        // Cập nhật tồn kho cho các sản phẩm
        foreach ($stockImport->items as $item) 
        {
            if ($item->product_type === 'medicine') //nếu sản phẩm là thuốc
            {
                $medicine = Medicine::find($item->product_id);
                $medicine->ton_kho += $item->quantity;
                $medicine->save();
            } 
            else //nếu sản phẩm là hàng hóa
            {
                $goods = Goods::find($item->product_id);
                $goods->ton_kho += $item->quantity;
                $goods->save();
            }
        }

        // Cập nhật trạng thái
        $stockImport->update(['status' => 'completed']);

        return redirect()->route('admin.import.index')
            ->with('success', 'Phiếu nhập hàng đã được hoàn thành!');
    }

    public function processExcel(Request $request)
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
}
