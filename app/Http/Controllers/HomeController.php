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

        $user = auth()->user();

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
    public function products()
    {
        $medicines = Medicine::with(['category', 'manufacturer'])
            ->where('ban_truc_tiep', true)
            ->latest()
            ->get();

        $goods = Goods::with(['category', 'manufacturer'])
            ->where('ban_truc_tiep', true)
            ->latest()
            ->get();

        $allProducts = $medicines->merge($goods)
            ->sortByDesc('created_at')
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->ten_thuoc ?? $item->ten_hang_hoa, // medicines dùng ten_thuoc, goods dùng ten_hang_hoa
                    'gia_ban_formatted' => $item->gia_ban ? number_format($item->gia_ban, 0, ',', '.') . ' đ/' . ($item->don_vi_tinh ?? ''): '',
                    'unit'  => $item->don_vi_tinh, // đơn vị tính
                    'image' => $item->image ? asset('storage/' . $item->image) : null,       // cột image trong DB
                    'type'  => isset($item->ten_thuoc) ? 'medicine' : 'goods'
            ];
        })
        ->values();

        return Inertia::render('Public/Products', [
            'products' => $allProducts
        ]);
    }

    public function services() //trang dịch vụ
    {
        return Inertia::render('public/services');
    }

    public function contact()
    {
        return Inertia::render('public/contact');
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
