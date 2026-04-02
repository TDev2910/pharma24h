<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\Goods;
use App\Models\Manufacturer;
use App\Models\Position;
use App\Models\ProductCategory;
use App\Traits\HasTreeStructure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Inertia\Inertia;

class GoodsController extends Controller
{
    use HasTreeStructure;

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

        $allCategories = ProductCategory::orderBy('sort_order')->orderBy('name')->get();
        $categories = $this->buildSelectOptions($allCategories);

        return Inertia::render('Admin/Products/Lists/UnifiedList', [
            'productType' => 'goods',
            'medicines' => [],
            'goods' => $goods,
            'services' => [],
            'categories' => $categories,
            'data' => []
        ]);
    }

    /**
     * API endpoint for goods listing with filters and pagination.
     */
    public function apiIndex(Request $request)
    {
        $query = Goods::with(['category', 'manufacturer', 'position']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ten_hang_hoa', 'LIKE', "%{$search}%")
                    ->orWhere('ma_hang', 'LIKE', "%{$search}%")
                    ->orWhere('ten_viet_tat', 'LIKE', "%{$search}%");
            });
        }
        if ($request->filled('category_id')) {
            $query->where('nhom_hang_id', $request->category_id);
        }
        if ($request->filled('manufacturer_id')) {
            $query->where('manufacturer_id', $request->manufacturer_id);
        }
        if ($request->filled('position_id')) {
            $query->where('position_id', $request->position_id);
        }

        // chức năng lọc theo ngày
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $perPage = $request->get('per_page', 10);
        $goods = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $goods->items(),
            'pagination' => [
                'current_page' => $goods->currentPage(),
                'last_page' => $goods->lastPage(),
                'per_page' => $goods->perPage(),
                'total' => $goods->total(),
                'from' => $goods->firstItem(),
                'to' => $goods->lastItem()
            ]
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
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
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

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Prepare data
            $data = $request->all();
            $data['slug'] = Str::slug($request->ten_hang_hoa) . '-' . time();
            $data['ban_truc_tiep'] = $request->has('ban_truc_tiep') ? 1 : 0;
            $data['quan_ly_theo_lo'] = $request->has('quan_ly_theo_lo') ? 1 : 0;

            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('goods', 'public');
                $data['image'] = $imagePath;
            }

            // Create the goods
            $goods = Goods::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Hàng hóa đã được thêm thành công',
                'data' => $goods
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm hàng hóa: ' . $e->getMessage()
            ], 500);
        }
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
        // Validate the request
        $validator = Validator::make($request->all(), [
            'ten_hang_hoa'      => 'nullable|string|max:255',
            'ma_hang'           => 'nullable|string|max:50|unique:goods,ma_hang,' . $id,
            'ma_vach'           => 'nullable|string|max:100|unique:goods,ma_vach,' . $id,
            'nhom_hang_id'      => 'nullable|exists:product_categories,id',
            'gia_von'           => 'nullable|numeric|min:0',
            'gia_ban'           => 'nullable|numeric|min:0',
            'gia_khuyen_mai'    => 'nullable|numeric|min:0',
            'quy_cach_dong_goi' => 'nullable|string|max:255',
            'manufacturer_id'   => 'nullable|exists:manufacturers,id',
            'nuoc_san_xuat'     => 'nullable|string|max:100',
            'ton_khuyen_mai'    => 'nullable|integer|min:0',
            'ton_thap_nhat'     => 'nullable|integer|min:0',
            'ton_cao_nhat'      => 'nullable|integer|min:0',
            'position_id'       => 'nullable|exists:positions,id',
            'trong_luong'       => 'nullable|numeric|min:0',
            'don_vi_tinh'       => 'nullable|string|max:50',
            'ban_truc_tiep'     => 'nullable',
            'mo_ta'             => 'nullable|string',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'ma_hang.unique'    => 'Mã hàng bạn nhập đang trùng với một sản phẩm khác.',
            'ma_vach.unique'    => 'Mã vạch bạn nhập đang trùng với một sản phẩm khác.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if (
            $request->has('ton_khuyen_mai') &&
            $request->has('ton_kho') &&
            ((int)$request->input('ton_khuyen_mai') > (int)$request->input('ton_kho'))
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Tồn khuyến mãi không được lớn hơn tổng tồn kho',
                'errors' => ['ton_khuyen_mai' => ['Tồn khuyến mãi không được lớn hơn tổng tồn kho']]
            ], 422);
        }

        try {
            $goods = Goods::findOrFail($id);

            $data = $request->all();

            // Cập nhật slug tương tự như Post
            if ($request->has('ten_hang_hoa')) {
                $data['slug'] = Str::slug($request->ten_hang_hoa) . '-' . time();
            }

            // Xử lý checkbox ban_truc_tiep
            $data['ban_truc_tiep'] = $request->has('ban_truc_tiep') ? 1 : 0;
            $data['quan_ly_theo_lo'] = $request->has('quan_ly_theo_lo') ? 1 : 0;

            // Xử lý upload ảnh
            if ($request->hasFile('image')) {
                // Xóa ảnh cũ nếu có
                if ($goods->image) {
                    Storage::disk('public')->delete($goods->image);
                }

                // Upload ảnh mới
                $imagePath = $request->file('image')->store('goods', 'public');
                $data['image'] = $imagePath;
            } else {
                // Không có file mới - giữ nguyên ảnh cũ
                unset($data['image']);
            }

            // Update
            $goods->update($data);

            // Kiểm tra nếu là AJAX request
            if (request()->ajax() || request()->wantsJson()) {
                // Refresh goods data từ database để đảm bảo có dữ liệu mới nhất
                $goods->refresh();

                // Load relationships để trả về đầy đủ thông tin
                $goods->load([
                    'category:id,name',
                    'manufacturer:id,name',
                    'position:id,name'
                ]);

                // Thêm product_type để datatable hiển thị đúng
                $goodsData = $goods->fresh()->toArray();
                $goodsData['product_type'] = 'goods';

                return response()->json([
                    'success' => true,
                    'message' => 'Thông tin hàng hóa đã được cập nhật thành công',
                    'data' => $goodsData // Đảm bảo trả về dữ liệu mới nhất với relationships
                ]);
            }

            return redirect()->route('admin.products.index')->with('success', 'Cập nhật hàng hóa thành công');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy hàng hóa với ID: ' . $id
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật hàng hóa: ' . $e->getMessage()
            ], 500);
        }
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
        if (request()->ajax() || request()->wantsJson() || request()->has('ajax')) {
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
        $allCategories = ProductCategory::orderBy('sort_order')->orderBy('name')->get();
        $categoryOptions = $this->buildSelectOptions($allCategories);

        return [
            'categories'       => $categoryOptions,
            'parentCategories' => $categoryOptions,
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
