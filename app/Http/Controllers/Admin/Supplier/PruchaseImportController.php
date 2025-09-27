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
}
