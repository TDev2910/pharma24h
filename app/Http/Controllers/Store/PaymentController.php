<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Core\Payment\Ports\Inbound\PaymentUseCaseInterface;
use App\Core\Payment\Domain\DTOs\PaymentCheckoutData;
use App\Core\Payment\Domain\DTOs\PaymentReturnData;
use Inertia\Inertia;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected PaymentUseCaseInterface $paymentUseCase;

    public function __construct(PaymentUseCaseInterface $paymentUseCase)
    {
        $this->paymentUseCase = $paymentUseCase;
    }

    /**
     * Khởi tạo thanh toán - Chỉ nhận diện Luồng (Flow) 
     * còn Logic thì giao phó cho UseCase.
     */
    public function checkout(string $driver, int $order_id)
    {
        $data = new PaymentCheckoutData($driver, $order_id);

        //Gọi qua Inbound Port với DTO
        $response = $this->paymentUseCase->handleCheckout($data);

        $paymentData = $response['payment_data'];
        $order = $response['order'];

        if ($paymentData['type'] === 'url') {
            return Inertia::location($paymentData['payment_url']);
        }

        // Nếu là SePay hoặc các loại thanh toán yêu cầu POST Form
        if ($paymentData['type'] === 'form_post') {
            return Inertia::render('Public/Checkout/PaymentRedirect', [
                'action_url' => $paymentData['action_url'],
                'fields'     => $paymentData['fields'],
                'amount'     => $paymentData['amount'],
                'message'    => 'Đang chuyển hướng bạn đến cổng thanh toán an toàn của SePay...'
            ]);
        }

        if ($paymentData['type'] === 'qr') {
            return Inertia::render('Public/Checkout/PaymentQR', [
                'order'       => $order,
                'qr_url'      => $paymentData['qr_url'],
                'description' => $paymentData['description'],
                'amount'      => $paymentData['amount'],
                'bank_info'   => [
                    'account_num' => $paymentData['account_num'],
                    'bank_code'   => $paymentData['bank_code']
                ]
            ]);
        }

        return redirect()->route('checkout.failed');
    }

    /**
     * Xử lý ReturnUrl 
     */
    public function return(string $driver, Request $request)
    {
        // 1. Tạo DTO phản hồi
        $data = new PaymentReturnData($driver, $request->all());

        // 2. Gọi qua Inbound Port
        $result = $this->paymentUseCase->handleReturn($data);

        if ($result['success']) {
            return redirect()->route('checkout.success', [
                'order_id' => $result['order']->id
            ]);
        }

        return redirect()->route('checkout.failed')->with('error', $result['message']);
    }

    /**
     * Webhook/IPN xử lý ngầm
     */
    public function ipn(string $driver, Request $request)
    {
        // 1. Tạo DTO phản hồi
        $data = new PaymentReturnData($driver, $request->all());

        // 2. Gọi qua Inbound Port
        $result = $this->paymentUseCase->handleIpn($data);

        if ($driver === 'vnpay') {
            return response()->json([
                'RspCode' => $result['success'] ? '00' : '97',
                'Message' => $result['message'],
            ]);
        }

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
        ]);
    }
}
