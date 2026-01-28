<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class StaffCustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $query = User::where('role', 'user')
            ->when($search, function ($q, $search) {
                $q->where(function ($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            });

        $stats = [
            'totalCustomers' => User::where('role', 'user')->count(),
            'activeCustomers' => User::where('role', 'user')->whereNotNull('email_verified_at')->count(),
        ];

        $customers = $query->withCount('orders')
            ->withSum('orders', 'total_amount')
            ->latest()
            ->paginate($perPage)
            ->withQueryString()
            ->through(function ($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'address' => $customer->address,
                    'avatar_url' => $this->getAvatarUrl($customer->avatar),
                    'orders_count' => $customer->orders_count ?? 0,
                    'total_amount' => $customer->orders_sum_total_amount ?? 0
                ];
            });

        // LƯU Ý: Kiểm tra kỹ tên folder 'Customer' hay 'Customers' trong resources/js/Pages/Staff/
        return Inertia::render('Staff/Customer/Dashboard', [
            'customers' => $customers,
            'stats' => $stats,
            'filters' => $request->only(['search', 'per_page']),
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'nullable|string|max:15',
            ]);

            // Xử lý an toàn dữ liệu Tỉnh/Thành
            $province = $this->safeInput($request->province);
            $district = $this->safeInput($request->district);
            $ward = $this->safeInput($request->ward);

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

    public function edit(string $id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['success' => false, 'message' => 'Không tìm thấy'], 404);
        return response()->json(['success' => true, 'data' => $user]);
    }

    public function update(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
            ]);

            $updateData = $request->only(['name', 'email', 'phone', 'address']);

            // Kiểm tra nếu có thay đổi Tỉnh/Thành thì mới update, và xử lý an toàn
            if ($request->has('province')) $updateData['province'] = $this->safeInput($request->province);
            if ($request->has('district')) $updateData['district'] = $this->safeInput($request->district);
            if ($request->has('ward')) $updateData['ward'] = $this->safeInput($request->ward);

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

    // --- CÁC HÀM HELPER ---

    /**
     * Helper: Tránh lỗi truy cập mảng trên null hoặc chuỗi
     */
    private function safeInput($input)
    {
        if (is_array($input)) {
            return $input['name'] ?? null;
        }
        return $input;
    }

    private function getAvatarUrl($avatarPath)
    {
        if (!$avatarPath) return null;
        if (str_starts_with($avatarPath, 'http')) return $avatarPath;
        
        // Chuẩn hóa đường dẫn
        $path = str_starts_with($avatarPath, 'avatars/') ? $avatarPath : 'avatars/' . $avatarPath;

        // Nếu file tồn tại vật lý
        if (file_exists(public_path('storage/' . $path))) {
             return url('storage/' . $path);
        }
        return Storage::url($path);
    }
}