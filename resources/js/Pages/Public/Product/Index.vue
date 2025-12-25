<template>
  <div style="margin-top: -20px;">
    <Header :auth="auth" />

    <div class="container my-4 responsive-top-spacing" style="padding-top: 100px;">
      <div class="banner-wrapper">
        <img src="https://nhathuocminhchau.com/storage/uploads/logo/slider-2-5886-hinh.webp" alt="Banner"
          class="banner-image" />
      </div>
    </div>
    <div class="container my-4 py-4">
      <div class="products-toolbar d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-3 toolbar-left">
          <span class="fw-bold">Bộ lọc</span>
          <button type="button" class="btn btn-outline-secondary btn-sm reset-btn" style="margin-left: 70px;"
            @click="resetPriceFilter">
            Thiết lập lại
          </button>
        </div>

        <div class="d-flex align-items-center gap-2 toolbar-right" style="margin-left: auto;">
          <span class="title-filter">Sắp xếp theo:</span>

          <div class="sort-buttons">
             <button type="button" class="btn-sort" :class="{ 'active': currentSort === 'desc' }"
            @click="sortProducts('desc')">
            Giá giảm dần
          </button>

          <button type="button" class="btn-sort" :class="{ 'active': currentSort === 'asc' }"
            @click="sortProducts('asc')">
            Giá tăng dần
          </button>
          </div>

          <input type="text" class="form-control search-input" placeholder="Tìm kiếm" style="width: 300px;margin-left: 10px;"
            v-model="searchQuery" @input="handleSearch">
        </div>
      </div>
      <hr class="light-divider sidebar-divider" style="width: 215px; background-color: grey;">
      <div class="row">
        <div class="col-lg-3 col-md-4 mb-4 mb-md-0">
          <div class="mb-4">
            <h6 class="mb-3 fw-bold">Khoảng giá</h6>
            <div class="mb-2">
              <div class="d-flex flex-column gap-2 mb-2">
                <div class="input-group">
                  <input type="number" id="minPrice" class="form-control" placeholder="Min">
                  <span class="input-group-text border-start-0">đ</span>
                </div>
                <div class="input-group">
                  <input type="number" id="maxPrice" class="form-control" placeholder="Max">
                  <span class="input-group-text border-start-0">đ</span>
                </div>
              </div>

              <button id="applyFilterBtn" class="btn btn-primary fw-bold sidebar-btn"
                style="background-color:#005EB8; border:none; width: 215px;">
                Áp dụng
              </button>
            </div>

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
        </div>

        <div class="col-lg-9 col-md-8">
          <div class="row g-3 g-md-4 product-grid" id="productGrid">
            <div v-for="product in displayedProducts" :key="product.id + '-' + product.type"
              class="col-6 col-md-6 col-lg-3"> <div class="product-card-modern">
                <div class="product-label" v-if="isPromotionActive(product)">-{{ Math.round((1 - product.gia_khuyen_mai/product.gia_ban) * 100) }}%</div>
                <div class="product-img-wrapper" @click="goToProductDetail(product)">
                  <img :src="product.image || 'https://via.placeholder.com/150'" class="product-img"
                    :alt="product.name" />
                </div>
                <div class="product-body">
                  <div class="product-title-modern" :title="product.name">{{ product.name }}</div>
                  <div class="product-price-modern">
                    <template v-if="isPromotionActive(product)">
                      <div class="d-flex flex-column flex-wrap">
                        <span class="text-danger price-main">{{ formatPrice(product.gia_khuyen_mai) }}</span>
                        <div class="price-old-group">
                           <span class="text-muted text-decoration-line-through price-old">
                            {{ formatPrice(product.gia_ban) }}
                          </span>
                        </div>
                      </div>
                    </template>
                    <template v-else>
                      <span class="price-main">{{ formatPrice(product.gia_ban) }}</span>
                    </template>
                  </div>
                  <button class="btn product-btn" :class="{
                        'btn-status-disabled': isOutOfStock(product),
                        'btn-status-promo': isPromotionActive(product) && !isOutOfStock(product),
                        'btn-status-default': !isPromotionActive(product) && !isOutOfStock(product)
                    }" @click.stop="addToCartHandler(product)" :disabled="isButtonDisabled(product)">

                    <i class="fas fa-cart-plus me-1"></i>
                    <span class="btn-text">{{ getButtonLabel(product) }}</span>
                    </button>
                </div>
              </div>
            </div>
          </div>

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
import Header from '@/Components/Global/Header.vue'
import Footer from '@/Components/Global/Footer.vue'
import { computed, onMounted, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
  auth: { type: Object, default: () => ({ user: null }) },
  products: { type: Array, default: () => [] }
})

const searchQuery = ref('');
const debounceTimer = ref(null);

function handleSearch() {
  clearTimeout(debounceTimer.value);
  debounceTimer.value = setTimeout(() => {
    applySearch()
  }, 300)
}


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

function applySearch() {
  const query = searchQuery.value.toLowerCase().trim();
  // Nếu không có từ khóa, hiển thị tất cả sản phẩm gốc
  if (!query) {
    displayedProducts.value = [...props.products]
    return
  }
  // Lọc sản phẩm theo tên (có thể mở rộng thêm mã sản phẩm)
  displayedProducts.value = props.products.filter(product => {
    const name = (product.name || '').toLowerCase()
    // Có thể thêm tìm theo mã sản phẩm nếu có
    return name.includes(query)
  })

  // Giữ nguyên sắp xếp hiện tại sau khi tìm kiếm
  if (currentSort.value) {
    sortProducts(currentSort.value)
  }
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

  // Lấy danh sách sản phẩm đã được lọc theo search (nếu có)
  const baseProducts = searchQuery.value ? displayedProducts.value : props.products

  // Lọc sản phẩm
  displayedProducts.value = baseProducts.filter(product => {
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
  searchQuery.value = ''

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
  return tonKho <= 0;
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
  if (isPromotionActive(product)) return 'Khuyến Mãi';
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
      item_id: product.id,
      item_type: product.type,
      quantity: 1,
      is_promotion: isPromotionActive(product)
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
  white-space: nowrap;
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
  white-space: nowrap;
}

.reset-btn:hover {
  background-color: #f8f9fa;
  border-color: #6b7280;
  color: #374151;
}

.sort-buttons {
  display: flex;
  gap: 8px;
}

.btn-sort {
  background: #fff;
  border: 1px solid #d9dde3;
  color: #6b7280;
  padding: .35rem .9rem;
  border-radius: 12px;
  font-weight: 500;
  line-height: 1.2;
  white-space: nowrap;
  font-size: 0.9rem;
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
  height: 100%;
  min-height: 360px;
  position: relative;
  transition: box-shadow 0.2s;
}

.product-card-modern:hover {
  box-shadow: 0 6px 24px rgba(0, 0, 0, 0.12);
  cursor: pointer;
}

.product-label {
  position: absolute;
  top: 10px;
  left: 10px;
  background: #dc3545; 
  color: #fff;
  font-size: 0.7rem;
  font-weight: 700;
  border-radius: 4px;
  padding: 2px 6px;
  z-index: 2;
  display: inline-block;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
  background: #fff; /* Đổi nền trắng cho ảnh */
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
  margin-bottom: 8px;
  min-height: 2.4em;
  max-height: 2.4em;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  line-height: 1.25;
}

.product-price-modern {
  color: #1a56db;
  font-size: 1.15rem;
  font-weight: 700;
  margin-bottom: auto; /* Đẩy button xuống đáy */
  padding-bottom: 12px;
  width: 100%;
}

.price-old-group {
  line-height: 1;
  margin-top: 2px;
}

.price-old {
  font-size: 0.85rem;
  font-weight: 400;
}

.product-btn {
  width: 100%;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.95rem;
  padding: 8px 0;
  border: none !important;
  color: #fff !important;
  /* Tắt hiệu ứng chuyển màu mượt để cảm giác bấm chắc chắn hơn */
  transition: none !important;
  margin-top: auto;
  box-shadow: none !important;
}

/* 1. TRẠNG THÁI KHUYẾN MÃI (Màu Xanh Lá) */
.btn-status-promo,
.btn-status-promo:hover,
.btn-status-promo:focus,
.btn-status-promo:active {
  background-color: #28a745 !important; /* Xanh lá cây */
  opacity: 1 !important;
}

/* 2. TRẠNG THÁI MẶC ĐỊNH (Màu Xanh Dương hiện tại) */
.btn-status-default,
.btn-status-default:hover,
.btn-status-default:focus,
.btn-status-default:active {
  background-color: #1a56db !important; /* Xanh dương chuẩn của bạn */
  opacity: 1 !important;
}

/* 3. TRẠNG THÁI HẾT HÀNG (Màu Xám) */
.btn-status-disabled,
.btn-status-disabled:hover,
.btn-status-disabled:focus,
.btn-status-disabled:active {
  background-color: #6c757d !important; /* Màu xám */
  cursor: not-allowed;
  opacity: 0.8 !important;
}

.product-btn:hover {
  background: #1650cf;
}

.banner-wrapper {
  width: 100%;
  margin-bottom: 24px;
}

.input-group
{
    width: 219px;
}

.banner-image {
  width: 100%;
  height: auto;
  object-fit: cover;
  border-radius: 12px;
  display: block;
  min-height: 120px; /* Đảm bảo banner không bị quá bé */
}

/* =========================================
   MEDIA QUERIES FOR RESPONSIVE (ADDITIONS)
   ========================================= */

/* Tablet & Mobile (Dưới 992px) */
@media (max-width: 991px) {
  .products-toolbar {
    flex-wrap: wrap; /* Cho phép xuống dòng */
    height: auto;
    padding: 10px 0;
  }

  .products-toolbar .toolbar-left,
  .products-toolbar .toolbar-right {
    width: 100%; /* Chiếm hết chiều ngang */
    justify-content: space-between;
  }

  .products-toolbar .toolbar-right {
    margin-left: 0 !important; /* Ghi đè margin inline cũ */
    flex-wrap: wrap;
  }

  /* Ghi đè inline style của input search */
  .search-input {
    width: 100% !important;
    margin-left: 0 !important;
    margin-top: 8px;
  }

  /* Ghi đè inline style của nút reset */
  .reset-btn {
    margin-left: auto !important; /* Đẩy sang phải thay vì margin cứng 70px */
  }

  /* Ẩn bớt bộ lọc trên mobile nếu cần hoặc style lại */
  .sidebar-btn, .sidebar-divider {
    width: 100% !important; /* Ghi đè width 215px inline */
  }

  .product-card-modern {
    min-height: 340px;
    padding: 12px;
  }
}

/* Mobile (Dưới 768px) */
@media (max-width: 767px) {
  /* Xử lý khoảng cách top cho banner */
  .responsive-top-spacing {
    padding-top: 80px !important; /* Ghi đè 100px inline */
  }

  /* Toolbar sắp xếp lại */
  .products-toolbar {
    gap: 8px;
  }

  .sort-buttons {
    width: 100%;
    display: flex;
    justify-content: space-between;
  }

  .btn-sort {
    flex: 1;
    text-align: center;
    font-size: 0.8rem;
    padding: 6px 4px;
  }

  .title-filter {
    display: none; /* Ẩn chữ 'Sắp xếp theo' trên mobile cho gọn */
  }

  /* Product Card Mobile */
  .product-card-modern {
    min-height: 290px;
    padding: 10px 8px;
    border-radius: 10px;
  }

  .product-img-wrapper {
    height: 110px;
  }

  .product-img {
    max-height: 100px;
  }

  .product-title-modern {
    font-size: 0.85rem;
    margin-bottom: 4px;
    min-height: 2.5em; /* Giảm chiều cao tiêu đề */
  }

  .product-price-modern {
    font-size: 0.95rem;
    padding-bottom: 8px;
  }

  .price-old {
    font-size: 0.75rem;
  }

  .product-btn {
    font-size: 0.8rem;
    padding: 6px 0;
  }

  .btn-text {
    /* Có thể ẩn chữ 'Thêm vào giỏ' chỉ hiện icon nếu muốn, ở đây giữ lại nhưng font bé */
  }

  /* Input giá bộ lọc nằm ngang */
  .col-lg-3 .d-flex.gap-2 {
    /* Giữ input min/max nằm ngang */
  }
}

/* Extra small devices (Dưới 400px) */
@media (max-width: 400px) {
  .product-card-modern {
    min-height: 270px;
  }
  .product-img-wrapper {
    height: 90px;
  }
}
</style>
