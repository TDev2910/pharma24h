<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicine;
use App\Models\Goods;
use Inertia\Inertia;

class HomeController extends Controller
{

    /**
     * Trang chủ dùng Inertia + Vue (SPA)
     */
    public function homeInertia()
    {
        $medicines = Medicine::with(['category', 'manufacturer'])
            ->where('ban_truc_tiep', true)
            ->latest()
            ->limit(4)
            ->get();

        $goods = Goods::with(['category', 'manufacturer'])
            ->where('ban_truc_tiep', true)
            ->latest()
            ->limit(4)
            ->get();

        // Debug auth
        $user = auth()->user();
        \Log::info('HomeController - User authenticated:', ['user' => $user ? $user->toArray() : null]);

        return Inertia::render('Public/Home', [
            'medicines' => $medicines,
            'goods' => $goods,
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ] : null,
            ],
        ]);
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
    public function products() //trang sản phẩm
    {
        // Lấy tất cả thuốc và hàng hóa để hiển thị
        // $medicines = Medicine::with(['category', 'manufacturer'])
        //     ->where('ban_truc_tiep', true)
        //     ->latest()
        //     ->get();
            
        // $goods = Goods::with(['category', 'manufacturer'])
        //     ->where('ban_truc_tiep', true)
        //     ->latest()
        //     ->get();
            
        // // Merge tất cả sản phẩm
        // $allProducts = $medicines->merge($goods)->sortByDesc('created_at');
        
        return view('public.products');
    }

    public function services() //trang dịch vụ
    {
        return Inertia::render('public/services');
    }

    // /**
    //  * Hiển thị trang giới thiệu
    //  */
    // public function about()
    // {
    //     return view('public.about');
    // }

    // /**
    //  * Hiển thị trang liên hệ
    //  */
    // public function contact()
    // {
    //     return view('public.contact');
    // }
}
