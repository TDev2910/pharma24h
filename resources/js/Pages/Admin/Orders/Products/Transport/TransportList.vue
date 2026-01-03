<template>
  <main class="main-content">
    <div class="top-bar">
      <div class="search-box">
        <input type="text" class="form-control" style="border-radius:8px;" placeholder="Tìm theo mã vận đơn..."
          :value="searchQuery" @input="$emit('update:searchQuery', $event.target.value)">
      </div>
    </div>

    <!-- PrimeVue DataTable -->
    <div class="table-container">
      <DataTable :value="orders.data" :paginator="true" :rows="pagination.per_page || 20"
        :totalRecords="pagination.total || 0" :first="first" @page="onPageChange"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[10, 20, 30, 50]"
        currentPageReportTemplate="Hiển thị {first} đến {last} trong tổng số {totalRecords} vận đơn" dataKey="id"
        stripedRows responsiveLayout="scroll" emptyMessage="Không có vận đơn nào"
        tableStyle="min-width: 50rem">

        <!-- Mã vận đơn -->
        <Column field="code" header="Mã vận đơn" sortable style="width: 15%">
          <template #body="{ data }">
            <span class="code-link">{{ data.code }}</span>
          </template>
        </Column>

        <!-- Mã hóa đơn -->
        <Column field="order_code" header="Mã hóa đơn" sortable style="width: 12%">
          <template #body="{ data }">
            <span class="order-code">{{ data.order_code }}</span>
          </template>
        </Column>

        <!-- Khách hàng -->
        <Column field="customer_name" header="Khách hàng" sortable style="width: 18%">
          <template #body="{ data }">
            <span>{{ data.customer_name || 'N/A' }}</span>
          </template>
        </Column>

        <!-- Đối tác giao hàng -->
        <Column field="partner" header="Đối tác giao hàng" style="width: 15%">
          <template #body="{ data }">
            <Tag :value="data.partner.toUpperCase()" :severity="data.partner === 'ghn' ? 'info' : 'success'" />
          </template>
        </Column>

        <!-- Trạng thái giao -->
        <Column field="status" header="Trạng thái giao" style="width: 12%">
          <template #body="{ data }">
            <Tag :value="getStatusText(data.status)" :severity="getStatusSeverity(data.status)" />
          </template>
        </Column>

        <!-- Actions -->
        <Column header="Thao tác" style="width: 10%">
          <template #body="{ data }">
            <Button icon="pi pi-external-link" class="p-button-sm p-button-secondary p-button-text"
              :disabled="!getTrackingUrl(data) || getTrackingUrl(data) === '#'"
              @click="openTracking(getTrackingUrl(data))"
              v-tooltip.top="getTrackingUrl(data) && getTrackingUrl(data) !== '#' ? 'Theo dõi vận đơn trên GHN' : 'Chưa có mã vận đơn'" />
          </template>
        </Column>
      </DataTable>
    </div>
  </main>
</template>

<script>
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Tag from 'primevue/tag'
import Button from 'primevue/button'

export default {
  name: 'TransportList',
  components: {
    DataTable,
    Column,
    Tag,
    Button
  },
  props: {
    orders: {
      type: Object,
      default: () => ({ data: [] })
    },
    pagination: {
      type: Object,
      default: () => ({
        current_page: 1,
        per_page: 20,
        total: 0
      })
    },
    searchQuery: {
      type: String,
      default: ''
    },
    loading: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      first: 0
    }
  },
  watch: {
    pagination: {
      handler(newPagination) {
        // Cập nhật first index khi pagination thay đổi
        this.first = (newPagination.current_page - 1) * newPagination.per_page
      },
      immediate: true
    }
  },
  methods: {
    onPageChange(event) {
      this.$emit('page-change', {
        page: event.page + 1,
        per_page: event.rows
      })
    },

    getStatusText(status) {
      const statusMap = {
        // Trạng thái khởi tạo
        'ready_to_pick': 'Chờ lấy hàng',
        'picking': 'Đang lấy hàng',
        'cancel': 'Đã hủy',
        'money_collect_picking': 'Đang thu tiền người gửi',
        'picked': 'Đã lấy hàng',
        'storing': 'Nhập kho',
        'transporting': 'Đang luân chuyển',
        'sorting': 'Đang phân loại',
        'delivering': 'Đang giao hàng',
        'money_collect_delivering': 'Đang thu tiền người nhận',
        'delivered': 'Đã giao hàng',
        'delivery_fail': 'Giao hàng thất bại',
        'waiting_to_return': 'Chờ trả hàng',
        'return': 'Trả hàng',
        'return_transporting': 'Đang luân chuyển hàng trả',
        'return_sorting': 'Đang phân loại hàng trả',
        'returning': 'Đang trả hàng',
        'return_fail': 'Trả hàng thất bại',
        'returned': 'Đã trả hàng',
        'exception': 'Đơn hàng ngoại lệ',
        'damage': 'Hàng bị hư hỏng',
        'lost': 'Hàng thất lạc',

        // Trạng thái cũ (backwards compatibility)
        'pending': 'Chờ lấy hàng',
        'completed': 'Đã giao',
        'cancelled': 'Đã hủy'
      }
      return statusMap[status] || status || 'Không xác định'
    },

    getStatusSeverity(status) {
      const severityMap = {
        // Chờ xử lý - Info (blue)
        'ready_to_pick': 'info',
        'picking': 'info',
        'money_collect_picking': 'info',
        'pending': 'info',

        // Đang xử lý - Warning (orange/yellow)
        'picked': 'warning',
        'storing': 'warning',
        'transporting': 'warning',
        'sorting': 'warning',
        'delivering': 'warning',
        'money_collect_delivering': 'warning',

        // Thành công - Success (green)
        'delivered': 'success',
        'completed': 'success',

        // Thất bại/Hủy - Danger (red)
        'cancel': 'danger',
        'delivery_fail': 'danger',
        'return_fail': 'danger',
        'exception': 'danger',
        'damage': 'danger',
        'lost': 'danger',
        'cancelled': 'danger',

        // Đang trả hàng - Secondary (gray)
        'waiting_to_return': 'secondary',
        'return': 'secondary',
        'return_transporting': 'secondary',
        'return_sorting': 'secondary',
        'returning': 'secondary',
        'returned': 'secondary'
      }
      return severityMap[status] || 'secondary'
    },

    formatCurrency(amount) {
      return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
      }).format(amount)
    },

    viewDetail(order) {
      this.$emit('view-detail', order)
    },

    getTrackingUrl(order) {
      if (!order?.code) return '#'
      return order.tracking_url || `https://donhang.ghn.vn/?order_code=${order.code}`
    },

    openTracking(url) {
      if (!url || url === '#') {
        console.warn('Tracking URL is not available')
        return
      }
      window.open(url, '_blank', 'noopener,noreferrer')
    }
  }
}
</script>

<style scoped>
.main-content {
  flex: 1;
  padding: 20px;
  background-color: #f6f8fa;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
}

.top-bar {
  display: flex;
  justify-content: space-between;
  margin-bottom: 20px;
  align-items: center;
}

.search-box {
  position: relative;
  width: 350px;
}

.search-box input {
  width: 100%;
  padding: 10px 10px 10px 35px;
  border: 1px solid #ddd;
  border-radius: 6px;
  outline: none;
}

.search-icon {
  position: absolute;
  left: 10px;
  top: 50%;
  transform: translateY(-50%);
  color: #888;
}

.table-container {
  background: white;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.code-link {
  color: #007bff;
  font-weight: 500;
  cursor: pointer;
}

.code-link:hover {
  text-decoration: underline;
}

.order-code {
  font-weight: 500;
  color: #333;
}

.cod-amount {
  display: block;
  font-size: 11px;
  color: #666;
  margin-top: 2px;
}
</style>
