<template>
  <div class="page-container">
    <h1 class="page-title">Báo cáo khách hàng</h1>

    <div class="layout-wrapper">

      <aside class="sidebar">

        <div class="filter-group">
          <label class="label">Kiểu hiển thị</label>
          <div class="toggle-container">
            <button @click="filter.viewMode = 'chart'"
              :class="['toggle-btn', filter.viewMode === 'chart' ? 'active' : '']">
              Biểu đồ
            </button>
            <button @click="filter.viewMode = 'report'"
              :class="['toggle-btn', filter.viewMode === 'report' ? 'active' : '']">
              Báo cáo
            </button>
          </div>
        </div>

        <div class="filter-group">
          <label class="label">Thời gian</label>
          <div class="radio-list">
            <label class="radio-item" :class="{ 'selected': filter.timeRange === 'week' }">
              <div class="radio-left">
                <input type="radio" value="week" v-model="filter.timeRange">
                <span>Tuần này</span>
              </div>
              <span class="icon-arrow">›</span>
            </label>

            <label class="radio-item" :class="{ 'selected': filter.timeRange === 'custom' }">
              <div class="radio-left">
                <input type="radio" value="custom" v-model="filter.timeRange">
                <span>Tùy chỉnh</span>
              </div>
              <span class="icon-calendar">📅</span>
            </label>
          </div>
          <transition name="fade">
            <div v-if="filter.timeRange === 'custom'" class="mt-2">
              <input type="date" class="form-input" v-model="filter.customDateStart">
            </div>
          </transition>
        </div>

        <div class="filter-group">
          <label class="label">Khách hàng</label>
          <input type="text" placeholder="Theo tên, email, số điện thoại" class="form-input search-input"
            v-model="filter.searchQuery">
        </div>
        <div class="sidebar-collapse-btn">
          <span class="arrow-left">‹</span>
        </div>
      </aside>


      <main class="main-content">

        <div class="content-header-text">
          <span v-if="filter.viewMode === 'report'">
            Top 10 khách hàng mua nhiều nhất
          </span>
          <span v-else>
            Biểu đồ biến động doanh thu Top 10 khách hàng mua nhiều nhất
          </span>
        </div>

        <div v-if="filter.viewMode === 'report'" class="products-table-wrapper">
          <div class="products-table">
            <table>
              <thead>
                <tr>
                  <th>STT</th>
                  <th>Tên khách hàng</th>
                  <th>Email</th>
                  <th>Số điện thoại</th>
                  <th>Số lượng đơn hàng</th>
                  <th class="text-right">Doanh thu</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(customer, index) in filteredCustomers" :key="`${customer.id}-${index}`">
                  <td>{{ index + 1 }}</td>
                  <td>{{ customer.name }}</td>
                  <td>{{ customer.email }}</td>
                  <td>{{ customer.phone }}</td>
                  <td>{{ customer.order_count.toLocaleString() }}</td>
                  <td class="text-right"><strong class="earnings">{{ formatCurrency(customer.total_spent) }}</strong>
                  </td>
                </tr>
                <tr v-if="filteredCustomers.length === 0">
                  <td colspan="6" class="empty-state">
                    Chưa có dữ liệu
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div v-else class="chart-view-container">
          <div class="chart-wrapper">
            <Chart type="bar" :data="chartData" :options="chartOptions" style="height: 400px;" class="h-full" />
          </div>
        </div>

      </main>
    </div>
  </div>
</template>

<script>
// Import Component Chart từ PrimeVue
// Đảm bảo bạn đã cài đặt: npm install primevue chart.js
import Chart from 'primevue/chart';

export default {
  name: 'GoodsReport',
  components: {
    Chart
  },
  props: {
    // Dữ liệu giả lập (Fallback) nếu cha không truyền vào
    topCustomers: {
      type: Array,
      default: () => [
        { id: 1, name: 'Nguyễn Văn A', email: 'nguyenvana@gmail.com', phone: '0909090909', order_count: 10, total_spent: 1000000 },
        { id: 2, name: 'Nguyễn Thị B', email: 'nguyenthib@gmail.com', phone: '0909090909', order_count: 8, total_spent: 800000 },
        { id: 3, name: 'Nguyễn Văn C', email: 'nguyenvanc@gmail.com', phone: '0909090909', order_count: 6, total_spent: 600000 },
        { id: 4, name: 'Nguyễn Thị D', email: 'nguyenthid@gmail.com', phone: '0909090909', order_count: 4, total_spent: 400000 },
        { id: 5, name: 'Nguyễn Văn E', email: 'nguyenvane@gmail.com', phone: '0909090909', order_count: 2, total_spent: 200000 },
      ]
    }
  },
  data() {
    return {
      filter: {
        viewMode: 'chart',
        isMerge: false,
        concern: 'sales',
        timeRange: 'week',
        customDateStart: '',
        searchQuery: ''
      },
      // Cấu hình Chart.js
      chartOptions: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false // Ẩn legend mặc định, dùng custom legend bên trên
          },
          tooltip: {
            callbacks: {
              label: function (context) {
                let value = context.raw;
                return ' ' + new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function (value) {
                // Format trục Y dạng rút gọn (1tr, 2tr) nếu số quá lớn
                if (value >= 1000000) return (value / 1000000) + 'M';
                if (value >= 1000) return (value / 1000) + 'k';
                return value;
              }
            }
          },
          x: {
            ticks: {
              autoSkip: false,
              maxRotation: 45,
              minRotation: 0
            }
          }
        }
      }
    };
  },
  computed: {
    // Thêm computed property để filter khách hàng
    filteredCustomers() {
      let customers = this.topCustomers || [];

      // Lọc theo tìm kiếm (tên, email, số điện thoại)
      if (this.filter.searchQuery) {
        const query = this.filter.searchQuery.toLowerCase();
        customers = customers.filter(c =>
          (c.name && c.name.toLowerCase().includes(query)) ||
          (c.email && c.email.toLowerCase().includes(query)) ||
          (c.phone && c.phone.toString().includes(query))
        );
      }

      return customers;
    },

    // XỬ LÝ DỮ LIỆU CHO BIỂU ĐỒ
    chartData() {
      // Sử dụng filteredCustomers thay vì topCustomers để biểu đồ cũng được filter
      const customers = this.filteredCustomers;

      // 1. Labels: Tên khách hàng
      const labels = customers.map(c => c.name);

      // 2. Data: Doanh thu
      const dataValues = customers.map(c => c.total_spent);

      // 3. Colors: Xanh cho tất cả
      const backgroundColors = customers.map(() => '#42A5F5');

      return {
        labels: labels,
        datasets: [
          {
            label: 'Doanh thu',
            data: dataValues,
            backgroundColor: backgroundColors,
            borderRadius: 4,
            barPercentage: 0.6
          }
        ]
      };
    }
  },
  methods: {
    formatCurrency(value) {
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
    },
  }
};
</script>

<style scoped>
/* ================= 1. LAYOUT CHUNG ================= */
.page-container {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  color: #333;
  padding: 20px;
  background-color: #f5f7fa;
  min-height: 100vh;
}

.page-title {
  font-size: 20px;
  font-weight: 700;
  margin-bottom: 20px;
  color: #111;
}

.layout-wrapper {
  display: flex;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  overflow: hidden;
  min-height: 600px;
  /* Chiều cao tối thiểu của khung report */
}

/* ================= 2. SIDEBAR ================= */
.sidebar {
  width: 280px;
  flex-shrink: 0;
  border-right: 1px solid #eee;
  padding: 24px;
  position: relative;
  background: #fff;
}

.filter-group {
  margin-bottom: 24px;
}

.label {
  display: block;
  font-size: 14px;
  font-weight: 600;
  color: #333;
  margin-bottom: 10px;
}

/* Toggle Pill Button */
.toggle-container {
  background-color: #f1f3f5;
  border-radius: 20px;
  padding: 4px;
  display: flex;
}

.toggle-btn {
  flex: 1;
  border: none;
  background: transparent;
  padding: 8px 0;
  border-radius: 16px;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  color: #666;
  transition: all 0.2s;
}

.toggle-btn.active {
  background-color: #007bff;
  color: white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Inputs */
.form-input {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 14px;
  outline: none;
  box-sizing: border-box;
}

.form-input:focus {
  border-color: #007bff;
}

.checkbox-group {
  display: flex;
  align-items: center;
  font-size: 14px;
}

.checkbox-group input {
  margin-right: 8px;
  width: 16px;
  height: 16px;
}

/* Radio List */
.radio-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.radio-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 12px;
  border: 1px solid #eee;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.2s;
}

.radio-item:hover {
  background-color: #f9f9f9;
}

.radio-item.selected {
  border-color: #007bff;
  background-color: #eef7ff;
}

.radio-left {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 14px;
}

.icon-arrow,
.icon-calendar {
  color: #999;
  font-size: 12px;
}

.sidebar-collapse-btn {
  position: absolute;
  top: 50%;
  right: -12px;
  width: 24px;
  height: 24px;
  background: white;
  border: 1px solid #eee;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #007bff;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

/* ================= 3. MAIN CONTENT ================= */
.main-content {
  flex: 1;
  padding: 30px;
  background-color: #fff;
  display: flex;
  flex-direction: column;
}

.content-header-text {
  text-align: center;
  color: #666;
  font-size: 15px;
  margin-bottom: 30px;
  font-weight: 500;
}

/* --- TABLE STYLES --- */
.products-table-wrapper {
  overflow-x: auto;
}

.products-table table {
  width: 100%;
  border-collapse: collapse;
}

.products-table th {
  background-color: #f8f9fa;
  text-align: left;
  padding: 14px 16px;
  font-size: 13px;
  font-weight: 700;
  color: #495057;
  border-bottom: 2px solid #e9ecef;
}

.products-table td {
  padding: 14px 16px;
  border-bottom: 1px solid #f1f3f5;
  font-size: 14px;
  color: #333;
}

.products-table tr:hover td {
  background-color: #f8f9fa;
}

/* Badges */
.badge {
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
}

.badge-medicine {
  background-color: #e3f2fd;
  color: #1976d2;
}

.badge-goods {
  background-color: #fff8e1;
  color: #f57f17;
}

.earnings {
  color: #28a745;
}

.text-right {
  text-align: right;
}

.empty-state {
  text-align: center;
  padding: 40px;
  color: #999;
}

/* --- CHART STYLES --- */
.chart-view-container {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-height: 400px;
}

.chart-legend {
  display: flex;
  justify-content: center;
  gap: 24px;
  margin-bottom: 20px;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: #555;
  font-weight: 500;
}

.color-box {
  width: 16px;
  height: 16px;
  border-radius: 4px;
}

.bg-blue {
  background-color: #42A5F5;
}

.bg-yellow {
  background-color: #FFCA28;
}

.chart-wrapper {
  flex: 1;
  position: relative;
  min-height: 400px;
}
</style>