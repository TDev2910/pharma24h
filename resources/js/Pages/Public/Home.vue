<template>
  <div>
    <!-- Banner Carousel -->
    <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000" style="margin-top: 0;">
      <div class="carousel-indicators">
        <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
      </div>
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img src="https://cdn.nhathuocbewell.com/images/eyJfcmFpbHMiOnsibWVzc2FnZSI6IkJBaHBBcS9pIiwiZXhwIjpudWxsLCJwdXIiOiJibG9iX2lkIn19--918088de75b0611a6f53fdca34cb6dea5552095e/1000x0/filters:quality(90)/Banner-web_Hero-Chinh_3708x1240px.jpg" class="d-block w-100 banner-image" alt="Banner 1" />
        </div>
        <div class="carousel-item">
          <img src="https://inhopgiaygiare.vn/wp-content/uploads/2024/12/banner-Trai-Nghiem.jpg" class="d-block w-100 banner-image" alt="Banner 2" />
        </div>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>

    <!-- Introduction -->
    <div class="container mt-4 mt-md-5" id="section_introduction3" data-section-name="Introduction3" data-section-active="true">
      <div class="row align-items-center">
        <div class="col-lg-6 col-md-12 mb-4 mb-lg-0">
          <div class="MuiBox-root css-11mgf69">
            <div class="MuiBox-root css-11icu4z">
              <h1 class="hero-title mb-3">Dược sĩ tận tâm, tư vấn chuyên nghiệp</h1>
              <p class="hero-description">Đội ngũ Dược sĩ được đào tạo bài bản, có chuyên môn cao và giàu kinh nghiệm. Luôn đặt lợi ích khách hàng lên hàng đầu, sẵn sàng tư vấn, giải đáp thắc mắc và hướng dẫn sử dụng sản phẩm hiệu quả, an toàn.</p>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-12">
          <div class="MuiBox-root css-1g29oey text-center text-lg-end">
            <img loading="lazy" alt="Introduction" src="https://cdn.kiotvietweb.vn/page_builder_default_config/pharmacy/theme_2/homepage/introduction/picture_1.webp" class="hero-image" style="max-width: 100%; height: auto; border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.1);"/>
          </div>
        </div>
      </div>
    </div>
    <!-- Hiển thị sản phẩm -->
    <!-- SẢN PHẨM MỚI NHẤT: Medicines -->
    <div class="container mt-5" v-if="medicines && medicines.length">
      <div class="row mb-4">
        <div class="col-12">
          <h4 class="product-section-title">Thuốc mới nhất</h4>
        </div>
      </div>
      <div class="row g-4 mb-5">
        <div class="col-6 col-md-6 col-lg-3" v-for="m in medicines" :key="`m-` + m.id">
          <div class="product-card h-100 d-flex flex-column">
            <div class="product-image-wrapper">
              <img :src="m.image_url" :alt="m.ten_thuoc" class="product-image" @error="handleImageError" />
              <div class="product-badge">
                <span class="badge-text medicine-badge">Thuốc</span>
              </div>
              <div class="product-overlay">
                <button class="btn-quick-view"><i class="fas fa-eye"></i></button>
                <button class="btn-add-cart" @click="addToCart({ id: m.id, type: 'medicine' })"><i class="fas fa-shopping-cart"></i></button>
              </div>
            </div>
            <div class="product-info d-flex flex-column flex-grow-1">
              <div class="product-category">{{ m.category?.name ?? 'Thuốc' }}</div>
              <h5 class="product-name">{{ m.ten_thuoc }}</h5>
              <div class="product-manufacturer"><i class="fas fa-building"></i> {{ m.manufacturer?.name ?? 'Chưa rõ' }}</div>
              <div class="product-pricing">
                <span class="current-price">{{ m.gia_ban_formatted ?? '0 VND' }}</span>
              </div>
              <div class="product-actions mt-auto">
                <button class="btn-primary-action" @click="addToCart({ id: m.id, type: 'medicine' })">
                  <i class="fas fa-cart-plus me-1"></i> Thêm vào giỏ
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- SẢN PHẨM MỚI NHẤT: Goods -->
    <div class="container mt-4" v-if="goods && goods.length">
      <div class="row mb-4">
        <div class="col-12">
          <h4 class="product-section-title">Hàng hóa mới nhất</h4>
        </div>
      </div>
      <div class="row g-4">
        <div class="col-6 col-md-6 col-lg-3" v-for="g in goods" :key="`g-` + g.id">
          <div class="product-card h-100 d-flex flex-column">
            <div class="product-image-wrapper">
              <img :src="g.image_url" :alt="g.ten_hang_hoa" class="product-image" @error="handleImageError" />
              <div class="product-badge">
                <span class="badge-text goods-badge">Hàng hóa</span>
              </div>
              <div class="product-overlay">
                <button class="btn-quick-view"><i class="fas fa-eye"></i></button>
                <button class="btn-add-cart" @click="addToCart({ id: g.id, type: 'goods' })"><i class="fas fa-shopping-cart"></i></button>
              </div>
            </div>
            <div class="product-info d-flex flex-column flex-grow-1">
              <div class="product-category">{{ g.category?.name ?? 'Hàng hóa' }}</div>
              <h5 class="product-name">{{ g.ten_hang_hoa }}</h5>
              <div class="product-manufacturer"><i class="fas fa-building"></i> {{ g.manufacturer?.name ?? 'Chưa rõ' }}</div>
              <div class="product-pricing">
                <span class="current-price">{{ g.gia_ban_formatted ?? '0 VND' }}</span>
              </div>
              <div class="product-actions mt-auto">
                <button class="btn-primary-action" @click="addToCart({ id: g.id, type: 'goods' })">
                  <i class="fas fa-cart-plus me-1"></i> Thêm vào giỏ
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
  </div>
</template>

<script setup>
import axios from 'axios'

const props = defineProps({
  medicines: { type: Array, default: () => [] },
  goods: { type: Array, default: () => [] },
});

async function addToCart({ id, type }) {
  try {
    await axios.post('/cart/add', { item_id: id, item_type: type, quantity: 1 });
    
    // Trigger cart update event
    window.dispatchEvent(new CustomEvent('cart-updated'));
    
    // TODO: toast/flash sau
  } catch (e) { 
    // ignore for now
  }
}

function handleImageError(event) {
  // Fallback image
  event.target.src = '/images/products/đạt.jpg';
}
</script>
