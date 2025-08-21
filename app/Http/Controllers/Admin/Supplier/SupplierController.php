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
        $suppliers = collect([]); 
        $supplierGroups = SupplierCategory::active()->ordered()->get();
        $provinces = collect([]); 
        $businessTypes = collect([]); 

        return view('admin.products.Nhaphang.Suppliers.index', compact('suppliers', 'supplierGroups', 'provinces', 'businessTypes'));
    }

    /**
     * Show the form for creating a new supplier
     */
    public function create()
    {
                             
    }

    /**
     * Store a newly created supplier
     */
    public function store(Request $request)
    {
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

            Supplier::create($supplierData);

            return redirect()->route('admin.suppliers.index')->with('success', 'Tạo nhà cung cấp thành công!');
    }

    /**
     * Display the specified supplier
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified supplier
     */
    public function edit($id)
    {

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
