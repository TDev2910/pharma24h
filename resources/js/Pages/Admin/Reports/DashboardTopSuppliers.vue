<template>
  <div class="page-container">
    <h1 class="page-title">Báo cáo nhà cung cấp</h1>

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
            Top 10 nhà cung cấp nhập hàng nhiều nhất ( đã trừ trả hàng )
          </span>
          <span v-else>
            Tỷ trọng số lượng nhập: Top 10 nhà cung cấp
          </span>
        </div>

        <div v-if="filter.viewMode === 'report'" class="products-table-wrapper">
          <div class="products-table">
            <table>
              <thead>
                <tr>
                  <th>STT</th>
                  <th>Tên nhà cung cấp</th>
                  <th>Email</th>
                  <th>Tên công ty</th>
                  <th>Mã số thuế</th>
                  <th>Số lượng nhập hàng</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(supplier, index) in filteredSuppliers" :key="`${supplier.id}-${index}`">
                  <td>{{ index + 1 }}</td>
                  <td>{{ supplier.name }}</td>
                  <td>{{ supplier.email }}</td>
                  <td>{{ supplier.company_name }}</td>
                  <td>{{ supplier.tax_code }}</td>
                  <td class="text-right"><strong>{{ supplier.total_imported.toLocaleString() }}</strong></td>
                </tr>
                <tr v-if="filteredSuppliers.length === 0">
                  <td colspan="6" class="empty-state">
                    Chưa có dữ liệu
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div v-else class="chart-container pie-chart-mode">
          <Chart type="pie" :data="chartData" :options="chartOptions" class="w-full md:w-[30rem] mx-auto"
            style="height: 400px;" />
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
  name: 'TopSuppliersReport',
  components: {
    Chart
  },
  props: {
    // Dữ liệu giả lập (Fallback) nếu cha không truyền vào
    topSuppliers: {
      type: Array,
      default: () => [
        { id: 1, name: 'Nhà cung cấp 1', email: 'nha_cung_cap1@gmail.com', phone: '0909090909', order_count: 10, total_spent: 1000000 },
        { id: 2, name: 'Nhà cung cấp 2', email: 'nha_cung_cap2@gmail.com', phone: '0909090909', order_count: 8, total_spent: 800000 },
        { id: 3, name: 'Nhà cung cấp 3', email: 'nha_cung_cap3@gmail.com', phone: '0909090909', order_count: 6, total_spent: 600000 },
        { id: 4, name: 'Nhà cung cấp 4', email: 'nha_cung_cap4@gmail.com', phone: '0909090909', order_count: 4, total_spent: 400000 },
        { id: 5, name: 'Nhà cung cấp 5', email: 'nha_cung_cap5@gmail.com', phone: '0909090909', order_count: 2, total_spent: 200000 },
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
            display: true,
            position: 'bottom', // Đặt chú thích xuống dưới đáy
            labels: {
              usePointStyle: true, // Dùng dấu chấm tròn đẹp hơn
              padding: 20,
              font: {
                size: 14
              }
            }
          },
          // Cấu hình Tooltip để hiện %
          tooltip: {
            callbacks: {
              label: function (context) {
                let label = context.label || '';
                let value = context.raw || 0;
                // Lấy tổng giá trị của cả biểu đồ tròn
                let total = context.chart._metasets[context.datasetIndex].total;
                // Tính phần trăm
                let percentage = total > 0 ? Math.round((value / total) * 100) : 0;

                // Format số lượng cho đẹp (ví dụ: 1,000)
                let formattedValue = new Intl.NumberFormat('vi-VN').format(value);

                // Kết quả: "Nhà cung cấp A: 1,000 (40%)"
                return `${label}: ${formattedValue} (${percentage}%)`;
              }
            }
          },
          // Tiêu đề nhỏ trên biểu đồ
          title: {
            display: true,
            text: 'Tổng số lượng nhập theo nhà cung cấp',
            padding: { bottom: 20 },
            font: { weight: 'normal' }
          }
        }
      }
    };
  },
  computed: {
    // Thêm computed property để filter khách hàng
    filteredSuppliers() {
      let suppliers = this.topSuppliers || [];

      if (this.filter.searchQuery) {
        const query = this.filter.searchQuery.toLowerCase();
        suppliers = suppliers.filter(s =>
          (s.name && s.name.toLowerCase().includes(query)) ||
          (s.email && s.email.toLowerCase().includes(query)) ||
          (s.company_name && s.company_name.toLowerCase().includes(query)) ||
          (s.tax_code && s.tax_code.toString().includes(query))
        );
      }

      return suppliers;
    },

    // XỬ LÝ DỮ LIỆU CHO BIỂU ĐỒ TRÒN (PIE)
    chartData() {
      const suppliers = this.filteredSuppliers;

      // Lấy top 10 nhà cung cấp (hoặc tất cả nếu ít hơn 10)
      const topSuppliers = suppliers.slice(0, 10);

      // Kiểm tra nếu không có dữ liệu
      if (topSuppliers.length === 0) {
        return {
          labels: ['Không có dữ liệu'],
          datasets: [{ data: [0], backgroundColor: ['#e0e0e0'] }]
        };
      }

      // Tạo mảng màu sắc cho các nhà cung cấp
      const colors = [
        '#42A5F5', '#FFCA28', '#66BB6A', '#EF5350', '#AB47BC',
        '#26A69A', '#FFA726', '#5C6BC0', '#EC407A', '#78909C'
      ];

      // Cấu trúc dữ liệu cho Pie Chart
      return {
        labels: topSuppliers.map(s => s.name), // Tên các nhà cung cấp
        datasets: [
          {
            data: topSuppliers.map(s => s.total_imported || 0), // Số lượng nhập hàng
            backgroundColor: topSuppliers.map((_, index) => colors[index % colors.length]),
            hoverBackgroundColor: topSuppliers.map((_, index) => {
              // Màu hover sáng hơn
              const baseColor = colors[index % colors.length];
              // Có thể điều chỉnh độ sáng ở đây nếu cần
              return baseColor;
            }),
            borderWidth: 2,
            borderColor: '#ffffff'
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

/* Container chứa biểu đồ tròn */
.chart-container {
  padding: 20px;
  background: white;
  border-radius: 8px;
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Style riêng khi ở chế độ Pie Chart */
.pie-chart-mode {
  /* Có thể thêm background nhẹ để làm nổi bật biểu đồ tròn */
}

/* Class tiện ích để set chiều cao/rộng */
.w-\[30rem\] {
  width: 30rem;
}

.md\:w-\[30rem\] {
  @media (min-width: 768px) {
    width: 30rem;
  }
}

.mx-auto {
  margin-left: auto;
  margin-right: auto;
}

.w-full {
  width: 100%;
}
</style>