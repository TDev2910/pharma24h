<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Medicine;
use App\Models\Goods;
use App\Models\Service;
use App\Models\Content\Category;
use App\Models\Post;
use Inertia\Inertia;
use Illuminate\Support\Str;
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
        return Inertia::render('Public/Cosokhambenh');
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
                'gia_ban' => $item->gia_ban ?? 0,
                'gia_khuyen_mai' => $item->gia_khuyen_mai ?? 0,
                'ton_kho' => $item->ton_kho ?? 0,
                'ton_khuyen_mai' => $item->ton_khuyen_mai ?? 0,
                'gia_ban_formatted' => $item->gia_ban ? number_format($item->gia_ban, 0, ',', '.') . ' đ/' . ($item->don_vi_tinh ?? '') : '',
                'unit'  => $item->don_vi_tinh,
                'don_vi_tinh' => $item->don_vi_tinh,
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
                'gia_ban' => $item->gia_ban ?? 0,
                'gia_khuyen_mai' => $item->gia_khuyen_mai ?? 0,
                'ton_kho' => $item->ton_kho ?? 0,
                'ton_khuyen_mai' => $item->ton_khuyen_mai ?? 0,
                'gia_ban_formatted' => $item->gia_ban ? number_format($item->gia_ban, 0, ',', '.') . ' đ/' . ($item->don_vi_tinh ?? '') : '',
                'unit'  => $item->don_vi_tinh,
                'don_vi_tinh' => $item->don_vi_tinh,
                'image' => $item->image ? asset('storage/' . $item->image) : null,
                'type'  => 'goods',
                'created_at' => $item->created_at
            ];
        });

        $allProducts = $medicines->merge($goods)
            ->sortByDesc('created_at')
            ->values();

        return Inertia::render('Public/Product/Index', [
            'products' => $allProducts
        ]);
    }
    public function productDetail($type, $id)
    {
        if ($type === 'medicine') {
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

        return Inertia::render('Public/Product/Show', [
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

        return Inertia::render('Public/Service/Index', [
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
        $service = Service::with(['category','doctor'])
            ->where('trang_thai', 'kich_hoat')
            ->findOrFail($id);

        $user = auth()->user();

        return Inertia::render('Public/Service/Show', [
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

    public function posts(Request $request)
    {
        //lấy danh sách category
        $categories = Category::has('posts')->withCount('posts')->get();

        //lấy danh sách bài viết
        $query = Post::with('category')->where('is_published', true)->latest();

        if ($request->has('category')) {
            $slug = $request->category;
            $query->whereHas('category', function($q) use ($slug) {
                $q->where('slug', $slug);
            });
        }

        $allPosts = $query->take(6)->get(); // Chỉ cần lấy 6 bài cho phần Featured

        //format post
        $formatPost = function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'summary' => Str::limit($post->summary, 120),
                'category' => $post->category->name ?? 'Tin tức',
                'categorySlug' => $post->category->slug ?? '',
                'image' => $post->thumbnail 
                    ? (str_starts_with($post->thumbnail, 'http') ? $post->thumbnail : asset('storage/' . $post->thumbnail))
                    : 'https://via.placeholder.com/800x600.png?text=No+Image',
                'slug' => $post->slug,
                'date' => $post->created_at ? $post->created_at->format('d/m/Y') : '',
                'views' => rand(100, 2000), 
            ];
        };
        
        $featuredPosts = $allPosts->map($formatPost)->values(); // 6 bài nổi bật

        // Lấy tất cả danh mục có bài viết (đã xuất bản) để hiển thị Section
        // Thay vì hardcode slug, ta query lấy hết các Category
        $activeCategories = Category::whereHas('posts', function($q) {
            $q->where('is_published', true);
        })->get();

        $categorySections = [];

        foreach ($activeCategories as $cat) {
            $posts = Post::with('category')
                ->where('category_id', $cat->id)
                ->where('is_published', true)
                ->latest()
                ->take(5) // Lấy 5 bài cho bố cục (1 to, 1 vừa, 3 nhỏ)
                ->get()
                ->map($formatPost)
                ->values();

            if ($posts->count() > 0) {
                $categorySections[] = [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'slug' => $cat->slug,
                    'posts' => $posts
                ];
            }
        }

        $user = auth()->user();

        return Inertia::render('Public/Posts', [
            'categories' => $categories,
            'featuredPosts' => $featuredPosts,
            'categorySections' => $categorySections,
            'filters' => $request->only(['category']),
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
        return Inertia::render('Public/Contact');
    }
}

