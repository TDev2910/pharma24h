<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\CheckoutService;
use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\Shipping\GHNService;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StaffOrderController extends Controller
{
    protected $ghnService;
    public function __construct(GHNService $ghnService)
    {
        $this->ghnService = $ghnService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {   
        // Thống kê nhanh (Stats)
        $stats = [
            'total'     => Order::count(),
            'pending'   => Order::where('order_status', 'pending')->count(),
            'completed' => Order::where('order_status', 'completed')->count(),
        ];

        // Query danh sách đơn hàng
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

        // Phân trang & Format dữ liệu trả về cho Table
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
                //GHN
                'shipping_code'  => $order->shipping_code,
                'ghn_order_code' => $order->ghn_order_code,
                'district_id'    => $order->district_id,
                'ward_code'      => $order->ward_code,
            ];
        });

        // Render View với Inertia
        return Inertia::render('Staff/Orders/Dashboard', [
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
                'order' => $order,
                'items' => $items,
            ]);
        }

        // Redirect về trang danh sách thay vì render view show
        return redirect()->route('staff.orders.index');
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
     * Update resource (RESTful) – hiện chỉ hỗ trợ cập nhật order_status.
     */
    public function update(Request $request, string $id)
    {
        $order = Order::findOrFail($id);

        // 1. Validate dữ liệu
        $validated = $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'required|string|max:20',
            'customer_email'   => 'nullable|email|max:255',
            'payment_method'   => 'nullable|string|max:50',
            'delivery_method'  => 'nullable|in:shipping,pickup',

            // Validate 
            'shipping_address' => 'nullable|required_if:delivery_method,shipping|string|max:255',
            'province'         => 'nullable|string|max:255',
            'district'         => 'nullable|string|max:255',
            'ward'             => 'nullable|string|max:255',
            'pickup_location'  => 'nullable|required_if:delivery_method,pickup|string|max:255',
            'note'             => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // 2. Cập nhật dữ liệu
            $order->fill($validated);
            $order->save();

            DB::commit();

            // 3. Trả về JSON 
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cập nhật thông tin thành công!',
                    'order'   => $order
                ]);
            }

            return back()->with('success', 'Cập nhật thông tin thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Lỗi hệ thống: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return back()->with('success', 'Đơn hàng đã được xóa thành công!');
    }

    public function markCompleted(int $id, CheckoutService $checkout)
    {
        $order = $checkout->completeOrder($id);
        return back()->with('success', "Đã hoàn thành đơn #{$order->id} và trừ tồn kho.");
    }

    //In hóa đơn file pdf
    public function printInvoice($id)
    {
        $order = Order::with(['items.item', 'user'])->findOrFail($id);

        $pdf = Pdf::loadView('staff.orders.invoice', compact('order'));

        return $pdf->stream('HOADON-' . $order->order_code . '.pdf');
    }

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
