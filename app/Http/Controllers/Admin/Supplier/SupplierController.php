<?php

namespace App\Http\Controllers\Admin\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupplierCategory;
use App\Models\Supplier;
use App\Models\StockImport;
use App\Models\Inventory\PurchaseReturn;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SupplierController extends Controller
{
    /**
     * Display a listing of suppliers
     */
    public function index()
    {
        // Lấy suppliers từ database với relationship
        $suppliers = Supplier::with('category')
            ->latest()
            ->paginate(15);      
        // Lấy supplier groups cho filter
        $supplierGroups = SupplierCategory::active()->ordered()->get();
        
        // Tạo empty collections cho các biến cần thiết trong view
        $provinces = collect([]);
        $businessTypes = collect([]);
        
        // Format suppliers cho Vue component
        $formattedSuppliers = $suppliers->getCollection()->map(function($supplier) {
            return [
                'id' => $supplier->id,
                'ma_nha_cung_cap' => $supplier->ma_nha_cung_cap,
                'ten_nha_cung_cap' => $supplier->ten_nha_cung_cap,
                'dien_thoai' => $supplier->dien_thoai,
                'email' => $supplier->email,
                'dia_chi' => $supplier->dia_chi,
                'khu_vuc' => $supplier->khu_vuc,
                'phuong_xa' => $supplier->phuong_xa,
                'nhom_nha_cung_cap_id' => $supplier->nhom_nha_cung_cap_id,
                'ten_cong_ty' => $supplier->ten_cong_ty,
                'ma_so_thue' => $supplier->ma_so_thue,
                'ghi_chu' => $supplier->ghi_chu,
                'trang_thai' => $supplier->trang_thai,
                'created_at' => $supplier->created_at,
                'category' => $supplier->category ? [
                    'id' => $supplier->category->id,
                    'name' => $supplier->category->name
                ] : null
            ];
        });
        
        return Inertia::render('Admin/Purchases/Suppliers/Dashboard', [
            'suppliers' => $formattedSuppliers,
            'supplierGroups' => $supplierGroups->map(function($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name
                ];
            }),
            'provinces' => $provinces->map(function($province) {
                return [
                    'id' => $province->id ?? $province,
                    'name' => $province->name ?? $province
                ];
            }),
            'pagination' => [
                'current_page' => $suppliers->currentPage(),
                'last_page' => $suppliers->lastPage(),
                'per_page' => $suppliers->perPage(),
                'total' => $suppliers->total(),
                'from' => $suppliers->firstItem(),
                'to' => $suppliers->lastItem()
            ]
        ]);
    }

    public function create()
    {
        if (request()->ajax()) {
            // Lấy supplier groups cho dropdown
            $supplierGroups = SupplierCategory::active()->ordered()->get();
            
            $html = view('admin.products.Nhaphang.Suppliers.partials.supplier-form', compact('supplierGroups'))->render();
            return response()->json([
                'status' => 'success',
                'html' => $html
            ]);
        }
        
        return redirect()->route('admin.suppliers.index');
    }

    /**
     * Store a newly created supplier
     */
    public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'ten_nha_cung_cap' => 'required|string|max:255',
            'ma_nha_cung_cap' => 'required|string|max:50|unique:suppliers',
            'dien_thoai' => 'required|string|max:20',
            'email' => 'nullable|email|max:100|unique:suppliers',
            'dia_chi' => 'required|string',
            'khu_vuc' => 'required|string|max:100',
            'phuong_xa' => 'required|string|max:100',
            'nhom_nha_cung_cap_id' => 'required|exists:supplier_categories,id',
            'ghi_chu' => 'nullable|string',
            'ten_cong_ty' => 'nullable|string|max:255|unique:suppliers',
            'ma_so_thue' => 'nullable|string|max:20|unique:suppliers',   
            'trang_thai' => 'nullable|in:active,inactive'
        ], [
            'ma_nha_cung_cap.unique' => 'Mã nhà cung cấp đã tồn tại!',
            'email.unique' => 'Email đã được sử dụng!',
            'ten_cong_ty.unique' => 'Tên công ty đã tồn tại trong hệ thống!',
            'ma_so_thue.unique' => 'Mã số thuế đã được đăng ký!'
        ]);

        // Cảnh báo nếu tên nhà cung cấp trùng nhau
        $existingName = Supplier::where('ten_nha_cung_cap', $validated['ten_nha_cung_cap'])->first();
        if ($existingName) {
            session()->flash('warning', 'Đã có nhà cung cấp cùng tên: ' . $existingName->ma_nha_cung_cap);
        }

        // Set default status nếu không có
        if (!isset($validated['trang_thai'])) {
            $validated['trang_thai'] = 'active';
        }

        // Load relationship để trả về đầy đủ thông tin
        $supplier = Supplier::with('category')->create($validated);

        // Kiểm tra nếu là Inertia request
        if ($request->header('X-Inertia')) {
            // Trả về redirect cho Inertia (sẽ trigger onSuccess callback trong Vue)
            // Supplier mới sẽ được load lại trong index() method
            return redirect()->route('admin.suppliers.index')
                ->with('success', 'Nhà cung cấp đã được thêm thành công');
        }

        // Nếu là AJAX request thông thường (không phải Inertia)
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Nhà cung cấp đã được thêm thành công',
                'data' => $supplier
            ], 201);
        }

        // Default: redirect với flash message
        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Nhà cung cấp đã được thêm thành công');
            
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Validation errors - Inertia tự động xử lý và trigger onError
        if ($request->header('X-Inertia')) {
            throw $e; // Inertia sẽ tự động handle validation errors
        }
        
        // AJAX validation error
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        }
        
        throw $e;
        
    } catch (\Exception $e) {
        \Log::error('Error creating supplier: ' . $e->getMessage());
        
        // Inertia request - trả về redirect với errors
        if ($request->header('X-Inertia')) {
            return redirect()->back()
                ->withErrors(['general' => 'Có lỗi xảy ra: ' . $e->getMessage()])
                ->withInput();
        }
        
        // AJAX error
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm nhà cung cấp: ' . $e->getMessage()
            ], 500);
        }
        
        return redirect()->back()
            ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
            ->withInput();
    }
}

    /**
     * Display the specified supplier
     */
    public function show($id)
    {
        $supplier = Supplier::with('category')->findOrFail($id);
        $supplierStats = [
            'total_orders' => 0, // Tổng đơn hàng
            'total_products' => 0, // Tổng sản phẩm cung cấp
            'last_order_date' => null, // Đơn hàng gần nhất
        ];
        
        if (request()->ajax()) {
            $html = view('admin.products.Nhaphang.Suppliers.partials.supplier-view', compact('supplier', 'supplierStats'))->render();
            return response()->json([
                'status' => 'success',
                'html' => $html,
                'supplier' => $supplier
            ]);
        }
        
        return view('admin.products.Nhaphang.Suppliers.show', compact('supplier', 'supplierStats'));
    }

    /**
     * Show edit 
     */
    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplierGroups = SupplierCategory::active()->ordered()->get();
        
        return response()->json([
            'success' => true,
            'supplier' => $supplier,
            'supplierGroups' => $supplierGroups
        ]);
    }

    /**
     * Update supplier
     */
    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật thành công!'
        ]);
    }

    /**
     * Remove the specified supplier
     */
    public function destroy($id)
    {

    }

    public function getImports($supplierId)
    {
        $imports = StockImport::where('supplier_id', $supplierId)
            ->latest()
            ->get();
        
        $formattedImports = $imports->map(function($import) {
            return [
                'Code' => $import->import_code,
                'DayImport' => $import->import_date ? \Carbon\Carbon::parse($import->import_date)->format('d/m/Y') : '-',
                'User' => Auth::user()->name ?? 'N/A', 
                'TotalAmount' => number_format($import->total_amount ?? 0, 0, ',', '.') . ' đ',
                'Status' => $import->status ?? 'pending'
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $formattedImports
        ]);
    }

    public function getReturns($supplierId)
    {
        $returns = PurchaseReturn::where('supplier_id', $supplierId)
            ->latest()
            ->get();
        
        $formattedReturns = $returns->map(function($return) {
            return [
                'Code' => $return->return_code,
                'DayReturn' => $return->return_date ? \Carbon\Carbon::parse($return->return_date)->format('d/m/Y') : '-',
                'UserReturn' => Auth::user()->name ?? 'N/A', 
                'TotalAmountReturn' => number_format($return->total_amount ?? 0, 0, ',', '.') . ' đ',
                'StatusReturn' => $return->status ?? 'pending'
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $formattedReturns
        ]);
    }
}
