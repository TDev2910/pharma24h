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
            <span class="badge bg-primary mb-2">{{ type === 'medicine' ? 'Thuốc' : 'Hàng hóa' }}</span>
            <h1 class="product-name">{{ product.ten_thuoc || product.ten_hang_hoa }}</h1>
            
            <div class="product-price mb-4">
              <span class="current-price">{{ formatCurrency(product.gia_ban) }}</span>
              <span class="unit">/{{ product.don_vi_tinh || 'Đơn vị' }}</span>
            </div>
            
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
            <div class="description-content" v-if="type === 'medicine'">
              <p v-if="product.hoat_chat"><strong>Hoạt chất:</strong> {{ product.hoat_chat }}</p>
              <p v-if="product.ham_luong"><strong>Hàm lượng:</strong> {{ product.ham_luong }}</p>
              <p v-if="product.quy_cach_dong_goi"><strong>Quy cách đóng gói:</strong> {{ product.quy_cach_dong_goi }}</p>
            </div>
            <div class="description-content" v-else>
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

const props = defineProps({
  auth: { type: Object, default: () => ({ user: null }) },
  product: { type: Object, required: true },
  type: { type: String, required: true }
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
}
</style>