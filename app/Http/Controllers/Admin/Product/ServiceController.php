<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ProductCategory;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
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
        $doctors = Doctor::all();

        return view('admin.products.Danhsachhanghoa.index', compact('categories', 'services', 'doctors'));
    }

    /**
     * Store a newly created service
     */
    public function store(Request $request)
    {
        $request->validate([
            'ten_dich_vu' => 'required|string|max:255',
            'nhom_hang_id' => 'nullable|exists:product_categories,id',
            'doctor_id' => 'nullable|exists:doctors,id',
            'gia_dich_vu' => 'required|numeric|min:0',
            'mo_ta' => 'nullable|string',
            'hinh_thuc' => 'required|in:tai_nha_thuoc,tai_nha_khach',
            'thoi_gian_thuc_hien' => 'nullable|integer|min:1',
            'trang_thai' => 'required|in:kich_hoat,tam_ngung,luu_tam',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ghi_chu' => 'nullable|string',
            'ma_dich_vu' => 'nullable|string|max:255'
        ]);

        $data = $request->except(['image']);


        // Auto generate service code if not provided
        if (empty($data['ma_dich_vu'])) {
            $data['ma_dich_vu'] = 'DV' . date('Ymd') . str_pad(Service::count() + 1, 4, '0', STR_PAD_LEFT);
        }

        // Add user tracking
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('services', $imageName, 'public');
            $data['image'] = $imagePath;
        }

        try {
            $service = Service::create($data);

            // Return JSON response for AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Dịch vụ đã được tạo thành công!',
                    'data' => $service->load('category')
                ]);
            }

            return redirect()->route('admin.products.index')
                ->with('success', 'Dịch vụ đã được tạo thành công!');
        } catch (\Exception $e) {

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi tạo dịch vụ: ' . $e->getMessage()
                ], 500);
            }

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
            'ma_dich_vu' => 'required|string|max:255|unique:services,ma_dich_vu,' . $id,
            'ten_dich_vu' => 'required|string|max:255',
            'nhom_hang_id' => 'nullable|exists:product_categories,id',
            'doctor_id' => 'nullable|exists:doctors,id',
            'gia_dich_vu' => 'required|numeric|min:0',
            'mo_ta' => 'nullable|string',
            'hinh_thuc' => 'required|in:tai_nha_thuoc,tai_nha_khach',
            'thoi_gian_thuc_hien' => 'nullable|integer|min:1',
            'trang_thai' => 'required|in:kich_hoat,tam_ngung,luu_tam',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ghi_chu' => 'nullable|string'
        ]);

        $data = $request->except(['image']);

        // No field mapping needed since form fields match database columns

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

    /**
     * List services for Vue component
     */
    public function listServices(Request $request)
    {
        $query = Service::with(['category', 'doctor']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ten_dich_vu', 'LIKE', "%{$search}%")
                    ->orWhere('ma_dich_vu', 'LIKE', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('nhom_hang_id', $request->category_id);
        }

        $services = $query->latest()->get();
        $data = $this->getFormData();

        return Inertia::render('Admin/Products/Lists/ListServices', [
            'services' => $services,
            'data' => $data
        ]);
    }

    /**
     * Get form data for services
     */
    protected function getFormData()
    {
        return [
            'categories' => ProductCategory::select('id', 'name')->get(),
            'doctors' => Doctor::all(),
        ];
    }
}
