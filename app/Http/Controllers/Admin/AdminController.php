<?php

namespace App\Http\Controllers\Admin; 

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Medicine;
use App\Models\Goods;
use App\Models\Service;
use App\Models\Order;
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
            ]
        ]);
    }
}