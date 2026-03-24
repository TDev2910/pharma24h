<?php

namespace App\Infrastructure\Payment\Adapters;

use App\Core\Payment\Ports\Outbound\PaymentGatewayInterface;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use SePay\SePayClient;
use SePay\Builders\CheckoutBuilder;

class SePayAdapter implements PaymentGatewayInterface
{
    protected SePayClient $client;
    protected string $secretKey;

    public function __construct()
    {
        // Khởi tạo Client từ SDK với Merchant ID và Secret Key
        $this->client = new SePayClient(
            config('services.sepay.merchant_id'),
            config('services.sepay.secret_key'),
            config('services.sepay.sandbox') ? SePayClient::ENVIRONMENT_SANDBOX : SePayClient::ENVIRONMENT_PRODUCTION
        );

        $this->secretKey = config('services.sepay.secret_key');
    }

    public function generatePaymentData(Order $order): array
    {
        $checkoutData = CheckoutBuilder::make()
            ->paymentMethod('BANK_TRANSFER') 
            ->currency('VND')
            ->orderInvoiceNumber('INV-' . $order->id . '-' . time())
            ->orderAmount((int)$order->total_amount)
            ->operation('PURCHASE')
            ->orderDescription('Thanh toán đơn hàng #' . $order->id)
            ->successUrl(route('checkout.success', ['order_id' => $order->id]))
            ->build();

        // Tạo các field cần thiết để submit form 
        $formFields = $this->client->checkout()->generateFormFields($checkoutData);
        $actionUrl  = $this->client->checkout()->getCheckoutUrl(
            config('services.sepay.sandbox') ? 'sandbox' : 'production'
        );

        // Lưu transaction id (Invoice Number)
        $order->transaction_id = $checkoutData['order_invoice_number'];
        $order->save();

        return [
            'type'        => 'form_post', 
            'action_url'  => $actionUrl,
            'fields'      => $formFields,
            'description' => $checkoutData['order_description'],
            'amount'      => $checkoutData['order_amount']
        ];
    }

    public function verifyPayment(array $data): array
    {
        // 1. Xác thực Webhook Token từ Header Authorization
        $receivedToken = request()->header('Authorization');
        if ($receivedToken !== "Apikey {$this->secretKey}") {
            Log::error('SePay: Webhook Token invalid', ['received' => $receivedToken]);
            return ['success' => false, 'message' => 'Token Webhook không hợp lệ!'];
        }

        /**
         * 2. Tìm đơn hàng qua invoice_number hoặc content
         * SePay gửi về payload chứa 'content' (Nội dung chuyển khoản)
         */
        $content = $data['content'] ?? '';

        // Tìm Invoice Number trong transaction_id của Order
        $order = Order::where('transaction_id', 'like', '%' . $content . '%')
            ->orWhere('id', 'like', '%' . preg_replace('/[^0-9]/', '', $content) . '%')
            ->first();

        if (!$order) {
            return ['success' => false, 'message' => 'Không tìm thấy đơn hàng tương ứng với nội dung chuyển khoản.'];
        }

        // 3. Kiểm tra số tiền
        $transferAmount = (int) ($data['transferAmount'] ?? 0);
        if ($transferAmount < (int) $order->total_amount) {
            return ['success' => false, 'message' => 'Số tiền chuyển khoản không khớp.', 'order' => $order];
        }

        // 4. Hoàn tất thanh toán
        $order->payment_status = 'paid';
        $order->save();

        return ['success' => true, 'message' => 'Thanh toán thành công qua SePay!', 'order' => $order];
    }
}
