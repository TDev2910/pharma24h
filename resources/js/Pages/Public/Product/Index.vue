<template>
  <div class="mt-5 pt-3">
    <div class="container my-4 responsive-top-spacing">
      <div class="banner-wrapper">
        <img src="https://nhathuocminhchau.com/storage/uploads/logo/slider-2-5886-hinh.webp" alt="Banner"
          class="banner-image" />
      </div>
    </div>
    <div class="container my-4 py-4">
      <div class="row g-4">
        <div class="col-lg-2 col-md-3 mb-4 mb-md-0">
          <div class="d-flex align-items-center justify-content-between mb-3 toolbar-left">
            <span class="fw-bold">Bộ lọc</span>
            <button type="button" class="btn btn-outline-secondary btn-sm reset-btn" @click="resetPriceFilter">
              Thiết lập lại
            </button>
          </div>
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

              <button id="applyFilterBtn" class="btn btn-primary fw-bold sidebar-btn w-100"
                style="width: 110% !important; background-color:#005EB8; border:none; color: white;">
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

        <div class="col-lg-10 col-md-9">
          <!-- Toolbar Moved Here -->
          <div class="products-toolbar d-flex align-items-center justify-content-between mb-3 p-0"
            style="background: transparent; border: none;">
            <div class="d-flex align-items-center gap-2 toolbar-right ms-auto">
              <span class="title-filter">Sắp xếp:</span>
              <div class="sort-buttons">
                <button type="button" class="btn-sort" :class="{ 'active': currentSort === 'desc' }"
                  @click="sortProducts('desc')">
                  Giá giảm
                </button>
                <button type="button" class="btn-sort" :class="{ 'active': currentSort === 'asc' }"
                  @click="sortProducts('asc')">
                  Giá tăng
                </button>
              </div>
              <input type="text" class="form-control search-input" placeholder="Tìm kiếm sản phẩm..." style="width: 250px; background-color: #f9fbff; 
                border: 1.5px solid #cfe0ff; border-radius: 10px; padding: 8px 14px; transition: all 0.3s ease;"
                v-model="searchQuery" @input="handleSearch">
            </div>
          </div>
          <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-2 g-md-3 product-grid" id="productGrid">
            <div v-for="product in displayedProducts" :key="product.id + '-' + product.type" class="col">
              <div class="product-card-modern">
                <div class="product-label" v-if="isPromotionActive(product)">-{{ Math.round((1 -
                  product.gia_khuyen_mai / product.gia_ban) * 100) }}%</div>
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
  if (!product.slug) {
    alert("Lỗi: Sản phẩm này chưa có Slug! Kiểm tra Database của bạn.");
    console.log("Sản phẩm lỗi:", product);
    return;
  }
  router.visit(`/san-pham/${product.slug}`);
}

onMounted(() => {
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
@import "../../../../css/Public/Products/index/index.css";
</style>
