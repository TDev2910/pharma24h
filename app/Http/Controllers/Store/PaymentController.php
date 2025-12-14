<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Payment\VNPayService;
use App\Services\EmailSMTP\EmailService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected VNPayService $vnpayService;
    protected EmailService $emailService;

    public function __construct(VNPayService $vnpayService, EmailService $emailService)
    {
        $this->vnpayService = $vnpayService;
        $this->emailService = $emailService;
    }

    // Khởi tạo thanh toán VNPAY cho đơn hàng cụ thể và lấy orderid
    public function vnpayCheckout(int $order_id)
    {
        $order = Order::findOrFail($order_id);
        $url = $this->vnpayService->generatePaymentUrl($order);
        return redirect()->away($url);
    }

    // Return URL từ VNPAY
    public function vnpayReturn(Request $request)
    {
        $result = $this->vnpayService->processReturn($request->all());
        if ($result['success']) {
            // Gửi email xác nhận đơn hàng
            try {
                $this->emailService->sendOrderConfirmation($result['order']);
            } catch (\Exception $e) {
                // Silent fail - không log lỗi
            }
            
            return redirect()->route('checkout.success', ['order_id' => $result['order']->id]);
        }
        return redirect()->route('checkout.failed')->with('error', $result['message']);
    }

    public function vnpayIpn(Request $request)
    {
        $result = $this->vnpayService->processReturn($request->all());
        return response()->json([
            'RspCode' => $result['success'] ? '00' : '97',
            'Message' => $result['message'] ?? 'Invalid signature',
        ]);
    }
}