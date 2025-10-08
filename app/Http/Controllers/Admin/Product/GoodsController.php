<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\Goods;
use App\Models\Manufacturer;
use App\Models\Position;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class GoodsController extends Controller
{
    /**
     * Display main goods listing page.
     */
    public function index()
    {
        $goods = Goods::with(['category', 'manufacturer', 'position'])
            ->latest()
            ->paginate(10);

        $data = $this->getFormData();

        return view(
            'admin.products.Danhsachhanghoa.index',
            compact('goods', 'data')
        );
    }

    /**
     * List goods with relationships.
     */
    public function listGoods()
    {
        $goods = Goods::with(['category', 'manufacturer', 'position'])->get();
        $data = $this->getFormData();
        
        return view('admin.products.Danhsachhanghoa.index', compact('goods', 'data'));
    }

    /**
     * Vue component for goods listing with PrimeVue DataTable.
     */
    public function vueListGoods()
    {
        $goods = Goods::with(['category', 'manufacturer', 'position'])
            ->get()
            ->map(function ($good) {
                return [
                    'id' => $good->id,
                    'ma_hang' => $good->ma_hang,
                    'ten_hang_hoa' => $good->ten_hang_hoa,
                    'category_name' => $good->category?->name,
                    'quy_cach_dong_goi' => $good->quy_cach_dong_goi,
                    'don_vi_tinh' => $good->don_vi_tinh,
                    'gia_von' => $good->gia_von,
                    'gia_ban' => $good->gia_ban,
                    'ban_truc_tiep' => $good->ban_truc_tiep,
                    'gia_von_formatted' => $good->gia_von_formatted,
                    'gia_ban_formatted' => $good->gia_ban_formatted,
                ];
            });

        $categories = ProductCategory::select('id', 'name')->get();

        return Inertia::render('Admin/Products/Lists/ListGoods', [
            'goods' => $goods,
            'categories' => $categories,
        ]);
    }

    /**
     * Display goods inventory/stock view.
     */
    public function inventory()
    {
        $goods = Goods::with(['category', 'manufacturer', 'position'])
            ->orderBy('ten_hang_hoa')
            ->paginate(15);

        return view('admin.products.Danhsachthuoc.Listgoods', compact('goods'));
    }

    /**
     * Show the form for creating a new goods.
     */
    public function create()
    {
        $data = $this->getFormData();
        return view('admin.products.Danhsachhanghoa.create.goods', $data);
    }

    /**
     * Store a newly created goods.
     */
    /**
     * Store a newly created goods.
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 1. Validate input data
        $request->validate([
            'ten_hang_hoa'      => 'required|string|max:255',
            'ma_hang'           => 'nullable|string|max:50',
            'ma_vach'           => 'nullable|string|max:100',
            'nhom_hang_id'      => 'required|exists:product_categories,id',
            'gia_von'           => 'required|numeric|min:0',
            'gia_ban'           => 'required|numeric|min:0',
            'quy_cach_dong_goi' => 'required|string|max:255',
            'manufacturer_id'   => 'nullable|exists:manufacturers,id',
            'nuoc_san_xuat'     => 'nullable|string|max:100',
            'ton_thap_nhat'     => 'nullable|integer|min:0',
            'ton_cao_nhat'      => 'nullable|integer|min:0',
            'position_id'       => 'nullable|exists:positions,id',
            'trong_luong'       => 'nullable|numeric|min:0',
            'don_vi_tinh'       => 'nullable|string|max:50',
            'ban_truc_tiep'     => 'nullable',
            'mo_ta'             => 'nullable|string',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();
        $data['ban_truc_tiep'] = $request->has('ban_truc_tiep') ? 1 : 0;
        $data['quan_ly_theo_lo'] = $request->has('quan_ly_theo_lo') ? 1 : 0;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('goods', 'public');
            $data['image'] = $imagePath;
        }

        Goods::create($data);
        
        return redirect()->route('admin.products.index')->with('success', 'Thêm hàng hóa thành công!');
    }

    /**
     * Show the form for editing a goods.
     */
    public function edit($id)
    {
        $goods = Goods::findOrFail($id);
        $data = $this->getFormData();
        
        return view('admin.products.Danhsachhanghoa.edit.goods', compact('goods', 'data'));
    }

    /**
     * Update the specified goods.
     */
    /**
     * Update the specified goods.
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // 1. Find goods by ID
        $goods = Goods::findOrFail($id);
        
        // 2. Validate input data
        $request->validate([
            'ten_hang_hoa'      => 'required|string|max:255',
            'ma_hang'           => 'nullable|string|max:50',
            'ma_vach'           => 'nullable|string|max:100',
            'nhom_hang_id'      => 'required|exists:product_categories,id',
            'gia_von'           => 'required|numeric|min:0',
            'gia_ban'           => 'required|numeric|min:0',
            'quy_cach_dong_goi' => 'required|string|max:255',
            'manufacturer_id'   => 'nullable|exists:manufacturers,id',
            'nuoc_san_xuat'     => 'nullable|string|max:100',
            'ton_thap_nhat'     => 'nullable|integer|min:0',
            'ton_cao_nhat'      => 'nullable|integer|min:0',
            'position_id'       => 'nullable|exists:positions,id',
            'trong_luong'       => 'nullable|numeric|min:0',
            'don_vi_tinh'       => 'nullable|string|max:50',
            'ban_truc_tiep'     => 'nullable',
            'mo_ta'             => 'nullable|string',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();
        $data['ban_truc_tiep'] = $request->has('ban_truc_tiep') ? 1 : 0;
        $data['quan_ly_theo_lo'] = $request->has('quan_ly_theo_lo') ? 1 : 0;

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu có
            if ($goods->image) {
                Storage::disk('public')->delete($goods->image);
            }
            $imagePath = $request->file('image')->store('goods', 'public');
            $data['image'] = $imagePath;
        }

        $goods->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật hàng hóa thành công!');
    }

    /**
     * Delete a goods.
     */
    public function destroy($id)
    {
        $goods = Goods::findOrFail($id);
        
        // Xóa ảnh nếu có
        if ($goods->image) {
            Storage::disk('public')->delete($goods->image);
        }
        
        $goods->delete();

        // Kiểm tra nếu là AJAX request
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Xóa hàng hóa thành công!'
            ]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Xóa hàng hóa thành công!');
    }

    /**
     * Show detail of a goods (API).
     */
    public function show($id)
    {
        $goods = Goods::with(['category', 'manufacturer', 'position'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'product' => $goods
        ]);
    }

    /**
     * Get goods detail for edit modal (API).
     */
    public function detail($id)
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
    protected function getFormData() //lấy dữ liệu cho form
    {
        return [
            'categories'       => ProductCategory::getAllCategoriesWithDepth(),
            'parentCategories' => ProductCategory::getAllCategoriesWithDepth(),
            'manufacturers'    => Manufacturer::all(),
            'positions'        => Position::all(),
        ];
    }

    public function generateCodes() //tao mã hàng ngẫu nhiên và mã vạch ngẫu nhiên
    {
        $productCode = Goods::generateProductCode();
        $barcode = Goods::generateBarcode();

        return response()->json([
            'ma_hang' => $productCode,
            'ma_vach' => $barcode
        ]);
    }
}
