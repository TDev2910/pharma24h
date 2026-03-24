<?php

namespace App\Core\Payment\Services;

use App\Core\Payment\Ports\Inbound\PaymentUseCaseInterface;
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
    public function handleCheckout(string $driver, int $orderId): array
    {
        $order = Order::findOrFail($orderId);
        $gateway = $this->paymentManager->driver($driver);
        
        $paymentData = $gateway->generatePaymentData($order);

        return [
            'order'        => $order,
            'payment_data' => $paymentData,
            'driver'       => $driver
        ];
    }

    /**
     * @inheritDoc
     */
    public function handleReturn(string $driver, array $data): array
    {
        $gateway = $this->paymentManager->driver($driver);
        $result = $gateway->verifyPayment($data);

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
    public function handleIpn(string $driver, array $data): array
    {
        $gateway = $this->paymentManager->driver($driver);
        return $gateway->verifyPayment($data);
    }
}
