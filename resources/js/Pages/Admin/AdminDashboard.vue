<template>
  <div class="dashboard-container">
    <!-- Main Layout -->
    <div class="dashboard-layout">
      <!-- Main Content -->
      <main class="main-content">

        <!-- Stats Cards -->
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
        <!-- Service Chart Row -->
        <div class="charts-row service-chart-row">
          <!-- Service Chart -->
          <div class="chart-card">
            <div class="chart-header">
              <h3>Tổng quan doanh thu</h3>
              <div class="chart-controls">
                <button class="chart-btn" :class="{ active: totalRevenuePeriod === 'day' }"
                  @click="setTotalRevenuePeriod('day')">
                  Ngày
                </button>
                <button class="chart-btn" :class="{ active: totalRevenuePeriod === 'week' }"
                  @click="setTotalRevenuePeriod('week')">
                  Tuần
                </button>
                <button class="chart-btn" :class="{ active: totalRevenuePeriod === 'month' }"
                  @click="setTotalRevenuePeriod('month')">
                  Tháng
                </button>
                <button class="chart-btn" :class="{ active: totalRevenuePeriod === 'year' }"
                  @click="setTotalRevenuePeriod('year')">
                  Năm
                </button>
              </div>
            </div>
            <Chart type="bar" :data="totalRevenueChartData" :options="totalRevenueChartOptions" />
          </div>
        </div>
        <!-- Charts Row -->
        <div class="charts-row">
          <!-- Sales Revenue Chart -->
          <div class="chart-card">
            <div class="chart-header">
              <h3>Doanh thu đơn hàng</h3>
              <div class="chart-controls">
                <button class="chart-btn" :class="{ active: orderRevenuePeriod === 'day' }"
                  @click="setOrderRevenuePeriod('day')">
                  Ngày
                </button>
                <button class="chart-btn" :class="{ active: orderRevenuePeriod === 'week' }"
                  @click="setOrderRevenuePeriod('week')">
                  Tuần
                </button>
                <button class="chart-btn" :class="{ active: orderRevenuePeriod === 'month' }"
                  @click="setOrderRevenuePeriod('month')">
                  Tháng
                </button>
                <button class="chart-btn" :class="{ active: orderRevenuePeriod === 'year' }"
                  @click="setOrderRevenuePeriod('year')">
                  Năm
                </button>
              </div>
            </div>
            <!-- Biểu đồ Doanh thu Đơn hàng -->
            <Chart v-if="!loadingOrderRevenue" type="bar" :data="orderRevenueChartData"
              :options="orderRevenueChartOptions" />
            <div v-else class="chart-loading">Đang tải dữ liệu...</div>
            <!-- Biểu đồ Doanh thu dịch vụ -->
            <div class="chart-card">
              <div class="chart-header">
                <h3>Doanh thu dịch vụ</h3>
                <div class="chart-controls">
                  <button class="chart-btn" :class="{ active: serviceRevenuePeriod === 'day' }"
                    @click="setServiceRevenuePeriod('day')">
                    Ngày
                  </button>
                  <button class="chart-btn" :class="{ active: serviceRevenuePeriod === 'week' }"
                    @click="setServiceRevenuePeriod('week')">
                    Tuần
                  </button>
                  <button class="chart-btn" :class="{ active: serviceRevenuePeriod === 'month' }"
                    @click="setServiceRevenuePeriod('month')">
                    Tháng
                  </button>
                  <button class="chart-btn" :class="{ active: serviceRevenuePeriod === 'year' }"
                    @click="setServiceRevenuePeriod('year')">
                    Năm
                  </button>
                </div>
              </div>
              <Chart v-if="!loadingServiceRevenue" type="bar" :data="serviceRevenueChartData"
                :options="serviceRevenueChartOptions" />
              <div v-else class="chart-loading">Đang tải dữ liệu...</div>
            </div>
          </div>


          <!-- Top Categories Chart -->
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

        <!-- Bottom Row -->
        <div class="bottom-row">
          <!-- Recent Activity -->
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

          <!-- Top Products -->
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
import axios from 'axios';

export default {
  name: 'AdminDashboard',
  components: {
    Chart
  },
  props: {
    topCustomers: {
      type: Array,
      default: () => [],
    },
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
      orderRevenuePeriod: 'month',
      serviceRevenuePeriod: 'month',
      totalRevenuePeriod: 'month',
      loadingOrderRevenue: false,
      loadingServiceRevenue: false,
      loadingTotalRevenue: false,
      orderRevenueData: {
        labels: [],
        revenues: []
      },
      serviceRevenueData: {
        labels: [],
        revenues: []
      },
      totalRevenueData: {
        labels: [],
        revenues: []
      },
      // Total Revenue Chart Options
      totalRevenueChartOptions: {
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            callbacks: {
              label: (context) => {
                return this.formatCurrency(context.parsed.y);
              }
            }
          }
        },
        scales: {
          x: {
            grid: {
              display: false
            }
          },
          y: {
            beginAtZero: true,
            ticks: {
              callback: (value) => {
                return this.formatCurrency(value);
              }
            }
          }
        },
        maintainAspectRatio: false
      },
      // Service Revenue Chart Options
      orderRevenueChartOptions: {
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            callbacks: {
              label: (context) => {
                return this.formatCurrency(context.parsed.y);
              }
            }
          }
        },
        scales: {
          x: {
            grid: {
              display: false
            }
          },
          y: {
            beginAtZero: true,
            ticks: {
              callback: (value) => {
                return this.formatCurrency(value);
              }
            }
          }
        },
        maintainAspectRatio: false
      },
      // Service Revenue Chart Options
      serviceRevenueChartOptions: {
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            callbacks: {
              label: (context) => {
                return this.formatCurrency(context.parsed.y);
              }
            }
          }
        },
        scales: {
          x: {
            grid: {
              display: false
            }
          },
          y: {
            beginAtZero: true,
            ticks: {
              callback: (value) => {
                return this.formatCurrency(value);
              }
            }
          }
        },
        maintainAspectRatio: false
      },

      // Bar Chart Data for Sales Revenue (old - có thể xóa sau)
      barChartData: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'],
        datasets: [
          {
            label: 'One-Time Revenue',
            data: [6000, 8000, 12000, 6000, 10000, 15000, 18000, 20000],
            backgroundColor: '#93c5fd'
          },
          {
            label: 'Recurring Revenue',
            data: [15000, 18000, 20000, 25000, 22000, 25000, 28000, 30000],
            backgroundColor: '#3b82f6'
          }
        ]
      },
      barChartOptions: {
        plugins: {
          legend: {
            position: 'top',
            labels: {
              usePointStyle: true,
              padding: 15
            }
          },
          tooltip: {
            callbacks: {
              label: function (context) {
                let label = context.dataset.label || '';
                if (label) {
                  label += ': ';
                }
                label += '$' + context.parsed.y.toLocaleString();
                return label;
              }
            }
          }
        },
        scales: {
          x: {
            stacked: false,
            grid: {
              display: false
            }
          },
          y: {
            stacked: false,
            beginAtZero: true,
            ticks: {
              callback: function (value) {
                return '$' + value.toLocaleString();
              }
            }
          }
        },
        maintainAspectRatio: false
      },
      chartOptions: {
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            callbacks: {
              label: function (context) {
                let label = context.label || '';
                if (label) {
                  label += ': ';
                }
                label += '$' + context.parsed.toLocaleString();
                return label;
              }
            }
          }
        },
        cutout: '60%',
        maintainAspectRatio: false
      },
      // Service Chart Data
      serviceChartData: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'],
        datasets: [
          {
            label: 'Service Revenue',
            data: [5000, 7000, 9000, 8000, 11000, 13000, 15000, 17000],
            backgroundColor: '#10b981'
          },
          {
            label: 'Service Orders',
            data: [12000, 14000, 16000, 15000, 18000, 20000, 22000, 24000],
            backgroundColor: '#059669'
          }
        ]
      },
      serviceChartOptions: {
        plugins: {
          legend: {
            position: 'top',
            labels: {
              usePointStyle: true,
              padding: 15
            }
          },
          tooltip: {
            callbacks: {
              label: function (context) {
                let label = context.dataset.label || '';
                if (label) {
                  label += ': ';
                }
                label += '$' + context.parsed.y.toLocaleString();
                return label;
              }
            }
          }
        },
        scales: {
          x: {
            stacked: false,
            grid: {
              display: false
            }
          },
          y: {
            stacked: false,
            beginAtZero: true,
            ticks: {
              callback: function (value) {
                return '$' + value.toLocaleString();
              }
            }
          }
        },
        maintainAspectRatio: false
      }
    };
  },
  computed: {
    // Cập nhật chartData với dữ liệu thực
    chartData() {
      return {
        labels: ['Thuốc', 'Vật tư y tế', 'Dịch vụ'],
        datasets: [
          {
            data: [
              this.categoryStats.medicineCount,
              this.categoryStats.goodsCount,
              this.categoryStats.serviceCount
            ],
            backgroundColor: [
              '#3b82f6',
              '#10b981',
              '#f59e0b'
            ],
            borderWidth: 0
          }
        ]
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
      return {
        labels: this.totalRevenueData.labels,
        datasets: [
          {
            label: 'Doanh thu tổng quan',
            data: this.totalRevenueData.revenues,
            backgroundColor: '#3b82f6'
          }
        ]
      };
    },
    // Computed cho Order Revenue Chart
    orderRevenueChartData() {
      return {
        labels: this.orderRevenueData.labels,
        datasets: [
          { 
            label: 'Doanh thu đơn hàng',
            data: this.orderRevenueData.revenues,
            backgroundColor: '#3b82f6'
          }
        ]
      };
    },
    serviceRevenueChartData() {
      return {
        labels: this.serviceRevenueData.labels,
        datasets: [
          {
            label: 'Doanh thu dịch vụ',
            data: this.serviceRevenueData.revenues,
            backgroundColor: '#A8CD89'
          }
        ]
      };
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
      this.loadingTotalRevenue = true;
      try {
        const response = await axios.get('/admin/dashboard/revenue/total', {
          params: { period }
        });

        if (response.data.success) {
          this.totalRevenueData = {
            labels: response.data.data.labels,
            revenues: response.data.data.revenues
          };
        }
      } catch (error) {
        console.error('Error fetching total revenue:', error);
        this.totalRevenueData = {
          labels: [],
          revenues: []
        };
      } finally {
        this.loadingTotalRevenue = false;
      }
    },
    // Fetch doanh thu đơn hàng từ API
    async fetchOrderRevenue(period) {
      this.loadingOrderRevenue = true;
      try {
        const response = await axios.get('/admin/dashboard/revenue/orders', {
          params: { period }
        });

        if (response.data.success) {
          this.orderRevenueData = {
            labels: response.data.data.labels,
            revenues: response.data.data.revenues
          };
        }
      } catch (error) {
        console.error('Error fetching order revenue:', error);
        this.orderRevenueData = {
          labels: [],
          revenues: []
        };
      } finally {
        this.loadingOrderRevenue = false;
      }
    },
    async fetchServiceRevenue(period) {
      this.loadingServiceRevenue = true;
      try {
        const response = await axios.get('/admin/dashboard/revenue/services', {
          params: { period }
        });

        if (response.data.success) {
          this.serviceRevenueData = {
            labels: response.data.data.labels,
            revenues: response.data.data.revenues
          };
        }
      } catch (error) {
        console.error('Error fetching service revenue:', error);
        this.serviceRevenueData = {
          labels: [],
          revenues: []
        };
      } finally {
        this.loadingServiceRevenue = false;
      }
    },


    // Set period cho đơn hàng
    setOrderRevenuePeriod(period) {
      this.orderRevenuePeriod = period;
      this.fetchOrderRevenue(period);
    },
    setServiceRevenuePeriod(period) {
      this.serviceRevenuePeriod = period;
      this.fetchServiceRevenue(period);
    },
    setTotalRevenuePeriod(period) {
      this.totalRevenuePeriod = period;
      this.fetchTotalRevenue(period);
    }
  },

  // Lifecycle hook
  mounted() {
    // Fetch dữ liệu ban đầu với period mặc định
    this.fetchOrderRevenue(this.orderRevenuePeriod);
    this.fetchServiceRevenue(this.serviceRevenuePeriod);
  }
};
</script>

<style scoped>
.dashboard-container {
  min-height: 100vh;
  background: #f8f9fa;
}

/* Header Styles */
.dashboard-header {
  background: white;
  border-bottom: 1px solid #e5e7eb;
  position: sticky;
  top: 0;
  z-index: 100;
}

.header-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.header-top-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.header-text {
  font-size: 14px;
  color: #374151;
}

.status-badge {
  font-size: 12px;
  color: #10b981;
  background: #d1fae5;
  padding: 4px 8px;
  border-radius: 4px;
}

.follow-btn {
  background: #f3f4f6;
  border: none;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 12px;
  cursor: pointer;
}

.header-top-right {
  display: flex;
  align-items: center;
  gap: 16px;
}

.header-icon {
  color: #6b7280;
  cursor: pointer;
  font-size: 18px;
}

.header-icon.small {
  font-size: 14px;
}

.header-icon.large {
  font-size: 24px;
}

.get-touch-btn {
  background: #1f2937;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 12px;
  cursor: pointer;
}

.header-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 24px;
}

.header-bottom-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.logo-text {
  font-size: 20px;
  font-weight: 700;
  color: #1f2937;
}

.header-bottom-center {
  display: flex;
  align-items: center;
  gap: 16px;
}

.user-icons {
  display: flex;
  gap: 4px;
}

.search-bar {
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 12px;
  color: #9ca3af;
  font-size: 16px;
}

.search-input {
  padding: 8px 12px 8px 36px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  width: 300px;
  font-size: 14px;
}

/* Layout */
.dashboard-layout {
  display: flex;
}

/* Sidebar Styles */
.sidebar {
  width: 260px;
  background: white;
  border-right: 1px solid #e5e7eb;
  height: calc(100vh - 120px);
  overflow-y: auto;
  padding: 24px 16px;
  display: flex;
  flex-direction: column;
}

.sidebar-logo {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px;
  margin-bottom: 24px;
  font-weight: 700;
  font-size: 18px;
  color: #1f2937;
}

.menu-section {
  margin-bottom: 32px;
}

.menu-title {
  font-size: 11px;
  font-weight: 600;
  color: #9ca3af;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 12px;
  padding: 0 12px;
}

.menu-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px;
  margin-bottom: 4px;
  border-radius: 8px;
  cursor: pointer;
  color: #6b7280;
  transition: all 0.2s;
}

.menu-item:hover {
  background: #f3f4f6;
}

.menu-item.active {
  background: #f3f4f6;
  color: #1f2937;
  font-weight: 600;
}

.menu-item i {
  margin-right: 12px;
  font-size: 18px;
}

.menu-item.has-submenu {
  position: relative;
}

.menu-item-content {
  display: flex;
  align-items: center;
  flex: 1;
}

.submenu {
  display: none;
  margin-top: 4px;
  padding-left: 40px;
}

.submenu-item {
  padding: 8px 12px;
  color: #6b7280;
  font-size: 14px;
  cursor: pointer;
}

.submenu-item:hover {
  color: #1f2937;
}

.sidebar-bottom {
  margin-top: auto;
  padding-top: 24px;
}

.dark-mode-toggle {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px;
  margin-bottom: 16px;
  color: #6b7280;
}

.toggle-switch {
  width: 40px;
  height: 20px;
  background: #e5e7eb;
  border-radius: 10px;
  position: relative;
  cursor: pointer;
}

.toggle-switch::after {
  content: '';
  position: absolute;
  width: 16px;
  height: 16px;
  background: white;
  border-radius: 50%;
  top: 2px;
  left: 2px;
  transition: all 0.2s;
}

.premium-card {
  background: #f3f4f6;
  border-radius: 12px;
  padding: 16px;
}

.premium-title {
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 8px;
}

.premium-text {
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 12px;
}

.upgrade-btn {
  background: #1f2937;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 12px;
  cursor: pointer;
  width: 100%;
}

/* Main Content Styles */
.main-content {
  flex: 1;
  padding: 24px;
  overflow-y: auto;
  height: calc(100vh - 120px);
}

.page-title {
  font-size: 28px;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 24px;
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

.chart-card {
  background: white;
  padding: 24px;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.chart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.chart-header h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
}

.chart-controls {
  display: flex;
  gap: 8px;
}

.chart-btn {
  padding: 6px 12px;
  border: 1px solid #e5e7eb;
  background: white;
  border-radius: 6px;
  font-size: 12px;
  cursor: pointer;
}

.chart-btn.active {
  background: #1f2937;
  color: white;
  border-color: #1f2937;
}

.chart-placeholder {
  height: 300px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f9fafb;
  border-radius: 8px;
  color: #9ca3af;
}

.chart-card :deep(.p-chart) {
  height: 300px;
  width: 100%;
}

.chart-card :deep(canvas) {
  max-height: 300px;
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

.see-all-btn {
  color: #3b82f6;
  background: none;
  border: none;
  font-size: 14px;
  cursor: pointer;
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

.activity-badge.low-stock {
  background: #fef3c7;
  color: #92400e;
}

.activity-badge.campaign {
  background: #e0e7ff;
  color: #3730a3;
}

.activity-badge.system {
  background: #f3f4f6;
  color: #374151;
}

.card-actions {
  display: flex;
  gap: 8px;
}

.action-btn {
  padding: 6px 12px;
  border: 1px solid #e5e7eb;
  background: white;
  border-radius: 6px;
  font-size: 12px;
  cursor: pointer;
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