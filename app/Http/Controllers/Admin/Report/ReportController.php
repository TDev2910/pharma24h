<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Medicine;
use App\Models\Goods;

class ReportController extends Controller
{
    /**
     * Hiển thị trang báo cáo Top 10 sản phẩm được mua nhiều nhất
     */
    public function topProductsSell()
    {
        $topProducts = $this->getTopProducts(10);
        return Inertia::render('Admin/Reports/DashboardTopProductsSell', [
            'topProducts' => $topProducts,
        ]);
    }

    /**
     * Lấy Top N sản phẩm được mua nhiều nhất (dựa trên tổng số lượng bán ra)
     * Chỉ tính đơn đã thanh toán, chỉ lấy Medicine và Goods
     *
     * @param int $limit Số lượng sản phẩm cần lấy (mặc định 10)
     * @return \Illuminate\Support\Collection
     */
    private function getTopProducts($limit = 10)
    {
        $types = ['medicine', 'goods'];

        // 2. Query Aggregate Data
        $topProducts = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'paid')
            ->whereIn('order_items.item_type', $types)
            ->selectRaw('
                order_items.item_id,
                order_items.item_type,
                SUM(order_items.quantity) as total_sold,
                SUM(order_items.subtotal) as total_earnings,
                MAX(order_items.product_name) as historical_name
            ')
            ->groupBy('order_items.item_id', 'order_items.item_type')
            ->orderByDesc('total_sold')
            ->take($limit) // Dùng take() thay vì limit() cho đúng chuẩn Eloquent builder
            ->get();

        if ($topProducts->isEmpty()) {
            return collect([]);
        }

        // 3. Eager Loading thủ công (Manual Eager Loading)
        // Dùng withTrashed() để lấy cả sản phẩm đã ngừng kinh doanh (Soft Delete)
        $medicines = Medicine::withTrashed()
            ->whereIn('id', $topProducts->where('item_type', 'medicine')->pluck('item_id'))
            ->get()
            ->keyBy('id');

        $goods = Goods::withTrashed()
            ->whereIn('id', $topProducts->where('item_type', 'goods')->pluck('item_id'))
            ->get()
            ->keyBy('id');

        // 4. Mapping Data
        return $topProducts->map(function ($item) use ($medicines, $goods) {
            $product = null;

            if ($item->item_type === 'medicine') {
                $product = $medicines->get($item->item_id);
            } elseif ($item->item_type === 'goods') {
                $product = $goods->get($item->item_id);
            }

            return [
                'id'       => $item->item_id,
                'type'     => $item->item_type,
                // Ưu tiên tên hiện tại, nếu không có (bị xóa cứng) thì dùng tên lịch sử
                'name'     => $product ? ($product->ten_thuoc ?? $product->ten_hang_hoa) : $item->historical_name,
                'stocks'   => (int) ($product->ton_kho ?? 0),
                'price'    => (float) ($product->gia_ban ?? 0), // Giá hiện tại
                'sales'    => (int) $item->total_sold,
                'earnings' => (float) $item->total_earnings,
            ];
        });
    }
}