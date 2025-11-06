<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use App\Models\Medicine;
use App\Models\Goods;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Lấy danh sách reviews của sản phẩm
     */
    public function index($type, $id)
    {
        $reviews = ProductReview::where('product_id', $id)
            ->where('product_type', $type)
            ->approved()
            ->with('user:id,name') //lấy thông tin user đã đánh giá
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'reviews' => $reviews,
            'average_rating' => $reviews->avg('rating'),
            'total_count' => $reviews->count()
        ]);
    }

    /**
     * Tạo review mới
     */
    public function store(Request $request)
    {
        // Kiểm tra người dùng đã đăng nhập chưa 
        //chỉ áp dụng cho user có tài khoản , nếu chưa có sẽ bắt đăng nhập
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để đánh giá!'
            ], 401);
        }

        // Validate
        $request->validate([
            'product_id' => 'required|integer',
            'product_type' => 'required|string|in:medicine,goods',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|min:10|max:500'
        ]);

        // Kiểm tra đã review chưa (1 user chỉ review 1 lần)
        $existingReview = ProductReview::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->where('product_type', $request->product_type)
            ->first();

        if ($existingReview) { //nếu user đã bình luận rồi thì không được bình luận lại
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã đánh giá sản phẩm này rồi!'
            ], 422);
        }

        // Tạo review
        $review = ProductReview::create([
            'user_id' => auth()->id(),
            'product_id' => $request->product_id,
            'product_type' => $request->product_type,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'status' => 'approved' // Tự động approve
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đánh giá của bạn đã được gửi!',
            'review' => $review->load('user:id,name')
        ]);
    }

    /**
     * Xóa review (chỉ owner)
     */
    public function destroy($id)
    {
        $review = ProductReview::findOrFail($id);

        // Kiểm tra quyền
        if ($review->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa đánh giá này!'
            ], 403);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa đánh giá!'
        ]);
    }
}

