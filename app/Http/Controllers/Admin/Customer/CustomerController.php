<?php

namespace App\Http\Controllers\Admin\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Core\Customer\Ports\Inbound\CustomerUseCaseInterface;
use App\Core\Customer\Domain\DTOs\CustomerData;

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

        $this->useCase->createCustomer($dto);

        return redirect()->back()->with('success', 'Thêm khách hàng thành công!');
    }

    /**
     * Edit
     */
    public function edit(string $id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['success' => false, 'message' => 'Không tìm thấy'], 404);
        return response()->json(['success' => true, 'data' => $user]);
    }

    /**
     * Update
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'ward' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
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

        return redirect()->back()->with('success', 'Cập nhật khách hàng thành công!');
    }

    /**
     * Destroy
     */
    public function destroy(string $id)
    {
        $this->useCase->deleteCustomer($id);

        return redirect()->back()->with('success', 'Xóa khách hàng thành công!');
    }
}