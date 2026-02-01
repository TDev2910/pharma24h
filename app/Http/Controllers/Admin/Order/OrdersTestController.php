<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Services\Shipping\GHNService;

class OrdersTestController extends Controller
{
    protected $ghnService;

    // Inject GHNService vào Constructor để sử dụng toàn cục
    public function __construct(GHNService $ghnService)
    {
        $this->ghnService = $ghnService;
    }

    /**
     * 1. Hiển thị danh sách đơn hàng & Lazy Load cho Modal chi tiết
     */
    public function index(Request $request)
    {
        // A. Thống kê nhanh (Stats)
        $stats = [
            'total'     => Order::count(),
            'pending'   => Order::where('order_status', 'pending')->count(),
            'completed' => Order::where('order_status', 'completed')->count(),
        ];

        // B. Query danh sách đơn hàng
        $query = Order::with(['user', 'items'])->latest();

        // Filter: Tìm kiếm
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('order_code', 'like', "%{$term}%")
                    ->orWhere('customer_name', 'like', "%{$term}%")
                    ->orWhere('customer_phone', 'like', "%{$term}%");
            });
        }

        // Filter: Trạng thái
        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        // Filter: Ngày tháng
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
        }

        // C. Phân trang & Format dữ liệu trả về cho Table
        $orders = $query->paginate(10)->withQueryString()->through(function ($order) {
            return [
                'id'             => $order->id,
                'order_code'     => $order->order_code,
                'customer_name'  => $order->customer_name ?? 'Khách lẻ',
                'customer_phone' => $order->customer_phone ?? '',
                'total_amount'   => $order->total_amount,
                'order_status'   => $order->order_status,
                'payment_status' => $order->payment_status,
                'created_at'     => $order->created_at ? $order->created_at->format('d/m/Y H:i') : '',
                'customer_email'   => $order->customer_email,
                'payment_method'   => $order->payment_method,
                'delivery_method'  => $order->delivery_method,
                'shipping_address' => $order->shipping_address,
                'province'         => $order->province,
                'district'         => $order->district,
                'ward'             => $order->ward,
                'pickup_location'  => $order->pickup_location,
                'note'             => $order->note,
                'items'            => $order->items,
                //GHN/In
                'shipping_code'  => $order->shipping_code,
                'ghn_order_code' => $order->ghn_order_code,
                'district_id'    => $order->district_id,
                'ward_code'      => $order->ward_code,
            ];
        });

        // D. Render View với Inertia
        return Inertia::render('Admin/Orders/Products/DashboardTest', [
            'stats'   => $stats,
            'orders'  => $orders,
            'filters' => $request->only(['search', 'status', 'from_date', 'to_date']),

            // Lazy Load: Chỉ tải khi frontend yêu cầu 'selectedOrder'
            'selectedOrder' => Inertia::lazy(function () use ($request) {
                if (!$request->has('order_id')) return null;

                // Load sâu các quan hệ để hiển thị chi tiết
                return Order::with(['items.item', 'user'])
                    ->find($request->order_id);
            }),
        ]);
    }

    /**
     * 2. Cập nhật trạng thái đơn hàng (Dùng cho Modal Edit)
     */
    public function updateStatus(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,delivering,completed,cancelled',
            'note'   => 'nullable|string'
        ]);

        $order = Order::findOrFail($id);

        DB::transaction(function () use ($order, $request) {
            // Cập nhật trạng thái
            $order->order_status = $request->status;

            // Logic phụ: Nếu hoàn thành thì set đã thanh toán
            if ($request->status === 'completed') {
                $order->payment_status = 'paid';
            }

            // Lưu ghi chú nếu có
            if ($request->filled('note')) {
                $order->note = $request->note;
            }

            $order->save();
        });

        return back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
    }

    /**
     * 3. Tạo đơn Giao Hàng Nhanh (GHN) - Gọi Service Thật
     */
    public function createGhnOrder(Request $request, string $id)
    {
        // Lấy đơn hàng kèm items để tính trọng lượng
        $order = Order::with('items')->findOrFail($id);

        // Validate dữ liệu địa chỉ trước khi gọi API
        if (!$order->district_id || !$order->ward_code) {
            return back()->with('error', 'Lỗi: Đơn hàng thiếu thông tin Quận/Huyện hoặc Phường/Xã.');
        }

        try {
            // Gọi Service GHN (Hàm createOrder bạn đã viết trong GHNService)
            $result = $this->ghnService->createOrder($order);

            if ($result['success']) {
                // Thành công: Cập nhật thông tin GHN vào DB
                $order->update([
                    'carrier'                    => 'GHN',
                    'ghn_order_code'             => $result['order_code'],
                    'ghn_status'                 => 'ready_to_pick', // Trạng thái ban đầu
                    'ghn_fee'                    => $result['total_fee'] ?? 0,
                    'ghn_expected_delivery_time' => $result['expected_delivery_time'] ?? null,
                    'ghn_created_at'             => now(),

                    // Tự động chuyển trạng thái đơn hàng sang "Đang giao"
                    'order_status'               => 'delivering'
                ]);

                return back()->with('success', 'Tạo vận đơn GHN thành công! Mã: ' . $result['order_code']);
            } else {
                // Thất bại: Trả về lỗi từ GHN
                return back()->with('error', 'Lỗi GHN: ' . ($result['message'] ?? 'Không xác định'));
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }

    /**
     * 4. In hóa đơn PDF (Nội bộ)
     */
    public function printInvoice($id)
    {
        $order = Order::with(['items.item', 'user'])->findOrFail($id);

        // Đảm bảo bạn đã có view 'admin.orders.invoice'
        $pdf = Pdf::loadView('admin.orders.invoice', compact('order'));

        return $pdf->stream('HOADON-' . $order->order_code . '.pdf');
    }

    //Đồng bộ trạng thái giao hang từ GHN thủ công
    public function syncGhnStatus($id)
    {
        $order = Order::findOrFail($id);

        //kiểm tra đơn hàng có mã vận đơn GHN chưa
        if (!$order->ghn_order_code) {
            return back()->with('error', 'Đơn hàng chưa có mã vận đơn GHN.');
        }

        try {
            // 1. Gọi Service lấy thông tin từ GHN
            $result = $this->ghnService->getOrderInfo($order->ghn_order_code);

            if ($result['success']) {
                $ghnData = $result['data'];
                $ghnStatus = strtolower($ghnData['status'] ?? '');

                // 2. Cập nhật thông tin GHN vào DB
                $order->ghn_status = $ghnStatus;

                // Cập nhật thêm các thông tin khác nếu GHN trả về
                if (isset($ghnData['total_fee'])) {
                    $order->ghn_fee = $ghnData['total_fee'];
                }

                // 3. Logic đồng bộ trạng thái đơn hàng (Mapping Status)
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

    /**
     * 5. In vận đơn GHN (Redirect sang trang in của GHN hoặc API)
     */
    public function printGhnOrder($id)
    {
        $order = Order::findOrFail($id);

        if (!$order->ghn_order_code) {
            return back()->with('error', 'Đơn hàng chưa có mã vận đơn GHN.');
        }

        // Nếu GHN có link public tracking/print thì redirect
        // Nếu không, bạn cần gọi API GHN để lấy token in đơn.
        // Tạm thời trả về thông báo hoặc link tracking nếu có.
        if ($order->ghn_tracking_url) {
            return Inertia::location($order->ghn_tracking_url);
        }

        return back()->with('warning', 'Chức năng in vận đơn đang được cập nhật.');
    }
}
