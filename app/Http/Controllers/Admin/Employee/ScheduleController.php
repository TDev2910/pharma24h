<?php

namespace App\Http\Controllers\Admin\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreScheduleRequest;
use App\Models\EmployeeSchedule;
use App\Models\Employee;
use App\Models\Shift;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Exception;

class ScheduleController extends Controller
{
    /**
     * Hiển thị danh sách lịch làm việc
     */
    public function index(Request $request)
    {
        $schedules = EmployeeSchedule::with(['employee.user', 'shift'])
            ->when($request->employee_id, function($query, $employeeId) {
                $query->where('employee_id', $employeeId);
            })
            ->when($request->date_from, function($query, $dateFrom) {
                $query->where('schedule_date', '>=', $dateFrom);
            })
            ->when($request->date_to, function($query, $dateTo) {
                $query->where('schedule_date', '<=', $dateTo);
            })
            ->orderBy('schedule_date', 'desc')
            ->paginate(20);

        return Inertia::render('Admin/Employees/Schedules/Index', [
            'schedules' => $schedules,
            'employees' => Employee::with('user')->get(),
            'shifts' => Shift::all(),
        ]);
    }

    /**
     * API: Lấy lịch làm việc của nhân viên
     */
    public function getSchedules(Request $request)
    {
        $schedules = EmployeeSchedule::with(['employee.user', 'shift'])
            ->when($request->employee_id, function($query, $employeeId) {
                $query->where('employee_id', $employeeId);
            })
            ->when($request->month, function($query, $month) {
                $query->whereMonth('schedule_date', $month);
            })
            ->when($request->year, function($query, $year) {
                $query->whereYear('schedule_date', $year);
            })
            ->orderBy('schedule_date', 'asc')
            ->get();

        return response()->json($schedules);
    }

    /**
     * Lưu lịch làm việc mới
     */
    public function store(StoreScheduleRequest $request)
    {
        try {
            $schedule = EmployeeSchedule::create($request->validated());
            $schedule->load(['employee.user', 'shift']);

            return redirect()->back()->with('success', 'Thêm lịch làm việc thành công!');

        } catch (Exception $e) {
            // Kiểm tra lỗi duplicate entry
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return redirect()->back()
                    ->with('error', 'Nhân viên đã có lịch làm việc cho ca này trong ngày đã chọn!')
                    ->withInput();
            }

            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Cập nhật lịch làm việc
     */
    public function update(StoreScheduleRequest $request, $id)
    {
        try {
            $schedule = EmployeeSchedule::findOrFail($id);
            $schedule->update($request->validated());
            $schedule->load(['employee.user', 'shift']);

            return redirect()->back()->with('success', 'Cập nhật lịch làm việc thành công!');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Xóa lịch làm việc
     */
    public function destroy($id)
    {
        try {
            $schedule = EmployeeSchedule::findOrFail($id);
            $schedule->delete();

            return redirect()->back()->with('success', 'Xóa lịch làm việc thành công!');

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
