<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;
    
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    
    /**
     * Thêm sản phẩm vào giỏ hàng
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function addToCart(Request $request)
    {
        try {
            // Kiểm tra nếu là request AJAX
            $isAjax = $request->ajax() || $request->wantsJson();
            
            // Validate đầu vào
            try {
                $validated = $request->validate([
                    'item_id' => 'required|integer',
                    'item_type' => 'required|in:medicine,goods',
                    'quantity' => 'integer|min:1|nullable'
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                if ($isAjax) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Dữ liệu không hợp lệ',
                        'errors' => $e->errors()
                    ], 422);
                }
                throw $e; // Rethrow nếu không phải AJAX
            }
            
            $quantity = $validated['quantity'] ?? 1;
            
            // Thêm vào giỏ hàng
            $result = $this->cartService->addToCart(
                $validated['item_id'],
                $validated['item_type'],
                $quantity
            );
            
            if ($isAjax) {
                return response()->json($result);
            }
            
            if ($result['success']) {
                return back()->with('success', $result['message']);
            } else {
                return back()->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            // Xử lý ngoại lệ chung
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi thêm vào giỏ hàng',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Có lỗi xảy ra khi thêm vào giỏ hàng');
        }
    }
    
    /**
     * Lấy thông tin giỏ hàng
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getCart(Request $request)
    {
        try {
            $cartData = $this->cartService->getCartSummary();
            
            if ($request->ajax() || $request->wantsJson() || $request->query('format') === 'json') {
                return response()->json($cartData);
            }
            
            return view('store.cart.index', compact('cartData'));
        } catch (\Exception $e) {
            
            if ($request->ajax() || $request->wantsJson() || $request->query('format') === 'json') {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi lấy thông tin giỏ hàng',
                    'count' => 0,
                    'total' => 0,
                    'items' => [],
                    'preview_items' => []
                ]);
            }
            
            return view('store.cart.index', ['cartData' => [
                'items' => [],
                'preview_items' => [],
                'count' => 0,
                'total' => 0,
                'has_more' => false
            ]]);
        }
    }
    
    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateQuantity(Request $request)
    {
        try {
            $validated = $request->validate([
                'cart_id' => 'required|integer',
                'quantity' => 'required|integer|min:1'
            ]);
            
            $result = $this->cartService->updateQuantity(
                $validated['cart_id'],
                $validated['quantity']
            );
            
            return response()->json($result);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật số lượng'
            ], 500);
        }
    }
    
    /**
     * Xóa sản phẩm khỏi giỏ hàng
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function removeItem(Request $request)
    {
        try {
            $validated = $request->validate([
                'cart_id' => 'required|integer'
            ]);
            
            $result = $this->cartService->removeItem($validated['cart_id']);
            
            return response()->json($result);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa sản phẩm khỏi giỏ hàng'
            ], 500);
        }
    }
}
