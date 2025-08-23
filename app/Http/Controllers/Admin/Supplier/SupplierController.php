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

    /**
     * Show the form for creating a new supplier (AJAX support for modal)
     */
    public function create()
    {
        // Lấy supplier groups cho dropdown
        $supplierGroups = SupplierCategory::active()->ordered()->get();
        
        if (request()->ajax()) {
            return response()->json([
                'supplierGroups' => $supplierGroups,
                'status' => 'success'
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
            'khu_vuc_name' => 'required|string|max:100',        // Tên tỉnh/thành
            'phuong_xa_name' => 'required|string|max:100',      // Tên phường/xã
            'khu_vuc' => 'required|string|max:10',              // Mã tỉnh/thành (để backup)
            'phuong_xa' => 'required|string|max:10', 
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

            // Xử lý dữ liệu từ API tỉnh/thành
            $supplierData = $validated;
            if (isset($validated['khu_vuc_name'])) {
                $supplierData['khu_vuc'] = $validated['khu_vuc_name'];
            }
            if (isset($validated['phuong_xa_name'])) {
                $supplierData['phuong_xa'] = $validated['phuong_xa_name'];
            }
            
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
        $supplier ->load('category');
        $supplierStats = [
            'total_orders' => 0, // Tổng đơn hàng
            'total_products' => 0, // Tổng sản phẩm cung cấp
            'last_order_date' => null, // Đơn hàng gần nhất
        ];
        
        return view('admin.products.Nhaphang.Suppliers.show', compact('supplier', 'supplierStats'));
    }

    /**
     * Show the form for editing the specified supplier
     */
    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        $data = $this->getFormData();
        
        return view('admin.products.Nhaphang.Suppliers.edit',compact('supplier', 'data'));
    }

    /**
     * Update the specified supplier
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified supplier
     */
    public function destroy($id)
    {

    }
}
