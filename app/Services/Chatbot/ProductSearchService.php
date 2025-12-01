<?php

namespace App\Services\Chatbot;

use App\Models\Medicine;
use App\Models\Goods;
use App\Models\Service;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ProductSearchService
{
    public function search(string $message): array
    {
        // 1. Trích xuất từ khóa và giá
        $keywords    = $this->extractKeywords($message);
        $priceRange  = $this->extractPriceRange($message);
        $searchType  = $this->detectSearchType($message);

        // 2. Lọc bỏ các từ khóa quá chung chung (thuốc, sản phẩm...)
        $keywords = $this->filterGenericKeywords($keywords);

        // 3. [QUAN TRỌNG] Logic "Thoát sớm" (Early Exit)
        // Nếu sau khi lọc mà không còn từ khóa nào (ví dụ user chỉ chat "Hi", "Xin chào")
        // VÀ không có khoảng giá -> Trả về rỗng ngay. Không query DB.
        if (empty($keywords) && empty($priceRange)) {
            return [
                'medicines' => collect([]),
                'goods'     => collect([]),
                'services'  => collect([]),
            ];
        }

        $result = [
            'medicines' => collect([]),
            'goods'     => collect([]),
            'services'  => collect([]),
        ];

        // 4. Query Database (chỉ chạy khi có keywords hoặc priceRange)
        if ($searchType === 'product' || $searchType === 'all') {
            if ($priceRange && empty($keywords)) {
                $result['medicines'] = $this->searchMedicinesByPrice($priceRange);
                $result['goods']     = $this->searchGoodsByPrice($priceRange);
            } elseif (!empty($keywords)) {
                $result['medicines'] = $this->searchMedicines($keywords, $priceRange);
                $result['goods']     = $this->searchGoods($keywords, $priceRange);
            } elseif ($priceRange) {
                $result['medicines'] = $this->searchMedicinesByPrice($priceRange);
                $result['goods']     = $this->searchGoodsByPrice($priceRange);
            }
        }

        if ($searchType === 'service' || $searchType === 'all') {
            if ($priceRange && empty($keywords)) {
                $result['services'] = $this->searchServicesByPrice($priceRange);
            } elseif (!empty($keywords)) {
                $result['services'] = $this->searchServices($keywords, $priceRange);
            } elseif ($priceRange) {
                $result['services'] = $this->searchServicesByPrice($priceRange);
            }
        }

        return $result;
    }

    //tìm kiếm thuốc
    private function searchMedicines(array $keywords, ?array $priceRange = null): Collection
    {
        $query = Medicine::where('ban_truc_tiep', true);

        // Sử dụng logic and: duyệt qua từng từ khóa
        // Sản phẩm phải thỏa mãn từ khóa 1 VÀ từ khóa 2 VÀ ...
        foreach ($keywords as $keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('ten_thuoc', 'like', '%' . $keyword . '%')
                    ->orWhere('ten_viet_tat', 'like', '%' . $keyword . '%')
                    ->orWhere('ma_hang', 'like', '%' . $keyword . '%')
                    ->orWhere('ma_vach', 'like', '%' . $keyword . '%')
                    ->orWhere('hoat_chat', 'like', '%' . $keyword . '%'); // Thêm tìm theo hoạt chất
            });
        }

        if ($priceRange) {
            $query->whereBetween('gia_ban', [$priceRange['min'], $priceRange['max']]);
        }

        // Giới hạn 5 kết quả để phản hồi nhanh và chính xác hơn bàng limit 5
        return $query->with(['category', 'manufacturer'])->limit(5)->get();
    }

    //tìm kiếm vật tư ý té
    private function searchGoods(array $keywords, ?array $priceRange = null): Collection
    {
        $query = Goods::where('ban_truc_tiep', true);

        foreach ($keywords as $keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('ten_hang_hoa', 'like', '%' . $keyword . '%')
                    ->orWhere('mo_ta', 'like', '%' . $keyword . '%');
            });
        }

        if ($priceRange) {
            $query->whereBetween('gia_ban', [$priceRange['min'], $priceRange['max']]);
        }

        return $query->with(['category', 'manufacturer'])->limit(5)->get();
    }

    //tìm kiếm dịch vụ
    private function searchServices(array $keywords, ?array $priceRange = null): Collection
    {
        $query = Service::where('trang_thai', 'kich_hoat');

        foreach ($keywords as $keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('ten_dich_vu', 'like', '%' . $keyword . '%')
                    ->orWhere('mo_ta', 'like', '%' . $keyword . '%');
            });
        }

        if ($priceRange) {
            $query->whereBetween('gia_dich_vu', [$priceRange['min'], $priceRange['max']]);
        }

        return $query->with(['category', 'doctor'])->limit(5)->get();
    }


    // xử lý từ khóa từ user nhập vào
    private function extractKeywords(string $message): array
    {
        $message = mb_strtolower($message, 'UTF-8');

        // Danh sách từ dừng (Stopwords) bao gồm cả từ chào hỏi
        $stopwords = [
            'bao', 'nhiêu', 'tiền', 'có', 'không', 'là', 'của', 'và',
            'cho', 'tôi', 'mua', 'được', 'thì', 'như', 'nào', 'gì',
            'về', 'này', 'đó', 'vậy', 'à', 'ạ', 'nhé', 'nha', 'từ', 'đến', 'tới',
            'tìm', 'sản', 'phẩm', 'muốn', 'cần',
            // Từ khóa chào hỏi xã giao (để tránh query DB)
            'hi', 'hello', 'xin', 'chào', 'shop', 'ad', 'admin', 'ơi', 'alo', 'hế', 'lô', 'giúp'
        ];

        $message = str_replace([',', '.', '?', '!', ';'], ' ', $message);

        $words = preg_split('/\s+/', $message);

        $keywords = array_filter($words, function ($word) use ($stopwords) {
            // Giữ lại từ >= 2 ký tự và không nằm trong stopwords
            return !in_array($word, $stopwords) && mb_strlen($word, 'UTF-8') >= 2;
        });

        return array_values($keywords);
    }

    public function extractProductImages(array $searchResults): array
    {
        $images = [];

        // 1. Lấy ảnh thuốc
        foreach ($searchResults['medicines'] as $medicine) {
            if (!empty($medicine->image)) {
                $images[] = [
                    'id'    => $medicine->id,
                    'name'  => $medicine->ten_thuoc,
                    'price' => $medicine->gia_ban_formatted ?? number_format($medicine->gia_ban) . ' đ',
                    'image' => asset('storage/' . $medicine->image),
                    'type'  => 'Thuốc'
                ];
            }
        }

        // 2. Lấy ảnh vật tư y tế
        foreach ($searchResults['goods'] as $goods) {
            if (!empty($goods->image)) {
                $images[] = [
                    'id'    => $goods->id,
                    'name'  => $goods->ten_hang_hoa,
                    'price' => $goods->gia_ban_formatted ?? number_format($goods->gia_ban) . ' đ',
                    'image' => asset('storage/' . $goods->image),
                    'type'  => 'Vật tư y tế'
                ];
            }
        }
        return $images;
    }

    private function extractPriceRange(string $message): ?array
    {
        $message  = mb_strtolower($message, 'UTF-8');
        $patterns = [
            '/từ\s*(\d+(?:[.,]\d+)?)\s*(?:ngàn|k|000)?\s*(?:vnđ|đ|vnd)?\s*(?:đến|tới|-)\s*(\d+(?:[.,]\d+)?)\s*(?:ngàn|k|000)?\s*(?:vnđ|đ|vnd)?/i',
            '/(\d+(?:[.,]\d+)?)\s*(?:ngàn|k|000)?\s*(?:vnđ|đ|vnd)?\s*(?:đến|tới|-)\s*(\d+(?:[.,]\d+)?)\s*(?:ngàn|k|000)?\s*(?:vnđ|đ|vnd)?/i',
            '/khoảng\s*(\d+(?:[.,]\d+)?)\s*(?:ngàn|k|000)?\s*(?:vnđ|đ|vnd)?\s*(?:đến|tới|-)\s*(\d+(?:[.,]\d+)?)\s*(?:ngàn|k|000)?\s*(?:vnđ|đ|vnd)?/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                $min = $this->normalizePrice($matches[1]);
                $max = $this->normalizePrice($matches[2]);
                if ($min && $max && $min <= $max) {
                    return ['min' => $min, 'max' => $max];
                }
            }
        }
        return null;
    }

    private function normalizePrice(string $price): ?int
    {
        $price = preg_replace('/[^\d.,]/', '', $price);
        $price = str_replace([',', '.'], '', $price);
        if (preg_match('/(\d+)\s*(?:k|ngàn)/i', $price, $m)) {
            return (int) $m[1] * 1000;
        }
        return $price ? (int) $price : null;
    }

    private function detectSearchType(string $message): string
    {
        $message         = mb_strtolower($message, 'UTF-8');
        $productKeywords = ['sản phẩm', 'thuốc', 'hàng hóa', 'kem', 'viên', 'siro', 'chai', 'hộp', 'hũ'];
        $serviceKeywords = ['dịch vụ', 'khám', 'tư vấn', 'bác sĩ', 'doctor'];

        $hasProduct = false;
        $hasService = false;

        foreach ($productKeywords as $keyword) {
            if (mb_strpos($message, $keyword) !== false) {
                $hasProduct = true;
                break;
            }
        }
        foreach ($serviceKeywords as $keyword) {
            if (mb_strpos($message, $keyword) !== false) {
                $hasService = true;
                break;
            }
        }

        if ($hasProduct && !$hasService) {
            return 'product';
        } elseif ($hasService && !$hasProduct) {
            return 'service';
        }
        return 'all';
    }

    private function filterGenericKeywords(array $keywords): array
    {
        $genericWords = [
            'thuốc', 'sản', 'phẩm', 'hàng', 'hóa',
            'tìm', 'cần', 'muốn', 'giá', 'chi', 'tiết', 'bán', 'mua'
        ];

        return array_filter($keywords, function ($keyword) use ($genericWords) {
            return !in_array($keyword, $genericWords);
        });
    }

    //tìm kiếm thuốc theo giá
    private function searchMedicinesByPrice(array $priceRange): Collection
    {
        return Medicine::where('ban_truc_tiep', true)
            ->whereBetween('gia_ban', [$priceRange['min'], $priceRange['max']])
            ->with(['category', 'manufacturer'])
            ->limit(5)
            ->get();
    }

    //tìm kiếm vật tư ý té theo giá
    private function searchGoodsByPrice(array $priceRange): Collection
    {
        return Goods::where('ban_truc_tiep', true)
            ->whereBetween('gia_ban', [$priceRange['min'], $priceRange['max']])
            ->with(['category', 'manufacturer'])
            ->limit(5)
            ->get();
    }

    //tìm kiếm dịch vụ theo giá
    private function searchServicesByPrice(array $priceRange): Collection
    {
        return Service::where('trang_thai', 'kich_hoat')
            ->whereBetween('gia_dich_vu', [$priceRange['min'], $priceRange['max']])
            ->with(['category', 'doctor'])
            ->limit(5)
            ->get();
    }

    public function formatForGemini(array $searchResults): string
    {
        $formatted = "THÔNG TIN SẢN PHẨM/DỊCH VỤ LIÊN QUAN:\n\n";

        // Kiểm tra nếu tất cả đều rỗng -> Trả về thông báo không tìm thấy
        if (
            $searchResults['medicines']->isEmpty() &&
            $searchResults['goods']->isEmpty() &&
            $searchResults['services']->isEmpty()
        ) {
            return "Không tìm thấy sản phẩm cụ thể nào trong hệ thống. Hãy tư vấn chung dựa trên kiến thức y khoa.";
        }

        if ($searchResults['medicines']->isNotEmpty()) {
            foreach ($searchResults['medicines'] as $index => $medicine) {
                $formatted .= "- Sản phẩm: {$medicine->ten_thuoc}\n";
                $formatted .= "  Giá: {$medicine->gia_ban_formatted} | Kho: " . ($medicine->ton_kho > 0 ? "Còn hàng" : "Hết") . "\n";
                $shortDesc = Str::limit($medicine->mo_ta ?? $medicine->cong_dung ?? 'Hỗ trợ điều trị', 150);
                $formatted .= "  Công dụng chính: {$shortDesc}\n";
                if ($medicine->mo_ta) {
                    $formatted .= "  [Thông tin chi tiết - Chỉ dùng khi khách hỏi sâu]: {$medicine->mo_ta}\n";
                }
                $formatted .= "---\n";
            }
        }

        if ($searchResults['goods']->isNotEmpty()) {
            foreach ($searchResults['goods'] as $index => $goods) {
                $formatted .= "- Sản phẩm: {$goods->ten_hang_hoa}\n";
                $formatted .= "  Giá: {$goods->gia_ban_formatted} | Kho: " . ($goods->ton_kho > 0 ? "Còn hàng" : "Hết") . "\n";
                $shortDesc = Str::limit($goods->mo_ta ?? 'Hỗ trợ điều trị', 150);
                $formatted .= "  Công dụng chính: {$shortDesc}\n";
                if ($goods->mo_ta) {
                    $formatted .= "  [Thông tin chi tiết - Chỉ dùng khi khách hỏi sâu]: {$goods->mo_ta}\n";
                }
                $formatted .= "---\n";
            }
        }

        if ($searchResults['services']->isNotEmpty()) {
            foreach ($searchResults['services'] as $index => $service) {
                $formatted .= ($index + 1) . ". {$service->ten_dich_vu}\n";
                $formatted .= "   - Giá: " . number_format($service->gia_dich_vu, 0, ',', '.') . " VNĐ\n";
                if ($service->doctor) {
                    $formatted .= "   - Bác sĩ: {$service->doctor->ten_bac_si}\n";
                }
                if ($service->thoi_gian_thuc_hien) {
                    $formatted .= "   - Thời gian: {$service->thoi_gian_thuc_hien}\n";
                }
                if ($service->mo_ta) {
                    $formatted .= "   - Mô tả: {$service->mo_ta}\n";
                }
                $formatted .= "\n";
            }
        }

        return $formatted;
    }
}