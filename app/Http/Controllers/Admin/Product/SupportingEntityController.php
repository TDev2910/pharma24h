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
     * Get all DrugRoutes (Đường dùng).
     */
    public function indexDrugRoute()
    {
        try {
            $drugRoutes = DrugRoute::orderBy('name')->get();
            
            return response()->json([
                'success' => true,
                'data' => $drugRoutes
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching drug routes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy danh sách đường dùng',
                'data' => []
            ], 500);
        }
    }

    public function indexManufacturer()
    {
        try {
            $manufacturers = Manufacturer::orderBy('name')->get();

            return response()->json([
                'success' => true,
                'data' => $manufacturers
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching manufacturers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy danh sách hãng sản xuất',
                'data' => []
            ], 500);
        }
    }

    public function indexPosition()
    {
        try {
            $positions = Position::orderBy('name')->get();

            return response()->json([
                'success' => true,
                'data' => $positions
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching positions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy danh sách vị trí',
                'data' => []
            ], 500);
        }
    }

    /**
     * Store a newly created DrugRoute (Đường dùng).
     */
    public function storeDrugRoute(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:drug_routes,name',
                'description' => 'nullable|string|max:1000'
            ]);

            $route = DrugRoute::create([
                'name' => trim($validated['name']),
                'description' => $validated['description'] ? trim($validated['description']) : null
            ]);

            return response()->json
            ([
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
                'name' => 'required|string|max:255|unique:manufacturers,name',
                'description' => 'nullable|string|max:1000'
            ], [
                'name.required' => 'Tên hãng sản xuất là bắt buộc',
                'name.unique' => 'Hãng sản xuất này đã tồn tại',
                'name.max' => 'Tên hãng sản xuất không được quá 255 ký tự'
            ]);

            $manufacturer = Manufacturer::create([
                'name' => trim($validated['name']),
                'description' => $validated['description'] ? trim($validated['description']) : null
            ]);

            return response()->json([
                'success'      => true,
                'manufacturer' => [
                    'id' => $manufacturer->id,
                    'name' => $manufacturer->name,
                    'description' => $manufacturer->description
                ],
                'message'=> 'Tạo hãng sản xuất thành công!'
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
                'name' => 'required|string|max:255|unique:positions,name',
                'description' => 'nullable|string|max:1000'
            ]);

            $position = Position::create([
                'name' => trim($validated['name']),
                'description' => $validated['description'] ? trim($validated['description']) : null
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

    /**
     * Update the specified DrugRoute.
     */
    public function updateDrugRoute(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:drug_routes,name,' . $id,
                'description' => 'nullable|string|max:1000'
            ]);

            $route = DrugRoute::findOrFail($id);
            $route->update([
                'name' => trim($validated['name']),
                'description' => $validated['description'] ? trim($validated['description']) : null
            ]);

            return response()->json([
                'success' => true,
                'drug_route' => $route,
                'message' => 'Cập nhật đường dùng thành công!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error updating drug route: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật đường dùng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete the specified DrugRoute.
     */
    public function destroyDrugRoute($id)
    {
        try {
            $route = DrugRoute::findOrFail($id);
            $route->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa đường dùng thành công!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting drug route: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa đường dùng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified Manufacturer.
     */
    public function updateManufacturer(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:manufacturers,name,' . $id,
                'description' => 'nullable|string|max:1000'
            ], [
                'name.required' => 'Tên hãng sản xuất là bắt buộc',
                'name.unique' => 'Hãng sản xuất này đã tồn tại',
                'name.max' => 'Tên hãng sản xuất không được quá 255 ký tự'
            ]);

            $manufacturer = Manufacturer::findOrFail($id);
            $manufacturer->update([
                'name' => trim($validated['name']),
                'description' => $validated['description'] ? trim($validated['description']) : null
            ]);

            return response()->json([
                'success' => true,
                'manufacturer' => $manufacturer,
                'message' => 'Cập nhật hãng sản xuất thành công!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error updating manufacturer: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật hãng sản xuất: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete the specified Manufacturer.
     */
    public function destroyManufacturer($id)
    {
        try {
            $manufacturer = Manufacturer::findOrFail($id);
            $manufacturer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa hãng sản xuất thành công!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting manufacturer: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa hãng sản xuất: ' . $e->getMessage()
            ], 500);
        }
    }
    public function updatePosition(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:positions,name,' . $id,
                'description' => 'nullable|string|max:1000'
            ], [
                'name.required' => 'Tên vị trí là bắt buộc',
                'name.unique' => 'Vị trí này đã tồn tại',
                'name.max' => 'Tên vị trí không được quá 255 ký tự'
            ]);

            $position = Position::findOrFail($id);
            $position->update([
                'name' => trim($validated['name']),
                'description' => $validated['description'] ? trim($validated['description']) : null
            ]);

            return response()->json([
                'success' => true,
                'position' => $position,
                'message' => 'Cập nhật vị trí thành công!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error updating position: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật vị trí: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete the specified Position.
     */
    public function destroyPosition($id)
    {
        try {
            $position = Position::findOrFail($id);
            $position->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa vị trí thành công!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting position: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa vị trí: ' . $e->getMessage()
            ], 500);
        }
    }
}
