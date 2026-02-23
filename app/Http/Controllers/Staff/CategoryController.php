<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\Category\StoreCategoryRequest;
use App\Http\Requests\Staff\Category\UpdateCategoryRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Models\Content\Category;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $allCategories = Category::withCount('posts')
            ->with('parent')
            ->orderBy('name')
            ->get();

        if ($request->search) {
            $categories = $allCategories->filter(function($cat) use ($request) {
                return str_contains(strtolower($cat->name), strtolower($request->search));
            });
        } else {
            // Build a flattened tree for display in the table
            $categories = $this->buildFlattenedTree($allCategories);
        }
        
        // Prepare categories for TreeSelect in modal
        $categoryTree = $this->buildTreeNodes($allCategories);

        return Inertia::render('Staff/Categories/Index', [
            'categories' => $categories,
            'categoryTree' => $categoryTree,
            'baseUrl' => route('staff.categories.index'), 
            'filters' => $request->only('search'),
        ]);
    }

    /**
     * Build a flat list ordered by hierarchy for table display
     */
    private function buildFlattenedTree($elements, $parentId = null, $level = 0)
    {
        $flat = [];
        foreach ($elements as $element) {
            if ($element->parent_id == $parentId) {
                $element->level = $level;
                $flat[] = $element;
                $children = $this->buildFlattenedTree($elements, $element->id, $level + 1);
                $flat = array_merge($flat, $children);
            }
        }
        return $flat;
    }

    /**
     * Build standard Tree Nodes for PrimeVue TreeSelect
     */
    private function buildTreeNodes($elements, $parentId = null) 
    {
        $branch = array();
        foreach ($elements as $element) {
            if ($element->parent_id == $parentId) {
                $children = $this->buildTreeNodes($elements, $element->id);
                $node = [
                    'key' => (string)$element->id,
                    'label' => $element->name,
                    'data' => $element->id
                ];
                if ($children) {
                    $node['children'] = $children;
                }
                $branch[] = $node;
            }
        }
        return $branch;
    }
    public function store(StoreCategoryRequest $request)
    {
        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->back()->with('success', 'Thêm danh mục mới thành công');
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        if($request->parent_id && $request->parent_id == $category->id) {
            return redirect()->back()->with('error', 'Không thể chọn chính nó làm danh mục cha');
        }

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->back()->with('success', 'Cập nhật danh mục thành công');
    }
    
    public function destroy(Category $category)
    {
        //Kiểm tra danh mục có bài viết không
        if ($category->posts()->exists()) {
            return redirect()->back()->with('error', 'Không thể xóa danh mục đang chứa bài viết!');
        }

        if($category->children()->exists()) {
            return redirect()->back()->with('error', 'Không thể xóa danh mục vì co có con!');
        }

        $category->delete();
        return redirect()->back()->with('success', 'Đã xóa danh mục.');
    }
}
