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
            <h6>Thông tin sản phẩm</h6>
            <hr>
            <div class="product-meta mb-4">
              <div class="meta-item">
                <strong>Đơn vị tính:</strong> {{ product.don_vi_tinh || 'N/A' }}
              </div>
              <div class="meta-item">
                <strong>Mã hàng:</strong> {{ product.ma_hang || 'N/A' }}
              </div>
              <div class="meta-item" v-if="product.category">
                <strong>Danh mục sản phẩm :</strong> {{ product.category.name }}
              </div>
              <div class="meta-item" v-if="type === 'medicine' && product.category">
                <strong>Số đăng ký thuốc :</strong> {{ product.so_dang_ky || 'N/A' }}
              </div>
              <a v-if="type === 'medicine'" href="https://dichvucong.dav.gov.vn/congbothuoc/index" target="_blank" class="registry-link">
                <h7>Tra cứu số đăng ký thuốc được cấp phép tại đây</h7>
              </a>
              <div class="meta-item" v-if="product.manufacturer">
                <strong>Quy cách đóng gói:</strong> {{ product.quy_cach_dong_goi }}
              </div>
              <div class="meta-item" v-if="product.manufacturer">
                <strong>Nhà sản xuất:</strong> {{ product.manufacturer.name }}
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
    
    <!-- Reviews Section -->
    <div class="container py-5" v-if="reviews">
      <div class="reviews-section">
        <h3 class="section-title">
          Đánh giá sản phẩm <span class="review-count-badge">({{ reviewCount }} đánh giá)</span>
        </h3>
        
        <!-- Rating Summary -->
        <div class="rating-summary-new" v-if="reviewCount > 0">
          <div class="rating-left">
            <div class="rating-label">Trung bình</div>
            <div class="avg-rating-large">{{ averageRating.toFixed(1) }} <span class="star-icon">★</span></div>
            <button class="btn btn-primary btn-write-review" @click="scrollToReviewForm">
              Gửi đánh giá
            </button>
          </div>
          
          <div class="rating-right">
            <div v-for="star in [5, 4, 3, 2, 1]" :key="star" class="rating-bar-row">
              <div class="star-label">
                <span v-for="i in star" :key="i" class="star-small-icon">★</span>
              </div>
              <div class="progress-bar-container">
                <div 
                  class="progress-bar-fill" 
                  :style="{ width: reviewCount > 0 ? (ratingBreakdown[star] / reviewCount * 100) + '%' : '0%' }"
                ></div>
              </div>
              <div class="rating-count">{{ ratingBreakdown[star] }}</div>
            </div>
          </div>
        </div>
        
        <!-- Review Form -->
        <div v-if="auth.user" class="review-form-card">
          <h4 class="form-title">Viết đánh giá của bạn</h4>
          <form @submit.prevent="submitReview">
            <!-- Rating Stars Input -->
            <div class="form-group">
              <label>Đánh giá của bạn <span class="required">*</span></label>
              <div class="star-rating-input">
                <span 
                  v-for="star in 5" 
                  :key="star"
                  @click="reviewForm.rating = star"
                  @mouseover="hoverRating = star"
                  @mouseleave="hoverRating = 0"
                  class="star-input"
                  :class="{ 'filled': star <= (hoverRating || reviewForm.rating) }"
                >
                  ★
                </span>
              </div>
            </div>
            
            <!-- Comment Textarea -->
            <div class="form-group">
              <label>Nhận xét của bạn <span class="required">*</span></label>
              <textarea 
                v-model="reviewForm.comment"
                class="form-control"
                rows="4"
                placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm..."
                minlength="10"
                maxlength="500"
              ></textarea>
              <small class="text-muted">{{ reviewForm.comment?.length || 0 }}/500 ký tự</small>
            </div>
            
            <!-- Submit Button -->
            <button 
              type="submit" 
              class="btn btn-primary btn-submit-review"
              :disabled="!reviewForm.rating || !reviewForm.comment || reviewForm.comment.length < 10"
            >
              <i class="fas fa-paper-plane me-2"></i>Gửi đánh giá
            </button>
          </form>
        </div>
        
        <!-- Login Prompt -->
        <div v-else class="login-prompt-card">
          <i class="fas fa-user-lock"></i>
          <p>Vui lòng <a href="/login">đăng nhập</a> để viết đánh giá sản phẩm</p>
        </div>
        
        <!-- Reviews List -->
        <div class="reviews-list" v-if="reviews.length > 0">
          <h4 class="reviews-list-title">Tất cả đánh giá ({{ reviewCount }})</h4>
          
          <div v-for="review in reviews" :key="review.id" class="review-item">
            <div class="review-header">
              <div class="reviewer-info">
                <div class="reviewer-avatar">
                  <i class="fas fa-user"></i>
                </div>
                <div class="reviewer-details">
                  <strong class="reviewer-name">{{ review.user.name }}</strong>
                  <div class="review-stars">
                    <span v-for="i in 5" :key="i" class="star-small" :class="{ filled: i <= review.rating }">★</span>
                  </div>
                </div>
              </div>
              <div class="review-date">
                <small>{{ formatReviewDate(review.created_at) }}</small>
              </div>
            </div>
            
            <p class="review-comment" v-if="review.comment">{{ review.comment }}</p>
            
            <!-- Delete button (nếu là owner) -->
            <button 
              v-if="auth.user && auth.user.id === review.user_id"
              @click="deleteReview(review.id)"
              class="btn btn-sm btn-danger btn-delete-review"
            >
              <i class="fas fa-trash me-1"></i>Xóa
            </button>
          </div>
        </div>
        
        <!-- Empty state -->
        <div v-else class="empty-reviews">
          <i class="fas fa-comments"></i>
          <p>Chưa có đánh giá nào. Hãy là người đầu tiên đánh giá sản phẩm này!</p>
          </div>
        </div>
      </div>     
    </div>
  </template> 

<script setup>
import Header from './components/Header.vue'
import Footer from './components/Footer.vue'
import axios from 'axios'
import { computed, ref } from 'vue' 
import DOMPurify from 'dompurify' 
  
const props = defineProps({
  auth: { type: Object, default: () => ({ user: null }) },
  product: { type: Object, required: true },
  type: { type: String, required: true },
  reviews: { type: Array, default: () => [] },
  averageRating: { type: Number, default: 0 },
  reviewCount: { type: Number, default: 0 },
  ratingBreakdown: { type: Object, default: () => ({ 5: 0, 4: 0, 3: 0, 2: 0, 1: 0 }) }
})

// Review form data
const reviewForm = ref({
  rating: 0,
  comment: ''
})

const hoverRating = ref(0)

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

// Submit review
const submitReview = async () => {
  if (!reviewForm.value.rating || !reviewForm.value.comment) {
    if (typeof window.showNotification === 'function') {
      window.showNotification('Vui lòng chọn số sao và viết nhận2 xét!', 'error');
    }
    return;
  }

  try {
    const response = await axios.post('/reviews', {
      product_id: props.product.id,
      product_type: props.type,
      rating: reviewForm.value.rating,
      comment: reviewForm.value.comment
    });

    if (response.data.success) {
      // Reset form
      reviewForm.value.rating = 0;
      reviewForm.value.comment = '';
      
      // Reload page để hiển thị review mới
      window.location.reload();
      
      if (typeof window.showNotification === 'function') {
        window.showNotification('Đánh giá của bạn đã được gửi!', 'success');
      }
    }
  } catch (error) {
    console.error('Error submitting review:', error);
    if (typeof window.showNotification === 'function') {
      window.showNotification('Có lỗi xảy ra khi gửi đánh giá!', 'error');
    }
  }
}

// Delete review
const deleteReview = async (reviewId) => {
  if (!confirm('Bạn có chắc chắn muốn xóa đánh giá này?')) {
    return;
  }

  try {
    const response = await axios.delete(`/reviews/${reviewId}`);

    if (response.data.success) {
      // Reload page
      window.location.reload();
      
      if (typeof window.showNotification === 'function') {
        window.showNotification('Đánh giá đã được xóa!', 'success');
      }
    }
  } catch (error) {
    console.error('Error deleting review:', error);
    if (typeof window.showNotification === 'function') {
      window.showNotification('Có lỗi xảy ra khi xóa đánh giá!', 'error');
    }
  }
}

// Format review date
const formatReviewDate = (dateString) => {
  const date = new Date(dateString);
  const now = new Date();
  const diffMs = now - date;
  const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

  if (diffDays === 0) return 'Hôm nay';
  if (diffDays === 1) return 'Hôm qua';
  if (diffDays < 7) return `${diffDays} ngày trước`;
  if (diffDays < 30) return `${Math.floor(diffDays / 7)} tuần trước`;
  
  return date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

// Scroll to review form
const scrollToReviewForm = () => {
  const reviewFormCard = document.querySelector('.review-form-card') || document.querySelector('.login-prompt-card');
  if (reviewFormCard) {
    reviewFormCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
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

/* ✅ Style cho link tra cứu số đăng ký thuốc */
.registry-link {
  display: inline-block;
  margin: 12px 0;
  padding: 8px 12px;
  background: #e3f2fd;
  border-radius: 4px;
  text-decoration: none;
  transition: all 0.2s ease;
}

.registry-link:hover {
  background: #bbdefb;
}

.registry-link h7 {
  margin: 0;
  font-size: 0.9rem;
  color: #1a56db;
  font-weight: 500;
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

.html-content :deep(p) {
  margin-bottom: 1rem;
}

.html-content :deep(strong),
.html-content :deep(b) {
  font-weight: 600;
  color: #2c3e50;
}

.html-content :deep(em),
.html-content :deep(i) {
  font-style: italic;
}

.html-content :deep(u) {
  text-decoration: underline;
}

.html-content :deep(h1),
.html-content :deep(h2),
.html-content :deep(h3),
.html-content :deep(h4),
.html-content :deep(h5),
.html-content :deep(h6) {
  margin-top: 1.5rem;
  margin-bottom: 1rem;
  font-weight: 600;
  color: #2c3e50;
}

.html-content :deep(h1) { font-size: 2rem; }
.html-content :deep(h2) { font-size: 1.75rem; }
.html-content :deep(h3) { font-size: 1.5rem; }
.html-content :deep(h4) { font-size: 1.25rem; }

.html-content :deep(ul),
.html-content :deep(ol) {
  margin-left: 1.5rem;
  margin-bottom: 1rem;
}

.html-content :deep(ul li) {
  list-style-type: disc;
  margin-bottom: 0.5rem;
}

.html-content :deep(ol li) {
  list-style-type: decimal;
  margin-bottom: 0.5rem;
}

.html-content :deep(a) {
  color: #1a56db;
  text-decoration: underline;
}

.html-content :deep(a:hover) {
  color: #1650cf;
}

.html-content :deep(img) {
  max-width: 100%;
  height: auto;
  border-radius: 8px;
  margin: 1rem 0;
}

.html-content :deep(blockquote) {
  border-left: 4px solid #1a56db;
  padding-left: 1rem;
  margin: 1rem 0;
  font-style: italic;
  color: #6c757d;
}

.html-content :deep(code) {
  background: #f8f9fa;
  padding: 2px 6px;
  border-radius: 4px;
  font-family: 'Courier New', monospace;
  font-size: 0.9em;
}

.html-content :deep(pre) {
  background: #f8f9fa;
  padding: 1rem;
  border-radius: 8px;
  overflow-x: auto;
  margin: 1rem 0;
}

.html-content :deep(pre code) {
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
  
  .html-content :deep(h1) { font-size: 1.5rem; }
  .html-content :deep(h2) { font-size: 1.35rem; }
  .html-content :deep(h3) { font-size: 1.2rem; }
}

/* ==================== REVIEWS SECTION ==================== */
.reviews-section {
  background: white;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.section-title {
  font-size: 1.75rem;
  font-weight: 700;
  color: #2c3e50;
  margin-bottom: 1.5rem;
}

.review-count-badge {
  font-size: 1rem;
  font-weight: 400;
  color: #6c757d;
  margin-left: 0.5rem;
}

/* Rating Summary - New Design */
.rating-summary-new {
  display: grid;
  grid-template-columns: 200px 1fr;
  gap: 3rem;
  background: white;
  border: 1px solid #e9ecef;
  border-radius: 12px;
  padding: 2rem;
  margin-bottom: 2rem;
}

.rating-left {
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  border-right: 1px solid #e9ecef;
  padding-right: 2rem;
}

.rating-label {
  font-size: 0.875rem;
  color: #6c757d;
  margin-bottom: 0.5rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.avg-rating-large {
  font-size: 3.5rem;
  font-weight: 700;
  color: #2c3e50;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.star-icon {
  color: #ffc107;
  font-size: 2.5rem;
}

.btn-write-review {
  background: #4169e1;
  border: none;
  padding: 0.5rem 1.5rem;
  border-radius: 6px;
  font-size: 0.9rem;
  font-weight: 600;
  white-space: nowrap;
  transition: all 0.3s;
}

.btn-write-review:hover {
  background: #3456c4;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(65, 105, 225, 0.3);
}

.rating-right {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  justify-content: center;
}

.rating-bar-row {
  display: grid;
  grid-template-columns: 100px 1fr 50px;
  gap: 1rem;
  align-items: center;
}

.star-label {
  display: flex;
  gap: 2px;
}

.star-small-icon {
  font-size: 1rem;
  color: #ffc107;
}

.progress-bar-container {
  height: 8px;
  background: #e9ecef;
  border-radius: 4px;
  overflow: hidden;
}

.progress-bar-fill {
  height: 100%;
  background: linear-gradient(90deg, #ffc107 0%, #ff9800 100%);
  border-radius: 4px;
  transition: width 0.3s ease;
}

.rating-count {
  text-align: right;
  font-weight: 600;
  color: #495057;
  font-size: 0.95rem;
}

/* Review Form */
.review-form-card {
  background: #f8f9fa;
  border-radius: 12px;
  padding: 2rem;
  margin-bottom: 2rem;
  border: 2px dashed #dee2e6;
}

.form-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 1.5rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  font-weight: 600;
  color: #495057;
  margin-bottom: 0.5rem;
}

.required {
  color: #dc3545;
}

/* Star Rating Input */
.star-rating-input {
  display: flex;
  gap: 5px;
}

.star-input {
  font-size: 2.5rem;
  color: #dee2e6;
  cursor: pointer;
  transition: all 0.2s;
  user-select: none;
}

.star-input:hover {
  transform: scale(1.1);
}

.star-input.filled {
  color: #ffc107;
}

/* Submit Button */
.btn-submit-review {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
  padding: 0.75rem 2rem;
  font-weight: 600;
  transition: all 0.3s;
}

.btn-submit-review:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-submit-review:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Login Prompt */
.login-prompt-card {
  background: #fff3cd;
  border: 2px solid #ffc107;
  border-radius: 12px;
  padding: 2rem;
  text-align: center;
  margin-bottom: 2rem;
}

.login-prompt-card i {
  font-size: 3rem;
  color: #ffc107;
  margin-bottom: 1rem;
  display: block;
}

.login-prompt-card p {
  font-size: 1.1rem;
  color: #856404;
  margin: 0;
}

.login-prompt-card a {
  color: #667eea;
  font-weight: 600;
  text-decoration: none;
}

.login-prompt-card a:hover {
  text-decoration: underline;
}

/* Reviews List */
.reviews-list {
  margin-top: 2rem;
}

.reviews-list-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 1.5rem;
  padding-bottom: 0.75rem;
  border-bottom: 2px solid #e9ecef;
}

.review-item {
  background: white;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  padding: 1.5rem;
  margin-bottom: 1rem;
  transition: all 0.3s;
}

.review-item:hover {
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  border-color: #667eea;
}

.review-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.reviewer-info {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.reviewer-avatar {
  width: 50px;
  height: 50px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
  flex-shrink: 0;
}

.reviewer-details {
  flex: 1;
}

.reviewer-name {
  display: block;
  font-size: 1rem;
  color: #2c3e50;
  margin-bottom: 0.25rem;
}

.review-stars {
  display: flex;
  gap: 2px;
}

.star-small {
  font-size: 1rem;
  color: #dee2e6;
}

.star-small.filled {
  color: #ffc107;
}

.review-date {
  color: #6c757d;
  font-size: 0.875rem;
}

.review-comment {
  color: #495057;
  line-height: 1.6;
  margin: 0;
  padding-left: 66px;
}

.btn-delete-review {
  margin-top: 1rem;
  margin-left: 66px;
  font-size: 0.875rem;
  padding: 0.375rem 0.75rem;
}

/* Empty State */
.empty-reviews {
  text-align: center;
  padding: 3rem 1rem;
  color: #6c757d;
}

.empty-reviews i {
  font-size: 4rem;
  color: #dee2e6;
  margin-bottom: 1rem;
  display: block;
}

.empty-reviews p {
  font-size: 1.1rem;
  margin: 0;
}

/* Responsive */
@media (max-width: 768px) {
  .reviews-section {
    padding: 1.5rem;
  }
  
  .rating-summary-new {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
  
  .rating-left {
    border-right: none;
    border-bottom: 1px solid #e9ecef;
    padding-right: 0;
    padding-bottom: 1.5rem;
  }
  
  .avg-rating-large {
    font-size: 2.5rem;
  }
  
  .star-icon {
    font-size: 2rem;
  }
  
  .rating-bar-row {
    grid-template-columns: 80px 1fr 40px;
    gap: 0.5rem;
  }
  
  .review-form-card {
    padding: 1.5rem;
  }
  
  .review-comment {
    padding-left: 0;
  }
  
  .btn-delete-review {
    margin-left: 0;
  }
  
  .reviewer-info {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>