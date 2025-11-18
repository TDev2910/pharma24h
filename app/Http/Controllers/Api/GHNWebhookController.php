<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class GHNWebhookController extends Controller
{
    /**
     * Xử lý webhook từ GHN
     * Route: POST /api/ghn/webhook
     */
    public function handleWebhook(Request $request)
    {
        // Log toàn bộ request để debug
        Log::info('GHN Webhook received', [
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'ip' => $request->ip()
        ]);

        // Lấy dữ liệu từ request
        $data = $request->all();
        $ghnOrderCode = $data['order_code'] ?? null;
        
        if (!$ghnOrderCode) {
            Log::warning('GHN Webhook: Missing order_code', ['data' => $data]);
            return response()->json([
                'code' => 400,
                'message' => 'Missing order_code'
            ], 400);
        }

        // Tìm đơn hàng theo mã GHN
        $order = Order::where('ghn_order_code', $ghnOrderCode)->first();
        
        if (!$order) {
            Log::warning('GHN Webhook: Order not found', ['ghn_order_code' => $ghnOrderCode]);
            return response()->json([
                'code' => 404,
                'message' => 'Order not found'
            ], 404);
        }

        // Cập nhật trạng thái đơn hàng
        try {
            DB::transaction(function () use ($order, $data) {
                // Cập nhật trạng thái GHN
                if (isset($data['status'])) {
                    $order->ghn_status = $data['status'];
                }
                
                if (isset($data['expected_delivery_time'])) {
                    $order->ghn_expected_delivery_time = $data['expected_delivery_time'];
                }
                
                // Lưu thông tin shipper nếu có
                if (isset($data['shipper_name'])) {
                    $order->ghn_shipper_name = $data['shipper_name'];
                }
                if (isset($data['shipper_phone'])) {
                    $order->ghn_shipper_phone = $data['shipper_phone'];
                }
                
                // Cập nhật trạng thái đơn hàng hệ thống dựa trên GHN status
                $this->updateOrderStatus($order, $data['status'] ?? $order->ghn_status);
                
                $order->save();

                Log::info('GHN Webhook: Order updated', [
                    'order_id' => $order->id,
                    'ghn_order_code' => $ghnOrderCode,
                    'ghn_status' => $order->ghn_status,
                    'order_status' => $order->order_status
                ]);
            });

            // Trả về response thành công cho GHN
            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => null
            ], 200);

        } catch (\Exception $e) {
            Log::error('GHN Webhook processing error', [
                'order_id' => $order->id ?? null,
                'ghn_order_code' => $ghnOrderCode,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'code' => 500,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Cập nhật trạng thái đơn hàng dựa trên GHN status
     */
    private function updateOrderStatus(Order $order, string $ghnStatus)
    {
        $statusMapping = [
            'ready_to_pick' => Order::STATUS['PENDING'],      // Chờ lấy hàng
            'picking' => Order::STATUS['PENDING'],            // Đang lấy hàng
            'storing' => Order::STATUS['PENDING'],            // Đang lưu kho
            'transporting' => Order::STATUS['PENDING'],       // Đang vận chuyển
            'delivering' => Order::STATUS['PENDING'],         // Đang giao hàng
            'delivered' => Order::STATUS['COMPLETED'],        // Đã giao → Hoàn thành
            'return' => Order::STATUS['CANCELLED'],           // Trả hàng → Hủy
            'cancel' => Order::STATUS['CANCELLED'],           // Hủy
        ];

        $newStatus = $statusMapping[$ghnStatus] ?? $order->order_status;
        
        if ($newStatus !== $order->order_status) {
            $oldStatus = $order->order_status;
            $order->order_status = $newStatus;
            
            // Nếu đã giao hàng, cập nhật payment status nếu là COD
            if ($ghnStatus === 'delivered' && $order->payment_method === 'cod') {
                $order->payment_status = 'paid';
            }
            
            Log::info('Order status updated from GHN webhook', [
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'ghn_status' => $ghnStatus
            ]);
        }
    }

    /**
     * Xác thực signature từ GHN (nếu có)
     * TODO: Implement khi GHN cung cấp webhook secret
     */
    private function verifySignature(Request $request): bool
    {
        // GHN có thể gửi signature trong header hoặc body
        // Cần kiểm tra tài liệu GHN API để biết cách verify
        $signature = $request->header('X-GHN-Signature') ?? $request->input('signature');
        $secret = config('services.ghn.webhook_secret');
        
        if (!$secret || !$signature) {
            // Nếu không có secret, tạm thời cho phép (có thể dùng IP whitelist)
            return true;
        }
        
        // Logic verify signature (tùy theo cách GHN implement)
        // Ví dụ: hash_hmac('sha256', json_encode($request->all()), $secret)
        
        return true; // Tạm thời return true, cần implement theo GHN docs
    }
}