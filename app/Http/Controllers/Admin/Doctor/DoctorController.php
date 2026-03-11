<?php

namespace App\Http\Controllers\Admin\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Core\Doctor\Ports\Inbound\DoctorUseCaseInterface;
use App\Core\Doctor\Domain\DTOs\DoctorData;
use App\Http\Requests\Doctor\StoreDoctorRequest;
use App\Http\Requests\Doctor\UpdateDoctorRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class DoctorController extends Controller
{
    public function __construct( 
        private DoctorUseCaseInterface $useCase
    ) {}
    /**
     * Display a listing of the resource (Vue.js page).
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $data = $this->useCase->getDashboardData($search, $perPage);

        return Inertia::render('Admin/Doctors/Dashboard', [
            'doctors' => $data['doctors'],
            'filters' => $request->only(['search', 'per_page']),
        ]);
    }

    /**
     * Get doctors data for API (JSON response).
     */
    public function getDoctors(Request $request)
    {
        try {
            $search = $request->input('search');
            $perPage = $request->input('per_page', 10);

            $data = $this->useCase->getDashboardData($search, $perPage);
            $doctors = $data['doctors'];

            return response()->json([
                'success' => true,
                'data' => $doctors->items(),
                'pagination' => [
                    'current_page' => $doctors->currentPage(),
                    'last_page' => $doctors->lastPage(),
                    'per_page' => $doctors->perPage(),
                    'total' => $doctors->total(),
                    'from' => $doctors->firstItem(),
                    'to' => $doctors->lastItem()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDoctorRequest $request)
    {
        $dto = $request->toDTO();
        $this->useCase->createDoctor($dto);
        return redirect()->back()->with('success','Thêm bác sĩ thành công');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = Doctor::find($id);
        if (!$user) return response()->json(['success' => false, 'message' => 'Không tìm thấy'], 404);
        return response()->json(['success' => true, 'data' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDoctorRequest $request, string $id)
    {
        $dto = $request->toDTO();
        $this->useCase->updateDoctor($id,$dto);
        return redirect()->back()->with('success', 'Cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->useCase->deleteDoctor($id);
        return redirect()->back()->with('success','Xóa thành công!');
    }

    /**
     * Generate a unique doctor code
     */
    public function generateDoctorCode()
    {
        $code = $this->useCase->generateDoctorCode();

        return response()->json([
            'success' => true,
            'code' => $code
        ]);
    }
}
