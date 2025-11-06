<?php

namespace App\Http\Controllers\Admin\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\Department;
use App\Models\JobTitle;
use App\Models\Branch;
use App\Services\EmployeeManagement\EmployeeService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Exception;

class EmployeeController extends Controller
{
    protected $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    /**
     * Hiển thị danh sách nhân viên
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->get('search'),
            'department_id' => $request->get('department_id'),
            'job_title_id' => $request->get('job_title_id'),
            'branch_id' => $request->get('branch_id'),
        ];

        $employees = $this->employeeService->getEmployees($filters, 15);

        return Inertia::render('Admin/Employee/Dashboard', [
            'employees' => $employees,
            'filters' => $filters,
        ]);
    }

    public function salaryDashboard(Request $request)
    {
        return Inertia::render('Admin/Employee/Salary/Dashboard', [
            'employees' => Employee::all(),
        ]);
    }

    /**
     * API: Lấy danh sách nhân viên
     */
    public function apiIndex(Request $request)
    {
        $employees = Employee::with(['user', 'jobTitle', 'department', 'branch'])
            ->when($request->search, function ($query, $search) {
                $query->where('full_name', 'LIKE', "%{$search}%")
                    ->orWhere('employee_code', 'LIKE', "%{$search}%");
            })
            ->limit(50)
            ->get();

        return response()->json($employees);
    }
    /**
     * Lưu nhân viên mới
     */
    public function store(StoreEmployeeRequest $request)
    {
        try {
            $validated = $request->validated();
            // Debug: Kiểm tra dữ liệu đã validate
            \Log::info('Employee creation data:', $validated);
            \Log::info('Branch ID:', ['branch_id' => $validated['branch_id'] ?? 'null']);
            
            $employee = $this->employeeService->createEmployee($validated);

            // Trả về JSON nếu request là AJAX/axios
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Thêm nhân viên thành công!',
                    'employee' => $employee
                ], 201);
            }

            return redirect()->back()->with('success', 'Thêm nhân viên thành công!');
        } catch (Exception $e) {
            // Log lỗi để debug
            \Log::error('Error creating employee: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            // Trả về JSON nếu request là AJAX/axios
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Hiển thị chi tiết nhân viên
     */
    public function show($id)
    {
        $employee = $this->employeeService->getEmployeeDetail($id);

        return Inertia::render('Admin/Employees/Show', [
            'employee' => $employee,
        ]);
    }

    /**
     * Hiển thị form sửa nhân viên
     */
    public function edit($id)
    {
        $employee = $this->employeeService->getEmployeeDetail($id);

        return Inertia::render('Admin/Employees/Edit', [
            'employee' => $employee,
            'jobTitles' => JobTitle::all(),
            'departments' => Department::all(),
            'branches' => Branch::all(),
        ]);
    }

    /**
     * Cập nhật thông tin nhân viên
     */
    public function update(UpdateEmployeeRequest $request, $id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $this->employeeService->updateEmployee($employee, $request->validated());

            return redirect()->back()->with('success', 'Cập nhật nhân viên thành công!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $this->employeeService->deleteEmployee($employee);

            return response()->json([
                'success' => true,
                'message' => 'Nhân viên đã được xóa thành công'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy nhân viên với ID: ' . $id
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa nhân viên: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * API: Lấy dữ liệu phụ trợ cho form
     */
    public function getResources()
    {
        return response()->json([
            'job_titles' => JobTitle::all(),
            'departments' => Department::all(),
            'branches' => Branch::all(),
        ]);
    }

    /**
     * API: Generate mã nhân viên mới
     */
    public function generateCode()
    {
        return response()->json([
            'employee_code' => Employee::generateEmployeeCode(),
        ]);
    }
}
