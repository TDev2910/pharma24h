<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicine;
use App\Models\Goods;

class HomeController extends Controller
{
    /**
     * Hiển thị trang chủ công cộng
     */
    public function index()
    {
        // Lấy 4 thuốc mới nhất cho hàng đầu tiên mặc định
        $medicines = Medicine::with(['category', 'manufacturer'])
            ->where('ban_truc_tiep', true)
            ->latest()
            ->limit(4) //giới hạn 4 sản phẩm
            ->get();
            
        // Lấy 4 hàng hóa mới nhất cho hàng thứ hai mặc định 
        $goods = Goods::with(['category', 'manufacturer'])
            ->where('ban_truc_tiep', true)
            ->latest()
            ->limit(4)
            ->get();
        
        return view('home', compact('medicines', 'goods'));
    }

    /**
     * Hiển thị trang cơ sở khám bệnh
     */
    public function cosokhambenh()
    {
        return view('public.cosokhambenh');
    }

    /**
     * Hiển thị trang sản phẩm công cộng - Xem tất cả
     */
    public function products()
    {
        // Lấy tất cả thuốc và hàng hóa để hiển thị
        $medicines = Medicine::with(['category', 'manufacturer'])
            ->where('ban_truc_tiep', true)
            ->latest()
            ->get();
            
        $goods = Goods::with(['category', 'manufacturer'])
            ->where('ban_truc_tiep', true)
            ->latest()
            ->get();
            
        // Merge tất cả sản phẩm
        $allProducts = $medicines->merge($goods)->sortByDesc('created_at');
        
        return view('public.products', compact('allProducts', 'medicines', 'goods'));
    }

    /**
     * Hiển thị trang giới thiệu
     */
    public function about()
    {
        return view('public.about');
    }

    /**
     * Hiển thị trang liên hệ
     */
    public function contact()
    {
        return view('public.contact');
    }
}
