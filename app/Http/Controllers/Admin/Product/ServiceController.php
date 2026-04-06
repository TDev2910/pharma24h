<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\StoreServiceRequest;
use App\Http\Requests\Admin\Product\UpdateServiceRequest;
use App\Core\Products\Services\Ports\Inbound\ServiceUseCaseInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function __construct(
        private readonly ServiceUseCaseInterface $serviceUseCase
    ) {}

    /**
     * Display a listing of services
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);

        $data = $this->serviceUseCase->getServiceList($search, $perPage);
        $formData = $this->serviceUseCase->getFormData();

        return Inertia::render('Admin/Products/Lists/ListServices', array_merge(
            $data,
            $formData
        ));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create()
    {
        $formData = $this->serviceUseCase->getFormData();
        return Inertia::render('Admin/Products/Create/Service', $formData);
    }

    /**
     * Store a newly created service.
     */
    public function store(StoreServiceRequest $request)
    {
        $this->serviceUseCase->createService($request->toDTO());

        return redirect()->route('admin.products.index')->with('success', 'Dịch vụ đã được tạo thành công!');
    }

    /**
     * Show the form for editing a service.
     */
    public function edit(int|string $id)
    {
        $detail = $this->serviceUseCase->getServiceById($id);
        $formData = $this->serviceUseCase->getFormData();

        return Inertia::render('Admin/Products/Edit/Service', array_merge(
            $detail,
            $formData
        ));
    }

    /**
     * Update the specified service.
     */
    public function update(UpdateServiceRequest $request, int|string $id)
    {
        $this->serviceUseCase->updateService($id, $request->toDTO());
        return redirect()->back()->with('success', 'Cập nhật dịch vụ thành công!');
    }

    /**
     * Remove the specified service.
     */
    public function destroy(int|string $id)
    {
        $this->serviceUseCase->deleteService($id);
        return redirect()->back()->with('success', 'Xóa dịch vụ thành công!');
    }

    /**
     * Display the specified service (JSON for modals if needed)
     */
    public function show(int|string $id)
    {
        return response()->json($this->serviceUseCase->getServiceById($id));
    }

    /**
     * List services for unified product list page.
     */
    public function listServices(Request $request)
    {
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);
        
        $data = $this->serviceUseCase->getServiceList($search, $perPage);

        return Inertia::render('Admin/Products/Lists/UnifiedList', array_merge(
            $data,
            $this->serviceUseCase->getFormData(),
            [
                'productType' => 'service',
                'medicines' => [],
                'goods' => []
            ]
        ));
    }

    /**
     * Generate codes for API
     */
    public function generateCodes()
    {
        return response()->json($this->serviceUseCase->generateCodes());
    }
}
