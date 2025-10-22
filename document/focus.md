Quy Tắc Làm Việc Cho Dự Án (Project Flow)
Vui lòng tuân thủ nghiêm ngặt các quy tắc sau đây khi hỗ trợ tôi trên dự án này:

1. Tập trung tuyệt đối vào Bối Cảnh Dự Án
Không đề xuất bên ngoài: Chỉ đưa ra các giải pháp, thư viện hoặc thay đổi dựa trên các công nghệ, framework (ví dụ: Laravel, Vue, Bootstrap) và cấu trúc đã có sẵn trong dự án.

Hiểu rõ kiến trúc: Trước khi đề xuất code, hãy phân tích các file liên quan tôi đã cung cấp (@file) để đảm bảo giải pháp của bạn nhất quán với logic và kiến trúc hiện tại.

Không gợi ý linh tinh: Chỉ tập trung vào yêu cầu hiện tại. Không đề xuất thêm các tính năng hoặc thư viện mới không liên quan trực tiếp đến vấn đề đang giải quyết.

2. Ưu tiên Code Chất Lượng Cao và Dễ Bảo Trì
Nguyên tắc DRY (Don't Repeat Yourself): Không được phép tạo code trùng lặp. Nếu bạn phát hiện logic tương tự đã tồn tại, hãy đề xuất tái cấu trúc (refactor) để tạo một hàm/phương thức/component dùng chung.

Nguyên tắc SOLID và KISS: Mọi code mới phải tuân thủ các nguyên tắc thiết kế cơ bản (đặc biệt là Single Responsibility - Đơn trách nhiệm). Giữ cho các hàm/phương thức ngắn gọn và chỉ làm một việc duy nhất.

Cấu trúc rõ ràng: Code phải dễ đọc. Sử dụng tên biến, tên hàm rõ ràng, mang tính mô tả. Chia logic phức tạp thành các bước hoặc hàm nhỏ hơn.

3. Xử lý Thay thế và Xóa Code Cũ
Chỉ cung cấp giải pháp thay thế: Khi tôi yêu cầu refactor hoặc sửa lỗi, giải pháp của bạn phải là phiên bản code hoàn chỉnh để thay thế.

Không comment code cũ: Không được đề xuất giải pháp bằng cách comment (chú thích) code cũ và thêm code mới bên dưới. Hãy cung cấp thẳng khối code đã được cập nhật.

Chỉ định rõ ràng việc xóa file: Nếu việc refactor của bạn dẫn đến việc một file cũ không còn cần thiết, hãy nêu rõ ràng: "File path/to/old/file.php này có thể được xóa bỏ sau khi áp dụng thay đổi."

4. Định dạng Code Cung Cấp
Luôn cung cấp các khối code hoàn chỉnh, đúng định dạng (sử dụng markdown cho PHP, JS, Vue, v.v.).

Nếu thay đổi liên quan đến nhiều file, hãy nhóm code lại theo từng file một cách rõ ràng.