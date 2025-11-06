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
use App\Services\Excel\Export\StockImportExport;

class PruchaseImportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $imports = StockImport::with(['supplier', 'items'])->get();

        // Transform data to match Vue component expectations
        $orders = $imports->map(function ($import) {
            return [
                'id' => $import->id,
                'order_code' => $import->import_code,
                'created_at' => $import->created_at,
                'supplier_name' => $import->supplier->ten_nha_cung_cap ?? 'N/A',
                'ma_nha_cung_cap' => $import->supplier->ma_nha_cung_cap ?? 'N/A',
                'total_amount' => $import->total_amount ?? 0,
                'discount' => $import->discount ?? 0,
                'supplier_pay' => $import->supplier_pay ?? 0,
                'supplier_paid' => $import->supplier_paid ?? 0,
                'status' => $import->status ?? 'pending',
                'note' => $import->note ?? ''
            ];
        });

        return inertia('Admin/Purchases/Purchase-Orders/Dashboard', [
            'orders' => $orders
        ]);
    }

    /**
     * API endpoint để lọc đơn nhập hàng theo ngày
     */
    public function apiIndex(Request $request)
    {
        $query = StockImport::with(['supplier', 'items']);

        // Sử dụng scope FilterByDate từ Model
        if ($request->filled('from_date') || $request->filled('to_date')) {
            $query->filterByDate($request->from_date, $request->to_date);
        }

        // Filter theo search nếu có
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('import_code', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($subQ) use ($search) {
                        $subQ->where('ten_nha_cung_cap', 'like', "%{$search}%");
                    });
            });
        }

        // Lọc trạng thái 
        if ($request->filled('status')) {
            $statuses = explode(',', $request->status);
            $query->whereIn('status', $statuses);
        }

        // Lấy dữ liệu với pagination
        $imports = $query->latest()->paginate($request->get('per_page', 10));

        // Transform data
        $orders = $imports->getCollection()->map(function ($import) {
            return [
                'id' => $import->id,
                'order_code' => $import->import_code,
                'created_at' => $import->created_at,
                'supplier_name' => $import->supplier->ten_nha_cung_cap ?? 'N/A',
                'ma_nha_cung_cap' => $import->supplier->ma_nha_cung_cap ?? 'N/A',
                'total_amount' => $import->total_amount ?? 0,
                'discount' => $import->discount ?? 0,
                'supplier_pay' => $import->supplier_pay ?? 0,
                'supplier_paid' => $import->supplier_paid ?? 0,
                'remaining_amount' => $import->remaining_amount ?? 0,
                'status' => $import->status ?? 'pending',
                'note' => $import->note ?? ''
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $orders,
            'pagination' => [
                'current_page' => $imports->currentPage(),
                'last_page' => $imports->lastPage(),
                'per_page' => $imports->perPage(),
                'total' => $imports->total(),
                'from' => $imports->firstItem(),
                'to' => $imports->lastItem()
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::orderBy('ten_nha_cung_cap')
            ->get(['id', 'ten_nha_cung_cap', 'ma_nha_cung_cap']);

        $medicines = Medicine::all();
        $goods = Goods::all();

        return inertia('Admin/Purchases/Purchase-Orders/Create', [
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

            if ($item['product_type'] === 'medicine') {
                $medicine = Medicine::find($item['product_id']);
                $medicine->ton_kho += (int)$quantity;
                $medicine->save();
            } else {
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

        // Transform data for Vue component
        $order = [
            'id' => $stockImport->id,
            'order_code' => $stockImport->import_code,
            'created_at' => $stockImport->created_at,
            'supplier_name' => $stockImport->supplier->ten_nha_cung_cap ?? 'N/A',
            'total_amount' => $stockImport->total_amount ?? 0,
            'discount' => $stockImport->discount ?? 0,
            'supplier_pay' => $stockImport->supplier_pay ?? 0,
            'supplier_paid' => $stockImport->supplier_paid ?? 0,
            'status' => $stockImport->status ?? 'pending',
            'note' => $stockImport->note ?? '',
            'items' => $stockImport->items
        ];

        return inertia('Admin/Purchases/Purchase-Orders/Show', [
            'order' => $order
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockImport $stockImport)
    {
        $suppliers = Supplier::orderBy('ten_nha_cung_cap')
            ->get(['id', 'ten_nha_cung_cap', 'ma_nha_cung_cap']);

        $medicines = Medicine::all();
        $goods = Goods::all();

        $stockImport->load(['items']);

        // Transform data for Vue component
        $order = [
            'id' => $stockImport->id,
            'order_code' => $stockImport->import_code,
            'created_at' => $stockImport->created_at,
            'supplier_name' => $stockImport->supplier->ten_nha_cung_cap ?? 'N/A',
            'total_amount' => $stockImport->total_amount ?? 0,
            'discount' => $stockImport->discount ?? 0,
            'supplier_pay' => $stockImport->supplier_pay ?? 0,
            'supplier_paid' => $stockImport->supplier_paid ?? 0,
            'status' => $stockImport->status ?? 'pending',
            'note' => $stockImport->note ?? '',
            'items' => $stockImport->items
        ];

        return inertia('Admin/Purchases/Purchase-Orders/Edit', [
            'order' => $order,
            'suppliers' => $suppliers,
            'medicines' => $medicines,
            'goods' => $goods
        ]);
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
     * Export purchase orders to Excel
     */
    public function export(Request $request)
    {
        try {
            // Lấy dữ liệu với filter
            $query = StockImport::with(['supplier', 'items']);

            // Filter theo status nếu có
            if ($request->has('status') && $request->status) {
                $statuses = explode(',', $request->status);
                // Chỉ filter nếu có ít nhất 1 status hợp lệ
                $validStatuses = array_intersect($statuses, ['imported', 'pending', 'completed', 'cancelled', 'temp', 'ordered']);
                if (!empty($validStatuses)) {
                    $query->whereIn('status', $validStatuses);
                }
            }

            // Filter theo ngày nếu có (hỗ trợ cả from_date/to_date và date_from/date_to)
            if ($request->filled('from_date') || $request->filled('to_date')) {
                $query->filterByDate($request->from_date, $request->to_date);
            }

            // Filter theo nhà cung cấp nếu có
            if ($request->has('supplier_id') && $request->supplier_id) {
                $query->where('supplier_id', $request->supplier_id);
            }

            // Filter theo search query nếu có
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('import_code', 'like', "%{$search}%")
                        ->orWhereHas('supplier', function ($subQ) use ($search) {
                            $subQ->where('ten_nha_cung_cap', 'like', "%{$search}%")
                                ->orWhere('ma_nha_cung_cap', 'like', "%{$search}%");
                        });
                });
            }

            $imports = $query->orderBy('created_at', 'desc')->get();

            // Format dữ liệu cho export
            $formattedImports = $imports->map(function ($import) {
                return [
                    'import_code' => $import->import_code,
                    'supplier_name' => $import->supplier->ten_nha_cung_cap ?? 'N/A',
                    'ma_nha_cung_cap' => $import->supplier->ma_nha_cung_cap ?? 'N/A',
                    'import_date' => $import->import_date,
                    'status' => $import->status,
                    'total_amount' => $import->total_amount,
                    'paid_amount' => $import->paid_amount ?? 0,
                    'remaining_amount' => $import->remaining_amount ?? 0,
                    'note' => $import->note ?? '',
                    'items_count' => $import->items->count(),
                    'created_at' => $import->created_at,
                ];
            });

            // Export file
            $exportService = new StockImportExport();
            return $exportService->download($formattedImports->toArray());
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
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
        foreach ($stockImport->items as $item) {
            if ($item->product_type === 'medicine') //nếu sản phẩm là thuốc
            {
                $medicine = Medicine::find($item->product_id);
                $medicine->ton_kho += $item->quantity;
                $medicine->save();
            } else //nếu sản phẩm là hàng hóa
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

    /**
     * Xuất file Excel cho một phiếu đặt hàng cụ thể
     */
    public function exportSingle(string $id)
    {
        try {
            // Load với items và product relationship
            $stockImport = StockImport::with(['supplier', 'items.product'])->findOrFail($id);

            // Format dữ liệu với chi tiết items
            $formattedImport = [
                'import_code' => $stockImport->import_code,
                'supplier_name' => $stockImport->supplier->ten_nha_cung_cap ?? 'N/A',
                'ma_nha_cung_cap' => $stockImport->supplier->ma_nha_cung_cap ?? '',
                'created_at' => $stockImport->created_at,
                'status' => $stockImport->status,
                'total_amount' => $stockImport->total_amount,
                'discount' => $stockImport->discount ?? 0,
                'supplier_pay' => $stockImport->supplier_pay ?? 0,
                'supplier_paid' => $stockImport->supplier_paid ?? 0,
                'note' => $stockImport->note,
                'items' => $stockImport->items->map(function ($item) {
                    // Sử dụng accessor product_name có sẵn trong model
                    $productName = 'N/A';

                    if ($item->product) {
                        if ($item->product_type === 'medicine') {
                            $productName = $item->product->ten_thuoc ?? 'N/A';
                        } else {
                            $productName = $item->product->ten_hang_hoa ?? 'N/A';
                        }
                    }

                    return [
                        'product_name' => $productName,
                        'product_type' => $item->product_type === 'medicine' ? 'Thuốc' : 'Hàng hóa',
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'discount' => $item->discount ?? 0,
                        'total_price' => $item->total_price,
                        'note' => $item->note ?? ''
                    ];
                })
            ];

            // Export file
            $exportService = new StockImportExport();
            return $exportService->downloadSingle($formattedImport, $stockImport->import_code);
        } catch (\Exception $e) {
            \Log::error('Export Single Purchase Order Error: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }
}
