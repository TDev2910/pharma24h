<?php

namespace App\Infrastructure\Payment\Adapters;

use App\Core\Payment\Ports\Outbound\PaymentGatewayInterface;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class VNPayAdapter implements PaymentGatewayInterface
{
    protected string $tmnCode;
    protected string $hashSecret;
    protected string $vnpUrl;

    public function __construct()
    {
        $this->tmnCode = config('services.vnpay.tmn_code');
        $this->hashSecret = config('services.vnpay.hash_secret');
        $this->vnpUrl = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
    }

    public function generatePaymentData(Order $order): array
    {
        $returnUrl = route('payment.vnpay.return');
        $txnRef = $order->id . '_' . time();

        $params = [
            'vnp_Version'    => '2.1.0',
            'vnp_TmnCode'    => $this->tmnCode,
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

        ksort($params);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($params as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $this->vnpUrl . "?" . $query;
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $this->hashSecret);
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

        // Lưu transaction id
        $order->transaction_id = $txnRef;
        $order->save();

        return [
            'type' => 'url',
            'payment_url' => $vnp_Url
        ];
    }

    public function verifyPayment(array $data): array
    {
        $vnp_SecureHash = $data['vnp_SecureHash'] ?? '';
        unset($data['vnp_SecureHash'], $data['vnp_SecureHashType']);
        ksort($data);

        $i = 0;
        $hashData = "";
        foreach ($data as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $this->hashSecret);

        if ($secureHash !== $vnp_SecureHash) {
            return ['success' => false, 'message' => 'Chữ ký không hợp lệ!'];
        }

        $orderId = isset($data['vnp_TxnRef']) ? explode('_', $data['vnp_TxnRef'])[0] : null;
        $order = Order::find($orderId);

        if (!$order) {
            return ['success' => false, 'message' => 'Không tìm thấy đơn hàng'];
        }

        if ($data['vnp_ResponseCode'] === '00') {
            $order->payment_status = 'paid';
            $order->save();
            return ['success' => true, 'message' => 'Thanh toán thành công!', 'order' => $order];
        }

        return ['success' => false, 'message' => 'Thanh toán thất bại!', 'order' => $order];
    }
}
