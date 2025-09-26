<template>
  <div class="container">
    <div class="row">
      <div class="col-12">
        <h2 class="mb-4">Sản phẩm</h2>
        <p>Trang sản phẩm đang được phát triển...</p>
        
        <!-- Hiển thị sản phẩm nếu có data -->
        <div v-if="medicines && medicines.length" class="mt-4">
          <h4>Thuốc</h4>
          <div class="row g-4">
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
        
        <div v-if="goods && goods.length" class="mt-4">
          <h4>Hàng hóa</h4>
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
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'

defineProps({
  medicines: { type: Array, default: () => [] },
  goods: { type: Array, default: () => [] },
  allProducts: { type: Array, default: () => [] },
});

async function addToCart({ id, type }) {
  try {
    await axios.post('/cart/add', { item_id: id, item_type: type, quantity: 1 });
    window.dispatchEvent(new CustomEvent('cart-updated'));
  } 
  catch (e) { 
  }
}

function handleImageError(event) {
  // Fallback image
  event.target.src = '/images/products/đạt.jpg';
}
</script>
