<template>
    <div>
        <!-- Product Detail Content -->
        <div class="container py-5">
            <div class="row">
                <!-- Product Image -->
                <div class="col-md-5">
                    <div class="product-image-container">
                        <img :src="product.image ? `/storage/${product.image}` : 'https://via.placeholder.com/400'"
                            :alt="product.ten_thuoc || product.ten_hang_hoa" class="product-detail-image" />
                    </div>
                </div>

                <!-- Product Info -->
                <div class="col-md-7">
                    <div class="product-detail-info">
                        <h1 class="product-name" style="font-style:bold">{{ product.ten_thuoc || product.ten_hang_hoa }}
                        </h1>

                        <div class="product-price mb-4">
                            <template v-if="isPromotionActive">
                                <span class="current-price text-danger">{{ formatCurrency(product.gia_khuyen_mai)
                                    }}</span>
                                <span class="badge bg-warning text-dark ms-2">KM</span>
                                <span class="original-price ms-3"
                                    style="text-decoration:line-through; color:#999; font-size:1rem;">
                                    {{ formatCurrency(product.gia_ban) }}
                                </span>
                            </template>
                            <template v-else>
                                <span class="current-price">{{ formatCurrency(product.gia_ban) }}</span>
                            </template>
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
                            <a v-if="type === 'medicine'" href="https://dichvucong.dav.gov.vn/congbothuoc/index"
                                target="_blank" class="registry-link">
                                <h7>Tra cứu số đăng ký thuốc được cấp phép tại đây</h7>
                            </a>
                            <div class="meta-item" v-if="product.manufacturer">
                                <strong>Quy cách đóng gói:</strong> {{ product.quy_cach_dong_goi }}
                            </div>
                            <div class="meta-item" v-if="product.manufacturer">
                                <strong>Nhà sản xuất:</strong> {{ product.manufacturer.name }}
                            </div>
                            <div class="meta-item" v-if="type === 'medicine' && product.drugRoute">
                                <strong>Đường dùng:</strong> {{ product.drugRoute.name }}
                            </div>
                        </div>

                        <!-- Add to Cart -->
                        <div class="product-actions">
                            <button class="btn btn-primary btn-lg" @click="addToCartHandler"
                                :disabled="isButtonDisabled"
                                :class="{ 'btn-secondary': isOutOfStock, 'btn-success': isPromotionActive }">
                                <i class="fas fa-cart-plus me-2"></i>
                                {{ buttonLabel }}
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
                            <p v-if="product.quy_cach_dong_goi"><strong>Quy cách đóng gói:</strong> {{
                                product.quy_cach_dong_goi }}
                            </p>
                        </div>
                        <div class="description-content mt-4" v-else>
                            <p v-if="product.quy_cach_dong_goi"><strong>Quy cách đóng gói:</strong> {{
                                product.quy_cach_dong_goi }}
                            </p>
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
                        <div class="avg-rating-large">{{ averageRating.toFixed(1) }} <span class="star-icon">★</span>
                        </div>
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
                                <div class="progress-bar-fill"
                                    :style="{ width: reviewCount > 0 ? (ratingBreakdown[star] / reviewCount * 100) + '%' : '0%' }">
                                </div>
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
                                <span v-for="star in 5" :key="star" @click="reviewForm.rating = star"
                                    @mouseover="hoverRating = star" @mouseleave="hoverRating = 0" class="star-input"
                                    :class="{ 'filled': star <= (hoverRating || reviewForm.rating) }">
                                    ★
                                </span>
                            </div>
                        </div>

                        <!-- Comment Textarea -->
                        <div class="form-group">
                            <label>Nhận xét của bạn <span class="required">*</span></label>
                            <textarea v-model="reviewForm.comment" class="form-control" rows="4"
                                placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm..." minlength="10"
                                maxlength="500"></textarea>
                            <small class="text-muted">{{ reviewForm.comment?.length || 0 }}/500 ký tự</small>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary btn-submit-review"
                            :disabled="!reviewForm.rating || !reviewForm.comment || reviewForm.comment.length < 10">
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
                                        <span v-for="i in 5" :key="i" class="star-small"
                                            :class="{ filled: i <= review.rating }">★</span>
                                    </div>
                                </div>
                            </div>
                            <div class="review-date">
                                <small>{{ formatReviewDate(review.created_at) }}</small>
                            </div>
                        </div>

                        <p class="review-comment" v-if="review.comment">{{ review.comment }}</p>

                        <!-- Delete button (nếu là owner) -->
                        <button v-if="auth.user && auth.user.id === review.user_id" @click="deleteReview(review.id)"
                            class="btn btn-sm btn-danger btn-delete-review">
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

// định dạng tiền tệ
function formatCurrency(amount) {
    if (!amount) return '0 VNĐ'
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount)
}

const isPromotionActive = computed(() => {
    return props.product.ton_khuyen_mai > 0 && props.product.gia_khuyen_mai > 0;
});
const isOutOfStock = computed(() => {
    return props.product.ton_kho == 0;
});
const displayPrice = computed(() => {
    return isPromotionActive.value ? props.product.gia_khuyen_mai : props.product.gia_ban;
});
const isButtonDisabled = computed(() => {
    if (isOutOfStock.value) return true;
    if (isPromotionActive.value && props.product.ton_khuyen_mai == 0) return true;
    return false;
});
const buttonLabel = computed(() => {
    if (isOutOfStock.value) return 'Hết hàng';
    if (isPromotionActive.value) return 'Mua với giá khuyến mãi';
    return 'Thêm vào giỏ hàng';
});

// Xử lý hàm thêm vào giỏ hàng
async function addToCartHandler() {
    try {
        const response = await axios.post('/cart/add', {
            item_id: props.product.id,
            item_type: props.type,
            quantity: 1,
            is_promotion: isPromotionActive.value
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

// gửi đánh giá
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

// xóa đánh giá
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

// định dạng ngày đánh giá
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

// scroll to review form
const scrollToReviewForm = () => {
    const reviewFormCard = document.querySelector('.review-form-card') || document.querySelector('.login-prompt-card');
    if (reviewFormCard) {
        reviewFormCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}
</script>

<style scoped src="../../../../css/Public/Products/show/show.css"></style>
