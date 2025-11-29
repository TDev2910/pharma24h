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
        $keywords = $this->extractKeywords($message);
        $priceRange = $this->extractPriceRange($message);
        $searchType = $this->detectSearchType($message);

        $result = [
            'medicines' => collect([]),
            'goods' => collect([]),
            'services' => collect([]),
        ];
        
        $keywords = $this->filterGenericKeywords($keywords);
        
        // Chỉ tìm sản phẩm nếu user hỏi về sản phẩm
        if ($searchType === 'product' || $searchType === 'all') {
            if ($priceRange && empty($keywords)) {
                $result['medicines'] = $this->searchMedicinesByPrice($priceRange);
                $result['goods'] = $this->searchGoodsByPrice($priceRange);
            } elseif (!empty($keywords)) {
                // Có keywords cụ thể, tìm theo keywords + priceRange (nếu có)
                $result['medicines'] = $this->searchMedicines($keywords, $priceRange);
                $result['goods'] = $this->searchGoods($keywords, $priceRange);
            } elseif ($priceRange) {
                // Chỉ có priceRange, không có keywords
                $result['medicines'] = $this->searchMedicinesByPrice($priceRange);
                $result['goods'] = $this->searchGoodsByPrice($priceRange);
            }
        }
        
        // Chỉ tìm dịch vụ nếu user hỏi về dịch vụ
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

    private function extractPriceRange(string $message): ?array
    {
        $message = mb_strtolower($message, 'UTF-8');
        
        // Pattern: "từ X đến Y", "X - Y", "X đến Y", "khoảng X đến Y"
        // Hỗ trợ: 200.000, 200000, 200k, 200 ngàn
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
        
        // Nếu có "k" hoặc "ngàn", nhân 1000
        if (preg_match('/(\d+)\s*(?:k|ngàn)/i', $price, $m)) {
            return (int)$m[1] * 1000;
        }
        
        return $price ? (int)$price : null;
    }

    private function detectSearchType(string $message): string
    {
        $message = mb_strtolower($message, 'UTF-8');
        
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

    private function searchMedicines(array $keywords, ?array $priceRange = null): Collection
    {
        $query = Medicine::where('ban_truc_tiep', true);
        
        $query->where(function($q) use ($keywords) {
            foreach($keywords as $keyword) {
                $q->orWhere('ten_thuoc', 'like', '%' . $keyword . '%')
                  ->orWhere('ten_viet_tat', 'like', '%' . $keyword . '%')
                  ->orWhere('ma_hang', 'like', '%' . $keyword . '%')
                  ->orWhere('ma_vach', 'like', '%' . $keyword . '%');
            }
        });

        if ($priceRange) {
            $query->whereBetween('gia_ban', [$priceRange['min'], $priceRange['max']]);
        }
        
        return $query->with(['category','manufacturer'])->limit(10)->get();
    }

    private function searchMedicinesByPrice(array $priceRange): Collection
    {
        return Medicine::where('ban_truc_tiep', true)
            ->whereBetween('gia_ban', [$priceRange['min'], $priceRange['max']])
            ->with(['category','manufacturer'])
            ->limit(10)
            ->get();
    }

    private function searchGoods(array $keywords, ?array $priceRange = null): Collection
    {
        $query = Goods::where('ban_truc_tiep', true);
        
        $query->where(function($q) use ($keywords) {
            foreach($keywords as $keyword) {
                $q->orWhere('ten_hang_hoa', 'like', '%' . $keyword . '%')
                  ->orWhere('mo_ta', 'like', '%' . $keyword . '%');
            }
        });

        if ($priceRange) {
            $query->whereBetween('gia_ban', [$priceRange['min'], $priceRange['max']]);
        }
        
        return $query->with(['category','manufacturer'])->limit(10)->get();
    }

    private function searchGoodsByPrice(array $priceRange): Collection
    {
        return Goods::where('ban_truc_tiep', true)
            ->whereBetween('gia_ban', [$priceRange['min'], $priceRange['max']])
            ->with(['category','manufacturer'])
            ->limit(10)
            ->get();
    }

    private function searchServices(array $keywords, ?array $priceRange = null): Collection
    {
        $query = Service::where('trang_thai', 'kich_hoat');
        
        $query->where(function($q) use ($keywords) {
            foreach($keywords as $keyword) {
                $q->orWhere('ten_dich_vu', 'like', '%' . $keyword . '%')
                  ->orWhere('mo_ta', 'like', '%' . $keyword . '%');
            }
        });

        if ($priceRange) {
            $query->whereBetween('gia_dich_vu', [$priceRange['min'], $priceRange['max']]);
        }
        
        return $query->with(['category','doctor'])->limit(10)->get();
    }

    private function searchServicesByPrice(array $priceRange): Collection
    {
        return Service::where('trang_thai', 'kich_hoat')
            ->whereBetween('gia_dich_vu', [$priceRange['min'], $priceRange['max']])
            ->with(['category','doctor'])
            ->limit(10)
            ->get();
    }

    private function extractKeywords(string $message): array
    {
        $message = mb_strtolower($message, 'UTF-8');
        
        $stopwords = [
            'bao', 'nhiêu', 'tiền', 'có', 'không', 'là', 'của', 'và', 
            'cho', 'tôi', 'mua', 'được', 'thì', 'như', 'nào', 'gì',
            'về', 'này', 'đó', 'vậy', 'à', 'ạ', 'nhé', 'nha', 'từ', 'đến', 'tới',
            'tìm', 'sản', 'phẩm', 'muốn', 'cần' 
        ];
        
        $message = str_replace([',', '.', '?', '!', ';'], ' ', $message);

        $words = preg_split('/\s+/', $message);
        
        $keywords = array_filter($words, function($word) use ($stopwords) {
            return !in_array($word, $stopwords) && mb_strlen($word, 'UTF-8') >= 2;
        });
        
        return array_values($keywords);
    }

    private function filterGenericKeywords(array $keywords): array
    {
        $genericWords = [
            'thuốc', 'sản', 'phẩm', 'hàng', 'hóa', 
            'tìm', 'cần', 'muốn', 'giá', 'chi', 'tiết'
        ];        
        
        return array_filter($keywords, function($keyword) use ($genericWords) {
            return !in_array($keyword, $genericWords);
        });
    }

    public function formatForGemini(array $searchResults): string
    {
        $formatted = "THÔNG TIN SẢN PHẨM/DỊCH VỤ LIÊN QUAN:\n\n";
        
        if ($searchResults['medicines']->isNotEmpty()) {
            foreach ($searchResults['medicines'] as $index => $medicine) {
                $formatted .= "- Sản phẩm: {$medicine->ten_thuoc}\n";
                $formatted .= "  Giá: {$medicine->gia_ban_formatted} | Kho: " . ($medicine->ton_kho > 0 ? "Còn hàng" : "Hết") . "\n";
                
                // Xử lý mô tả: Chỉ lấy 150 ký tự đầu tiên hoặc bỏ bớt các từ ngữ chuyên môn dài dòng
                // Để AI tự tóm tắt dựa trên đoạn ngắn này
                $shortDesc = Str::limit($medicine->mo_ta ?? $medicine->cong_dung ?? 'Hỗ trợ điều trị', 150);
                $formatted .= "  Công dụng chính: {$shortDesc}\n"; 
                
                // Nếu muốn AI biết chi tiết liều dùng để trả lời KHI CẦN, hãy để vào field riêng
                // Nhưng đánh dấu là [Chi tiết - Chỉ dùng khi được hỏi]
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
                
                // Xử lý mô tả: Chỉ lấy 150 ký tự đầu tiên hoặc bỏ bớt các từ ngữ chuyên môn dài dòng
                // Để AI tự tóm tắt dựa trên đoạn ngắn này
                $shortDesc = Str::limit($goods->mo_ta ?? 'Hỗ trợ điều trị', 150);
                $formatted .= "  Công dụng chính: {$shortDesc}\n"; 
                
                // Nếu muốn AI biết chi tiết liều dùng để trả lời KHI CẦN, hãy để vào field riêng
                // Nhưng đánh dấu là [Chi tiết - Chỉ dùng khi được hỏi]
                if ($goods->mo_ta) {
                    $formatted .= "  [Thông tin chi tiết - Chỉ dùng khi khách hỏi sâu]: {$goods->mo_ta}\n";
                }
                $formatted .= "---\n";
            }
        }
        
        if ($searchResults['services']->isNotEmpty()) {
            $formatted .= "DỊCH VỤ:\n";
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
        
        if ($searchResults['medicines']->isEmpty() && 
            $searchResults['goods']->isEmpty() && 
            $searchResults['services']->isEmpty()) {
            $formatted .= "Không tìm thấy sản phẩm/dịch vụ liên quan trong database.\n";
        }
        
        return $formatted;
    }
}