<?php

namespace App\Http\Controllers\Admin\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Core\Customer\Ports\Inbound\CustomerUseCaseInterface;
use App\Core\Customer\Domain\CustomerData;

class CustomerController extends Controller
{
    public function __construct(
        private CustomerUseCaseInterface $useCase
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $data = $this->useCase->getDashboardData($search, $perPage);

        return Inertia::render('Admin/Customers/Dashboard', [
            'customers' => $data['customers'],
            'stats' => $data['stats'],
            'filters' => $request->only(['search', 'per_page']), 
        ]);
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

            $dto = new CustomerData(
                name: $request->name,
                email: $request->email,
                phone: $request->phone,
                address: $request->address,
                province: $this->safeInput($request->province),
                district: $this->safeInput($request->district),
                ward: $this->safeInput($request->ward),
                password: $request->password
            );

            $user = $this->useCase->createCustomer($dto);

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
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
            ]);
            
            $dto = new CustomerData(
                name: $request->name,
                email: $request->email,
                phone: $request->phone,
                address: $request->address,
                province: $this->safeInput($request->province),
                district: $this->safeInput($request->district),
                ward: $this->safeInput($request->ward),
                password: $request->filled('password') ? $request->password : null
            );

            $this->useCase->updateCustomer($id, $dto);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thành công!'
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
            $this->useCase->deleteCustomer($id);

            return redirect()->back()->with('success', 'Xóa khách hàng thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}