<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Shipping\GHNService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GHNController extends Controller
{
    protected GHNService $ghnService;

    public function __construct(GHNService $ghnService)
    {
        $this->ghnService = $ghnService;
    }

    /**
     * Tạo đơn hàng GHN
     */
    public function createShippingOrder(Request $request, Order $order)
    {
        // Kiểm tra đơn hàng có phải shipping không
        if (!$order->isShipping()) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng này không phải đơn giao hàng'
            ], 400);
        }

        // Kiểm tra đã có đơn GHN chưa
        if ($order->ghn_order_code) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng đã có mã vận đơn GHN: ' . $order->ghn_order_code
            ], 400);
        }

        // Kiểm tra có district_id và ward_code chưa
        if (!$order->district_id || !$order->ward_code) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng thiếu thông tin địa chỉ (district_id hoặc ward_code). Vui lòng cập nhật địa chỉ giao hàng.'
            ], 400);
        }

        try {
            $result = $this->ghnService->createOrder($order);

            if ($result['success']) {
                // Cập nhật thông tin GHN vào Order
                $order->ghn_order_code = $result['order_code'];
                $order->ghn_fee = $result['total_fee'];
                $order->ghn_expected_delivery_time = $result['expected_delivery_time'] ?? null;
                $order->ghn_status = 'ready_to_pick';
                $order->ghn_created_at = now();
                
                // Lưu tracking URL nếu có
                if (isset($result['data']['tracking_url'])) {
                    $order->ghn_tracking_url = $result['data']['tracking_url'];
                }
                
                $order->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Tạo đơn GHN thành công!',
                    'data' => [
                        'ghn_order_code' => $order->ghn_order_code,
                        'ghn_fee' => $order->ghn_fee,
                        'expected_delivery_time' => $order->ghn_expected_delivery_time,
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Lỗi tạo đơn GHN'
            ], 400);

        } catch (\Exception $e) {
            Log::error('GHN Controller Error - createShippingOrder: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tính phí vận chuyển
     */
    public function getShippingFee(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($request->order_id);

        if (!$order->isShipping()) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng này không phải đơn giao hàng'
            ], 400);
        }

        if (!$order->district_id || !$order->ward_code) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng thiếu thông tin địa chỉ (district_id hoặc ward_code)'
            ], 400);
        }

        $result = $this->ghnService->calculateShippingFee($order);

        return response()->json($result);
    }

    /**
     * Lấy danh sách tỉnh/thành
     */
    public function getProvinces()
    {
        $result = $this->ghnService->getProvinces();
        
        // Log để debug
        if (!$result['success']) {
            Log::warning('GHN getProvinces failed', [
                'result' => $result
            ]);
        }
        
        return response()->json($result);
    }

    /**
     * Lấy danh sách quận/huyện
     */
    public function getDistricts(Request $request)
    {
        $request->validate([
            'province_id' => 'required|integer',
        ]);

        $result = $this->ghnService->getDistricts($request->province_id);
        return response()->json($result);
    }

    /**
     * Lấy danh sách phường/xã
     */
    public function getWards(Request $request)
    {
        $request->validate([
            'district_id' => 'required|integer',
        ]);

        $result = $this->ghnService->getWards($request->district_id);
        return response()->json($result);
    }

    /**
     * Lấy thông tin tracking đơn hàng
     */
    public function trackOrder(Order $order)
    {
        if (!$order->ghn_order_code) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng chưa có mã vận đơn GHN'
            ], 400);
        }

        $result = $this->ghnService->getOrderInfo($order->ghn_order_code);

        if ($result['success']) {
            // Cập nhật trạng thái nếu có thay đổi
            if (isset($result['data']['status'])) {
                $order->ghn_status = $result['data']['status'];
                $order->save();
            }
        }

        return response()->json($result);
    }

    /**
     * Test kết nối GHN API
     */
    public function testConnection()
    {
        try {
            $result = $this->ghnService->getProvinces();
            return response()->json([
                'success' => true,
                'ghn_api_result' => $result,
                'config' => [
                    'base_url' => config('services.ghn.base_url'),
                    'token_set' => !empty(config('services.ghn.token')),
                    'token_length' => strlen(config('services.ghn.token') ?? ''),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Map địa chỉ (tên) sang GHN ID
     */
    public function mapAddressToGHN(Request $request)
    {
        $request->validate([
            'province' => 'required|string',
            'district' => 'required|string',
            'ward' => 'required|string',
        ]);

        try {
            // Lấy danh sách provinces từ GHN
            $provincesResult = $this->ghnService->getProvinces();
            
            if (!$provincesResult['success']) {
                $errorMessage = $provincesResult['message'] ?? 'Không thể lấy danh sách tỉnh/thành từ GHN';
                Log::error('GHN Map Address - getProvinces failed', [
                    'error' => $errorMessage,
                    'request' => $request->all()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }

            // Tìm province ID - cải thiện matching
            $provinceId = null;
            $provinceName = $this->normalizeProvinceName($request->province);
            
            foreach ($provincesResult['data'] as $province) {
                $ghnProvinceName = $this->normalizeProvinceName($province['ProvinceName']);
                
                // Exact match
                if (strcasecmp($ghnProvinceName, $provinceName) === 0) {
                    $provinceId = $province['ProvinceID'];
                    break;
                }
                
                // Contains match
                if (stripos($ghnProvinceName, $provinceName) !== false || 
                    stripos($provinceName, $ghnProvinceName) !== false) {
                    $provinceId = $province['ProvinceID'];
                    break;
                }
            }

            if (!$provinceId) {
                Log::warning('GHN Map Address - Province not found', [
                    'requested' => $request->province,
                    'normalized' => $provinceName
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy tỉnh/thành phố "' . $request->province . '" trong GHN'
                ], 404);
            }

            // Lấy danh sách districts từ GHN
            $districtsResult = $this->ghnService->getDistricts($provinceId);
            
            if (!$districtsResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể lấy danh sách quận/huyện từ GHN'
                ], 400);
            }

            // Tìm district ID - cải thiện matching
            $districtId = null;
            $districtName = $this->normalizeDistrictName($request->district);
            
            foreach ($districtsResult['data'] as $district) {
                $ghnDistrictName = $this->normalizeDistrictName($district['DistrictName']);
                
                // Exact match
                if (strcasecmp($ghnDistrictName, $districtName) === 0) {
                    $districtId = $district['DistrictID'];
                    break;
                }
                
                // Contains match
                if (stripos($ghnDistrictName, $districtName) !== false || 
                    stripos($districtName, $ghnDistrictName) !== false) {
                    $districtId = $district['DistrictID'];
                    break;
                }
            }

            if (!$districtId) {
                Log::warning('GHN Map Address - District not found', [
                    'province_id' => $provinceId,
                    'requested' => $request->district,
                    'normalized' => $districtName
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy quận/huyện "' . $request->district . '" trong GHN'
                ], 404);
            }

            // Lấy danh sách wards từ GHN
            $wardsResult = $this->ghnService->getWards($districtId);
            
            if (!$wardsResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể lấy danh sách phường/xã từ GHN'
                ], 400);
            }

            // Tìm ward code - cải thiện matching
            $wardCode = null;
            $wardName = $this->normalizeWardName($request->ward);
            
            foreach ($wardsResult['data'] as $ward) {
                $ghnWardName = $this->normalizeWardName($ward['WardName']);
                
                // Exact match
                if (strcasecmp($ghnWardName, $wardName) === 0) {
                    $wardCode = $ward['WardCode'];
                    break;
                }
                
                // Contains match
                if (stripos($ghnWardName, $wardName) !== false || 
                    stripos($wardName, $ghnWardName) !== false) {
                    $wardCode = $ward['WardCode'];
                    break;
                }
            }

            if (!$wardCode) {
                Log::warning('GHN Map Address - Ward not found', [
                    'district_id' => $districtId,
                    'requested' => $request->ward,
                    'normalized' => $wardName
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy phường/xã "' . $request->ward . '" trong GHN'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'district_id' => $districtId,
                    'ward_code' => $wardCode
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('GHN Controller Error - mapAddressToGHN: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Chuẩn hóa tên tỉnh/thành phố để so sánh
     */
    private function normalizeProvinceName($name)
    {
        $name = trim($name);
        // Xử lý các trường hợp đặc biệt
        $replacements = [
            'Thành phố Hồ Chí Minh' => 'Hồ Chí Minh',
            'TP. Hồ Chí Minh' => 'Hồ Chí Minh',
            'TP Hồ Chí Minh' => 'Hồ Chí Minh',
            'HCM' => 'Hồ Chí Minh',
            'Thành phố Hà Nội' => 'Hà Nội',
            'TP. Hà Nội' => 'Hà Nội',
            'TP Hà Nội' => 'Hà Nội',
        ];
        
        foreach ($replacements as $key => $value) {
            if (stripos($name, $key) !== false) {
                $name = $value;
                break;
            }
        }
        
        return $name;
    }

    /**
     * Chuẩn hóa tên quận/huyện để so sánh
     */
    private function normalizeDistrictName($name)
    {
        $name = trim($name);
        // Loại bỏ các từ thừa
        $name = preg_replace('/^(Quận|Huyện|Thị xã|Thành phố)\s*/i', '', $name);
        return trim($name);
    }

    /**
     * Chuẩn hóa tên phường/xã để so sánh
     */
    private function normalizeWardName($name)
    {
        $name = trim($name);
        // Loại bỏ các từ thừa (Phường, Xã, Thị trấn)
        $name = preg_replace('/^(Phường|Xã|Thị trấn)\s*/i', '', $name);
        // Giữ lại số phường (ví dụ: "Phường 25" -> "25")
        return trim($name);
    }
}