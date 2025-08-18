<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\DrugRoute;
use App\Models\Manufacturer;
use App\Models\Position;
use Illuminate\Http\Request;

class SupportingEntityController extends Controller
{
    /**
     * Store a newly created DrugRoute (Đường dùng).
     */
    public function storeDrugRoute(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:drug_routes,name'
            ]);

            $route = DrugRoute::create([
                'name'=> trim($validated['name'])
            ]);

            return response()->json([
                'success'    => true,
                'drug_route' => $route,
                'message'    => 'Tạo đường dùng thành công!'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error creating drug route: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo đường dùng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created Manufacturer (Hãng sản xuất).
     */
    public function storeManufacturer(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:manufacturers,name'
            ], [
                'name.required' => 'Tên hãng sản xuất là bắt buộc',
                'name.unique' => 'Hãng sản xuất này đã tồn tại',
                'name.max' => 'Tên hãng sản xuất không được quá 255 ký tự'
            ]);

            $manufacturer = Manufacturer::create([
                'name'        => trim($validated['name'])
            ]);

            return response()->json([
                'success'      => true,
                'manufacturer' => [
                    'id' => $manufacturer->id,
                    'name' => $manufacturer->name,
                    'description' => $manufacturer->description
                ],
                'message'      => 'Tạo hãng sản xuất thành công!'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created Position (Vị trí).
     */
    public function storePosition(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:positions,name'
            ]);

            $position = Position::create([
                'name'=> trim($validated['name'])
            ]);

            return response()->json([
                'success'  => true,
                'position' => $position,
                'message'  => 'Tạo vị trí thành công!'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error creating position: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo vị trí: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all drug routes for select options.
     */
    public function getDrugRoutes() //lấy danh sách đường dùng
    {
        $drugRoutes = DrugRoute::all();
        
        return response()->json([
            'success' => true,
            'drug_routes' => $drugRoutes
        ]);
    }

    /**
     * Get all manufacturers for select options.
     */
    public function getManufacturers() //lấy danh sách hãng sản xuất
    {
        $manufacturers = Manufacturer::all();
        
        return response()->json([
            'success' => true,
            'manufacturers' => $manufacturers
        ]);
    }

    /**
     * Get all positions for select options.
     */
    public function getPositions() //lấy danh sách vị trí
    {
        $positions = Position::all();
        
        return response()->json([
            'success' => true,
            'positions' => $positions
        ]);
    }
}
