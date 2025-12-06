<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Medicine;
use App\Models\Goods;
use App\Models\StockImportItem;
use App\Models\StockImport;
use App\Models\Inventory\PurchaseReturnItem;
use App\Models\Inventory\PurchaseReturn;
use App\Models\User;
use App\Models\Supplier;

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

        // Query aggregate data
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
            ->take($limit)
            ->get();

        if ($topProducts->isEmpty()) {
            return collect([]);
        }

        // Manual eager load (with soft deleted)
        $medicines = Medicine::withTrashed()
            ->whereIn('id', $topProducts->where('item_type', 'medicine')->pluck('item_id'))
            ->get()
            ->keyBy('id');

        $goods = Goods::withTrashed()
            ->whereIn('id', $topProducts->where('item_type', 'goods')->pluck('item_id'))
            ->get()
            ->keyBy('id');

        // Mapping data
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
                'stocks'   => (int)($product->ton_kho ?? 0),
                'price'    => (float)($product->gia_ban ?? 0),
                'sales'    => (int)$item->total_sold,
                'earnings' => (float)$item->total_earnings,
            ];
        });
    }

    /**
     * Hiển thị trang báo cáo Top 10 sản phẩm được đặt hàng nhiều nhất
     */
    public function topStockImports()
    {
        $stockImports = $this->getTopStockImports(10);

        return Inertia::render('Admin/Reports/DashboardStockImports', [
            'topProducts' => $stockImports, 
        ]);
    }

    /**
     * Lấy Top N sản phẩm được nhập nhiều nhất (dựa trên tổng số lượng nhập)
     * Chỉ tính đơn đã hoàn thành (completed), chỉ lấy Medicine và Goods
     *
     * @param int $limit Số lượng sản phẩm cần lấy (mặc định 10)
     * @return \Illuminate\Support\Collection
     */
    private function getTopStockImports($limit = 10)
    {
        $types = ['medicine', 'goods'];

        $topProducts = StockImportItem::query()
            ->join('stock_imports', 'stock_import_items.stock_import_id', '=', 'stock_imports.id')
            ->whereIn('stock_import_items.product_type', $types)
            ->selectRaw('
                stock_import_items.product_id,
                stock_import_items.product_type,
                SUM(stock_import_items.quantity) as total_imported,
                SUM(stock_import_items.total_price) as total_cost,
                AVG(stock_import_items.unit_price) as avg_unit_price')
            ->groupBy('stock_import_items.product_id', 'stock_import_items.product_type')
            ->orderByDesc('total_imported')
            ->take($limit)
            ->get();

        if ($topProducts->isEmpty()) {
            return collect([]);
        }

        // Manual eager load (with soft deleted)
        $medicines = Medicine::withTrashed()
            ->whereIn('id', $topProducts->where('product_type', 'medicine')->pluck('product_id'))
            ->get()
            ->keyBy('id');

        $goods = Goods::withTrashed()
            ->whereIn('id', $topProducts->where('product_type', 'goods')->pluck('product_id'))
            ->get()
            ->keyBy('id');

        // Mapping data
        return $topProducts->map(function ($item) use ($medicines, $goods) {
            $product = null;

            if ($item->product_type === 'medicine') {
                $product = $medicines->get($item->product_id);
            } elseif ($item->product_type === 'goods') {
                $product = $goods->get($item->product_id);
            }

            return [
                'id'             => $item->product_id,
                'type'           => $item->product_type,
                'name'           => $product ? ($product->ten_thuoc ?? $product->ten_hang_hoa) : 'N/A',
                'stocks'         => (int)($product->ton_kho ?? 0),
                'price'          => (float)($product->gia_ban ?? 0),
                'imported'       => (int)$item->total_imported,
                'total_cost'     => (float)$item->total_cost,
                'avg_unit_price' => (float)$item->avg_unit_price,
            ];
        });
    }

     /**
     * Hiển thị trang báo cáo Top 10 sản phẩm được trả hàng nhiều nhất
     */
    public function topStockReturns()
    {
        $stockReturns = $this->getTopStockReturns(10);

        return Inertia::render('Admin/Reports/DashboardPurchaseRetuns', [ 
            'topProducts' => $stockReturns,
        ]);
    }

    /**
     * Lấy Top N sản phẩm được nhập nhiều nhất (dựa trên tổng số lượng nhập)
     * Chỉ tính đơn đã hoàn thành (completed), chỉ lấy Medicine và Goods
     *
     * @param int $limit Số lượng sản phẩm cần lấy (mặc định 10)
     * @return \Illuminate\Support\Collection
     */
    private function getTopStockReturns($limit = 10)
    {
        $types = ['medicine', 'goods'];

        $topProducts = PurchaseReturnItem::query()
            ->join('purchase_returns', 'purchase_return_items.purchase_return_id', '=', 'purchase_returns.id')
            ->whereIn('purchase_return_items.product_type', $types)
            ->selectRaw('
                purchase_return_items.product_id,
                purchase_return_items.product_type,
                SUM(purchase_return_items.quantity) as total_returned,
                SUM(purchase_return_items.total_price) as total_cost,
                AVG(purchase_return_items.unit_price) as avg_unit_price
            ')
            ->groupBy('purchase_return_items.product_id', 'purchase_return_items.product_type')
            ->orderByDesc('total_returned')
            ->take($limit)
            ->get();

        if ($topProducts->isEmpty()) {
            return collect([]);
        }

        $medicines = Medicine::withTrashed()
            ->whereIn('id', $topProducts->where('product_type', 'medicine')->pluck('product_id'))
            ->get()
            ->keyBy('id');

        $goods = Goods::withTrashed()
            ->whereIn('id', $topProducts->where('product_type', 'goods')->pluck('product_id'))
            ->get()
            ->keyBy('id');

        // Mapping data
        return $topProducts->map(function ($item) use ($medicines, $goods) {
            $product = null;

            if ($item->product_type === 'medicine') {
                $product = $medicines->get($item->product_id);
            } elseif ($item->product_type === 'goods') {
                $product = $goods->get($item->product_id);
            }

            return [
                'id'             => $item->product_id,
                'type'           => $item->product_type,
                'name'           => $product ? ($product->ten_thuoc ?? $product->ten_hang_hoa) : 'N/A',
                'stocks'         => (int) ($product->ton_kho ?? 0),
                'price'          => (float) ($product->gia_ban ?? 0),
                'returned'       => (int) $item->total_returned,
                'total_cost'     => (float) $item->total_cost,
                'avg_unit_price' => (float) $item->avg_unit_price,
            ];
        });
    }

     /**
     * Hiển thị trang báo cáo Top 10 khách hàng mua nhiều nhất
     */
    public function topCustomers()
    {
        $stockCustomers = $this->getTopCustomers(10);
        return Inertia::render('Admin/Reports/DashboardTopCustomers', [
            'topCustomers' => $stockCustomers,
        ]);
    }

        /**
     * Lấy Top N sản phẩm được nhập nhiều nhất (dựa trên tổng số lượng nhập)
     * Chỉ tính đơn đã hoàn thành (completed), chỉ lấy Medicine và Goods
     *
     * @param int $limit Số lượng sản phẩm cần lấy (mặc định 10)
     * @return \Illuminate\Support\Collection
     */

     private function getTopCustomers($limit = 10)
     {
        $topCustomersQuery = Order::where('payment_status', 'paid')
            ->whereNotNull('user_id');
        
        $topCustomers = $topCustomersQuery
            ->selectRaw('
                user_id,
                COUNT(*) as order_count,
                SUM(total_amount) as total_spent
            ')
            ->groupBy('user_id')
            ->orderByDesc('total_spent')
            ->take($limit)
            ->get();
        if($topCustomers->isEmpty()) {
            return collect([]);
        }
        $userIds = $topCustomers->pluck('user_id')->unique()->toArray();
        $users = User::whereIn('id', $userIds)->get()->keyBy('id');

        return $topCustomers->map(function($item) use ($users) {
            $user = $users->get($item->user_id);
            return [
                'id' => $user ? $user->id : $item->user_id,
                'name' => $user ? $user->name : 'Khách hàng #' . $item->user_id,
                'email' => $user ? $user->email : 'N/A',
                'phone' => $user ? $user->phone : 'N/A',
                'order_count' => (int) $item->order_count,
                'total_spent' => (float) $item->total_spent,
            ];
        });
     }

    public function topSuppliers()
    {
        $stockSuppliers = $this->getTopSuppliers(10);
        return Inertia::render('Admin/Reports/DashboardTopSuppliers', [
            'topSuppliers' => $stockSuppliers,
        ]);
    }

    private function getTopSuppliers($limit = 10)
    {
        $topSuppliers = StockImport::query()
            ->join('stock_import_items', 'stock_imports.id', '=', 'stock_import_items.stock_import_id')
            ->join('suppliers', 'stock_imports.supplier_id', '=', 'suppliers.id')
            // Bỏ filter status hoặc mở rộng
            // ->where('stock_imports.status', 'completed') // Bỏ dòng này hoặc thay bằng whereIn
            ->selectRaw('
                suppliers.id,
                suppliers.ten_nha_cung_cap,
                suppliers.email,
                suppliers.ten_cong_ty,
                suppliers.ma_so_thue,
                SUM(stock_import_items.quantity) as total_imported
            ')
            ->groupBy('suppliers.id', 'suppliers.ten_nha_cung_cap', 'suppliers.email', 'suppliers.ten_cong_ty', 'suppliers.ma_so_thue')
            ->orderByDesc('total_imported')
            ->take($limit)
            ->get();
        
        if($topSuppliers->isEmpty()) {
            return collect([]);
        }
        
        // Sửa: Dùng $topSuppliers thay vì $topSuppliersQuery, và không cần $suppliers nữa
        return $topSuppliers->map(function($item) {
            return [
                'id' => $item->id,
                'name' => $item->ten_nha_cung_cap, // Đổi tên để khớp với Vue
                'email' => $item->email ?? 'N/A',
                'company_name' => $item->ten_cong_ty ?? 'N/A', // Đổi tên
                'tax_code' => $item->ma_so_thue ?? 'N/A', // Đổi tên
                'total_imported' => (int) $item->total_imported, // Giữ nguyên hoặc đổi thành order_count nếu cần
            ];
        });
    }    
}