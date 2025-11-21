<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Payment\VNPayService;
use App\Services\EmailSMTP\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            \Log::info('VNPay payment successful, attempting to send email', [
                'order_id' => $result['order']->id,
                'order_code' => $result['order']->order_code,
                'customer_email' => $result['order']->customer_email
            ]);
            
            // Gửi email xác nhận đơn hàng
            try {
                $this->emailService->sendOrderConfirmation($result['order']);
            } catch (\Exception $e) {
                \Log::error('Failed to send order confirmation email after VNPay payment', [
                    'order_id' => $result['order']->id,
                    'order_code' => $result['order']->order_code,
                    'error' => $e->getMessage()
                ]);
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
