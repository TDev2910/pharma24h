<?php

namespace App\Http\Controllers\Admin\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupplierCategory;
use App\Models\Supplier;

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
        
        return view('admin.products.Nhaphang.Suppliers.index',compact('suppliers', 'supplierGroups', 'provinces', 'businessTypes'));
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
            'khu_vuc' => 'required|string|max:100',             // Tỉnh/Thành phố
            'phuong_xa' => 'required|string|max:100',           // Quận/Huyện
            'nhom_nha_cung_cap_id' => 'required|exists:supplier_categories,id',
            'ghi_chu' => 'nullable|string',
            'ten_cong_ty' => 'nullable|string|max:255|unique:suppliers',
            'ma_so_thue' => 'nullable|string|max:20|unique:suppliers',   
            'trang_thai' => 'nullable|in:active,inactive'], 
            [
                //thông báo lỗi 
                'ma_nha_cung_cap.unique' => 'Mã nhà cung cấp đã tồn tại!',
                'email.unique' => 'Email đã được sử dụng!',
                'ten_cong_ty.unique' => 'Tên công ty đã tồn tại trong hệ thống!',
                'ma_so_thue.unique' => 'Mã số thuế đã được đăng ký!'
            ]);

            // Cảnh báo nếu tên nhà cung cấp trùng nhaau
            $existingName = Supplier::where('ten_nha_cung_cap', $validated['ten_nha_cung_cap'])->first();
            if ($existingName) {
                session()->flash('warning', 'Đã có nhà cung cấp cùng tên: ' . $existingName->ma_nha_cung_cap);
            }

            // Sử dụng dữ liệu đã validate trực tiếp
            $supplierData = $validated;
            
            // Set default status nếu không có
            if (!isset($supplierData['trang_thai'])) {
                $supplierData['trang_thai'] = 'active';
            }

            $supplier = Supplier::create($supplierData);

            // Nếu là AJAX request, trả về JSON response
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Tạo nhà cung cấp thành công!',
                    'supplier' => $supplier->load('category'),
                    'redirect' => route('admin.suppliers.index')
                ]);
            }

            return redirect()->route('admin.suppliers.index')->with('success', 'Tạo nhà cung cấp thành công!');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // AJAX validation error response
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            // General error handling
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
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
}
