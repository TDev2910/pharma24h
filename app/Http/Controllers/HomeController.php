<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicine;
use App\Models\Goods;
use App\Models\Service;
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
        //lấy sản phẩm thuộc loại danh mục thuốc
        $medicines = Medicine::with(['category', 'manufacturer'])
            ->where('ban_truc_tiep', true)
            ->latest()
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->ten_thuoc,
                    'gia_ban_formatted' => $item->gia_ban ? number_format($item->gia_ban, 0, ',', '.') . ' đ/' . ($item->don_vi_tinh ?? ''): '',
                    'unit'  => $item->don_vi_tinh,
                    'image' => $item->image ? asset('storage/' . $item->image) : null,
                    'type'  => 'medicine',
                    'created_at' => $item->created_at
                ];
            });

        //lấy sản phẩm thuộc loại danh mục vật tư y tế
        $goods = Goods::with(['category', 'manufacturer'])
            ->where('ban_truc_tiep', true)
            ->latest()
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->ten_hang_hoa,
                    'gia_ban_formatted' => $item->gia_ban ? number_format($item->gia_ban, 0, ',', '.') . ' đ/' . ($item->don_vi_tinh ?? ''): '',
                    'unit'  => $item->don_vi_tinh,
                    'image' => $item->image ? asset('storage/' . $item->image) : null,
                    'type'  => 'goods',
                    'created_at' => $item->created_at
                ];
            });

        $allProducts = $medicines->merge($goods)
            ->sortByDesc('created_at')
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

    /**
     * Hiển thị trang dịch vụ công cộng
     */
    public function services(Request $request)
    {
        $query = Service::with(['category'])
            ->where('trang_thai', 'kich_hoat'); // Chỉ hiển thị dịch vụ đang kích hoạt

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ten_dich_vu', 'LIKE', "%{$search}%")
                ->orWhere('ma_dich_vu', 'LIKE', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('nhom_hang_id', $request->category_id);
        }

        $services = $query->latest()->get();

        $user = auth()->user();

        return Inertia::render('Public/Services', [
            'services' => $services,
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
     * Hiển thị chi tiết dịch vụ
     */
    public function serviceDetail($id)
    {
        $service = Service::with(['category'])
            ->where('trang_thai', 'kich_hoat')
            ->findOrFail($id);

        $user = auth()->user();

        return Inertia::render('Public/DetailsServices', [
            'service' => $service,
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
