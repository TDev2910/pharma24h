# Technical Context - SUCKHOE24H

## Technology Stack

### Backend Framework
- **Laravel 11.x** - PHP web application framework
- **PHP 8.x** - Server-side scripting language
- **Composer** - Dependency management

### Database
- **MySQL** - Primary database
- **Eloquent ORM** - Database abstraction layer
- **Migrations** - Database version control

### Frontend
- **Blade Templates** - Laravel's templating engine
- **Bootstrap 5** - CSS framework
- **JavaScript (Vanilla)** - Client-side scripting
- **Font Awesome** - Icon library

### Development Tools
- **Artisan** - Laravel command-line interface
- **Vite** - Frontend build tool
- **NPM** - Frontend package management

## Project Structure

```
suckhoe24h/
├── app/                           # Core application logic
│   ├── Console/
│   │   └── Commands/
│   │       └── CreateAdminUser.php    # Custom Artisan commands
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/                 # Admin-specific controllers
│   │   │   │   ├── AdminController.php
│   │   │   │   ├── ProductCategoryController.php
│   │   │   │   └── ProductController.php
│   │   │   ├── Auth/                  # Authentication controllers
│   │   │   │   └── AuthController.php
│   │   │   ├── Controller.php         # Base controller
│   │   │   ├── POS/                   # Point of Sale (future)
│   │   │   └── User/                  # User-facing controllers
│   │   └── Middleware/
│   │       └── AdminMiddleware.php    # Admin access protection
│   ├── Models/                        # Eloquent models
│   │   ├── DrugRoute.php
│   │   ├── Manufacturer.php
│   │   ├── Medicine.php
│   │   ├── Position.php
│   │   ├── ProductCategory.php
│   │   └── User.php
│   └── Providers/
│       └── AppServiceProvider.php     # Service container bindings
├── bootstrap/                         # Application bootstrap
│   ├── app.php
│   ├── cache/
│   └── providers.php
├── config/                           # Configuration files
│   ├── app.php                       # Application config
│   ├── auth.php                      # Authentication config
│   ├── database.php                  # Database connections
│   ├── filesystems.php               # File storage config
│   └── [other config files]
├── database/                         # Database related files
│   ├── factories/
│   │   └── UserFactory.php          # Model factories for testing
│   ├── migrations/                   # Database migrations
│   │   ├── 2025_07_22_081959_create_users_table.php
│   │   ├── 2025_07_25_151953_create_product_categories_table.php
│   │   ├── 2025_07_28_022605_create_positions_table.php
│   │   ├── 2025_07_28_023921_create_manufacturers_table.php
│   │   ├── 2025_07_29_142427_create_drug_routes_table.php
│   │   ├── 2025_07_30_173046_create_medicines_table.php
│   │   └── 2025_08_01_162756_rename_manufacturer_column_in_medicines_table.php
│   └── seeders/                      # Database seeders
├── memory-bank/                      # Project documentation
│   ├── productContext.md
│   ├── systemPatterns.md
│   ├── projectbrief.md
│   ├── techContext.md
│   └── [other documentation]
├── public/                           # Publicly accessible files
│   ├── css/                          # Compiled stylesheets
│   │   ├── admin.css
│   │   ├── app.css
│   │   ├── pos.css
│   │   ├── reponsive.css
│   │   └── user.css
│   ├── js/                           # Compiled JavaScript
│   │   ├── admin.js
│   │   ├── app.js
│   │   ├── pos.js
│   │   └── user.js
│   ├── auth/                         # Authentication specific assets
│   │   ├── common.css
│   │   ├── login.css
│   │   └── register.css
│   ├── images/                       # Static images
│   ├── index.php                     # Application entry point
│   └── favicon.ico
├── resources/                        # Raw application resources
│   ├── css/
│   │   └── app.css                   # Source CSS
│   ├── js/
│   │   ├── app.js                    # Source JavaScript
│   │   └── bootstrap.js              # Bootstrap configuration
│   └── views/                        # Blade templates
│       ├── admin/                    # Admin interface views
│       │   ├── admindashboard.blade.php
│       │   ├── categories/           # Category management
│       │   │   ├── edit.blade.php
│       │   │   └── index.blade.php
│       │   └── products/             # Product management
│       │       ├── Danhsachhanghoa/  # Medicine listing
│       │       │   ├── create_combo.blade.php
│       │       │   ├── create_goods.blade.php
│       │       │   ├── create_service.blade.php
│       │       │   ├── createmedicine.blade.php
│       │       │   ├── editmedicine.blade.php
│       │       │   ├── index.blade.php
│       │       │   └── formmodal/
│       │       │       └── unit_modal.blade.php
│       │       └── Danhsachthuoc/    # Medicine catalog
│       │           └── Listmedicine.blade.php
│       ├── auth/                     # Authentication views
│       │   ├── login.blade.php
│       │   └── register.blade.php
│       ├── components/               # Reusable components
│       │   ├── footer.blade.php
│       │   ├── header.blade.php
│       │   └── side-bar.blade.php
│       ├── layouts/                  # Layout templates
│       │   ├── admin.blade.php       # Admin layout
│       │   ├── app.blade.php         # Main app layout
│       │   └── user.blade.php        # User layout
│       ├── pos/                      # Point of Sale views (future)
│       ├── user/                     # User-facing views
│       │   ├── cosokhambenh.blade.php
│       │   └── home.blade.php
│       └── welcome.blade.php         # Default welcome page
├── routes/                           # Route definitions
│   ├── admin.php                     # Admin routes
│   ├── pos.php                       # POS routes (future)
│   └── web.php                       # Web routes
├── storage/                          # Application storage
│   ├── app/                          # Application files
│   ├── framework/                    # Framework cache/sessions
│   │   ├── cache/
│   │   ├── sessions/
│   │   ├── testing/
│   │   └── views/
│   └── logs/                         # Application logs
├── tests/                            # Test files
│   ├── Feature/
│   │   └── ExampleTest.php
│   ├── Unit/
│   │   └── ExampleTest.php
│   └── TestCase.php
├── document/                         # Project documentation
│   ├── folder.md
│   └── test.md
├── composer.json                     # PHP dependencies
├── composer.lock                     # Locked PHP dependencies
├── package.json                      # Node.js dependencies  
├── package-lock.json                 # Locked Node dependencies
├── vite.config.js                    # Vite configuration
├── phpunit.xml                       # PHPUnit configuration
├── artisan                           # Laravel command line tool
└── README.md                         # Project readme
```

## Database Schema

### Core Entities
1. **users** - System users (admin, regular users)
2. **product_categories** - Medicine categorization
3. **manufacturers** - Medicine manufacturers
4. **drug_routes** - Drug administration routes (oral, injection, etc.)
5. **positions** - Storage positions/locations
6. **medicines** - Main medicine data

### Key Relationships
- `medicines.category_id` → `product_categories.id`
- `medicines.manufacturer_id` → `manufacturers.id`
- `medicines.drug_route_id` → `drug_routes.id` (was `drugusage_id`)
- `medicines.position_id` → `positions.id`

## Development Environment

### Requirements
- **PHP 8.1+**
- **Composer 2.x**
- **Node.js 18+**
- **MySQL 8.0+**
- **Web Server** (Apache/Nginx)

### Setup Commands
```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Asset compilation
npm run dev
# or for production
npm run build

# Start development server
php artisan serve
```

## Current Technical Challenges

### Identified Issues
1. **Naming Inconsistency**: `drugusage_id` vs `drug_route_id`
2. **Large Blade Files**: Some templates exceed 1000 lines
3. **Inline JavaScript**: Mixed with Blade templates
4. **Missing Service Layer**: Business logic in controllers
5. **Limited Testing**: Minimal test coverage

### Architecture Improvements Needed
1. **Service Layer Implementation**
2. **Repository Pattern** for data access
3. **Frontend Asset Organization**
4. **Component-based Blade Templates**
5. **Comprehensive Testing Strategy**

## Performance Considerations

### Current State
- **Database Queries**: Some N+1 query issues
- **Asset Loading**: Unoptimized CSS/JS
- **Caching**: Minimal caching implementation
- **Image Handling**: Basic file upload system

### Optimization Opportunities
- **Query Optimization**: Eager loading improvements
- **Asset Bundling**: Vite optimization
- **Redis Caching**: Session và query caching
- **CDN Integration**: Static asset delivery
- **Database Indexing**: Strategic index creation