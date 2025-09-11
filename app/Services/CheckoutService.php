<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class CheckoutService
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function createOrder(array $orderData): Order
    {
        return DB::transaction(function () use ($orderData) {
            // Xác định user_id và session_id
            $userId = Auth::id();
            $sessionId = $userId ? null : session('cart_session_id');
            
            // Lấy thông tin giỏ hàng
            $cartSummary = $this->cartService->getCartSummary();
            $cartItems = $cartSummary['items'];
            $total = $cartSummary['total'];
            
            if ($cartItems->isEmpty()) {
                throw new \Exception('Giỏ hàng trống');
            }
            
            // Tạo đơn hàng
            $order = Order::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'customer_name' => $orderData['customer_name'],
                'customer_email' => $orderData['customer_email'] ?? null,
                'customer_phone' => $orderData['customer_phone'],
                'delivery_method' => $orderData['delivery_method'],
                'shipping_address' => $orderData['delivery_method'] === 'shipping' ? $orderData['shipping_address'] : null,
                'province' => $orderData['delivery_method'] === 'shipping' ? $orderData['province'] : null,
                'district' => $orderData['delivery_method'] === 'shipping' ? $orderData['district'] : null,
                'ward' => $orderData['delivery_method'] === 'shipping' ? $orderData['ward'] : null,
                'pickup_location' => $orderData['delivery_method'] === 'pickup' ? $orderData['pickup_location'] : null,
                'total_amount' => $total,
                'payment_method' => $orderData['payment_method'],
                'payment_status' => 'pending',
                'order_status' => 'new',
                'note' => $orderData['note'] ?? null,
            ]);

            // Thêm các sản phẩm từ giỏ hàng vào chi tiết đơn hàng
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $item->item_id,
                    'item_type' => $item->item_type,
                    'product_name' => $item->name,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->price * $item->quantity,
                ]);
            }

            // Xóa giỏ hàng
            Cart::where(function($query) use ($userId, $sessionId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })->delete();
            
            // Nếu là session_id, xóa session để tránh đặt hàng lại
            if ($sessionId) {
                session()->forget('cart_session_id');
            }

            return $order;
        });
    }

    public function generateVnpayPaymentUrl(Order $order): string
    {
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_ReturnUrl = route('checkout.vnpay.return');
        $vnp_TmnCode = config('services.vnpay.tmn_code');
        $vnp_HashSecret = config('services.vnpay.hash_secret');

        $vnp_TxnRef = $order->id . '_' . time();
        $vnp_OrderInfo = "Thanh toán đơn hàng #" . $order->id;
        // Với sandbox vpcpay.html nên dùng loại "other" để tương thích
        $vnp_OrderType = 'other';
        $vnp_Amount = (int) ($order->total_amount * 100);
        $vnp_Locale = 'vn';
        $vnp_IpAddr = request()->ip();
        // Dùng múi giờ Việt Nam để tránh lỗi hết hạn (code=15)
        $vnp_CreateDate = now('Asia/Ho_Chi_Minh')->format('YmdHis');
        $vnp_ExpireDate = now('Asia/Ho_Chi_Minh')->addMinutes(15)->format('YmdHis'); // hết hạn sau 15 phút

        $inputData = [
            'vnp_Version'   => '2.1.0',
            'vnp_TmnCode'   => $vnp_TmnCode,
            'vnp_Command'   => 'pay',
            'vnp_Amount'    => $vnp_Amount,
            'vnp_CurrCode'  => 'VND',
            'vnp_TxnRef'    => $vnp_TxnRef,
            'vnp_OrderInfo' => $vnp_OrderInfo,
            'vnp_OrderType' => $vnp_OrderType,
            'vnp_Locale'    => $vnp_Locale,
            'vnp_ReturnUrl' => $vnp_ReturnUrl,
            'vnp_CreateDate'=> $vnp_CreateDate,
            'vnp_ExpireDate'=> $vnp_ExpireDate,
            'vnp_IpAddr'    => $vnp_IpAddr,
        ];
        $filtered = [];
        foreach ($inputData as $k => $v) {
            if ($v !== null && $v !== '') {
                $filtered[$k] = $v;
            }
        }
        ksort($filtered);

        // Chuỗi để ký: urlencode(key)=urlencode(value) nối bằng &
        $pairs = [];
        foreach ($filtered as $k => $v) {
            $pairs[] = urlencode($k) . '=' . urlencode((string) $v);
        }
        $rawToSign = implode('&', $pairs);
        $vnpSecureHash = hash_hmac('sha512', $rawToSign, $vnp_HashSecret);

        // URL chuyển hướng: encode bình thường
        $vnp_Url = $vnp_Url . '?' . http_build_query($filtered) . '&vnp_SecureHash=' . $vnpSecureHash;
        // Lưu thông tin giao dịch vào đơn hàng
        $order->transaction_id = $vnp_TxnRef;
        $order->save();

        return $vnp_Url;
    }
    
    public function processVnpayReturn(array $vnpayData): array
    {
        Log::info('VNPAY return', $vnpayData);
        $vnp_HashSecret = config('services.vnpay.hash_secret');
        $vnp_SecureHash = $vnpayData['vnp_SecureHash'] ?? '';
        
        // Xóa các tham số không cần thiết
        unset($vnpayData['vnp_SecureHash']);
        unset($vnpayData['vnp_SecureHashType']);
        
        // Sắp xếp dữ liệu theo key
        ksort($vnpayData);
        
        // Loại bỏ tham số rỗng và sắp xếp lại
        $filtered = [];
        foreach ($vnpayData as $k => $v) {
            if ($v !== null && $v !== '') {
                $filtered[$k] = $v;
            }
        }
        ksort($filtered);
        // Tạo chuỗi hash để kiểm tra: urlencode(key)=urlencode(value)
        $pairs = [];
        foreach ($filtered as $k => $v) {
            $pairs[] = urlencode($k) . '=' . urlencode((string) $v);
        }
        $rawToSign = implode('&', $pairs);
        $secureHash = hash_hmac('sha512', $rawToSign, $vnp_HashSecret);
        
        // Kiểm tra hash
        if ($secureHash !== $vnp_SecureHash) {
            return [
                'success' => false,
                'message' => 'Chữ ký không hợp lệ!'
            ];
        }
        
        // Lấy order_id từ vnp_TxnRef
        $orderId = explode('_', $vnpayData['vnp_TxnRef'])[0] ?? null;
        
        if (!$orderId) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng!'
            ];
        }
        
        $order = Order::find($orderId);
        
        if (!$order) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng!'
            ];
        }
        
        // Kiểm tra kết quả giao dịch
        $responseCode = $vnpayData['vnp_ResponseCode'] ?? null;
        if (!$responseCode) {
            return [
                'success' => false,
                'message' => 'Thiếu tham số phản hồi từ VNPAY',
                'order' => $order
            ];
        }

        if ($responseCode === '00') {
            $order->payment_status = 'paid';
            $order->save();
            
            return [
                'success' => true,
                'message' => 'Thanh toán thành công!',
                'order' => $order
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Thanh toán thất bại! Mã: ' . $responseCode,
                'order' => $order
            ];
        }
    }
}