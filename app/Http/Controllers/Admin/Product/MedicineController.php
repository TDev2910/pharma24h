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
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class MedicineController extends Controller
{
    /**
     * Display main medicine listing page.
     */
    public function index()
    {
        $medicines = Medicine::with(['category', 'manufacturer', 'drugRoute', 'position'])
            ->latest()
            ->get();

        $data = $this->getFormData();

        return Inertia::render('Admin/Products/Lists/ListMedicines', [
            'medicines' => $medicines,
            'data' => $data
        ]);
    }

    public function apiIndex(Request $request)
    {
        $query = Medicine::with(['category', 'manufacturer', 'drugRoute', 'position']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ten_thuoc', 'LIKE', "%{$search}%")
                    ->orWhere('ma_hang', 'LIKE', "%{$search}%")
                    ->orWhere('hoat_chat', 'LIKE', "%{$search}%");
            });
        }
        if ($request->filled('category_id')) {
            $query->where('nhom_hang_id', $request->category_id);
        }
        if ($request->filled('manufacturer_id')) {
            $query->where('manufacturer_id', $request->manufacturer_id);
        }
        if ($request->filled('drugRoute_id')) {
            $query->where('drugRoute_id', $request->drugRoute_id);
        }

        // chức năng lọc theo ngày
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $perPage = $request->get('per_page', 10);
        $medicines = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $medicines->items(),
            'pagination' => [
                'current_page' => $medicines->currentPage(),
                'last_page' => $medicines->lastPage(),
                'per_page' => $medicines->perPage(),
                'total' => $medicines->total()
            ]
        ]);
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

        $medicines = $query->latest()->get();
        $data = $this->getFormData();

        return Inertia::render('Admin/Products/Lists/UnifiedList', [
            'productType' => 'medicine',
            'medicines' => $medicines,
            'goods' => [],
            'services' => [],
            'categories' => [],
            'data' => $data
        ]);
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
        try {
            // Validate the request
            $validator = \Validator::make($request->all(), [
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

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Prepare data
            $data = $request->all();
            $data['ban_truc_tiep'] = $request->has('ban_truc_tiep') ? 1 : 0;

            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
                $data['image'] = $imagePath;
            }

            // Create the medicine
            $medicine = Medicine::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Thuốc đã được thêm thành công',
                'data' => $medicine
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm thuốc: ' . $e->getMessage()
            ], 500);
        }
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

        // Validate the request
        $validator = Validator::make($request->all(), [
            'ten_thuoc'         => 'nullable|string|max:255',
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
            'gia_khuyen_mai'    => 'nullable|numeric|min:0',
            'ton_khuyen_mai'    => 'nullable|integer|min:0',
        ], [
            'ma_hang.unique'    => 'Mã hàng bạn nhập đang trùng với một sản phẩm khác.',
            'ma_vach.unique'    => 'Mã vạch bạn nhập đang trùng với một sản phẩm khác.',
            'so_dang_ky.unique' => 'Số đăng ký bạn nhập đang trùng với một sản phẩm khác.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Bắt buộc tồn_khuyen_mai không lớn hơn tồn_kho (nếu tồn_tồn_khuyen_mai có)
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
            $medicine = Medicine::findOrFail($id);

            $data = $request->all();

            // Xử lý checkbox ban_truc_tiep
            $data['ban_truc_tiep'] = $request->has('ban_truc_tiep') ? 1 : 0;

            // Xử lý trường bắt buộc khác (nếu có)
            if (!isset($data['khach_dat'])) {
                $data['khach_dat'] = $medicine->khach_dat ?? 0;
            }

            // Xử lý upload ảnh
            if ($request->hasFile('image')) {
                // Xóa ảnh cũ nếu có
                if ($medicine->image) {
                    Storage::disk('public')->delete($medicine->image);
                }

                // Upload ảnh mới (giống Create)
                $imagePath = $request->file('image')->store('products', 'public');
                $data['image'] = $imagePath;
            } else {
                // Không có file mới - giữ nguyên ảnh cũ
                unset($data['image']);
            }

            // Update
            $medicine->update($data);

            // Kiểm tra nếu là AJAX request
            if (request()->ajax() || request()->wantsJson()) {
                // Refresh medicine data từ database để đảm bảo có dữ liệu mới nhất
                $medicine->refresh();


                // Load relationships để trả về đầy đủ thông tin
                $medicine->load([
                    'category:id,name',
                    'manufacturer:id,name',
                    'drugRoute:id,name',
                    'position:id,name'
                ]);

                // Thêm product_type để datatable hiển thị đúng
                $medicineData = $medicine->fresh()->toArray();
                $medicineData['product_type'] = 'medicine';

                return response()->json([
                    'success' => true,
                    'message' => 'Thông tin thuốc đã được cập nhật thành công',
                    'data' => $medicineData // Đảm bảo trả về dữ liệu mới nhất với relationships
                ]);
            }

            return redirect()->route('admin.products.index')->with('success', 'Cập nhật thuốc thành công');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thuốc với ID: ' . $id
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật thuốc: ' . $e->getMessage()
            ], 500);
        }
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

    public function generateCodes() //tao mã hàng và mã vạch ngẫu nhiên
    {
        $productCode = Medicine::generateProductCode();
        $barcode = Medicine::generateBarcode();

        return response()->json([
            'ma_hang' => $productCode,
            'ma_vach' => $barcode
        ]);
    }
}
