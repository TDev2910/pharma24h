<?php

namespace App\Http\Controllers\Staff; 

use Illuminate\Http\Request;
use App\Models\EmployeeSchedule;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class StaffController extends \App\Http\Controllers\Controller
{
    public function dashboard()
    {
        return Inertia::render('Staff/StaffDashboard');
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
        $estimatedSalary = 0;
        if ($employee->salary_type === 'fixed') {
            // Giả sử tháng có 22 ngày làm việc
            $estimatedSalary = ($employee->salary_level / 22) * ($shiftCount / 7) * 7;
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