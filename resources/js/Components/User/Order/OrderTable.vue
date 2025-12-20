<template>
  <div class="orders-list" v-if="orders && orders.length > 0">
    <div v-for="order in orders" :key="order.id" class="order-card" @click="$emit('order-click', order.id)">
      <!-- Order Header -->
      <div class="order-header">
        <span class="order-type-badge">Mua tại nhà thuốc</span>
        <span class="order-created-at">{{ formatDateTime(order.created_at) }}</span>
      </div>

      <!-- Pharmacy Info -->
      <div class="pharmacy-info" v-if="order.pickup_location">
        Nhà thuốc: {{ order.pickup_location }}
      </div>

      <!-- Product Display -->
      <div class="product-section">
        <div v-for="(item, index) in getDisplayItems(order)" :key="item.id || index" class="product-item">
          <div class="product-image">
            <img :src="getImageUrl(item)" :alt="item.product_name || item.name" @error="handleImageError" />
          </div>
          <div class="product-details">
            <div class="product-name">
              {{ item.product_name || item.name }}
            </div>
            <div class="product-category">Phân loại: Viên</div>
          </div>
          <div class="product-price">
            {{ formatCurrency(item.price) }} ₫
          </div>
        </div>
      </div>

      <!-- Order Footer -->
      <div class="order-footer">
        <div class="total-price">
          <span>Tổng cộng:</span>
          <span class="amount">{{ formatCurrency(order.total_amount || 0) }} ₫</span>
        </div>
        <OrderStatus :status="order.status || 'pending'" />
      </div>
    </div>
  </div>
  <div v-else class="no-orders">
    <i class="fas fa-box-open"></i>
    <p>Chưa có đơn hàng nào</p>
  </div>
</template>

<script setup>
import OrderStatus from './OrderStatus.vue'

const props = defineProps({
  orders: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['order-click'])

const formatDateTime = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('vi-VN', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getDisplayItems = (order) => {
  if (!order.items || order.items.length === 0) return []
  return order.items.slice(0, 2) // Show maximum 2 items
}

const getImageUrl = (item) => {
  return item.image_url || item.image || 'https://via.placeholder.com/80'
}

const formatCurrency = (value) => {
  if (!value) return '0'
  return new Intl.NumberFormat('vi-VN').format(value)
}

const handleImageError = (event) => {
  event.target.src = 'https://via.placeholder.com/80?text=No+Image'
}
</script>

<style scoped>
.orders-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.order-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  border: 1px solid #E2E8F0;
  cursor: pointer;
  transition: all 0.2s;
}

.order-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
  padding-bottom: 12px;
  border-bottom: 1px solid #F1F5F9;
}

.order-type-badge {
  background: #EFF6FF;
  color: #2563EB;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
}

.order-created-at {
  font-size: 13px;
  color: #64748B;
}

.pharmacy-info {
  font-size: 14px;
  color: #334155;
  margin-bottom: 12px;
  font-weight: 500;
}

.product-section {
  margin-bottom: 16px;
}

.product-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 0;
  border-bottom: 1px solid #F1F5F9;
}

.product-item:last-child {
  border-bottom: none;
}

.product-image {
  width: 60px;
  height: 60px;
  border-radius: 8px;
  overflow: hidden;
  flex-shrink: 0;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-details {
  flex: 1;
}

.product-name {
  font-size: 14px;
  font-weight: 500;
  color: #1E293B;
  margin-bottom: 4px;
}

.product-category {
  font-size: 12px;
  color: #94A3B8;
}

.product-price {
  font-size: 14px;
  font-weight: 600;
  color: #1E293B;
}

.order-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 16px;
  border-top: 1px solid #F1F5F9;
}

.total-price {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.total-price span {
  font-size: 13px;
  color: #64748B;
}

.total-price .amount {
  font-size: 18px;
  font-weight: 700;
  color: #DC2626;
}

.no-orders {
  text-align: center;
  padding: 60px 20px;
  color: #94A3B8;
}

.no-orders i {
  font-size: 48px;
  margin-bottom: 16px;
  display: block;
}

.no-orders p {
  font-size: 16px;
  margin: 0;
}
</style>

