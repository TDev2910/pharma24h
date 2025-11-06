<?php

namespace App\Http\Controllers\Admin\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShiftRequest;
use App\Models\Shift;
use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Exception;

class ShiftController extends Controller
{
    /**
     * Hiển thị danh sách ca làm việc
     * Note: Frontend component đã bị xóa, method này chỉ để tương thích với routes
     * Nếu cần sử dụng, có thể trả về JSON hoặc redirect
     */
    public function index(Request $request)
    {
        // Frontend component đã bị xóa, trả về JSON nếu là API request
        if ($request->expectsJson() || $request->ajax()) {
            return $this->apiIndex($request);
        }
        
        // Hoặc redirect về trang khác
        return redirect()->route('admin.employees.index')
            ->with('info', 'Trang quản lý ca làm việc chưa được triển khai');
    }

    /**
     * API: Lấy danh sách ca làm việc
     */
    public function apiIndex(Request $request)
    {
        $shifts = Shift::with('branch')
            ->when($request->branch_id, function ($query, $branchId) {
                $query->where('branch_id', $branchId);
            })
            ->orderBy('start_time', 'asc')
            ->get();

        return response()->json($shifts);
    }

    /**
     * Lưu ca làm việc mới
     */
    public function store(StoreShiftRequest $request)
    {
        try {
            $shift = Shift::create($request->validated());
            $shift->load('branch');

            return redirect()->back()->with('success', 'Thêm ca làm việc thành công!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Cập nhật ca làm việc
     */
    public function update(StoreShiftRequest $request, $id)
    {
        try {
            $shift = Shift::findOrFail($id);
            $shift->update($request->validated());
            $shift->load('branch');

            return redirect()->back()->with('success', 'Cập nhật ca làm việc thành công!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Xóa ca làm việc
     */
    public function destroy($id)
    {
        try {
            $shift = Shift::findOrFail($id);

            // Kiểm tra ca này có đang được sử dụng không
            if ($shift->schedules()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Không thể xóa ca làm việc này vì đang có lịch làm việc sử dụng!');
            }

            $shift->delete();

            return redirect()->back()->with('success', 'Xóa ca làm việc thành công!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
