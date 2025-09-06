# Project Brief - Suckhoe24h

## Project Overview
**Suckhoe24h** là một hệ thống quản lý dược phẩm và sản phẩm y tế được xây dựng bằng Laravel. Dự án tập trung vào việc quản lý thuốc, hàng hóa y tế và dịch vụ khám chữa bệnh.

## Core Requirements
1. **Quản lý sản phẩm**: Thuốc (Medicine), Hàng hóa (Goods), Dịch vụ (Service)
2. **Quản lý danh mục**: Phân loại sản phẩm theo cấp bậc
3. **Quản lý nhà cung cấp**: Supplier và SupplierCategory
4. **Hệ thống người dùng**: Admin và User với phân quyền
5. **Giỏ hàng**: Chức năng mua sắm cho người dùng cuối

## Current Status
- ✅ Models cơ bản đã được tạo (Medicine, Goods, Service, User, etc.)
- ✅ Admin panel cho quản lý sản phẩm
- ✅ Authentication system
- ✅ Product management (CRUD operations)
- 🔄 **Đang phát triển**: Chức năng giỏ hàng (Cart functionality)

## Technology Stack
- **Backend**: Laravel (PHP)
- **Frontend**: Blade templates, Bootstrap, JavaScript
- **Database**: MySQL
- **Authentication**: Laravel Auth

## Key Features
1. **Product Management**: CRUD operations cho thuốc, hàng hóa, dịch vụ
2. **Category Management**: Hệ thống danh mục phân cấp
3. **User Management**: Phân quyền admin/user
4. **Search & Filter**: Tìm kiếm và lọc sản phẩm
5. **Cart System**: Giỏ hàng cho người dùng (đang phát triển)

## Project Structure
```
app/
├── Http/Controllers/
│   ├── Admin/Product/ (MedicineController, GoodsController, etc.)
│   └── Auth/
├── Models/ (Medicine, Goods, Service, User, etc.)
└── Providers/

resources/views/
├── admin/ (Admin panel views)
├── auth/ (Login/Register views)
├── compoments/ (Header, Footer components)
└── home.blade.php (Public homepage)

routes/
├── admin.php (Admin routes)
├── user.php (User routes)
└── web.php (Public routes)
```
