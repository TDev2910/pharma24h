<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    protected $cartService;
    protected $checkoutService;

    public function __construct(CartService $cartService, CheckoutService $checkoutService)
    {
        $this->cartService = $cartService;
        $this->checkoutService = $checkoutService;
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
            $order = $this->checkoutService->createOrder($request->validated());

            if ($request->payment_method === 'vnpay') {
                $vnpayUrl = $this->checkoutService->generateVnpayPaymentUrl($order);
                return redirect($vnpayUrl);
            }

            return redirect()->route('checkout.success', ['order_id' => $order->id]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage())->withInput();
        }
    }

    // Trang thành công
    public function success(Request $request)
    {
        $orderId = $request->order_id;
        $order = Order::with('items')->findOrFail($orderId);

        return view('store.checkout.success', compact('order'));
    }

    // Trang thất bại
    public function failed()
    {
        return view('store.checkout.failed');
    }

    // Xử lý callback từ VNPAY
    public function vnpayReturn(Request $request)
    {
        $result = $this->checkoutService->processVnpayReturn($request->all());

        if ($result['success']) {
            return redirect()->route('checkout.success', ['order_id' => $result['order']->id]);
        }

        return redirect()->route('checkout.failed')->with('error', $result['message']);
    }
}
