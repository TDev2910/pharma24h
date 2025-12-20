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
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="orders.length === 0">
            <td colspan="5" class="text-center text-muted">Chưa có đơn hàng nào</td>
          </tr>
          <tr v-for="order in orders" :key="order.id">
            <td class="code">{{ order.code }} <br></td>
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
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  orders: {
    type: Array,
    default: () => []
  }
})
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
}

.custom-table th {
  text-align: left;
  color: #000;
  font-size: 14px;
  font-weight: 600;
  padding-bottom: 12px;
  border-bottom: 1px solid #F1F5F9;
}

.custom-table td {
  padding: 16px 0;
  border-bottom: 1px solid #F1F5F9;
  color: #334155;
  font-size: 14px;
  vertical-align: middle;
}

.custom-table tr:last-child td {
  border-bottom: none;
}

.custom-table .code {
  font-weight: 600;
  color: #1E293B;
}

.custom-table .code small {
  color: #94A3B8;
  font-weight: 400;
}

.custom-table .price {
  font-weight: 700;
  color: #1E293B;
}

.status-badge {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
}

.status-badge.processing {
  background: #EFF6FF;
  color: #3B82F6;
}

.status-badge.shipping {
  background: #FFEDD5;
  color: #F97316;
}

.status-badge.completed {
  background: #DCFCE7;
  color: #16A34A;
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
