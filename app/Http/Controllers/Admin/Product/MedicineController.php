<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\DrugRoute;
use App\Models\Manufacturer;
use App\Models\Medicine;
use App\Models\Position;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MedicineController extends Controller
{
    /**
     * Display main medicine listing page.
     */
    public function index()
    {
        $medicines = Medicine::with(['category', 'manufacturer', 'drugRoute', 'position'])
            ->latest()
            ->paginate(10);

        $data = $this->getFormData();

        return view(
            'admin.products.Danhsachthuoc.Listmedicine',
            compact('medicines', 'data')
        );
    }

    /**
     * List medicines with search and filter.
     */
    public function listMedicines(Request $request)
    {
        $query = Medicine::with(['category', 'manufacturer', 'drugRoute', 'position']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ten_thuoc', 'LIKE', "%{$search}%")
                ->orWhere('ma_hang', 'LIKE', "%{$search}%")
                ->orWhere('hoat_chat', 'LIKE', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('nhom_hang_id', $request->category_id);
        }

        $medicines = $query->latest()->paginate(15);
        $data = $this->getFormData();

        return view(
            'admin.products.Danhsachthuoc.Listmedicine',
            compact('medicines', 'data')
        );
    }

    /**
     * Show the form for creating a new medicine.
     */
    public function create()
    {
        $data = $this->getFormData();
        return view('admin.products.Danhsachhanghoa.create.medicine', $data);
    }

    /**
     * Store a newly created medicine.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ten_thuoc'         => 'required|string|max:255',
            'ma_hang'           => 'nullable|string|max:50|unique:medicines,ma_hang',
            'ten_viet_tat'      => 'nullable|string|max:100',
            'ton_kho'           => 'required|integer|min:0',
            'gia_ban'           => 'nullable|numeric|min:0',
            'gia_von'           => 'nullable|numeric|min:0',
            'ton_thap_nhat'     => 'nullable|integer|min:0',
            'khach_dat'         => 'nullable|integer|min:0',
            'mo_ta'             => 'nullable|string',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'nhom_hang_id'      => 'nullable|exists:product_categories,id',
            'manufacturer_id'   => 'nullable|exists:manufacturers,id',
            'drugusage_id'      => 'nullable|exists:drug_routes,id',
            'position_id'       => 'nullable|exists:positions,id',
            'ma_vach'           => 'nullable|string|unique:medicines,ma_vach',
            'so_dang_ky'        => 'nullable|string|unique:medicines,so_dang_ky',
            'hoat_chat'         => 'nullable|string',
            'ham_luong'         => 'nullable|string',
            'nuoc_san_xuat'     => 'nullable|string',
            'quy_cach_dong_goi' => 'nullable|string',
            'ton_cao_nhat'      => 'nullable|integer|min:0',
            'trong_luong'       => 'nullable|numeric|min:0',
            'don_vi_tinh'       => 'nullable|string',
            'ban_truc_tiep'     => 'nullable',
        ], [
            'ma_hang.unique'    => 'Mã hàng bạn nhập đang trùng với một sản phẩm khác.',
            'ma_vach.unique'    => 'Mã vạch bạn nhập đang trùng với một sản phẩm khác.',
            'so_dang_ky.unique' => 'Số đăng ký bạn nhập đang trùng với một sản phẩm khác.',
        ]);

        $data = $request->all();
        $data['ban_truc_tiep'] = $request->has('ban_truc_tiep') ? 1 : 0;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = $imagePath;
        }

        Medicine::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Thêm thuốc thành công!');
    }

    /**
     * Show the form for editing a medicine.
     */
    public function edit($id)
    {
        $medicine = Medicine::findOrFail($id);
        $data = $this->getFormData();
        
        return view('admin.products.Danhsachhanghoa.edit.medicine', compact('medicine', 'data'));
    }

    /**
     * Update the specified medicine.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'ten_thuoc'         => 'required|string|max:255',
            'ma_hang'           => 'nullable|string|max:50|unique:medicines,ma_hang,' . $id,
            'ten_viet_tat'      => 'nullable|string|max:100',
            'gia_ban'           => 'nullable|numeric|min:0',
            'gia_von'           => 'nullable|numeric|min:0',
            'ton_thap_nhat'     => 'nullable|integer|min:0',
            'khach_dat'         => 'nullable|integer|min:0',
            'mo_ta'             => 'nullable|string',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'nhom_hang_id'      => 'nullable|exists:product_categories,id',
            'manufacturer_id'   => 'nullable|exists:manufacturers,id',
            'drugusage_id'      => 'nullable|exists:drug_routes,id',
            'position_id'       => 'nullable|exists:positions,id',
            'ma_vach'           => 'nullable|string|unique:medicines,ma_vach,' . $id,
            'so_dang_ky'        => 'nullable|string|unique:medicines,so_dang_ky,' . $id,
            'hoat_chat'         => 'nullable|string',
            'ham_luong'         => 'nullable|string',
            'nuoc_san_xuat'     => 'nullable|string',
            'quy_cach_dong_goi' => 'nullable|string',
            'ton_cao_nhat'      => 'nullable|integer|min:0',
            'trong_luong'       => 'nullable|numeric|min:0',
            'don_vi_tinh'       => 'nullable|string',
            'ban_truc_tiep'     => 'nullable',
        ], [
            'ma_hang.unique'    => 'Mã hàng bạn nhập đang trùng với một sản phẩm khác.',
            'ma_vach.unique'    => 'Mã vạch bạn nhập đang trùng với một sản phẩm khác.',
            'so_dang_ky.unique' => 'Số đăng ký bạn nhập đang trùng với một sản phẩm khác.',
        ]);

        $medicine = Medicine::findOrFail($id);
        
        // Sanitize giá trị
        $data = $request->all();
        $data['gia_von'] = (int)preg_replace('/\D/', '', $request->gia_von);
        $data['gia_ban'] = (int)preg_replace('/\D/', '', $request->gia_ban);
        
        // Xử lý checkbox ban_truc_tiep
        $data['ban_truc_tiep'] = $request->has('ban_truc_tiep') ? 1 : 0;
        
        // Xử lý trường bắt buộc khác (nếu có)
        if (!isset($data['khach_dat'])) {
            $data['khach_dat'] = $medicine->khach_dat ?? 0;
        }
        
        // Update
        $medicine->update($data);
        
        return redirect()->route('admin.products.index')->with('success', 'Cập nhật thuốc thành công');
    }

    /**
     * Delete a medicine.
     */
    public function destroy($id)
    {
        $medicine = Medicine::findOrFail($id);

        // Xóa ảnh nếu có
        if ($medicine->image) {
            Storage::disk('public')->delete($medicine->image);
        }

        $medicine->delete();

        // Kiểm tra nếu là AJAX request
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Xóa thuốc thành công!'
            ]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Xóa thuốc thành công!');
    }

    /**
     * Show detail of a medicine (API).
     */
    public function show($id)
    {
        $medicine = Medicine::with(['category', 'manufacturer', 'drugRoute', 'position'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'product' => $medicine
        ]);
    }

    /**
     * Get data for forms.
     */
    protected function getFormData()
    {
        return [
            'categories'       => ProductCategory::getAllCategoriesWithDepth(),
            'parentCategories' => ProductCategory::getAllCategoriesWithDepth(),
            'manufacturers'    => Manufacturer::all(),
            'drugRoutes'       => DrugRoute::all(),
            'positions'        => Position::all(),
        ];
    }
}
