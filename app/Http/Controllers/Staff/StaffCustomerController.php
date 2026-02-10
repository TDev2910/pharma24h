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
                    'province' => $customer->province,
                    'district' => $customer->district,
                    'ward' => $customer->ward,
                    'avatar_url' => $this->getAvatarUrl($customer->avatar),
                    'orders_count' => $customer->orders_count ?? 0,
                    'total_amount' => $customer->orders_sum_total_amount ?? 0
                ];
            });
        return Inertia::render('Staff/Customer/Dashboard', [
            'customers' => $customers,
            'stats' => $stats,
            'filters' => $request->only(['search', 'per_page']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:15',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone,
            'role' => 'user',
            //tỉnh thành
            'province' => $this->safeInput($request->province),
            'district' => $this->safeInput($request->district),
            'ward' => $this->safeInput($request->ward),
            // 'role' => 'user',
        ]);
        return redirect()->back()->with('success', 'Thêm khách hàng thành công!');
    }

    public function edit(string $id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['success' => false, 'message' => 'Không tìm thấy'], 404);
        return response()->json(['success' => true, 'data' => $user]);
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        //Validate
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'ward' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'province' => $this->safeInput($request->province),
            'district' => $this->safeInput($request->district),
            'ward' => $this->safeInput($request->ward),
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);
        return redirect()->back()->with('success', 'Cập nhật khách hàng thành công!');
    }

    public function destroy(string $id)
    {
        $user = User::find($id);
        $user->delete();
        return redirect()->back()->with('success', 'Xóa khách hàng thành công!');
    }

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

        //đường dẫn avatar
        $path = str_starts_with($avatarPath, 'avatars/') ? $avatarPath : 'avatars/' . $avatarPath;

        if (file_exists(public_path('storage/' . $path))) {
            return url('storage/' . $path);
        }
        return Storage::url($path);
    }
}
