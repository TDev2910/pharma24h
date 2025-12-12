<template>
  <div class="dashboard-container">
    <div class="dashboard-header">
      <div>
        <h2 class="page-title" style="margin-left: 20px;">Dashboard</h2>
        <p class="text-subtitle" style="margin-left: 20px;">Tổng quan hiệu suất bán hàng hôm nay</p>
      </div>
      <div class="header-actions">
        <span class="current-date">{{ currentDate }}</span>
      </div>
    </div>

    <div class="stats-grid">
      <div v-for="(stat, index) in stats" :key="index" class="stat-card">
        <div class="stat-header">
          <span class="stat-title">{{ stat.title }}</span>
          <div class="stat-icon" :class="stat.iconClass">
            <i :class="stat.icon"></i>
          </div>
        </div>
        <div class="stat-value">{{ stat.value }}</div>
        <div class="stat-footer">
          <span class="trend-badge" :class="stat.isPositive ? 'trend-up' : 'trend-down'">
            <i :class="stat.isPositive ? 'pi pi-arrow-up' : 'pi pi-arrow-down'"></i>
            {{ stat.percent }}
          </span>
          <span class="subtext">{{ stat.subtext }}</span>
        </div>
      </div>
    </div>

    <div class="main-grid">
      <div class="chart-section card-box">
        <div class="card-header">
          <div>
            <h3>Doanh thu theo tuần</h3>
            <p class="subtext">Tổng quan hiệu suất bán hàng 7 ngày qua</p>
          </div>
          <div class="highlight-value">120.5M ₫ <span class="trend-up-text">+8.5%</span></div>
        </div>
        <div class="chart-container">
          <Chart type="line" :data="chartData" :options="chartOptions" class="h-full" />
        </div>
      </div>

      <div class="alert-section card-box">
        <div class="card-header flex-between">
          <h3 class="text-danger"><i class="pi pi-exclamation-triangle mr-2"></i>Sắp hết hàng</h3>
          <a href="/staff/products/stock" class="link-action">Xem kho</a>
        </div>
        <div class="stock-list">
          <div v-for="item in lowStockItems" :key="item.id" class="stock-item">
            <div class="stock-info">
              <span class="product-name">{{ item.name }}</span>
              <span class="stock-qty text-danger">{{ item.qty }} {{ item.unit }}</span>
            </div>
            <div class="progress-bg">
              <div class="progress-fill" :style="{ width: item.percent + '%' }"></div>
            </div>
            <small class="min-stock">Mức tối thiểu: {{ item.min }} {{ item.unit }}</small>
          </div>
        </div>
      </div>
    </div>

    <div class="table-section card-box">
      <div class="card-header flex-between">
        <h3>TOP 5 khách hàng mua nhiều nhất</h3>
      </div>

      <DataTable :value="topCustomers" responsiveLayout="scroll" class="p-datatable-sm">
        <Column header="#" style="width: 60px">
          <template #body="slotProps">
            <span class="rank-badge">{{ slotProps.index + 1 }}</span>
          </template>
        </Column>
        <Column field="name" header="Khách hàng">
          <template #body="slotProps">
            <div class="customer-cell">
              <span>{{ slotProps.data.name }}</span>
            </div>
          </template>
        </Column>
        <Column field="order_count" header="Số đơn hàng">
          <template #body="slotProps">
            <strong class="text-primary">{{ slotProps.data.order_count }}</strong>
          </template>
        </Column>
        <Column field="total_spent_formatted" header="Tổng tiền đã mua">
          <template #body="slotProps">
            <strong>{{ slotProps.data.total_spent_formatted }}</strong>
          </template>
        </Column>
      </DataTable>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import Chart from 'primevue/chart';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  stats: {
    type: Object,
    default: () => ({
      todayRevenue: '0',
      revenueChange: 0,
      todayOrders: 0,
      ordersChange: 0,
    })
  },
  topCustomers: {
    type: Array,
    default: () => []
  },
  lowStockItems: {
    type: Array,
    default: () => []
  }
});

//Map dữ liệu khách hàng
const topCustomers = computed(() => {
  return props.topCustomers.map(customer => ({
    id: customer.id,
    name: customer.name,
    order_count: customer.order_count,
    total_spent: customer.total_spent,
    total_spent_formatted: customer.total_spent_formatted,
  }));
});

const lowStockItems = computed(() => {
  return props.lowStockItems.map(item => ({
    id: item.id,
    name: item.name,
    qty: item.qty,
    unit: item.unit,
    min: item.min,
    percent: item.percent,
  }))
});

// Stats với dữ liệu thật
const stats = computed(() => [
  {
    title: 'Doanh thu hôm nay',
    value: `${props.stats.todayRevenue} ₫`,
    percent: `${props.stats.revenueChange >= 0 ? '+' : ''}${props.stats.revenueChange}%`,
    subtext: 'so với hôm qua',
    isPositive: props.stats.revenueChange >= 0,
    icon: 'pi pi-wallet',
    iconClass: 'bg-green-100 text-green-600'
  },
  {
    title: 'Đơn hàng mới',
    value: props.stats.todayOrders.toString(),
    percent: `${props.stats.ordersChange >= 0 ? '+' : ''}${props.stats.ordersChange}%`,
    subtext: 'so với hôm qua',
    isPositive: props.stats.ordersChange >= 0,
    icon: 'pi pi-shopping-cart',
    iconClass: 'bg-blue-100 text-blue-600'
  },
  {
    title: 'Cảnh báo kho',
    value: '5 sản phẩm',
    percent: '',
    subtext: 'Cần nhập thêm ngay',
    isPositive: false,
    icon: 'pi pi-exclamation-circle',
    iconClass: 'bg-red-100 text-red-600'
  },
  {
    title: 'Khách hàng mới',
    value: '3',
    percent: '1%',
    subtext: 'tăng trưởng',
    isPositive: true,
    icon: 'pi pi-user-plus',
    iconClass: 'bg-purple-100 text-purple-600'
  }
]);

// 2. Chart Configuration
const chartData = ref();
const chartOptions = ref();

const initChart = () => {
  const documentStyle = getComputedStyle(document.documentElement);
  const textColor = '#64748b';
  const textColorSecondary = '#94a3b8';
  const surfaceBorder = '#f1f5f9';

  chartData.value = {
    labels: ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'CN'],
    datasets: [
      {
        label: 'Doanh thu',
        data: [65, 59, 80, 81, 120, 95, 110],
        fill: true,
        borderColor: '#3B82F6', // Màu xanh dương giống ảnh
        backgroundColor: 'rgba(59, 130, 246, 0.1)', // Màu nền mờ
        tension: 0.4 // Độ cong mượt
      }
    ]
  };

  chartOptions.value = {
    plugins: {
      legend: { display: false } // Ẩn chú thích để giống ảnh
    },
    scales: {
      x: {
        ticks: { color: textColorSecondary },
        grid: { display: false } // Ẩn lưới dọc
      },
      y: {
        ticks: { display: false }, // Ẩn số trục Y cho gọn giống ảnh
        grid: { color: surfaceBorder, drawBorder: false }
      }
    },
    maintainAspectRatio: false
  };
};

// Helper styles
const currentDate = new Date().toLocaleDateString('vi-VN', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

const getStatusClass = (status) => {
  const statusMap = {
    'Hoàn thành': 'status-success',
    'Chờ xử lý': 'status-warning',
    'Đang giao': 'status-info',
    'Đã xác nhận': 'status-info',
    'Đã hủy': 'status-danger',
  };
  return statusMap[status] || 'status-warning';
};

onMounted(() => {
  initChart();
});
</script>

<style scoped>
/* --- GLOBAL LAYOUT --- */
.dashboard-container {
  padding: 24px;
  max-width: 1400px;
  margin: 0 auto;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
}

.card-box {
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
  border: 1px solid #f1f5f9;
}

/* --- HEADER --- */
.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 24px;
}

.page-title {
  font-size: 24px;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 4px 0;
}

.version-tag {
  font-size: 12px;
  background: #f1f5f9;
  padding: 2px 6px;
  border-radius: 4px;
  color: #64748b;
  vertical-align: middle;
}

.text-subtitle {
  color: #64748b;
  font-size: 14px;
  margin: 0;
}

.current-date {
  color: #64748b;
  font-size: 14px;
  font-weight: 500;
}

/* --- STATS GRID --- */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 20px;
  margin-bottom: 24px;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
  border: 1px solid #f1f5f9;
  transition: transform 0.2s;
}

.stat-card:hover {
  transform: translateY(-2px);
}

.stat-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 12px;
}

.stat-title {
  color: #64748b;
  font-size: 14px;
  font-weight: 500;
}

.stat-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-value {
  font-size: 24px;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 8px;
}

.trend-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 12px;
  font-weight: 600;
  padding: 2px 6px;
  border-radius: 4px;
  margin-right: 8px;
}

.trend-up {
  background: #dcfce7;
  color: #16a34a;
}

.trend-down {
  background: #fee2e2;
  color: #dc2626;
}

.subtext {
  font-size: 12px;
  color: #94a3b8;
}

.bg-green-100 {
  background: #dcfce7;
}

.text-green-600 {
  color: #16a34a;
}

.bg-blue-100 {
  background: #dbeafe;
}

.text-blue-600 {
  color: #2563eb;
}

.bg-red-100 {
  background: #fee2e2;
}

.text-red-600 {
  color: #dc2626;
}

.bg-purple-100 {
  background: #f3e8ff;
}

.text-purple-600 {
  color: #9333ea;
}

/* --- MAIN GRID (Chart + Alert) --- */
.main-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  /* Tỉ lệ 2:1 giống ảnh */
  gap: 24px;
  margin-bottom: 24px;
}

.card-header {
  margin-bottom: 20px;
}

.card-header h3 {
  font-size: 16px;
  font-weight: 600;
  color: #1e293b;
  margin: 0;
}

.flex-between {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.highlight-value {
  font-size: 18px;
  font-weight: 700;
  color: #1e293b;
  text-align: right;
}

.trend-up-text {
  font-size: 13px;
  color: #10b981;
  background: #ecfdf5;
  padding: 2px 6px;
  border-radius: 4px;
  margin-left: 8px;
}

.chart-container {
  height: 300px;
  position: relative;
}

/* Alert Section */
.text-danger {
  color: #ef4444;
}

.link-action {
  font-size: 13px;
  color: #3b82f6;
  text-decoration: none;
  font-weight: 500;
}

.stock-item {
  margin-bottom: 16px;
  padding-bottom: 16px;
  border-bottom: 1px solid #f1f5f9;
}

.stock-item:last-child {
  border-bottom: none;
  margin-bottom: 0;
}

.stock-info {
  display: flex;
  justify-content: space-between;
  margin-bottom: 6px;
  font-size: 14px;
  font-weight: 500;
}

.progress-bg {
  height: 6px;
  background: #f1f5f9;
  border-radius: 10px;
  margin-bottom: 4px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: #ef4444;
  border-radius: 10px;
}

.min-stock {
  font-size: 11px;
  color: #94a3b8;
}

/* --- TABLE SECTION --- */
.btn-filter {
  background: white;
  border: 1px solid #e2e8f0;
  padding: 6px 12px;
  border-radius: 6px;
  color: #64748b;
  cursor: pointer;
}

.text-primary {
  color: #3b82f6;
}

.customer-cell {
  display: flex;
  align-items: center;
  gap: 8px;
}

.avatar-circle {
  width: 24px;
  height: 24px;
  background: #dbeafe;
  color: #3b82f6;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: bold;
}

.status-badge {
  padding: 4px 8px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
}

.status-success {
  background: #dcfce7;
  color: #16a34a;
}

.status-warning {
  background: #fef9c3;
  color: #ca8a04;
}

.status-info {
  background: #dbeafe;
  color: #2563eb;
}

.btn-icon {
  border: none;
  background: transparent;
  color: #94a3b8;
  cursor: pointer;
}

/* Responsive */
@media (max-width: 1024px) {
  .main-grid {
    grid-template-columns: 1fr;
  }
}
</style>