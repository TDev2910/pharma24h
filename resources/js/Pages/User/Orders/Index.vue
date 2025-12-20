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

    <!-- Orders Table Component -->
    <OrderTable :orders="filteredOrders" @order-click="goToOrderDetails" />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import OrderTable from '@/Components/User/Order/OrderTable.vue'

const props = defineProps({
  orders: {
    type: Array,
    default: () => []
  }
})

const searchQuery = ref('')
const activeTab = ref('all')

const statusTabs = [
  { status: 'all', label: 'Tất cả' },
  { status: 'pending', label: 'Chờ xử lý' },
  { status: 'processing', label: 'Đang xử lý' },
  { status: 'shipping', label: 'Đang giao' },
  { status: 'completed', label: 'Hoàn thành' },
  { status: 'cancelled', label: 'Đã hủy' }
]

const filteredOrders = computed(() => {
  let filtered = props.orders || []

  // Filter by active tab
  if (activeTab.value !== 'all') {
    filtered = filtered.filter(order => order.status === activeTab.value)
  }

  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(order => {
      return order.code?.toLowerCase().includes(query) ||
             order.items?.some(item => 
               item.product_name?.toLowerCase().includes(query) ||
               item.name?.toLowerCase().includes(query)
             )
    })
  }

  return filtered
})

const getOrdersCountByStatus = (status) => {
  if (status === 'all') return props.orders?.length || 0
  return props.orders?.filter(order => order.status === status).length || 0
}

const goToOrderDetails = (orderId) => {
  router.visit(`/user/orders/${orderId}`)
}
</script>

<style scoped>
.orders-history-container {
  max-width: 1200px;
  margin: 0 auto;
}

/* Header */
.orders-header {
  margin-bottom: 32px;
}

.orders-title {
  font-size: 28px;
  font-weight: 700;
  color: #1E293B;
  margin-bottom: 20px;
}

.search-container {
  position: relative;
  max-width: 500px;
}

.search-icon {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #94A3B8;
}

.search-input {
  width: 100%;
  padding: 12px 16px 12px 48px;
  border: 1px solid #CBD5E1;
  border-radius: 12px;
  font-size: 14px;
  transition: border-color 0.2s;
}

.search-input:focus {
  outline: none;
  border-color: #3B82F6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Status Tabs */
.status-tabs {
  display: flex;
  gap: 8px;
  margin-bottom: 24px;
  overflow-x: auto;
  padding-bottom: 8px;
}

.status-tab {
  padding: 10px 20px;
  background: white;
  border: 1px solid #E2E8F0;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  color: #64748B;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}

.status-tab:hover {
  background: #F8FAFC;
  border-color: #CBD5E1;
}

.status-tab.active {
  background: #3B82F6;
  color: white;
  border-color: #3B82F6;
}

/* Responsive */
@media (max-width: 768px) {
  .orders-title {
    font-size: 24px;
  }

  .search-container {
    max-width: 100%;
  }
  
  .status-tabs {
    flex-wrap: nowrap;
    -webkit-overflow-scrolling: touch;
  }
}
</style>
