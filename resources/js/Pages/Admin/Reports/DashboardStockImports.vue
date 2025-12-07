<template>
  <div class="page-container">
    <h1 class="page-title">Báo cáo nhập hàng</h1>

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
          <label class="label">Hàng hóa (Lọc dữ liệu biểu đồ)</label>
          <input type="text" placeholder="Theo mã, tên hàng" class="form-input search-input"
            v-model="filter.searchQuery">
        </div>
        <div class="filter-group" v-if="filter.viewMode === 'report'">
          <label class="label">Loại sản phẩm</label>
          <select v-model="filter.productType" class="form-input">
            <option value="">Tất cả</option>
            <option value="medicine">Thuốc</option>
            <option value="goods">Vật tư y tế</option>
          </select>
        </div>
      </aside>

      <main class="main-content">
        <div class="content-header-text">
          <span v-if="filter.viewMode === 'report'">
            Top 10 sản phẩm đặt hàng nhiều nhất
          </span>
          <span v-else>
            Tỷ trọng số lượng nhập: Thuốc vs Vật tư y tế
          </span>
        </div>

        <div v-if="filter.viewMode === 'report'" class="products-table-wrapper">
          <div class="products-table">
            <table>
              <thead>
                <tr>
                  <th>STT</th>
                  <th>Tên sản phẩm</th>
                  <th>Loại</th>
                  <th>Giá bán</th>
                  <th>Tồn kho</th>
                  <th>Số lượng đã nhập</th>
                  <th>Tổng chi phí</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(product, index) in filteredProducts" :key="`${product.type}-${product.id}`">
                  <td>{{ index + 1 }}</td>
                  <td>{{ product.name }}</td>
                  <td>
                    <span :class="getTypeBadgeClass(product.type)">
                      {{ getTypeLabel(product.type) }}
                    </span>
                  </td>
                  <td>{{ formatCurrency(product.price) }}</td>
                  <td>{{ product.stocks.toLocaleString() }}</td>
                  <td><strong>{{ product.imported.toLocaleString() }}</strong></td>
                  <td><strong class="cost">{{ formatCurrency(product.total_cost) }}</strong></td>
                </tr>
                <tr v-if="filteredProducts.length === 0">
                  <td colspan="7" class="empty-state">
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
import Chart from 'primevue/chart';

export default {
  name: 'StockImportsPieReport',
  components: {
    Chart
  },
  props: {
    topProducts: {
      type: Array,
      // Dữ liệu mẫu để test tỷ trọng
      default: () => [
        { id: 1, name: 'Panadol', type: 'medicine', imported: 600, price: 10000, stocks: 100, total_cost: 6000000 },
        { id: 2, name: 'Khẩu trang 4D', type: 'goods', imported: 1000, price: 5000, stocks: 5000, total_cost: 5000000 },
        { id: 3, name: 'Efferalgan', type: 'medicine', imported: 400, price: 15000, stocks: 200, total_cost: 6000000 },
        { id: 4, name: 'Bông băng', type: 'goods', imported: 500, price: 2000, stocks: 1000, total_cost: 1000000 },
        // Tổng Thuốc: 600 + 400 = 1000
        // Tổng VTYT: 1000 + 500 = 1500
        // Tổng cộng: 2500 -> Thuốc chiếm 40%, VTYT chiếm 60%
      ]
    }
  },
  data() {
    return {
      filter: {
        viewMode: 'chart',
        timeRange: 'week',
        customDateStart: '',
        searchQuery: '',
        productType: '' // Filter này chỉ dùng cho Table view
      },
      // CẤU HÌNH BIỂU ĐỒ TRÒN (PIE)
      chartOptions: {
        responsive: true,
        maintainAspectRatio: false, // Để class w-[30rem] điều khiển kích thước
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

                // Kết quả: "Thuốc: 1,000 (40%)"
                return `${label}: ${formattedValue} (${percentage}%)`;
              }
            }
          },
          // Tiêu đề nhỏ trên biểu đồ (tùy chọn)
          title: {
            display: true,
            text: 'Tổng số lượng nhập theo loại',
            padding: { bottom: 20 },
            font: { weight: 'normal' }
          }
        },
        // QUAN TRỌNG: Xóa bỏ 'scales' vì Pie chart không có trục X, Y
      }
    };
  },
  computed: {
    // Lọc dữ liệu (Dùng chung cho cả Table và Chart)
    filteredProducts() {
      let products = this.topProducts;

      // Lưu ý: Khi xem biểu đồ tròn tỷ trọng, ta thường không lọc theo 'productType'
      // vì cần cả 2 loại để so sánh. Logic lọc productType chỉ áp dụng khi xem Báo cáo.
      if (this.filter.viewMode === 'report' && this.filter.productType) {
        products = products.filter(p => p.type === this.filter.productType);
      }

      // Vẫn cho phép lọc theo tên/mã hàng để xem tỷ trọng của một nhóm hàng cụ thể
      if (this.filter.searchQuery) {
        const query = this.filter.searchQuery.toLowerCase();
        products = products.filter(p =>
          p.name.toLowerCase().includes(query) ||
          p.id.toString().includes(query)
        );
      }

      return products;
    },

    // XỬ LÝ DỮ LIỆU CHO BIỂU ĐỒ TRÒN (Tính tổng gộp)
    chartData() {
      const filtered = this.filteredProducts;

      // 1. Tính tổng số lượng nhập của THUỐC
      const totalMedicine = filtered
        .filter(p => p.type === 'medicine')
        .reduce((sum, product) => sum + (product.imported || 0), 0);

      // 2. Tính tổng số lượng nhập của VẬT TƯ Y TẾ
      const totalGoods = filtered
        .filter(p => p.type === 'goods')
        .reduce((sum, product) => sum + (product.imported || 0), 0);

      // Kiểm tra nếu không có dữ liệu
      if (totalMedicine === 0 && totalGoods === 0) {
        // Trả về dữ liệu rỗng để tránh lỗi biểu đồ
        return {
          labels: ['Không có dữ liệu'],
          datasets: [{ data: [0], backgroundColor: ['#e0e0e0'] }]
        };
      }

      // 3. Cấu trúc dữ liệu cho Pie Chart
      return {
        labels: ['Thuốc', 'Vật tư y tế'], // Nhãn cố định
        datasets: [
          {
            data: [totalMedicine, totalGoods], // Dữ liệu tổng đã tính
            backgroundColor: [
              '#42A5F5', // Màu Xanh cho Thuốc (khớp với vị trí số 1 trong data)
              '#FFCA28'  // Màu Vàng cho VTYT (khớp với vị trí số 2 trong data)
            ],
            hoverBackgroundColor: [
              '#64B5F6', // Màu hover sáng hơn chút
              '#FFD54F'
            ],
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
    getTypeLabel(type) {
      const labels = { 'medicine': 'Thuốc', 'goods': 'Vật tư y tế' };
      return labels[type] || type;
    },
    getTypeBadgeClass(type) {
      return {
        'badge': true,
        'badge-medicine': type === 'medicine',
        'badge-goods': type === 'goods'
      };
    }
  }
};
</script>

<style scoped>
/* Giữ nguyên các style cũ */
.page-container {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  color: #333;
  padding: 20px;
  background-color: #f9f9f9;
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
  gap: 0;
  background: white;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  min-height: 600px;
}

.sidebar {
  width: 260px;
  flex-shrink: 0;
  border-right: 1px solid #e0e0e0;
  padding: 20px;
  background: #fff;
}

.filter-group {
  margin-bottom: 20px;
}

.label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: #333;
  margin-bottom: 8px;
}

.toggle-container {
  background-color: #f0f2f5;
  border-radius: 18px;
  padding: 2px;
  display: flex;
}

.toggle-btn {
  flex: 1;
  border: none;
  background: transparent;
  padding: 6px 0;
  border-radius: 16px;
  font-size: 13px;
  cursor: pointer;
  color: #666;
  transition: all 0.2s;
}

.toggle-btn.active {
  background-color: #007bff;
  color: white;
  font-weight: 500;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.form-input {
  width: 100%;
  padding: 8px 10px;
  border: 1px solid #d9d9d9;
  border-radius: 4px;
  font-size: 14px;
  outline: none;
  box-sizing: border-box;
}

.form-input:focus {
  border-color: #40a9ff;
}

.radio-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.radio-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 10px;
  border: 1px solid #e5e5e5;
  border-radius: 4px;
  cursor: pointer;
  transition: background 0.2s;
}

.radio-item:hover {
  background-color: #f5f5f5;
}

.radio-item.selected {
  border-color: #40a9ff;
  background-color: #e6f7ff;
}

.radio-left {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
}

.icon-arrow,
.icon-calendar {
  color: #999;
  font-size: 14px;
}

.main-content {
  flex: 1;
  padding: 24px;
  background-color: #fff;
  display: flex;
  flex-direction: column;
}

.content-header-text {
  text-align: center;
  color: #666;
  font-size: 14px;
  margin-bottom: 24px;
}

.products-table-wrapper {
  overflow-x: auto;
}

.products-table table {
  width: 100%;
  border-collapse: collapse;
}

.products-table th {
  background-color: #f9fafb;
  text-align: left;
  padding: 12px 16px;
  font-size: 13px;
  font-weight: 600;
  color: #374151;
  border-bottom: 1px solid #e5e7eb;
}

.products-table td {
  padding: 12px 16px;
  border-bottom: 1px solid #e5e7eb;
  font-size: 13px;
  color: #374151;
}

.products-table tr:hover td {
  background-color: #f9fafb;
}

.badge {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 500;
}

.badge-medicine {
  background-color: #dbeafe;
  color: #1e40af;
}

.badge-goods {
  background-color: #fef3c7;
  color: #92400e;
}

.cost {
  color: #dc2626;
}

.empty-state {
  text-align: center;
  padding: 30px;
  color: #999;
}

/* Container chứa biểu đồ */
.chart-container {
  padding: 20px;
  background: white;
  border-radius: 8px;
  flex: 1;
  /* Để nó chiếm hết chiều cao còn lại */
  display: flex;
  /* Để căn giữa biểu đồ tròn */
  align-items: center;
  justify-content: center;
}

/* Style riêng khi ở chế độ Pie Chart để căn giữa đẹp hơn */
.pie-chart-mode {
  /* Có thể thêm background nhẹ để làm nổi bật biểu đồ tròn */
  /* background-color: #f8f9fa; */
}

/* Class tiện ích để set chiều cao/rộng như yêu cầu (nếu không dùng Tailwind) */
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