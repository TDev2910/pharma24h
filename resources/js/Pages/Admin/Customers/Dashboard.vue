<template>
  <div class="customers-page">
    <!-- Header Control Bar -->
    <div class="header-control-bar">
      <div class="controls-section"
        style="width:100%; display:flex; align-items:center; justify-content:center; gap:16px; flex-wrap:wrap;">
        <!-- Title Section -->
        <div class="title-section">
          <h3>Danh sách khách hàng</h3>
        </div>
        <!-- Search Section -->
        <div style="flex:1; display:flex; justify-content:center;">
          <div class="search-wrapper">
            <div class="input-group">
              <span class="input-group-text">
                <i class="pi pi-search"></i>
              </span>
              <input type="text" class="form-control" style="border-radius:8px;"
                placeholder="Tìm kiếm theo tên, email, số điện thoại khách hàng" v-model="searchQuery"
                @input="debounceSearch">
            </div>
          </div>
        </div>
        <!-- Utility Options -->
        <div class="ultility-options">
          <!-- Thêm khách hàng -->
          <Button icon="pi pi-plus" label="Khách hàng" @click="openCreateModal" severity="secondary"
            style="background:#0b1020; border:none; color:white; font-weight:600; padding:6px 18px; border-radius:8px;" />
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
              <i class="fas fa-users"></i>
            </div>
            <div>
              <div class="stats-label">Tổng khách hàng có tài khoản</div>
              <div class="stats-number">{{ stats.totalCustomers }}</div>
            </div>
          </div>
        </div>
      </div>
      <div>
        <div class="table-header">
          <h3 class="table-title">Danh sách dữ liệu khách hàng</h3>
        </div>

        <!-- Customer Data Table -->
        <div class="table-container">
          <DataTable :value="filteredCustomers" removableSort tableStyle="min-width: 50rem" class="customers-table"
            :paginator="true" :row="5" :rows="pagination.per_page" :totalRecords="pagination.total"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
            :rowsPerPageOptions="[5, 10, 25]"
            currentPageReportTemplate="Hiển thị {first} đến {last} trong tổng số {totalRecords} khách hàng">
            <!-- Avatar Column -->
            <Column field="avatar" header="Avatar" style="width: 10%">
              <template #body="slotProps">
                <div class="customer-avatar">
                  <img v-if="slotProps.data.avatar_url" :src="slotProps.data.avatar_url" :alt="slotProps.data.name"
                    style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                  <div v-else class="avatar-placeholder">
                    {{ slotProps.data.name ? slotProps.data.name.substring(0, 2).toUpperCase() : 'N/A' }}
                  </div>
                </div>
              </template>
            </Column>

            <!-- Name Column -->
            <Column field="name" header="Tên khách hàng" sortable style="width: 15%">
              <template #body="slotProps">
                <span class="customer-name">{{ slotProps.data.name || 'N/A' }}</span>
              </template>
            </Column>

            <!-- Email Column -->
            <Column field="email" header="Email" sortable style="width: 20%">
              <template #body="slotProps">
                <span>{{ slotProps.data.email || 'N/A' }}</span>
              </template>
            </Column>

            <!-- Phone Column -->
            <Column field="phone" header="Số điện thoại" sortable style="width: 12%">
              <template #body="slotProps">
                <span>{{ slotProps.data.phone || 'N/A' }}</span>
              </template>
            </Column>

            <!-- Address Column -->
            <Column field="address" header="Địa chỉ" style="width: 20%">
              <template #body="slotProps">
                <span>{{ slotProps.data.address || 'N/A' }}</span>
              </template>
            </Column>

            <!-- Orders Count Column -->
            <Column field="orders_count" header="Tổng đơn hàng" sortable style="width: 10%" class="text-center">
              <template #body="slotProps">
                <span>{{ slotProps.data.orders_count || 0 }}</span>
              </template>
            </Column>

            <!-- Total Amount Column -->
            <Column field="total_amount" header="Tổng chi tiêu" sortable style="width: 10%" class="text-center">
              <template #body="slotProps">
                <span>{{ formatCurrency(slotProps.data.total_amount || 0) }}</span>
              </template>
            </Column>

            <!-- Actions Column -->
            <Column header="Thao tác" style="width: 10%;font-size: 13.4px;">
              <template #body="slotProps">
                <div class="action-group">
                  <Button icon="pi pi-pencil" class="p-button-sm btn-edit" @click="editCustomer(slotProps.data)"
                    v-tooltip.top="'Sửa'" />
                  <Button icon="pi pi-trash" class="p-button-sm btn-delete" @click="deleteCustomer(slotProps.data)"
                    v-tooltip.top="'Xóa'" />
                </div>
              </template>
            </Column>
          </DataTable>
        </div>
      </div>

    </div>
  </div>

  <!-- Create Customer Modal -->
  <CreateCustomerModal :visible="showCreateModal" @close="showCreateModal = false" @created="onCustomerCreated" />

  <!-- Edit Customer Modal -->
  <EditCustomerModal :visible="showEditModal" :customer="selectedCustomer" @close="showEditModal = false"
    @updated="onCustomerUpdated" />
</template>

<script>
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Card from 'primevue/card'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import CreateCustomerModal from './Modals/Create.vue'
import EditCustomerModal from './Modals/Edit.vue'
import axios from 'axios'
import Swal from 'sweetalert2'

export default {
  name: 'CustomerDashboard',
  components: {
    Button,
    InputText,
    Card,
    DataTable,
    Column,
    CreateCustomerModal,
    EditCustomerModal
  },

  props: {
    stats: {
      type: Object,
      default: () => ({
        totalCustomers: 0,
        newCustomers: 0,
        activeCustomers: 0
      })
    },
    customers: {
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
    }
  },

  data() {
    return {
      searchQuery: '',
      searchTimeout: null,
      showCreateModal: false,
      showEditModal: false,
      selectedCustomer: null,
      localCustomers: []
    }
  },

  computed: {
    displayCustomers() {
      return this.localCustomers.length > 0 ? this.localCustomers : this.customers;
    },

    //Lọc khách hàng
    filteredCustomers() {
      const customers = this.localCustomers.length > 0 ? this.localCustomers : this.customers;

      //Nếu không nhập từ khóa , thông tin khách hàng sẽ hiển thị tất cả
      if (!this.searchQuery || !this.searchQuery.trim()) {
        return customers; //Trả về tất cả khách hàng
      }

      //Lọc khách hàng theo tên, email, số điện thoại
      const term = this.searchQuery.toLowerCase().trim();
      return customers.filter(customer => {
        const name = (customer.name || '').toLowerCase();
        const email = (customer.email || '').toLowerCase();
        const phone = (customer.phone || '').toLowerCase();
        return name.includes(term) || email.includes(term) || phone.includes(term);
      });
    }
  },

  watch: {
    customers: {
      handler(newCustomers) {
        this.localCustomers = [...newCustomers];
      },
      immediate: true
    }
  },

  methods: {
    openCreateModal() {
      this.showCreateModal = true
    },

    debounceSearch() {
      clearTimeout(this.searchTimeout)
      this.searchTimeout = setTimeout(() => {
      }, 200)
    },


    formatCurrency(amount) {
      return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
      }).format(amount)
    },

    viewCustomer(customer) {
      console.log('View customer:', customer)
      // Implement view customer logic
    },

    editCustomer(customer) {
      console.log('Edit customer:', customer)
      this.selectedCustomer = customer
      this.showEditModal = true
    },

    async deleteCustomer(customer = null) {
      if (!customer?.id) return; // guard

      const result = await Swal.fire({
        title: 'Xác nhận xóa',
        text: `Bạn có chắc chắn muốn xóa khách hàng "${customer.name}" không?`,
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
        console.log('Sending DELETE request to:', `/admin/customers/${customer.id}`);
        const response = await axios.delete(`/admin/customers/${customer.id}`);
        console.log('Response received:', response);
        console.log('Response data:', response.data);

        if (response.data?.success) {
          // Cập nhật localCustomers
          this.localCustomers = this.localCustomers.filter(c => c.id !== customer.id);

          await Swal.fire({
            title: 'Thành công!',
            text: response.data.message || 'Đã xóa khách hàng',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
          });
        } else {
          console.log('Response success is false:', response.data);
          await Swal.fire({ icon: 'warning', title: 'Không thành công', text: response.data?.message || 'Thao tác thất bại' });
        }
      } catch (error) {
        console.error('Error in deleteCustomer:', error);
        console.error('Error response:', error.response);
        await Swal.fire({
          title: 'Lỗi!',
          text: error.response?.data?.message || 'Có lỗi xảy ra khi xóa khách hàng',
          icon: 'error'
        });
      }
    },


    onCustomerCreated(customer) {
      console.log('Customer created:', customer)
      // Refresh page để load dữ liệu mới
      window.location.reload()
    },

    onCustomerUpdated(customer) {
      console.log('Customer updated:', customer)
      // Cập nhật localCustomers
      const index = this.localCustomers.findIndex(c => c.id === customer.id);
      if (index !== -1) {
        this.localCustomers[index] = customer;
      }
    },

    mounted() {
      // Filter sẽ tự động áp dụng thông qua computed property
    }
  }
}
</script>

<style scoped>
.customers-page {
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

.title-section h4 {
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

/* Search wrapper improvements */
.search-wrapper {
  position: relative;
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

/* Content Area */
/* .content-area {
  background: #fff;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  border: 1px solid #e9ecef;
} */

.test-section {
  margin-top: 20px;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 8px;
}

.mr-2 {
  margin-right: 0.5rem;
}

.mt-3 {
  margin-top: 1rem;
}

/* DataTable Styling - Compact Version */
.table-container {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  margin-top: 16px;
  overflow: hidden;
}

.customers-table {
  width: 100%;
  font-size: 18px;
}

/* PrimeVue DataTable custom styling */
:deep(.p-datatable) {
  font-size: 19px;
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

/* Customer Avatar - Smaller */
.customer-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
  color: #fff;
  font-weight: 600;
  font-size: 12px;
}

.customer-name {
  font-size: 13px;
  font-weight: 500;
  color: #1f2937;
}

/* Action buttons - Smaller */
.action-group {
  display: flex;
  gap: 4px;
  justify-content: center;
}

:deep(.p-button.p-button-sm) {
  width: 24px;
  height: 24px;
  padding: 0;
  font-size: 12px;
}

.btn-detail {
  color: #4F46E5 !important;
}


.btn-delete {
  color: #EF4444 !important;
}

.text-center {
  text-align: center !important;
}

/* Table title - Smaller */
.table-title {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}
</style>
