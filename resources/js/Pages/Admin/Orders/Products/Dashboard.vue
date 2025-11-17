<template>
  <div class="orders-page">
    <!-- Header Control Bar -->
    <div class="header-control-bar">
      <div class="controls-section"
        style="width:100%; display:flex; align-items:center; justify-content:center; gap:16px; flex-wrap:wrap;">
        <!-- Title Section -->
        <div class="title-section">
          <h3>Danh sách đơn hàng</h3>
        </div>
        <!-- Search Section -->
        <div style="flex:1; display:flex; justify-content:center;">
          <div class="search-wrapper">
            <div class="input-group">
              <span class="input-group-text">
                <i class="pi pi-search"></i>
              </span>
              <input type="text" class="form-control" style="border-radius:8px;"
                placeholder="Tìm kiếm theo mã đơn hàng, tên khách hàng, số điện thoại" v-model="searchQuery"
                @input="debounceSearch">
            </div>
          </div>
        </div>
        <!-- Utility Options -->
        <div class="ultility-options">
          <!-- In hóa đơn đã chọn -->
          <Button icon="pi pi-print" label="In hóa đơn đã chọn" @click="printSelectedInvoices"
            :disabled="selectedOrders.length === 0" severity="success"
            style="background:#10b981; border:none; color:white; font-weight:600; padding:6px 18px; border-radius:8px;" />
          <!-- Utility Icons -->
          <div class="utility-icons">
            <button class="btn" title="Chế độ xem">
              <i class="pi pi-list"></i>
            </button>
            <button class="btn" title="Cài đặt">
              <i class="pi pi-cog"></i>
            </button>
            <button class="btn" title="Trợ giúp">
              <i class="pi pi-question-circle"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Content Area -->
    <div class="content-area">
      <!-- Stats Cards -->
      <div class="stats-row">
        <div class="stats-card">
          <div class="stats-card-inner">
            <div class="stats-icon" style="background: #4F46E5;">
              <i class="fas fa-shopping-cart"></i>
            </div>
            <div>
              <div class="stats-label">Tổng đơn hàng</div>
              <div class="stats-number">{{ stats.totalOrders }}</div>
            </div>
          </div>
        </div>
        <div class="stats-card">
          <div class="stats-card-inner">
            <div class="stats-icon" style="background: #F59E0B;">
              <i class="fas fa-clock"></i>
            </div>
            <div>
              <div class="stats-label">Đơn chờ xử lý</div>
              <div class="stats-number">{{ stats.pendingOrders }}</div>
            </div>
          </div>
        </div>
        <div class="stats-card">
          <div class="stats-card-inner">
            <div class="stats-icon" style="background: #10B981;">
              <i class="fas fa-check-circle"></i>
            </div>
            <div>
              <div class="stats-label">Đơn hoàn thành</div>
              <div class="stats-number">{{ stats.completedOrders }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Filter Section -->
      <div class="filter-section">
        <div class="filter-wrapper">
          <div class="filter-row">
            <!-- Order Code Filter -->
            <div class="filter-item">
              <label class="filter-label">Mã đơn hàng</label>
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control" placeholder="Tìm theo mã" v-model="filters.order_code"
                  @input="applyFilters">
              </div>
            </div>

            <!-- Date Range Filter -->
            <div class="filter-item">
              <label class="filter-label">Ngày đặt hàng</label>
              <div class="input-group">
                <input type="date" class="form-control" placeholder="Từ ngày" v-model="filters.from_date"
                  @change="applyFilters">
                <span class="input-group-text">-</span>
                <input type="date" class="form-control" placeholder="Đến ngày" v-model="filters.to_date"
                  @change="applyFilters">
              </div>
            </div>

            <!-- Status Filter -->
            <div class="filter-item">
              <label class="filter-label">Trạng thái</label>
              <select class="form-select" v-model="filters.status" @change="applyFilters">
                <option value="">Tất cả</option>
                <option value="pending">Đang chờ xử lý</option>
                <option value="confirmed">Đã xác nhận</option>
                <option value="completed">Hoàn thành</option>
                <option value="cancellation_requested">Yêu cầu hủy</option>
                <option value="cancelled">Đã hủy</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Orders Data Table -->
      <div>
        <div class="table-header">
          <h3 class="table-title">Danh sách dữ liệu đơn hàng</h3>
        </div>

        <div class="table-container">
          <DataTable :value="filteredOrders" removableSort tableStyle="min-width: 50rem" class="orders-table"
            :paginator="true" :rows="pagination.per_page" :totalRecords="pagination.total"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
            :rowsPerPageOptions="[5, 10, 25]"
            currentPageReportTemplate="Hiển thị {first} đến {last} trong tổng số {totalRecords} đơn hàng">
            <!-- Checkbox Column -->
            <Column headerStyle="width: 30px">
              <template #body="slotProps">
                <div class="form-check">
                  <input class="form-check-input order-select" type="checkbox" :value="slotProps.data.id"
                    :checked="selectedOrders.includes(slotProps.data.id)"
                    @change="handleSelectOrder(slotProps.data.id, $event)">
                </div>
              </template>
              <template #header>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="selectAll"
                    :checked="selectedOrders.length === filteredOrders.length && filteredOrders.length > 0"
                    @change="handleSelectAll">
                </div>
              </template>
            </Column>

            <!-- Order Code Column -->
            <Column field="order_code" header="Mã đơn hàng" sortable style="width: 12%">
              <template #body="slotProps">
                <span class="fw-medium">{{ slotProps.data.order_code || 'N/A' }}</span>
              </template>
            </Column>

            <!-- Customer Name Column -->
            <Column field="customer_name" header="Tên khách hàng" sortable style="width: 18%">
              <template #body="slotProps">
                <span>{{ slotProps.data.customer_name || 'N/A' }}</span>
              </template>
            </Column>

            <!-- Status Column -->
            <Column field="order_status" header="Trạng thái" sortable style="width: 15%">
              <template #body="slotProps">
                <span :class="getStatusBadgeClass(slotProps.data.order_status)">
                  {{ getStatusText(slotProps.data.order_status) }}
                </span>
              </template>
            </Column>

            <!-- Payment Method Column -->
            <Column field="payment_method" header="Phương thức thanh toán" style="width: 15%">
              <template #body="slotProps">
                <span>{{ getPaymentMethodText(slotProps.data.payment_method) }}</span>
              </template>
            </Column>

            <!-- Total Amount Column -->
            <Column field="total_amount" header="Tổng tiền" sortable style="width: 12%" class="text-center">
              <template #body="slotProps">
                <span>{{ formatCurrency(slotProps.data.total_amount || 0) }}</span>
              </template>
            </Column>

            <!-- Created Date Column -->
            <Column field="created_at_formatted" header="Ngày tạo" sortable style="width: 10%">
              <template #body="slotProps">
                <span>{{ slotProps.data.created_at_formatted || 'N/A' }}</span>
              </template>
            </Column>

            <!-- Actions Column -->
            <Column header="Thao tác" style="width: 15%">
              <template #body="slotProps">
                <div class="action-group">
                  <Button icon="pi pi-eye" class="p-button-sm btn-detail" @click="viewOrderDetail(slotProps.data)"
                    v-tooltip.top="'Xem chi tiết'" />
                  <Button icon="pi pi-pencil" class="p-button-sm btn-edit" @click="editOrder(slotProps.data)"
                    v-tooltip.top="'Chỉnh sửa'" />
                  <Button icon="pi pi-trash" class="p-button-sm btn-delete" @click="deleteOrder(slotProps.data)"
                    v-tooltip.top="'Xóa'" />
                </div>
              </template>
            </Column>
          </DataTable>
        </div>
      </div>
    </div>

    <!-- Order Details Modal -->
    <OrderDetailsModal :visible="showDetailsModal" :orderId="selectedOrderId" @close="showDetailsModal = false" />

    <!-- Order Edit Modal -->
    <OrderEditModal :visible="showEditModal" :orderId="selectedEditOrderId" @close="showEditModal = false"
      @updated="onOrderUpdated" />

    <InvoiceModal :visible="showInvoiceModal" :orderId="selectedInvoiceOrderId" @close="showInvoiceModal = false" />
  </div>
</template>

<script>
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import OrderDetailsModal from './modals/details_modal.vue'
import OrderEditModal from './modals/edit_modal.vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import { router } from '@inertiajs/vue3'
import InvoiceModal from './invoice_modal.vue'

export default {
  name: 'OrdersDashboard',
  components: {
    Button,
    InputText,
    DataTable,
    Column,
    OrderDetailsModal,
    OrderEditModal,
    InvoiceModal
  },

  props: {
    stats: {
      type: Object,
      default: () => ({
        totalOrders: 0,
        pendingOrders: 0,
        completedOrders: 0
      })
    },
    orders: {
      type: Array,
      default: () => []
    },
    pagination: {
      type: Object,
      default: () => ({
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
        from: 0,
        to: 0
      })
    },
    filters: {
      type: Object,
      default: () => ({
        order_code: '',
        status: '',
        from_date: '',
        to_date: ''
      })
    }
  },

  data() {
    return {
      searchQuery: '',
      searchTimeout: null,
      selectedOrders: [],
      localOrders: [],
      showDetailsModal: false,
      showEditModal: false,
      showInvoiceModal: false,
      selectedOrderId: null,
      selectedEditOrderId: null,
      selectedInvoiceOrderId: null,
      filters: {
        order_code: '',
        status: '',
        from_date: '',
        to_date: ''
      }
    }
  },

  computed: {
    filteredOrders() {
      const orders = this.localOrders.length > 0 ? this.localOrders : this.orders;

      // Nếu không có search query và filters, trả về tất cả
      if (!this.searchQuery || !this.searchQuery.trim()) {
        return orders;
      }

      // Lọc theo search query
      const term = this.searchQuery.toLowerCase().trim();
      return orders.filter(order => {
        const orderCode = (order.order_code || '').toLowerCase();
        const customerName = (order.customer_name || '').toLowerCase();
        const customerPhone = (order.customer_phone || '').toLowerCase();
        return orderCode.includes(term) || customerName.includes(term) || customerPhone.includes(term);
      });
    }
  },

  watch: {
    orders: {
      handler(newOrders) {
        this.localOrders = [...newOrders];
      },
      immediate: true
    },
    filters: {
      handler(newFilters) {
        this.filters = { ...newFilters };
      },
      immediate: true,
      deep: true
    }
  },

  methods: {
    debounceSearch() {
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        // Search được xử lý qua computed property
      }, 200);
    },

    formatCurrency(amount) {
      return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
      }).format(amount);
    },

    formatDate(dateString) {
      if (!dateString) return 'N/A';
      const date = new Date(dateString);
      return date.toLocaleDateString('vi-VN');
    },

    getStatusBadgeClass(status) {
      const s = (status || '').toString().toLowerCase();
      if (s === 'pending' || s === 'new') {
        return 'badge bg-warning text-dark';
      } else if (s === 'confirmed') {
        return 'badge bg-info';
      } else if (s === 'completed') {
        return 'badge bg-success';
      } else if (s === 'cancelled') {
        return 'badge bg-danger';
      } else if (s === 'cancellation_requested') {
        return 'badge bg-danger';
      }
      return 'badge bg-secondary';
    },

    getStatusText(status) {
      const s = (status || '').toString().toLowerCase();
      if (s === 'pending' || s === 'new') {
        return 'Đang chờ xử lý';
      } else if (s === 'confirmed') {
        return 'Đã xác nhận';
      } else if (s === 'completed') {
        return 'Hoàn thành';
      } else if (s === 'cancelled') {
        return 'Đã hủy';
      } else if (s === 'cancellation_requested') {
        return 'Yêu cầu hủy';
      }
      return 'Khác';
    },

    getPaymentMethodText(method) {
      const m = (method || '').toString().toLowerCase();
      switch (m) {
        case 'cash':
          return 'Tiền mặt';
        case 'transfer':
          return 'Chuyển khoản';
        case 'vnpay':
          return 'VNPay';
        case 'momo':
          return 'Ví MoMo';
        case 'zalopay':
          return 'ZaloPay';
        default:
          return method || 'Không xác định';
      }
    },

    handleSelectAll(event) {
      if (event.target.checked) {
        this.selectedOrders = this.filteredOrders.map(order => order.id);
      } else {
        this.selectedOrders = [];
      }
    },

    handleSelectOrder(orderId, event) {
      if (event.target.checked) {
        if (!this.selectedOrders.includes(orderId)) {
          this.selectedOrders.push(orderId);
        }
      } else {
        this.selectedOrders = this.selectedOrders.filter(id => id !== orderId);
      }
    },

    printSelectedInvoices() {
      if (this.selectedOrders.length === 0) {
        Swal.fire({
          icon: 'warning',
          title: 'Thông báo',
          text: 'Vui lòng chọn ít nhất một đơn hàng để in.',
          timer: 2000,
          showConfirmButton: false
        });
        return;
      }

      // Mở từng hóa đơn ở tab mới
      this.selectedOrders.forEach((id, idx) => {
        const url = `/admin/orders/${id}/invoice`;
        setTimeout(() => {
          window.open(url, '_blank');
        }, idx * 250);
      });

      Swal.fire({
        icon: 'success',
        title: 'Thành công',
        text: `Đang mở ${this.selectedOrders.length} hóa đơn để in.`,
        timer: 2000,
        showConfirmButton: false
      });
    },

    viewOrderDetail(order) {
      this.selectedOrderId = order.id;
      this.showDetailsModal = true;
    },

    editOrder(order) {
      this.selectedEditOrderId = order.id;
      this.showEditModal = true;
    },

    onOrderUpdated(order) {
      router.reload({ only: ['orders'] });
    },

    async deleteOrder(order) {
      if (!order?.id) return;

      const result = await Swal.fire({
        title: 'Xác nhận xóa',
        text: `Bạn có chắc chắn muốn xóa đơn hàng "${order.order_code}" không?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy',
        reverseButtons: true
      });

      if (!result.isConfirmed) return;

      try {
        const response = await axios.delete(`/admin/orders/${order.id}`);

        if (response.data?.success) {
          // Cập nhật localOrders
          this.localOrders = this.localOrders.filter(o => o.id !== order.id);

          // Cập nhật selectedOrders
          this.selectedOrders = this.selectedOrders.filter(id => id !== order.id);

          await Swal.fire({
            title: 'Thành công!',
            text: response.data.message || 'Đã xóa đơn hàng',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
          });

          // Reload để cập nhật stats
          router.reload();
        } else {
          await Swal.fire({
            icon: 'warning',
            title: 'Không thành công',
            text: response.data?.message || 'Thao tác thất bại'
          });
        }
      } catch (error) {
        console.error('Error in deleteOrder:', error);
        await Swal.fire({
          title: 'Lỗi!',
          text: error.response?.data?.message || 'Có lỗi xảy ra khi xóa đơn hàng',
          icon: 'error'
        });
      }
    },

    printInvoice(order) {
      this.selectedInvoiceOrderId = order.id;
      this.showInvoiceModal = true;
    },

    applyFilters() {
      router.get('/admin/orders', {
        order_code: this.filters.order_code || '',
        status: this.filters.status || '',
        from_date: this.filters.from_date || '',
        to_date: this.filters.to_date || ''
      }, {
        preserveState: true,
        preserveScroll: true
      });
    }
  }
}
</script>

<style scoped>
.orders-page {
  padding: 20px;
}

/* Header Control Bar */
.header-control-bar {
  background: #fff;
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  border: 1px solid #e9ecef;
}

.controls-section {
  display: flex;
  align-items: center;
  gap: 16px;
}

.title-section h3 {
  color: #2c3e50;
  margin: 0;
  font-weight: 600;
  font-size: 18px;
}

/* Search Box */
.search-wrapper {
  flex: 1;
  max-width: 465px;
  min-width: 280px;
}

.search-wrapper .form-control {
  padding-left: 40px !important;
  padding-right: 16px !important;
  border: 2px solid #91C4C3 !important;
  border-radius: 8px !important;
  height: 42px !important;
  font-size: 14px !important;
  background: #fff !important;
  transition: all 0.2s ease !important;
}

.search-wrapper .form-control:focus {
  border-color: #007bff !important;
  box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1) !important;
  outline: none !important;
}

.search-wrapper .input-group {
  position: relative;
}

.search-wrapper .input-group-text {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: transparent;
  border: none;
  color: #6c757d;
  z-index: 2;
  pointer-events: none;
  transition: color 0.2s ease;
}

.search-wrapper .form-control:focus+.input-group-text {
  color: #007bff;
}

/* Utility Options */
.ultility-options {
  display: flex;
  align-items: center;
  gap: 12px;
}

.utility-icons {
  display: flex;
  gap: 8px;
}

.btn {
  background: #f8f9fa;
  border: 1px solid #e9ecef;
  border-radius: 6px;
  padding: 8px 10px;
  color: #6c757d;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn:hover {
  background: #e9ecef;
  color: #495057;
}

.btn i {
  font-size: 14px;
}

/* Stats Card Section */
.stats-row {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
  margin-bottom: 32px;
}

.stats-card {
  background: white;
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.stats-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  transform: translateY(-2px);
}

.stats-card-inner {
  display: flex;
  align-items: center;
  gap: 16px;
}

.stats-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  color: white;
  flex-shrink: 0;
}

.stats-label {
  font-size: 14px;
  color: #6b7280;
  margin-bottom: 4px;
}

.stats-number {
  font-size: 24px;
  font-weight: 700;
  color: #1f2937;
}

/* Filter Section */
.filter-section {
  background: #fff;
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  border: 1px solid #e9ecef;
}

.filter-wrapper {
  width: 100%;
}

.filter-row {
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
  align-items: end;
}

.filter-item {
  flex: 1;
  min-width: 200px;
}

.filter-label {
  font-size: 13px;
  font-weight: 600;
  color: #495057;
  margin-bottom: 6px;
  display: block;
}

.filter-item .form-control,
.filter-item .form-select {
  font-size: 14px;
  border-radius: 6px;
  border: 1px solid #dee2e6;
}

.filter-item .input-group-text {
  background: #f8f9fa;
  border: 1px solid #dee2e6;
}

/* DataTable Styling */
.table-container {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  margin-top: 16px;
  overflow: hidden;
}

.orders-table {
  width: 100%;
}

:deep(.p-datatable) {
  font-size: 14px;
}

:deep(.p-datatable .p-datatable-header) {
  font-size: 13px;
  padding: 8px 12px;
}

:deep(.p-datatable .p-datatable-thead > tr > th) {
  font-size: 13.5px;
  font-weight: 600;
  padding: 8px 12px;
  background: #f8f9fa;
  color: #495057;
}

:deep(.p-datatable .p-datatable-tbody > tr > td) {
  font-size: 14px;
  padding: 8px 12px;
  border-bottom: 1px solid #f1f3f5;
}

:deep(.p-datatable .p-datatable-tbody > tr:hover) {
  background: #f6f8fa;
}

/* Pagination styling */
:deep(.p-paginator) {
  font-size: 12px;
  padding: 8px 12px;
}

:deep(.p-paginator .p-paginator-pages .p-paginator-page) {
  font-size: 12px;
  padding: 4px 8px;
}

/* Badge styles */
.badge {
  display: inline-block;
  padding: 4px 8px;
  font-size: 12px;
  font-weight: 600;
  border-radius: 4px;
}

.bg-warning {
  background-color: #ffc107 !important;
  color: #000 !important;
}

.bg-success {
  background-color: #198754 !important;
  color: #fff !important;
}

.bg-danger {
  background-color: #dc3545 !important;
  color: #fff !important;
}

.bg-secondary {
  background-color: #6c757d !important;
  color: #fff !important;
}

/* Action buttons */
.action-group {
  display: flex;
  gap: 4px;
  justify-content: center;
}

:deep(.p-button.p-button-sm) {
  width: 28px;
  height: 28px;
  padding: 0;
  font-size: 12px;
}

.btn-detail {
  color: #4F46E5 !important;
}

.btn-edit {
  color: #0ea5e9 !important;
}

.btn-delete {
  color: #EF4444 !important;
}

.text-center {
  text-align: center !important;
}

.fw-medium {
  font-weight: 500;
}

/* Table title */
.table-title {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
  padding: 16px 0 8px 0;
}

.table-header {
  margin-bottom: 8px;
}

/* Form check styles */
.form-check {
  display: flex;
  align-items: center;
  justify-content: center;
}

.form-check-input {
  cursor: pointer;
}
</style>