<template>
  <header class="mediaid-header">
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
      <div class="container">
        <button class="navbar-toggler border-0 p-0 me-3" type="button" data-bs-toggle="offcanvas"
          data-bs-target="#mobileMenu">
          <i class="fas fa-bars fs-5"></i>
        </button>

        <a class="navbar-brand d-flex align-items-center" href="/" data-inertia>
          <div class="brand-container">
            <span class="brand-text">PCT Pharma</span>
          </div>
        </a>

        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <a class="nav-link" href="/" data-inertia>Trang chủ</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/co-so-kham-benh" data-inertia>Đội ngũ</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/products" data-inertia>Sản phẩm</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/services" data-inertia>Dịch vụ</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/contact" data-inertia>Liên hệ</a>
            </li>
          </ul>

          <div class="user-actions d-flex align-items-center">
            <template v-if="auth.user">
              <div class="dropdown">
                <a href="#" class="action-link me-3 dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  <i class="fas fa-user"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                  <li class="px-3 py-2 border-bottom">
                    <div class="fw-bold">{{ auth.user.name }}</div>
                    <div class="text-muted" style="font-size: 13px;">{{ auth.user.email }}</div>
                  </li>
                  <li>
                    <a class="dropdown-item" href="/user/dashboard">
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
              <a href="/login" class="action-link me-3">
                <i class="fas fa-user"></i>
              </a>
            </template>

            <div class="dropdown cart-dropdown">
              <button type="button" class="action-link position-relative dropdown-toggle border-0 bg-transparent"
                id="cartDropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                  style="display: none;">0</span>
              </button>
              <div class="dropdown-menu dropdown-menu-end cart-dropdown-menu" aria-labelledby="cartDropdown"
                style="width: 360px;">
                <div class="cart-loading text-center p-3 d-none">
                  <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                  <span class="ms-2">Đang tải giỏ hàng...</span>
                </div>
                <div class="cart-items p-2" style="max-height: 400px; overflow-y: auto;">
                  <div class="empty-cart text-center py-3">
                    <p class="mb-0">Giỏ hàng trống</p>
                  </div>
                </div>
                <div class="cart-footer p-3 border-top">
                  <div class="cart-total d-flex justify-content-between mb-3">
                    <strong>Tổng cộng:</strong>
                    <span class="cart-total-amount text-danger fw-bold" aria-live="polite"
                      aria-label="Tổng tiền giỏ hàng">0
                      VNĐ</span>
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

        <div class="d-lg-none d-flex align-items-center">

          <template v-if="auth.user">
            <div class="dropdown me-3">
              <a href="#" class="action-link dropdown-toggle" id="mobileUserDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user"></i>
              </a>
              <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="mobileUserDropdown"
                  style="max-height: 300px; overflow-y: auto; min-width: 250px;">
                <li class="px-3 py-2 border-bottom bg-light">
                  <div class="fw-bold text-truncate" style="max-width: 200px;">{{ auth.user.name }}</div>
                  <div class="text-muted small text-truncate" style="max-width: 200px;">{{ auth.user.email }}</div>
                </li>
                <li>
                  <a class="dropdown-item py-2" href="/user/dashboard">
                    <i class="fas fa-cog me-2 text-secondary"></i>Cài đặt tài khoản
                  </a>
                </li>
                <li><hr class="dropdown-divider m-0"></li>
                <li>
                  <form method="POST" action="/logout">
                    <input type="hidden" name="_token" :value="csrfToken">
                    <button type="submit" class="dropdown-item py-2 text-danger">
                      <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                    </button>
                  </form>
                </li>
              </ul>
            </div>
          </template>
          <template v-else>
            <a href="/login" class="action-link me-3">
              <i class="fas fa-user"></i>
            </a>
          </template>

          <div class="dropdown cart-dropdown">
            <button type="button" class="action-link position-relative border-0 bg-transparent" id="mobilecartDropdown"
              data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
              <i class="fas fa-shopping-cart"></i>
              <span class="cart-count position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                style="display: none;">0</span>
            </button>

            <div class="dropdown-menu dropdown-menu-end cart-dropdown-menu shadow" aria-labelledby="mobilecartDropdown"
              style="min-width: 300px; max-width: 90vw;">

              <div class="cart-loading text-center p-3 d-none">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <span class="ms-2">Đang tải...</span>
              </div>

              <div class="cart-items p-2" style="max-height: 300px; overflow-y: auto;">
                <div class="empty-cart text-center py-3">
                  <p class="mb-0">Giỏ hàng trống</p>
                </div>
              </div>

              <div class="cart-footer p-3 border-top">
                <div class="cart-total d-flex justify-content-between mb-3">
                  <strong>Tổng cộng:</strong>
                  <span class="cart-total-amount text-danger fw-bold" aria-live="polite" aria-label="Tổng tiền giỏ hàng">0
                    VNĐ</span>
                </div>
                <div class="cart-actions d-flex gap-2">
                  <a href="/cart" class="btn btn-outline-primary btn-sm flex-grow-1">Xem giỏ</a>
                  <a href="/checkout" class="btn btn-primary btn-sm flex-grow-1">Thanh toán</a>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </nav>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title">
          <i class="fas fa-plus-circle text-primary me-2"></i>
          PCT Pharma
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
      </div>
      <div class="offcanvas-body">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="/" data-inertia>Trang chủ</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/co-so-kham-benh" data-inertia>Đội ngũ</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/products" data-inertia>Sản phẩm</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/services" data-inertia>Dịch vụ</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/contact" data-inertia>Liên hệ</a>
          </li>
        </ul>
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
<style scoped>
/* --- STYLES CỐ ĐỊNH CHO DESKTOP & CHUNG --- */
.mediaid-header {
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.brand-text {
  font-weight: 700; font-size: 1.5rem; color: #1a56db;
}

/* Các button trong Cart actions */
.cart-actions .btn-primary:hover,
.cart-actions .btn-primary:focus,
.cart-actions .btn-primary:active,
.cart-actions .btn-outline-primary:hover,
.cart-actions .btn-outline-primary:focus,
.cart-actions .btn-outline-primary:active {
  background-color: #1a56db !important;
  border-color: #1a56db !important;
  color: #fff !important;
}

.cart-actions .btn-outline-primary {
  border-color: #1a56db !important; color: #1a56db !important; background-color: transparent !important;
}

.cart-actions .btn-primary {
  background-color: #1a56db !important; border-color: #1a56db !important; color: #fff !important;
}

/* --- MOBILE RESPONSIVE STYLES (Dưới 992px) --- */
@media (max-width: 991.98px) {
    /* 1. Xử lý Dropdown Giỏ hàng trên Mobile */
    .dropdown-menu.cart-dropdown-menu {
        position: fixed !important; /* Đổi thành fixed để tránh overflow */
        top: auto !important;
        right: 10px !important; /* Cách lề phải 10px */
        left: 10px !important; /* Cách lề trái 10px */
        bottom: auto !important;
        margin-top: 10px;

        /* Kích thước */
        width: auto !important; /* Tự động điều chỉnh */
        max-width: calc(100vw - 20px) !important; /* Trừ padding 2 bên */

        /* Visual */
        box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
        border: none;
        border-radius: 12px;
        z-index: 1050;
    }

    /* Điều chỉnh position cho dropdown parent */
    .d-lg-none .dropdown.cart-dropdown {
        position: static !important;
    }

    /* 2. Xử lý User Dropdown trên Mobile */
    .d-lg-none .dropdown-menu {
        position: fixed !important;
        right: 10px !important;
        left: auto !important;
        top: auto !important;
        min-width: 250px;
        max-width: calc(100vw - 20px);
    }

    /* 3. Tinh chỉnh Offcanvas menu */
    .offcanvas-body .nav-link {
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
        font-size: 16px;
    }
}

/* Mobile nhỏ (Dưới 576px) */
@media (max-width: 575.98px) {
    .dropdown-menu.cart-dropdown-menu {
        right: 5px !important;
        left: 5px !important;
        max-width: calc(100vw - 10px) !important;
    }

    .d-lg-none .dropdown-menu {
        right: 5px !important;
        max-width: calc(100vw - 10px);
        min-width: 240px;
    }
}
</style>
