<template>
  <div style="margin-top: 100px;">
    <!-- Header Component -->
    <Header :auth="auth" />
    
    <!-- Product Detail Content -->
    <div class="container py-5">
      <div class="row">
        <!-- Product Image -->
        <div class="col-md-5">
          <div class="product-image-container">
            <img 
              :src="product.image ? `/storage/${product.image}` : 'https://via.placeholder.com/400'" 
              :alt="product.ten_thuoc || product.ten_hang_hoa"
              class="product-detail-image"
            />
          </div>
        </div>
        
        <!-- Product Info -->
        <div class="col-md-7">
          <div class="product-detail-info">
            <h1 class="product-name" style="font-style:bold">{{ product.ten_thuoc || product.ten_hang_hoa }}</h1>
            
            <div class="product-price mb-4">
              <span class="current-price">{{ formatCurrency(product.gia_ban) }}</span>
              <span class="unit">/{{ product.don_vi_tinh || 'Đơn vị' }}</span>
            </div>
            <p>Phân loại sản phẩm</p>
            <div class="product-meta mb-4">
              <div class="meta-item">
                <strong>Mã hàng:</strong> {{ product.ma_hang || 'N/A' }}
              </div>
              <div class="meta-item" v-if="product.category">
                <strong>Danh mục:</strong> {{ product.category.name }}
              </div>
              <div class="meta-item" v-if="product.manufacturer">
                <strong>Nhà sản xuất:</strong> {{ product.manufacturer.name }}
              </div>
              <div class="meta-item" v-if="type === 'medicine' && product.drugRoute">
                <strong>Đường dùng:</strong> {{ product.drugRoute.name }}
              </div>
              <div class="meta-item">
                <strong>Tồn kho:</strong> 
                <span :class="product.ton_kho > 0 ? 'text-success' : 'text-danger'">
                  {{ product.ton_kho || 0 }} {{ product.don_vi_tinh }}
                </span>
              </div>
            </div>
            
            <!-- Add to Cart -->
            <div class="product-actions">
              <button 
                class="btn btn-primary btn-lg"
                @click="addToCart"
                :disabled="!product.ton_kho || product.ton_kho <= 0">
                <i class="fas fa-cart-plus me-2"></i>
                {{ product.ton_kho > 0 ? 'Thêm vào giỏ hàng' : 'Hết hàng' }} 
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Product Description -->
      <div class="row mt-5">
        <div class="col-12">
          <div class="product-description">
            <h3>Thông tin chi tiết</h3>
            <div v-html="sanitizedDescription" class="html-content"></div>       
            <!-- Thông tin bổ sung -->
            <div class="description-content mt-4" v-if="type === 'medicine'">
              <p v-if="product.hoat_chat"><strong>Hoạt chất:</strong> {{ product.hoat_chat }}</p>
              <p v-if="product.ham_luong"><strong>Hàm lượng:</strong> {{ product.ham_luong }}</p>
              <p v-if="product.quy_cach_dong_goi"><strong>Quy cách đóng gói:</strong> {{ product.quy_cach_dong_goi }}</p>
            </div>
            <div class="description-content mt-4" v-else>
              <p v-if="product.quy_cach_dong_goi"><strong>Quy cách đóng gói:</strong> {{ product.quy_cach_dong_goi }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Footer Component -->
    <Footer />
  </div>
</template>

<script setup>
import Header from './components/Header.vue'
import Footer from './components/Footer.vue'
import axios from 'axios'
import { computed } from 'vue' 
import DOMPurify from 'dompurify' 

const props = defineProps({
  auth: { type: Object, default: () => ({ user: null }) },
  product: { type: Object, required: true },
  type: { type: String, required: true }
})

//Sanitize HTML từ mo_ta để tránh XSS
const sanitizedDescription = computed(() => {
  const dirtyHTML = props.product.mo_ta || ''
  return DOMPurify.sanitize(dirtyHTML, {
    ALLOWED_TAGS: ['p', 'br', 'strong', 'em', 'u', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 
                  'ul', 'ol', 'li', 'a', 'img', 'blockquote', 'code', 'pre', 'span', 'div'],
    ALLOWED_ATTR: ['href', 'target', 'src', 'alt', 'class', 'style'],
    ALLOW_DATA_ATTR: false
  })
})

// Format currency
function formatCurrency(amount) {
  if (!amount) return '0 VNĐ'
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(amount)
}

// Add to cart function
async function addToCart() {
  try {
    const response = await axios.post('/cart/add', { 
      item_id: props.product.id, 
      item_type: props.type, 
      quantity: 1 
    });
    
    if (response.data.success) {
      // Cập nhật số lượng giỏ hàng
      window.dispatchEvent(new CustomEvent('cart-updated'));
      
      // Mở dropdown giỏ hàng
      const cartDropdown = document.querySelector('#cartDropdown');
      if (cartDropdown && typeof bootstrap !== 'undefined') {
        const bsDropdown = new bootstrap.Dropdown(cartDropdown);
        bsDropdown.show();
      }
      
      // Load cart items
      if (typeof window.loadCartItems === 'function') {
        setTimeout(() => window.loadCartItems(), 100);
      }
      
      // Hiển thị thông báo
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
</script>

<style scoped>
.product-image-container {
  background: #f8f9fa;
  border-radius: 12px;
  padding: 20px;
  text-align: center;
}

.product-detail-image {
  max-width: 100%;
  max-height: 400px;
  object-fit: contain;
  border-radius: 8px;
}

.product-detail-info {
  padding: 20px;
}

.product-name {
  font-size: 2rem;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 20px;
}

.product-price {
  display: flex;
  align-items: baseline;
  gap: 8px;
}

.current-price {
  font-size: 2rem;
  font-weight: 700;
  color: #1a56db;
}

.unit {
  font-size: 1rem;
  color: #6c757d;
}

.product-meta {
  background: #f8f9fa;
  border-radius: 8px;
  padding: 20px;
}

.meta-item {
  padding: 8px 0;
  border-bottom: 1px solid #e9ecef;
}

.meta-item:last-child {
  border-bottom: none;
}

.meta-item strong {
  color: #495057;
  margin-right: 8px;
}

.product-actions {
  margin-top: 30px;
}

.product-actions .btn {
  min-width: 200px;
  padding: 12px 30px;
  font-size: 1.1rem;
  font-weight: 600;
}

.product-description {
  background: #fff;
  border-radius: 12px;
  padding: 30px;
  border: 1px solid #e9ecef;
}

.product-description h3 {
  color: #2c3e50;
  margin-bottom: 20px;
  padding-bottom: 10px;
  border-bottom: 2px solid #1a56db;
}

.description-content p {
  margin-bottom: 12px;
  line-height: 1.6;
}

/* ✅ THÊM: Styles cho HTML content từ PrimeVue Editor */
.html-content {
  line-height: 1.8;
  color: #495057;
}

.html-content >>> p {
  margin-bottom: 1rem;
}

.html-content >>> strong,
.html-content >>> b {
  font-weight: 600;
  color: #2c3e50;
}

.html-content >>> em,
.html-content >>> i {
  font-style: italic;
}

.html-content >>> u {
  text-decoration: underline;
}

.html-content >>> h1,
.html-content >>> h2,
.html-content >>> h3,
.html-content >>> h4,
.html-content >>> h5,
.html-content >>> h6 {
  margin-top: 1.5rem;
  margin-bottom: 1rem;
  font-weight: 600;
  color: #2c3e50;
}

.html-content >>> h1 { font-size: 2rem; }
.html-content >>> h2 { font-size: 1.75rem; }
.html-content >>> h3 { font-size: 1.5rem; }
.html-content >>> h4 { font-size: 1.25rem; }

.html-content >>> ul,
.html-content >>> ol {
  margin-left: 1.5rem;
  margin-bottom: 1rem;
}

.html-content >>> ul li {
  list-style-type: disc;
  margin-bottom: 0.5rem;
}

.html-content >>> ol li {
  list-style-type: decimal;
  margin-bottom: 0.5rem;
}

.html-content >>> a {
  color: #1a56db;
  text-decoration: underline;
}

.html-content >>> a:hover {
  color: #1650cf;
}

.html-content >>> img {
  max-width: 100%;
  height: auto;
  border-radius: 8px;
  margin: 1rem 0;
}

.html-content >>> blockquote {
  border-left: 4px solid #1a56db;
  padding-left: 1rem;
  margin: 1rem 0;
  font-style: italic;
  color: #6c757d;
}

.html-content >>> code {
  background: #f8f9fa;
  padding: 2px 6px;
  border-radius: 4px;
  font-family: 'Courier New', monospace;
  font-size: 0.9em;
}

.html-content >>> pre {
  background: #f8f9fa;
  padding: 1rem;
  border-radius: 8px;
  overflow-x: auto;
  margin: 1rem 0;
}

.html-content >>> pre code {
  background: none;
  padding: 0;
}

@media (max-width: 768px) {
  .product-name {
    font-size: 1.5rem;
  }
  
  .current-price {
    font-size: 1.5rem;
  }
  
  .product-actions .btn {
    width: 100%;
  }
  
  .html-content >>> h1 { font-size: 1.5rem; }
  .html-content >>> h2 { font-size: 1.35rem; }
  .html-content >>> h3 { font-size: 1.2rem; }
}
</style>