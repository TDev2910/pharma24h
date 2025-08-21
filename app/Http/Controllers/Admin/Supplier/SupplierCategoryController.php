<?php

namespace App\Http\Controllers\Admin\Supplier;

use App\Http\Controllers\Controller;
use App\Models\SupplierCategory;
use Illuminate\Http\Request;

class SupplierCategoryController extends Controller
{
    /**
     * Display a listing of supplier categories
     */
    public function index()
    {
        $categories = SupplierCategory::withCount('suppliers')->ordered()->get();
        return view('admin.products.Nhaphang.Categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created supplier category
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:supplier_categories,name',
                'description' => 'nullable|string|max:500',
                'status' => 'nullable|string|in:active,inactive'
            ]);

            // Set default status if not provided
            if (!isset($validated['status'])) {
                $validated['status'] = 'active';
            }

            SupplierCategory::create($validated);
            
            return redirect()->route('admin.suppliers.index')
                ->with('success', 'Tạo nhóm nhà cung cấp thành công!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi tạo nhóm nhà cung cấp: ' . $e->getMessage())
                ->withInput();
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
        //
    }

    /**
     * Update the specified supplier category
     */
    public function update(Request $request, SupplierCategory $supplierCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:supplier_categories,name,' . $supplierCategory->id,
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive'
        ]);

        $supplierCategory->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Cập nhật nhóm nhà cung cấp thành công!'
        ]);
    }

    /**
     * Remove the specified supplier category
     */
    public function destroy(SupplierCategory $supplierCategory)
    {
        // Kiểm tra xem có suppliers nào đang sử dụng category này không
        if ($supplierCategory->suppliers()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa nhóm này vì đang có nhà cung cấp sử dụng!'
            ], 400);
        }

        $supplierCategory->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Xóa nhóm nhà cung cấp thành công!'
        ]);
    }
}
