<template>
  <div class="section-card">
    <div class="card-header">
      <h3><i class="fas fa-box"></i> Đơn hàng gần đây</h3>
      <Link href="/user/orders" class="link-text">Xem tất cả</Link>
    </div>

    <div class="table-responsive">
      <table class="custom-table">
        <thead>
          <tr>
            <th style="width: 20%;">Mã đơn</th>
            <th style="width: 40%;">Sản phẩm</th>
            <th style="width: 20%;" class="text-right">Tổng tiền</th>
            <th style="width: 20%;" class="text-center">Trạng thái</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="orders.length === 0">
            <td colspan="4" class="text-center text-muted">Chưa có đơn hàng nào</td>
          </tr>
          <tr v-for="order in orders" :key="order.id">
            <td class="code">{{ order.code }}</td>
            <td class="product-col">
              <div class="truncate-text" :title="order.product">
                {{ order.product }}
              </div>
            </td>
            <td class="price text-right">{{ order.total }}</td>
            <td class="text-center">
              <span :class="['status-badge', order.statusClass]">{{ order.status }}</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  orders: {
    type: Array,
    default: () => []
  }
})

const getStatusClass = (status) => {
  const map = {
    'pending': 'status-pending',
    'confirmed': 'status-confirmed',
    'delivering': 'status-delivering',
    'completed': 'status-completed',
    'cancelled': 'status-cancelled',
  };
  return map[status] || 'status-default';
}

const getStatusLabel = (status) => {
  const map = {
    'pending': 'Chờ xử lý',
    'confirmed': 'Đã xác nhận',
    'delivering': 'Đang giao',
    'completed': 'Hoàn thành',
    'cancelled': 'Đã hủy',
  };
  return map[status] || status;
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(amount);
}
</script>

<style scoped>
.section-card {
  background: white;
  border-radius: 16px;
  padding: 24px;
  border: 1px solid #E2E8F0;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
  margin-bottom: 24px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.card-header h3 {
  font-size: 16px;
  font-weight: 700;
  color: #1E293B;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 8px;
}

.card-header h3 i {
  color: #3B82F6;
}

.link-text {
  color: #3B82F6;
  text-decoration: none;
  font-size: 13px;
  font-weight: 500;
}

.table-responsive {
  overflow-x: auto;
}

/* --- TABLE --- */
.custom-table {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
  /* Giúp các cột chia tỷ lệ chính xác hơn */
}

/* Thêm padding trái/phải để chữ không bị dính vào nhau */
.custom-table th {
  text-align: left;
  color: #475569;
  font-size: 13px;
  font-weight: 600;
  padding: 12px 12px;
  border-bottom: 1px solid #E2E8F0;
  text-transform: uppercase;
  /* In hoa nhẹ tiêu đề để sang trọng hơn */
}

.custom-table td {
  padding: 16px 12px;
  border-bottom: 1px solid #F1F5F9;
  color: #334155;
  font-size: 14px;
  vertical-align: middle;
}

/* Bỏ padding trái của cột đầu, phải của cột cuối để bảng gọn gàng */
.custom-table th:first-child,
.custom-table td:first-child {
  padding-left: 0;
}

.custom-table th:last-child,
.custom-table td:last-child {
  padding-right: 0;
}

.custom-table tr:last-child td {
  border-bottom: none;
}

.custom-table .code {
  font-weight: 600;
  color: #3B82F6;
  /* Đổi màu mã đơn thành xanh cho nổi bật */
}

.custom-table .price {
  font-weight: 700;
  color: #1E293B;
}

/* --- XỬ LÝ TEXT DÀI (SẢN PHẨM) --- */
.product-col {
  max-width: 0;
  /* Bắt buộc phải có để truncate hoạt động trong table-layout: fixed */
}

.truncate-text {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  display: block;
  font-weight: 500;
  color: #0F172A;
}

/* --- BADGE TRẠNG THÁI (ĐẬM & RÕ RÀNG HƠN) --- */
.status-badge {
  padding: 4px 10px;
  border-radius: 4px;
  font-size: 13px;
  font-weight: 600;
  border: 1px solid transparent;
}

/* Processing - Blue */
.status-badge.processing {
  background: #2563EB;
  color: #ffffff;
  border-color: #1D4ED8;
}

/* Shipping - Orange */
.status-badge.shipping {
  background: #EA580C;
  color: #ffffff;
  border-color: #C2410C;
}

/* Completed / Success - Green */
.status-badge.completed,
.status-badge.success {
  background: #16A34A;
  color: #ffffff;
  border-color: #15803D;
}

.status-badge.cancelled {
  background: #DC2626;
  color: #ffffff;
  border-color: #B91C1C;
}

/* Warning - Amber */
.status-badge.warning {
  background: #D97706;
  color: #ffffff;
  border-color: #B45309;
}

/* Danger - Red */
.status-badge.danger {
  background: #DC2626;
  color: #ffffff;
  border-color: #B91C1C;
}

/* Info - Indigo */
.status-badge.info {
  background: #4F46E5;
  color: #ffffff;
  border-color: #4338CA;
}

/* --- TIỆN ÍCH --- */
.text-center {
  text-align: center !important;
}

.text-right {
  text-align: right !important;
}

.text-muted {
  color: #94A3B8;
  font-style: italic;
}
</style>