suckhoe24h/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   └── AuthController.php
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php       // Tổng quan, thống kê
│   │   │   │   ├── ProductController.php         // Quản lý hàng hóa
│   │   │   │   ├── OrderController.php           // Quản lý đơn hàng
│   │   │   │   ├── CustomerController.php        // Quản lý khách hàng
│   │   │   │   ├── ReportController.php          // Báo cáo, sổ quỹ
│   │   │   │   └── StaffController.php           // Quản lý nhân viên
│   │   │   ├── User/
│   │   │   │   ├── HomeController.php            // Trang chủ MediAid
│   │   │   │   ├── ProductController.php         // Xem sản phẩm
│   │   │   │   ├── CartController.php            // Giỏ hàng online
│   │   │   │   └── OrderController.php           // Đặt hàng online
│   │   │   └── POS/
│   │   │       ├── SaleController.php            // Bán hàng trực tiếp
│   │   │       ├── CustomerController.php        // Thông tin khách hàng
│   │   │       ├── ProductController.php         // Tìm kiếm sản phẩm
│   │   │       └── ReceiptController.php         // Xuất hóa đơn/bill
│   │   ├── Middleware/
│   │   │   ├── AdminMiddleware.php               // Role admin
│   │   │   ├── POSMiddleware.php                 // Role thu ngân
│   │   │   └── CustomerMiddleware.php            // Role khách hàng
│   └── Models/
│       ├── User.php                              // Admin, Staff, Customer
│       ├── Product.php                           // Thuốc, vật tư y tế
│       ├── Category.php                          // Phân loại thuốc
│       ├── Sale.php                              // Bán hàng trực tiếp
│       ├── SaleItem.php                          // Chi tiết bán hàng
│       ├── Order.php                             // Đơn hàng online
│       ├── Customer.php                          // Khách hàng
│       └── Receipt.php                           // Hóa đơn
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── admin.blade.php                   // Layout admin (như KiotViet)
│   │   │   ├── user.blade.php                    // Layout user (như MediAid)
│   │   │   └── pos.blade.php                     // Layout POS (màn hình bán hàng)
│   │   ├── admin/                                // Giao diện quản lý
│   │   │   ├── dashboard.blade.php
│   │   │   ├── products/
│   │   │   ├── orders/
│   │   │   └── reports/
│   │   ├── user/                                 // Website khách hàng
│   │   │   ├── home.blade.php
│   │   │   ├── products/
│   │   │   └── about.blade.php
│   │   └── pos/                                  // Giao diện thu ngân
│   │       ├── sale.blade.php                    // Màn hình bán hàng
│   │       ├── customer-info.blade.php           // Nhập thông tin KH
│   │       └── receipt.blade.php                 // In hóa đơn
│   ├── css/
│   │   ├── admin.css                             // Style cho admin
│   │   ├── user.css                              // Style cho website
│   │   └── pos.css                               // Style cho POS
│   └── js/
│       ├── admin.js
│       ├── user.js
│       └── pos.js                                // Tính tiền, quét mã
└── routes/
    ├── web.php                                   // Routes chung
    ├── admin.php                                 // Routes admin
    └── pos.php                                   // Routes POS