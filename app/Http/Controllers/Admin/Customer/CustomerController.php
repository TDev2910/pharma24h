<?php

namespace App\Http\Controllers\Admin\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Nhận tham số tìm kiếm và số dòng/trang từ Frontend
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10); // Mặc định 10 nếu không gửi lên

        // 2. Query dữ liệu + Tìm kiếm Server-side
        $query = User::where('role', 'user')
            ->when($search, function ($q, $search) {
                $q->where(function ($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            });

        // 3. Thống kê (Realtime)
        $stats = [
            'totalCustomers' => User::where('role', 'user')->count(),
            'activeCustomers' => User::where('role', 'user')->whereNotNull('email_verified_at')->count(),
        ];

        // 4. Phân trang & Format dữ liệu
        $customers = $query->withCount('orders')
            ->withSum('orders', 'total_amount')
            ->latest() // Sắp xếp 
            ->paginate($perPage)
            ->withQueryString() 
            ->through(function ($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'address' => $customer->address,
                    'avatar_url' => $this->getAvatarUrl($customer->avatar), // Hàm helper xử lý ảnh
                    'orders_count' => $customer->orders_count ?? 0,
                    'total_amount' => $customer->orders_sum_total_amount ?? 0
                ];
            });

        return Inertia::render('Admin/Customers/Dashboard', [
            'customers' => $customers,
            'stats' => $stats,
            'filters' => $request->only(['search', 'per_page']), 
        ]);
    }

    /**
     * Helper: Xử lý đường dẫn Avatar
     */
    private function getAvatarUrl($avatarPath)
    {
        if (!$avatarPath) return null;
        if (str_starts_with($avatarPath, 'http')) return $avatarPath;

        $path = str_starts_with($avatarPath, 'avatars/') ? $avatarPath : 'avatars/' . $avatarPath;

        // Kiểm tra file có tồn tại trong storage public không
        if (file_exists(public_path('storage/' . $path))) {
             return url('storage/' . $path); 
        }

        return Storage::url($path);
    }

    /**
     * Store
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'nullable|string|max:15',
                'address' => 'nullable|string|max:255',
            ], [
                'name.required' => 'Vui lòng nhập tên khách hàng',
                'email.unique' => 'Email đã được sử dụng',
                'password.confirmed' => 'Xác nhận mật khẩu không khớp',
            ]);

            // Xử lý logic Province/District/Ward
            $province = $request->province['name'] ?? ($request->province ?? null);
            $district = $request->district['name'] ?? ($request->district ?? null);
            $ward = $request->ward['name'] ?? ($request->ward ?? null);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'phone' => $request->phone,
                'address' => $request->address,
                'province' => $province,
                'district' => $district,
                'ward' => $ward,
                'role' => 'user',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thêm khách hàng thành công!',
                'data' => $user
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Edit: API lấy dữ liệu chi tiết
     */
    public function edit(string $id)
    {
        try {
             $user = User::findOrFail($id);
             return response()->json(['success' => true, 'data' => $user]);
        } catch (\Exception $e) {
             return response()->json(['success' => false, 'message' => 'Không tìm thấy user'], 404);
        }
    }

    /**
     * Update: Return JSON
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
            ]);
            
            // Logic xử lý update giữ nguyên như cũ của bạn...
            $updateData = $request->only(['name', 'email', 'phone', 'address']);
            
            // Xử lý địa chỉ
            if ($request->province) $updateData['province'] = $request->province['name'] ?? $request->province;
            if ($request->district) $updateData['district'] = $request->district['name'] ?? $request->district;
            if ($request->ward) $updateData['ward'] = $request->ward['name'] ?? $request->ward;
            
            if ($request->filled('password')) {
                $updateData['password'] = bcrypt($request->password);
            }

            $user->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thành công!',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Destroy: Dùng Redirect để Inertia tự reload list
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return redirect()->back()->with('success', 'Xóa khách hàng thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}