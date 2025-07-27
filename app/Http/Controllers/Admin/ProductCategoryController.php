<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCategory;


class ProductCategoryController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::all();
        $parents = ProductCategory::all(); 
        return view('admin.products.DanhSachHangHoa.index', compact('categories', 'parents'));
    }

    // Hiển thị form tạo nhóm hàng mới
    public function create()
    {               
        $parents = ProductCategory::all();  
        return view('admin.categories.create', compact('parents'));
    }

    // Lưu nhóm hàng mới
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'parent_id' => 'nullable|exists:product_categories,id'
        ]);
        \App\Models\ProductCategory::create($request->only('name', 'parent_id'));   

        // Chuyển hướng về trang danh sách sản phẩm
        return redirect()->route('admin.products.index')->with('success', 'Tạo nhóm hàng thành công!');
    }
}
