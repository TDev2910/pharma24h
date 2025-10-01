<template>
  <header class="mediaid-header">
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
      <div class="container">
        <!-- Menu Toggle for Mobile -->
        <button class="navbar-toggler border-0 p-0 me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
          <i class="fas fa-bars fs-5"></i>
        </button>

        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="/" data-inertia>
          <div class="brand-container">
            <span class="brand-text">PCT Pharma</span>
          </div>
        </a>

        <!-- Desktop Navigation -->
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <a class="nav-link" href="/" data-inertia>Trang chủ</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/co-so-kham-benh" data-inertia>Cơ sở khám bệnh</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/products" data-inertia>Sản phẩm</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/services" data-inertia>Dịch vụ</a>
            </li>
          </ul>

          <!-- Search Bar -->
          <!-- <form class="search-form me-3" method="GET" action="/search">
            <div class="search-container">
              <i class="fas fa-search search-icon"></i>
              <input type="search" 
                name="q" 
                class="form-control search-input" 
                placeholder="Tìm kiếm sản phẩm"
                aria-label="Search">
            </div>
          </form> -->

          <!-- User Actions -->
          <div class="user-actions d-flex align-items-center">
            <template v-if="auth.user">
              <!-- Dropdown User Icon -->
              <div class="dropdown">
                <a href="#" class="action-link me-3 dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="fas fa-user"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                  <li class="px-3 py-2 border-bottom">
                    <div class="fw-bold">{{ auth.user.name }}</div>
                    <div class="text-muted" style="font-size: 13px;">{{ auth.user.email }}</div>
                  </li>
                  <li>
                    <a class="dropdown-item" href="/user/profile-settings">
                      <i class="fas fa-cog me-2"></i>Account Settings
                    </a>
                  </li>
                  <li>
                    <form method="POST" action="/logout">
                      <input type="hidden" name="_token" :value="csrfToken">
                      <button type="submit" class="dropdown-item text-danger">
                        <i class="fas fa-sign-out-alt me-2"></i>Log out
                      </button>
                    </form>
                  </li>
                </ul>
              </div>
            </template>
            <template v-else>
              <!-- Nếu chưa đăng nhập -->
              <a href="/login" class="action-link me-3">
                <i class="fas fa-user"></i> 
              </a>
            </template>

            <!-- Shopping Cart -->
            <div class="dropdown cart-dropdown">
              <button type="button" class="action-link position-relative dropdown-toggle border-0 bg-transparent" 
                      id="cartDropdown" 
                      data-bs-toggle="dropdown" 
                      data-bs-auto-close="outside"
                      aria-expanded="false">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                     style="display: none;">0</span>
              </button>
              <div class="dropdown-menu dropdown-menu-end cart-dropdown-menu" 
                   aria-labelledby="cartDropdown" 
                   style="width: 360px;">
                <div class="cart-loading text-center p-3 d-none">
                  <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                  <span class="ms-2">Đang tải giỏ hàng...</span>
                </div>
                <div class="cart-items p-2">
                  <!-- Cart items will be loaded here via JavaScript -->
                  <div class="empty-cart text-center py-3">
                    <p class="mb-0">Giỏ hàng trống</p>
                  </div>
                </div>
                <div class="cart-footer p-3 border-top">
                  <div class="cart-total d-flex justify-content-between mb-3">
                    <strong>Tổng cộng:</strong>
                    <span class="cart-total-amount text-danger fw-bold" 
                          aria-live="polite" 
                          aria-label="Tổng tiền giỏ hàng">0 VNĐ</span>
                  </div>
                  <div class="cart-actions d-flex">
                    <a href="/cart" class="btn btn-outline-primary btn-sm me-2 flex-grow-1">Xem giỏ hàng</a>
                    <a href="/checkout" class="btn btn-primary btn-sm flex-grow-1">Thanh toán</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Mobile User Actions -->
        <div class="d-lg-none d-flex align-items-center">
          <a href="#" class="action-link me-3">   
            <i class="fas fa-user"></i>
          </a>
          <button type="button" class="action-link position-relative border-0 bg-transparent" 
                  id="mobilecartDropdown"
                  data-bs-toggle="dropdown" 
                  data-bs-auto-close="outside"
                  aria-expanded="false">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-count position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                 style="display: none;">0</span>
          </button>
          <div class="dropdown-menu dropdown-menu-end cart-dropdown-menu" 
               aria-labelledby="mobilecartDropdown" 
               style="width: 360px;">
            <div class="cart-loading text-center p-3 d-none">
              <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <span class="ms-2">Đang tải giỏ hàng...</span>
            </div>
            <div class="cart-items p-2">
              <!-- Cart items will be loaded here via JavaScript -->
              <div class="empty-cart text-center py-3">
                <p class="mb-0">Giỏ hàng trống</p>
              </div>
            </div>
            <div class="cart-footer p-3 border-top">
              <div class="cart-total d-flex justify-content-between mb-3">
                <strong>Tổng cộng:</strong>
                <span class="cart-total-amount text-danger fw-bold" 
                      aria-live="polite" 
                      aria-label="Tổng tiền giỏ hàng">0 VNĐ</span>
              </div>
              <div class="cart-actions d-flex">
                <a href="/cart" class="btn btn-outline-primary btn-sm me-2 flex-grow-1">Xem giỏ hàng</a>
                <a href="/checkout" class="btn btn-primary btn-sm flex-grow-1">Thanh toán</a>
              </div>
            </div>
          </div>
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
            <a class="nav-link" href="/" data-inertia>Trang chủ</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/co-so-kham-benh" data-inertia>Cơ sở khám bệnh</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="products">Sản phẩm</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="team">Dịch vụ</a>
          </li>
        </ul>
        
        <!-- Thanh tìm kiếm sản phẩm -->
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
</template>

<script setup>
import { computed, onMounted, onUnmounted } from 'vue'

// Props từ Inertia
const props = defineProps({
  auth: {
    type: Object,
    default: () => ({ user: null })
  }
})

// CSRF Token từ meta tag
const csrfToken = computed(() => {
  const meta = document.querySelector('meta[name="csrf-token"]')
  return meta ? meta.getAttribute('content') : ''
})

// Listen for cart updates
function handleCartUpdate() {
  // Call the correct function from cart.js
  if (typeof window.updateCartCount === 'function') {
    window.updateCartCount()
  }
}

onMounted(() => {
  window.addEventListener('cart-updated', handleCartUpdate)
  
  // Debug auth data
  console.log('Header mounted, auth data:', props.auth)
  console.log('User data:', props.auth?.user)
  console.log('Is user logged in?', !!props.auth?.user)
  
  // Initialize Bootstrap dropdowns
  const dropdownElementList = document.querySelectorAll('.dropdown-toggle')
  const dropdownList = [...dropdownElementList].map(dropdownToggleEl => new bootstrap.Dropdown(dropdownToggleEl))
})

onUnmounted(() => {
  window.removeEventListener('cart-updated', handleCartUpdate)
})
</script>

