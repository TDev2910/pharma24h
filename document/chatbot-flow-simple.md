# 📱 LUỒNG CHATBOT ĐƠN GIẢN - DỄ HIỂU NHẤT

## 🎯 TÓM TẮT NHANH (30 GIÂY)

**Chatbot hoạt động như thế nào?**
1. Bạn gửi tin nhắn → Hệ thống tìm sản phẩm trong database
2. Gửi ảnh sản phẩm về màn hình ngay lập tức
3. Gửi câu hỏi + thông tin sản phẩm cho AI Gemini
4. AI Gemini trả lời → Hiển thị từng chữ (như đang gõ)

---

## 📖 GIẢI THÍCH CHI TIẾT (NHƯ ĐANG NÓI CHUYỆN)

### Ví dụ thực tế: Bạn hỏi "Tôi cần tìm Siro ho Prospan"

Hãy tưởng tượng bạn đang vào nhà thuốc và hỏi nhân viên:

---

### BƯỚC 1: BẠN GỬI TIN NHẮN

**Bạn làm gì:**
- Mở chat, gõ: "Tôi cần tìm Siro ho Prospan"
- Nhấn Enter

**Máy tính làm gì:**
- Lấy tin nhắn của bạn
- Gửi đến server qua đường link: `/api/chatbot/chat`
- Giống như gửi email, nhưng nhanh hơn

---

### BƯỚC 2: SERVER NHẬN TIN NHẮN

**Server làm gì:**
- Nhận tin nhắn: "Tôi cần tìm Siro ho Prospan"
- Kiểm tra xem tin nhắn có hợp lệ không (không quá 1000 ký tự)
- Chuẩn bị tìm kiếm sản phẩm

**Giống như:** Nhân viên nhận được yêu cầu của bạn và chuẩn bị đi tìm sản phẩm

---

### BƯỚC 3: TÌM KIẾM SẢN PHẨM TRONG DATABASE

**Hệ thống làm gì:**

#### 3.1. Phân tích câu hỏi của bạn
- Câu hỏi: "Tôi cần tìm Siro ho Prospan"
- Loại bỏ từ không cần thiết: "Tôi", "cần", "tìm"
- Giữ lại từ quan trọng: **"Siro"**, **"ho"**, **"Prospan"**

**Giống như:** Nhân viên nghe bạn nói và ghi nhớ các từ khóa quan trọng

#### 3.2. Tìm trong database
- Mở database (giống như mở tủ thuốc)
- Tìm sản phẩm có tên chứa: "Siro" VÀ "ho" VÀ "Prospan"
- Tìm trong các cột: tên thuốc, tên viết tắt, mã hàng, hoạt chất
- Chỉ lấy sản phẩm đang bán trực tiếp (`ban_truc_tiep = true`)
- Lấy tối đa 5 sản phẩm (để không quá nhiều)

**Kết quả tìm được:**
```
Sản phẩm: Siro ho Prospan Engelhard
Giá: 93.000 đ
Ảnh: /storage/products/prospan.jpg
Còn hàng: Có (50 chai)
```

**Giống như:** Nhân viên tìm thấy sản phẩm trong tủ và lấy ra

---

### BƯỚC 4: GỬI ẢNH SẢN PHẨM VỀ MÀN HÌNH NGAY

**Hệ thống làm gì:**
- Lấy ảnh sản phẩm từ kết quả tìm kiếm
- Gửi về màn hình của bạn ngay lập tức
- Không cần đợi AI trả lời

**Bạn thấy gì trên màn hình:**
```
┌─────────────────────┐
│   [Ảnh Siro ho]     │
│  Siro ho Prospan    │
│    93.000 đ         │
│   [Chi tiết]        │
└─────────────────────┘
```

**Giống như:** Nhân viên đưa sản phẩm cho bạn xem ngay, không cần đợi giải thích

---

### BƯỚC 5: CHUẨN BỊ THÔNG TIN CHO AI GEMINI

**Hệ thống làm gì:**

#### 5.1. Format thông tin sản phẩm thành văn bản
```
THÔNG TIN SẢN PHẨM:
- Sản phẩm: Siro ho Prospan
  Giá: 93.000 đ | Kho: Còn hàng
  Công dụng: Điều trị viêm phế quản...
```

#### 5.2. Tạo câu hỏi cho AI
Hệ thống tạo một "bức thư" gửi cho AI Gemini:

```
"Bạn là nhân viên bán hàng tại nhà thuốc Pharma PCT.
Nhiệm vụ: Trả lời ngắn gọn, thân thiện.

DỮ LIỆU SẢN PHẨM HIỆN CÓ:
- Sản phẩm: Siro ho Prospan
  Giá: 93.000 đ | Kho: Còn hàng

CÂU HỎI CỦA KHÁCH: 'Tôi cần tìm Siro ho Prospan'
TRẢ LỜI:"
```

**Giống như:** Nhân viên chuẩn bị giấy tờ và thông tin để hỏi đồng nghiệp khác

---

### BƯỚC 6: GỌI AI GEMINI ĐỂ SINH PHẢN HỒI

**Hệ thống làm gì:**
- Gửi "bức thư" đã chuẩn bị đến Google Gemini API
- Đợi AI Gemini đọc và suy nghĩ
- AI Gemini sinh ra câu trả lời

**AI Gemini trả lời:**
```
"Dạ có Siro ho Prospan Engelhard ạ. 
Sản phẩm giá 93.000đ/chai và hiện tại bên em còn hàng ạ."
```

**Giống như:** Đồng nghiệp đọc thông tin và trả lời cho bạn

---

### BƯỚC 7: GỬI PHẢN HỒI VỀ MÀN HÌNH (TỪNG CHỮ)

**Hệ thống làm gì:**
- Nhận câu trả lời từ AI Gemini
- Chia câu trả lời thành từng từ: "Dạ", "có", "Siro", "ho"...
- Gửi từng từ về màn hình của bạn
- Thêm delay nhỏ (50ms) mỗi 3 từ để tạo hiệu ứng gõ chữ

**Bạn thấy gì trên màn hình:**
```
Bot: Dạ có Siro ho Prospan Engelhard ạ...
     (từng chữ xuất hiện dần, như đang gõ)
```

**Giống như:** Nhân viên đọc từng từ cho bạn nghe, không đọc hết một lúc

---

## 🎬 TÓM TẮT TOÀN BỘ QUÁ TRÌNH

```
Bạn: "Tôi cần tìm Siro ho Prospan"
  ↓
Server nhận tin nhắn
  ↓
Tìm trong database → Tìm thấy sản phẩm
  ↓
Gửi ảnh sản phẩm về màn hình (NGAY LẬP TỨC)
  ↓
Chuẩn bị thông tin cho AI Gemini
  ↓
Gọi AI Gemini → Nhận câu trả lời
  ↓
Gửi câu trả lời về màn hình (TỪNG CHỮ)
  ↓
Bạn thấy: Ảnh sản phẩm + Câu trả lời của bot
```

---

## 🔍 CÁC THÀNH PHẦN QUAN TRỌNG

### 1. Frontend (Màn hình bạn nhìn thấy)
- **File:** `resources/js/Components/ChatbotPopup.vue`
- **Nhiệm vụ:** Hiển thị chat, gửi tin nhắn, nhận và hiển thị kết quả

### 2. Backend Controller (Người điều phối)
- **File:** `app/Http/Controllers/Api/ChatbotController.php`
- **Nhiệm vụ:** Nhận tin nhắn, điều phối các bước xử lý, gửi kết quả về

### 3. ProductSearchService (Công cụ tìm kiếm)
- **File:** `app/Services/Chatbot/ProductSearchService.php`
- **Nhiệm vụ:** Tìm kiếm sản phẩm trong database

### 4. Database (Kho lưu trữ)
- **Bảng:** medicines, goods, services
- **Nhiệm vụ:** Lưu trữ thông tin sản phẩm

### 5. Gemini API (AI trả lời)
- **Model:** gemini-2.5-flash
- **Nhiệm vụ:** Sinh câu trả lời tự nhiên dựa trên thông tin sản phẩm

---

## 💡 TẠI SAO LÀM NHƯ VẬY?

### 1. Tại sao tìm sản phẩm trước?
- Để có thông tin chính xác từ database
- AI chỉ cần đọc thông tin và trả lời, không cần tự nghĩ ra

### 2. Tại sao gửi ảnh trước?
- Người dùng thấy sản phẩm ngay, không phải đợi
- Trải nghiệm tốt hơn

### 3. Tại sao gửi từng chữ?
- Tạo cảm giác như đang chat thật với người
- Không cảm thấy máy móc

### 4. Tại sao dùng SSE (Server-Sent Events)?
- Cho phép server gửi dữ liệu liên tục về client
- Không cần client hỏi lại nhiều lần
- Giống như radio: phát liên tục, bạn chỉ cần nghe

---

## 🎯 VÍ DỤ CỤ THỂ

### Câu hỏi: "Siro ho bao nhiêu tiền?"

**Luồng xử lý:**

1. **Tìm kiếm:** Tìm tất cả sản phẩm có "siro" và "ho"
   - Tìm thấy: Prospan (93.000đ), ATessen (50.000đ)

2. **Gửi ảnh:** Hiển thị 2 product cards ngay

3. **Gọi AI:** 
   - Input: "Có 2 loại siro ho: Prospan 93k, ATessen 50k. Khách hỏi giá."
   - Output: "Hiện tại bên em có 2 loại siro ho ạ: Prospan 93.000đ, ATessen 50.000đ..."

4. **Hiển thị:** Câu trả lời xuất hiện từng chữ

---

## ❓ CÂU HỎI THƯỜNG GẶP

### Q: Nếu không tìm thấy sản phẩm thì sao?
A: Hệ thống vẫn gọi AI, nhưng AI sẽ trả lời chung chung dựa trên kiến thức y khoa.

### Q: Nếu AI Gemini lỗi thì sao?
A: Có hàm `getFallbackResponse()` để trả lời dựa trên từ khóa (giảm đau, cảm cúm, địa chỉ...)

### Q: Tại sao chỉ lấy 5 sản phẩm?
A: Để phản hồi nhanh và không quá nhiều thông tin cho người dùng.

### Q: SSE là gì?
A: Server-Sent Events - Cho phép server gửi dữ liệu liên tục về client mà không cần client hỏi lại.

---

## 📝 GHI CHÚ KỸ THUẬT

### Các event trong SSE Stream:
- `event: images` → Gửi ảnh sản phẩm
- `event: update` → Gửi text từ AI

### Các bước trong code:
1. `ChatbotController::chat()` → Nhận request
2. `ProductSearchService::search()` → Tìm sản phẩm
3. `ProductSearchService::extractProductImages()` → Lấy ảnh
4. `ProductSearchService::formatForGemini()` → Format dữ liệu
5. `ChatbotController::buildEnhancedPrompt()` → Tạo prompt
6. Gọi Gemini API → Nhận response
7. Stream từng từ về client

---

**Tài liệu này giải thích đơn giản nhất có thể. Nếu vẫn chưa hiểu, hãy đọc lại từ đầu và theo dõi từng bước!** 🎓

