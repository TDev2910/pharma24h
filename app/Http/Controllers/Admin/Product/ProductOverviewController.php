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
use App\Traits\HasTreeStructure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class ProductOverviewController extends Controller
{
    use HasTreeStructure;

    /**
     * Display main product listing page (Unified View).
     */
    public function index()
    {
        $medicines        = Medicine::with(['category', 'manufacturer', 'drugRoute', 'position'])->latest()->paginate(5);
        $goods            = Goods::with(['category', 'manufacturer', 'position'])->latest()->paginate(5);
        $services         = Service::with(['category', 'creator', 'updater'])->latest()->paginate(5);
        
        $allCategories    = ProductCategory::orderBy('sort_order')->orderBy('name')->get();
        $categories       = $this->buildSelectOptions($allCategories);
        $parentCategories = $categories;
        
        $manufacturers    = Manufacturer::all();
        $drugRoutes       = DrugRoute::all();
        $positions        = Position::all();

        return view(
            'admin.products.Danhsachhanghoa.index',
            compact('medicines', 'goods', 'services', 'categories', 'parentCategories', 'manufacturers', 'drugRoutes', 'positions')
        );
    }

    /**
     * Show the form for creating a new medicine (Legacy Blade).
     */
    public function createMedicine()
    {
        $data = $this->getFormData();
        return view('admin.products.Danhsachhanghoa.create.medicine', $data);
    }

    /**
     * Show the form for creating a new goods (Legacy Blade).
     */
    public function createGoods()
    {
        $data = $this->getFormData();
        return view('admin.products.Danhsachhanghoa.create.goods', $data);
    }

    /**
     * Store a newly created medicine (Legacy Blade).
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
            return redirect()->route('admin.products.createMedicine')->with('success', 'Thêm thuốc thành công! Vui lòng nhập thông tin thuốc tiếp theo.');
        }

        return redirect()->route('admin.products.index')->with('success', 'Thêm thuốc thành công!');
    }

    /**
     * Store a newly created goods (Legacy Blade).
     */
    public function storeGoods(Request $request)
    {
        try {
            $request->validate([
                'ten_hang_hoa'      => 'required|string|max:255',
                'nhom_hang_id'      => 'required|exists:product_categories,id',
                'gia_von'           => 'required|numeric|min:0',
                'gia_ban'           => 'required|numeric|min:0',
                'quy_cach_dong_goi' => 'required|string|max:255',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed:', $e->errors());
            return redirect()->back()->withInput()->withErrors($e->errors());
        }

        $data = $request->all();
        $data['ban_truc_tiep'] = $request->has('ban_truc_tiep') ? 1 : 0;
        $data['quan_ly_theo_lo'] = $request->has('quan_ly_theo_lo') ? 1 : 0;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('goods', 'public');
            $data['image'] = $imagePath;
        }

        try {
            Goods::create($data);
            return redirect()->route('admin.products.index')->with('success', 'Thêm hàng hóa thành công!');
        } catch (\Exception $e) {
            Log::error('Error creating goods: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'Lỗi khi tạo hàng hóa']);
        }
    }

    /**
     * Show detail (API)
     */
    public function showDetail(string $id)
    {
        $medicine = Medicine::with(['category', 'manufacturer', 'drugRoute', 'position'])->findOrFail($id);
        return response()->json(['success' => true, 'product' => $medicine]);
    }

    /**
     * Show goods detail (API)
     */
    public function showGoodsDetail(string $id)
    {
        $goods = Goods::with(['category', 'manufacturer', 'position'])->findOrFail($id);
        return response()->json(['success' => true, 'product' => $goods]);
    }

    /**
     * Get data for forms.
     */
    protected function getFormData()
    {
        $allCategories = ProductCategory::orderBy('sort_order')->orderBy('name')->get();
        $categoryOptions = $this->buildSelectOptions($allCategories);
        
        return [
            'categories'       => $categoryOptions,
            'parentCategories' => $categoryOptions,
            'manufacturers'    => Manufacturer::all(),
            'drugRoutes'       => DrugRoute::all(),
            'positions'        => Position::all(),
        ];
    }
}
