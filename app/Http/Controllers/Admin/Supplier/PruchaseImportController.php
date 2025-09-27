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
use Shuchkin\SimpleXLSX;

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
            'status' => 'pending', //trạng thái chờ xử lý
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
        }

        // Cập nhật tổng tiền
        $stockImport->update([
            'total_amount' => $totalAmount,
            'remaining_amount' => $totalAmount
        ]);

        return redirect()->route('admin.purchase-imports.index')
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
        return redirect()->route('admin.purchase-imports.index')
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

        return redirect()->route('admin.purchase-imports.index')
            ->with('success', 'Phiếu nhập hàng đã được hoàn thành!');
    }

    public function processExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx|max:5120'
        ]);

        $path = $request->file('excel_file')->getRealPath();

        if ($xlsx = SimpleXLSX::parse($path)) {
            $rows = $xlsx->rows(); 

            // Nếu hàng đầu là header, tách header + map
            $header = array_map('trim', $rows[0] ?? []);
            $data = array_slice($rows, 1);

            $clean = array_map(function($r) use ($header) {
                $row = array_combine($header, $r);

                $f = function($v){
                    if (is_string($v)) {
                        $v = preg_replace('/\x{FEFF}|\x{200B}|\x{200C}|\x{200D}/u', '', $v);
                        $v = trim($v);
                        if (!mb_check_encoding($v, 'UTF-8')) {
                            $v = mb_convert_encoding($v, 'UTF-8', 'auto');
                        }
                    }
                    return $v;
                };

                return [
                    'ma_hang'  => $f($row['Ma hang'] ?? $row['Mã hàng'] ?? ''),
                    'ten_hang' => $f($row['Ten hang'] ?? $row['Tên hàng'] ?? ''),
                    'dvt'      => $f($row['DVT'] ?? 'Cái'),
                    'so_luong' => (int)($row['So luong'] ?? $row['Số lượng'] ?? 0),
                    'don_gia'  => (float)($row['Don gia'] ?? $row['Đơn giá'] ?? 0),
                ];
            }, $data);
        
            $importedItems = [];
            $errors = [];

            // Xử lý từng dòng dữ liệu đã được clean
            foreach ($clean as $item) {
                $maHang = $item['ma_hang'];
                $tenHang = $item['ten_hang'];
                $soLuong = $item['so_luong'];
                $donGia = $item['don_gia'];
                $donViTinh = $item['dvt'];

                if (empty($maHang) && empty($tenHang)) {
                    continue; // Bỏ qua dòng trống
                }

                // Tìm sản phẩm trong database
                $product = null;
                $productType = null;

                // Tìm trong medicines
                $medicine = Medicine::where('ma_hang', $maHang)
                    ->orWhere('ten_thuoc', 'like', '%' . $tenHang . '%')
                    ->first();

                if ($medicine) {
                    $product = $medicine;
                    $productType = 'medicine';
                } else {
                    // Tìm trong goods
                    $goods = Goods::where('ma_hang', $maHang)
                        ->orWhere('ten_hang_hoa', 'like', '%' . $tenHang . '%')
                        ->first();
                    
                    if ($goods) {
                        $product = $goods;
                        $productType = 'goods';
                    }
                }

                if ($product) {
                    $importedItems[] = [
                        'product_type' => $productType,
                        'product_id' => $product->id,
                        'ma_hang' => $product->ma_hang,
                        'ten_hang' => $productType === 'medicine' ? $product->ten_thuoc : $product->ten_hang_hoa,
                        'don_vi_tinh' => $product->don_vi_tinh ?? $donViTinh,
                        'so_luong' => $soLuong,
                        'don_gia' => $donGia,
                        'thanh_tien' => $soLuong * $donGia
                    ];
                } else {
                    $errors[] = "Không tìm thấy sản phẩm: {$maHang} - {$tenHang}";
                }
            }

            return response()->json([
                'success' => true,
                'items' => $importedItems,
                'errors' => $errors,
                'message' => 'Import thành công ' . count($importedItems) . ' sản phẩm'
            ], 200, [], JSON_INVALID_UTF8_SUBSTITUTE|JSON_UNESCAPED_UNICODE);
        }

        return response()->json([
            'success' => false,
            'message' => 'Lỗi khi đọc file Excel: ' . SimpleXLSX::parseError()
        ], 422);
}
}
