<?php

namespace App\Services\Chatbot;

use App\Models\Medicine;
use App\Models\Goods;
use App\Models\Service;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ProductSearchService
{
    protected $isAskingPrice = false;

    public function search(string $message): array
    {
        $this->detectPriceIntent($message);
        // 1. Trích xuất từ khóa và giá
        $keywords   = $this->extractKeywords($message);
        $priceRange = $this->extractPriceRange($message);
        $searchType = $this->detectSearchType($message);

        // 2. Lọc bỏ các từ khóa quá chung chung (thuốc, sản phẩm...)
        $keywords = $this->filterGenericKeywords($keywords);

        // Nếu sau khi lọc không còn từ khóa và không có khoảng giá -> trả về kết quả rỗng, không truy vấn DB
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

        // Truy vấn Database khi có từ khóa hoặc khoảng giá
        if ($searchType === 'product' || $searchType === 'all')
        {
            // Tìm thuốc
            $result['medicines'] = $this->smartSearch($keywords, $priceRange, Medicine::class, 'ten_thuoc');
            // Tìm hàng hóa
            $result['goods'] = $this->smartSearch($keywords, $priceRange, Goods::class, 'ten_hang_hoa');
        }

        if ($searchType === 'service' || $searchType === 'all') {
            // Tìm dịch vụ
            $result['services'] = $this->smartSearch($keywords, $priceRange, Service::class, 'ten_dich_vu');
        }

        return $result;
    }

    // xử lý từ khóa từ user nhập vào
    private function extractKeywords(string $message): array
    {
        $message = mb_strtolower($message, 'UTF-8');

        // Danh sách từ dừng (Stopwords)
        $stopwords = [
            // Nhóm câu hỏi & đại từ
            'bao', 'nhiêu', 'tiền', 'có', 'không', 'là', 'của', 'và',
            'cho', 'tôi', 'mua', 'được', 'thì', 'như', 'nào', 'gì',
            'về', 'này', 'đó', 'vậy', 'à', 'ạ', 'nhé', 'nha', 'từ', 'đến', 'tới',
            'tìm', 'sản', 'phẩm', 'muốn', 'cần', 'biết', 'hỏi', 'xem',
            'mình', 'bạn', 'em', 'anh', 'chị', 'cô', 'chú', 'bác',

            //Nhóm từ "thông tin" gây lỗi tìm kiếm
            'thông', 'tin', 'chi', 'tiết', 'cụ', 'thể', 'tư', 'vấn', 'hỗ', 'trợ',

            // Từ khóa chào hỏi
            'hi', 'hello', 'xin', 'chào', 'shop', 'ad', 'admin', 'ơi', 'alo', 'hế', 'lô', 'giúp'
        ];

        // Loại bỏ ký tự đặc biệt
        $message = str_replace([',', '.', '?', '!', ';', ':', '-', '(', ')', '"', "'"], ' ', $message);

        $words = preg_split('/\s+/', $message);

        $keywords = array_filter($words, function ($word) use ($stopwords) {
            // Giữ lại từ >= 2 ký tự và không nằm trong stopwords
            return !in_array($word, $stopwords) && mb_strlen($word, 'UTF-8') >= 2;
        });

        return array_values($keywords);
    }


    // trích xuất khoảng giá từ tin nhắn
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

    // phát hiện ý định hỏi về giá
    private function detectPriceIntent(string $message): void
    {
        $priceKeywords = ['giá', 'tiền', 'bao nhiêu', 'nhiêu', 'cost', 'price', 'chi phí'];
        $message = mb_strtolower($message);
        foreach ($priceKeywords as $word) {
            if (str_contains($message, $word)) {
                $this->isAskingPrice = true;
                return;
            }
        }
        $this->isAskingPrice = false;
    }

    // tìm kiếm thông minh với 2 chiến lược
    // Hàm tìm kiếm thông minh với cơ chế Chấm điểm (Ranking)
    private function smartSearch(array $keywords, ?array $priceRange, string $modelClass, string $nameColumn): Collection
    {
        $query = $modelClass::query();

        // Điều kiện hiển thị
        if ($modelClass === Medicine::class || $modelClass === Goods::class) {
            $query->where('ban_truc_tiep', true);
        } elseif ($modelClass === Service::class) {
            $query->where('trang_thai', 'kich_hoat');
        }

        // --- CHIẾN LƯỢC 1: Tìm kiếm CHÍNH XÁC (Logic AND) ---
        $exactQuery = clone $query;
        foreach ($keywords as $keyword) {
            $exactQuery->where(function ($q) use ($keyword, $nameColumn, $modelClass) {
                $q->where($nameColumn, 'like', '%' . $keyword . '%');
                if ($modelClass === Medicine::class) {
                    $q->orWhere('hoat_chat', 'like', '%' . $keyword . '%')
                        ->orWhere('cong_dung', 'like', '%' . $keyword . '%')
                        ->orWhere('mo_ta', 'like', '%' . $keyword . '%')
                        ->orWhere('ham_luong', 'like', '%' . $keyword . '%');
                }
            });
        }

        if ($priceRange) {
             $colPrice = ($modelClass === Service::class) ? 'gia_dich_vu' : 'gia_ban';
             $exactQuery->whereBetween($colPrice, [$priceRange['min'], $priceRange['max']]);
        }

        $results = $exactQuery->limit(5)->get();

        // --- CHIẾN LƯỢC 2: Tìm kiếm MỞ RỘNG & CHẤM ĐIỂM (Logic OR + Ranking) ---
        // Chỉ chạy khi tìm chính xác không ra kết quả
        if ($results->isEmpty() && count($keywords) > 1) {
            $fuzzyQuery = clone $query;
            $fuzzyQuery->where(function ($q) use ($keywords, $nameColumn, $modelClass) {
                foreach ($keywords as $keyword) {
                    $q->orWhere($nameColumn, 'like', '%' . $keyword . '%');
                    if ($modelClass === Medicine::class) {
                        $q->orWhere('hoat_chat', 'like', '%' . $keyword . '%')
                            ->orWhere('cong_dung', 'like', '%' . $keyword . '%')
                            ->orWhere('mo_ta', 'like', '%' . $keyword . '%')
                            ->orWhere('ham_luong', 'like', '%' . $keyword . '%');
                    }
                }
            });

            if ($priceRange) {
                $colPrice = ($modelClass === Service::class) ? 'gia_dich_vu' : 'gia_ban';
                $fuzzyQuery->whereBetween($colPrice, [$priceRange['min'], $priceRange['max']]);
            }

            // Lấy nhiều kết quả hơn để lọc (ví dụ 20)
            $candidates = $fuzzyQuery->limit(20)->get();

            // Tính điểm cho từng sản phẩm dựa trên số lượng từ khóa xuất hiện trong tên
            $scoredResults = $candidates->map(function ($item) use ($keywords, $nameColumn) {
                $score = 0;
                $itemName = mb_strtolower($item->{$nameColumn}, 'UTF-8');

                foreach ($keywords as $keyword) {
                    // Nếu từ khóa xuất hiện trong tên sản phẩm -> Cộng 1 điểm
                    if (mb_strpos($itemName, $keyword) !== false) {
                        $score++;
                    }
                }

                // Gán điểm tạm thời vào object
                $item->relevance_score = $score;
                return $item;
            });

            // Sắp xếp giảm dần theo điểm và lấy Top 5
            // Filter: Chỉ lấy những sản phẩm có điểm số >= 50% số lượng từ khóa (để loại bỏ kết quả dư thừa)
            $minScore = ceil(count($keywords) * 0.5);

            $results = $scoredResults
                ->filter(function ($item) use ($minScore) {
                    return $item->relevance_score >= $minScore;
                })
                ->sortByDesc('relevance_score')
                ->values()
                ->take(5);
        }

        // Load quan hệ
        if ($results->isNotEmpty()) {
            // Cần lấy lại ID để query load relations vì collection đã bị biến đổi
            $ids = $results->pluck('id')->toArray();

            // Query lại một lần nữa để load relations chuẩn (hoặc load trực tiếp trên collection nếu model hỗ trợ)
            // Cách nhanh nhất là load trực tiếp:
            if ($modelClass === Medicine::class || $modelClass === Goods::class) {
                $results->load(['category', 'manufacturer']);
            } elseif ($modelClass === Service::class) {
                $results->load(['category', 'doctor']);
            }
        }

        return $results;
    }

    // chuẩn hóa giá tiền
    private function normalizePrice(string $price): ?int
    {
        $price = preg_replace('/[^\d.,]/', '', $price);
        $price = str_replace([',', '.'], '', $price);
        if (preg_match('/(\d+)\s*(?:k|ngàn)/i', $price, $m)) {
            return (int) $m[1] * 1000;
        }
        return $price ? (int) $price : null;
    }

    // trích xuất ảnh sản phẩm
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

    // Phát hiện loại tìm kiếm mà khách hàng muốn: "thuốc/sản phẩm", "dịch vụ" hay "cả hai"
    private function detectSearchType(string $message): string
    {
        $message = mb_strtolower($message, 'UTF-8');

        $productKeywords = [
            'sản phẩm', 'thuốc', 'hàng hóa',
            'kem', 'viên', 'siro', 'chai', 'hộp', 'hũ'
        ];
        $serviceKeywords = [
            'dịch vụ', 'khám', 'tư vấn', 'bác sĩ', 'doctor'
        ];

        $hasProduct = false;
        $hasService = false;

        // Kiểm tra từ khóa liên quan đến sản phẩm/thuốc
        foreach ($productKeywords as $keyword) {
            if (mb_strpos($message, $keyword) !== false) {
                $hasProduct = true;
                break;
            }
        }
        // Kiểm tra từ khóa liên quan đến dịch vụ
        foreach ($serviceKeywords as $keyword) {
            if (mb_strpos($message, $keyword) !== false) {
                $hasService = true;
                break;
            }
        }

        // Ưu tiên phân loại rõ ràng, nếu có cả hai thì trả về 'all'
        if ($hasProduct && !$hasService) {
            return 'product';
        }
        if ($hasService && !$hasProduct) {
            return 'service';
        }
        return 'all';
    }

    // lọc bỏ các từ khóa quá chung chung (thuốc, sản phẩm...)
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



    public function formatForGemini(array $searchResults): string
    {
        $formatted = "THÔNG TIN SẢN PHẨM/DỊCH VỤ LIÊN QUAN:\n\n";

        if ($this->isAskingPrice) {
            $formatted .= "!!! LƯU Ý QUAN TRỌNG: KHÁCH HÀNG ĐANG HỎI VỀ GIÁ TIỀN. Hãy trả lời ngắn gọn, đưa GIÁ TIỀN lên đầu câu trả lời.\n\n";
        }
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
