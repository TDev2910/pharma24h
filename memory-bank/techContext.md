# Technical Context - Suckhoe24h

## Technology Stack

### Backend
- **Framework**: Laravel (PHP)
- **Database**: MySQL
- **Authentication**: Laravel Auth (built-in)
- **File Storage**: Laravel Storage (local/public)

### Frontend
- **Templates**: Blade (Laravel's templating engine)
- **CSS Framework**: Bootstrap 5
- **JavaScript**: Vanilla JS + jQuery (for AJAX)
- **Icons**: Font Awesome
- **Build Tool**: Vite (Laravel default)

### Development Environment
- **Server**: XAMPP (Apache + MySQL + PHP)
- **PHP Version**: 8.x
- **Composer**: Dependency management
- **Artisan**: Laravel CLI tool

## Project Configuration

### Database Configuration
```php
// config/database.php
'default' => env('DB_CONNECTION', 'mysql'),
'mysql' => [
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'suckhoe24h'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
]
```

### File Storage
```php
// config/filesystems.php
'disks' => [
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
]
```

## Current Models & Relationships

### Core Models
1. **User** - Authentication & user management
2. **ProductCategory** - Hierarchical product categories
3. **Medicine** - Medicine products
4. **Goods** - General goods/products
5. **Service** - Services offered
6. **Manufacturer** - Product manufacturers
7. **Supplier** - Product suppliers
8. **Position** - Product positions/locations

### Key Relationships
```php
// Medicine relationships
Medicine::belongsTo(ProductCategory::class, 'nhom_hang_id')
Medicine::belongsTo(Manufacturer::class)
Medicine::belongsTo(DrugRoute::class, 'drugusage_id')
Medicine::belongsTo(Position::class)

// Goods relationships  
Goods::belongsTo(ProductCategory::class, 'nhom_hang_id')
Goods::belongsTo(Manufacturer::class)
Goods::belongsTo(Position::class)
```

## Current Controllers

### Admin Controllers
- `MedicineController` - CRUD operations for medicines
- `GoodsController` - CRUD operations for goods
- `ServiceController` - CRUD operations for services
- `ProductController` - General product management

### User Controllers
- `UserController` - User profile management
- `HomeController` - Public homepage

### Auth Controllers
- `LoginController` - User authentication
- `RegisterController` - User registration

## Routes Structure

### Admin Routes (`routes/admin.php`)
```php
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::resource('products', ProductController::class);
    Route::resource('medicines', MedicineController::class);
    Route::resource('goods', GoodsController::class);
    Route::resource('services', ServiceController::class);
});
```

### User Routes (`routes/user.php`)
```php
Route::prefix('user')->middleware('auth')->group(function () {
    Route::get('profile', [UserController::class, 'profile']);
    Route::get('settings', [UserController::class, 'settings']);
});
```

### Public Routes (`routes/web.php`)
```php
Route::get('/', [HomeController::class, 'index']);
Route::get('/search', [SearchController::class, 'index']);
```

## Frontend Assets

### CSS Files
- `public/css/admin.css` - Admin panel styles
- `public/css/home.css` - Public homepage styles
- `public/css/user.css` - User interface styles
- `public/css/forms.css` - Form styling
- `public/css/modals.css` - Modal components

### JavaScript Files
- `public/js/admin.js` - Admin functionality
- `public/js/user.js` - User functionality
- `public/js/forms.js` - Form handling
- `public/js/modals.js` - Modal interactions

## Development Setup

### Required Dependencies
```json
// composer.json
{
    "require": {
        "laravel/framework": "^10.0",
        "php": "^8.1"
    }
}
```

### Environment Variables
```env
APP_NAME=Suckhoe24h
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=suckhoe24h
DB_USERNAME=root
DB_PASSWORD=
```

## Current Features Status

### ✅ Implemented
- User authentication (login/register)
- Admin panel with product management
- CRUD operations for medicines, goods, services
- Product categories with hierarchy
- Search functionality
- File upload for product images
- Responsive design with Bootstrap

### 🔄 In Development
- Cart system for e-commerce functionality
- Real-time cart updates
- User shopping experience

### 📋 Planned
- Order management system
- Payment integration
- Inventory management
- Advanced search filters
- User reviews and ratings
