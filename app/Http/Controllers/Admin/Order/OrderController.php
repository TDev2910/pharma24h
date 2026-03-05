<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Services\Shipping\GHNService;
use App\Core\Order\Ports\Inbound\OrderUseCaseInterface;

class OrderController extends Controller
{
    public function __construct(
        protected GHNService $ghnService,
        protected OrderUseCaseInterface $useCase
    ) {}

    public function index(Request $request)
    {
        $data = $this->useCase->getAdminDashboardData($request->all());

        return Inertia::render('Admin/Orders/Products/DashboardTest', [
            'stats'   => $data['stats'],
            'orders'  => $data['orders'],
            'filters' => $request->only(['search', 'status', 'from_date', 'to_date']),

            'selectedOrder' => Inertia::lazy(function () use ($request) {
                if (!$request->has('order_id')) return null;

                return Order::with(['items.item', 'user'])
                    ->find($request->order_id);
            }),
        ]);
    }

    public function updateStatus(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,delivering,completed,cancelled',
            'note'   => 'nullable|string'
        ]);

        $order = Order::findOrFail($id);

        DB::transaction(function () use ($order, $request) {
            $order->order_status = $request->status;

            if ($request->status === 'completed') {
                $order->payment_status = 'paid';
            }

            if ($request->filled('note')) {
                $order->note = $request->note;
            }

            $order->save();
        });

        return back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
    }

    public function createGhnOrder(Request $request, string $id)
    {
        $order = Order::with('items')->findOrFail($id);

        if (!$order->district_id || !$order->ward_code) {
            return back()->with('error', 'Lỗi: Đơn hàng thiếu thông tin Quận/Huyện hoặc Phường/Xã.');
        }

        try {
            $result = $this->ghnService->createOrder($order);

            if ($result['success']) {
                $order->update([
                    'carrier'                    => 'GHN',
                    'ghn_order_code'             => $result['order_code'],
                    'ghn_status'                 => 'ready_to_pick',
                    'ghn_fee'                    => $result['total_fee'] ?? 0,
                    'ghn_expected_delivery_time' => $result['expected_delivery_time'] ?? null,
                    'ghn_created_at'             => now(),
                    'order_status'               => 'delivering'
                ]);

                return back()->with('success', 'Tạo vận đơn GHN thành công! Mã: ' . $result['order_code']);
            } else {
                return back()->with('error', 'Lỗi GHN: ' . ($result['message'] ?? 'Không xác định'));
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }

    public function printInvoice($id)
    {
        $order = Order::with(['items.item', 'user'])->findOrFail($id);

        $pdf = Pdf::loadView('admin.orders.invoice', compact('order'));

        return $pdf->stream('HOADON-' . $order->order_code . '.pdf');
    }

    public function syncGhnStatus($id)
    {
        $order = Order::findOrFail($id);

        if (!$order->ghn_order_code) {
            return back()->with('error', 'Đơn hàng chưa có mã vận đơn GHN.');
        }

        try {
            $result = $this->ghnService->getOrderInfo($order->ghn_order_code);

            if ($result['success']) {
                $ghnData = $result['data'];
                $ghnStatus = strtolower($ghnData['status'] ?? '');

                $order->ghn_status = $ghnStatus;

                if (isset($ghnData['total_fee'])) {
                    $order->ghn_fee = $ghnData['total_fee'];
                }

                switch ($ghnStatus) {
                    case 'cancel':
                    case 'return':
                    case 'returned':
                    case 'damage':
                    case 'lost':
                        if ($order->order_status !== 'cancelled') {
                            $order->order_status = 'cancelled';
                            $order->payment_status = 'cancelled';
                        }
                        break;

                    case 'delivered':
                        $order->order_status = 'completed';
                        if ($order->payment_method === 'cod') {
                            $order->payment_status = 'paid';
                        }
                        break;

                    case 'picking':
                    case 'storing':
                    case 'transporting':
                    case 'delivering':
                        $order->order_status = 'delivering';
                        break;
                }

                $order->save();

                return back()->with('success', 'Đồng bộ trạng thái GHN thành công: ' . $ghnStatus);
            } else {
                return back()->with('error', 'Lỗi từ GHN: ' . ($result['message'] ?? 'Không lấy được dữ liệu'));
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }

    public function printGhnOrder($id)
    {
        $order = Order::findOrFail($id);

        if (!$order->ghn_order_code) {
            return back()->with('error', 'Đơn hàng chưa có mã vận đơn GHN.');
        }

        if ($order->ghn_tracking_url) {
            return Inertia::location($order->ghn_tracking_url);
        }

        return back()->with('warning', 'Chức năng in vận đơn đang được cập nhật.');
    }
}
