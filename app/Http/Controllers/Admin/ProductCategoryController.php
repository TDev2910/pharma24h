<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Traits\HasTreeStructure;
use App\Http\Requests\Admin\ProductCategory\StoreProductCategoryRequest;
use App\Http\Requests\Admin\ProductCategory\UpdateProductCategoryRequest;

class ProductCategoryController extends Controller
{
    use HasTreeStructure;

    /**
     * Hiển thị form tạo/sửa (Thường dùng trong Dashboard)
     */
    public function edit(ProductCategory $category)
    {
        $parents = ProductCategory::where('id', '!=', $category->id)->get();
        return view('admin.categories.edit', compact('category', 'parents'));
    }

    /**
     * Lưu danh mục mới
     */
    public function store(StoreProductCategoryRequest $request)
    {
        $category = ProductCategory::create($request->validated());

        return $this->respondWithSuccess('Tạo nhóm hàng thành công!', $category, 201);
    }

    /**
     *Cập nhật danh mục
     */
    public function update(UpdateProductCategoryRequest $request, ProductCategory $category)
    {
        $category->update($request->validated());

        return $this->respondWithSuccess('Cập nhật nhóm hàng thành công!', $category);
    }

    /**
     * Xóa danh mục
     */
    public function destroy(ProductCategory $category)
    {
        $category->delete();
        return $this->respondWithSuccess("Đã xóa nhóm hàng thành công!");
    }

    /**
     *API: Lấy dữ liệu cây cho Modal/TreeSelect
     */
    public function getCategoriesForModal(Request $request)
    {
        // Load đệ quy tất cả cấp con
        $categories = ProductCategory::all();

        $treeData = $this->buildTreeNodes($categories, null, ['key' => 'id', 'label' => 'name']);

        return response()->json(['success' => true, 'data' => $treeData]);
    }

    /**
     * Hàm phụ trợ trả về Response 
     */
    private function respondWithSuccess($message, $data = null, $status = 200)
    {
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $data
            ], $status);
        }

        return redirect()->route('admin.products.index')->with('success', $message);
    }
}
