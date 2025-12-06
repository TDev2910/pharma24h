<template>
  <div class="dashboard-container">
    <div class="dashboard-layout">
      <main class="main-content">

        <div class="stats-grid">
          <div class="stat-card" style="background: #F5FAE1;">
            <div class="stat-label">Tổng sản phẩm</div>
            <div class="stat-value">{{ stats.totalProducts.toLocaleString() }}</div>
          </div>
          <div class="stat-card" style="background: #EEEEEE;">
            <div class="stat-label">Tổng đơn hàng</div>
            <div class="stat-value">{{ stats.totalOrders.toLocaleString() }}</div>
          </div>
          <div class="stat-card" style="background: #F0E4D3;">
            <div class="stat-label">Tổng khách hàng</div>
            <div class="stat-value">{{ stats.totalCustomers.toLocaleString() }}</div>
          </div>
          <div class="stat-card" style="background: #EDDFE0;">
            <div class="stat-label">Tổng doanh thu</div>
            <div class="stat-value">{{ formatCurrency(stats.totalRevenue) }}</div>
          </div>
        </div>

        <div class="charts-row service-chart-row">
          <div class="chart-card">
            <div class="chart-header">
              <h3>Tổng quan doanh thu</h3>
              <div class="chart-controls">
                <Dropdown v-model="totalRevenuePeriod" :options="periodOptions" optionLabel="label" optionValue="value"
                  placeholder="Tháng này" class="period-dropdown" />
              </div>
            </div>
            <Chart type="bar" style="height: 260px;" :data="totalRevenueChartData"
              :options="totalRevenueChartOptions" />
          </div>
        </div>

        <div class="charts-row">

          <div class="left-chart-column" style="display: flex; flex-direction: column; gap: 20px;">

            <div class="chart-card">
              <div class="chart-header">
                <h3>Doanh thu đơn hàng</h3>
                <div class="chart-controls">
                  <Dropdown v-model="orderRevenuePeriod" :options="periodOptions" optionLabel="label"
                    optionValue="value" placeholder="Tháng này" class="period-dropdown" />
                </div>
              </div>
              <Chart v-if="!loadingOrderRevenue" type="bar" style="height: 260px;" :data="orderRevenueChartData"
                :options="orderRevenueChartOptions" />
              <div v-else class="chart-loading">Đang tải dữ liệu...</div>
            </div>
            <div class="chart-card">
              <div class="chart-header">
                <h3>Doanh thu dịch vụ</h3>
                <div class="chart-controls">
                  <Dropdown v-model="serviceRevenuePeriod" :options="periodOptions" optionLabel="label"
                    optionValue="value" placeholder="Tháng này" class="period-dropdown" />
                </div>
              </div>
              <Chart v-if="!loadingServiceRevenue" type="bar" style="height: 260px;" :data="serviceRevenueChartData"
                :options="serviceRevenueChartOptions" />
              <div v-else class="chart-loading">Đang tải dữ liệu...</div>
            </div>

          </div>
          <div class="chart-card">
            <div class="chart-header">
              <h3>Danh mục</h3>
            </div>
            <Chart type="doughnut" :data="chartData" :options="chartOptions" class="w-full md:w-[30rem]" />
            <div class="categories-list">
              <div v-for="(category, index) in categoriesList" :key="index" class="category-item">
                <div class="category-info">
                  <span class="category-dot" :style="{ backgroundColor: category.color }"></span>
                  <span class="category-name">{{ category.name }}</span>
                </div>
                <div class="category-values">
                  <span class="category-amount">${{ category.amount.toLocaleString() }}</span>
                  <span class="category-percentage">{{ category.percentage }}%</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="bottom-row">
          <div class="activity-card">
            <div class="card-header">
              <h3>Khách hàng mua nhiều nhất</h3>
            </div>
            <div class="activity-list">
              <div v-for="(customer, index) in topCustomers" :key="customer.id" class="activity-item">
                <div class="activity-content">
                  <div class="activity-title">{{ customer.name }}</div>
                  <div class="activity-meta">
                    {{ customer.order_count }} đơn hàng •
                    {{ formatCurrency(customer.total_spent) }}
                  </div>
                </div>
                <button class="activity-badge new-order">
                  Top {{ index + 1 }}
                </button>
              </div>
              <div v-if="topCustomers.length === 0" class="activity-item">
                <div class="activity-content">
                  <div class="activity-title">Chưa có dữ liệu</div>
                </div>
              </div>
            </div>
          </div>

          <div class="products-card">
            <div class="card-header">
              <h3>Sản phẩm được mua nhiều nhất</h3>
            </div>
            <div class="products-table">
              <table>
                <thead>
                  <tr>
                    <th>Tên sản phẩm</th>
                    <th>Tồn kho</th>
                    <th>Số lượng đã bán</th>
                    <th>Doanh thu</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="product in topProducts" :key="`${product.type}-${product.id}`">
                    <td>{{ product.name }}</td>
                    <td>{{ product.stocks.toLocaleString() }}</td>
                    <td>{{ product.sales.toLocaleString() }}</td>
                    <td>{{ formatCurrency(product.earnings) }}</td>
                  </tr>
                  <tr v-if="topProducts.length === 0">
                    <td colspan="5" style="text-align: center; padding: 20px; color: #6b7280;">
                      Chưa có dữ liệu
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
import Chart from 'primevue/chart';
import Dropdown from 'primevue/dropdown';
import axios from 'axios';

export default {
  name: 'AdminDashboard',
  components: {
    Chart,
    Dropdown
  },
  props: {
    stats: {
      type: Object,
      default: () => ({
        totalProducts: 0,
        totalOrders: 0,
        totalCustomers: 0,
        totalRevenue: 0,
      })
    },
    categoryStats: {
      type: Object,
      default: () => ({
        medicineCount: 0,
        goodsCount: 0,
        serviceCount: 0,
      })
    },
    topCustomers: {
      type: Array,
      default: () => []
    },
    topProducts: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      // Order Revenue Data
      orderRevenuePeriod: 'thisMonth',
      serviceRevenuePeriod: 'thisMonth',
      totalRevenuePeriod: 'thisMonth',
      loadingOrderRevenue: false,
      loadingServiceRevenue: false,
      loadingTotalRevenue: false,

      // Options cho dropdown
      periodOptions: [
        { label: 'Hôm nay', value: 'today' },
        { label: 'Hôm qua', value: 'yesterday' },
        { label: '7 ngày qua', value: 'last7days' },
        { label: 'Tháng này', value: 'thisMonth' },
        { label: 'Tháng trước', value: 'lastMonth' }
      ],

      orderRevenueData: { labels: [], revenues: [] },
      serviceRevenueData: { labels: [], revenues: [] },
      totalRevenueData: { labels: [], revenues: [] },

      // Total Revenue Chart Options
      totalRevenueChartOptions: {
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: (context) => this.formatCurrency(context.parsed.y)
            }
          }
        },
        scales: {
          x: { grid: { display: false } },
          y: {
            beginAtZero: true,
            ticks: { callback: (value) => this.formatCurrency(value) }
          }
        },
        maintainAspectRatio: false
      },
      // Order Revenue Chart Options
      orderRevenueChartOptions: {
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: (context) => this.formatCurrency(context.parsed.y)
            }
          }
        },
        scales: {
          x: { grid: { display: false } },
          y: {
            beginAtZero: true,
            ticks: { callback: (value) => this.formatCurrency(value) }
          }
        },
        maintainAspectRatio: false
      },
      // Service Revenue Chart Options
      serviceRevenueChartOptions: {
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: (context) => this.formatCurrency(context.parsed.y)
            }
          }
        },
        scales: {
          x: { grid: { display: false } },
          y: {
            beginAtZero: true,
            ticks: { callback: (value) => this.formatCurrency(value) }
          }
        },
        maintainAspectRatio: false
      },
      // Doughnut Chart Options
      chartOptions: {
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: function (context) {
                let label = context.label || '';
                if (label) { label += ': '; }
                label += context.parsed.toLocaleString();
                return label;
              }
            }
          }
        },
        cutout: '60%',
        maintainAspectRatio: false
      },
    };
  },
  computed: {
    chartData() {
      return {
        labels: ['Thuốc', 'Vật tư y tế', 'Dịch vụ'],
        datasets: [{
          data: [
            this.categoryStats.medicineCount,
            this.categoryStats.goodsCount,
            this.categoryStats.serviceCount
          ],
          backgroundColor: ['#3b82f6', '#10b981', '#f59e0b'],
          borderWidth: 0
        }]
      };
    },
    categoriesList() {
      const labels = this.chartData.labels;
      const data = this.chartData.datasets[0].data;
      const colors = this.chartData.datasets[0].backgroundColor;
      const total = data.reduce((sum, value) => sum + value, 0);

      return labels.map((label, index) => {
        const amount = data[index];
        const percentage = total > 0 ? Math.round((amount / total) * 100) : 0;
        return {
          name: label,
          amount: amount,
          percentage: percentage,
          color: colors[index]
        };
      });
    },
    totalRevenueChartData() {
      const colorPalette = [
        '#3b82f6', '#10b981', '#f59e0b', '#ef4444',
        '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16',
        '#f97316', '#6366f1', '#14b8a6', '#a855f7',
        '#22c55e', '#eab308', '#f43f5e', '#0ea5e9'
      ];

      return {
        labels: this.totalRevenueData.labels,
        datasets: [{
          label: 'Doanh thu tổng quan',
          data: this.totalRevenueData.revenues,
          backgroundColor: this.totalRevenueData.revenues.map((_, index) =>
            colorPalette[index % colorPalette.length]
          )
        }]
      };
    },
    orderRevenueChartData() {
      const colorPalette = [
        '#3b82f6', '#10b981', '#f59e0b', '#ef4444',
        '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16',
        '#f97316', '#6366f1', '#14b8a6', '#a855f7',
        '#22c55e', '#eab308', '#f43f5e', '#0ea5e9'
      ];

      return {
        labels: this.orderRevenueData.labels,
        datasets: [{
          label: 'Doanh thu đơn hàng',
          data: this.orderRevenueData.revenues,
          backgroundColor: this.orderRevenueData.revenues.map((_, index) =>
            colorPalette[index % colorPalette.length]
          )
        }]
      };
    },
    serviceRevenueChartData() {
      const colorPalette = [
        '#3b82f6', '#10b981', '#f59e0b', '#ef4444',
        '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16',
        '#f97316', '#6366f1', '#14b8a6', '#a855f7',
        '#22c55e', '#eab308', '#f43f5e', '#0ea5e9'
      ];

      return {
        labels: this.serviceRevenueData.labels,
        datasets: [{
          label: 'Doanh thu dịch vụ',
          data: this.serviceRevenueData.revenues,
          backgroundColor: this.serviceRevenueData.revenues.map((_, index) =>
            colorPalette[index % colorPalette.length]
          )
        }]
      };
    }
  },
  watch: {
    totalRevenuePeriod(newValue) {
      console.log('Total Revenue Period changed to:', newValue);
      this.fetchTotalRevenue(newValue);
    },
    orderRevenuePeriod(newValue) {
      console.log('Order Revenue Period changed to:', newValue);
      this.fetchOrderRevenue(newValue);
    },
    serviceRevenuePeriod(newValue) {
      console.log('Service Revenue Period changed to:', newValue);
      this.fetchServiceRevenue(newValue);
    }
  },
  methods: {
    formatCurrency(value) {
      if (!value) return '0 đ';
      return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
      }).format(value);
    },
    async fetchTotalRevenue(period) {
      console.log('Fetching total revenue with period:', period);
      this.loadingTotalRevenue = true;
      try {
        const response = await axios.get('/admin/dashboard/revenue/total', { params: { period } });
        console.log('Total revenue response:', response.data);
        if (response.data.success) {
          this.totalRevenueData = {
            labels: response.data.data.labels,
            revenues: response.data.data.revenues
          };
          console.log('Total revenue data updated:', this.totalRevenueData);
        }
      } catch (error) {
        console.error('Error fetching total revenue:', error);
      } finally {
        this.loadingTotalRevenue = false;
      }
    },
    async fetchOrderRevenue(period) {
      console.log('Fetching order revenue with period:', period);
      this.loadingOrderRevenue = true;
      try {
        const response = await axios.get('/admin/dashboard/revenue/orders', { params: { period } });
        console.log('Order revenue response:', response.data);
        if (response.data.success) {
          this.orderRevenueData = {
            labels: response.data.data.labels,
            revenues: response.data.data.revenues
          };
          console.log('Order revenue data updated:', this.orderRevenueData);
        }
      } catch (error) {
        console.error('Error fetching order revenue:', error);
      } finally {
        this.loadingOrderRevenue = false;
      }
    },
    async fetchServiceRevenue(period) {
      console.log('Fetching service revenue with period:', period);
      this.loadingServiceRevenue = true;
      try {
        const response = await axios.get('/admin/dashboard/revenue/services', { params: { period } });
        console.log('Service revenue response:', response.data);
        if (response.data.success) {
          this.serviceRevenueData = {
            labels: response.data.data.labels,
            revenues: response.data.data.revenues
          };
          console.log('Service revenue data updated:', this.serviceRevenueData);
        }
      } catch (error) {
        console.error('Error fetching service revenue:', error);
      } finally {
        this.loadingServiceRevenue = false;
      }
    },
  },
  mounted() {
    this.fetchOrderRevenue(this.orderRevenuePeriod);
    this.fetchServiceRevenue(this.serviceRevenuePeriod);
    this.fetchTotalRevenue(this.totalRevenuePeriod);
  }
};
</script>

<style scoped>
/* =========== CSS QUAN TRỌNG ĐỂ SỬA LỖI DROPDOWN =========== */

/* 1. Cho phép dropdown tràn ra ngoài card */
.chart-card {
  background: white;
  padding: 24px;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: visible !important;
  /* QUAN TRỌNG: Cho phép tràn */
}

/* 2. Tạo stacking context mới và đưa header lên cao */
.chart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  position: relative;
  z-index: 20;
  /* QUAN TRỌNG: Nổi lên trên biểu đồ */
}

/* 3. Đưa controls lên cao hơn nữa nếu cần */
.chart-controls {
  display: flex;
  gap: 8px;
  position: relative;
  z-index: 21;
}

/* 4. Dropdown menu phải nổi cao nhất */
.dropdown-menu {
  position: absolute;
  top: calc(100% + 4px);
  right: 0;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  min-width: 160px;
  z-index: 9999;
  /* Luôn trên cùng */
  overflow: visible;
}

/* =========== CÁC CSS KHÁC (GIỮ NGUYÊN) =========== */
.dashboard-container {
  min-height: 100vh;
  background: #f8f9fa;
}

.dashboard-layout {
  display: flex;
}

.main-content {
  flex: 1;
  padding: 24px;
  overflow-y: auto;
  height: calc(100vh - 120px);

  /* Ẩn scrollbar nhưng vẫn cho phép scroll */
  scrollbar-width: none;
  /* Firefox */
  -ms-overflow-style: none;
  /* IE và Edge */
}

.main-content::-webkit-scrollbar {
  display: none;
  /* Chrome, Safari, Opera */
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  margin-bottom: 24px;
}

.stat-card {
  background: white;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.stat-label {
  font-size: 14px;
  color: #6b7280;
  margin-bottom: 8px;
}

.stat-value {
  font-size: 24px;
  font-weight: 700;
  color: #1f2937;
}

.charts-row {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 20px;
  margin-bottom: 24px;
}

.service-chart-row {
  grid-template-columns: 1fr;
}

/* PrimeVue Dropdown Styles */
.period-dropdown {
  min-width: 140px;
}

.period-dropdown :deep(.p-dropdown) {
  border: 1px solid #3b82f6;
  border-radius: 6px;
  font-size: 12px;
  width: 100%;
}

.period-dropdown :deep(.p-dropdown-label) {
  padding: 6px 12px;
  font-size: 12px;
  color: #374151;
}

.period-dropdown :deep(.p-dropdown-trigger) {
  width: 2rem;
}

.period-dropdown :deep(.p-dropdown-panel) {
  font-size: 12px;
}

.period-dropdown :deep(.p-dropdown-item) {
  padding: 10px 12px;
  font-size: 13px;
}

.chart-loading {
  height: 300px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6b7280;
  font-size: 14px;
}

.categories-list {
  margin-top: 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.category-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
}

.category-info {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1;
}

.category-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  display: inline-block;
}

.category-name {
  font-size: 14px;
  color: #1f2937;
  font-weight: 500;
}

.category-values {
  display: flex;
  align-items: center;
  gap: 16px;
}

.category-amount {
  font-size: 14px;
  color: #6b7280;
  min-width: 80px;
  text-align: right;
}

.category-percentage {
  font-size: 14px;
  color: #1f2937;
  font-weight: 700;
  min-width: 40px;
  text-align: right;
}

.bottom-row {
  display: grid;
  grid-template-columns: 1fr 1.5fr;
  gap: 20px;
}

.activity-card,
.products-card {
  background: white;
  padding: 24px;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.card-header h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
}

.activity-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.activity-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 16px;
  border-bottom: 1px solid #e5e7eb;
}

.activity-item:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.activity-title {
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 4px;
}

.activity-meta {
  font-size: 12px;
  color: #6b7280;
}

.activity-badge {
  padding: 6px 12px;
  border: none;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
}

.activity-badge.new-order {
  background: #dbeafe;
  color: #1e40af;
}

.products-table {
  overflow-x: auto;
}

.products-table table {
  width: 100%;
  border-collapse: collapse;
}

.products-table thead {
  background: #f9fafb;
}

.products-table th {
  padding: 12px;
  text-align: left;
  font-size: 12px;
  font-weight: 600;
  color: #6b7280;
  text-transform: uppercase;
}

.products-table td {
  padding: 12px;
  font-size: 14px;
  color: #1f2937;
  border-top: 1px solid #e5e7eb;
}

.products-table tbody tr:hover {
  background: #f9fafb;
}
</style>