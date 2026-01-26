<template>
  <div>
    <!-- Banner Section -->
    <BannerSection />

    <!-- Introduction Section -->
    <IntroSection />

    <!-- Feature Icons Section -->
    <FeatureIcons />

    <!-- Hiển thị sản phẩm -->
    <!-- SẢN PHẨM MỚI NHẤT: Medicines -->
    <div class="container mt-5" v-if="medicines && medicines.length">
      <div class="row mb-4">
        <div class="col-12">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="product-section-title mb-0">Thuốc nổi bật</h4>
            <Link href="/products" class="text-primary text-decoration-none" style="font-size: 18px;">Xem thêm</Link>
          </div>
        </div>
      </div>
      <div class="row g-3 product-grid mb-5">
        <div class="col-lg-3 col-md-4 col-6" v-for="m in medicines" :key="`m-${m.id}`">
          <ProductCard :product="m" type="medicine" class="h-100" />
        </div>
      </div>
    </div>

    <!-- SẢN PHẨM MỚI NHẤT: Goods -->
    <div class="container mt-4" v-if="goods && goods.length">
      <div class="row mb-4">
        <div class="col-12">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="product-section-title mb-0">Vật tư y tế nổi bật</h4>
            <Link href="/products" class="text-primary text-decoration-none" style="font-size: 18px;">Xem thêm</Link>
          </div>
        </div>
      </div>
      <div class="row g-3 product-grid">
        <div class="col-lg-3 col-md-4 col-6" v-for="g in goods" :key="`g-${g.id}`">
          <ProductCard :product="g" type="goods" class="h-100" />
        </div>
      </div>
    </div>

    <!-- Services Section -->
    <ServiceSection />
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

// Import components
import BannerSection from '@/Components/Home/BannerSection.vue'
import IntroSection from '@/Components/Home/IntroSection.vue'
import FeatureIcons from '@/Components/Home/FeatureIcons.vue'
import ServiceSection from '@/Components/Home/ServiceSection.vue'
import ProductCard from '@/Components/Product/ProductCard.vue'

const props = defineProps({
  medicines: { type: Array, default: () => [] },
  goods: { type: Array, default: () => [] },
  auth: { type: Object, default: () => ({ user: null }) }
})
</script>

<style scoped>
.product-grid {
  margin-bottom: 0;
}

.product-section-title {
  font-weight: 700;
  color: #1a4f6e;
}

/* --- PHẦN CODE MỚI THÊM ĐỂ FIX LỖI RESPONSIVE --- */

/* 1. Thiết lập thẻ Card luôn cao bằng nhau */
:deep(.product-card),
:deep(.product-card-modern),
:deep(.card) {
  height: 100%;       /* Bắt buộc chiều cao full */
  display: flex;
  flex-direction: column;
}

/* 2. Đẩy nút bấm xuống đáy thẻ */
:deep(.product-body),
:deep(.card-body) {
  flex: 1;            /* Chiếm phần không gian còn thừa */
  display: flex;
  flex-direction: column;
}

:deep(.product-btn),
:deep(.btn-add-cart) {
  margin-top: auto;   /* Đẩy nút xuống dưới cùng */
}

/* 3. Giới hạn tên sản phẩm chỉ hiển thị 2 dòng (Thêm dấu ...) */
:deep(.product-title),
:deep(.product-title-modern),
:deep(h5) { /* Hoặc thẻ chứa tên sản phẩm của bạn */
  display: -webkit-box;
  -webkit-line-clamp: 2; /* Số dòng muốn hiển thị */
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  min-height: 48px; /* Chiều cao cố định tương ứng 2 dòng (tùy font-size) */
  font-size: 15px;  /* Chỉnh font nhỏ lại chút cho mobile */
  line-height: 1.5;
}

/* 4. Tùy chỉnh riêng cho Mobile */
@media (max-width: 767px) {
  :deep(.product-card) {
    padding: 10px; /* Giảm padding thẻ cho gọn */
  }

  :deep(.product-img-wrapper),
  :deep(.card-img-top) {
    height: 140px; /* Cố định chiều cao ảnh trên mobile */
    object-fit: contain;
  }

  :deep(.product-title) {
    font-size: 14px; /* Chữ nhỏ hơn trên mobile */
  }

  :deep(.product-price) {
    font-size: 15px; /* Giá tiền nổi bật */
    font-weight: bold;
  }
}
</style>
