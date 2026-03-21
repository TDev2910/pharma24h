<?php

namespace App\Core\Chatbot\Application\Services;

use App\Core\Chatbot\Ports\Inbound\ChatUseCaseInterface;
use App\Core\Chatbot\Ports\Outbound\AiProviderInterface;
use App\Core\Chatbot\Ports\Outbound\ProductRepositoryInterface;
use App\Core\Chatbot\Domain\DTOs\ChatRequestData;
use App\Core\Chatbot\Domain\DTOs\ChatResponsePart;

class ChatService implements ChatUseCaseInterface
{
    public function __construct(
        private AiProviderInterface $aiProvider,
        private ProductRepositoryInterface $productRepositoryInterface
    ) {}

    public function chat(ChatRequestData $data): \Generator
    {
        // 1. Tìm kiếm sản phẩm trong Repository
        $searchResults = $this->productRepositoryInterface->searchProducts($data->message);

        // 2. Gửi ảnh sản phẩm về trước 
        $productImages = $this->extractImagesFromResults($searchResults);
        if (!empty($productImages)) {
            yield new ChatResponsePart(
                type: 'images',
                content: json_encode($productImages)
            );
        }

        //Xây dựng Prompt từ dữ liệu sản phẩm
        $productInfo = $this->formatProductInfo($searchResults);
        $prompt = $this->buildPrompt($data->message, $productInfo);

        foreach ($this->aiProvider->generateStream($prompt) as $word) {
            yield new ChatResponsePart(
                type: 'text',
                content: $word
            );
        }

        yield new ChatResponsePart(
            type: 'text',
            content: '',
            isFinal: true
        );
    }

    private function extractImagesFromResults(array $results): array
    {
        return [];
    }

    private function formatProductInfo(array $results): string
    {
        return json_encode($results);
    }

    private function buildPrompt(string $message, string $productInfo): string
    {
        return <<<PROMPT
        Bạn là "Dược sĩ ảo Pharma PCT". Nhiệm vụ của bạn là tư vấn sức khỏe chuẩn xác và giới thiệu sản phẩm phù hợp.
        KHI KHÁCH HỎI VỀ BỆNH/TRIỆU CHỨNG (ví dụ: bị ho, đau bụng...):
        1. **Lời khuyên đầu tiên**: Đưa ra 2-3 lời khuyên chăm sóc sức khỏe cơ bản (ví dụ: giữ ấm, ăn nhẹ, nghỉ ngơi).
        2. **Giới thiệu sản phẩm**: Dựa vào "DỮ LIỆU HỆ THỐNG" dưới đây, hãy liệt kê ngay 1-2 loại thuốc phù hợp nhất để điều trị triệu chứng đó.
        3. **Cách trình bày sản phẩm**: Gọi tên sản phẩm kèm giá tiền và cách dùng ngắn gọn. Dùng danh sách gạch đầu dòng cho dễ nhìn.
        
        LƯU Ý QUAN TRỌNG:
        - Nếu trong dữ liệu có thuốc phù hợp, hãy viết: "Dạ, anh/chị có thể tham khảo các dòng sản phẩm này tại bên em:" và liệt kê ra.
        - Trả lời chân thành, ngắn gọn, đi thẳng vào vấn đề.
        - Kết thúc bằng việc dặn khách liên hệ hotline hoặc đến nhà thuốc nếu triệu chứng không giảm.
        DỮ LIỆU HỆ THỐNG (Sản phẩm/Dịch vụ): {$productInfo}
        CÂU HỎI CỦA KHÁCH: "{$message}"
        PROMPT;
    }
}
