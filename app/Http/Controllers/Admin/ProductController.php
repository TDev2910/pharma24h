<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DrugRoute;
use App\Models\Manufacturer;
use App\Models\Medicine;
use App\Models\Position;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $medicines        = Medicine::with(['category', 'manufacturer', 'drugRoute', 'position'])->latest()->paginate(10);
        $categories       = ProductCategory::getCategoriesForSelect();
        $parentCategories = ProductCategory::getParentCategories();
        $manufacturers    = Manufacturer::all();
        $drugRoutes       = DrugRoute::all();
        $positions        = Position::all();

        return view(
            'admin.products.Danhsachhanghoa.index',
            compact('medicines', 'categories', 'parentCategories', 'manufacturers', 'drugRoutes', 'positions')
        );
    }

    /**
     * Get data for medicine forms.
     */
    protected function getFormData()
    {
        return [
            'categories'       => ProductCategory::getCategoriesForSelect(),
            'parentCategories' => ProductCategory::getParentCategories(),
            'manufacturers'    => Manufacturer::all(),
            'drugRoutes'       => DrugRoute::all(),
            'positions'        => Position::all(),
        ];
    }

    /**
     * Show the form for creating a new medicine.
     */
    public function createMedicine()
    {
        $data = $this->getFormData();
        return view('admin.products.Danhsachhanghoa.createmedicine', $data);
    }

    /**
     * Store a newly created medicine.
     */
    public function storeMedicine(Request $request)
    {
        $request->validate([
            'ten_thuoc'         => 'required|string|max:255',
            'ma_hang'           => 'nullable|string|max:50',
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
            'ma_vach'           => 'nullable|string',
            'so_dang_ky'        => 'nullable|string',
            'hoat_chat'         => 'nullable|string',
            'ham_luong'         => 'nullable|string',
            'nuoc_san_xuat'     => 'nullable|string',
            'quy_cach_dong_goi' => 'nullable|string',
            'ton_cao_nhat'      => 'nullable|integer|min:0',
            'trong_luong'       => 'nullable|numeric|min:0',
            'don_vi_tinh'       => 'nullable|string',
        ]);

        $data = $request->all();
        $data['ban_truc_tiep'] = $request->has('ban_truc_tiep') ? 1 : 0;

        if ($request->hasFile('image')) {
            $imagePath     = $request->file('image')->store('products', 'public');
            $data['image'] = $imagePath;
        }

        Medicine::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Thêm thuốc thành công!');
    }

    /**
     * Store a newly created DrugRoute (Đường dùng).
     */
    public function storeDrugRoute(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:drug_routes,name'
        ]);

        $route = DrugRoute::create([
            'name'        => trim($validated['name']),
            'description' => $request->description ? trim($request->description) : null
        ]);

        return response()->json([
            'success'    => true,
            'drug_route' => $route,
            'message'    => 'Tạo đường dùng thành công!'
        ], 201);
    }

    /**
     * Store a newly created Manufacturer (Hãng sản xuất).
     */
    public function storeManufacturer(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:manufacturers,name'
            ], [
                'name.required' => 'Tên hãng sản xuất là bắt buộc',
                'name.unique'   => 'Hãng sản xuất này đã tồn tại',
                'name.max'      => 'Tên hãng sản xuất không được quá 255 ký tự'
            ]);

            $manufacturer = Manufacturer::create([
                'name'        => trim($validated['name']),
                'description' => $request->description ? trim($request->description) : null
            ]);

            return response()->json([
                'success'      => true,
                'manufacturer' => [
                    'id'          => $manufacturer->id,
                    'name'        => $manufacturer->name,
                    'description' => $manufacturer->description
                ],
                'message'      => 'Tạo hãng sản xuất thành công!'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created Position (Vị trí).
     */
    public function storePosition(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:positions,name'
        ]);

        $position = Position::create([
            'name'        => trim($validated['name']),
            'description' => $request->description ? trim($request->description) : null
        ]);

        return response()->json([
            'success'  => true,
            'position' => $position,
            'message'  => 'Tạo vị trí thành công!'
        ], 201);
    }

    /**
     * Show the form for creating a new medicine (alias).
     */
    public function create()
    {
        $data = $this->getFormData();
        return view('admin.products.Danhsachhanghoa.createmedicine', $data);
    }

    /**
     * Show the form for editing a medicine.
     */
    public function editMedicine($id)
    {
        $medicine = Medicine::findOrFail($id);
        $data     = $this->getFormData();
        return view('admin.products.Danhsachhanghoa.editmedicine', compact('medicine', 'data'));
    }

    /**
     * Update the specified medicine.
     */
    public function updateMedicine(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);

        $request->validate([
            'ten_thuoc'         => 'required|string|max:255',
            'ma_hang'           => 'nullable|string|max:50',
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
            'ma_vach'           => 'nullable|string',
            'so_dang_ky'        => 'nullable|string',
            'hoat_chat'         => 'nullable|string',
            'ham_luong'         => 'nullable|string',
            'nuoc_san_xuat'     => 'nullable|string',
            'quy_cach_dong_goi' => 'nullable|string',
            'ton_cao_nhat'      => 'nullable|integer|min:0',
            'trong_luong'       => 'nullable|numeric|min:0',
            'don_vi_tinh'       => 'nullable|string',
        ]);

        $data = $request->all();
        $data['ban_truc_tiep'] = $request->has('ban_truc_tiep') ? 1 : 0;

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu có
            if ($medicine->image) {
                Storage::disk('public')->delete($medicine->image);
            }
            $imagePath     = $request->file('image')->store('products', 'public');
            $data['image'] = $imagePath;
        }

        $medicine->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật thuốc thành công!');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ten_thuoc'        => 'required',
            'nhom_hang_id'     => 'required|exists:product_categories,id',
            'manufacturer_id'  => 'required|exists:manufacturers,id',
            'drugusage_id'     => 'required|exists:drug_routes,id',
        ]);

        Medicine::create($request->all());

        if ($request->has('save_and_create')) {
            return redirect()->route('admin.products.create')->with('success', 'Thêm thuốc thành công! Vui lòng nhập thông tin thuốc tiếp theo.');
        }

        return redirect()->route('admin.products.index')->with('success', 'Thêm thuốc thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show detail of a medicine (API).
     */
    public function showDetail(string $id)
    {
        $medicine = Medicine::with(['category', 'manufacturer', 'drugRoute', 'position'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'product' => $medicine
        ]);
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(ProductCategory $category)
    {
        $parents = ProductCategory::where('id', '!=', $category->id)->get();
        return view('admin.categories.edit', compact('category', 'parents'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, ProductCategory $category)
    {
        $request->validate([
            'name'      => 'required',
            'parent_id' => 'nullable|exists:product_categories,id'
        ]);
        $category->update($request->only('name', 'parent_id'));
        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
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

        $medicines        = $query->latest()->paginate(15);
        $categories       = ProductCategory::getCategoriesForSelect();
        $parentCategories = ProductCategory::getParentCategories();
        $manufacturers    = Manufacturer::all();
        $positions        = Position::all();

        return view(
            'admin.products.Danhsachthuoc.Listmedicine',
            compact('medicines', 'categories', 'parentCategories', 'manufacturers', 'positions')
        );
    }

    /**
     * Delete a medicine.
     */
    public function deleteMedicine($id)
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
}
