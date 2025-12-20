<template>
  <div class="section-card">
    <div class="card-header">
      <h3><i class="fas fa-history"></i> Đơn hàng gần đây</h3>
      <Link href="/user/orders" class="link-text">Xem tất cả</Link>
    </div>

    <div class="table-responsive">
      <table class="custom-table">
        <thead>
          <tr>
            <th>Mã đơn</th>
            <th>Sản phẩm</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="orders.length === 0">
            <td colspan="4" class="text-center text-muted">Chưa có đơn hàng nào</td>
          </tr>
          <tr v-for="order in orders" :key="order.id" @click="viewOrderDetail(order.id)" class="clickable-row">
            <td class="code">{{ order.code }}</td>
            <td>{{ order.product }}</td>
            <td class="price">{{ order.total }}</td>
            <td>
              <span :class="['status-badge', order.statusClass]">{{ order.status }}</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'

const props = defineProps({
  orders: {
    type: Array,
    default: () => []
  }
})

const viewOrderDetail = (orderId) => {
  router.visit(`/user/orders/${orderId}`)
}
</script>

<style scoped>
.section-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 16px;
  border-bottom: 1px solid #E2E8F0;
}

.card-header h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1E293B;
  display: flex;
  align-items: center;
  gap: 8px;
  margin: 0;
}

.card-header h3 i {
  color: #3B82F6;
}

.link-text {
  color: #3B82F6;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
}

.link-text:hover {
  text-decoration: underline;
}

.table-responsive {
  overflow-x: auto;
}

.custom-table {
  width: 100%;
  border-collapse: collapse;
}

.custom-table thead th {
  text-align: left;
  padding: 12px;
  font-size: 13px;
  font-weight: 600;
  color: #64748B;
  border-bottom: 1px solid #E2E8F0;
}

.custom-table tbody tr {
  border-bottom: 1px solid #F1F5F9;
  transition: background 0.2s;
}

.custom-table tbody tr.clickable-row {
  cursor: pointer;
}

.custom-table tbody tr.clickable-row:hover {
  background: #F8FAFC;
}

.custom-table tbody td {
  padding: 16px 12px;
  font-size: 14px;
  color: #334155;
}

.custom-table tbody td.code {
  font-weight: 600;
  color: #3B82F6;
}

.custom-table tbody td.price {
  font-weight: 600;
  color: #1E293B;
}

.status-badge {
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
  display: inline-block;
}

.status-badge.success {
  background: #DCFCE7;
  color: #16A34A;
}

.status-badge.warning {
  background: #FEF3C7;
  color: #D97706;
}

.status-badge.danger {
  background: #FEE2E2;
  color: #DC2626;
}

.status-badge.info {
  background: #DBEAFE;
  color: #2563EB;
}

.text-center {
  text-align: center;
}

.text-muted {
  color: #94A3B8;
  font-style: italic;
}
</style>

