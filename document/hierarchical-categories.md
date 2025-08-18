# 🌳 HIERARCHICAL CATEGORIES SYSTEM - HỆ THỐNG DANH MỤC PHÂN CẤP

## 📚 OVERVIEW - TỔNG QUAN

**Tên gọi**: Self-Referencing Table / Adjacency List Model  
**Mục đích**: Tạo cấu trúc cây danh mục với nhiều cấp độ (unlimited depth)  
**Ứng dụng**: Danh mục sản phẩm (Thuốc > Thuốc dị ứng > Thuốc say xe)

---

## 🏗️ DATABASE DESIGN - THIẾT KẾ CSDL

### Table Structure
```sql
CREATE TABLE product_categories (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    parent_id BIGINT NULLABLE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (parent_id) REFERENCES product_categories(id) ON DELETE CASCADE
);
```

### Vai trò từng field:
- **`id`**: Định danh duy nhất
- **`name`**: Tên danh mục hiển thị
- **`parent_id`**: 🔑 CORE FIELD - Self-reference key
  - `NULL` = Root category (danh mục gốc)
  - `NOT NULL` = Child category (danh mục con)
- **`sort_order`**: Thứ tự sắp xếp custom
- **`CASCADE DELETE`**: Xóa cha → xóa tất cả con

---

## 🔄 ELOQUENT RELATIONSHIPS

### Model Definition
```php
class ProductCategory extends Model
{
    protected $fillable = ['name', 'parent_id', 'sort_order'];

    // Relationship đến danh mục cha
    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    // Relationship đến các danh mục con
    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }
}
```

### Relationship Breakdown:
- **`parent()`**: belongsTo - Lấy 1 danh mục cha
- **`children()`**: hasMany - Lấy nhiều danh mục con

---

## 📊 CORE METHODS FLOW

### 1. getCategoryTree() - EAGER LOADING METHOD

```php
public static function getCategoryTree()
{
    return self::whereNull('parent_id')                    // Step 1
        ->with(['children' => function($query) {           // Step 2
            $query->with(['children' => function($subQuery) {  // Step 3
                $subQuery->with('children');               // Step 4
            }]);
        }])
        ->orderBy('sort_order')                            // Step 5
        ->orderBy('name')                                  // Step 6
        ->get();                                          // Step 7
}
```

#### Query Execution Flow:
```
Step 1: whereNull('parent_id')
├── SQL: SELECT * FROM product_categories WHERE parent_id IS NULL
├── Kết quả: Root categories (A)

Step 2: ->with(['children' => function($query)
├── SQL: SELECT * FROM product_categories WHERE parent_id IN (1)
├── Kết quả: Level 2 categories (B, C)

Step 3: $query->with(['children' => function($subQuery)
├── SQL: SELECT * FROM product_categories WHERE parent_id IN (2, 3)
├── Kết quả: Level 3 categories (D, E)

Step 4: $subQuery->with('children')
├── SQL: SELECT * FROM product_categories WHERE parent_id IN (4, 5)
├── Kết quả: Level 4 categories (F, G)

Step 5-6: orderBy('sort_order')->orderBy('name')
├── Sắp xếp theo sort_order trước, sau đó theo name

Step 7: get()
├── Execute query và return Collection
```

#### Performance Analysis:
- **Queries**: 4 queries total (optimal)
- **N+1 Problem**: ✅ Avoided with eager loading
- **Max Depth**: 4 levels (hard-coded)

### 2. getAllCategoriesWithDepth() - RECURSIVE METHOD

```php
public static function getAllCategoriesWithDepth()
{
    $result = [];
    $rootCategories = self::whereNull('parent_id')
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();
    
    foreach ($rootCategories as $root) {
        self::addCategoryWithChildren($root, $result, 0);
    }
    
    return $result;
}

private static function addCategoryWithChildren($category, &$result, $depth)
{
    // Tạo prefix dựa trên depth
    $prefix = str_repeat(' - ', $depth);
    $result[$category->id] = $prefix . $category->name;
    
    // Recursive call cho tất cả children
    $children = $category->children()->orderBy('name')->get();
    foreach ($children as $child) {
        self::addCategoryWithChildren($child, $result, $depth + 1);
    }
}
```

#### Recursive Flow:
```
addCategoryWithChildren(A, [], 0)
├── $prefix = "" (depth=0)
├── $result[1] = "Thuốc"
├── children = [B, C]
├── addCategoryWithChildren(B, [], 1)
│   ├── $prefix = " - " (depth=1)
│   ├── $result[2] = " - Thuốc dị ứng"
│   ├── children = [D]
│   └── addCategoryWithChildren(D, [], 2)
│       ├── $prefix = " -  - " (depth=2)
│       └── $result[4] = " -  - Thuốc say xe"
└── addCategoryWithChildren(C, [], 1)
    ├── $prefix = " - " (depth=1)
    └── $result[3] = " - Thuốc ho"
```

#### Advantages:
- **Unlimited Depth**: Không giới hạn số cấp
- **Dynamic Prefix**: Tự động tạo prefix theo depth
- **Clean Output**: Format chuẩn cho dropdown

---

## 🎯 DATA FLOW EXAMPLES

### Sample Data:
```sql
INSERT INTO product_categories VALUES
(1, 'Thuốc', NULL, 1),           -- Root
(2, 'Thuốc dị ứng', 1, 0),      -- Level 2
(3, 'Thuốc ho', 1, 0),          -- Level 2  
(4, 'Thuốc say xe', 2, 0),      -- Level 3
(7, 'Hàng hóa', NULL, 2),       -- Root
(8, 'Dịch vụ', NULL, 3);        -- Root
```

### Tree Structure:
```
📁 Thuốc (id:1, parent:NULL, sort:1)
├── 📁 Thuốc dị ứng (id:2, parent:1, sort:0)
│   └── 📄 Thuốc say xe (id:4, parent:2, sort:0)
├── 📄 Thuốc ho (id:3, parent:1, sort:0)
📁 Hàng hóa (id:7, parent:NULL, sort:2)
📁 Dịch vụ (id:8, parent:NULL, sort:3)
```

### Method Outputs:

#### getCategoryTree() Output:
```php
Collection [
    ProductCategory {
        id: 1, name: "Thuốc", parent_id: null,
        children: Collection [
            ProductCategory {
                id: 2, name: "Thuốc dị ứng", parent_id: 1,
                children: Collection [
                    ProductCategory { id: 4, name: "Thuốc say xe", parent_id: 2, children: [] }
                ]
            },
            ProductCategory { id: 3, name: "Thuốc ho", parent_id: 1, children: [] }
        ]
    },
    ProductCategory { id: 7, name: "Hàng hóa", parent_id: null, children: [] },
    ProductCategory { id: 8, name: "Dịch vụ", parent_id: null, children: [] }
]
```

#### getAllCategoriesWithDepth() Output:
```php
[
    1 => "Thuốc",
    2 => " - Thuốc dị ứng",
    4 => " -  - Thuốc say xe",
    3 => " - Thuốc ho",
    7 => "Hàng hóa", 
    8 => "Dịch vụ"
]
```

---

## 🎨 FRONTEND INTEGRATION

### Dropdown Implementation:
```html
<select name="category_id" class="form-select">
    <option value="">Chọn danh mục</option>
    @foreach($categories as $id => $name)
        <option value="{{ $id }}">{{ $name }}</option>
    @endforeach
</select>
```

### Controller Usage:
```php
public function create()
{
    $categories = ProductCategory::getAllCategoriesWithDepth();
    return view('admin.products.create', compact('categories'));
}
```

### Visual Result:
```
Chọn danh mục
Thuốc
 - Thuốc dị ứng
 -  - Thuốc say xe
 - Thuốc ho
Hàng hóa
Dịch vụ
```

---

## ⚡ UTILITY METHODS

### Additional Helper Methods:
```php
// Kiểm tra danh mục gốc
public function isRoot()
{
    return is_null($this->parent_id);
}

// Kiểm tra danh mục lá (không có con)
public function isLeaf()
{
    return $this->children()->count() === 0;
}

// Lấy breadcrumb path
public function getFullPath()
{
    $path = [$this->name];
    $parent = $this->parent;
    
    while ($parent) {
        array_unshift($path, $parent->name);
        $parent = $parent->parent;
    }
    
    return implode(' > ', $path);
}
// Output: "Thuốc > Thuốc dị ứng > Thuốc say xe"

// Lấy tất cả descendants
public function getAllDescendants()
{
    $descendants = collect();
    
    foreach ($this->children as $child) {
        $descendants->push($child);
        $descendants = $descendants->merge($child->getAllDescendants());
    }
    
    return $descendants;
}
```

---

## 🚀 PERFORMANCE OPTIMIZATION

### Query Optimization:
```php
// ✅ Good: Eager loading
$categories = ProductCategory::with('children.children.children')->get();

// ❌ Bad: N+1 Problem
$categories = ProductCategory::all();
foreach ($categories as $category) {
    echo $category->children; // N+1 queries
}
```

### Caching Strategy:
```php
public static function getCachedCategoryTree()
{
    return Cache::remember('category_tree', 3600, function () {
        return self::getCategoryTree();
    });
}
```

---

## 🎯 BEST PRACTICES

### Do's ✅:
1. **Always use eager loading** để tránh N+1 problem
2. **Add sort_order field** để custom ordering
3. **Use CASCADE DELETE** để maintain data integrity
4. **Cache frequently accessed trees** để improve performance
5. **Validate parent_id** để avoid circular references

### Don'ts ❌:
1. **Không tạo circular references** (A parent của B, B parent của A)
2. **Không hard-code depth levels** nếu cần unlimited depth
3. **Không query trong loop** without eager loading
4. **Không delete parent** without handling children

### Validation Example:
```php
public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'parent_id' => [
            'nullable',
            'exists:product_categories,id',
            function ($attribute, $value, $fail) use ($request) {
                // Prevent self-reference
                if ($value == $request->id) {
                    $fail('Category cannot be parent of itself');
                }
                
                // Prevent circular reference (optional: implement depth check)
            }
        ]
    ]);
}
```

---

## 📋 SUMMARY - TÓM TẮT

### Core Concepts:
- **Self-Referencing Table**: 1 bảng tự tham chiếu qua parent_id
- **Adjacency List Model**: Mỗi node biết parent của nó
- **Eloquent Relationships**: belongsTo + hasMany
- **Eager Loading**: Tối ưu performance với nested with()
- **Recursive Methods**: Unlimited depth processing

### Key Benefits:
- **Simple Structure**: Chỉ cần 1 bảng
- **Flexible**: Thêm/sửa/xóa dễ dàng
- **Scalable**: Unlimited depth support
- **Laravel Native**: Tận dụng Eloquent ORM

### Common Use Cases:
- Product Categories
- Menu Systems  
- Organization Charts
- File/Folder Structures
- Comment Threads

**Đây là pattern chuẩn cho hierarchical data trong Laravel!** 🌟
