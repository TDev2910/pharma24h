<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Core\Products\Good\Ports\Inbound\GoodUseCaseInterface;
use App\Http\Requests\Admin\Product\StoreGoodRequest;
use App\Http\Requests\Admin\Product\UpdateGoodRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GoodsController extends Controller
{
    public function __construct(
        private readonly GoodUseCaseInterface $useCase
    ) {}

    /**
     * Display main goods listing page.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);

        $data = $this->useCase->getGoodList($search, $perPage);
        $formData = $this->useCase->getFormData();

        return Inertia::render('Admin/Products/Lists/ListGoods', array_merge(
            $data,
            $formData
        ));
    }

    /**
     * Vue component for goods listing with PrimeVue DataTable.
     */
    public function vueListGoods()
    {
        $search = request('search');
        $perPage = request('per_page', 10);
        
        $data = $this->useCase->getGoodList($search, $perPage);
        $formData = $this->useCase->getFormData();

        return Inertia::render('Admin/Products/Lists/UnifiedList', [
            'productType' => 'goods',
            'medicines'   => [],
            'goods'       => $data['goods'],
            'services'    => [],
            'categories'  => $formData['categories'],
            'data'        => $formData
        ]);
    }

    /**
     * API endpoint for goods listing with filters and pagination.
     */
    public function apiIndex(Request $request)
    {
        $filters = $request->only(['search', 'category_id', 'manufacturer_id', 'position_id', 'from_date', 'to_date']);
        $perPage = $request->get('per_page', 10);

        return response()->json($this->useCase->getFilteredGoods($filters, $perPage));
    }

    /**
     * Show the form for creating a new goods.
     */
    public function create()
    {
        $formData = $this->useCase->getFormData();
        return Inertia::render('Admin/Products/Create/Goods', $formData);
    }

    /**
     * Store a newly created goods.
     */
    public function store(StoreGoodRequest $request)
    {
        $result = $this->useCase->createGood($request->toDTO());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($result, 201);
        }

        return redirect()->route('admin.products.index')->with('success', 'Hàng hóa đã được thêm thành công!');
    }

    /**
     * Show the form for editing a goods.
     */
    public function edit($id)
    {
        $detail   = $this->useCase->getGoodById($id);
        $formData = $this->useCase->getFormData();

        return Inertia::render('Admin/Products/Edit/Goods', array_merge(
            $detail,
            $formData
        ));
    }

    /**
     * Update the specified goods.
     */
    public function update(UpdateGoodRequest $request, $id)
    {
        $result = $this->useCase->updateGood($id, $request->toDTO());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($result);
        }

        return redirect()->back()->with('success', 'Cập nhật hàng hóa thành công!');
    }

    /**
     * Delete a goods.
     */
    public function destroy($id)
    {
        $result = $this->useCase->deleteGood($id);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($result);
        }

        return redirect()->back()->with('success', 'Xóa hàng hóa thành công!');
    }

    /**
     * Show detail of a goods (API).
     */
    public function show($id)
    {
        return response()->json($this->useCase->getGoodById($id));
    }

    /**
     * Get goods detail for edit modal (API).
     */
    public function detail($id)
    {
        return response()->json($this->useCase->getGoodById($id));
    }

    public function generateCodes()
    {
        return response()->json($this->useCase->generateCodes());
    }
}
