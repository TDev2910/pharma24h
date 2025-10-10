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
     * Display a listing of the resource (Vue.js page).
     */
    public function index()
    {
        return Inertia::render('Admin/Doctors/Dashboard');
    }

    /**
     * Get doctors data for API (JSON response).
     */
    public function getDoctors(Request $request)
    {
        try {
            $query = Doctor::query();
            
            // Search functionality
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('doctor_code', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
                });
            }
            
            // Pagination
            $doctors = $query->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 10));
            
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
                'message' => 'Có lỗi xảy ra khi tải danh sách bác sĩ: ' . $e->getMessage()
            ], 500);
        }
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
                'avatar' => $request->avatar, // Lưu path avatar
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
        try {
            // Validate ID format
            if (!is_numeric($id) || $id <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID bác sĩ không hợp lệ'
                ], 400);
            }

            $doctor = Doctor::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $doctor
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bác sĩ với ID: ' . $id
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải thông tin bác sĩ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'doctorCode' => 'required|string|size:6|unique:doctors,doctor_code,' . $id,
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
        try 
        {
            $doctor = Doctor::findOrFail($id);
            $genderMap = [
                'male' => 'Male',
                'female' => 'Female'
            ];

            $provinceName = $request->province['name'] ?? null;
            $wardName = $request->ward['name'] ?? null;

            $doctorData = [
                'doctor_code' => $request->doctorCode,
                'name' => $request->name,
                'gender' => $genderMap[$request->gender] ?? 'Male',
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'province_district' => $provinceName,
                'ward_commune' => $wardName,
                'specialty' => $request->specialty,
                'qualification' => $request->degree,
                'note' => $request->notes,
                'avatar' => $request->avatar,
                'status' => 'active'
            ];
            $doctor->update($doctorData);

            return response()->json([
                'success' => true,
                'message' => 'Thông tin bác sĩ đã được cập nhật thành công',
                'data' => $doctor
            ]);

        } 
        catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bác sĩ với ID: ' . $id
            ], 404);
            
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật bác sĩ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $doctor = Doctor::findOrFail($id);
            $doctor->delete();

            return response()->json([
                'success' => true,
                'message' => 'Bác sĩ đã được xóa thành công'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bác sĩ với ID: ' . $id
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa bác sĩ: ' . $e->getMessage()
            ], 500);
        }
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

    //Upload avatar
    public function uploadAvatar(Request $request)
    {
        try {
            // Validate file
            $validator = Validator::make($request->all(), [
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ], [
                'avatar.required' => 'Vui lòng chọn ảnh',
                'avatar.image' => 'File phải là ảnh',
                'avatar.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif',
                'avatar.max' => 'Kích thước ảnh không được vượt quá 2MB'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                
                // Tạo tên file unique
                $filename = 'doctor_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                // Lưu vào storage/app/public/avatars/doctors/
                $path = $file->storeAs('avatars/doctors', $filename, 'public');
                
                // Trả về URL để hiển thị
                $url = asset('storage/' . $path);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Upload ảnh thành công',
                    'data' => [
                        'filename' => $filename,
                        'path' => $path,
                        'url' => $url
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy file ảnh'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
