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

    //Khởi tạo ProductSearchService trong service
    public function __construct()
    {
        $this->productSearch = new ProductSearchService();
    }

    //Xử lý chat với Chatbot
    public function chat(Request $request)
    {
        // Kiểm tra xem câu hỏi của khách có phải là câu hỏi về sản phẩm không
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        // Lấy câu hỏi của khách
        $userMessage = $request->input('message');

        return response()->eventStream(
            function () use ($userMessage) {
                try {
                    // 1. Tìm kiếm dữ liệu trong DB
                    $searchResults = $this->productSearch->search($userMessage);

                    // Gửi ảnh về client trước
                    $productImages = $this->productSearch->extractProductImages($searchResults);
                    if (!empty($productImages)) {
                        // Gửi sự kiện tên là 'images'
                        echo "event: images\n";
                        echo "data: " . json_encode($productImages) . "\n\n";

                        // Đẩy dữ liệu đi ngay lập tức (Flush buffer)
                        if (ob_get_level() > 0) ob_flush();
                        flush();
                    }
                    // Kết thúc gửi ảnh về client

                    // 2. Chuẩn bị dữ liệu cho Gemini
                    $productInfo = $this->productSearch->formatForGemini($searchResults);
                    $apiKey = config('services.gemini.api_key');

                    if (empty($apiKey)) {
                        throw new \Exception('API key chưa cấu hình');
                    }

                    $prompt = $this->buildEnhancedPrompt($userMessage, $productInfo);

                    // 3. Gọi Gemini API dùng gemini-2.5-flash
                    // (sử dụng gemini-2.5-flash-lite-preview-02-05 để tăng tốc độ phản hồi)
                    $response = Http::timeout(30)->post(
                        "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey,
                        [
                            'contents' => [
                                [
                                    'parts' => [
                                        [
                                            'text' => $prompt
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    );

                    if ($response->successful()) {
                        $responseData = $response->json();
                        // Kiểm tra null safe cho response
                        $content = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '';
                        $words = explode(' ', $content);

                        foreach ($words as $index => $word) {
                            // Gửi sự kiện tên là 'update' (chứa text)
                            echo "event: update\n";
                            echo "data: " . $word . ' ' . "\n\n";

                            // Flush buffer định kỳ để tạo hiệu ứng gõ chữ mượt mà
                            if ($index % 3 === 0) {
                                if (ob_get_level() > 0) ob_flush();
                                flush();
                                usleep(50000);
                            }
                        }
                    } else {
                        // Xử lý lỗi API
                        echo "event: update\n";
                        echo "data: Xin lỗi, hệ thống đang bận.\n\n";
                    }
                } catch (\Exception $e) {
                    echo "event: update\n";
                    echo "data: Có lỗi xảy ra: " . $e->getMessage() . "\n\n";
                }
            },
            [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'X-Accel-Buffering' => 'no', // Quan trọng nếu dùng Nginx
            ]
        );
    }

    public function index()
    {

    }


    //Build prompt cho Gemini API hiểu môi trường làm việc của Chatbot
    private function buildEnhancedPrompt(string $userMessage, string $productInfo): string
    {
        return <<<PROMPT
        Bạn là nhân viên bán hàng tại nhà thuốc Pharma PCT.

        QUY TẮC TUYỆT ĐỐI:
        1. CHỈ trả lời dựa trên "DỮ LIỆU SẢN PHẨM HIỆN CÓ" bên dưới.
        2. Nếu trong dữ liệu KHÔNG có dịch vụ/sản phẩm khách hỏi, hãy nói: "Dạ hiện tại bên em chưa có dịch vụ/sản phẩm này ạ, anh/chị tham khảo các dịch vụ khác bên em nhé."
        3. KHÔNG ĐƯỢC tự bịa ra dịch vụ (như đo huyết áp, đường huyết) nếu không thấy trong dữ liệu.

        DỮ LIỆU SẢN PHẨM HIỆN CÓ:
        {$productInfo}

        CÂU HỎI CỦA KHÁCH: "{$userMessage}"
        TRẢ LỜI:
        PROMPT;
    }
    // private function buildEnhancedPrompt(string $userMessage, string $productInfo): string
    // {
    //     return <<<PROMPT
    //     Bạn là nhân viên bán hàng tại nhà thuốc Pharma PCT (12 Đô Lương, Vũng Tàu).
    //     Nhiệm vụ: Trả lời ngắn gọn, súc tích, đúng trọng tâm câu hỏi của khách (đóng vai người bán hàng thực tế).

    //     DỮ LIỆU SẢN PHẨM HIỆN CÓ:
    //     {$productInfo}

    //     YÊU CẦU TRẢ LỜI:
    //     1. NGUYÊN TẮC VÀNG: Khách hỏi gì đáp nấy. KHÔNG trả lời dư thừa.
    //     - Khách hỏi "giá bao nhiêu": Chỉ trả lời Tên thuốc + Giá + Tình trạng hàng (Còn/Hết). KHÔNG nêu cách dùng, thành phần (trừ khi khách hỏi thêm).
    //     - Khách hỏi "công dụng": Chỉ nêu ngắn gọn 1 dòng tác dụng chính.
    //     - Khách hỏi "cách dùng": Mới được nêu chi tiết liều lượng.

    //     2. VĂN PHONG:
    //     - Thân thiện, ngắn gọn.
    //     - Không liệt kê dài dòng kiểu văn bản hành chính.
    //     - Nếu có nhiều sản phẩm, hãy tóm tắt dạng danh sách ngắn.

    //     3. KỊCH BẢN MẪU:
    //     - Khách: "Siro ho bao nhiêu tiền?"
    //     - Bạn: "Hiện tại, bên em đang có 2 loại siro ho ạ:
    //         1. Prospan (Đức): 93.000đ/chai (Trị viêm phế quản).
    //         2. ATessen: 50.000đ/hộp (Giảm ho khan).
    //         Bạn muốn lấy loại nào ạ?"

    //     CÂU HỎI CỦA KHÁCH: "{$userMessage}"
    //     TRẢ LỜI:
    //     PROMPT;
    // }

    //Kiểm tra xem câu hỏi của khách có phải là câu hỏi về sản phẩm không
    private function isProductQuery(string $message): bool
    {
        $message = mb_strtolower($message, 'UTF-8');
        $productKeywords = ['sản phẩm', 'thuốc', 'hàng hóa', 'kem', 'viên', 'siro'];
        foreach ($productKeywords as $keyword) {
            if (mb_strpos($message, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }

    // phòng hờ trường hợp gọi gemini lỗi
    // phản hồi dữ liệu được set cố định để tránh lỗi khi Gemini lỗi
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

        // Phản hồi mặc định - không nói "bảo trì" nữa
        return "Xin chào! Tôi là trợ lý của nhà thuốc Pharma PCT. Tôi có thể hỗ trợ bạn về thuốc men, giờ làm việc, địa chỉ, hoặc dịch vụ khám bệnh. Để được tư vấn chi tiết hơn, bạn có thể gọi hotline 0901645269 hoặc đến trực tiếp nhà thuốc tại 12 Đô Lương, Phường 11, Vũng Tàu.";
    }
}
 