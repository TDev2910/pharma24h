<?php

namespace App\Http\Controllers\Admin\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Admin/Doctors/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'doctorCode' => 'required|string|size:6|unique:doctors,doctor_code',
            'gender' => 'required|in:male,female',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'specialty' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'province' => 'nullable|array',
            'ward' => 'nullable|array',
            'degree' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ], 
        
        [
            'province.array' => 'Tỉnh/thành phố phải là object',
            'ward.array' => 'Quận/huyện phải là object'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Map form data to database fields
            $genderMap = [
                'male' => 'Male',
                'female' => 'Female'
            ];
            
            // Handle province and ward data (save names instead of codes)
            $provinceName = $request->province['name'] ?? null;
            $wardName = $request->ward['name'] ?? null;
            
            $doctorData = [
                'doctor_code' => $request->doctorCode,
                'name' => $request->name,
                'gender' => $genderMap[$request->gender] ?? 'Male',
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'province_district' => $provinceName, // Lưu tên tỉnh thành
                'ward_commune' => $wardName, // Lưu tên phường/xã
                'specialty' => $request->specialty,
                'qualification' => $request->degree,
                'note' => $request->notes,
                'status' => 'active'
            ];

            // Create the doctor
            $doctor = Doctor::create($doctorData);

            return response()->json([
                'success' => true,
                'message' => 'Bác sĩ đã được thêm thành công',
                'data' => $doctor
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm bác sĩ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Generate a unique doctor code
     */
    public function generateDoctorCode()
    {
        $code = Doctor::generateDoctorCode();

        return response()->json([
            'success' => true,
            'code' => $code
        ]);
    }
}
