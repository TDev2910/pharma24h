<?php

namespace App\Http\Controllers\Admin\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Lấy dữ liệu từ db với orders count và total amount
        $totalCustomers = User::where('role', 'user')->count();
        $activeCustomers = User::where('role', 'user')
            ->whereNotNull('email_verified_at')
            ->count();

        // Lấy danh sách khách hàng 
        $customers = User::where('role', 'user')
            ->withCount('orders') //đếm số lượng đơn hàng user đã mua
            ->withSum('orders', 'total_amount') // tổng tiền user đã mua
            ->paginate(10);

        $customersData = $customers->map(function ($customer) {
            $avatarUrl = null;
            if ($customer->avatar) {
                // đường dẫn avatar
                $avatarPath = $customer->avatar;

                // Nếu đường dẫn không bắt đầu bằng 'avatars/', thêm vào
                if (!str_starts_with($avatarPath, 'avatars/')) {
                    $avatarPath = 'avatars/' . $avatarPath;
                }

                $avatarUrl = url('storage/' . $avatarPath);

                // Kiểm tra file có tồn tại không
                $fullPath = public_path('storage/' . $avatarPath);
                if (!file_exists($fullPath)) {
                    $avatarUrl = null; // Set null nếu file không tồn tại
                }
            }

            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'address' => $customer->address,
                'avatar' => $customer->avatar,
                'avatar_url' => $avatarUrl,
                'orders_count' => $customer->orders_count ?? 0,
                'total_amount' => $customer->orders_sum_total_amount ?? 0
            ];
        });

        return inertia('Admin/Customers/Dashboard', [
            'stats' => [
                'totalCustomers' => $totalCustomers,
                'activeCustomers' => $activeCustomers
            ],
            'customers' => $customersData,
            'pagination' => [
                'current_page' => $customers->currentPage(),
                'last_page' => $customers->lastPage(),
                'per_page' => $customers->perPage(),
                'total' => $customers->total(),
                'from' => $customers->firstItem(),
                'to' => $customers->lastItem()
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
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
                'province' => 'nullable|string',
                'district' => 'nullable|string',
                'ward' => 'nullable|string',
            ], [
                'name.required' => 'Vui lòng nhập tên khách hàng',
                'email.required' => 'Vui lòng nhập email',
                'email.email' => 'Email không đúng định dạng',
                'email.unique' => 'Email đã được sử dụng',
                'password.required' => 'Vui lòng nhập mật khẩu',
                'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
                'password.confirmed' => 'Xác nhận mật khẩu không khớp',
            ]);

            // Xử lý dữ liệu tỉnh/thành phố từ Vue.js
            $province = null;
            $district = null;
            $ward = null;

            if ($request->province) {
                if (is_array($request->province)) {
                    $province = $request->province['name'] ?? null;
                } else {
                    $province = $request->province;
                }
            }

            if ($request->district) {
                if (is_array($request->district)) {
                    $district = $request->district['name'] ?? null;
                } else {
                    $district = $request->district;
                }
            }

            if ($request->ward) {
                if (is_array($request->ward)) {
                    $ward = $request->ward['name'] ?? null;
                } else {
                    $ward = $request->ward;
                }
            }

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

            // Trả về JSON response cho Vue.js
            return response()->json([
                'success' => true,
                'message' => 'Thêm khách hàng thành công!',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'avatar_url' => null,
                    'orders_count' => 0,
                    'total_amount' => 0
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            if (!is_numeric($id) || $id <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID khách hàng không hợp lệ'
                ], 400);
            }

            $user = User::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khách hàng với ID: ' . $id
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải thông tin khách hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'phone' => 'nullable|string|max:15',
                'address' => 'nullable|string|max:255',
                'province' => 'nullable|string',
                'district' => 'nullable|string',
                'ward' => 'nullable|string',
                'password' => 'nullable|string|min:8|confirmed',
            ], [
                'name.required' => 'Vui lòng nhập tên khách hàng',
                'email.required' => 'Vui lòng nhập email',
                'email.email' => 'Email không đúng định dạng',
                'email.unique' => 'Email đã được sử dụng',
                'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
                'password.confirmed' => 'Xác nhận mật khẩu không khớp',
            ]);

            // Xử lý dữ liệu tỉnh/thành phố từ Vue.js
            $province = $user->province; // Giữ nguyên dữ liệu cũ
            $district = $user->district; // Giữ nguyên dữ liệu cũ  
            $ward = $user->ward; // Giữ nguyên dữ liệu cũ

            // Chỉ cập nhật nếu có dữ liệu mới từ request
            if ($request->province) {
                if (is_array($request->province)) {
                    $province = $request->province['name'] ?? $user->province;
                } else {
                    $province = $request->province;
                }
            }

            if ($request->district) {
                if (is_array($request->district)) {
                    $district = $request->district['name'] ?? $user->district;
                } else {
                    $district = $request->district;
                }
            }

            if ($request->ward) {
                if (is_array($request->ward)) {
                    $ward = $request->ward['name'] ?? $user->ward;
                } else {
                    $ward = $request->ward;
                }
            }
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'province' => $province,
                'district' => $district,
                'ward' => $ward,
            ];
            // Cập nhật mật khẩu nếu có
            if ($request->filled('password')) {
                $updateData['password'] = bcrypt($request->password);
            }

            $user->update($updateData);

            // Trả về JSON response cho Vue.js
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thông tin khách hàng thành công!',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'province' => $user->province,
                    'district' => $user->district,
                    'ward' => $user->ward,
                    'avatar_url' => null,
                    'orders_count' => $user->orders_count ?? 0,
                    'total_amount' => $user->total_amount ?? 0
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khách hàng với ID: ' . $id
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa khách hàng thành công!'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khách hàng với ID: ' . $id
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dashboard data for Vue.js component
     */
    public function dashboard()
    {
        // Lấy dữ liệu thật từ database
        $totalCustomers = User::where('role', 'user')->count();
        $activeCustomers = User::where('role', 'user')
            ->whereNotNull('email_verified_at') // Khách hàng đã verify email
            ->count();

        // Lấy danh sách khách hàng với pagination
        $customers = User::where('role', 'user')
            ->withCount('orders')
            ->withSum('orders', 'total_amount')
            ->paginate(10);

        // Format dữ liệu cho Vue component
        $customersData = $customers->map(function ($customer) {
            $avatarUrl = null;
            if ($customer->avatar) {
                // Kiểm tra và sửa đường dẫn avatar
                $avatarPath = $customer->avatar;

                // Nếu đường dẫn không bắt đầu bằng 'avatars/', thêm vào
                if (!str_starts_with($avatarPath, 'avatars/')) {
                    $avatarPath = 'avatars/' . $avatarPath;
                }

                $avatarUrl = url('storage/' . $avatarPath);

                // Kiểm tra file có tồn tại không
                $fullPath = public_path('storage/' . $avatarPath);
                if (!file_exists($fullPath)) {
                    $avatarUrl = null; // Set null nếu file không tồn tại
                }
            }

            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'address' => $customer->address,
                'avatar' => $customer->avatar,
                'avatar_url' => $avatarUrl,
                'orders_count' => $customer->orders_count ?? 0,
                'total_amount' => $customer->orders_sum_total_amount ?? 0
            ];
        });

        return Inertia::render('Admin/Customers/Dashboard', [
            'stats' => [
                'totalCustomers' => $totalCustomers,
                'activeCustomers' => $activeCustomers
            ],
            'customers' => $customersData,
            'pagination' => [
                'current_page' => $customers->currentPage(),
                'last_page' => $customers->lastPage(),
                'per_page' => $customers->perPage(),
                'total' => $customers->total(),
                'from' => $customers->firstItem(),
                'to' => $customers->lastItem()
            ]
        ]);
    }
}
