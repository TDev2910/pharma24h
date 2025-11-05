<?php

namespace App\Http\Controllers\Admin\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\Department;
use App\Models\JobTitle;
use App\Models\Branch;
use App\Services\EmployeeService;
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

        return Inertia::render('Admin/Employees/Index', [
            'employees' => $employees,
            'filters' => $filters,
        ]);
    }

    /**
     * API: Lấy danh sách nhân viên (cho dropdown, autocomplete...)
     */
    public function apiIndex(Request $request)
    {
        $employees = Employee::with(['user', 'jobTitle', 'department'])
            ->when($request->search, function($query, $search) {
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
            $employee = $this->employeeService->createEmployee($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Thêm nhân viên thành công!',
                'employee' => $employee,
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
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
            'departments' => Department::all(),
            'job_titles' => JobTitle::all(),
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

    /**
     * Xóa nhân viên
     */
    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $this->employeeService->deleteEmployee($employee);

            return redirect()->back()->with('success', 'Xóa nhân viên thành công!');

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * API: Lấy dữ liệu phụ trợ cho form (Departments, Positions, Branches)
     */
    public function getResources()
    {
        return response()->json([
            'departments' => Department::all(),
            'job_titles' => JobTitle::all(),
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
