<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Core\Products\Medicine\Ports\Inbound\MedicineUseCaseInterface;
use App\Http\Requests\Admin\Product\StoreMedicineRequest;
use App\Http\Requests\Admin\Product\UpdateMedicineRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MedicineController extends Controller
{
    public function __construct(
        private readonly MedicineUseCaseInterface $useCase
    ) {}

    /**
     * Display main medicine listing page.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);

        $data = $this->useCase->getMedicineList($search, $perPage);

        return Inertia::render('Admin/Products/Lists/ListMedicines', array_merge(
            $data,
            $this->useCase->getFormData()
        ));
    }

    /**
     * List medicines with advanced filter 
     */
    public function apiIndex(Request $request)
    {
        $filters = $request->only(['search', 'category_id', 'manufacturer_id', 'drugRoute_id', 'from_date', 'to_date']);
        $perPage = $request->get('per_page', 10);

        return response()->json($this->useCase->getFilteredMedicines($filters, $perPage));
    }

    /**
     * List medicines for unified product list page.
     */
    public function listMedicines(Request $request)
    {
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);

        $data = $this->useCase->getMedicineList($search, $perPage);

        return Inertia::render('Admin/Products/Lists/UnifiedList', array_merge(
            $data,
            $this->useCase->getFormData(),
            ['productType' => 'medicine', 'goods' => [], 'services' => []]
        ));
    }

    /**
     * Show the form for creating a new medicine.
     */
    public function create()
    {
        $formData = $this->useCase->getFormData();
        return Inertia::render('Admin/Products/Create/Medicine', $formData);
    }

    /**
     * Store a newly created medicine.
     */
    public function store(StoreMedicineRequest $request)
    {
        $this->useCase->createMedicine($request->toDTO());
        
        return redirect()->route('admin.products.index')->with('success', 'Thuốc đã được thêm thành công!');
    }

    /**
     * Show the form for editing a medicine.
     */
    public function edit(int|string $id)
    {
        $detail   = $this->useCase->getMedicineById($id);
        $formData = $this->useCase->getFormData();

        return Inertia::render('Admin/Products/Edit/Medicine', array_merge(
            $detail,
            $formData
        ));
    }

    /**
     * Update the specified medicine.
     */
    public function update(UpdateMedicineRequest $request, int|string $id)
    {
        $this->useCase->updateMedicine($id, $request->toDTO());
        return redirect()->back()->with('success', 'Cập nhật thành công!');
    }

    /**
     * Delete a medicine.
     */
    public function destroy(int|string $id)
    {
        $this->useCase->deleteMedicine($id);
        return redirect()->back()->with('success', 'Xóa thành công!');
    }

    public function show(int|string $id)
    {
        return response()->json($this->useCase->getMedicineById($id));
    }

    /**
     * Generate random product code & barcode.
     */
    public function generateCodes()
    {
        return response()->json($this->useCase->generateCodes());
    }
}
