<?php

namespace App\Services\Payment;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class VNPayService
{
    public function generatePaymentUrl(Order $order): string
    {
        $vnpUrl = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
        $returnUrl = route('payment.vnpay.return');
        $tmnCode = config('services.vnpay.tmn_code');
        $hashSecret = config('services.vnpay.hash_secret');

        $txnRef = $order->id . '_' . time();
        $params = [
            'vnp_Version'    => '2.1.0',
            'vnp_TmnCode'    => $tmnCode,
            'vnp_Command'    => 'pay',
            'vnp_Amount'     => (int) ($order->total_amount * 100),
            'vnp_CurrCode'   => 'VND',
            'vnp_TxnRef'     => $txnRef,
            'vnp_OrderInfo'  => 'Thanh toán đơn hàng #' . $order->id,
            'vnp_OrderType'  => 'other',
            'vnp_Locale'     => 'vn',
            'vnp_ReturnUrl'  => $returnUrl,
            'vnp_CreateDate' => now('Asia/Ho_Chi_Minh')->format('YmdHis'),
            'vnp_ExpireDate' => now('Asia/Ho_Chi_Minh')->addMinutes(15)->format('YmdHis'),
            'vnp_IpAddr'     => request()->ip(),
        ];

        // lọc rỗng và sort
        $filtered = [];
        foreach ($params as $k => $v) {
            if ($v !== null && $v !== '') {
                $filtered[$k] = $v;
            }
        }
        ksort($filtered);

        // chuỗi ký
        $pairs = [];
        foreach ($filtered as $k => $v) {
            $pairs[] = urlencode($k) . '=' . urlencode((string) $v);
        }
        $rawToSign = implode('&', $pairs);
        $secureHash = hash_hmac('sha512', $rawToSign, $hashSecret);

        $redirectUrl = $vnpUrl . '?' . http_build_query($filtered) . '&vnp_SecureHash=' . $secureHash;

        Log::info('VNPAY: build payment url', [
            'order_id' => $order->id,
            'raw_to_sign' => $rawToSign,
            'secure_hash' => $secureHash,
            'url' => $redirectUrl,
        ]);

        // lưu txn ref
        $order->transaction_id = $txnRef;
        $order->save();

        return $redirectUrl;
    }

    public function processReturn(array $data): array
    {
        $hashSecret = config('services.vnpay.hash_secret');  
        $receivedHash = $data['vnp_SecureHash'] ?? '';
        unset($data['vnp_SecureHash'], $data['vnp_SecureHashType']);

        $filtered = [];
        foreach ($data as $k => $v) {
            if ($v !== null && $v !== '') {
                $filtered[$k] = $v;
            }
        }
        ksort($filtered);

        $pairs = [];
        foreach ($filtered as $k => $v) {
            $pairs[] = urlencode($k) . '=' . urlencode((string) $v);
        }
        $rawToSign = implode('&', $pairs);
        $calc = hash_hmac('sha512', $rawToSign, $hashSecret);

        if (!hash_equals($receivedHash, $calc)) {
            return ['success' => false, 'message' => 'Chữ ký không hợp lệ!'];
        }

        $orderId = isset($filtered['vnp_TxnRef']) ? explode('_', $filtered['vnp_TxnRef'])[0] : null;
        if (!$orderId) {
            return ['success' => false, 'message' => 'Thiếu mã đơn hàng'];
        }

        $order = Order::find($orderId);
        if (!$order) {
            return ['success' => false, 'message' => 'Không tìm thấy đơn hàng'];
        }

        $code = $filtered['vnp_ResponseCode'] ?? null;
        if ($code === '00') {
            $order->payment_status = 'paid';
            $order->save();
            return ['success' => true, 'message' => 'Thanh toán thành công!', 'order' => $order];
        }

        return ['success' => false, 'message' => 'Thanh toán thất bại! Mã: ' . ($code ?? 'N/A'), 'order' => $order];
    }
}


