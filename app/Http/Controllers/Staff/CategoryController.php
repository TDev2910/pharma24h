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
    public function index()
    {
        $categories = Category::withCount('posts')
            ->latest()
            ->paginate(10);

        return Inertia::render('Staff/Categories/Index', [
            'categories' => $categories
        ]);
    }

    public function store(StoreCategoryRequest $request)
    {
        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Thêm danh mục mới thành công');
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Cập nhật danh mục thành công');
    }
    
    public function destroy(Category $category)
    {
        //Kiểm tra danh mục có bài viết không
        if ($category->posts()->exists()) {
            return redirect()->back()->with('error', 'Không thể xóa danh mục đang chứa bài viết!');
        }

        $category->delete();
        return redirect()->back()->with('success', 'Đã xóa danh mục.');
    }
}
