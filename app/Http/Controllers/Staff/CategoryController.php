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
        $categories = Category::withCount('posts')
            ->with('parent')
            ->when($request->search, function($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);
        
        $parentCategories = Category::where('parent_id')
            ->select('id', 'name')
            ->get();

        return Inertia::render('Staff/Categories/Index', [
            'categories' => $categories,
            'parentCategories' => $parentCategories,
            'baseUrl' => route('staff.categories.index'), 
            'filters' => $request->only('search'),
        ]);
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
            return redirect()->back()->with('error', 'Không thể xóa danh mục vì có danh mục con!');
        }

        $category->delete();
        return redirect()->back()->with('success', 'Đã xóa danh mục.');
    }
}
