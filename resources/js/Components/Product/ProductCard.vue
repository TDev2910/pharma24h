<template>
  <div class="product-card-modern">
    <div class="product-img-wrapper" @click="handleProductClick">
      <img :src="product.image_url || 'https://via.placeholder.com/150'" class="product-img" :alt="productName"
        @error="handleImageError" />
    </div>
    <div class="product-body">
      <div class="product-title-modern">{{ productName }}</div>
      <div class="product-price-modern">
        {{ product.gia_ban_formatted }}
      </div>
      <button class="btn btn-primary product-btn" @click="handleAddToCart">
        Thêm vào giỏ
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
  product: {
    type: Object,
    required: true
  },
  type: {
    type: String,
    required: true,
    validator: (value) => ['medicine', 'goods'].includes(value)
  }
})

// Computed property để lấy tên sản phẩm dựa trên type
const productName = computed(() => {
  return props.type === 'medicine' ? props.product.ten_thuoc : props.product.ten_hang_hoa
})

// Handle product click to navigate to detail page
function handleProductClick() {
  router.visit(`/products/${props.type}/${props.product.id}`)
}

// Handle image error - fallback image
function handleImageError(event) {
  event.target.src = '/images/products/đạt.jpg'
}

// Handle add to cart
async function handleAddToCart() {
  try {
    const response = await axios.post('/cart/add', {
      item_id: props.product.id,
      item_type: props.type,
      quantity: 1
    })

    if (response.data.success) {
      // 1. Cập nhật số lượng giỏ hàng
      window.dispatchEvent(new CustomEvent('cart-updated'))

      // 2. Tự động MỞ dropdown giỏ hàng
      const cartDropdown = document.querySelector('#cartDropdown')
      if (cartDropdown && typeof bootstrap !== 'undefined') {
        const bsDropdown = new bootstrap.Dropdown(cartDropdown)
        bsDropdown.show()
      }

      // 3. Load cart items (gọi function từ cart.js)
      if (typeof window.loadCartItems === 'function') {
        setTimeout(() => window.loadCartItems(), 100)
      }

      // 4. Hiển thị thông báo
      if (typeof window.showNotification === 'function') {
        window.showNotification('Đã thêm vào giỏ hàng!', 'success')
      }
    }
  } catch (e) {
    console.error('Error adding to cart:', e)
    if (typeof window.showNotification === 'function') {
      window.showNotification('Có lỗi xảy ra!', 'error')
    }
  }
}
</script>

<style scoped>
.product-card-modern {
  background: #fff;
  border: 1px solid #e3e6ef;
  border-radius: 14px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  padding: 16px 16px 12px 16px;
  display: flex;
  flex-direction: column;
  align-items: center;
  min-height: 340px;
  position: relative;
  transition: box-shadow 0.2s;
}

.product-card-modern:hover {
  box-shadow: 0 6px 24px rgba(0, 0, 0, 0.12);
  cursor: pointer;
}

.product-img-wrapper {
  width: 100%;
  height: 160px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 10px;
  cursor: pointer;
}

.product-img {
  max-width: 100%;
  max-height: 140px;
  object-fit: contain;
  border-radius: 8px;
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
  line-clamp: 2;
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

/* Override Bootstrap hover effect - giữ nguyên màu khi hover */
.product-btn:hover,
.btn-primary.product-btn:hover,
.product-btn:focus,
.btn-primary.product-btn:focus,
.product-btn:active,
.btn-primary.product-btn:active {
  background: #1a56db !important;
  border-color: transparent !important;
  color: #fff !important;
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

