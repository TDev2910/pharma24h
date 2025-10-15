<?php

namespace App\Http\Controllers\Admin\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Inventory\PurchaseReturn;
use App\Models\Inventory\PurchaseReturnItem;
use App\Models\Inventory\PurchaseReturnPayment;
use App\Models\Medicine;
use App\Models\Goods;

use Inertia\Inertia;

class PurchaseReturnsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $returns = PurchaseReturn::with(['supplier', 'items'])->get();

        $formattedReturns = $returns->map(function($return) {
            return [
                'id' => $return->id,
                'return_code' => $return->return_code,
                'supplier_name' => $return->supplier->ten_nha_cung_cap ?? 'N/A',
                'return_date' => $return->return_date,
                'status' => $return->status,
                'total_amount' => $return->total_amount,
                'total_discount' => $return->total_discount,
                'remaining_amount' => $return->remaining_amount,
                'note' => $return->note,
                'items_count' => $return->items->count(),
                'created_at' => $return->created_at,
                // Map đúng các field cho frontend
                'discount' => $return->total_discount,
                'supplier_pay' => $return->remaining_amount,
                'supplier_paid' => $return->paid_amount ?? 0,
                'reason' => $return->note ?? 'Không có lý do'
            ];
        });
            
        return Inertia::render('Admin/Purchases/Purchase-Returns/Dashboard', [
            'returns' => $formattedReturns
        ]);    
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
        return Inertia::render('Admin/Purchases/Purchase-Returns/Create', [
            'suppliers' => $suppliers,
            'medicines' => $medicines,
            'goods' => $goods
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'return_code' => 'required|unique:purchase_returns,return_code',
                'supplier_id' => 'required|exists:suppliers,id',
                'return_date' => 'required|date',
                'note' => 'nullable|string',
                'discount' => 'nullable|numeric|min:0',
                'items' => 'required|array|min:1',
                'items.*.product_type' => 'required|in:medicine,goods',
                'items.*.product_id' => 'required|integer',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.discount' => 'nullable|numeric|min:0',
            ]);

        // Tạo phiếu trả hàng
        $purchaseReturn = PurchaseReturn::create([
            'return_code' => $request->return_code,
            'supplier_id' => $request->supplier_id,
            'return_date' => $request->return_date,
            'status' => 'returned', // Mặc định là đã trả hàng
            'total_amount' => 0,
            'total_discount' => 0,
            'note' => $request->note
        ]);

        $totalAmount = 0;
        $totalDiscount = 0;
        
        // Lấy giảm giá tổng từ form (nếu có)
        $formDiscount = $request->discount ?? 0;

        // Tạo chi tiết trả hàng
        foreach ($request->items as $item) {
            $quantity = $item['quantity'];
            $unitPrice = $item['unit_price'];
            $discount = $item['discount'] ?? 0;
            
            // Tính thành tiền: (số lượng × đơn giá) - giảm giá
            $totalPrice = ($quantity * $unitPrice) - $discount;
            
            $totalAmount += $totalPrice;
            $totalDiscount += $discount;

            PurchaseReturnItem::create([
                'purchase_return_id' => $purchaseReturn->id,
                'product_type' => $item['product_type'],
                'product_id' => $item['product_id'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'discount' => $discount,
                'total_price' => $totalPrice,
                'note' => $item['note'] ?? null
            ]);

            // GIẢM tồn kho (khác với nhập hàng là TĂNG)
            if($item['product_type'] === 'medicine') {
                $medicine = Medicine::find($item['product_id']);
                $medicine->ton_kho -= (int)$quantity; // TRỪ tồn kho
                $medicine->save();
            } else {
                $goods = Goods::find($item['product_id']);
                $goods->ton_kho -= (int)$quantity; // TRỪ tồn kho
                $goods->save();
            }
        }

        // Áp dụng giảm giá tổng vào tổng tiền
        $finalAmount = $totalAmount - $formDiscount;
        
        // Cập nhật tổng tiền và tổng giảm giá
        $purchaseReturn->update([
            'total_amount' => $finalAmount,
            'total_discount' => $totalDiscount + $formDiscount,
            'paid_amount' => 0, // Ban đầu chưa trả tiền
            'remaining_amount' => $finalAmount // Số tiền còn lại = tổng tiền (vì chưa trả)
        ]);

            return redirect()->route('admin.purchase-returns.index')
                ->with('success', 'Phiếu trả hàng đã được tạo thành công!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi lưu phiếu trả hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $purchaseReturn = PurchaseReturn::with(['supplier', 'items.product'])->findOrFail($id);
        
        return Inertia::render('Admin/Purchases/Purchase-Returns/Show', [
            'purchaseReturn' => $purchaseReturn
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $purchaseReturn = PurchaseReturn::with(['supplier', 'items.product'])->findOrFail($id);
        $suppliers = Supplier::orderBy('ten_nha_cung_cap')->get(['id','ten_nha_cung_cap','ma_nha_cung_cap']);
        
        return Inertia::render('Admin/Purchases/Purchase-Returns/Edit', [
            'purchaseReturn' => $purchaseReturn,
            'suppliers' => $suppliers
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $purchaseReturn = PurchaseReturn::findOrFail($id);
            
            // Xóa các items liên quan
            $purchaseReturn->items()->delete();
            
            // Xóa payments liên quan
            $purchaseReturn->payments()->delete();
            
            // Xóa purchase return
            $purchaseReturn->delete();
            
            return redirect()->route('admin.purchase-returns.index')
                ->with('success', 'Phiếu trả hàng đã được xóa thành công!');
                
        } catch (\Exception $e) {
            return redirect()->route('admin.purchase-returns.index')
                ->with('error', 'Có lỗi xảy ra khi xóa phiếu trả hàng: ' . $e->getMessage());
        }
    }

    /**
     * Generate random return code
     */
    public function generateReturnCode()
    {
        do {
            $code = str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT);
        } while (PurchaseReturn::where('return_code', $code)->exists());
        
        return response()->json(['code' => $code]);
    }

    /**
     * Process Excel file for purchase returns (delegated to ImportController)
     */
    public function processExcel(Request $request)
    {
        $importController = app(\App\Http\Controllers\Admin\ImportController::class);
        return $importController->processPurchaseReturnExcel($request);
    }

    /**
     * Create sample data for testing
     */
    public function createSampleData()
    {
        // Tạo một phiếu trả hàng mẫu
        $purchaseReturn = PurchaseReturn::create([
            'return_code' => 'TR' . date('Ymd') . rand(1000, 9999),
            'supplier_id' => 1, // Giả sử có supplier với ID = 1
            'return_date' => now(),
            'status' => 'returned', // Mặc định là đã trả hàng
            'total_amount' => 5000000,
            'total_discount' => 500000,
            'paid_amount' => 0,
            'remaining_amount' => 4500000,
            'note' => 'Trả hàng mẫu để test'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã tạo dữ liệu mẫu thành công!',
            'data' => $purchaseReturn
        ]);
    }
}
