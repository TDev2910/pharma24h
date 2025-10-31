<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Services\CheckoutService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::with('user')->latest();
        if ($request->filled('order_code')) {
            $query->where('order_code', $request->order_code);
        }
        // Lọc theo trạng thái nếu có
        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }
        // Lọc theo ngày đặt hàng nếu có
        $from = $request->input('from_date');
        $to = $request->input('to_date');
        $query->filterByDate($from, $to);
        $orders = $query->get();
        return view('admin.orders.index', compact('orders'));
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
            'status' => 'required|in:pending,completed,cancelled',
        ]);
        $order = Order::findOrFail($order);
        // Nếu cập nhật đơn hàng Hoàn thành từ modal, gọi service để trừ tồn + set trạng thái
        if ($request->status === 'completed') 
        {
            $order = $checkout->completeOrder((int) $order->id);
        } 
        elseif ($request->status === 'cancelled') 
        {
            // Nếu hủy đơn, gọi service để restore tồn kho chính (nếu đã completed)
            $order = $checkout->cancelOrder((int) $order->id);
        } 
        else 
        {
            // Cập nhật các trạng thái khác không trừ tồn
            $order->order_status = $request->status;
            if ($request->status === 'pending') {
                $order->payment_status = 'unpaid';
            }
            $order->save();
        }
        if ($request->ajax() || $request->wantsJson()) 
        {
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
            // Không nhận order_status ở đây để tránh nhầm với dropdown cập nhật trạng thái
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
        return $pdf->download('hoa-don-'.$order->order_code.'.pdf');
    }
}

