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
     * Trang chủ 
     */
    public function homeInertia(Request $request)
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
        $posts = Post::with('category')
            ->where('is_published', true)
            ->latest()
            ->limit(8)
            ->get()
            ->map(function ($post) {
                // Format lại dữ liệu 
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'summary' => Str::limit($post->summary, 160),
                    'image' => $post->thumbnail 
                        ? (str_starts_with($post->thumbnail, 'http') 
                        ? $post->thumbnail 
                        : asset('storage/' . $post->thumbnail))
                        : 'https://via.placeholder.com/800x600.png?text=No+Image',
                    'date' => $post->created_at ? $post->created_at->format('d/m/Y') : '',
                ];
            });
        $user = $request->user();

        return Inertia::render('Public/Home', [
            'medicines' => $medicines,
            'goods' => $goods,
            'posts' => $posts,
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

    public function medicalTeam()
    {
        return Inertia::render('Public/MedicalTeam');
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
    public function productDetail(Request $request, $type, $id)
    {
        if ($type === 'medicine') {
            $product = Medicine::with(['category', 'manufacturer', 'drugRoute', 'position'])->findOrFail($id);
        } else {
            $product = Goods::with(['category', 'manufacturer', 'position'])->findOrFail($id);
        }

        $user = $request->user();

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
            ->where('trang_thai', 'kich_hoat'); 

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

        $user = $request->user();

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
    public function serviceDetail(Request $request, $id)
    {
        $service = Service::with(['category','doctor'])
            ->where('trang_thai', 'kich_hoat')
            ->findOrFail($id);

        $user = $request->user();

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

        //lấy danh sách bài viết được đăng công khai
        $query = Post::with('category')->where('is_published', true)->latest();

        if ($request->has('category')) {
            $slug = $request->category;
            $filterCat = Category::where('slug', $slug)->first();
            if ($filterCat) {
                $catIds = $this->getDescendantIds($filterCat);
                $query->whereIn('category_id', $catIds);
            }
        }

        // format post helper
        $formatPost = function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'summary' => Str::limit($post->summary, 160),
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

        $allPosts = $query->take(6)->get(); // lấy 6 bài cho phần Featured
        $featuredPosts = $allPosts->map($formatPost)->values(); 

        // Lấy Section theo Danh mục gốc (Inheritance)
        $rootCategories = Category::whereNull('parent_id')
            ->where(function($q) {
                $q->whereHas('posts', function($pq) {
                    $pq->where('is_published', true);
                })->orWhereHas('children.posts', function($pq) {
                    $pq->where('is_published', true);
                });
            })
            ->with(['children' => function($q) {
                $q->whereHas('posts', function($pq) {
                    $pq->where('is_published', true);
                });
            }])->get();

        $categorySections = [];

        foreach ($rootCategories as $root) {
            $catIds = $this->getDescendantIds($root);

            $posts = Post::with('category')
                ->whereIn('category_id', $catIds)
                ->where('is_published', true)
                ->latest()
                ->take(5) 
                ->get()
                ->map($formatPost)
                ->values();

            if ($posts->count() > 0) {
                $categorySections[] = [
                    'id' => $root->id,
                    'name' => $root->name,
                    'slug' => $root->slug,
                    'subcategories' => $root->children->map(fn($c) => [
                        'name' => $c->name,
                        'slug' => $c->slug
                    ]),
                    'posts' => $posts
                ];
            }
        }

        $user = $request->user();

        return Inertia::render('Public/Posts/Index', [
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

    public function DetailsPost(Request $request, $slug)
    {
        $post = Post::with(['category.parent', 'author'])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail(); //nếu ko có báo lỗi 

        $postData = [
            'id' => $post->id,
            'title' => $post->title,
            'content' => $post->content, 
            'summary' => $post->summary,
            'image' => $post->thumbnail 
                ? (str_starts_with($post->thumbnail, 'http') ? $post->thumbnail : asset('storage/' . $post->thumbnail))
                : 'https://via.placeholder.com/1200x600.png?text=No+Image',
            'category' => $post->category->name ?? 'Tin tức',
            'categorySlug' => $post->category->slug ?? '',
            'author' => $post->author ? $post->author->name : 'Admin',
            'date' => $post->created_at ? $post->created_at->format('d/m/Y') : '',
            'views' => rand(100, 5000), 
        ];

        // Tìm root category để lấy bài liên quan từ cả "nhánh"
        $rootCategory = $post->category;
        while ($rootCategory && $rootCategory->parent_id) {
            $rootCategory = $rootCategory->parent;
            // Load children if not loaded to help getDescendantIds
            if (!$rootCategory->relationLoaded('children')) {
                $rootCategory->load('children');
            }
        }

        $relatedPostQuery = Post::where('is_published', true)
            ->where('id', '!=', $post->id);

        if ($rootCategory) {
            $relatedCatIds = $this->getDescendantIds($rootCategory);
            $relatedPostQuery->whereIn('category_id', $relatedCatIds);
        } else {
            $relatedPostQuery->where('category_id', $post->category_id);
        }

        $relatedPosts = $relatedPostQuery->take(4)
            ->latest()
            ->get()
            ->map(function($p) {
                 return [
                    'id' => $p->id,
                    'title' => $p->title,
                    'slug' => $p->slug,
                    'image' => $p->thumbnail 
                        ? (str_starts_with($p->thumbnail, 'http') ? $p->thumbnail : asset('storage/' . $p->thumbnail))
                        : 'https://via.placeholder.com/800x600.png?text=No+Image',
                    'date' => $p->created_at ? $p->created_at->format('d/m/Y') : '',
                 ];
            });

        $user = $request->user();

        return Inertia::render('Public/Posts/Show', [
            'post' => $postData,
            'relatedPosts' => $relatedPosts,
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

    private function getDescendantIds($category)
    {
        $ids = [$category->id];
        // Ensure children are loaded
        if (!$category->relationLoaded('children')) {
            $category->load('children');
        }
        foreach ($category->children as $child) {
            $ids = array_merge($ids, $this->getDescendantIds($child));
        }
        return array_unique($ids);
    }
}

