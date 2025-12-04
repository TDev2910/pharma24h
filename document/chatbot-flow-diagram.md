# 📊 SƠ ĐỒ LUỒNG CHATBOT - TRỰC QUAN NHẤT

## 🎯 SƠ ĐỒ TỔNG QUAN

```
┌─────────────────────────────────────────────────────────────────┐
│                    NGƯỜI DÙNG GỬI TIN NHẮN                      │
│              "Tôi cần tìm Siro ho Prospan"                      │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│              SERVER NHẬN TIN NHẮN                                │
│         ChatbotController::chat()                                │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│           TÌM KIẾM SẢN PHẨM TRONG DATABASE                       │
│         ProductSearchService::search()                           │
│                                                                  │
│  • Phân tích: "Siro", "ho", "Prospan"                          │
│  • Tìm trong database                                            │
│  • Tìm thấy: Siro ho Prospan (93.000đ)                          │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ├─────────────────┐
                             │                 │
                             ▼                 ▼
        ┌──────────────────────────┐  ┌──────────────────────────┐
        │  GỬI ẢNH SẢN PHẨM        │  │  CHUẨN BỊ CHO AI GEMINI   │
        │  (Ngay lập tức)          │  │                          │
        │                          │  │  • Format thông tin     │
        │  [Ảnh]                   │  │  • Tạo prompt            │
        │  Siro ho Prospan         │  │  • Gửi cho AI           │
        │  93.000 đ                │  │                          │
        └──────────────────────────┘  └────────────┬─────────────┘
                                                   │
                                                   ▼
                            ┌──────────────────────────────────────┐
                            │      AI GEMINI SINH PHẢN HỒI        │
                            │                                      │
                            │  "Dạ có Siro ho Prospan ạ.           │
                            │   Giá 93.000đ/chai..."               │
                            └────────────┬─────────────────────────┘
                                         │
                                         ▼
                            ┌──────────────────────────────────────┐
                            │   GỬI VỀ MÀN HÌNH (TỪNG CHỮ)         │
                            │                                      │
                            │  Bot: Dạ có Siro ho...               │
                            │       (từng chữ xuất hiện)           │
                            └──────────────────────────────────────┘
```

---

## 🔄 LUỒNG CHI TIẾT TỪNG BƯỚC

### BƯỚC 1: NGƯỜI DÙNG GỬI TIN NHẮN

```
┌─────────────────────────────────────┐
│  👤 Người dùng                       │
│                                     │
│  Gõ: "Tôi cần tìm Siro ho Prospan" │
│  Nhấn Enter                         │
└──────────────┬──────────────────────┘
               │
               │ POST /api/chatbot/chat
               │ message: "Tôi cần tìm..."
               │
               ▼
┌─────────────────────────────────────┐
│  💻 Frontend (ChatbotPopup.vue)     │
│                                     │
│  • Lấy tin nhắn                    │
│  • Gửi POST request                 │
│  • Đợi phản hồi                     │
└──────────────┬──────────────────────┘
               │
               ▼
```

### BƯỚC 2: SERVER NHẬN VÀ XỬ LÝ

```
┌─────────────────────────────────────┐
│  🖥️  Backend Server                 │
│                                     │
│  ChatbotController::chat()         │
│                                     │
│  • Validate message                 │
│  • Khởi tạo SSE Stream              │
│  • Bắt đầu xử lý                    │
└──────────────┬──────────────────────┘
               │
               ▼
```

### BƯỚC 3: TÌM KIẾM SẢN PHẨM

```
┌─────────────────────────────────────┐
│  🔍 ProductSearchService            │
│                                     │
│  1. Phân tích câu hỏi:              │
│     "Tôi cần tìm Siro ho Prospan"  │
│     ↓                                │
│     Từ khóa: "Siro", "ho", "Prospan"│
│                                     │
│  2. Tìm trong database:             │
│     WHERE ten_thuoc LIKE '%Siro%'  │
│       AND ten_thuoc LIKE '%ho%'     │
│       AND ten_thuoc LIKE '%Prospan%'│
│                                     │
│  3. Kết quả:                         │
│     • Siro ho Prospan                │
│     • Giá: 93.000 đ                 │
│     • Ảnh: prospan.jpg               │
│     • Còn hàng: Có                  │
└──────────────┬──────────────────────┘
               │
               ├─────────────────┐
               │                 │
               ▼                 ▼
```

### BƯỚC 4A: GỬI ẢNH SẢN PHẨM (NHANH)

```
┌─────────────────────────────────────┐
│  📸 Extract Product Images          │
│                                     │
│  • Lấy ảnh từ kết quả               │
│  • Format dữ liệu                   │
│  • Gửi qua SSE: event: images       │
└──────────────┬──────────────────────┘
               │
               │ SSE Stream
               │ event: images
               │ data: [{...}]
               │
               ▼
┌─────────────────────────────────────┐
│  💻 Frontend nhận và hiển thị       │
│                                     │
│  ┌─────────────────┐               │
│  │  [Ảnh sản phẩm]  │               │
│  │  Siro ho Prospan │               │
│  │    93.000 đ     │               │
│  │  [Chi tiết]     │               │
│  └─────────────────┘               │
│                                     │
│  ✅ Hiển thị NGAY LẬP TỨC           │
└─────────────────────────────────────┘
```

### BƯỚC 4B: CHUẨN BỊ CHO AI GEMINI

```
┌─────────────────────────────────────┐
│  📝 Format Data for Gemini          │
│                                     │
│  Input:                             │
│  {                                  │
│    medicines: [{                    │
│      ten_thuoc: "Siro ho Prospan", │
│      gia_ban: 93000,                │
│      ...                            │
│    }]                               │
│  }                                  │
│                                     │
│  Output:                            │
│  "THÔNG TIN SẢN PHẨM:               │
│   - Sản phẩm: Siro ho Prospan      │
│     Giá: 93.000 đ | Kho: Còn hàng" │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│  🎭 Build Prompt                    │
│                                     │
│  "Bạn là nhân viên bán hàng...     │
│                                     │
│   DỮ LIỆU SẢN PHẨM:                 │
│   [Thông tin đã format]             │
│                                     │
│   CÂU HỎI KHÁCH: 'Tôi cần tìm...'  │
│   TRẢ LỜI:"                         │
└──────────────┬──────────────────────┘
               │
               ▼
```

### BƯỚC 5: GỌI AI GEMINI

```
┌─────────────────────────────────────┐
│  🤖 Gọi Gemini API                  │
│                                     │
│  POST → Google Gemini               │
│  Model: gemini-2.5-flash            │
│                                     │
│  Gửi: Prompt đã chuẩn bị           │
│  Đợi: AI suy nghĩ và trả lời        │
└──────────────┬──────────────────────┘
               │
               │ Response
               │
               ▼
┌─────────────────────────────────────┐
│  🧠 AI Gemini Response             │
│                                     │
│  "Dạ có Siro ho Prospan             │
│   Engelhard ạ. Sản phẩm giá         │
│   93.000đ/chai và hiện tại bên em   │
│   còn hàng ạ."                      │
└──────────────┬──────────────────────┘
               │
               ▼
```

### BƯỚC 6: STREAM VỀ CLIENT

```
┌─────────────────────────────────────┐
│  📤 Stream Response                 │
│                                     │
│  Chia câu trả lời thành từng từ:   │
│  "Dạ" → "có" → "Siro" → "ho" ...   │
│                                     │
│  Gửi từng từ qua SSE:               │
│  event: update                      │
│  data: "Dạ "                        │
│                                     │
│  event: update                      │
│  data: "có "                        │
│                                     │
│  ... (tiếp tục)                     │
│                                     │
│  Delay 50ms mỗi 3 từ                │
└──────────────┬──────────────────────┘
               │
               │ SSE Stream
               │ (từng từ)
               │
               ▼
┌─────────────────────────────────────┐
│  💻 Frontend hiển thị               │
│                                     │
│  Bot: Dạ có Siro ho Prospan...     │
│       (từng chữ xuất hiện dần)     │
│                                     │
│  ✅ Hiệu ứng gõ chữ mượt mà         │
└─────────────────────────────────────┘
```

---

## 🎬 LUỒNG HOÀN CHỈNH (NHÌN TỔNG QUAN)

```
┌──────────────┐
│  👤 User     │  "Tôi cần tìm Siro ho Prospan"
└──────┬───────┘
       │
       │ POST
       ▼
┌─────────────────────────────────────────────────────────┐
│  🖥️  ChatbotController                                  │
│                                                         │
│  1. Nhận message                                       │
│  2. Gọi ProductSearchService ────────────────┐          │
│  3. Extract images ────────────────────┐    │          │
│  4. Format for Gemini ─────────────┐  │    │          │
│  5. Call Gemini API ────────────┐   │  │    │          │
│  6. Stream response ──────────┐ │   │  │    │          │
└────────────────────────────────│─│───│──│────│──────────┘
                                 │ │   │  │    │
                                 │ │   │  │    │
        ┌────────────────────────┘ │   │  │    │
        │                          │   │  │    │
        ▼                          │   │  │    │
┌───────────────────┐              │   │  │    │
│ 🔍 Search DB      │              │   │  │    │
│                   │              │   │  │    │
│ Tìm: Siro, ho,    │              │   │  │    │
│      Prospan      │              │   │  │    │
│                   │              │   │  │    │
│ Kết quả:          │              │   │  │    │
│ • Siro ho Prospan │              │   │  │    │
│ • 93.000 đ        │              │   │  │    │
│ • Còn hàng        │              │   │  │    │
└─────────┬─────────┘              │   │  │    │
          │                        │   │  │    │
          │                        │   │  │    │
          ├────────────────────────┘   │  │    │
          │                            │  │    │
          ├────────────────────────────┘  │    │
          │                               │    │
          ├───────────────────────────────┘    │
          │                                    │
          │                                    │
          ▼                                    │
┌───────────────────┐                         │
│ 📸 Extract Images │                         │
│                   │                         │
│ Gửi: event:images │                         │
└─────────┬─────────┘                         │
          │                                   │
          │                                   │
          ├───────────────────────────────────┘
          │
          ▼
┌───────────────────┐
│ 📝 Format Data    │
│                   │
│ Tạo prompt cho AI │
└─────────┬─────────┘
          │
          ▼
┌───────────────────┐
│ 🤖 Gemini API      │
│                   │
│ Sinh phản hồi     │
└─────────┬─────────┘
          │
          ▼
┌───────────────────┐
│ 📤 Stream Text    │
│                   │
│ Gửi từng từ      │
└─────────┬─────────┘
          │
          ▼
┌───────────────────┐
│ 💻 Frontend       │
│                   │
│ Hiển thị kết quả │
└───────────────────┘
```

---

## 📋 CHECKLIST CÁC BƯỚC

- [ ] **Bước 1:** User gửi tin nhắn
- [ ] **Bước 2:** Server nhận và validate
- [ ] **Bước 3:** Tìm kiếm sản phẩm trong DB
  - [ ] Phân tích từ khóa
  - [ ] Query database
  - [ ] Nhận kết quả
- [ ] **Bước 4A:** Gửi ảnh sản phẩm về client (ngay)
- [ ] **Bước 4B:** Chuẩn bị dữ liệu cho Gemini
  - [ ] Format thông tin sản phẩm
  - [ ] Tạo prompt
- [ ] **Bước 5:** Gọi Gemini API
- [ ] **Bước 6:** Stream phản hồi về client (từng chữ)
- [ ] **Hoàn thành:** User thấy ảnh + câu trả lời

---

## 🎯 ĐIỂM QUAN TRỌNG CẦN NHỚ

1. **Tìm kiếm trước, AI sau** - Để có thông tin chính xác
2. **Gửi ảnh ngay** - Không đợi AI, trải nghiệm tốt hơn
3. **Stream từng chữ** - Tạo cảm giác tự nhiên
4. **SSE** - Cho phép gửi dữ liệu liên tục

---

**Sơ đồ này giúp bạn hình dung rõ ràng luồng hoạt động của chatbot!** 🎓

