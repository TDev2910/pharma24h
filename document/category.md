# Quản lý Danh mục (Category Management)

## 🌳 Giới thiệu
Hệ thống sử dụng cấu trúc **cây danh mục (Category Tree)** để quản lý các nhóm hàng hóa/thuốc.  
Điều này giúp dễ dàng phân loại từ tổng quát đến chi tiết.

---

## 🧭 Flow tạo danh mục

1. **Tạo Danh mục CHA (root)**  
   - Ví dụ: `Thuốc`

2. **Tạo Danh mục CON (child) gắn với CHA**  
   - Ví dụ: `Thuốc dị ứng` (parent_id = Thuốc)

3. **Tạo Danh mục CON CỦA CON (sub-child)**  
   - Ví dụ: `Thuốc say tàu xe` (parent_id = Thuốc dị ứng)

4. **Có thể tiếp tục thêm nhiều cấp con khác nếu cần**

---

## 🔎 Ví dụ trong Database (Laravel Migration)

```php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->unsignedBigInteger('parent_id')->nullable(); // Tham chiếu đến danh mục cha
    $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
    $table->timestamps();
});
```

### Dữ liệu mẫu
| id | name             | parent_id |
|----|------------------|-----------|
| 1  | Thuốc            | null      |
| 2  | Thuốc dị ứng     | 1         |
| 3  | Thuốc say tàu xe | 2         |

---

## 📌 Kết quả trực quan 

```
Thuốc
   └── Thuốc dị ứng
         └── Thuốc say tàu xe
```

---

## ✅ Lợi ích
- Quản lý phân cấp rõ ràng
- Mở rộng linh hoạt
- Ứng dụng tốt trong hệ thống quản lý kho, thương mại điện tử, y tế
