# System Patterns - Suckhoe24h

## Architecture Overview
Hệ thống sử dụng **MVC pattern** với Laravel framework, có cấu trúc rõ ràng và dễ bảo trì.

## Key Design Patterns

### 1. Controller Pattern
- **Admin Controllers**: `App\Http\Controllers\Admin\Product\`
- **User Controllers**: `App\Http\Controllers\User\`
- **Auth Controllers**: `App\Http\Controllers\Auth\`

**Example**: `MedicineController.php`
```php
class MedicineController extends Controller
{
    public function index() // List view
    public function create() // Form view
    public function store() // Create action
    public function edit() // Edit form
    public function update() // Update action
    public function destroy() // Delete action
}
```

### 2. Model Pattern
- **Eloquent Models** với relationships
- **Fillable attributes** cho mass assignment
- **Accessors/Mutators** cho data formatting
- **Scopes** cho query optimization

**Example**: `Medicine.php`
```php
class Medicine extends Model
{
    protected $fillable = [...];
    
    // Relationships
    public function category() { return $this->belongsTo(...); }
    
    // Accessors
    public function getGiaBanFormattedAttribute() { ... }
}
```

### 3. View Pattern
- **Blade Templates** với components
- **Layout inheritance** (layouts/app.blade.php)
- **Component separation** (header.blade.php, footer.blade.php)
- **Admin/User separation** trong views

### 4. Route Pattern
- **Grouped routes** theo chức năng
- **Named routes** cho dễ maintain
- **Middleware protection** cho admin routes

```php
// admin.php
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::resource('products', ProductController::class);
});

// user.php  
Route::prefix('user')->middleware('auth')->group(function () {
    Route::get('profile', [UserController::class, 'profile']);
});
```

## Database Design Patterns

### 1. Product Hierarchy
```
ProductCategory (parent-child relationship)
├── Medicine
├── Goods  
└── Service
```

### 2. Relationship Patterns
- **BelongsTo**: Product → Category, Manufacturer
- **HasMany**: Category → Products
- **Polymorphic**: (for future extensions)

## Code Organization

### 1. Directory Structure
```
app/
├── Http/Controllers/
│   ├── Admin/Product/ (Product management)
│   ├── User/ (User features)
│   └── Auth/ (Authentication)
├── Models/ (All Eloquent models)
└── Providers/ (Service providers)

resources/views/
├── admin/ (Admin panel)
├── user/ (User interface)
├── auth/ (Login/Register)
├── compoments/ (Reusable components)
└── layouts/ (Base layouts)
```

### 2. Naming Conventions
- **Controllers**: `{Feature}Controller.php`
- **Models**: Singular name (Medicine, Goods, Service)
- **Views**: kebab-case (danhsachhanghoa, create-medicine)
- **Routes**: dot notation (admin.products.index)

## Security Patterns
- **CSRF Protection** cho tất cả forms
- **Authentication Middleware** cho protected routes
- **Authorization** cho admin functions
- **Input Validation** với Form Requests
- **File Upload Security** với validation

## Performance Patterns
- **Eager Loading** với `with()` để tránh N+1 queries
- **Pagination** cho large datasets
- **Image Optimization** với proper storage
- **Caching** (sẽ implement trong tương lai)

## Error Handling
- **Try-Catch blocks** trong controllers
- **Validation errors** với proper feedback
- **404 handling** với `findOrFail()`
- **JSON responses** cho AJAX requests
