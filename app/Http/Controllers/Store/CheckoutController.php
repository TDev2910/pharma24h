<?php

namespace App\Http\Controllers\Store;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\EmailSMTP\EmailService;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class CheckoutController extends Controller
{
    protected $cartService;
    protected $checkoutService;
    protected $emailService;
    public function __construct(CartService $cartService, CheckoutService $checkoutService, EmailService $emailService)
    {
        $this->cartService = $cartService;
        $this->checkoutService = $checkoutService;
        $this->emailService = $emailService;
    }

    // Hiển thị form checkout
    public function index()
    {
        $cartSummary = $this->cartService->getCartSummary(100);
        $cartItems = $cartSummary['items'];
        $cartTotal = $cartSummary['total'];

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }
        $user = Auth::user();
        $pharmacyLocations = [
            ['id' => 1, 'name' => 'Nhà thuốc Sức Khỏe 24h - Chi nhánh 1', 'address' => '123 Nguyễn Văn A, Quận 1, TP.HCM'],
            ['id' => 2, 'name' => 'Nhà thuốc Sức Khỏe 24h - Chi nhánh 2', 'address' => '456 Lê Văn B, Quận 2, TP.HCM'],
            ['id' => 3, 'name' => 'Nhà thuốc Sức Khỏe 24h - Chi nhánh 3', 'address' => '789 Trần Văn C, Quận 3, TP.HCM'],
        ];

        return view('store.checkout.index', compact('cartItems', 'cartTotal', 'user', 'pharmacyLocations'));
    }

    // Xử lý đặt hàng
    public function process(CheckoutRequest $request)
    {
        $cartSummary = $this->cartService->getCartSummary();
        if (empty($cartSummary['items'])) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        try {
            // Lấy tất cả validated data và thêm district_id, ward_code nếu có
            $orderData = $request->validated();

            // Thêm district_id và ward_code nếu có (có thể không có trong validated nếu validation fail)
            if ($request->has('district_id')) {
                $orderData['district_id'] = $request->input('district_id');
            }
            if ($request->has('ward_code')) {
                $orderData['ward_code'] = $request->input('ward_code');
            }

            // Lấy phí ship từ input hidden gửi lên
            $orderData['shipping_fee'] = $request->input('shipping_fee', 0);
            $orderData['ghn_fee']      = $request->input('ghn_fee', 0);
            $order = $this->checkoutService->createOrder($orderData);

            if ($request->payment_method === 'vnpay') {
                return redirect()->route('payment.vnpay.checkout', ['order_id' => $order->id]);
            }

            // Gửi email cho đơn COD (cod)
            if ($request->payment_method === 'cod') {
                try {
                    $this->emailService->sendOrderConfirmation($order);
                } catch (\Exception $e) {
                    // Failed to send order confirmation email for COD order
                }
            }

            return redirect()->route('checkout.success', ['order_id' => $order->id]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage())->withInput();
        }
    }

    // View thành công
    public function success(Request $request)
    {
        $orderId = $request->order_id;
        $order = Order::with('items')->findOrFail($orderId);

        return view('store.checkout.success', compact('order'));
    }

    // View thất bại
    public function failed()
    {
        return view('store.checkout.failed');
    }

    public function getShippingFee(Request $request): JsonResponse
    {
        // 1. Validate dữ liệu từ Frontend gửi lên
        $request->validate([
            'district_id' => 'required|integer',
            'ward_code'   => 'required|string',
        ]);

        try {
            // 2. Lấy thông tin giỏ hàng hiện tại
            $cartSummary = $this->cartService->getCartSummary();
            $cartTotal = $cartSummary['total'];
            $cartItems = $cartSummary['items'];

            // === LOGIC MIỄN PHÍ VẬN CHUYỂN ===
            // Nếu tổng đơn hàng >= 500,000 VNĐ thì phí ship = 0
            if ($cartTotal >= 500000) {
                return response()->json([
                    'success' => true,
                    'fee' => 0,
                    'message' => 'Miễn phí vận chuyển (Đơn hàng trên 500k)'
                ]);
            }

            // 3. Tính toán khối lượng (Logic giả định 100g/sp như code cũ của bạn)
            $weight = $cartItems->sum('quantity') * 100;

            // 4. Gọi Service GHN để tính phí thực tế
            $ghnService = new \App\Services\Shipping\GHNService(); // Hoặc inject qua constructor

            $result = $ghnService->calculateFee(
                (int)$request->district_id,
                $request->ward_code,
                $weight,
                (int)$cartTotal // Giá trị bảo hiểm
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'fee' => $result['total'],
                    'message' => 'Tính phí thành công'
                ]);
            }

            return response()->json($result); // Trả về lỗi từ GHN nếu có

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ]);
        }
    }
}
