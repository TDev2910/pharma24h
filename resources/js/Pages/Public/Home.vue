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
      <div class="row g-4 product-grid mb-5">
        <div class="col-lg-3 col-md-4 col-sm-6" v-for="m in medicines" :key="`m-${m.id}`">
          <ProductCard :product="m" type="medicine" />
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
      <div class="row g-4 product-grid">
        <div class="col-lg-3 col-md-4 col-sm-6" v-for="g in goods" :key="`g-${g.id}`">
          <ProductCard :product="g" type="goods" />
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
</style>
