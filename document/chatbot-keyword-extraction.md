# 🔍 CÁCH BOT NHẬN TIN NHẮN VÀ TÌM SẢN PHẨM - CHI TIẾT NHẤT

## 🎯 TÓM TẮT NHANH

**Bot làm gì khi bạn gửi tin nhắn?**
1. Nhận tin nhắn: "Tôi cần tìm Siro ho Prospan"
2. Chia thành từng từ: ["Tôi", "cần", "tìm", "Siro", "ho", "Prospan"]
3. Loại bỏ từ không cần thiết: ["Siro", "ho", "Prospan"]
4. Tìm trong database với các từ khóa này
5. Trả về kết quả

---

## 📖 VÍ DỤ CỤ THỂ: "TÔI CẦN TÌM SIRO HO PROSPAN"

### BƯỚC 1: NHẬN TIN NHẮN

```
Bạn gửi: "Tôi cần tìm Siro ho Prospan"
         ↓
Bot nhận: "Tôi cần tìm Siro ho Prospan"
```

**Code:**
```php
// ChatbotController.php
$userMessage = $request->input('message');
// $userMessage = "Tôi cần tìm Siro ho Prospan"
```

---

### BƯỚC 2: CHUYỂN THÀNH CHỮ THƯỜNG

**Tại sao?** Để tìm kiếm không phân biệt hoa/thường

```
"Tôi cần tìm Siro ho Prospan"
         ↓
"tôi cần tìm siro ho prospan"
```

**Code:**
```php
$message = mb_strtolower($message, 'UTF-8');
// "tôi cần tìm siro ho prospan"
```

---

### BƯỚC 3: LOẠI BỎ DẤU CÂU

**Tại sao?** Để tách từ dễ hơn

```
"tôi cần tìm siro ho prospan?"
         ↓
"tôi cần tìm siro ho prospan"
```

**Code:**
```php
$message = str_replace([',', '.', '?', '!', ';'], ' ', $message);
```

---

### BƯỚC 4: CHIA THÀNH TỪNG TỪ

```
"tôi cần tìm siro ho prospan"
         ↓
["tôi", "cần", "tìm", "siro", "ho", "prospan"]
```

**Code:**
```php
$words = preg_split('/\s+/', $message);
// ["tôi", "cần", "tìm", "siro", "ho", "prospan"]
```

---

### BƯỚC 5: LOẠI BỎ TỪ KHÔNG CẦN THIẾT (STOPWORDS)

**Danh sách từ không cần thiết:**
```php
$stopwords = [
    'bao', 'nhiêu', 'tiền', 'có', 'không', 'là', 'của', 'và',
    'cho', 'tôi', 'mua', 'được', 'thì', 'như', 'nào', 'gì',
    'về', 'này', 'đó', 'vậy', 'à', 'ạ', 'nhé', 'nha',
    'từ', 'đến', 'tới',
    'tìm', 'sản', 'phẩm', 'muốn', 'cần',
    'hi', 'hello', 'xin', 'chào', 'shop', 'ad', 'admin', 'ơi', 'alo'
];
```

**Quá trình lọc:**

```
Từng từ: ["tôi", "cần", "tìm", "siro", "ho", "prospan"]
         ↓
Kiểm tra từng từ:
- "tôi" → Có trong stopwords? ✅ CÓ → BỎ
- "cần" → Có trong stopwords? ✅ CÓ → BỎ
- "tìm" → Có trong stopwords? ✅ CÓ → BỎ
- "siro" → Có trong stopwords? ❌ KHÔNG → GIỮ LẠI
- "ho" → Có trong stopwords? ❌ KHÔNG → GIỮ LẠI
- "prospan" → Có trong stopwords? ❌ KHÔNG → GIỮ LẠI
         ↓
Kết quả: ["siro", "ho", "prospan"]
```

**Code:**
```php
$keywords = array_filter($words, function ($word) use ($stopwords) {
    // Giữ lại từ >= 2 ký tự và không nằm trong stopwords
    return !in_array($word, $stopwords) && mb_strlen($word, 'UTF-8') >= 2;
});
// ["siro", "ho", "prospan"]
```

**Lưu ý:** Chỉ giữ từ có >= 2 ký tự (bỏ từ 1 ký tự như "a", "b")

---

### BƯỚC 6: LOẠI BỎ TỪ CHUNG CHUNG

**Danh sách từ chung chung:**
```php
$genericWords = [
    'thuốc', 'sản', 'phẩm', 'hàng', 'hóa',
    'tìm', 'cần', 'muốn', 'giá', 'chi', 'tiết', 'bán', 'mua'
];
```

**Ví dụ:** Nếu bạn gõ "Tôi cần thuốc siro ho"

```
Sau bước 5: ["thuốc", "siro", "ho"]
         ↓
Kiểm tra:
- "thuốc" → Có trong genericWords? ✅ CÓ → BỎ
- "siro" → Có trong genericWords? ❌ KHÔNG → GIỮ LẠI
- "ho" → Có trong genericWords? ❌ KHÔNG → GIỮ LẠI
         ↓
Kết quả: ["siro", "ho"]
```

**Code:**
```php
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
```

---

### BƯỚC 7: TÌM KIẾM TRONG DATABASE

**Với từ khóa:** `["siro", "ho", "prospan"]`

**Logic tìm kiếm:** Sản phẩm phải chứa TẤT CẢ các từ khóa (AND logic)

```
Tìm sản phẩm có:
- Tên chứa "siro" VÀ
- Tên chứa "ho" VÀ
- Tên chứa "prospan"
```

**Code:**
```php
private function searchMedicines(array $keywords, ?array $priceRange = null)
{
    $query = Medicine::where('ban_truc_tiep', true);
    
    // Duyệt qua từng từ khóa
    foreach ($keywords as $keyword) {
        $query->where(function ($q) use ($keyword) {
            // Sản phẩm phải chứa từ khóa này trong MỘT TRONG CÁC trường:
            $q->where('ten_thuoc', 'like', '%' . $keyword . '%')      // Tên thuốc
              ->orWhere('ten_viet_tat', 'like', '%' . $keyword . '%')  // Tên viết tắt
              ->orWhere('ma_hang', 'like', '%' . $keyword . '%')        // Mã hàng
              ->orWhere('ma_vach', 'like', '%' . $keyword . '%')       // Mã vạch
              ->orWhere('hoat_chat', 'like', '%' . $keyword . '%');     // Hoạt chất
        });
    }
    
    return $query->limit(5)->get();
}
```

**SQL tương đương:**
```sql
SELECT * FROM medicines 
WHERE ban_truc_tiep = true
  AND (
    ten_thuoc LIKE '%siro%' 
    OR ten_viet_tat LIKE '%siro%' 
    OR ma_hang LIKE '%siro%'
    OR ma_vach LIKE '%siro%'
    OR hoat_chat LIKE '%siro%'
  )
  AND (
    ten_thuoc LIKE '%ho%' 
    OR ten_viet_tat LIKE '%ho%' 
    OR ma_hang LIKE '%ho%'
    OR ma_vach LIKE '%ho%'
    OR hoat_chat LIKE '%ho%'
  )
  AND (
    ten_thuoc LIKE '%prospan%' 
    OR ten_viet_tat LIKE '%prospan%' 
    OR ma_hang LIKE '%prospan%'
    OR ma_vach LIKE '%prospan%'
    OR hoat_chat LIKE '%prospan%'
  )
LIMIT 5
```

**Kết quả tìm được:**
```
Sản phẩm: Siro ho Prospan Engelhard
Giá: 93.000 đ
Ảnh: prospan.jpg
Còn hàng: Có
```

---

## 🎬 VÍ DỤ ĐẦY ĐỦ: TỪNG BƯỚC

### Câu hỏi: "Tôi cần tìm Siro ho Prospan"

```
BƯỚC 1: Nhận tin nhắn
"Tôi cần tìm Siro ho Prospan"
         ↓

BƯỚC 2: Chuyển thành chữ thường
"tôi cần tìm siro ho prospan"
         ↓

BƯỚC 3: Loại bỏ dấu câu
"tôi cần tìm siro ho prospan"
         ↓

BƯỚC 4: Chia thành từng từ
["tôi", "cần", "tìm", "siro", "ho", "prospan"]
         ↓

BƯỚC 5: Loại bỏ stopwords
["siro", "ho", "prospan"]
         ↓

BƯỚC 6: Loại bỏ từ chung chung
["siro", "ho", "prospan"] (không có từ nào bị loại)
         ↓

BƯỚC 7: Tìm trong database
Tìm sản phẩm có:
- ten_thuoc LIKE '%siro%' AND
- ten_thuoc LIKE '%ho%' AND
- ten_thuoc LIKE '%prospan%'
         ↓

KẾT QUẢ:
Siro ho Prospan Engelhard (93.000 đ)
```

---

## 🔍 CÁC TRƯỜNG HỢP ĐẶC BIỆT

### 1. Câu hỏi chỉ có từ chào hỏi

**Ví dụ:** "Xin chào"

```
BƯỚC 1-4: ["xin", "chào"]
         ↓
BƯỚC 5: Loại bỏ stopwords
- "xin" → Có trong stopwords? ✅ CÓ → BỎ
- "chào" → Có trong stopwords? ✅ CÓ → BỎ
         ↓
Kết quả: [] (RỖNG)
         ↓
BƯỚC 7: KHÔNG tìm trong database (Early Exit)
         ↓
Kết quả: Trả về rỗng, không gọi AI
```

**Code:**
```php
// Early Exit - Thoát sớm nếu không có từ khóa
if (empty($keywords) && empty($priceRange)) {
    return [
        'medicines' => collect([]),
        'goods'     => collect([]),
        'services'  => collect([]),
    ];
}
```

---

### 2. Câu hỏi có khoảng giá

**Ví dụ:** "Tôi cần thuốc từ 50k đến 100k"

```
BƯỚC 1-6: Từ khóa: [] (rỗng vì "thuốc" bị loại)
         ↓
BƯỚC 7: Nhưng có priceRange: [min: 50000, max: 100000]
         ↓
Tìm sản phẩm theo GIÁ:
WHERE gia_ban BETWEEN 50000 AND 100000
```

**Code:**
```php
private function extractPriceRange(string $message): ?array
{
    // Tìm pattern: "từ 50k đến 100k"
    $patterns = [
        '/từ\s*(\d+)\s*(?:ngàn|k|000)?\s*(?:đến|tới|-)\s*(\d+)\s*(?:ngàn|k|000)?/i',
        '/(\d+)\s*(?:ngàn|k|000)?\s*(?:đến|tới|-)\s*(\d+)\s*(?:ngàn|k|000)?/i',
    ];
    
    // Nếu tìm thấy → Trả về [min, max]
    // Nếu không → Trả về null
}
```

---

### 3. Câu hỏi có từ viết tắt

**Ví dụ:** "Tôi cần Paracetamol 500mg"

```
BƯỚC 1-6: Từ khóa: ["paracetamol", "500mg"]
         ↓
BƯỚC 7: Tìm trong database
WHERE ten_thuoc LIKE '%paracetamol%' 
  AND ten_thuoc LIKE '%500mg%'
```

**Lưu ý:** Tìm trong nhiều trường:
- `ten_thuoc` (Tên thuốc)
- `ten_viet_tat` (Tên viết tắt)
- `ma_hang` (Mã hàng)
- `ma_vach` (Mã vạch)
- `hoat_chat` (Hoạt chất)

---

### 4. Câu hỏi có nhiều từ khóa

**Ví dụ:** "Tôi cần thuốc giảm đau hạ sốt"

```
BƯỚC 1-6: Từ khóa: ["giảm", "đau", "hạ", "sốt"]
         ↓
BƯỚC 7: Tìm sản phẩm có TẤT CẢ các từ:
WHERE ten_thuoc LIKE '%giảm%' 
  AND ten_thuoc LIKE '%đau%'
  AND ten_thuoc LIKE '%hạ%'
  AND ten_thuoc LIKE '%sốt%'
```

**Vấn đề:** Có thể không tìm thấy vì quá nhiều điều kiện

**Giải pháp:** Hệ thống sẽ tìm trong các trường khác nhau (ten_thuoc, mo_ta, hoat_chat...)

---

## 📊 BẢNG TÓM TẮT

| Bước | Hành động | Ví dụ |
|------|-----------|-------|
| 1 | Nhận tin nhắn | "Tôi cần tìm Siro ho Prospan" |
| 2 | Chuyển chữ thường | "tôi cần tìm siro ho prospan" |
| 3 | Loại bỏ dấu câu | "tôi cần tìm siro ho prospan" |
| 4 | Chia thành từ | ["tôi", "cần", "tìm", "siro", "ho", "prospan"] |
| 5 | Loại bỏ stopwords | ["siro", "ho", "prospan"] |
| 6 | Loại bỏ từ chung | ["siro", "ho", "prospan"] |
| 7 | Tìm trong database | WHERE ten_thuoc LIKE '%siro%' AND ... |

---

## 💡 TẠI SAO LÀM NHƯ VẬY?

### 1. Tại sao loại bỏ stopwords?
- **Lý do:** Từ như "tôi", "cần", "tìm" không giúp tìm sản phẩm
- **Ví dụ:** "Tôi cần tìm Siro ho" → Chỉ cần "Siro", "ho"

### 2. Tại sao loại bỏ từ chung chung?
- **Lý do:** Từ như "thuốc", "sản phẩm" quá chung, không cụ thể
- **Ví dụ:** "Tôi cần thuốc siro ho" → Chỉ cần "siro", "ho"

### 3. Tại sao dùng AND logic (tất cả từ khóa)?
- **Lý do:** Để tìm chính xác sản phẩm người dùng muốn
- **Ví dụ:** "Siro ho Prospan" → Phải có cả 3 từ, không chỉ "Siro"

### 4. Tại sao tìm trong nhiều trường?
- **Lý do:** Người dùng có thể nhớ tên, mã hàng, hoặc hoạt chất
- **Ví dụ:** "Paracetamol" có thể ở `ten_thuoc` hoặc `hoat_chat`

---

## 🎯 KẾT LUẬN

**Quá trình xử lý tin nhắn:**

```
Tin nhắn → Chia từ → Lọc từ → Tìm database → Kết quả
```

**Điểm quan trọng:**
1. ✅ Loại bỏ từ không cần thiết để tìm chính xác hơn
2. ✅ Tìm trong nhiều trường để tăng khả năng tìm thấy
3. ✅ Dùng AND logic để đảm bảo kết quả chính xác
4. ✅ Giới hạn 5 kết quả để phản hồi nhanh

---

**Tài liệu này giải thích chi tiết cách bot xử lý tin nhắn và tìm sản phẩm!** 🎓

