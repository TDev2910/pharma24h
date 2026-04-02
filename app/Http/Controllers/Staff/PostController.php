<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Traits\HasTreeStructure;
use App\Models\Post;
use App\Models\Content\Category;
use App\Models\Content\PostImage;
use App\Http\Requests\Staff\Post\StorePostRequest;
use App\Http\Requests\Staff\Post\UpdatePostRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PostController extends Controller
{
    use HasTreeStructure;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $posts = Post::with(['category', 'author', 'images']) 
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->latest() 
            ->paginate(10) 
            ->withQueryString(); 

        $categories = Category::select('id', 'name', 'parent_id')->get();
        $categoryTree = $this->buildTreeNodes($categories);

        return Inertia::render('Staff/Posts/Index', [
            'posts' => $posts,
            'categories' => $categories,
            'categoryTree' => $categoryTree,
            'baseUrl' => '/staff/posts',
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']) . '-' . time();
        $data['user_id'] = $request->user()->id;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('posts/thumbnails', 'public');
        }

        $post = Post::create($data);

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $path = $file->store('posts/gallery', 'public');
                $post->images()->create(['path' => $path]);
            }
        }

        return redirect()->back()->with('success', 'Đăng bài viết thành công!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, string $id)
    {
        $post = Post::findOrFail($id);
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']) . '-' . time();
        $data['user_id'] = $request->user()->id;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('posts/thumbnails', 'public');
        }

        $post->update($data);

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $path = $file->store('posts/gallery', 'public');
                $post->images()->create(['path' => $path]);
            }
        }

        return redirect()->back()->with('success', 'Cập nhật bài viết thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::findOrFail($id);
        $post->delete();
        return redirect()->back()->with('success', 'Xóa bài viết thành công!');
    }
}
