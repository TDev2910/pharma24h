<template>
  <div class="order-details-container">
    <!-- Header -->
    <div class="details-header">
      <Link href="/user/orders" class="back-button">
        <i class="fas fa-arrow-left"></i> Quay lại
      </Link>
      <h1 class="details-title">Chi tiết đơn hàng</h1>
    </div>

    <!-- Order Info -->
    <div v-if="order" class="order-info-card">
      <!-- Order Header -->
      <div class="order-header-section">
        <div class="order-code-section">
          <span class="order-code-label">Mã đơn hàng:</span>
          <span class="order-code-value">{{ order.order_code || formatOrderCode(order.id) }}</span>
        </div>
        <span class="order-status-badge" :class="getStatusClass(order.order_status)">
          {{ getStatusLabel(order.order_status) }}
        </span>
      </div>

      <div class="order-date">
        <i class="fas fa-calendar"></i>
        Ngày đặt: {{ formatDateTime(order.created_at) }}
      </div>

      <!-- Pharmacy Info -->
      <div v-if="order.pickup_location" class="pharmacy-info-section">
        <i class="fas fa-map-marker-alt"></i>
        <span>Nhà thuốc: {{ order.pickup_location }}</span>
      </div>

      <!-- Order Items -->
      <div class="order-items-section">
        <h3 class="section-title">Sản phẩm</h3>
        <div class="items-list">
          <div v-for="(item, index) in order.items" :key="item.id || index" class="item-card">
            <div class="item-image">
              <img :src="getImageUrl(item)" :alt="item.product_name || item.name" @error="handleImageError" />
            </div>
            <div class="item-details">
              <div class="item-name">{{ item.product_name || item.name }}</div>
              <div class="item-category">Phân loại: Viên</div>
              <div class="item-quantity">Số lượng: {{ item.quantity || 1 }}</div>
            </div>
            <div class="item-price">
              {{ formatCurrency(item.price) }} ₫
            </div>
          </div>
        </div>
      </div>

      <!-- Order Summary -->
      <div class="order-summary-section">
        <div class="summary-row">
          <span>Tạm tính:</span>
          <span>{{ formatCurrency(order.total_amount) }} ₫</span>
        </div>
        <div class="summary-row total-row">
          <span>Tổng tiền:</span>
          <span class="total-amount">{{ formatCurrency(order.total_amount) }} ₫</span>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-else class="loading-state">
      <i class="fas fa-spinner fa-spin"></i>
      <p>Đang tải thông tin đơn hàng...</p>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

// Props từ Inertia
const props = defineProps({
  order: {
    type: Object,
    default: null
  }
})

// Helper functions
const formatOrderCode = (id) => {
  return String(id).padStart(4, '0')
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('vi-VN').format(amount || 0)
}

const formatDateTime = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  const hours = String(date.getHours()).padStart(2, '0')
  const minutes = String(date.getMinutes()).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const year = date.getFullYear()
  return `${hours}:${minutes} ${day}/${month}/${year}`
}

const getImageUrl = (item) => {
  if (item.image) {
    if (item.image.startsWith('http://') || item.image.startsWith('https://')) {
      return item.image
    }
    if (item.image.startsWith('storage/')) {
      return `/storage/${item.image.replace('storage/', '')}`
    }
    return `/storage/${item.image}`
  }
  return 'https://placehold.co/80x80?text=No+Image'
}

const handleImageError = (event) => {
  event.target.src = 'https://placehold.co/80x80?text=No+Image'
}

const getStatusClass = (status) => {
  const statusMap = {
    'new': 'status-new',
    'delivered': 'status-delivered',
    'completed': 'status-completed',
    'cancelled': 'status-cancelled'
  }
  return statusMap[status] || 'status-default'
}

const getStatusLabel = (status) => {
  const labelMap = {
    'new': 'Chờ xử lý',
    'delivered': 'Đã giao',
    'completed': 'Hoàn thành',
    'cancelled': 'Đã hủy'
  }
  return labelMap[status] || status
}
</script>

<style scoped>
.order-details-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.details-header {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-bottom: 24px;
}

.back-button {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #0d6efd;
  text-decoration: none;
  font-size: 14px;
  transition: color 0.3s ease;
}

.back-button:hover {
  color: #0a58ca;
  text-decoration: none;
}

.details-title {
  font-size: 28px;
  font-weight: 700;
  color: #2c3e50;
  margin: 0;
}

.order-info-card {
  background: #ffffff;
  border: 1px solid #e9ecef;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.order-header-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  padding-bottom: 16px;
  border-bottom: 1px solid #e9ecef;
}

.order-code-section {
  display: flex;
  align-items: center;
  gap: 8px;
}

.order-code-label {
  color: #6c757d;
  font-size: 14px;
}

.order-code-value {
  color: #2c3e50;
  font-size: 16px;
  font-weight: 600;
}

.order-status-badge {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
}

.status-new {
  background-color: #fff3cd;
  color: #856404;
}

.status-delivered,
.status-completed {
  background-color: #d4edda;
  color: #155724;
}

.status-cancelled {
  background-color: #f8d7da;
  color: #721c24;
}

.order-date {
  color: #6c757d;
  font-size: 14px;
  margin-bottom: 12px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.pharmacy-info-section {
  color: #495057;
  font-size: 14px;
  margin-bottom: 24px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.order-items-section {
  margin-bottom: 24px;
}

.section-title {
  font-size: 18px;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 16px;
}

.items-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.item-card {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px;
  background-color: #f8f9fa;
  border-radius: 8px;
}

.item-image {
  width: 80px;
  height: 80px;
  flex-shrink: 0;
  border-radius: 8px;
  overflow: hidden;
  background-color: #e9ecef;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-details {
  flex: 1;
  min-width: 0;
}

.item-name {
  font-size: 14px;
  font-weight: 500;
  color: #2c3e50;
  margin-bottom: 4px;
}

.item-category,
.item-quantity {
  font-size: 12px;
  color: #6c757d;
  margin-bottom: 2px;
}

.item-price {
  font-size: 16px;
  font-weight: 600;
  color: #0d6efd;
  white-space: nowrap;
}

.order-summary-section {
  padding-top: 16px;
  border-top: 1px solid #e9ecef;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
  font-size: 14px;
  color: #495057;
}

.total-row {
  font-size: 18px;
  font-weight: 600;
  color: #2c3e50;
  margin-top: 8px;
  padding-top: 8px;
  border-top: 1px solid #e9ecef;
}

.total-amount {
  color: #dc3545;
  font-size: 20px;
}

.loading-state {
  text-align: center;
  padding: 60px 20px;
  color: #6c757d;
}

.loading-state i {
  font-size: 48px;
  margin-bottom: 16px;
}

/* Responsive */
@media (max-width: 768px) {
  .details-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .order-header-section {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }

  .item-card {
    flex-wrap: wrap;
  }

  .item-price {
    width: 100%;
    text-align: right;
  }
}
</style>