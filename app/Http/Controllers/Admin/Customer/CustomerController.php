<?php

namespace App\Http\Controllers\Admin\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Core\Customer\Ports\Inbound\CustomerUseCaseInterface;
use App\Core\Customer\Domain\DTOs\CustomerData;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;

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
     * Store
     */
    public function store(StoreCustomerRequest $request)
    {
        $dto = $request->toDTO();

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
    public function update(UpdateCustomerRequest $request, string $id)
    {
        $dto = $request->toDTO();

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