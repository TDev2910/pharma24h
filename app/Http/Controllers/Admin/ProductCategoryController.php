<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCategory;


class ProductCategoryController extends Controller
{
    /**
     * Store new category with enhanced validation
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:product_categories,name',
            'parent_id' => [
                'nullable',
                'exists:product_categories,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        // Prevent creating too deep hierarchy (optional limit)
                        $parent = ProductCategory::find($value);
                        if ($parent && $parent->getDepth() >= 3) {
                            $fail('Không thể tạo danh mục quá 4 cấp.');
                        }
                    }
                }
            ],
            'sort_order' => 'nullable|integer|min:0'
        ]);

        $category = ProductCategory::create($request->only('name', 'parent_id', 'sort_order'));

        // Return JSON response for API calls
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tạo nhóm hàng thành công!',
                'data' => $category
            ], 201);
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Tạo nhóm hàng thành công!');
    }

    /**
     * Update category with enhanced validation
     */
    public function update(Request $request, $id)
    {
        $category = ProductCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:product_categories,name,' . $id,
            'parent_id' => [
                'nullable',
                'exists:product_categories,id',
                function ($attribute, $value, $fail) use ($id) {
                    if ($value) {
                        // Prevent self-reference
                        if ($value == $id) {
                            $fail('Danh mục không thể là cha của chính nó.');
                        }

                        // Prevent circular reference
                        $parent = ProductCategory::find($value);
                        if ($parent) {
                            $ancestors = [];
                            $current = $parent;
                            while ($current) {
                                if ($current->id == $id) {
                                    $fail('Không thể tạo tham chiếu vòng tròn.');
                                    break;
                                }
                                $ancestors[] = $current->id;
                                $current = $current->parent;
                            }
                        }
                    }
                }
            ],
            'sort_order' => 'nullable|integer|min:0'
        ]);

        $category->update($request->only('name', 'parent_id', 'sort_order'));

        // Return JSON response for API calls
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật nhóm hàng thành công!',
                'data' => $category
            ], 200);
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Cập nhật nhóm hàng thành công!');
    }

    /**
     * Get categories for modal selection (with product counts)
     */
    public function getCategoriesForModal(Request $request)
    {
        $search = $request->get('search', '');

        $categories = ProductCategory::getCategoryTree();

        // Convert to array format for Vue component
        $categoriesArray = $this->convertToArray($categories);

        return response()->json([
            'success' => true,
            'data' => $categoriesArray
        ]);
    }

    /**
     * Convert Collection to array format for Vue component
     */
    private function convertToArray($categories)
    {
        $result = [];

        foreach ($categories as $category) {
            $categoryData = [
                'id' => $category->id,
                'name' => $category->name,
                'parent_id' => $category->parent_id,
                'children' => $category->children->isNotEmpty() ? $this->convertToArray($category->children) : []
            ];

            $result[] = $categoryData;
        }

        return $result;
    }

    /**
     * Add product counts to categories recursively
     */
    private function addProductCounts($categories, $search = '')
    {
        $result = [];

        foreach ($categories as $category) {
            // Filter by search term if provided
            $matchesSearch = empty($search) ||
                stripos($category->name, $search) !== false ||
                $this->categoryMatchesSearch($category, $search);

            if ($matchesSearch || !empty($search)) {
                $categoryData = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'parent_id' => $category->parent_id,
                    'children' => $this->addProductCounts($category->children, $search)
                ];

                $result[] = $categoryData;
            }
        }

        return $result;
    }

    /**
     * Check if category or any of its children matches search term
     */
    private function categoryMatchesSearch($category, $search)
    {
        if (stripos($category->name, $search) !== false) {
            return true;
        }

        foreach ($category->children as $child) {
            if ($this->categoryMatchesSearch($child, $search)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Delete category with cascade handling
     */
    public function destroy($id)
    {
        $category = ProductCategory::findOrFail($id);

        // Check if category has products (you might want to add this check)
        // $productCount = $category->products()->count();
        // if ($productCount > 0) {
        //     return redirect()->back()->with('error', 'Không thể xóa danh mục có sản phẩm!');
        // }

        $categoryName = $category->name;
        $category->delete(); // Will cascade delete children due to DB constraint

        // Return JSON response for API calls
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Đã xóa nhóm hàng '{$categoryName}' thành công!"
            ], 200);
        }

        return redirect()->route('admin.products.index')
            ->with('success', "Đã xóa nhóm hàng '{$categoryName}' thành công!");
    }
}
