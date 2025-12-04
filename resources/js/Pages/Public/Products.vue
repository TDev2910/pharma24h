<template>
  <div style="margin-top: -20px;">
    <!-- Header Component -->
    <Header :auth="auth" />

    <!--Banner-->
    <div class="container my-4" style="padding-top: 100px;">
      <div class="banner-wrapper">
        <img src="https://nhathuocminhchau.com/storage/uploads/logo/slider-2-5886-hinh.webp" alt="Banner"
          class="banner-image" />
      </div>
    </div>
    <!-- End Banner -->

    <div class="container my-4 py-4">
      <!-- Tabs bộ lọc và sắp xếp -->
      <div class="products-toolbar d-flex align-items-center justify-content-between mb-3">
        <!-- Trái -->
        <div class="d-flex align-items-center gap-3">
          <span class="fw-bold">Bộ lọc</span>
          <button type="button" class="btn btn-outline-secondary btn-sm reset-btn" style="margin-left: 70px;"
            @click="resetPriceFilter">
            Thiết lập lại
          </button>
        </div>

        <!-- Phải -->
        <div class="d-flex align-items-center gap-2" style="margin-right: 650px;">
          <span class="title-filter">Sắp xếp theo:</span>

          <button type="button" class="btn-sort" :class="{ 'active': currentSort === 'desc' }"
            @click="sortProducts('desc')">
            Giá giảm dần
          </button>

          <button type="button" class="btn-sort" :class="{ 'active': currentSort === 'asc' }"
            @click="sortProducts('asc')">
            Giá tăng dần
          </button>
        </div>
      </div>
      <hr class="light-divider" style="width: 215px; background-color: grey;">
      <div class="row">
        <!-- Bộ lọc bên trái -->
        <div class="col-lg-3 col-md-4">
          <!-- Khoảng giá -->
          <div class="mb-4">
            <h6 class="mb-3 fw-bold">Khoảng giá</h6>
            <div class="mb-2">
              <div class="input-group mb-2">
                <input type="number" id="minPrice" class="form-control w-50" placeholder="Tối thiểu"
                  style="max-width:180px;">
                <span class="input-group-text border-start-0">đ</span>
              </div>
              <div class="input-group mb-2">
                <input type="number" id="maxPrice" class="form-control w-50" placeholder="Tối đa"
                  style="max-width:180px;">
                <span class="input-group-text border-start-0">đ</span>
              </div>
              <button id="applyFilterBtn" class="btn btn-primary fw-bold"
                style="background-color:#005EB8; border:none; width: 215px;">
                Áp dụng
              </button>
            </div>

            <!-- Radio giá -->
            <div class="filter-group">
              <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="price" id="price1"
                  @change="setPriceRange(0, 100000)">
                <label class="form-check-label" for="price1">Dưới 100.000 đ</label>
              </div>
              <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="price" id="price2"
                  @change="setPriceRange(100000, 300000)">
                <label class="form-check-label" for="price2">100.000 đ - 300.000 đ</label>
              </div>
              <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="price" id="price3"
                  @change="setPriceRange(300000, 500000)">
                <label class="form-check-label" for="price3">300.000 đ - 500.000 đ</label>
              </div>
              <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="price" id="price4"
                  @change="setPriceRange(500000, null)">
                <label class="form-check-label" for="price4">Trên 500.000 đ</label>
              </div>
            </div>
          </div>
          <hr class="light-divider" style="width: 215px; background-color: grey;">
          <!-- Thương hiệu -->
          <div class="mb-4">
            <h6 class="mb-3 fw-bold">Thương hiệu</h6>
            <div class="mb-3">
              <input type="text" class="form-control mb-2" style="max-width:215px;" placeholder="Nhập tên thương hiệu">
            </div>
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" id="brand1">s
              <label class="form-check-label" for="brand1">STELLA</label>
            </div>
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" id="brand2">
              <label class="form-check-label" for="brand2">DHG Pharma</label>
            </div>
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" id="brand3">
              <label class="form-check-label" for="brand3">Davipharm</label>
            </div>
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" id="brand4">
              <label class="form-check-label" for="brand4">Hasan- Demarpharm</label>
            </div>
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" id="brand5">
              <label class="form-check-label" for="brand5">Domesco</label>
            </div>
            <button class="text-primary reset-link" @click="resetPriceFilter">Xem thêm</button>
          </div>
        </div>

        <!-- Sản phẩm bên phải -->
        <div class="col-lg-9 col-md-8">
          <div class="row g-4 product-grid" id="productGrid">
            <div v-for="product in displayedProducts" :key="product.id + '-' + product.type"
              class="col-lg-3 col-md-4 col-sm-6">
              <div class="product-card-modern">
                <div class="product-img-wrapper" @click="goToProductDetail(product)">
                  <img :src="product.image || 'https://via.placeholder.com/150'" class="product-img"
                    :alt="product.name" />
                </div>
                <div class="product-body">
                  <div class="product-title-modern">{{ product.name }}</div>
                  <div class="product-price-modern">
                    <template v-if="isPromotionActive(product)">
                      <span class="text-danger">{{ formatPrice(product.gia_khuyen_mai) }}</span>
                      <span class="badge bg-warning text-dark ms-2" style="font-size: 0.7rem;">KM</span>
                      <span class="text-muted ms-2" style="text-decoration: line-through; font-size: 0.9rem;">
                        {{ formatPrice(product.gia_ban) }}
                      </span>
                    </template>
                    <template v-else>
                      {{ formatPrice(product.gia_ban) }}
                    </template>
                  </div>
                  <button class="btn product-btn" :class="{
                    'btn-secondary': isOutOfStock(product),
                    'btn-success': isPromotionActive(product) && !isOutOfStock(product),
                    'btn-primary': !isPromotionActive(product) && !isOutOfStock(product)
                  }" @click.stop="addToCartHandler(product)" :disabled="isButtonDisabled(product)">
                    <i class="fas fa-cart-plus me-2"></i>
                    {{ getButtonLabel(product) }}
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Hiển thị thông báo khi không có sản phẩm -->
          <div v-if="displayedProducts.length === 0" class="text-center py-5">
            <p class="text-muted">Không tìm thấy sản phẩm nào trong khoảng giá đã chọn.</p>
            <button class="btn btn-outline-primary" @click="resetPriceFilter">
              Xóa bộ lọc
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Header from './components/Header.vue'
import Footer from './components/Footer.vue'
import { computed, onMounted, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
  auth: { type: Object, default: () => ({ user: null }) },
  products: { type: Array, default: () => [] }
})

// State để lưu danh sách sản phẩm đã lọc
const displayedProducts = ref([...props.products])

const currentSort = ref('desc');

// 2. Viết hàm xử lý sắp xếp
function sortProducts(order) {
  // Cập nhật trạng thái nút active
  currentSort.value = order;

  // Thực hiện sắp xếp trên mảng displayedProducts
  displayedProducts.value.sort((a, b) => {
    // Lấy giá bán, nếu không có thì mặc định là 0 để tránh lỗi
    const priceA = a.gia_ban || 0;
    const priceB = b.gia_ban || 0;

    if (order === 'asc') {
      return priceA - priceB;
    } else {
      return priceB - priceA;
    }
  });
}
// Hàm lọc sản phẩm theo giá (Client-side)
function applyPriceFilter(min, max) {
  let minPrice = min || 0
  let maxPrice = max || Infinity

  // Validate
  if (minPrice > maxPrice && maxPrice !== Infinity) {
    alert('Giá tối thiểu không thể lớn hơn giá tối đa!')
    return
  }

  console.log(`Lọc sản phẩm trong khoảng: ${minPrice}đ đến ${maxPrice === Infinity ? 'vô cùng' : maxPrice + 'đ'}`)

  // Lọc sản phẩm
  displayedProducts.value = props.products.filter(product => {
    const price = product.gia_ban || 0
    return price >= minPrice && price <= maxPrice
  })
}

// Hàm set giá từ radio button
function setPriceRange(min, max) {
  const minPriceInput = document.getElementById('minPrice')
  const maxPriceInput = document.getElementById('maxPrice')

  if (minPriceInput) minPriceInput.value = min
  if (maxPriceInput) maxPriceInput.value = max || ''

  applyPriceFilter(min, max)
}

// Hàm reset bộ lọc
function resetPriceFilter() {
  displayedProducts.value = [...props.products]

  const minPriceInput = document.getElementById('minPrice')
  const maxPriceInput = document.getElementById('maxPrice')
  const radioButtons = document.querySelectorAll('input[name="price"]')

  if (minPriceInput) minPriceInput.value = ''
  if (maxPriceInput) maxPriceInput.value = ''
  radioButtons.forEach(radio => {
    radio.checked = false
  })
}

function isPromotionActive(product) {
  // Kiểm tra null, undefined, 0, và chuyển đổi sang number
  const tonKhuyenMai = Number(product.ton_khuyen_mai) || 0;
  const giaKhuyenMai = Number(product.gia_khuyen_mai) || 0;
  return tonKhuyenMai > 0 && giaKhuyenMai > 0;
}

function isOutOfStock(product) {
  // Kiểm tra null, undefined, 0, string "0", và số âm
  const tonKho = Number(product.ton_kho) || 0;
  return tonKho <= 0;  // ✅ Dùng <= 0 thay vì == 0 để bao gồm cả số âm
}

function isButtonDisabled(product) {
  if (isOutOfStock(product)) return true;
  
  // Kiểm tra nếu có khuyến mãi nhưng hết tồn khuyến mãi
  if (isPromotionActive(product)) {
    const tonKhuyenMai = Number(product.ton_khuyen_mai) || 0;
    if (tonKhuyenMai <= 0) return true;
  }
  
  return false;
}

function getButtonLabel(product) {
  if (isOutOfStock(product)) return 'Hết hàng';
  if (isPromotionActive(product)) return 'Mua với giá KM';
  return 'Thêm vào giỏ';
}

function formatPrice(amount) {
  if (!amount) return '0 VNĐ';
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(amount);
}

// Xử lý hàm thêm vào giỏ hàng
async function addToCartHandler(product) {
  try {
    const response = await axios.post('/cart/add', {
      item_id: product.id,           // ✅ Dùng product từ tham số
      item_type: product.type,        // ✅ Dùng product.type
      quantity: 1,
      is_promotion: isPromotionActive(product)  // ✅ Gọi function với product
    });
    if (response.data.success) {
      window.dispatchEvent(new CustomEvent('cart-updated'));
      const cartDropdown = document.querySelector('#cartDropdown');
      if (cartDropdown && typeof bootstrap !== 'undefined') {
        const bsDropdown = new bootstrap.Dropdown(cartDropdown);
        bsDropdown.show();
      }
      if (typeof window.loadCartItems === 'function') {
        setTimeout(() => window.loadCartItems(), 100);
      }
      if (typeof window.showNotification === 'function') {
        window.showNotification('Đã thêm vào giỏ hàng!', 'success');
      }
    }
  } catch (e) {
    console.error('Error adding to cart:', e);
    if (typeof window.showNotification === 'function') {
      window.showNotification('Có lỗi xảy ra!', 'error');
    }
  }
}

//thêm sản phẩm vào giỏ hàng
// async function addToCart({ id, type }) {
//   try {
//     const response = await axios.post('/cart/add', {
//       item_id: id,
//       item_type: type,
//       quantity: 1
//     });

//     if (response.data.success) {
//       // 1. Cập nhật số lượng giỏ hàng
//       window.dispatchEvent(new CustomEvent('cart-updated'));

//       // 2. Tự động mở dropdown giỏ hàng
//       const cartDropdown = document.querySelector('#cartDropdown');
//       if (cartDropdown && typeof bootstrap !== 'undefined') {
//         const bsDropdown = new bootstrap.Dropdown(cartDropdown);
//         bsDropdown.show();
//       }

//       // 3. Load cart items (gọi function từ cart.js)
//       if (typeof window.loadCartItems === 'function') {
//         setTimeout(() => window.loadCartItems(), 100);
//       }

//       // 4. Hiển thị thông báo sau khi thêm vào giỏ hàng
//       if (typeof window.showNotification === 'function') {
//         window.showNotification('Đã thêm vào giỏ hàng!', 'success');
//       }
//     }
//   } catch (e) {
//     console.error('Error adding to cart:', e);
//     if (typeof window.showNotification === 'function') {
//       window.showNotification('Có lỗi xảy ra!', 'error');
//     }
//   }
// }

//chuyển hướng đến trang chi tiết sản phẩm
function goToProductDetail(product) {
  router.visit(`/products/${product.type}/${product.id}`)
}

onMounted(() => {
  // window.scrollTo({ top: 0, behavior: 'smooth' });

  // Thêm event listener cho nút Áp dụng (vanilla JS như bạn muốn)
  const minPriceInput = document.getElementById('minPrice')
  const maxPriceInput = document.getElementById('maxPrice')
  const applyFilterBtn = document.getElementById('applyFilterBtn')

  if (applyFilterBtn) {
    applyFilterBtn.addEventListener('click', () => {
      const minPrice = parseInt(minPriceInput?.value) || 0
      const maxPrice = parseInt(maxPriceInput?.value) || Infinity
      applyPriceFilter(minPrice, maxPrice)
    })
  }
})
</script>

<style scoped>
.title-filter {
  font-family: 'Roboto', Arial, sans-serif;
  font-size: 15px;
  color: #333;
  margin-right: 7px;
}


.products-toolbar {
  min-height: 40px;
  gap: 12px;
  flex-wrap: nowrap;
}

.reset-link {
  color: #0d6efd;
  text-decoration: none;
  font-weight: 500;
}

.reset-link:hover {
  text-decoration: underline;
}

/* Nút reset bộ lọc */
.reset-btn {
  color: #6b7280;
  background-color: #fff;
  border: 1px solid #d9dde3;
  padding: 0.35rem 0.9rem;
  border-radius: 8px;
  font-weight: 500;
  font-size: 0.875rem;
  transition: all 0.2s;
}

.reset-btn:hover {
  background-color: #f8f9fa;
  border-color: #6b7280;
  color: #374151;
}

.btn-sort {
  background: #fff;
  border: 1px solid #d9dde3;
  color: #6b7280;
  padding: .35rem .9rem;
  border-radius: 12px;
  font-weight: 500;
  line-height: 1.2;
}

.btn-sort:hover {
  background: #f8f9fb;
}

.btn-sort.active {
  color: #0d6efd;
  border-color: #0d6efd;
  box-shadow: 0 0 0 2px rgba(13, 110, 253, .12);
}

/* Nút sắp xếp */
.sort-btn {
  color: #666;
  background-color: #fff;
}

.sort-btn:hover {
  background-color: #f8f9fa;
}

/* Card sản phẩm */
.product-card {
  transition: all 0.2s ease;
  box-shadow: none;
}

.product-card:hover {
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
  transform: translateY(-2px);
}

.product-title {
  font-size: 0.9rem;
  min-height: 40px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Nút "Cần tư vấn dược sĩ" */
.btn-warning {
  background-color: #FF9800;
  border-color: #FF9800;
  font-size: 0.9rem;
}

.btn-warning:hover {
  background-color: #F57C00;
  border-color: #F57C00;
}

/* Nút "Chọn sản phẩm" */
.btn-primary {
  background-color: #1a56db;
  border-color: #1a56db;
  font-size: 0.9rem;
}

.btn-primary:hover {
  background-color: #1650cf;
  border-color: #1650cf;
}

/* Responsive */
@media (max-width: 767px) {
  .filter-tabs {
    flex-direction: column;
    align-items: flex-start !important;
  }

  .filter-tabs>div.ms-auto {
    margin-top: 10px;
    margin-left: 0 !important;
  }

  .product-title {
    font-size: 0.85rem;
  }
}

.product-grid {
  margin-bottom: 0;
}

.product-card-modern {
  background: #fff;
  border: 1px solid #e3e6ef;
  border-radius: 14px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  padding: 16px 16px 12px 16px;
  display: flex;
  flex-direction: column;
  align-items: center;
  min-height: 340px;
  position: relative;
  transition: box-shadow 0.2s;
}

.product-card-modern:hover {
  box-shadow: 0 6px 24px rgba(0, 0, 0, 0.12);
  cursor: pointer;
}

.product-label {
  position: absolute;
  top: 12px;
  left: 12px;
  background: #ff9800;
  color: #fff;
  font-size: 0.75rem;
  font-weight: 600;
  border-radius: 6px;
  padding: 2px 10px;
  z-index: 2;
  display: inline-block;
}

.product-img-wrapper {
  width: 100%;
  height: 160px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 10px;
}

.product-img {
  max-width: 100%;
  max-height: 140px;
  object-fit: contain;
  border-radius: 8px;
  background: #f8f9fb;
}

.product-body {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  flex: 1;
}

.product-title-modern {
  font-size: 1rem;
  font-weight: 500;
  color: #222;
  margin-bottom: 6px;
  min-height: 2.4em;
  max-height: 2.4em;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  line-height: 1.2;
  line-clamp: 2;
  /* For Firefox and future browsers */
}

.product-price-modern {
  color: #1a56db;
  font-size: 1.15rem;
  font-weight: 700;
  margin-bottom: 12px;
}

.product-btn {
  width: 100%;
  border-radius: 8px;
  font-weight: 600;
  font-size: 1rem;
  background: #1a56db;
  border: none;
  color: #fff;
  padding: 8px 0;
  transition: background 0.2s;
}

.product-btn:hover {
  background: #1650cf;
}

@media (max-width: 991px) {
  .product-card-modern {
    min-height: 320px;
    padding: 12px 8px 10px 8px;
  }

  .product-img-wrapper {
    height: 120px;
  }
}

@media (max-width: 767px) {
  .product-card-modern {
    min-height: 280px;
  }

  .product-img-wrapper {
    height: 90px;
  }

  .product-title-modern {
    font-size: 0.95rem;
    min-height: 36px;
  }

  .product-price-modern {
    font-size: 1rem;
  }
}

.banner-wrapper {
  width: 100%;
  margin-bottom: 24px;
}

.banner-image {
  width: 100%;
  height: auto;
  object-fit: cover;
  border-radius: 12px;
  display: block;
}
</style>
