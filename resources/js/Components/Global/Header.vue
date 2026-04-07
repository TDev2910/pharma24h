<template>
  <header class="mediaid-header sticky-top">
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom py-0">
      <div class="container">
        <button class="navbar-toggler border-0 p-0 me-3" type="button" data-bs-toggle="offcanvas"
          data-bs-target="#mobileMenu">
          <i class="fas fa-bars fs-5"></i>
        </button>

        <div class="navbar-brand d-flex align-items-center" style="cursor: default;">
          <div class="brand-container">
            <img :src="'/images/logo.png'" alt="Pharma Logo" class="brand-logo"
              onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
          </div>
        </div>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <Link class="nav-link" href="/">Trang chủ</Link>
            </li>
            <li class="nav-item">
              <Link class="nav-link" href="/medical-team">Đội ngũ</Link>
            </li>
            <li class="nav-item">
              <Link class="nav-link" href="/products">Sản phẩm</Link>
            </li>
            <li class="nav-item">
              <Link class="nav-link" href="/services">Dịch vụ</Link>
            </li>
            <li class="nav-item">
              <Link class="nav-link" href="/posts">Góc sức khỏe</Link>
            </li>
            <li class="nav-item">
              <Link class="nav-link" href="/contact">Liên hệ</Link>
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
          <li class="nav-item">
            <a class="nav-link" href="/" data-inertia @click="closeMenu">Trang chủ</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/medical-team" data-inertia @click="closeMenu">Đội ngũ</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/products" data-inertia @click="closeMenu">Sản phẩm</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/services" data-inertia @click="closeMenu">Dịch vụ</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/posts" data-inertia @click="closeMenu">Góc sức khỏe</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/contact" data-inertia @click="closeMenu">Liên hệ</a>
          </li>
        </ul>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link } from "@inertiajs/vue3";
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


const closeMenu = () => {
  const menuElement = document.getElementById('mobileMenu');
  const bsOffcanvas = bootstrap.Offcanvas.getInstance(menuElement);
  if (bsOffcanvas) {
    bsOffcanvas.hide();
  }
};

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

<style scoped src="@/../css/Public/Global/header.css"></style>