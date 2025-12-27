<?php

namespace App\Services\Shipping;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

class GHNService
{
    protected string $token;
    protected string $shopId;
    protected string $baseUrl;
    protected int $fromDistrictId;
    protected string $fromWardCode;
    protected int $defaultServiceType;

    public function __construct()
    {
        //gọi từ config/services.php
        $this->token = config('services.ghn.token'); //token của GHN
        $this->shopId = config('services.ghn.shop_id'); //id của shop GHN
        $this->baseUrl = config('services.ghn.base_url'); //url của API GHN
        $this->fromDistrictId = config('services.ghn.from_district_id'); //id của quận/huyện từ
        $this->fromWardCode = config('services.ghn.from_ward_code'); //code của phường/xã từ
        $this->defaultServiceType = config('services.ghn.default_service_type', 2); //loại dịch vụ mặc định là 2
    }

    /**
     * Lấy danh sách api tỉnh/thành phố
     */
    public function getProvinces(): array
    {
        try {
            //gọi API tỉnh/thành phố
            $url = $this->baseUrl . '/master-data/province';

            $response = Http::withHeaders([
                'Token' => $this->token
            ])->get($url);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'Lỗi kết nối API GHN: HTTP ' . $response->status()
                ];
            }

            $data = $response->json();

            if (!isset($data['code'])) {
                return [
                    'success' => false,
                    'message' => 'Định dạng response không hợp lệ từ GHN API'
                ];
            }

            if ($data['code'] == 200 && isset($data['data'])) {
                return [
                    'success' => true,
                    'data' => $data['data']
                ];
            }

            $errorMessage = $data['message'] ?? ($data['code_message'] ?? 'Lỗi không xác định');

            return [
                'success' => false,
                'message' => $errorMessage
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Lỗi kết nối API: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Lấy danh sách quận/huyện theo tỉnh/thành
     */
    public function getDistricts(int $provinceId): array
    {
        try {
            $response = Http::withHeaders([
                'Token' => $this->token,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/master-data/district', [
                'province_id' => $provinceId
            ]);

            $data = $response->json();

            if ($data['code'] == 200) {
                return [
                    'success' => true,
                    'data' => $data['data']
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Lỗi không xác định'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Lỗi kết nối API: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Lấy danh sách phường/xã theo quận/huyện
     */
    public function getWards(int $districtId): array
    {
        try {
            $response = Http::withHeaders([
                'Token' => $this->token
            ])->get($this->baseUrl . '/master-data/ward', [
                'district_id' => $districtId
            ]);

            $data = $response->json();

            if ($data['code'] == 200) {
                return [
                    'success' => true,
                    'data' => $data['data']
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Lỗi không xác định'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Lỗi kết nối API: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Tính phí vận chuyển
     */
    public function calculateShippingFee(Order $order): array
    {
        try {
            // Lấy thông tin địa chỉ từ đơn hàng
            $toDistrictId = $this->getDistrictIdFromOrder($order);
            $toWardCode = $this->getWardCodeFromOrder($order);

            if (!$toDistrictId || !$toWardCode) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin địa chỉ giao hàng. Vui lòng kiểm tra district_id và ward_code trong đơn hàng.'
                ];
            }

            // Tính tổng khối lượng và giá trị đơn hàng
            $weight = $this->calculateWeight($order);
            $codAmount = $this->getCodAmount($order);

            $response = Http::withHeaders([
                'Token' => $this->token,
                'ShopId' => $this->shopId,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/v2/shipping-order/fee',
            [
                'from_district_id' => $this->fromDistrictId, //id của quận/huyện lấy từ config/services.php
                'from_ward_code' => $this->fromWardCode, //code của phường/xã lấy từ config/services.php
                'to_district_id' => $toDistrictId, //id của quận/huyện từ đơn hàng
                'to_ward_code' => $toWardCode, //code của phường/xã từ đơn hàng
                'service_type_id' => $this->defaultServiceType,
                'weight' => $weight, //mặc định 100g mỗi sản phẩm
                'cod_amount' => $codAmount,
            ]);

            $data = $response->json();

            if ($data['code'] == 200) {
                return [
                    'success' => true,
                    'fee' => $data['data']['total'],
                    'service_fee' => $data['data']['service_fee'] ?? 0,
                    'insurance_fee' => $data['data']['insurance_fee'] ?? 0,
                    'data' => $data['data']
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Lỗi tính phí vận chuyển'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Lỗi kết nối API: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Tạo đơn hàng GHN
     */
    public function createOrder(Order $order): array
    {
        try {
            // Lấy thông tin địa chỉ
            $toDistrictId = $this->getDistrictIdFromOrder($order);
            $toWardCode = $this->getWardCodeFromOrder($order);

            if (!$toDistrictId || !$toWardCode) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin địa chỉ giao hàng. Vui lòng kiểm tra district_id và ward_code trong đơn hàng.'
                ];
            }

            // Kiểm tra thông tin địa chỉ gửi
            if (!$this->fromDistrictId || !$this->fromWardCode) {
                return [
                    'success' => false,
                    'message' => 'Thiếu cấu hình địa chỉ gửi hàng. Vui lòng kiểm tra GHN_FROM_DISTRICT_ID và GHN_FROM_WARD_CODE trong file .env'
                ];
            }

            // Tính khối lượng
            $weight = $this->calculateWeight($order);
            $codAmount = $this->getCodAmount($order);

            // Lấy danh sách items
            $items = [];
            foreach ($order->items as $item) {
                $items[] = [
                    'name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'weight' => 100, // Mặc định 100g mỗi sản phẩm
                ];
            }

            $url = $this->baseUrl . '/v2/shipping-order/create'; // gọi API tạo đơn hàng GHN
            $payload = [
                'payment_type_id' => 1,
                'note' => $order->note ?? '',
                'required_note' => 'CHOTHUHANG',
                'from_name' => config('app.name', 'Nhà thuốc Sức Khỏe 24h'),
                'from_phone' => config('app.phone', ''),
                'from_address' => config('app.address', ''),
                'from_ward_code' => $this->fromWardCode,
                'from_district_id' => $this->fromDistrictId,
                'to_name' => $order->customer_name,
                'to_phone' => $order->customer_phone,
                'to_address' => $order->shipping_address,
                'to_ward_code' => $toWardCode,
                'to_district_id' => $toDistrictId,
                'cod_amount' => $codAmount,
                'weight' => $weight,
                'length' => 10,
                'width' => 10,
                'height' => 10,
                'service_type_id' => $this->defaultServiceType,
                'items' => $items,
            ];

            $response = Http::withHeaders([
                'Token' => $this->token,
                'ShopId' => $this->shopId,
                'Content-Type' => 'application/json'
            ])->post($url, $payload);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'Lỗi kết nối API GHN: HTTP ' . $response->status()
                ];
            }

            $data = $response->json();

            if (!isset($data['code'])) {
                return [
                    'success' => false,
                    'message' => 'Định dạng response không hợp lệ từ GHN API'
                ];
            }

            if ($data['code'] == 200 && isset($data['data'])) {
                return [
                    'success' => true,
                    'order_code' => $data['data']['order_code'],
                    'total_fee' => $data['data']['total_fee'],
                    'expected_delivery_time' => $data['data']['expected_delivery_time'] ?? null,
                    'data' => $data['data']
                ];
            }

            $errorMessage = $data['message'] ?? ($data['code_message'] ?? 'Lỗi tạo đơn hàng GHN');

            return [
                'success' => false,
                'message' => $errorMessage,
                'code_message' => $data['code_message'] ?? null
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Lỗi kết nối API: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Lấy thông tin đơn hàng GHN
     */
    public function getOrderInfo(string $ghnOrderCode): array
    {
        try {
            $response = Http::withHeaders([
                'Token' => $this->token,
                'ShopId' => $this->shopId,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/v2/shipping-order/detail', [
                'order_code' => $ghnOrderCode
            ]);

            $data = $response->json();

            if ($data['code'] == 200) {
                return [
                    'success' => true,
                    'data' => $data['data']
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Lỗi lấy thông tin đơn hàng'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Lỗi kết nối API: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Hủy đơn hàng GHN
     */
    public function cancelOrder(string $ghnOrderCode, string $reason = ''): array
    {
        try {
            $response = Http::withHeaders([
                'Token' => $this->token,
                'ShopId' => $this->shopId,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/v2/switch-status/cancel', [
                'order_codes' => [$ghnOrderCode],
                'cancel_reason' => $reason ?: 'Khách hàng yêu cầu hủy'
            ]);

            $data = $response->json();

            if ($data['code'] == 200) {
                return [
                    'success' => true,
                    'message' => 'Hủy đơn hàng thành công'
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Lỗi hủy đơn hàng'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Lỗi kết nối API: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Tính khối lượng đơn hàng (gram)
     */
    protected function calculateWeight(Order $order): int
    {
        // Mặc định 100g mỗi sản phẩm
        $weightPerItem = 100;
        $totalItems = $order->items->sum('quantity');

        return $totalItems * $weightPerItem;
    }

    public function calculateFee(int $toDistrictId, string $toWardCode, int $weight , int $insuranceValue): array
    {
        try {
            // Validate dữ liệu đầu vào cơ bản
            if (!$toDistrictId || !$toWardCode) {
                return [
                    'success' => false,
                    'message' => 'Thiếu thông tin quận/huyện hoặc phường/xã'
                ];
            }

            $response = Http::withHeaders([
                'Token' => $this->token,
                'ShopId' => $this->shopId,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/v2/shipping-order/fee',
            [
                'from_district_id' => $this->fromDistrictId,
                'from_ward_code' => $this->fromWardCode,
                'to_district_id' => $toDistrictId,
                'to_ward_code' => $toWardCode,
                'service_type_id' => $this->defaultServiceType,
                'weight' => $weight > 0 ? $weight : 100, // Ít nhất 100g
                'insurance_value' => $insuranceValue, // Giá trị đơn hàng để tính bảo hiểm
                'coupon' => null
            ]);

            $data = $response->json();

            if ($data['code'] == 200) {
                return [
                    'success' => true,
                    'total' => $data['data']['total'], // Tổng phí ship (đã bao gồm các loại phí)
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Lỗi tính phí từ GHN'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Lỗi kết nối GHN: ' . $e->getMessage()
            ];
        }
    }
    /**
     * Lấy District ID từ Order
     */
    protected function getDistrictIdFromOrder(Order $order): ?int
    {
        // Ưu tiên lấy từ district_id nếu có
        if (isset($order->district_id) && $order->district_id) {
            return (int) $order->district_id;
        }
        return null;
    }

    /**
     * Lấy Ward Code từ Order
     */
    protected function getWardCodeFromOrder(Order $order): ?string
    {
        // Ưu tiên lấy từ ward_code nếu có
        if (isset($order->ward_code) && $order->ward_code) {
            return $order->ward_code;
        }

        return null;
    }

    /**
     * Lấy số tiền COD dạng integer để gửi GHN
     */
    protected function getCodAmount(Order $order): int
    {
        // Nếu thanh toán qua VNPAY (hoặc bất kỳ hình thức online nào đã 'paid')
        // Thì COD = 0 (Shipper chỉ giao hàng, không thu tiền)
        if ($order->payment_method === 'vnpay') {
            return 0;
        }

        // Nếu là COD, thu toàn bộ tổng tiền đơn hàng (bao gồm cả phí ship hiển thị cho khách)
        if ($order->payment_method === 'cod') {
            $amount = $order->total_amount ?? 0;
            return (int) round((float) $amount);
        }

        return 0; // Mặc định 0 cho an toàn
    }
}
