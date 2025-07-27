<header class="mediaid-header">
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
        <div class="container">
            <!-- Menu Toggle for Mobile -->
            <button class="navbar-toggler border-0 p-0 me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                <i class="fas fa-bars fs-5"></i>
            </button>

            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center" href="/">
                <div class="brand-container">
                    <span class="brand-text">PCT Pharma</span>
                </div>
            </a>

            <!-- Desktop Navigation -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="/">Trang chủ</a></li>
                    <li class="nav-item">
                            <a class="nav-link" href="{{ url('co-so-kham-benh') }}">Cơ sở khám bệnh</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="#products">Sản phẩm</a></li>
                    <li class="nav-item"><a class="nav-link" href="#team">Đội ngũ</a></li>
                    <li class="nav-item"><a class="nav-link" href="#certificates">Hình ảnh chúng tôi</a></li>
                </ul>

                <!-- Search Bar -->
                <form class="search-form me-3" method="GET" action="/search">
                    <div class="search-container">
                        <i class="fas fa-search search-icon"></i>
                        <input type="search" 
                            name="q" 
                            class="form-control search-input" 
                            placeholder="Tìm kiếm sản phẩm"
                            aria-label="Search">
                    </div>
                </form>

                <!-- User Actions -->
                <div class="user-actions d-flex align-items-center">
                    @auth
                        <!-- Dropdown User Icon -->
                        <div class="dropdown">
                            <a href="#" class="action-link me-3 dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li class="px-3 py-2 border-bottom">
                                    <div class="fw-bold">{{ Auth::user()->name }}</div>
                                    <div class="text-muted" style="font-size: 13px;">{{ Auth::user()->email }}</div>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/profile">
                                        <i class="fas fa-cog me-2"></i>Account Settings
                                    </a>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i>Log out
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <!-- Nếu chưa đăng nhập -->
                        <a href="{{ route('login') }}" class="action-link me-3">
                            <i class="fas fa-user"></i>
                        </a>
                    @endauth

                    <!-- Shopping Cart chưa làm-->
                    <a href="#" class="action-link position-relative">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-badge">0</span>
                    </a>
                </div>
            </div>

            <!-- Mobile User Actions -->
            <div class="d-lg-none d-flex align-items-center">
                <a href="#" class="action-link me-3">
                    <i class="fas fa-user"></i>
                </a>
                <a href="#" class="action-link position-relative">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge">0</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu Offcanvas -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">
                <i class="fas fa-plus-circle text-primary me-2"></i>
                MediAid
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="/">Trang chủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/facilities">Cơ sở khám bệnh</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="products">Sản phẩm</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="team">Đội ngũ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="certificates">Hình ảnh chứng tỏi</a>
                </li>
            </ul>
            
            <!-- Mobile Search -->
            <div class="mt-3">
                <form class="search-form">
                    <div class="search-container">
                        <i class="fas fa-search search-icon"></i>
                        <input type="search" 
                            name="q" 
                            class="form-control search-input" 
                            placeholder="Tìm kiếm sản phẩm">
                    </div>
                </form>
            </div>
        </div>
    </div> 
</header>