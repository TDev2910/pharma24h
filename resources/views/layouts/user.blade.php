<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Dashboard') - Sức Khỏe 24h</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">              
    <!-- User Dashboard CSS -->
    <link rel="stylesheet" href="{{ asset('css/user.css') }}">
    
    @stack('styles')
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h3><i class="fas fa-heartbeat me-2"></i>Sức Khỏe 24h</h3>
            </div>
            
            <div class="user-info">
                <div class="user-avatar">
                    @if(Auth::user()->avatar ?? false)
                        <img src="{{ asset('storage/avatars/' . Auth::user()->avatar) }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">
                    @else
                        <i class="fas fa-user"></i>
                    @endif
                </div>
                <div class="user-details">
                    <h5>{{ Auth::user()->name ?? 'User' }}</h5>
                    <small>{{ Auth::user()->email ?? 'user@example.com' }}</small>
                </div>
            </div>

            <ul class="sidebar-menu">
                <!-- Dashboard Overview -->
                <li><a href="{{ route('user.dashboard') }}" class="{{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>Dashboard
                </a></li>
                
                <!-- Account Settings -->
                <li><a href="{{ route('user.profile.settings') }}" class="{{ request()->routeIs('user.profile.settings') ? 'active' : '' }}">
                    <i class="fas fa-user-cog"></i>Account Settings
                </a></li>
                
                <!-- Đơn hàng của tôi -->
                <li><a href="{{ route('user.orders') }}" class="{{ request()->routeIs('user.orders') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list"></i>Đơn hàng
                </a></li>
                
                <!-- Hồ sơ sức khỏe -->
                <li><a href="{{ route('user.health.profile') }}" class="{{ request()->routeIs('user.health.profile') ? 'active' : '' }}">
                    <i class="fas fa-file-medical"></i>Hồ sơ sức khỏe
                </a></li>
                
                <!-- Thông báo -->
                <li><a href="{{ route('user.notifications') }}" class="{{ request()->routeIs('user.notifications') ? 'active' : '' }}">
                    <i class="fas fa-bell"></i>Thông báo
                </a></li>
                
                <!-- Divider -->
                <li style="margin: 20px 0; border-top: 1px solid #e2e8f0;"></li>
                
                <!-- Trang chủ -->
                <li><a href="{{ route('home') }}">
                    <i class="fas fa-home"></i>Trang chủ
                </a></li>
                
                <!-- Đăng xuất -->
                <li><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>Đăng xuất
                </a></li>
            </ul>
            
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Page Header -->
            @if(View::hasSection('page-title'))
                <div class="page-header">
                    <h1>@yield('page-title', 'Dashboard')</h1>
                    <p>@yield('page-description', 'Manage your account')</p>
                </div>
            @endif

            <!-- Page Content -->
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // ===== SIDEBAR NAVIGATION MANAGEMENT =====
        document.addEventListener('DOMContentLoaded', function() {
            setActiveNavigation();
        });

        function setActiveNavigation() {
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.sidebar-menu a');
            
            // Remove all active classes
            navLinks.forEach(link => link.classList.remove('active'));
            
            // Set active based on current path
            navLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (href && (currentPath === href || currentPath.includes(href))) {
                    link.classList.add('active');
                }
            });
        }
    </script>
    
    @stack('scripts')
</body>
</html>