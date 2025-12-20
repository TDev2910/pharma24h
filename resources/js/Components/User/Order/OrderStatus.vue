<template>
  <span :class="['order-status-badge', statusClass]">
    {{ statusText }}
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: {
    type: String,
    required: true
  }
})

const statusConfig = {
  'pending': { text: 'Chờ xử lý', class: 'warning' },
  'processing': { text: 'Đang xử lý', class: 'info' },
  'shipping': { text: 'Đang giao', class: 'info' },
  'delivered': { text: 'Đã giao', class: 'success' },
  'completed': { text: 'Hoàn thành', class: 'success' },
  'cancelled': { text: 'Đã hủy', class: 'danger' },
  'refunded': { text: 'Đã hoàn tiền', class: 'secondary' }
}

const statusClass = computed(() => {
  return statusConfig[props.status]?.class || 'secondary'
})

const statusText = computed(() => {
  return statusConfig[props.status]?.text || props.status
})
</script>

<style scoped>
.order-status-badge {
  padding: 6px 14px;
  border-radius: 16px;
  font-size: 12px;
  font-weight: 600;
  display: inline-block;
  text-transform: uppercase;
  letter-spacing: 0.3px;
}

.order-status-badge.success {
  background: #DCFCE7;
  color: #16A34A;
}

.order-status-badge.warning {
  background: #FEF3C7;
  color: #D97706;
}

.order-status-badge.danger {
  background: #FEE2E2;
  color: #DC2626;
}

.order-status-badge.info {
  background: #DBEAFE;
  color: #2563EB;
}

.order-status-badge.secondary {
  background: #F1F5F9;
  color: #64748B;
}
</style>

