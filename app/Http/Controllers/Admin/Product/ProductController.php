<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\DrugRoute;
use App\Models\Manufacturer;
use App\Models\Medicine;
use App\Models\Goods;
use App\Models\Service;
use App\Models\Position;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display main product listing page.
     */
    public function index()
    {
        $medicines        = Medicine::with(['category', 'manufacturer', 'drugRoute', 'position'])->latest()->paginate(10);
        $goods            = Goods::with(['category', 'manufacturer', 'position'])->latest()->paginate(10);
        $services         = Service::with(['category', 'creator', 'updater'])->latest()->paginate(10);
        $categories       = ProductCategory::getCategoriesForSelect();
        $parentCategories = ProductCategory::getParentCategories();
        $manufacturers    = Manufacturer::all();
        $drugRoutes       = DrugRoute::all();
        $positions        = Position::all();

        return view(
            'admin.products.Danhsachhanghoa.index',
            compact('medicines', 'goods', 'services', 'categories', 'parentCategories', 'manufacturers', 'drugRoutes', 'positions')
        );
    }

    /**
     * Show the form for creating a new medicine (legacy).
     */
    public function createMedicine()
    {
        $data = $this->getFormData();
        return view('admin.products.Danhsachhanghoa.create.medicine', $data);
    }

    /**
     * Show the form for creating a new goods (legacy).
     */
    public function createGoods()
    {
        $data = $this->getFormData();
        return view('admin.products.Danhsachhanghoa.create.goods', $data);
    }

    /**
     * Show the form for creating a new medicine (legacy alias).
     */
    public function create()
    {
        $data = $this->getFormData();
        return view('admin.products.Danhsachhanghoa.create.medicine', $data);
    }

    /**
     * Store a newly created medicine (legacy).
     */
    public function storeMedicine(Request $request)
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
     * Store a newly created goods (legacy).
     */
    public function storeGoods(Request $request)
    {
        // Log request data
        \Log::info('StoreGoods Request Data:', $request->all());
        
        try {
            $request->validate([
                'ten_hang_hoa'      => 'required|string|max:255',
                'ma_hang'           => 'nullable|string|max:50',
                'ma_vach'           => 'nullable|string',
                'nhom_hang_id'      => 'required|exists:product_categories,id',
                'gia_von'           => 'required|numeric|min:0',
                'gia_ban'           => 'required|numeric|min:0',
                'ton_kho'           => 'nullable|integer|min:0',
                'ton_thap_nhat'     => 'nullable|integer|min:0',
                'ton_cao_nhat'      => 'nullable|integer|min:0',
                'quan_ly_theo_lo'   => 'boolean',
                'manufacturer_id'   => 'nullable|exists:manufacturers,id',
                'nuoc_san_xuat'     => 'nullable|string',
                'quy_cach_dong_goi' => 'required|string|max:255',
                'position_id'       => 'nullable|exists:positions,id',
                'trong_luong'       => 'nullable|numeric|min:0',
                'don_vi_tinh'       => 'nullable|string',
                'ban_truc_tiep'     => 'nullable',
                'mo_ta'             => 'nullable|string',
                'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'khach_dat'         => 'nullable|integer|min:0',
            ]);
            
            \Log::info('Validation passed');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed:', $e->errors());
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
        }

        $data = $request->all();
        $data['ban_truc_tiep'] = $request->has('ban_truc_tiep') ? 1 : 0;
        $data['quan_ly_theo_lo'] = $request->has('quan_ly_theo_lo') ? 1 : 0;

        \Log::info('Processed data:', $data);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('goods', 'public');
            $data['image'] = $imagePath;
            \Log::info('Image uploaded:', ['path' => $imagePath]);
        }

        try {
            $goods = Goods::create($data);
            \Log::info('Goods created successfully:', ['id' => $goods->id]);
            return redirect()->route('admin.products.index')->with('success', 'Thêm hàng hóa thành công!');
        } catch (\Exception $e) {
            \Log::error('Error creating goods:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Lỗi khi tạo hàng hóa: ' . $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource (legacy method).
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
     * Remove the specified resource from storage (legacy method).
     */
    public function destroy(string $id)
    {
        // Legacy method - not used
    }

    /**
     * Display the specified resource (legacy method).
     */
    public function show(string $id)
    {
        // Legacy method - not used
    }

    /**
     * Show detail of a medicine (API) - legacy.
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
     * Show detail of a goods (API) - legacy.
     */
    public function showGoodsDetail(string $id)
    {
        $goods = Goods::with(['category', 'manufacturer', 'position'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'product' => $goods
        ]);
    }

    /**
     * Get data for forms.
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
}
