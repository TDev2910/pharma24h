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
            <li class="nav-item"><a class="nav-link" href="/" data-inertia>Trang chủ</a></li>
            <li class="nav-item"><a class="nav-link" href="/medical-team" data-inertia>Đội ngũ</a></li>
            <li class="nav-item"><a class="nav-link" href="/products" data-inertia>Sản phẩm</a></li>
            <li class="nav-item"><a class="nav-link" href="/services" data-inertia>Dịch vụ</a></li>
            <li class="nav-item"><a class="nav-link" href="/posts" data-inertia>Góc sức khỏe</a></li>
            <li class="nav-item"><a class="nav-link" href="/contact" data-inertia>Liên hệ</a></li>
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
                  </li>
                  <li><a class="dropdown-item" href="/user/dashboard"><i class="fas fa-cog me-2"></i>Cài đặt tài
                      khoản</a></li>
                  <li>
                    <form method="POST" action="/logout">
                      <input type="hidden" name="_token" :value="csrfToken">
                      <button type="submit" class="dropdown-item text-danger"><i
                          class="fas fa-sign-out-alt me-2"></i>Đăng xuất</button>
                    </form>
                  </li>
                </ul>
              </div>
            </template>
            <template v-else>
              <a href="/login" class="action-link me-3"><i class="fas fa-user"></i></a>
            </template>

            <div class="dropdown cart-dropdown">
              <button type="button" class="action-link position-relative border-0 bg-transparent" id="cartDropdown"
                @click.stop="toggleCart">
                <i class="fas fa-shopping-cart"></i>
                <span v-if="cartCount > 0"
                  class="cart-count position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                  {{ cartCount }}
                </span>
              </button>

              <div class="dropdown-menu dropdown-menu-end cart-dropdown-menu" :class="{ 'show': isCartOpen }"
                style="width: 360px;">

                <div v-if="loading" class="cart-loading">
                  <div class="spinner-border spinner-border-sm text-primary"></div>
                  <span>Đang tải...</span>
                </div>

                <div v-else-if="cartItems.length === 0" class="empty-cart-state">
                  <div class="icon-box mb-2">
                    <i class="fas fa-shopping-basket"></i>
                  </div>
                  <p class="text-muted">Giỏ hàng của bạn đang trống</p>
                  <a href="/products" class="btn btn-sm btn-primary mt-2">Mua sắm ngay</a>
                </div>

                <div v-else>
                  <div class="cart-scroll-area">
                    <div v-for="item in cartItems" :key="item.id" class="cart-item-row">
                      <div class="item-thumb">
                        <img :src="getImageUrl(item)" alt="Product">
                      </div>

                      <div class="item-info">
                        <h6 class="item-name text-truncate">{{ item.name }}</h6>
                        <div class="item-meta">
                          <span class="item-price">{{ formatPrice(item.price) }} đ</span>
                        </div>

                        <div class="qty-control">
                          <button class="qty-btn" @click.stop="updateQuantity(item.id, item.quantity - 1)">-</button>
                          <span class="qty-value">{{ item.quantity }}</span>
                          <button class="qty-btn" @click.stop="updateQuantity(item.id, item.quantity + 1)">+</button>
                        </div>
                      </div>

                      <button class="btn-remove" @click.stop="removeItem(item.id)" title="Xóa">
                        <i class="fas fa-times"></i>
                      </button>
                    </div>
                  </div>

                  <div class="cart-footer">
                    <div class="total-row">
                      <span class="label">Tổng cộng:</span>
                      <span class="value">{{ formatPrice(cartTotal) }} VNĐ</span>
                    </div>
                    <div class="action-row">
                      <a href="/cart" class="btn btn-outline-custom flex-grow-1">Xem giỏ</a>
                      <a href="/checkout" class="btn btn-primary-custom flex-grow-1">Thanh toán</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="d-lg-none d-flex align-items-center">
          <template v-if="auth.user">
            <div class="dropdown me-3">
              <a href="#" class="action-link dropdown-toggle" id="mobileUserDropdown" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="fas fa-user"></i>
              </a>
              <ul class="dropdown-menu dropdown-menu-end shadow">
                <li class="px-3 py-2 border-bottom bg-light">
                  <div class="fw-bold text-truncate">{{ auth.user.name }}</div>
                </li>
                <li><a class="dropdown-item py-2" href="/user/dashboard">Cài đặt tài khoản</a></li>
                <li>
                  <form method="POST" action="/logout">
                    <input type="hidden" name="_token" :value="csrfToken">
                    <button type="submit" class="dropdown-item py-2 text-danger">Đăng xuất</button>
                  </form>
                </li>
              </ul>
            </div>
          </template>
          <template v-else>
            <a href="/login" class="action-link me-3"><i class="fas fa-user"></i></a>
          </template>

          <div class="dropdown cart-dropdown">
            <button type="button" class="action-link position-relative border-0 bg-transparent" id="mobilecartDropdown"
              @click.stop="toggleCart">
              <i class="fas fa-shopping-cart"></i>
              <span v-if="cartCount > 0"
                class="cart-count position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ cartCount }}
              </span>
            </button>

            <div class="dropdown-menu dropdown-menu-end cart-dropdown-menu shadow" :class="{ 'show': isCartOpen }">

              <div v-if="loading" class="cart-loading">
                <div class="spinner-border spinner-border-sm text-primary"></div>
                <span>Đang tải...</span>
              </div>

              <div v-else-if="cartItems.length === 0" class="empty-cart-state">
                <div class="icon-box mb-2">
                  <i class="fas fa-shopping-basket"></i>
                </div>
                <p class="text-muted">Giỏ hàng của bạn đang trống</p>
                <a href="/products" class="btn btn-sm btn-primary mt-2">Mua sắm ngay</a>
              </div>

              <div v-else>
                <div class="cart-scroll-area">
                  <div v-for="item in cartItems" :key="item.id" class="cart-item-row">
                    <div class="item-thumb">
                      <img :src="getImageUrl(item)" alt="Product">
                    </div>

                    <div class="item-info">
                      <h6 class="item-name text-truncate">{{ item.name }}</h6>
                      <div class="item-meta">
                        <span class="item-price">{{ formatPrice(item.price) }} đ</span>
                      </div>

                      <div class="qty-control">
                        <button class="qty-btn" @click.stop="updateQuantity(item.id, item.quantity - 1)">-</button>
                        <span class="qty-value">{{ item.quantity }}</span>
                        <button class="qty-btn" @click.stop="updateQuantity(item.id, item.quantity + 1)">+</button>
                      </div>
                    </div>

                    <button class="btn-remove" @click.stop="removeItem(item.id)" title="Xóa">
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </div>

                <div class="cart-footer">
                  <div class="total-row">
                    <span class="label">Tổng cộng:</span>
                    <span class="value">{{ formatPrice(cartTotal) }} VNĐ</span>
                  </div>
                  <div class="action-row">
                    <a href="/cart" class="btn btn-outline-custom flex-grow-1">Xem giỏ</a>
                    <a href="/checkout" class="btn btn-primary-custom flex-grow-1">Thanh toán</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </nav>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title">PCT Pharma</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
      </div>
      <div class="offcanvas-body">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link" href="/" data-inertia>Trang chủ</a></li>
          <li class="nav-item"><a class="nav-link" href="/medical-team" data-inertia>Đội ngũ</a></li>
          <li class="nav-item"><a class="nav-link" href="/products" data-inertia>Sản phẩm</a></li>
          <li class="nav-item"><a class="nav-link" href="/services" data-inertia>Dịch vụ</a></li>
          <li class="nav-item"><a class="nav-link" href="/posts" data-inertia>Góc sức khỏe</a></li>
          <li class="nav-item"><a class="nav-link" href="/contact" data-inertia>Liên hệ</a></li>
        </ul>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'

const props = defineProps({
  auth: { type: Object, default: () => ({ user: null }) }
})

const csrfToken = computed(() => {
  const meta = document.querySelector('meta[name="csrf-token"]')
  return meta ? meta.getAttribute('content') : ''
})

// --- STATE ---
const loading = ref(false)
const cartItems = ref([])
const cartCount = ref(0)
const cartTotal = ref(0)
const isCartOpen = ref(false)

// --- HELPERS ---
const formatPrice = (p) => new Intl.NumberFormat('vi-VN').format(p || 0)
const getImageUrl = (item) => item.image ? `/storage/${item.image}` : '/images/placeholder.png'

// --- ACTIONS ---
const toggleCart = () => {
  isCartOpen.value = !isCartOpen.value
  if (isCartOpen.value) fetchCart()
}

const closeCartOnClickOutside = (e) => {
  if (isCartOpen.value && !e.target.closest('.cart-dropdown')) {
    isCartOpen.value = false
  }
}

const fetchCart = async () => {
  loading.value = true
  try {
    const response = await axios.get('/cart', { params: { format: 'json' } })
    if (response.data) {
      cartItems.value = response.data.preview_items || []
      cartCount.value = response.data.count || 0
      cartTotal.value = response.data.total || 0
    }
  } catch (err) { console.error(err) }
  finally { loading.value = false }
}

const updateQuantity = async (id, qty) => {
  if (qty < 1) return
  try {
    const formData = new FormData()
    formData.append('cart_id', id)
    formData.append('quantity', qty)
    const res = await axios.post('/cart/update', formData)
    if (res.data.success) {
      cartItems.value = res.data.cart.preview_items
      cartCount.value = res.data.cart.count
      cartTotal.value = res.data.cart.total
    }
  } catch (e) { console.error(e) }
}

const removeItem = async (id) => {
  try {
    const formData = new FormData()
    formData.append('cart_id', id)
    const res = await axios.post('/cart/remove', formData)
    if (res.data.success) fetchCart()
  } catch (e) { console.error(e) }
}

const handleGlobalUpdate = () => { fetchCart() }

onMounted(() => {
  fetchCart()
  document.addEventListener('click', closeCartOnClickOutside)
  window.addEventListener('cart-updated', handleGlobalUpdate)

  // Khởi tạo Bootstrap dropdown CHỈ cho User menu (tránh đụng hàng Cart)
  const userDrops = document.querySelectorAll('#userDropdown, #mobileUserDropdown')
  userDrops.forEach(el => new bootstrap.Dropdown(el))
})

onUnmounted(() => {
  document.removeEventListener('click', closeCartOnClickOutside)
  window.removeEventListener('cart-updated', handleGlobalUpdate)
})
</script>

<style scoped>
/* --- 1. CẤU HÌNH CHUNG --- */
.mediaid-header {
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
  background: #fff;
}

.brand-text {
  font-weight: 800;
  font-size: 1.6rem;
  color: #1a56db;
  /* Màu xanh thương hiệu PCT Pharma */
  letter-spacing: -0.5px;
}

/* Badge số lượng màu đỏ */
.cart-count {
  font-size: 10px;
  padding: 4px 6px;
  border: 2px solid #fff;
  /* Viền trắng tách biệt icon */
  top: 5px !important;
}

/* Icon action (User, Cart) */
.action-link {
  color: #333;
  font-size: 1.2rem;
  padding: 8px;
  transition: 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.action-link:hover {
  color: #1a56db;
}

/* --- 2. CSS DROPDOWN MENU (QUAN TRỌNG) --- */
.dropdown-menu {
  border: none;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
  /* Bóng đổ đẹp, sâu */
  border-radius: 12px;
  overflow: hidden;
  padding: 0;
  margin-top: 10px !important;
}

/* Fix hiển thị Vue */
.dropdown-menu.show {
  display: block !important;
  animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* --- 3. GIAO DIỆN BÊN TRONG GIỎ HÀNG --- */
.cart-loading,
.empty-cart-state {
  padding: 40px 20px;
  text-align: center;
  color: #6c757d;
}

.icon-box i {
  font-size: 3rem;
  color: #e9ecef;
  margin-bottom: 10px;
}

/* Khu vực cuộn danh sách */
.cart-scroll-area {
  max-height: 320px;
  overflow-y: auto;
  padding: 0;
}

/* Scrollbar mảnh đẹp */
.cart-scroll-area::-webkit-scrollbar {
  width: 5px;
}

.cart-scroll-area::-webkit-scrollbar-thumb {
  background: #dee2e6;
  border-radius: 10px;
}

/* Từng dòng sản phẩm */
.cart-item-row {
  display: flex;
  align-items: center;
  padding: 12px 16px;
  border-bottom: 1px solid #f1f3f5;
  transition: 0.2s;
}

.cart-item-row:hover {
  background-color: #f8f9fa;
}

/* Ảnh thumb */
.item-thumb img {
  width: 56px;
  height: 56px;
  border-radius: 8px;
  object-fit: cover;
  border: 1px solid #e9ecef;
  background: #fff;
}

/* Thông tin text */
.item-info {
  flex: 1;
  padding: 0 12px;
  min-width: 0;
  /* Fix lỗi text truncate */
}

.item-name {
  font-size: 14px;
  font-weight: 600;
  color: #343a40;
  margin-bottom: 4px;
}

.item-price {
  font-size: 13px;
  color: #1a56db;
  /* Màu xanh giá */
  font-weight: 700;
}

/* Bộ chỉnh số lượng (+ -) */
.qty-control {
  display: inline-flex;
  align-items: center;
  border: 1px solid #dee2e6;
  border-radius: 6px;
  margin-top: 4px;
  background: #fff;
  height: 24px;
}

.qty-btn {
  border: none;
  background: transparent;
  width: 24px;
  color: #495057;
  font-size: 14px;
  line-height: 1;
  cursor: pointer;
}

.qty-btn:hover {
  background: #f1f3f5;
  color: #1a56db;
}

.qty-value {
  font-size: 12px;
  font-weight: 600;
  padding: 0 6px;
  min-width: 20px;
  text-align: center;
}

/* Nút xóa */
.btn-remove {
  border: none;
  background: transparent;
  color: #adb5bd;
  padding: 4px;
  transition: 0.2s;
}

.btn-remove:hover {
  color: #e03131;
  transform: scale(1.1);
}

/* Footer giỏ hàng */
.cart-footer {
  padding: 16px;
  background: #fff;
  border-top: 1px solid #f1f3f5;
}

.total-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.total-row .label {
  font-weight: 700;
  color: #212529;
}

.total-row .value {
  font-weight: 500;
  font-size: 16px;
  color: #e03131;
  /* Màu đỏ giá tổng */
}

/* Button Actions */
.action-row {
  display: flex;
  gap: 10px;
}

.btn-primary-custom {
  background-color: #1a56db;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 8px 12px;
  font-weight: 600;
  font-size: 14px;
  text-align: center;
  text-decoration: none;
  transition: 0.2s;
}

.btn-primary-custom:hover {
  background-color: #1546b3;
  color: white;
}

.btn-outline-custom {
  background-color: #fff;
  color: #1a56db;
  border: 1px solid #1a56db;
  border-radius: 8px;
  padding: 8px 12px;
  font-weight: 600;
  font-size: 14px;
  text-align: center;
  text-decoration: none;
  transition: 0.2s;
}

.btn-outline-custom:hover {
  background-color: #f1f5ff;
}

/* --- 4. RESPONSIVE MOBILE --- */
@media (max-width: 991.98px) {

  /* Cart Dropdown Mobile: Full Width, Fixed */
  .dropdown-menu.cart-dropdown-menu {
    position: fixed !important;
    top: 60px !important;
    /* Cách header một chút */
    left: 10px !important;
    right: 10px !important;
    width: auto !important;
    max-width: none !important;
    z-index: 9999;
    /* Hiệu ứng kính mờ nền */
    backdrop-filter: blur(10px);
  }

  /* Đảm bảo nội dung user dropdown mobile cũng đẹp */
  .d-lg-none .dropdown-menu:not(.cart-dropdown-menu) {
    position: absolute;
    right: 0;
    left: auto;
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
  }
}
</style>