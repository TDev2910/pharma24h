<?php

namespace App\Services\Chatbot;

use App\Models\Medicine;
use App\Models\Goods;
use App\Models\Service;
use Illuminate\Support\Collection;

class ProductSearchService 
{
    public function search(string $message): array
    {
        $keywords = $this->extractKeywords($message);
        
        if(empty($keywords))
        {
            return [
                'medicines' => collect([]),
                'goods' => collect([]),
                'services' => collect([]),
            ];
        }
        return [
            'medicines' => $this->searchMedicines($keywords),
            'goods' => $this->searchGoods($keywords),
            'services' => $this->searchServices($keywords),
        ];
    }

    private function searchMedicines(array $keywords): Collection
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
        
        return $query->with(['category','manufacturer'])->limit(3)->get();
    }

    private function searchGoods(array $keywords): Collection
    {
        $query = Goods::where('ban_truc_tiep', true);
        
        $query->where(function($q) use ($keywords) {
            foreach($keywords as $keyword) {
                $q->orWhere('ten_hang_hoa', 'like', '%' . $keyword . '%')
                  ->orWhere('mo_ta', 'like', '%' . $keyword . '%');
            }
        });
        
        return $query->with(['category','manufacturer'])->limit(3)->get();
    }

    private function searchServices(array $keywords): Collection
    {
        $query = Service::where('trang_thai', 'kich_hoat');
        
        $query->where(function($q) use ($keywords) {
            foreach($keywords as $keyword) {
                $q->orWhere('ten_dich_vu', 'like', '%' . $keyword . '%')
                  ->orWhere('mo_ta', 'like', '%' . $keyword . '%');
            }
        });
        
        return $query->with(['category','doctor'])->limit(3)->get();
    }

    private function extractKeywords(string $message): array
    {
        $message = mb_strtolower($message, 'UTF-8');
        
        $stopwords = [
            'bao', 'nhiêu', 'tiền', 'có', 'không', 'là', 'của', 'và', 
            'cho', 'tôi', 'mua', 'được', 'thì', 'như', 'nào', 'gì',
            'về', 'này', 'đó', 'vậy', 'à', 'ạ', 'nhé', 'nha'
        ];
        
        $words = preg_split('/\s+/', $message);
        
        $keywords = array_filter($words, function($word) use ($stopwords) {
            return !in_array($word, $stopwords) && mb_strlen($word, 'UTF-8') > 2;
        });
        
        return array_values($keywords);
    }

    public function formatForGemini(array $searchResults): string
    {
        $formatted = "THÔNG TIN SẢN PHẨM/DỊCH VỤ LIÊN QUAN:\n\n";
        
        if ($searchResults['medicines']->isNotEmpty()) {
            $formatted .= "THUỐC:\n";
            foreach ($searchResults['medicines'] as $index => $medicine) {
                $formatted .= ($index + 1) . ". {$medicine->ten_thuoc}\n";
                $formatted .= "   - Giá: {$medicine->gia_ban_formatted}\n";
                if ($medicine->hoat_chat) {
                    $formatted .= "   - Hoạt chất: {$medicine->hoat_chat}\n";
                }
                if ($medicine->ham_luong) {
                    $formatted .= "   - Hàm lượng: {$medicine->ham_luong}\n";
                }
                $formatted .= "   - Tồn kho: " . ($medicine->ton_kho > 0 ? "Còn {$medicine->ton_kho} hộp" : "Hết hàng") . "\n";
                if ($medicine->mo_ta) {
                    $formatted .= "   - Mô tả: {$medicine->mo_ta}\n";
                }
                $formatted .= "\n";
            }
        }
        
        if ($searchResults['goods']->isNotEmpty()) {
            $formatted .= "HÀNG HÓA:\n";
            foreach ($searchResults['goods'] as $index => $goods) {
                $formatted .= ($index + 1) . ". {$goods->ten_hang_hoa}\n";
                $formatted .= "   - Giá: {$goods->gia_ban_formatted}\n";
                $formatted .= "   - Tồn kho: " . ($goods->ton_kho > 0 ? "Còn {$goods->ton_kho} sản phẩm" : "Hết hàng") . "\n";
                if ($goods->mo_ta) {
                    $formatted .= "   - Mô tả: {$goods->mo_ta}\n";
                }
                $formatted .= "\n";
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