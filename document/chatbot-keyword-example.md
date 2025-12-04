# 💻 VÍ DỤ CODE - CÁCH BOT XỬ LÝ TIN NHẮN

## 📍 TỔNG QUAN VỀ FILE VÀ HÀM XỬ LÝ

### File chính xử lý:

#### 1. Controller - Nhận request từ user
**File:** `app/Http/Controllers/Api/ChatbotController.php`
- **Hàm:** `chat()` - Dòng 21-108
- **Nhiệm vụ:** Nhận tin nhắn từ user, gọi service tìm kiếm, gửi kết quả về

#### 2. Service - Xử lý tìm kiếm sản phẩm
**File:** `app/Services/Chatbot/ProductSearchService.php`

**Các hàm chính:**
- **`search()`** - Dòng 13-65
  - Hàm chính điều phối toàn bộ quá trình tìm kiếm
  
- **`extractKeywords()`** - Dòng 132-156
  - Trích xuất từ khóa từ tin nhắn
  - Loại bỏ stopwords
  
- **`filterGenericKeywords()`** - Dòng 256-261
  - Loại bỏ từ chung chung (thuốc, sản phẩm...)
  
- **`extractPriceRange()`** - Dòng 192-211
  - Trích xuất khoảng giá từ tin nhắn (ví dụ: "từ 50k đến 100k")
  
- **`normalizePrice()`** - Dòng 214-222
  - Chuẩn hóa giá tiền (ví dụ: "50k" → 50000)
  
- **`searchMedicines()`** - Dòng 68-90
  - Tìm kiếm thuốc trong database theo từ khóa
  
- **`searchMedicinesByPrice()`** - Dòng 264-271
  - Tìm kiếm thuốc trong database theo khoảng giá

### Luồng xử lý chi tiết:
```
1. ChatbotController::chat() (Dòng 21)
   ↓ Nhận tin nhắn từ user
   
2. ProductSearchService::search() (Dòng 13)
   ↓ Điều phối quá trình tìm kiếm
   
3. ProductSearchService::extractKeywords() (Dòng 132)
   ↓ Trích từ khóa, loại bỏ stopwords
   
4. ProductSearchService::filterGenericKeywords() (Dòng 256)
   ↓ Loại bỏ từ chung chung
   
5. ProductSearchService::extractPriceRange() (Dòng 192)
   ↓ Trích khoảng giá (nếu có)
   
6. ProductSearchService::searchMedicines() (Dòng 68)
   ↓ Tìm trong database
   
7. Trả về kết quả
```

---

## 🎯 VÍ DỤ 1: "TÔI CẦN TÌM SIRO HO PROSPAN"

### 📍 File và Hàm xử lý

**File:** `app/Http/Controllers/Api/ChatbotController.php`  
**Hàm:** `chat()` - Dòng 21-108

**File:** `app/Services/Chatbot/ProductSearchService.php`  
**Hàm:** `search()` - Dòng 13-65  
**Hàm:** `extractKeywords()` - Dòng 132-156  
**Hàm:** `filterGenericKeywords()` - Dòng 256-261  
**Hàm:** `searchMedicines()` - Dòng 68-90

### Input (Đầu vào)
```php
// File: app/Http/Controllers/Api/ChatbotController.php
// Dòng: 27
$userMessage = $request->input('message');
// $userMessage = "Tôi cần tìm Siro ho Prospan"
```

### Quá trình xử lý

#### Bước 1: Chuyển thành chữ thường
**File:** `app/Services/Chatbot/ProductSearchService.php`  
**Hàm:** `extractKeywords()` - Dòng 134
```php
$message = mb_strtolower($userMessage, 'UTF-8');
// Kết quả: "tôi cần tìm siro ho prospan"
```

#### Bước 2: Loại bỏ dấu câu
**File:** `app/Services/Chatbot/ProductSearchService.php`  
**Hàm:** `extractKeywords()` - Dòng 146
```php
$message = str_replace([',', '.', '?', '!', ';'], ' ', $message);
// Kết quả: "tôi cần tìm siro ho prospan" (không có dấu câu)
```

#### Bước 3: Chia thành từng từ
**File:** `app/Services/Chatbot/ProductSearchService.php`  
**Hàm:** `extractKeywords()` - Dòng 148
```php
$words = preg_split('/\s+/', $message);
// Kết quả: ["tôi", "cần", "tìm", "siro", "ho", "prospan"]
```

#### Bước 4: Loại bỏ stopwords
**File:** `app/Services/Chatbot/ProductSearchService.php`  
**Hàm:** `extractKeywords()` - Dòng 137-155
```php
$stopwords = [
    'bao', 'nhiêu', 'tiền', 'có', 'không', 'là', 'của', 'và',
    'cho', 'tôi', 'mua', 'được', 'thì', 'như', 'nào', 'gì',
    'về', 'này', 'đó', 'vậy', 'à', 'ạ', 'nhé', 'nha',
    'từ', 'đến', 'tới',
    'tìm', 'sản', 'phẩm', 'muốn', 'cần',
    'hi', 'hello', 'xin', 'chào', 'shop', 'ad', 'admin', 'ơi', 'alo'
];

$keywords = array_filter($words, function ($word) use ($stopwords) {
    return !in_array($word, $stopwords) && mb_strlen($word, 'UTF-8') >= 2;
});

// Kết quả: ["siro", "ho", "prospan"]
```

**Giải thích:**
- "tôi" → Có trong stopwords → BỎ
- "cần" → Có trong stopwords → BỎ
- "tìm" → Có trong stopwords → BỎ
- "siro" → KHÔNG có trong stopwords → GIỮ LẠI
- "ho" → KHÔNG có trong stopwords → GIỮ LẠI
- "prospan" → KHÔNG có trong stopwords → GIỮ LẠI

#### Bước 5: Loại bỏ từ chung chung
**File:** `app/Services/Chatbot/ProductSearchService.php`  
**Hàm:** `filterGenericKeywords()` - Dòng 256-261  
**Được gọi từ:** `search()` - Dòng 21
```php
$genericWords = [
    'thuốc', 'sản', 'phẩm', 'hàng', 'hóa',
    'tìm', 'cần', 'muốn', 'giá', 'chi', 'tiết', 'bán', 'mua'
];

$keywords = array_filter($keywords, function ($keyword) use ($genericWords) {
    return !in_array($keyword, $genericWords);
});

// Kết quả: ["siro", "ho", "prospan"] (không có từ nào bị loại)
```

#### Bước 6: Tìm trong database
**File:** `app/Services/Chatbot/ProductSearchService.php`  
**Hàm:** `searchMedicines()` - Dòng 68-90  
**Được gọi từ:** `search()` - Dòng 46
```php
$query = Medicine::where('ban_truc_tiep', true);

foreach ($keywords as $keyword) {
    $query->where(function ($q) use ($keyword) {
        $q->where('ten_thuoc', 'like', '%' . $keyword . '%')
          ->orWhere('ten_viet_tat', 'like', '%' . $keyword . '%')
          ->orWhere('ma_hang', 'like', '%' . $keyword . '%')
          ->orWhere('ma_vach', 'like', '%' . $keyword . '%')
          ->orWhere('hoat_chat', 'like', '%' . $keyword . '%');
    });
}

$results = $query->limit(5)->get();
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

### Output (Đầu ra)
```php
[
    {
        id: 123,
        ten_thuoc: "Siro ho Prospan Engelhard",
        gia_ban: 93000,
        image: "prospan.jpg",
        ton_kho: 50
    }
]
```

---

## 🎯 VÍ DỤ 2: "SIRO HO BAO NHIÊU TIỀN?"

### 📍 File và Hàm xử lý

**File:** `app/Http/Controllers/Api/ChatbotController.php`  
**Hàm:** `chat()` - Dòng 21-108

**File:** `app/Services/Chatbot/ProductSearchService.php`  
**Hàm:** `search()` - Dòng 13-65  
**Hàm:** `extractKeywords()` - Dòng 132-156  
**Hàm:** `filterGenericKeywords()` - Dòng 256-261  
**Hàm:** `searchMedicines()` - Dòng 68-90

### Input
```php
// File: app/Http/Controllers/Api/ChatbotController.php
// Dòng: 27
$userMessage = "Siro ho bao nhiêu tiền?";
```

### Quá trình xử lý

#### Bước 1-3: Chuẩn hóa
```php
$message = "siro ho bao nhiêu tiền";
$words = ["siro", "ho", "bao", "nhiêu", "tiền"];
```

#### Bước 4: Loại bỏ stopwords
```php
// "bao", "nhiêu", "tiền" → Có trong stopwords → BỎ
// "siro", "ho" → KHÔNG có trong stopwords → GIỮ LẠI
$keywords = ["siro", "ho"];
```

#### Bước 5: Loại bỏ từ chung
```php
// Không có từ nào bị loại
$keywords = ["siro", "ho"];
```

#### Bước 6: Tìm trong database
```sql
SELECT * FROM medicines 
WHERE ban_truc_tiep = true
  AND (ten_thuoc LIKE '%siro%' OR ...)
  AND (ten_thuoc LIKE '%ho%' OR ...)
LIMIT 5
```

### Output
```php
[
    {
        id: 123,
        ten_thuoc: "Siro ho Prospan Engelhard",
        gia_ban: 93000
    },
    {
        id: 124,
        ten_thuoc: "Siro ho ATessen",
        gia_ban: 50000
    }
]
```

---

## 🎯 VÍ DỤ 3: "TÔI CẦN THUỐC GIẢM ĐAU"

### 📍 File và Hàm xử lý

**File:** `app/Http/Controllers/Api/ChatbotController.php`  
**Hàm:** `chat()` - Dòng 21-108

**File:** `app/Services/Chatbot/ProductSearchService.php`  
**Hàm:** `search()` - Dòng 13-65  
**Hàm:** `extractKeywords()` - Dòng 132-156  
**Hàm:** `filterGenericKeywords()` - Dòng 256-261  
**Hàm:** `searchMedicines()` - Dòng 68-90

### Input
```php
// File: app/Http/Controllers/Api/ChatbotController.php
// Dòng: 27
$userMessage = "Tôi cần thuốc giảm đau";
```

### Quá trình xử lý

#### Bước 1-3: Chuẩn hóa
```php
$message = "tôi cần thuốc giảm đau";
$words = ["tôi", "cần", "thuốc", "giảm", "đau"];
```

#### Bước 4: Loại bỏ stopwords
```php
// "tôi", "cần" → Có trong stopwords → BỎ
// "thuốc", "giảm", "đau" → KHÔNG có trong stopwords → GIỮ LẠI
$keywords = ["thuốc", "giảm", "đau"];
```

#### Bước 5: Loại bỏ từ chung
```php
// "thuốc" → Có trong genericWords → BỎ
// "giảm", "đau" → KHÔNG có trong genericWords → GIỮ LẠI
$keywords = ["giảm", "đau"];
```

#### Bước 6: Tìm trong database
```sql
SELECT * FROM medicines 
WHERE ban_truc_tiep = true
  AND (ten_thuoc LIKE '%giảm%' OR mo_ta LIKE '%giảm%' OR ...)
  AND (ten_thuoc LIKE '%đau%' OR mo_ta LIKE '%đau%' OR ...)
LIMIT 5
```

### Output
```php
[
    {
        id: 201,
        ten_thuoc: "Paracetamol 500mg - Giảm đau hạ sốt",
        gia_ban: 15000
    },
    {
        id: 202,
        ten_thuoc: "Ibuprofen 400mg - Giảm đau kháng viêm",
        gia_ban: 25000
    }
]
```

---

## 🎯 VÍ DỤ 4: "XIN CHÀO"

### 📍 File và Hàm xử lý

**File:** `app/Http/Controllers/Api/ChatbotController.php`  
**Hàm:** `chat()` - Dòng 21-108

**File:** `app/Services/Chatbot/ProductSearchService.php`  
**Hàm:** `search()` - Dòng 13-65  
**Hàm:** `extractKeywords()` - Dòng 132-156  
**Early Exit:** `search()` - Dòng 26-32

### Input
```php
// File: app/Http/Controllers/Api/ChatbotController.php
// Dòng: 27
$userMessage = "Xin chào";
```

### Quá trình xử lý

#### Bước 1-3: Chuẩn hóa
```php
$message = "xin chào";
$words = ["xin", "chào"];
```

#### Bước 4: Loại bỏ stopwords
```php
// "xin", "chào" → Có trong stopwords → BỎ
$keywords = []; // RỖNG
```

#### Bước 5: Early Exit (Thoát sớm)
**File:** `app/Services/Chatbot/ProductSearchService.php`  
**Hàm:** `search()` - Dòng 26-32
```php
if (empty($keywords) && empty($priceRange)) {
    // KHÔNG tìm trong database
    return [
        'medicines' => collect([]),
        'goods'     => collect([]),
        'services'  => collect([]),
    ];
}
```

### Output
```php
[] // Rỗng - Không tìm kiếm, chỉ trả lời chào hỏi
```

---

## 🎯 VÍ DỤ 5: "TÔI CẦN THUỐC TỪ 50K ĐẾN 100K"

### 📍 File và Hàm xử lý

**File:** `app/Http/Controllers/Api/ChatbotController.php`  
**Hàm:** `chat()` - Dòng 21-108

**File:** `app/Services/Chatbot/ProductSearchService.php`  
**Hàm:** `search()` - Dòng 13-65  
**Hàm:** `extractPriceRange()` - Dòng 192-211  
**Hàm:** `normalizePrice()` - Dòng 214-222  
**Hàm:** `searchMedicinesByPrice()` - Dòng 264-271

### Input
```php
// File: app/Http/Controllers/Api/ChatbotController.php
// Dòng: 27
$userMessage = "Tôi cần thuốc từ 50k đến 100k";
```

### Quá trình xử lý

#### Bước 1-5: Xử lý từ khóa
**File:** `app/Services/Chatbot/ProductSearchService.php`  
**Hàm:** `search()` - Dòng 16, 21
```php
$keywords = []; // Rỗng vì "thuốc" bị loại
```

#### Bước 6: Trích xuất khoảng giá
**File:** `app/Services/Chatbot/ProductSearchService.php`  
**Hàm:** `extractPriceRange()` - Dòng 192-211  
**Được gọi từ:** `search()` - Dòng 17
```php
private function extractPriceRange(string $message): ?array
{
    // Tìm pattern: "từ 50k đến 100k"
    $pattern = '/từ\s*(\d+)\s*(?:ngàn|k|000)?\s*(?:đến|tới|-)\s*(\d+)\s*(?:ngàn|k|000)?/i';
    
    if (preg_match($pattern, $message, $matches)) {
        $min = $this->normalizePrice($matches[1]); // 50000
        $max = $this->normalizePrice($matches[2]); // 100000
        return ['min' => $min, 'max' => $max];
    }
    
    return null;
}

// Kết quả: ['min' => 50000, 'max' => 100000]
```

**Hàm chuẩn hóa giá:** `normalizePrice()` - Dòng 214-222
```php
private function normalizePrice(string $price): ?int
{
    $price = preg_replace('/[^\d.,]/', '', $price);
    $price = str_replace([',', '.'], '', $price);
    if (preg_match('/(\d+)\s*(?:k|ngàn)/i', $price, $m)) {
        return (int) $m[1] * 1000; // "50k" → 50000
    }
    return $price ? (int) $price : null;
}
```

#### Bước 7: Tìm theo giá
**File:** `app/Services/Chatbot/ProductSearchService.php`  
**Hàm:** `searchMedicinesByPrice()` - Dòng 264-271  
**Được gọi từ:** `search()` - Dòng 43, 49
```php
// Vì không có keywords, tìm theo giá
$query = Medicine::where('ban_truc_tiep', true)
                 ->whereBetween('gia_ban', [50000, 100000])
                 ->limit(5)
                 ->get();
```

### Output
```php
[
    {
        id: 301,
        ten_thuoc: "Paracetamol 500mg",
        gia_ban: 15000
    },
    {
        id: 302,
        ten_thuoc: "Ibuprofen 400mg",
        gia_ban: 25000
    },
    // ... các sản phẩm có giá từ 50k đến 100k
]
```

---

## 📊 BẢNG SO SÁNH CÁC VÍ DỤ

| Câu hỏi | Từ khóa sau lọc | Kết quả |
|---------|----------------|---------|
| "Tôi cần tìm Siro ho Prospan" | ["siro", "ho", "prospan"] | Tìm chính xác sản phẩm |
| "Siro ho bao nhiêu tiền?" | ["siro", "ho"] | Tìm tất cả siro ho |
| "Tôi cần thuốc giảm đau" | ["giảm", "đau"] | Tìm thuốc giảm đau |
| "Xin chào" | [] | Không tìm, chỉ chào hỏi |
| "Từ 50k đến 100k" | [] + priceRange | Tìm theo giá |

---

## 💡 LƯU Ý QUAN TRỌNG

### 1. Logic AND (Tất cả từ khóa phải có)
**File:** `app/Services/Chatbot/ProductSearchService.php`  
**Hàm:** `searchMedicines()` - Dòng 74-82
```php
// "Siro ho Prospan" → Phải có CẢ 3 từ
// KHÔNG phải: Có "Siro" HOẶC có "ho" HOẶC có "Prospan"
foreach ($keywords as $keyword) {
    $query->where(function ($q) use ($keyword) {
        // Mỗi từ khóa phải có trong sản phẩm
    });
}
```

### 2. Tìm trong nhiều trường
**File:** `app/Services/Chatbot/ProductSearchService.php`  
**Hàm:** `searchMedicines()` - Dòng 76-80
```php
// Mỗi từ khóa tìm trong:
$q->where('ten_thuoc', 'like', '%' . $keyword . '%')      // Tên thuốc
  ->orWhere('ten_viet_tat', 'like', '%' . $keyword . '%') // Tên viết tắt
  ->orWhere('ma_hang', 'like', '%' . $keyword . '%')      // Mã hàng
  ->orWhere('ma_vach', 'like', '%' . $keyword . '%')       // Mã vạch
  ->orWhere('hoat_chat', 'like', '%' . $keyword . '%');    // Hoạt chất
```

### 3. Giới hạn 5 kết quả
**File:** `app/Services/Chatbot/ProductSearchService.php`  
**Hàm:** `searchMedicines()` - Dòng 89
```php
->limit(5) // Chỉ lấy 5 sản phẩm đầu tiên
```

### 4. Chỉ tìm sản phẩm đang bán
**File:** `app/Services/Chatbot/ProductSearchService.php`  
**Hàm:** `searchMedicines()` - Dòng 70
```php
->where('ban_truc_tiep', true) // Chỉ sản phẩm đang bán trực tiếp
```

---

## 🎯 KẾT LUẬN

**Quá trình xử lý:**

```
ChatbotController::chat() (Dòng 21)
  ↓
ProductSearchService::search() (Dòng 13)
  ↓
ProductSearchService::extractKeywords() (Dòng 132)
  ↓
ProductSearchService::filterGenericKeywords() (Dòng 256)
  ↓
ProductSearchService::searchMedicines() (Dòng 68)
  ↓
Kết quả
```

**File và hàm chính:**

| Bước | File | Hàm | Dòng |
|------|------|-----|------|
| Nhận request | `ChatbotController.php` | `chat()` | 21-108 |
| Tìm kiếm chính | `ProductSearchService.php` | `search()` | 13-65 |
| Trích từ khóa | `ProductSearchService.php` | `extractKeywords()` | 132-156 |
| Lọc từ chung | `ProductSearchService.php` | `filterGenericKeywords()` | 256-261 |
| Tìm thuốc | `ProductSearchService.php` | `searchMedicines()` | 68-90 |
| Tìm theo giá | `ProductSearchService.php` | `searchMedicinesByPrice()` | 264-271 |
| Trích khoảng giá | `ProductSearchService.php` | `extractPriceRange()` | 192-211 |

**Điểm quan trọng:**
- ✅ Loại bỏ từ không cần thiết để tìm chính xác
- ✅ Tìm trong nhiều trường để tăng khả năng tìm thấy
- ✅ Dùng AND logic để đảm bảo kết quả chính xác
- ✅ Early exit nếu không có từ khóa

---

**Các ví dụ này giúp bạn hiểu rõ cách bot xử lý tin nhắn!** 🎓

