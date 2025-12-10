<template>
  <main class="main-content">
    <div class="top-bar">
      <div class="search-box">
        <span class="search-icon">🔍</span>
        <input type="text" placeholder="Tìm theo mã vận đơn..." :value="searchQuery"
          @input="$emit('update:searchQuery', $event.target.value)">
      </div>
    </div>

    <!-- PrimeVue DataTable -->
    <div class="table-container">
      <DataTable :value="orders.data" :paginator="true" :rows="pagination.per_page || 20"
        :totalRecords="pagination.total || 0" :first="first" @page="onPageChange"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[10, 20, 30, 50]"
        currentPageReportTemplate="Hiển thị {first} đến {last} trong tổng số {totalRecords} vận đơn" dataKey="id"
        :loading="loading" stripedRows responsiveLayout="scroll" emptyMessage="Không có vận đơn nào"
        tableStyle="min-width: 50rem">

        <!-- Mã vận đơn -->
        <Column field="code" header="Mã vận đơn" sortable style="width: 15%">
          <template #body="{ data }">
            <span class="code-link">{{ data.code }}</span>
          </template>
        </Column>

        <!-- Thời gian tạo -->
        <Column field="created_at_formatted" header="Thời gian tạo" sortable style="width: 15%">
          <template #body="{ data }">
            <span>{{ data.created_at_formatted || 'N/A' }}</span>
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
        <Column field="partner" header="Đối tác giao hàng" style="width: 12%">
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

        <!-- Thời gian giao hàng -->
        <Column field="delivery_time" header="Thời gian giao hàng" style="width: 15%">
          <template #body="{ data }">
            <span>{{ data.delivery_time || 'Chưa có' }}</span>
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
      // Emit event để parent component xử lý pagination
      this.$emit('page-change', {
        page: event.page + 1, // PrimeVue dùng index 0, Laravel dùng index 1
        per_page: event.rows
      })
    },
    getStatusText(status) {
      const statusMap = {
        'delivering': 'Đang giao',
        'completed': 'Đã giao',
        'cancelled': 'Đã hủy'
      }
      return statusMap[status] || 'Không xác định'
    },
    getStatusSeverity(status) {
      const severityMap = {
        'delivering': 'warning',
        'completed': 'success',
        'cancelled': 'danger'
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
      // Kiểm tra nếu không có mã vận đơn
      if (!order?.code) return '#'
      // Nếu có tracking_url từ database, dùng nó
      // Nếu không, tạo URL tracking từ mã vận đơn GHN
      return order.tracking_url || `https://donhang.ghn.vn/?order_code=${order.code}`
    },
    openTracking(url) {
      // Kiểm tra URL hợp lệ trước khi mở
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