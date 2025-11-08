<?php

namespace App\Http\Controllers\Admin; 

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Medicine;
use App\Models\Goods;
use App\Models\Service;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;

class AdminController extends \App\Http\Controllers\Controller
{
    public function dashboard()
    {
        // Tính tổng số sản phẩm (Thuốc + Vật tư y tế + Dịch vụ)
        $totalProducts = Medicine::count() + Goods::count() + Service::count();
        
        // Tổng số đơn hàng
        $totalOrders = Order::count();
        
        // Tổng số khách hàng (role = 'user')
        $totalCustomers = User::where('role', 'user')->count();
        
        // Tổng doanh thu (tổng total_amount của các đơn hàng đã thanh toán)
        $totalRevenue = Order::where('payment_status', 'paid')
            ->sum('total_amount');
        
        // Dữ liệu cho biểu đồ Top Categories (3 loại)
        $medicineCount = Medicine::count();
        $goodsCount = Goods::count();
        $serviceCount = Service::count();
        
        // Top 5 khách hàng mua nhiều nhất
        $topCustomers = $this->getTopCustomers();

        // Top 5 sản phẩm được mua nhiều nhất
        $topProducts = $this->getTopProducts();

        return Inertia::render('Admin/AdminDashboard', [
            'stats' => [
                'totalProducts' => $totalProducts,
                'totalOrders' => $totalOrders,
                'totalCustomers' => $totalCustomers,
                'totalRevenue' => $totalRevenue,
            ],
            'categoryStats' => [
                'medicineCount' => $medicineCount,
                'goodsCount' => $goodsCount,
                'serviceCount' => $serviceCount,
            ],
            'topCustomers' => $topCustomers,
            'topProducts' => $topProducts,
        ]);
    }

    /**
     * Lấy Top 5 khách hàng mua nhiều nhất (dựa trên số đơn hàng đã thanh toán)
     * Tối ưu: Tránh N+1 query bằng cách load users một lần
     * 
     * @return \Illuminate\Support\Collection
     */
    private function getTopCustomers()
    {
        // Bước 1: Lọc đơn hàng đã thanh toán và có user_id
        $topCustomersQuery = Order::where('payment_status', 'paid')
            ->whereNotNull('user_id'); // Chỉ lấy đơn của user đã đăng nhập, bỏ qua đơn guest

        // Bước 2: Nhóm theo user_id và tính toán
        $topCustomers = $topCustomersQuery
            ->selectRaw('
                user_id,
                COUNT(*) as order_count,
                SUM(total_amount) as total_spent
            ')
            ->groupBy('user_id')
            ->orderByDesc('order_count') // Sắp xếp theo số đơn hàng giảm dần
            ->limit(5) // Chỉ lấy top 5
            ->get();

        // Bước 3: Tối ưu - Load tất cả users một lần thay vì find() từng cái
        $userIds = $topCustomers->pluck('user_id')->unique()->toArray();
        $users = User::whereIn('id', $userIds)->get()->keyBy('id');

        // Bước 4: Lấy thông tin user và format dữ liệu
        return $topCustomers->map(function ($item) use ($users) {
            $user = $users->get($item->user_id);
            
            // Trả về mảng với thông tin cần thiết
            return [
                'id' => $user ? $user->id : $item->user_id,
                'name' => $user ? $user->name : 'Khách hàng #' . $item->user_id,
                'order_count' => (int) $item->order_count, 
                'total_spent' => (float) $item->total_spent, 
            ];
        });
    }

    /**
     * Lấy Top 5 sản phẩm được mua nhiều nhất (dựa trên tổng số lượng bán ra)
     * Chỉ tính đơn đã thanh toán, chỉ lấy Medicine và Goods
     * Tối ưu: Tránh N+1 query bằng cách load products một lần
     * 
     * @return \Illuminate\Support\Collection
     */
    private function getTopProducts()
    {
        //Lấy OrderItem từ các Order đã thanh toán
        // Join với Order để lọc payment_status = 'paid'
        $topProductsQuery = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'paid')
            ->whereIn('order_items.item_type', ['medicine', 'goods']); // Chỉ lấy Medicine và Goods

        //Nhóm theo item_id và item_type, tính tổng số lượng và doanh thu
        $topProducts = $topProductsQuery
            ->selectRaw('
                order_items.item_id,
                order_items.item_type,
                SUM(order_items.quantity) as total_sold,
                SUM(order_items.subtotal) as total_earnings,
                MAX(order_items.product_name) as product_name
            ')
            ->groupBy('order_items.item_id', 'order_items.item_type')
            ->orderByDesc('total_sold') // Sắp xếp theo số lượng bán ra giảm dần
            ->limit(5) // Chỉ lấy top 5
            ->get();

        //Tối ưu - Load tất cả Medicine và Goods một lần thay vì find() từng cái
        $medicineIds = $topProducts->where('item_type', 'medicine')->pluck('item_id')->unique()->toArray();
        $goodsIds = $topProducts->where('item_type', 'goods')->pluck('item_id')->unique()->toArray();
        
        $medicines = Medicine::whereIn('id', $medicineIds)->get()->keyBy('id');
        $goods = Goods::whereIn('id', $goodsIds)->get()->keyBy('id');

        //Lấy thông tin chi tiết từ Medicine/Goods và format dữ liệu
        return $topProducts->map(function ($item) use ($medicines, $goods) {
            $product = null;
            $productName = '';
            $stocks = 0;
            $price = 0;

            // Lấy thông tin sản phẩm từ Medicine hoặc Goods (đã load sẵn)
            if ($item->item_type === 'medicine') {
                $product = $medicines->get($item->item_id);
                if ($product) {
                    $productName = $product->ten_thuoc;
                    $stocks = $product->ton_kho ?? 0;
                    $price = $product->gia_ban ?? 0;
                }
            } 
            elseif ($item->item_type === 'goods') {
                $product = $goods->get($item->item_id);
                if ($product) {
                    $productName = $product->ten_hang_hoa;
                    $stocks = $product->ton_kho ?? 0;
                    $price = $product->gia_ban ?? 0;
                }
            }

            // Fallback: Nếu không tìm thấy sản phẩm, dùng tên từ order_items
            if (!$product) {
                $productName = $item->product_name ?? 'Sản phẩm #' . $item->item_id;
            }

            // Trả về mảng với thông tin cần thiết
            return [
                'id' => $item->item_id,
                'type' => $item->item_type,
                'name' => $productName,
                'stocks' => (int) $stocks,
                'price' => (float) $price,
                'sales' => (int) $item->total_sold, // Số lượng đã bán
                'earnings' => (float) $item->total_earnings, // Tổng doanh thu
            ];
        });
    }
}