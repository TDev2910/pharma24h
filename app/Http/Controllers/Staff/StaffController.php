<?php

namespace App\Http\Controllers\Staff; 

use Illuminate\Http\Request;
use App\Models\EmployeeSchedule;
use App\Models\Employee;
use App\Models\Order;
use App\Models\User;
use App\Models\Medicine;
use App\Models\Goods;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class StaffController extends \App\Http\Controllers\Controller
{
    public function dashboard()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        
        // Stats hôm nay
        $todayRevenue = Order::whereDate('created_at', $today)
            ->where('order_status', 'completed')
            ->sum('total_amount');
        
        $yesterdayRevenue = Order::whereDate('created_at', $yesterday)
            ->where('order_status', 'completed')
            ->sum('total_amount');
        
        $todayOrders = Order::whereDate('created_at', $today)->count();
        $yesterdayOrders = Order::whereDate('created_at', $yesterday)->count();
        
        // Tính phần trăm thay đổi
        $revenueChange = $yesterdayRevenue > 0 
            ? round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100, 1)
            : ($todayRevenue > 0 ? 100 : 0);
        
        $ordersChange = $yesterdayOrders > 0
            ? round((($todayOrders - $yesterdayOrders) / $yesterdayOrders) * 100, 1)
            : ($todayOrders > 0 ? 100 : 0);
        
        // Top 5 khách hàng mua nhiều nhất
        $topCustomers = $this->getTopCustomers();
        $lowStockItems = $this->getLowStockItems();

        return Inertia::render('Staff/StaffDashboard', [
            'stats' => [
                'todayRevenue' => number_format($todayRevenue, 0, ',', '.'),
                'revenueChange' => $revenueChange,
                'todayOrders' => $todayOrders,
                'ordersChange' => $ordersChange,
            ],
            'topCustomers' => $topCustomers,
            'lowStockItems' => $lowStockItems,
        ]);
    }

    //top 5 khách hàng mua nhiều nhất
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
                'total_spent_formatted' => number_format($item->total_spent, 0, ',', '.') . ' ₫',
            ];
        });
    }

    private function getLowStockItems()
    {
        //tạo biến fectchlowstock lấy dữ liệu của medicine và goods
        $fetchLowStock = function ($query, $nameColumn) {
            return $query
                ->where('ton_kho', '>', 0)
                ->whereNotNull('ton_thap_nhat')
                ->where('ton_thap_nhat', '>', 0)
                ->whereRaw('ton_kho <= ton_thap_nhat')
                ->select(   
                    'id',
                    "$nameColumn as name",
                    'ton_kho as qty',
                    'don_vi_tinh as unit',
                    'ton_thap_nhat as min')
                ->get()
                ->toBase()  //sử dụng base collection để tránh lỗi getKey() và tăng tốc độ
                ->map(function ($item) {
                    $percent = $item->min > 0 ? ($item->qty / $item->min) * 100 : 0;

                    return [
                        'id'      => $item->id,
                        'name'    => $item->name,
                        'qty'     => (int) $item->qty,
                        'unit'    => $item->unit ?? 'cái',
                        'min'     => (int) $item->min,
                        'percent' => round(min($percent, 100), 0),
                    ];
                });
        };

        // 2. Fetch from both tables
        $lowStockMedicines = $fetchLowStock(Medicine::query(), 'ten_thuoc');
        $lowStockGoods     = $fetchLowStock(Goods::query(), 'ten_hang_hoa');

        // 3. Merge, sort, and take top 5
        $allLowStock = $lowStockMedicines
            ->merge($lowStockGoods)
            ->sortBy('percent')
            ->take(5)
            ->values();

        return $allLowStock;
    }

    private function getStatusLabel($status)
    {
        $map = [
            'new' => 'Chờ xử lý',
            'pending' => 'Chờ xử lý',
            'confirmed' => 'Đã xác nhận',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            'delivering' => 'Đang giao',
        ];
        
        return $map[$status] ?? 'Chờ xử lý';
    }

    public function mySchedule()
    {   
        $user = Auth::user();
        $employee = $user->employee;
        
        if (!$employee) {
            return redirect('/staff/dashboard')
                ->with('error', 'Không tìm thấy thông tin nhân viên!');
        }

        return Inertia::render('Staff/MySchedule', [
            'employee' => $employee->load(['user', 'branch', 'department', 'jobTitle'])
        ]);
    }

    public function getMyWeeklySchedule(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;
        
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin nhân viên!'
            ], 404);
        }

        $weekStart = $request->get('week_start', date('Y-m-d', strtotime('monday this week')));
        $weekEnd = date('Y-m-d', strtotime($weekStart . ' +6 days'));
        
        // Lấy lịch làm việc của nhân viên này trong tuần
        $schedules = EmployeeSchedule::with(['shift'])
            ->where('employee_id', $employee->id)
            ->whereBetween('schedule_date', [$weekStart, $weekEnd])
            ->orderBy('schedule_date', 'asc')
            ->orderBy('shift_id', 'asc')
            ->get();
        
        // Tính số ca làm việc trong tuần
        $shiftCount = $schedules->count();
        
        // Tính lương dự kiến
        // - Lương cố định (fixed): Lương tháng / 22 ngày làm việc * số ca trong tuần
        // - Lương theo giờ (per_hour): Cần thêm thông tin giờ làm việc từ shift
        $estimatedSalary = 0;
        if ($employee->salary_type === 'fixed') {
            // Giả sử tháng có 22 ngày làm việc, mỗi ngày 1 ca
            // Lương 1 ca = salary_level / 22
            // Lương tuần = Lương 1 ca * số ca trong tuần
            $salaryPerShift = $employee->salary_level / 22; // Lương 1 ca
            $estimatedSalary = $salaryPerShift * $shiftCount; // Lương tuần
        }
        
        // Group schedules theo ngày
        $schedulesByDate = $schedules->groupBy(function($schedule) {
            return $schedule->schedule_date->format('Y-m-d');
        })->map(function ($daySchedules) {
            return $daySchedules->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'shift' => $schedule->shift,
                    'schedule_date' => $schedule->schedule_date->format('Y-m-d'),
                    'notes' => $schedule->notes,
                ];
            })->values();
        })->toArray();
        
        return response()->json([
            'success' => true,
            'week_start' => $weekStart,
            'week_end' => $weekEnd,
            'employee' => $employee->load(['user', 'branch', 'department', 'jobTitle']),
            'schedules' => $schedulesByDate,
            'shift_count' => $shiftCount,
            'estimated_salary' => $estimatedSalary,
        ]);
    }
}