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
        // hiển thị danh sách lịch làm việc
        $schedules = EmployeeSchedule::with(['employee.user', 'shift'])
            ->when($request->employee_id, function ($query, $employeeId) {
                $query->where('employee_id', $employeeId);
            })
            ->when($request->date_from, function ($query, $dateFrom) {
                $query->where('schedule_date', '>=', $dateFrom);
            })
            ->when($request->date_to, function ($query, $dateTo) {
                $query->where('schedule_date', '<=', $dateTo);
            })
            ->orderBy('schedule_date', 'desc')
            ->paginate(20);

        return Inertia::render('Admin/Employee/Schedules/Index', [
            'schedules' => $schedules,
            'employees' => Employee::with('user')->get(),
            'shifts'    => Shift::all(),
        ]);
    }

    /**
     * API: Lấy lịch làm việc
     */
    public function getSchedules(Request $request)
    {
        $schedules = EmployeeSchedule::with(['employee.user', 'shift'])
            ->when($request->employee_id, function ($query, $employeeId) {
                $query->where('employee_id', $employeeId);
            })
            ->when($request->month, function ($query, $month) {
                $query->whereMonth('schedule_date', $month);
            })
            ->when($request->year, function ($query, $year) {
                $query->whereYear('schedule_date', $year);
            })
            ->when($request->week_start, function ($query, $weekStart) {
                // Tính tuần từ ngày bắt đầu (thứ 2) đến chủ nhật
                $weekEnd = date('Y-m-d', strtotime($weekStart . ' +6 days'));
                $query->whereBetween('schedule_date', [$weekStart, $weekEnd]);
            })
            ->orderBy('schedule_date', 'asc')
            ->get();

        return response()->json($schedules);
    }

    /**
     * API: Lấy lịch làm việc theo tuần với thông tin nhân viên và lương dự kiến
     */
    public function getWeeklySchedules(Request $request)
    {
        $weekStart = $request->get('week_start', date('Y-m-d', strtotime('monday this week')));
        $weekEnd   = date('Y-m-d', strtotime($weekStart . ' +6 days'));

        // Lấy tất cả nhân viên
        $employees = Employee::with(['user', 'branch', 'department', 'jobTitle'])->get();

        // Lấy lịch làm việc trong tuần
        $schedules = EmployeeSchedule::with(['shift'])
            ->whereBetween('schedule_date', [$weekStart, $weekEnd])
            ->get()
            ->groupBy('employee_id');

        // Tính lương dự kiến cho mỗi nhân viên
        $result = $employees->map(function ($employee) use ($schedules, $weekStart, $weekEnd) {
            $employeeSchedules = $schedules->get($employee->id, collect());

            // Tính số ca làm việc trong tuần
            $shiftCount = $employeeSchedules->count();

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
            } else {
                // Tính theo giờ (cần thêm thông tin giờ làm việc từ shift)
                // Tạm thời để 0
            }

            return [
                'employee'        => $employee,
                'schedules'       => $employeeSchedules->groupBy(function ($schedule) {
                    return $schedule->schedule_date->format('Y-m-d');
                })->map(function ($daySchedules) {
                    return $daySchedules->map(function ($schedule) {
                        return [
                            'id'           => $schedule->id,
                            'shift'        => $schedule->shift,
                            'schedule_date'=> $schedule->schedule_date->format('Y-m-d'),
                            'notes'        => $schedule->notes,
                        ];
                    })->values();
                })->toArray(),
                'shift_count'     => $shiftCount,
                'estimated_salary'=> $estimatedSalary,
            ];
        });

        return response()->json([
            'week_start' => $weekStart,
            'week_end'   => $weekEnd,
            'employees'  => $result,
        ]);
    }

    /**
     * Lưu lịch làm việc mới
     */
    public function store(StoreScheduleRequest $request)
    {
        try {
            $schedule = EmployeeSchedule::create($request->validated());
            $schedule->load(['employee.user', 'shift']);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success'  => true,
                    'message'  => 'Thêm lịch làm việc thành công!',
                    'schedule' => $schedule
                ], 201);
            }

            return redirect()->back()->with('success', 'Thêm lịch làm việc thành công!');
        }
        catch (Exception $e)
        {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $message = 'Nhân viên đã có lịch làm việc cho ca này trong ngày đã chọn!';

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 422);
                }

                return redirect()->back()
                    ->with('error', $message)
                    ->withInput();
            }

            $message = 'Có lỗi xảy ra: ' . $e->getMessage();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 500);
            }

            return redirect()->back()
                ->with('error', $message)
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

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success'  => true,
                    'message'  => 'Cập nhật lịch làm việc thành công!',
                    'schedule' => $schedule
                ]);
            }

            return redirect()->back()->with('success', 'Cập nhật lịch làm việc thành công!');
        } catch (Exception $e) {
            $message = 'Có lỗi xảy ra: ' . $e->getMessage();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 500);
            }

            return redirect()->back()
                ->with('error', $message)
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

            // [QUAN TRỌNG] Trả về JSON cho VueJS xử lý
            return response()->json([
                'success' => true,
                'message' => 'Xóa lịch làm việc thành công!'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
