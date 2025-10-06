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
            'note' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0', // Thêm validation cho giảm giá tổng
            'items' => 'required|array|min:1',
            'items.*.product_type' => 'required|in:medicine,goods',
            'items.*.product_id' => 'required|integer',
            // Chấp nhận cả format cũ và mới
            'items.*.quantity' => 'nullable|integer|min:1',
            'items.*.so_luong' => 'nullable|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.don_gia' => 'nullable|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        // Validate thêm: mỗi item phải có ít nhất quantity hoặc so_luong
        foreach ($request->items as $index => $item) {
            if (empty($item['quantity']) && empty($item['so_luong'])) {
                return back()->withErrors([
                    "items.{$index}.quantity" => 'Số lượng là bắt buộc'
                ])->withInput();
            }
            if (empty($item['unit_price']) && empty($item['don_gia'])) {
                return back()->withErrors([
                    "items.{$index}.unit_price" => 'Đơn giá là bắt buộc'
                ])->withInput();
            }
        }

        // Tạo phiếu nhập hàng
        $stockImport = StockImport::create([
            'import_code' => $request->import_code,
            'supplier_id' => $request->supplier_id,
            'import_date' => $request->import_date,
            'status' => 'imported',
            'total_amount' => 0, //tổng tiền mặc định là 0 đồng
            'total_discount' => 0, //tổng giảm giá mặc định là 0 đồng
            'note' => $request->note
        ]);

        $totalAmount = 0;
        $totalDiscount = 0;
        
        // Lấy giảm giá tổng từ form (nếu có)
        $formDiscount = $request->discount ?? 0;

        // Tạo chi tiết nhập hàng
        foreach ($request->items as $item) {
            // Xử lý cả format từ Excel import và format thông thường
            $quantity = $item['quantity'] ?? $item['so_luong'] ?? 0;
            $unitPrice = $item['unit_price'] ?? $item['don_gia'] ?? 0;
            $discount = $item['discount'] ?? 0;
            
            // Tính tổng tiền: (số lượng × đơn giá) - giảm giá
            if (isset($item['thanh_tien'])) {
                // Nếu có sẵn thành tiền từ Excel import
                $totalPrice = $item['thanh_tien'];
            } else {
                // Tính thành tiền: (số lượng × đơn giá) - giảm giá
                $totalPrice = ($quantity * $unitPrice) - $discount;
            }
            
            $totalAmount += $totalPrice; //tính tổng tiền cho tất cả sản phẩm
            $totalDiscount += $discount; //tính tổng giảm giá

            StockImportItem::create([
                'stock_import_id' => $stockImport->id,
                'product_type' => $item['product_type'],
                'product_id' => $item['product_id'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'discount' => $discount,
                'total_price' => $totalPrice,
                'note' => $item['note'] ?? null
            ]);

            if($item['product_type'] === 'medicine')
            {
                $medicine = Medicine::find($item['product_id']);
                $medicine->ton_kho += (int)$quantity;
                $medicine->save();
            }
            else
            {
                $goods = Goods::find($item['product_id']);
                $goods->ton_kho += (int)$quantity;
                $goods->save();
            }
        }

        // Áp dụng giảm giá tổng vào tổng tiền
        $finalAmount = $totalAmount - $formDiscount;
        
        // Cập nhật tổng tiền và tổng giảm giá
        $stockImport->update([
            'total_amount' => $finalAmount,
            'total_discount' => $totalDiscount + $formDiscount, // Cộng cả giảm giá từng sản phẩm và giảm giá tổng
            'remaining_amount' => $finalAmount
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

    /**
     * Process Excel file for import (delegated to ImportController)
     */
    public function processExcel(Request $request)
    {
        $importController = app(\App\Http\Controllers\Admin\ImportController::class);
        return $importController->processStockImportExcel($request);
    }
}
