<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicine;
use App\Models\Goods;
use Inertia\Inertia;
use App\Models\ProductReview;

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
    public function productDetail($type, $id)
    {
        if($type === 'medicine') {
            $product = Medicine::with(['category', 'manufacturer', 'drugRoute', 'position'])->findOrFail($id);
        } else {
            $product = Goods::with(['category', 'manufacturer', 'position'])->findOrFail($id);
        }
        
        $user = auth()->user();
        
        //lấy thông tin tất cả review của sản phẩm
        $reviews = ProductReview::where('product_id', $id)
            ->where('product_type', $type)
            ->approved()
            ->with('user:id,name')
            ->latest()
            ->get();
       
        $reviewCount = $reviews->count();
        $averageRating = $reviewCount > 0 ? $reviews->avg('rating') : 0;

        // Tính rating breakdown (số lượng review cho mỗi mức sao)
        $ratingBreakdown = [
            5 => $reviews->where('rating', 5)->count(),
            4 => $reviews->where('rating', 4)->count(),
            3 => $reviews->where('rating', 3)->count(),
            2 => $reviews->where('rating', 2)->count(),
            1 => $reviews->where('rating', 1)->count(),
        ];

        return Inertia::render('Public/DetailsProduct', [
            'product' => $product,
            'type' => $type,
            'reviews' => $reviews,
            'averageRating' => round($averageRating, 1),
            'reviewCount' => $reviewCount,
            'ratingBreakdown' => $ratingBreakdown,
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
