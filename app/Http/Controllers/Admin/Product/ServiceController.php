<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    /**
     * Display a listing of services
     */
    public function index()
    {
        $categories = ProductCategory::getCategoriesForSelect();
        $services = Service::with(['category'])->orderBy('created_at', 'desc')->get();
        $manufacturers = \App\Models\Manufacturer::all();
        $positions = \App\Models\Position::all();
        
        return view('admin.products.Danhsachhanghoa.index', compact('categories', 'services', 'manufacturers', 'positions'));
    }

    /**
     * Get services list for AJAX
     */
    public function listServices(Request $request)
    {
        $query = Service::with(['category']);

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('nhom_dich_vu_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('trang_thai', $request->status);
        }

        // Search by name or code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ten_dich_vu', 'LIKE', "%{$search}%")
                ->orWhere('ma_hang', 'LIKE', "%{$search}%");
            });
        }

        $services = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'services' => $services->items(),
            'pagination' => [
                'current_page' => $services->currentPage(),
                'last_page' => $services->lastPage(),
                'total' => $services->total()
            ]
        ]);
    }

    /**
     * Store a newly created service
     */
    public function store(Request $request)
    {
        $request->validate([
            'ma_hang' => 'required|string|max:255|unique:services,ma_hang',
            'ten_dich_vu' => 'required|string|max:255',
            'nhom_dich_vu_id' => 'nullable|exists:product_categories,id',
            'gia_ban' => 'required|numeric|min:0',
            'mo_ta' => 'nullable|string',
            'hinh_thuc' => 'required|in:tai_nha_thuoc,tai_nha_khach',
            'thoi_gian_thuc_hien' => 'nullable|integer|min:1',
            'trang_thai' => 'required|in:kich_hoat,tam_ngung,luu_tam',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ghi_chu' => 'nullable|string'
        ]);

        $data = $request->except(['image']);
        
        // Map form fields to database columns
        if (isset($data['gia_ban'])) {
            $data['gia_dich_vu'] = $data['gia_ban'];
            unset($data['gia_ban']);
        }
        
        if (isset($data['nhom_dich_vu_id'])) {
            $data['nhom_hang_id'] = $data['nhom_dich_vu_id'];
            unset($data['nhom_dich_vu_id']);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('services', $imageName, 'public');
            $data['image'] = $imagePath;
        }

        try {
            $service = Service::create($data);

            return redirect()->route('admin.products.index')
                ->with('success', 'Dịch vụ đã được tạo thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi tạo dịch vụ: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified service
     */
    public function edit($id)
    {
        $service = Service::with(['category'])->findOrFail($id);
        $categories = ProductCategory::getCategoriesForSelect();
        $manufacturers = \App\Models\Manufacturer::all();
        $positions = \App\Models\Position::all();
        
        return view('admin.products.Danhsachhanghoa.edit.service', compact('service', 'categories', 'manufacturers', 'positions'));
    }

    /**
     * Display the specified service
     */
    public function show($id)
    {
        try {
            $service = Service::with(['category', 'creator', 'updater'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'service' => $service
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy dịch vụ!'
            ], 404);
        }
    }

    /**
     * Update the specified service
     */
    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $request->validate([
            'ma_hang' => 'required|string|max:255|unique:services,ma_hang,' . $id,
            'ten_dich_vu' => 'required|string|max:255',
            'nhom_dich_vu_id' => 'nullable|exists:product_categories,id',
            'gia_ban' => 'required|numeric|min:0',
            'mo_ta' => 'nullable|string',
            'hinh_thuc' => 'required|in:tai_nha_thuoc,tai_nha_khach',
            'thoi_gian_thuc_hien' => 'nullable|integer|min:1',
            'trang_thai' => 'required|in:kich_hoat,tam_ngung,luu_tam',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ghi_chu' => 'nullable|string'
        ]);

        $data = $request->except(['image']);
        
        // Map form fields to database columns
        if (isset($data['gia_ban'])) {
            $data['gia_dich_vu'] = $data['gia_ban'];
            unset($data['gia_ban']);
        }
        
        if (isset($data['nhom_dich_vu_id'])) {
            $data['nhom_hang_id'] = $data['nhom_dich_vu_id'];
            unset($data['nhom_dich_vu_id']);
        }

        // Xử lý ảnh mới
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ
            if ($service->image && Storage::disk('public')->exists($service->image)) {
                Storage::disk('public')->delete($service->image);
            }

            $image = $request->file('image');
            $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('services', $imageName, 'public');
            $data['image'] = $imagePath;
        }

        try {
            $service->update($data);

            return redirect()->route('admin.products.index')
            ->with('success', 'Dịch vụ đã được cập nhật thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
            ->withInput()
            ->with('error', 'Có lỗi xảy ra khi cập nhật dịch vụ: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified service
     */
    public function destroy($id)
    {
        try {
            $service = Service::findOrFail($id);

            // Delete image if exists
            if ($service->image && Storage::disk('public')->exists($service->image)) {
                Storage::disk('public')->delete($service->image);
            }

            $service->delete();

            return response()->json([
                'success' => true,
                'message' => 'Dịch vụ đã được xóa thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa dịch vụ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get service detail for edit modal
     */
    public function detail($id)
    {
        try {
            $service = Service::with(['category'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'product' => $service  // Changed from 'service' to 'product' for consistency
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy dịch vụ!'
            ], 404);
        }
    }

    /**
     * Update service status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'trang_thai' => 'required|in:kich_hoat,tam_ngung,luu_tam'
        ]);

        try {
            $service = Service::findOrFail($id);
            $service->update([
                'trang_thai' => $request->trang_thai,
                'updated_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Trạng thái dịch vụ đã được cập nhật!',
                'service' => $service->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật trạng thái!'
            ], 500);
        }
    }
}
