<template>
  <div class="orders-history-container">
    <!-- Header Section -->
    <div class="orders-header">
      <h1 class="orders-title">Lịch sử đơn hàng</h1>
      <div class="search-container">
        <i class="fas fa-search search-icon"></i>
        <input type="text" v-model="searchQuery" class="search-input"
          placeholder="Tìm kiếm theo mã đơn hàng hoặc tên sản phẩm" />
      </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="status-tabs">
      <button v-for="tab in statusTabs" :key="tab.status" @click="activeTab = tab.status"
        :class="['status-tab', { active: activeTab === tab.status }]">
        {{ tab.label }} ({{ getOrdersCountByStatus(tab.status) }})
      </button>
    </div>

    <!-- Orders List -->
    <div class="orders-list" v-if="filteredOrders.length > 0">
      <div v-for="order in filteredOrders" :key="order.id" class="order-card" @click="goToOrderDetails(order.id)">        <!-- Order Header -->
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

        <!-- Order Summary -->
        <div class="order-summary">
          <div class="summary-left">
            <div class="additional-products" v-if="getAdditionalItemsCount(order) > 0" @click.stop="openModal(order.id)">              Cùng {{ getAdditionalItemsCount(order) }} sản phẩm khác >
            </div>
            <span class="order-code">Mã đơn hàng: {{ order.order_code }}</span>
          </div>
          
          <div class="total-amount">
            Thành tiền: <span class="total-value">{{ formatCurrency(order.total_amount) }} ₫</span>
          </div>
        </div>

      </div>
    </div>

    <!-- Empty State -->
    <div class="empty-state" v-else>
      <i class="fas fa-box-open empty-icon"></i>
      <p class="empty-text">Bạn chưa có đơn hàng nào.</p>
    </div>

    <!-- Order Details Modal -->
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router, usePage, useForm } from '@inertiajs/vue3'
// Props từ Inertia

const goToOrderDetails = (orderId) => {
  router.visit(`/user/orders/${orderId}`)
}
const props = defineProps({
  orders: {
    type: Array,
    default: () => []
  }
})

// Modal state
const showModal = ref(false)
const selectedOrderId = ref(null)

// Search and filter state
const searchQuery = ref('')
const activeTab = ref('all')

// Status tabs configuration
const statusTabs = [
  { status: 'all', label: 'Tất cả' },
  { status: 'new', label: 'Chờ xử lý' },
  { status: 'confirmed', label: 'Đã xác nhận' },
  { status: 'delivered', label: 'Hoàn thành' },
  { status: 'cancelled', label: 'Đã hủy' },
]

// Computed: Filtered orders
const filteredOrders = computed(() => {
  let filtered = props.orders

  // Filter by status
  if (activeTab.value !== 'all') {
    filtered = filtered.filter(order => {
      if (activeTab.value === 'delivered') {
        return order.order_status === 'delivered' || order.order_status === 'completed'
      }
      return order.order_status === activeTab.value
    })
  }

  // Filter by search query
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase().trim()
    filtered = filtered.filter(order => {
      // Search by order code
      const orderCode = (order.order_code || formatOrderCode(order.id)).toLowerCase()
      if (orderCode.includes(query)) return true

      // Search by product names
      if (order.items && order.items.length > 0) {
        return order.items.some(item => {
          const productName = (item.product_name || item.name || '').toLowerCase()
          return productName.includes(query)
        })
      }

      return false
    })
  }

  return filtered
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

const formatPoints = (amount) => {
  // Tính điểm tích lũy: 1% của tổng tiền, làm tròn
  const points = Math.round(amount / 100)
  return formatCurrency(points)
}

const getImageUrl = (item) => {
  if (item.image) {
    // Check if it's a full URL
    if (item.image.startsWith('http://') || item.image.startsWith('https://')) {
      return item.image
    }
    // Check if it already has storage/ prefix
    if (item.image.startsWith('storage/')) {
      return `/storage/${item.image.replace('storage/', '')}`
    }
    return `/storage/${item.image}`
  }
  // Default placeholder
  return 'https://placehold.co/80x80?text=No+Image'
}

const handleImageError = (event) => {
  event.target.src = 'https://placehold.co/80x80?text=No+Image'
}

const getDisplayItems = (order) => {
  // Hiển thị item đầu tiên
  if (order.items && order.items.length > 0) {
    return [order.items[0]]
  }
  return []
}

const getAdditionalItemsCount = (order) => {
  if (order.items && order.items.length > 1) {
    return order.items.length - 1
  }
  return 0
}

const getOrdersCountByStatus = (status) => {
  if (status === 'all') {
    return props.orders.length
  }
  if (status === 'delivered') {
    return props.orders.filter(o => o.order_status === 'delivered' || o.order_status === 'completed').length
  }
  return props.orders.filter(o => o.order_status === status).length
}

// Modal functions
const openModal = (orderId) => {
  selectedOrderId.value = orderId
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedOrderId.value = null
}
</script>

<style scoped>
.orders-history-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

/* Header Section */
.orders-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  flex-wrap: wrap;
  gap: 16px;
}

.orders-title {
  font-size: 28px;
  font-weight: 700;
  color: #2c3e50;
  margin: 0;
}

.search-container {
  position: relative;
  flex: 1;
  max-width: 500px;
  min-width: 300px;
}

.search-icon {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #6c757d;
  font-size: 16px;
}


.search-input {
  width: 100%;
  padding: 12px 16px 12px 44px;
  border: 1px solid #dee2e6;
  border-radius: 8px;
  font-size: 14px;
  transition: all 0.3s ease;
}

.search-input:focus {
  outline: none;
  border-color: #0d6efd;
  box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
}

/* Status Tabs */
.status-tabs {
  display: flex;
  gap: 8px;
  margin-bottom: 24px;
  flex-wrap: wrap;
  border-bottom: 2px solid #e9ecef;
  padding-bottom: 8px;
}

.status-tab {
  padding: 10px 20px;
  border: none;
  background: transparent;
  color: #6c757d;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  border-radius: 8px 8px 0 0;
  transition: all 0.3s ease;
  position: relative;
}

.status-tab:hover {
  background-color: #f8f9fa;
  color: #0d6efd;
}

.status-tab.active {
  color: #0d6efd;
  font-weight: 600;
}

.order-code {
  color: #6c757d;
  font-size: 14px;
  display: block;
  margin-top: 8px;
}

.status-tab.active::after {
  content: '';
  position: absolute;
  bottom: -10px;
  left: 0;
  right: 0;
  height: 3px;
  background-color: #0d6efd;
  border-radius: 2px 2px 0 0;
}

/* Orders List */
.orders-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.order-card {
  background: #ffffff;
  border: 1px solid #e9ecef;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
  cursor: pointer; /* Thêm dòng này */
}
/* Order Card */
.order-card {
  background: #ffffff;
  border: 1px solid #e9ecef;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
}

.order-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
}

/* Order Header */
.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.order-type-badge {
  background-color: #d4edda;
  color: #155724;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
}

.order-date {
  color: #6c757d;
  font-size: 14px;
}

/* Pharmacy Info */
.pharmacy-info {
  color: #495057;
  font-size: 14px;
  margin-bottom: 16px;
}

/* Product Section */
.product-section {
  margin-bottom: 16px;
}

.product-item {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 12px;
  background-color: #f8f9fa;
  border-radius: 8px;
  margin-bottom: 8px;
}

.product-image {
  width: 80px;
  height: 80px;
  flex-shrink: 0;
  border-radius: 8px;
  overflow: hidden;
  background-color: #e9ecef;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-details {
  flex: 1;
  min-width: 0;
}

.product-name {
  font-size: 14px;
  font-weight: 500;
  color: #2c3e50;
  margin-bottom: 4px;
  line-height: 1.4;
}

.product-category {
  font-size: 12px;
  color: #6c757d;
}

.product-price {
  font-size: 16px;
  font-weight: 600;
  color: #0d6efd;
  white-space: nowrap;
}

/* Order Summary */
.order-summary {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 12px;
  padding-top: 12px;
  border-top: 1px solid #e9ecef;
}

.summary-left {
  flex: 1;
}

.additional-products {
  color: #0d6efd;
  font-size: 14px;
  cursor: pointer;
  text-decoration: none;
  transition: color 0.3s ease;
  display: inline-block;
  margin-bottom: 8px;
}

.additional-products:hover {
  color: #0a58ca;
  text-decoration: underline;
}

.total-amount {
  font-size: 16px;
  font-weight: 600;
  color: #2c3e50;
  text-align: right;
}

.total-value {
  color: #dc3545;
  font-size: 18px;
}

/* Points Bar */
.points-bar {
  background-color: #fff3cd;
  color: #856404;
  padding: 10px 16px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  margin-top: 12px;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 60px 20px;
}

.empty-icon {
  font-size: 64px;
  color: #dee2e6;
  margin-bottom: 16px;
}

.empty-text {
  font-size: 16px;
  color: #6c757d;
  margin: 0;
}

/* Responsive */
@media (max-width: 768px) {
  .orders-header {
    flex-direction: column;
    align-items: stretch;
  }

  .search-container {
    max-width: 100%;
    min-width: 100%;
  }

  .status-tabs {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  .status-tab {
    white-space: nowrap;
    font-size: 12px;
    padding: 8px 16px;
  }

  .order-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }

  .product-item {
    flex-wrap: wrap;
  }

  .product-price {
    width: 100%;
    text-align: right;
  }

  .order-summary {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }

  .orders-title {
    font-size: 24px;
  }
}
</style>
