<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\StreamedResponse;
use Illuminate\Http\StreamedEvent;
use App\Services\Chatbot\ProductSearchService;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    protected $productSearch;

    public function __construct()
    {
        $this->productSearch = new ProductSearchService();
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userMessage = $request->input('message');
        
        return response()->eventStream(function () use ($userMessage) {
            try {
                // ========== THÊM MỚI: SEARCH PRODUCTS ==========
                $searchResults = $this->productSearch->search($userMessage);
                $productInfo = $this->productSearch->formatForGemini($searchResults);
                // ===============================================
                
                //lấy api key từ config đã set up sẵn trong file service.php 
                $apiKey = config('services.gemini.api_key');
                
                // ========== THAY ĐỔI: BUILD ENHANCED PROMPT ==========
                $prompt = $this->buildEnhancedPrompt($userMessage, $productInfo);
                // ====================================================
                
                //Gửi POST request đến Gemini API
                $response = Http::timeout(30)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey, 
                [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ]);
                
                if ($response->successful()) {
                    $responseData = $response->json();
                    $content = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? 'Không thể kết nối Gemini';
                    
                    // Stream Gemini response
                    $words = explode(' ', $content);
                    foreach ($words as $index => $word) {
                        yield new StreamedEvent(event: 'update', data: $word . ' ');
                        
                        if ($index % 3 === 0) {
                            usleep(50000); // 50ms delay
                        }
                    }
                    
                    yield new StreamedEvent(event: 'update', data: "\n\n");
                } else {
                    throw new \Exception('Gemini API failed: ' . $response->status());
                }
                
            } catch (\Exception $e) {
                // Fallback response khi Gemini lỗi
                $fallbackResponse = $this->getFallbackResponse($userMessage);
                
                // Stream fallback response
                $words = explode(' ', $fallbackResponse);
                foreach ($words as $index => $word) {
                    yield new StreamedEvent(event: 'update', data: $word . ' ');
                    
                    if ($index % 3 === 0) {
                        usleep(100000); // 100ms delay
                    }
                }               
                yield new StreamedEvent(event: 'update', data: "\n\n");
            }
        }, 
        headers: [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ]);
    }

    public function index()
    {
        
    }

    private function buildEnhancedPrompt(string $userMessage, string $productInfo): string
    {
        return <<<PROMPT
        Bạn là trợ lý AI của nhà thuốc Pharma PCT (địa chỉ: 12 Đô Lương, Phường 11, Vũng Tàu).

        {$productInfo}

        CÂU HỎI KHÁCH HÀNG: {$userMessage}

        HƯỚNG DẪN TRẢ LỜI:
        1. Nếu có thông tin sản phẩm/dịch vụ ở trên, hãy tư vấn cụ thể dựa trên dữ liệu đó (tên, giá, tồn kho, mô tả).
        2. Trả lời bằng tiếng Việt, thân thiện và chuyên nghiệp.
        3. Nếu không có thông tin sản phẩm, hãy tư vấn chung về sức khỏe và đề xuất khách hàng liên hệ hotline 0901645269.
        4. Luôn kết thúc bằng câu hỏi để tương tác với khách hàng.

        Trả lời:
        PROMPT;
    }

    //phản hồi dữ liệu được set cố định để tránh lỗi khi Gemini lỗi
    private function getFallbackResponse($message)
    {
        $message = strtolower($message);
        
        // Phản hồi dựa trên từ khóa cụ thể trước
        if (strpos($message, 'giảm đau') !== false || strpos($message, 'painkiller') !== false) {
            return "Chúng tôi có các loại thuốc giảm đau như Paracetamol, Ibuprofen, Aspirin. Tuy nhiên, nếu đau kéo dài hoặc nghiêm trọng, bạn nên tham khảo ý kiến bác sĩ. Hotline tư vấn: 0901645269.";
        }
        
        if (strpos($message, 'cảm') !== false || strpos($message, 'cúm') !== false || strpos($message, 'sốt') !== false) {
            return "Đối với các triệu chứng cảm cúm, chúng tôi có các loại thuốc giảm đau, hạ sốt và thuốc ho. Tuy nhiên, bạn nên đến khám bác sĩ để được chẩn đoán chính xác.";
        }
        
        if (strpos($message, 'đau') !== false || strpos($message, 'pain') !== false) {
            return "Nếu bạn đang gặp các cơn đau, chúng tôi có các loại thuốc giảm đau không cần toa. Tuy nhiên, nếu đau kéo dài, bạn nên tham khảo ý kiến bác sĩ.";
        }
        
        // Phản hồi chung về thuốc
        if (strpos($message, 'thuốc') !== false || strpos($message, 'medicine') !== false) {
            return "Chúng tôi có đầy đủ các loại thuốc theo toa và không cần toa. Để được tư vấn cụ thể về thuốc, bạn có thể đến trực tiếp nhà thuốc hoặc gọi hotline 0901645269.";
        }
        
        if (strpos($message, 'giờ') !== false || strpos($message, 'time') !== false || strpos($message, 'mở') !== false) {
            return "Nhà thuốc Pharma PCT mở cửa từ 7:00 - 22:00 hàng ngày. Chúng tôi phục vụ cả cuối tuần và ngày lễ.";
        }
        
        if (strpos($message, 'địa chỉ') !== false || strpos($message, 'address') !== false || strpos($message, 'ở đâu') !== false) {
            return "Nhà thuốc Pharma PCT tọa lạc tại 12 Đô Lương, Phường 11, Vũng Tàu. Bạn có thể tìm kiếm trên Google Maps với từ khóa 'Pharma PCT'.";
        }
        
        if (strpos($message, 'khám') !== false || strpos($message, 'bác sĩ') !== false || strpos($message, 'doctor') !== false) {
            return "Chúng tôi có dịch vụ khám bệnh với bác sĩ chuyên khoa. Để đặt lịch khám, bạn có thể gọi 0901645269 hoặc đến trực tiếp nhà thuốc.";
        }
        
        if (strpos($message, 'giá') !== false || strpos($message, 'price') !== false || strpos($message, 'tiền') !== false) {
            return "Giá thuốc tại nhà thuốc Pharma PCT cạnh tranh và hợp lý. Chúng tôi có nhiều chương trình khuyến mãi và giảm giá cho khách hàng thân thiết.";
        }
        
        // Phản hồi mặc định
        return "Xin chào! Tôi là trợ lý của nhà thuốc Pharma PCT. Hiện tại hệ thống AI đang bảo trì, nhưng tôi vẫn có thể hỗ trợ bạn. Bạn có thể hỏi về thuốc men, giờ làm việc, địa chỉ, hoặc dịch vụ khám bệnh. Để được tư vấn chi tiết, hãy gọi 0901645269.";
    }
}
