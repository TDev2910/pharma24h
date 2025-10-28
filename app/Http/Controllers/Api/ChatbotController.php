<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\StreamedResponse;
use Illuminate\Http\StreamedEvent;
use Illuminate\Support\Facades\Http;
use OpenAI\Laravel\Facades\OpenAI;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userMessage = $request->input('message');
        
        return response()->eventStream(function () use ($userMessage) {
            try {
                // Thử OpenAI trước
                $stream = OpenAI::chat()->createStreamed([
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Bạn là trợ lý AI của nhà thuốc Pharma PCT. Hãy trả lời bằng tiếng Việt một cách thân thiện và hữu ích về các vấn đề sức khỏe, thuốc men, và dịch vụ nhà thuốc. Nếu câu hỏi không liên quan đến y tế, hãy trả lời ngắn gọn và chuyển hướng về chủ đề nhà thuốc.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $userMessage
                        ]
                    ],
                    'max_tokens' => 300,
                    'temperature' => 0.7,
                ]);

                // Stream response từ OpenAI
                foreach ($stream as $response) {
                    $content = $response->choices[0]->delta->content ?? '';
                    
                    if (!empty($content)) {
                        yield new StreamedEvent(event: 'update', data: $content);
                    }
                }
                
                // Kết thúc stream
                yield new StreamedEvent(event: 'update', data: "\n\n");
                
            } catch (\Exception $e) {
                // Nếu OpenAI lỗi, thử Gemini API
                try {
                    $apiKey = config('services.gemini.api_key');
                    $prompt = "Bạn là trợ lý AI của nhà thuốc Pharma PCT. Hãy trả lời bằng tiếng Việt một cách thân thiện và hữu ích về các vấn đề sức khỏe, thuốc men, và dịch vụ nhà thuốc. Câu hỏi: " . $userMessage;
                    
                    $response = Http::timeout(30)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey, [
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
                    
                } catch (\Exception $geminiError) {
                    // Fallback response khi cả OpenAI và Gemini đều lỗi
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
            }
        }, headers: [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ]);
    }

    public function index()
    {
        return view('chatbot');
    }

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
