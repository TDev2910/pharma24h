<template>
  <div class="page-container">
    <h1 class="page-title">Báo cáo hàng hóa</h1>

    <div class="layout-wrapper">
      
      <aside class="sidebar">
        
        <div class="filter-group">
          <label class="label">Kiểu hiển thị</label>
          <div class="toggle-container">
            <button 
              @click="filter.viewMode = 'chart'"
              :class="['toggle-btn', filter.viewMode === 'chart' ? 'active' : '']"
            >
              Biểu đồ
            </button>
            <button 
              @click="filter.viewMode = 'report'"
              :class="['toggle-btn', filter.viewMode === 'report' ? 'active' : '']"
            >
              Báo cáo
            </button>
          </div>
        </div>

        <div class="filter-group checkbox-group">
          <input type="checkbox" id="merge" v-model="filter.isMerge" />
          <label for="merge">Gộp hàng hóa cùng loại</label>
        </div>

        <div class="filter-group">
          <label class="label">Mối quan tâm</label>
          <select v-model="filter.concern" class="form-input">
            <option value="sales">Doanh số bán hàng</option>
            <option value="profit">Lợi nhuận gộp</option>
          </select>
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
          <label class="label">Hàng hóa</label>
          <input 
            type="text" 
            placeholder="Theo mã, tên hàng" 
            class="form-input search-input"
            v-model="filter.searchQuery"
          >
        </div>

        <div class="sidebar-collapse-btn">
          <span class="arrow-left">‹</span>
        </div>
      </aside>


      <main class="main-content">
        
        <div class="content-header-text">
           <span v-if="filter.viewMode === 'report'">
             Top 10 sản phẩm bán chạy nhất (đã trừ trả hàng)
           </span>
           <span v-else>
             Biểu đồ biến động doanh thu Top 10
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
                  <th>Số lượng đã bán</th>
                  <th class="text-right">Doanh thu</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(product, index) in topProducts" :key="`${product.type}-${product.id}`">
                  <td>{{ index + 1 }}</td>
                  <td>{{ product.name }}</td>
                  <td>
                    <span :class="getTypeBadgeClass(product.type)">
                      {{ getTypeLabel(product.type) }}
                    </span>
                  </td>
                  <td>{{ formatCurrency(product.price) }}</td>
                  <td>{{ product.stocks.toLocaleString() }}</td>
                  <td><strong>{{ product.sales.toLocaleString() }}</strong></td>
                  <td class="text-right"><strong class="earnings">{{ formatCurrency(product.earnings) }}</strong></td>
                </tr>
                <tr v-if="topProducts.length === 0">
                  <td colspan="7" class="empty-state">
                    Chưa có dữ liệu
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div v-else class="chart-view-container">
           <div class="chart-legend">
              <div class="legend-item">
                 <span class="color-box bg-blue"></span>
                 <span>Thuốc</span>
              </div>
              <div class="legend-item">
                 <span class="color-box bg-yellow"></span>
                 <span>Vật tư y tế</span>
              </div>
           </div>

           <div class="chart-wrapper">
             <Chart 
               type="bar" 
               :data="chartData" 
               :options="chartOptions" 
               style="height: 400px;"
               class="h-full"
             />
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
    topProducts: {
      type: Array,
      default: () => [
        { id: 1, type: 'medicine', name: 'Panadol Extra', price: 250000, stocks: 120, sales: 50, earnings: 12500000 },
        { id: 2, type: 'goods', name: 'Khẩu trang Y tế 4 lớp', price: 35000, stocks: 5000, sales: 200, earnings: 7000000 },
        { id: 3, type: 'medicine', name: 'Efferalgan 500mg', price: 80000, stocks: 50, sales: 15, earnings: 1200000 },
        { id: 4, type: 'goods', name: 'Bông băng gạc', price: 15000, stocks: 200, sales: 50, earnings: 750000 },
        { id: 5, type: 'medicine', name: 'Berberin', price: 45000, stocks: 300, sales: 10, earnings: 450000 },
      ]
    }
  },
  data() {
    return {
      filter: {
        viewMode: 'chart', // Mặc định hiển thị Biểu đồ để test
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
              label: function(context) {
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
              callback: function(value) {
                 // Format trục Y dạng rút gọn (1tr, 2tr) nếu số quá lớn
                 if (value >= 1000000) return (value/1000000) + 'M';
                 if (value >= 1000) return (value/1000) + 'k';
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
    // XỬ LÝ DỮ LIỆU CHO BIỂU ĐỒ
    chartData() {
      // 1. Labels: Tên sản phẩm
      const labels = this.topProducts.map(p => p.name);

      // 2. Data: Doanh thu
      const dataValues = this.topProducts.map(p => p.earnings);

      // 3. Colors: Xanh cho Thuốc, Vàng cho Hàng hóa
      const backgroundColors = this.topProducts.map(p => {
        return p.type === 'medicine' ? '#42A5F5' : '#FFCA28';
      });

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
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
  overflow: hidden;
  min-height: 600px; /* Chiều cao tối thiểu của khung report */
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
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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

.icon-arrow, .icon-calendar {
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
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
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
.badge-medicine { background-color: #e3f2fd; color: #1976d2; }
.badge-goods { background-color: #fff8e1; color: #f57f17; }

.earnings { color: #28a745; }
.text-right { text-align: right; }
.empty-state { text-align: center; padding: 40px; color: #999; }

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
.bg-blue { background-color: #42A5F5; }
.bg-yellow { background-color: #FFCA28; }

.chart-wrapper {
  flex: 1;
  position: relative;
  min-height: 400px; 
}
</style>