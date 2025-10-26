<template>
  <div>
    <!-- Header Component -->
    <Header :auth="auth" />
    
    <!-- Main Content -->
    <div class="full-width-banner" style="margin-top: 80px;">
      <img :src="`/storage/banner/banner.jpg`" alt="Banner" class="banner-image" />
    </div>
    <!-- End Banner -->
    <!-- Dịch vụ -->
    <div>
      <div class="text-center my-5">
        <h2 class="fw-bold" style="color: #005EB8;">
          DỊCH VỤ
        </h2>
        <p class="fw-smeibold" style="color: #005EB8; font-size: 18px;margin-top: 20px; font-family: Arial, Helvetica, sans-serif;">
          DỊCH VỤ Y TẾ ĐA DẠNG - NHANH CHÓNG - TẬN TÂM - AN TOÀN - HIỆU QUẢ
        </p>
        <div class="container my-5">
      </div>
      </div>
    </div>
    <!-- Content -->
    <div class="container my-4 py-4">
      <div class="row">
        <!-- Dịch vụ -->
        <div class="col-12">
          <div class="row g-4 product-grid">
            <div
              v-for="service in props.services"
              :key="service.id"
              class="col-lg-3 col-md-4 col-sm-6">
              <div class="product-card-modern">
                <div class="product-img-wrapper" @click="goToServiceDetail(service)">
                  <img
                    :src="service.image ? `/storage/${service.image}` : 'https://via.placeholder.com/150'"
                    class="product-img"
                    :alt="service.ten_dich_vu"
                    @error="handleImageError"
                  />
                </div>
                <div class="product-body">
                  <div class="product-title-modern">{{ service.ten_dich_vu }}</div>
                  <div class="product-price-modern">
                    {{ formatCurrency(service.gia_dich_vu) }}
                  </div>
                  <div class="service-info mb-2">
                    <small class="text-muted">{{ service.ma_dich_vu }}</small>
                  </div>
                  <button class="btn btn-primary product-btn" @click="bookService(service)">
                    Đặt dịch vụ ngay
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Header from './components/Header.vue'
import Footer from './components/Footer.vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  auth: { type: Object, default: () => ({ user: null }) },
  services: { type: Array, default: () => [] }
})

// Format currency
const formatCurrency = (value) => {
  if (!value) return 'N/A'
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(value)
}

// Navigate to service detail
const goToServiceDetail = (service) => {
  router.visit(`/services/${service.id}`)
}

// Book service
const bookService = (service) => {
  // Implement booking functionality
  console.log('Book service:', service)
}


// Handle image error
const handleImageError = (event) => {
  event.target.src = 'https://via.placeholder.com/150'
}
</script>

<style scoped>
.full-width-banner {
  position: relative;
  width: 100vw;
  left: 50%;
  right: 50%;
  margin-left: -50vw;
  margin-right: -50vw;
}

.banner-image {
  width: 100%;
  height: auto;
  object-fit: cover;
  display: block;
}

.product-grid {
  margin-bottom: 0;
  margin-left: -60px;
}

.product-card-modern {
  background: #fff;
  border: 1px solid #e3e6ef;
  border-radius: 14px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  padding: 16px 16px 12px 16px;
  display: flex;
  flex-direction: column;
  align-items: center;
  min-height: 340px;
  position: relative;
  transition: box-shadow 0.2s;
}

.product-card-modern:hover {
  box-shadow: 0 6px 24px rgba(0,0,0,0.12);
  cursor: pointer;
}

.product-img-wrapper {
  width: 100%;
  height: 160px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 10px;
  overflow: hidden; /* Ẩn phần ảnh thừa */
  border-radius: 8px; /* Bo góc container */
}

.product-img {
  width: 100%; /* Chiếm hết chiều rộng */
  height: 100%; /* Chiếm hết chiều cao */
  object-fit: cover; /* Cắt ảnh để vừa khung */
  border-radius: 8px; /* Bo góc ảnh */
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
</style>