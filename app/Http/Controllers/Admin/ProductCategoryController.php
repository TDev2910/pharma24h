<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCategory;


class ProductCategoryController extends Controller
{
    public function index()
    {
        // Fix: Use getAllCategoriesWithDepth for dropdown compatibility
        $categories = ProductCategory::getAllCategoriesWithDepth();
        $parents = ProductCategory::getRootCategories(); 
        return view('admin.products.DanhSachHangHoa.index', compact('categories', 'parents'));
    }

    /**
     * Show form to create new category
     */
    public function create()
    {               
        $parents = ProductCategory::getAllCategoriesWithDepth();  
        return view('admin.categories.create', compact('parents'));
    }

    /**
     * Show form to edit category
     */
    public function edit($id)
    {
        $category = ProductCategory::findOrFail($id);
        // Use hierarchical dropdown for parent selection
        $parents = ProductCategory::getAllCategoriesWithDepth();
        return view('admin.categories.edit', compact('category', 'parents'));
    }

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

        ProductCategory::create($request->only('name', 'parent_id', 'sort_order'));

        return redirect()->route('admin.categories.index')
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

        return redirect()->route('admin.categories.index')
                        ->with('success', 'Cập nhật nhóm hàng thành công!');
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

        return redirect()->route('admin.categories.index')
                        ->with('success', "Đã xóa nhóm hàng '{$categoryName}' thành công!");
    }
}
