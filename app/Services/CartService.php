<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Medicine;
use App\Models\Goods;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartService
{
    //tạo session_id cho khách chưa đăng nhập / khách vãng lai
    protected function getSessionId()
    {
        if (!session()->has('cart_session_id')) {
            session(['cart_session_id' => Str::random(40)]);
        }
        return session('cart_session_id');
    }
    
    // Thêm sản phẩm vào giỏ hàng
    public function addToCart($itemId, $itemType, $quantity = 1, $isPromotion = false)
    {
        // Xác định loại sản phẩm và lấy thông tin
        if ($itemType === 'medicine') {
            $item = Medicine::find($itemId);
            $itemName = $item->ten_thuoc ?? '';
        } elseif ($itemType === 'goods') {
            $item = Goods::find($itemId);
            $itemName = $item->ten_hang_hoa ?? '';
        } else {
            return ['success' => false, 'message' => 'Loại sản phẩm không hợp lệ'];
        }

        if (!$item) {
            return ['success' => false, 'message' => 'Sản phẩm không tồn tại'];
        }

        // Xác định giỏ hàng hiện tại
        $userId = Auth::id();
        $sessionId = $userId ? null : $this->getSessionId();

        // Tìm cart item cùng loại KM/không KM
        $cartItem = Cart::where([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'item_id' => $itemId,
            'item_type' => $itemType,
            'is_promotion' => $isPromotion,
        ])->first();

        // Tính tổng số lượng sau khi cộng dồn
        $currentQty = $cartItem ? (int)$cartItem->quantity : 0;
        $requested = $currentQty + (int)$quantity;

        if ($isPromotion) {
            // Kiểm tra tồn KM và tồn kho tổng theo cộng dồn
            if ($requested > ($item->ton_khuyen_mai ?? 0)) {
                $remain = max(0, ($item->ton_khuyen_mai ?? 0) - $currentQty);
                return ['success' => false, 'message' => "Chỉ còn $remain suất khuyến mãi."];
            }
            if ($requested > ($item->ton_kho ?? 0)) {
                $remain = max(0, ($item->ton_kho ?? 0) - $currentQty);
                return ['success' => false, 'message' => "Không đủ tồn kho. Chỉ thêm được $remain."];
            }
            $price = ($item->gia_khuyen_mai ?? 0) > 0 ? $item->gia_khuyen_mai : $item->gia_ban;
        } else {
            // Kiểm tra tồn tổng theo cộng dồn
            if ($requested > ($item->ton_kho ?? 0)) {
                $remain = max(0, ($item->ton_kho ?? 0) - $currentQty);
                return ['success' => false, 'message' => "Không đủ tồn kho. Chỉ thêm được $remain."];
            }
            $price = $item->gia_ban;
        }

        if ($cartItem) {
            $cartItem->quantity = $requested;
            $cartItem->save();
        } else {
            Cart::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'item_id' => $itemId,
                'item_type' => $itemType,
                'quantity' => (int)$quantity,
                'is_promotion' => $isPromotion,
                'price' => $price,
                'name' => $itemName,
                'image' => $item->image
            ]);
        }

        $cartCount = $this->getCartCount();
        $cartTotal = $this->getCartTotal();

        return [
            'success' => true,
            'message' => 'Đã thêm vào giỏ hàng',
            'cart_count' => $cartCount,
            'cart_total' => $cartTotal
        ];
    }
    
    // Lấy số lượng sản phẩm trong giỏ
    public function getCartCount()
    {
        $userId = Auth::id();
        $sessionId = $userId ? null : session('cart_session_id');
        
        if (!$userId && !$sessionId) {
            return 0;
        }
        
        return Cart::where(function($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId); //nếu đã đăng nhập thì lọc theo user_id  
            } else {
                $query->where('session_id', $sessionId); //nếu chưa đăng nhập thì lọc theo session_id
            }
        })->sum('quantity'); //tính tổng quantity
    }
    
    // Tính tổng tiền giỏ hàng
    public function getCartTotal()
    {
        $userId = Auth::id();
        $sessionId = $userId ? null : session('cart_session_id');
        
        if (!$userId && !$sessionId) {
            return 0;
        }
        
        $cartItems = Cart::where(function($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
        })->get();
        
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item->price * $item->quantity;
        }
        
        return $total;
    }
    
    // Lấy thông tin tóm tắt giỏ hàng
    public function getCartSummary($limit = 3)
    {
        $userId = Auth::id();
        $sessionId = $userId ? null : session('cart_session_id');
        
        if (!$userId && !$sessionId) {
            return [
                'items' => [],
                'preview_items' => [],
                'count' => 0,
                'total' => 0,
                'has_more' => false
            ];
        }
        
        $cartItems = Cart::where(function($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
        })
        ->latest()
        ->get();
        
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item->quantity * $item->price;
        }
        
        $previewItems = $cartItems->take($limit);
        
        return [
            'items' => $cartItems,
            'preview_items' => $previewItems,
            'count' => $cartItems->sum('quantity'),
            'total' => $total,
            'has_more' => $cartItems->count() > $limit
        ];
    }
    
    // Cập nhật số lượng sản phẩm
    public function updateQuantity($cartId, $quantity)
    {
        $cart = Cart::find($cartId);

        // Kiểm tra quyền truy cập
        $userId = Auth::id();
        $sessionId = $userId ? null : session('cart_session_id');

        if (!$cart || ($cart->user_id != $userId && $cart->session_id != $sessionId)) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm trong giỏ hàng'
            ];
        }

        // Lấy sản phẩm tương ứng
        if ($cart->item_type === 'medicine') {
            $p = Medicine::find($cart->item_id);
        } else {
            $p = Goods::find($cart->item_id);
        }

        if (!$p) {
            return ['success' => false, 'message' => 'Sản phẩm không tồn tại'];
        }

        $qty = (int)$quantity;

        if ($cart->is_promotion) {
            if ($qty > ($p->ton_khuyen_mai ?? 0)) {
                return ['success' => false, 'message' => 'Vượt quá số lượng khuyến mãi hiện còn.'];
            }
            if ($qty > ($p->ton_kho ?? 0)) {
                return ['success' => false, 'message' => 'Không đủ tồn kho tổng.'];
            }
        } else {
            if ($qty > ($p->ton_kho ?? 0)) {
                return ['success' => false, 'message' => 'Không đủ tồn kho.'];
            }
        }

        $cart->quantity = $qty;
        $cart->save();

        return [
            'success' => true,
            'message' => 'Cập nhật số lượng thành công',
            'cart' => $this->getCartSummary()
        ];
    }
    
    // Xóa sản phẩm khỏi giỏ
    public function removeItem($cartId)
    {
        $cart = Cart::find($cartId);
        
        // Kiểm tra quyền truy cập
        $userId = Auth::id();
        $sessionId = $userId ? null : session('cart_session_id');
        
        if (!$cart || ($cart->user_id != $userId && $cart->session_id != $sessionId)) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm trong giỏ hàng'
            ];
        }
        
        $cart->delete();
        
        return [
            'success' => true,
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng',
            'cart' => $this->getCartSummary()
        ];
    }
    
    // Chuyển giỏ hàng từ session sang user khi đăng nhập
        public function mergeCartAfterLogin($userId)
        {
            $sessionId = session('cart_session_id');
            if (!$sessionId) return;
            
            // Lấy tất cả item trong giỏ hàng session
            $sessionCartItems = Cart::where('session_id', $sessionId)->get();
            
            foreach ($sessionCartItems as $item) {
                // Kiểm tra xem đã có item này trong giỏ của user chưa
                $existingItem = Cart::where([
                    'user_id' => $userId,
                    'item_id' => $item->item_id,
                    'item_type' => $item->item_type
                ])->first();
                
                if ($existingItem) {
                    // Đã có, cộng số lượng
                    $existingItem->quantity += $item->quantity;
                    $existingItem->save();
                    $item->delete();
                } else {
                    // Chưa có, chuyển từ session sang user
                    $item->user_id = $userId;
                    $item->session_id = null;
                    $item->save();
                }
            }
            
            // Xóa session_id
            session()->forget('cart_session_id');
        }
}