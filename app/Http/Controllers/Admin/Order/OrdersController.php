<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Services\CheckoutService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Notifications\OrderStatusUpdated;
use App\Notifications\OrderCancellationProcessed;
use Inertia\Inertia;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Tính stats
        $totalOrders = Order::count();
        $pendingOrders = Order::whereIn('order_status', ['new', 'pending'])->count();
        $completedOrders = Order::where('order_status', 'completed')->count();

        // Query với filters
        $query = Order::with('user')->latest();

        if ($request->filled('order_code')) {
            $query->where('order_code', $request->order_code);
        }

        // Lọc theo trạng thái nếu có
        if ($request->filled('status')) {
            if ($request->status === Order::STATUS['CANCELLATION_REQUESTED']) {
                $query->cancellationRequested();
            } else {
                $query->where('order_status', $request->status);
            }
        }

        // Lọc theo ngày đặt hàng nếu có
        $from = $request->input('from_date');
        $to = $request->input('to_date');
        $query->filterByDate($from, $to);

        // Pagination
        $orders = $query->paginate(10);

        // Format dữ liệu orders
        $ordersData = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'order_code' => $order->order_code,
                'customer_name' => $order->customer_name ?? 'N/A',
                'customer_email' => $order->customer_email ?? null,
                'customer_phone' => $order->customer_phone ?? null,
                'order_status' => $order->order_status ?? 'pending',
                'payment_status' => $order->payment_status ?? 'pending',
                'payment_method' => $order->payment_method ?? 'N/A',
                'total_amount' => $order->total_amount ?? 0,
                'created_at' => $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : null,
                'created_at_formatted' => $order->created_at ? $order->created_at->format('d/m/Y') : 'N/A',
            ];
        });

        return Inertia::render('Admin/Orders/Products/Dashboard', [
            'stats' => [
                'totalOrders' => $totalOrders,
                'pendingOrders' => $pendingOrders,
                'completedOrders' => $completedOrders
            ],
            'orders' => $ordersData,
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
                'from' => $orders->firstItem(),
                'to' => $orders->lastItem()
            ],
            'filters' => [
                'order_code' => $request->input('order_code', ''),
                'status' => $request->input('status', ''),
                'from_date' => $request->input('from_date', ''),
                'to_date' => $request->input('to_date', '')
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $order)
    {
        $order = Order::findOrFail($order);
        $items = $order->items;

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'order' => [
                    'id' => $order->id,
                    'order_code' => $order->order_code,
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $order->customer_phone,
                    'customer_email' => $order->customer_email,
                    'order_status' => $order->order_status,
                    'payment_status' => $order->payment_status,
                    'payment_method' => $order->payment_method,
                    'delivery_method' => $order->delivery_method,
                    'shipping_address' => $order->shipping_address,
                    'province' => $order->province,
                    'district' => $order->district,
                    'ward' => $order->ward,
                    'district_id' => $order->district_id,
                    'ward_code' => $order->ward_code,
                    'pickup_location' => $order->pickup_location,
                    'total_amount' => $order->total_amount,
                    'note' => $order->note,
                    'transaction_id' => $order->transaction_id,
                    'created_at' => $order->created_at,
                    'cancellation_status' => $order->cancellation_status,
                    'cancellation_reason' => $order->cancellation_reason,
                    'cancellation_user_note' => $order->cancellation_user_note,
                    'cancellation_admin_note' => $order->cancellation_admin_note,
                    'cancellation_requested_at' => $order->cancellation_requested_at,
                    'cancellation_processed_at' => $order->cancellation_processed_at,
                    // GHN fields
                    'ghn_order_code' => $order->ghn_order_code,
                    'ghn_status' => $order->ghn_status,
                    'ghn_fee' => $order->ghn_fee,
                    'ghn_expected_delivery_time' => $order->ghn_expected_delivery_time,
                    'ghn_tracking_url' => $order->ghn_tracking_url,
                    'ghn_shipper_name' => $order->ghn_shipper_name,
                    'ghn_shipper_phone' => $order->ghn_shipper_phone,
                    'ghn_created_at' => $order->ghn_created_at,
                ],
                'items' => $items,
            ]);
        }

        // Redirect về trang danh sách thay vì render view show
        return redirect()->route('admin.orders.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $order = Order::findOrFail($id);
        $items = $order->items;
        return response()->json([
            'success' => true,
            'order' => $order,
            'items' => $items,
        ]);
    }

    /**
     * Update the specified resource status.
     */
    public function updateStatus(Request $request, string $order, CheckoutService $checkout)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,confirmed,cancelled,delivered',
        ]);
        $order = Order::findOrFail($order);
        $oldStatus = $order->order_status;
        // Nếu cập nhật đơn hàng Hoàn thành từ modal, gọi service để trừ tồn + set trạng thái
        if ($request->status === 'completed') {
            $order = $checkout->completeOrder((int) $order->id);
        } elseif ($request->status === 'cancelled') {
            // Nếu hủy đơn, gọi service để restore tồn kho chính (nếu đã completed)
            $order = $checkout->cancelOrder((int) $order->id);
        } else {
            $order->order_status = $request->status;
            if($request->status === 'confirmed'){
                $order->payment_status = 'unpaid';
            }
            // Cập nhật các trạng thái khác không trừ tồn
            $order->order_status = $request->status;
            if ($request->status === 'pending') {
                $order->payment_status = 'unpaid';
            }   
            $order->save();
        }

        // Gửi notification cho user nếu status thay đổi
        if ($oldStatus !== $order->order_status && $order->user) {
            $order->user->notify(new OrderStatusUpdated($order, $oldStatus, $order->order_status));
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Trạng thái đơn hàng đã được cập nhật thành công!',
            ]);
        }
        return redirect()->back()->with('success', 'Trạng thái đơn hàng đã được cập nhật thành công!');
    }

    /**
     * Update resource (RESTful) – hiện chỉ hỗ trợ cập nhật order_status.
     */
    public function update(Request $request, string $id)
    {
        $order = Order::findOrFail($id);

        // Validate các trường thông tin đơn hàng (trừ trạng thái đơn cập nhật qua endpoint riêng)
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'shipping_address' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'ward' => 'nullable|string|max:255',
            'pickup_location' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:1000',
            'delivery_method' => 'nullable|in:shipping,pickup',
            'payment_method' => 'nullable|string|max:50',
        ]);

        $order->fill($validated);
        $order->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thông tin đơn hàng thành công',
                'order' => $order,
            ]);
        }
        return redirect()->back()->with('success', 'Cập nhật thông tin đơn hàng thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đơn hàng đã được xóa thành công!',
            ]);
        }

        return redirect()->route('admin.orders.index')->with('success', 'Đơn hàng đã được xóa thành công!');
    }

    public function markCompleted(int $id, CheckoutService $checkout)
    {
        $order = $checkout->completeOrder($id);
        return back()->with('success', "Đã hoàn thành đơn #{$order->id} và trừ tồn kho.");
    }

    //In hóa đơn file pdf
    public function printInvoice($orderId)
    {
        $order = Order::with('items')->findOrFail($orderId);
        $pdf = Pdf::loadView('admin.orders.invoice', compact('order'));
        return $pdf->download('hoa-don-' . $order->order_code . '.pdf');
    }

    public function approveCancellation(Request $request, Order $order, CheckoutService $checkout)
    {
        $data = $request->validate([
            'admin_note' => 'nullable|string|max:2000',
        ]);

        if ($order->cancellation_status !== Order::CANCELLATION_STATUS['REQUESTED']) {
            return back()->withErrors(['error' => 'Đơn này không có yêu cầu hủy hoặc đã xử lý.']);
        }
 
        try {
            DB::transaction(function () use ($order, $data, $checkout) {
                $order = Order::whereKey($order->getKey())->lockForUpdate()->firstOrFail();
 
                if ($order->order_status === Order::STATUS['COMPLETED']) {
                    $checkout->cancelOrder($order->id); // trả hàng + cập nhật payment_status
                }
                else 
                {
                    $order->order_status = Order::STATUS['CANCELLED'];
                    $order->payment_status = 'cancelled';
                    $order->save();
                }
 
                $order->cancellation_status = Order::CANCELLATION_STATUS['APPROVED'];
                $order->cancellation_admin_note = $data['admin_note'] ?? null;
                $order->cancellation_processed_at = now();
                $order->cancellation_processed_by = Auth::id();
                $order->order_status_before_cancellation = null;
                $order->save();
            });
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Không thể duyệt yêu cầu hủy: ' . $e->getMessage()]);
        }
 
        $order->refresh();

        if ($order->user) {
            $order->user->notify(
                new OrderCancellationProcessed(
                    $order,
                    Order::CANCELLATION_STATUS['APPROVED'],
                    $data['admin_note'] ?? null
                )
            );
        }

        return back()->with('success', 'Đã xác nhận hủy đơn hàng.');
    }
    
    public function rejectCancellation(Request $request, Order $order)
    {
        $data = $request->validate([
            'admin_note' => 'required|string|max:2000',
        ]);

        if($order->cancellation_status !== Order::CANCELLATION_STATUS['REQUESTED']) {
            return back()->withErrors(['error' => 'Đơn này không có yêu cầu hủy hoặc đã xử lý.']);
        }
     
        try {
            DB::transaction(function () use ($order, $data) {
                $order = Order::whereKey($order->getKey())->lockForUpdate()->firstOrFail();
 
                // Khôi phục trạng thái đơn về pending (hoặc trạng thái cũ nếu bạn lưu lại đâu đó)
                $order->order_status = $order->order_status_before_cancellation ?? Order::STATUS['PENDING'];
                $order->cancellation_status = Order::CANCELLATION_STATUS['REJECTED'];
                $order->cancellation_admin_note = $data['admin_note'];
                $order->cancellation_processed_at = now();
                $order->cancellation_processed_by = Auth::id();
                $order->order_status_before_cancellation = null;
                $order->save();
            });
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Không thể từ chối yêu cầu hủy: ' . $e->getMessage()]);
        }
        $order->refresh();

        if ($order->user) {
            $order->user->notify(
                new OrderCancellationProcessed(
                    $order,
                    Order::CANCELLATION_STATUS['REJECTED'],
                    $data['admin_note']
                )
            );
        }

        return back()->with('success', 'Đã từ chối yêu cầu hủy đơn hàng.');
    }
}
