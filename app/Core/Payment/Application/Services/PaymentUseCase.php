<?php

namespace App\Core\Payment\Application\Services;

use App\Core\Payment\Ports\Inbound\PaymentUseCaseInterface;
use App\Core\Payment\Domain\DTOs\PaymentCheckoutData;
use App\Core\Payment\Domain\DTOs\PaymentReturnData;
use App\Models\Order;
use App\Services\EmailSMTP\EmailService;
use Exception;

class PaymentUseCase implements PaymentUseCaseInterface
{
    protected PaymentManager $paymentManager;
    protected EmailService $emailService;

    public function __construct(PaymentManager $paymentManager, EmailService $emailService)
    {
        $this->paymentManager = $paymentManager;
        $this->emailService = $emailService;
    }

    /**
     * @inheritDoc
     */
    // xử lý thanh toán
    public function handleCheckout(PaymentCheckoutData $data): array
    {
        $order = Order::findOrFail($data->orderId); // lấy đơn hàng
        $gateway = $this->paymentManager->driver($data->driver); // chọn cổng thanh toán

        $paymentData = $gateway->generatePaymentData($order); // tạo dữ liệu thanh toán

        return [
            'order'        => $order,
            'payment_data' => $paymentData,
            'driver'       => $data->driver
        ];
    }

    /**
     * @inheritDoc
     */
    // xử lý kết quả trả về từ cổng thanh toán cho người dùng
    public function handleReturn(PaymentReturnData $data): array
    {
        $gateway = $this->paymentManager->driver($data->driver);
        // kiểm tra chữ ký
        $result = $gateway->verifyPayment($data->data);

        // Gửi email 
        if ($result['success'] && isset($result['order'])) {
            try {
                $this->emailService->sendOrderConfirmation($result['order']);
            } catch (\Exception $e) {
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    // xử lý cập nhật trạng thái ngầm (IPN) từ cổng thanh toán
    public function handleIpn(PaymentReturnData $data): array
    {
        $gateway = $this->paymentManager->driver($data->driver);
        return $gateway->verifyPayment($data->data);
    }
}
