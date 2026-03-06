<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Services\Shipping\GHNService;
use App\Core\Order\Domain\DTOs\OrderData;
use App\Services\CheckoutService;
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

        return Inertia::render('Admin/Orders/Products/Dashboard', [
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

    public function show(Request $request, string $order)
    {
        $data = $this->useCase->getOrderDetails((int)$order);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'order' => $data['order'],
                'items' => $data['items'],
            ]);
        }

        return redirect()->route('admin.orders.index');
    }

    public function edit(Request $request, string $order)
    {
        $data = $this->useCase->getOrderDetails((int)$order);

        return response()->json([
            'success' => true,
            'order' => $data['order'],
            'items' => $data['items'],
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

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'required|string|max:20',
            'customer_email'   => 'nullable|email|max:255',
            'payment_method'   => 'nullable|string|max:50',
            'delivery_method'  => 'nullable|in:shipping,pickup',
            'shipping_address' => 'nullable|required_if:delivery_method,shipping|string|max:255',
            'province'         => 'nullable|string|max:255',
            'district'         => 'nullable|string|max:255',
            'ward'             => 'nullable|string|max:255',
            'pickup_location'  => 'nullable|required_if:delivery_method,pickup|string|max:255',
            'note'             => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $orderData = new OrderData(
                customer_name: $validated['customer_name'] ?? null,
                customer_phone: $validated['customer_phone'] ?? null,
                customer_email: $validated['customer_email'] ?? null,
                shipping_address: $validated['shipping_address'] ?? null,
                province: $validated['province'] ?? null,
                district: $validated['district'] ?? null,
                ward: $validated['ward'] ?? null,
                pickup_location: $validated['pickup_location'] ?? null,
                note: $validated['note'] ?? null,
                delivery_method: $validated['delivery_method'] ?? null,
                payment_method: $validated['payment_method'] ?? null
            );

            $this->useCase->updateOrderInfo((int)$id, $orderData);

            DB::commit();

            if ($request->wantsJson()) {
                $updatedData = $this->useCase->getOrderDetails((int)$id);
                return response()->json([
                    'success' => true,
                    'message' => 'Cập nhật thông tin thành công!',
                    'order'   => $updatedData['order']
                ]);
            }

            return back()->with('success', 'Cập nhật thông tin thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Lỗi hệ thống: ' . $e->getMessage()]);
        }
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

    public function destroy(string $id)
    {
        $this->useCase->deleteOrder((int)$id);
        return back()->with('success', 'Đơn hàng đã được xóa thành công!');
    }

    public function approveCancellation(string $id)
    {
        $order = Order::findOrFail($id);

        DB::transaction(function () use ($order) {
            $order->order_status = 'cancelled';
            $order->payment_status = 'cancelled';
            $order->cancellation_status = 'approved';
            $order->save();
        });

        return back()->with('success', 'Đã duyệt yêu cầu hủy đơn hàng.');
    }

    public function rejectCancellation(Request $request, string $id)
    {
        $request->validate([
            'note' => 'required|string|max:1000'
        ]);

        $order = Order::findOrFail($id);

        DB::transaction(function () use ($order, $request) {
            $order->cancellation_status = 'rejected';
            $order->cancellation_note = $request->note;
            $order->save();
        });

        return back()->with('success', 'Đã từ chối yêu cầu hủy đơn hàng.');
    }

    public function markCompleted(int $id, CheckoutService $checkout)
    {
        $order = $checkout->completeOrder($id);
        return back()->with('success', "Đã hoàn thành đơn #{$order->id} và trừ tồn kho.");
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
