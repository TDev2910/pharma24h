<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modals.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
   
</head>
<body>
    <!-- Menu trên cùng --> 
    <nav class="navbar">
        <div class="nav-menu">
            <a href="{{ route('admin.dashboard') }}" class="nav-item active">Tổng Quan</a>
            <div class="nav-item dropdown">
                <span class="nav-item">Hàng hóa</span>
                <div class="nav-dropdown">
                    <div class="dropdown-col">
                        <div class="dropdown-title">Hàng hóa</div>
                        <a href="{{ route('admin.products.index') }}" class="dropdown-link">Danh sách hàng hóa</a>
                        <a href="{{ route('admin.medicines.list') }}" class="dropdown-link">Danh sách thuốc</a>
                        <a href="{{ route('admin.goods.inventory') }}" class="dropdown-link">Tồn kho hàng hóa</a>
                    </div>
                    <div class="dropdown-col">
                        <div class="dropdown-title">Kho hàng</div>
                        <a href="#" class="dropdown-link">Kiểm kho</a>
                        <a href="#" class="dropdown-link">Xuất hủy</a>
                    </div>
                    <div class="dropdown-col">
                        <div class="dropdown-title">Nhập hàng</div>
                        <a href="{{ route('admin.suppliers.index') }}" class="dropdown-link">Nhà cung cấp</a>
                        <a href="#" class="dropdown-link">Nhập hàng</a>
                        <a href="#" class="dropdown-link">Trả hàng nhập</a>
                    </div>
                </div>
            </div>
            <a href="#" class="nav-item">Đơn hàng</a>
            <a href="#" class="nav-item">Khách hàng</a>
            <a href="#" class="nav-item">Bác sĩ</a>
            <a href="#" class="nav-item">Nhân viên</a>
            <a href="#" class="nav-item">Sổ quỹ</a>
        </div>
        <button class="sell-btn">
            <i class="fas fa-cart-shopping cart-icon"></i> Bán hàng
        </button>
    </nav>
    <div>
        @yield('content')
    </div>
    <!-- Bootstrap JS (Load trước) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <!-- Custom JS (Load sau Bootstrap) -->
    <script src="{{ asset('js/modals.js') }}"></script>
    <script src="{{ asset('js/forms.js') }}"></script>
    @stack('scripts')
</body>
</html>