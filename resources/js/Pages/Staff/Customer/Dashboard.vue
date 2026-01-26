<template>
  <div class="customers-page">
    <div class="header-control-bar">
      <div class="controls-section">
        <div class="title-section">
          <h3>Danh sách khách hàng</h3>
        </div>

        <div class="search-container">
          <div class="search-wrapper">
            <div class="input-group">
              <span class="input-group-text">
                <i class="pi pi-search"></i>
              </span>
              <input type="text" class="form-control search-input" placeholder="Tìm kiếm tên, email, sđt..."
                v-model="searchQuery" @input="debounceSearch">
            </div>
          </div>
        </div>

        <div class="utility-options">
          <Button icon="pi pi-plus" label="Thêm mới" @click="openCreateModal" class="btn-create" />

          <div class="utility-icons">
            <button class="btn-icon" title="Chế độ xem">
              <i class="pi pi-list"></i>
            </button>
            <button class="btn-icon" title="Cài đặt">
              <i class="pi pi-cog"></i>
            </button>
            <button class="btn-icon" title="Trợ giúp">
              <i class="pi pi-question-circle"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="content-area">
      <div class="stats-row">
        <div class="stats-card">
          <div class="stats-card-inner">
            <div class="stats-icon bg-indigo">
              <i class="fas fa-users"></i>
            </div>
            <div class="stats-info">
              <div class="stats-label">Tổng khách hàng có tài khoản</div>
              <div class="stats-number">{{ stats.totalCustomers }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="table-wrapper">
        <div class="table-header">
          <h3 class="table-title">Dữ liệu chi tiết khách hàng</h3>
        </div>

        <div class="table-container">
          <DataTable :value="filteredCustomers" removableSort scrollable scrollHeight="flex" class="customers-table"
            :paginator="true" :row="5" :rows="pagination.per_page" :totalRecords="pagination.total"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
            :rowsPerPageOptions="[5, 10, 25]" currentPageReportTemplate="{first} - {last} / {totalRecords}">
            <Column field="avatar" header="Ảnh" style="min-width: 70px" frozen>
              <template #body="slotProps">
                <div class="customer-avatar">
                  <img v-if="slotProps.data.avatar_url" :src="slotProps.data.avatar_url" :alt="slotProps.data.name"
                    class="avatar-img">
                  <div v-else class="avatar-placeholder">
                    {{ slotProps.data.name ? slotProps.data.name.substring(0, 2).toUpperCase() : 'N/A' }}
                  </div>
                </div>
              </template>
            </Column>

            <Column field="name" header="Tên khách hàng" sortable style="min-width: 180px" frozen>
              <template #body="slotProps">
                <span class="customer-name">{{ slotProps.data.name || 'N/A' }}</span>
              </template>
            </Column>

            <Column field="email" header="Email" sortable style="min-width: 220px">
              <template #body="slotProps">
                <span class="text-truncate" :title="slotProps.data.email">{{ slotProps.data.email || 'N/A' }}</span>
              </template>
            </Column>

            <Column field="phone" header="SĐT" sortable style="min-width: 130px">
              <template #body="slotProps">
                <span>{{ slotProps.data.phone || 'N/A' }}</span>
              </template>
            </Column>

            <Column field="address" header="Địa chỉ" style="min-width: 250px">
              <template #body="slotProps">
                <span class="text-truncate" :title="slotProps.data.address">{{ slotProps.data.address || 'N/A' }}</span>
              </template>
            </Column>

            <Column field="orders_count" header="Đơn hàng" sortable style="min-width: 110px" class="text-center">
              <template #body="slotProps">
                <span>{{ slotProps.data.orders_count || 0 }}</span>
              </template>
            </Column>

            <Column field="total_amount" header="Tổng chi tiêu" sortable style="min-width: 150px" class="text-center">
              <template #body="slotProps">
                <span>{{ formatCurrency(slotProps.data.total_amount || 0) }}</span>
              </template>
            </Column>

            <Column header="Tác vụ" style="min-width: 100px" frozen alignFrozen="right">
              <template #body="slotProps">
                <div class="action-group">
                  <Button icon="pi pi-pencil" class="p-button-rounded p-button-text p-button-warning btn-action"
                    @click="editCustomer(slotProps.data)" v-tooltip.top="'Sửa'" />
                  <Button icon="pi pi-trash" class="p-button-rounded p-button-text p-button-danger btn-action"
                    @click="deleteCustomer(slotProps.data)" v-tooltip.top="'Xóa'" />
                </div>
              </template>
            </Column>
          </DataTable>
        </div>
      </div>
    </div>

    <StaffCreateCustomerModal :visible="showCreateModal" @close="showCreateModal = false"
      @created="onCustomerCreated" />

    <StaffEditCustomerModal :visible="showEditModal" :customer="selectedCustomer" @close="showEditModal = false"
      @updated="onCustomerUpdated" />
  </div>
</template>

<script>
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Card from 'primevue/card'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import StaffCreateCustomerModal from './Modals/Create.vue'
import StaffEditCustomerModal from './Modals/Edit.vue'
import axios from 'axios'
import Swal from 'sweetalert2'

export default {
  name: 'StaffCustomerDashboard',
  components: {
    Button,
    InputText,
    Card,
    DataTable,
    Column,
    StaffCreateCustomerModal,
    StaffEditCustomerModal
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

    // Lọc khách hàng
    filteredCustomers() {
      const customers = this.localCustomers.length > 0 ? this.localCustomers : this.customers;

      if (!this.searchQuery || !this.searchQuery.trim()) {
        return customers;
      }

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
        // Có thể gọi API search ở đây nếu cần search server-side
      }, 200)
    },

    formatCurrency(amount) {
      return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
      }).format(amount)
    },

    editCustomer(customer) {
      this.selectedCustomer = customer
      this.showEditModal = true
    },

    async deleteCustomer(customer = null) {
      if (!customer?.id) return;

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
        const response = await axios.delete(`/staff/customers/${customer.id}`);

        if (response.data?.success) {
          this.localCustomers = this.localCustomers.filter(c => c.id !== customer.id);
          await Swal.fire({
            title: 'Thành công!',
            text: response.data.message || 'Đã xóa khách hàng',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
          });
        } else {
          await Swal.fire({ icon: 'warning', title: 'Không thành công', text: response.data?.message || 'Thao tác thất bại' });
        }
      } catch (error) {
        console.error('Error in deleteCustomer:', error);
        await Swal.fire({
          title: 'Lỗi!',
          text: error.response?.data?.message || 'Có lỗi xảy ra khi xóa khách hàng',
          icon: 'error'
        });
      }
    },

    onCustomerCreated(customer) {
      window.location.reload()
    },

    onCustomerUpdated(customer) {
      const index = this.localCustomers.findIndex(c => c.id === customer.id);
      if (index !== -1) {
        this.localCustomers[index] = customer;
      }
    }
  }
}
</script>

<style scoped>
/* === LAYOUT & HEADER === */
.customers-page {
  padding-bottom: 2rem;
  background-color: #f8f9fa;
  min-height: 100vh;
}

.header-control-bar {
  padding: 1rem 1.5rem;
  background: #fff;
  border-bottom: 1px solid #e9ecef;
  margin-bottom: 1.5rem;
}

.controls-section {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
}

.title-section h3 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 700;
  color: #1e293b;
  white-space: nowrap;
}

/* === SEARCH BOX === */
.search-container {
  flex: 1;
  min-width: 280px;
}

.search-wrapper .input-group {
  display: flex;
  align-items: center;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  overflow: hidden;
  background: #f8fafc;
  transition: all 0.2s;
}

.search-wrapper .input-group:focus-within {
  border-color: #4F46E5;
  box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.1);
  background: #fff;
}

.input-group-text {
  padding: 0.6rem 0.8rem;
  color: #64748b;
}

.search-input {
  border: none;
  padding: 0.6rem;
  width: 100%;
  outline: none;
  background: transparent;
  font-size: 0.95rem;
}

/* === UTILITY BUTTONS === */
.utility-options {
  display: flex;
  align-items: center;
  gap: 12px;
}

.btn-create {
  background: #0b1020 !important;
  border: none !important;
  color: white !important;
  padding: 8px 16px !important;
  border-radius: 8px !important;
  font-weight: 600 !important;
  box-shadow: 0 2px 4px rgba(11, 16, 32, 0.2);
  transition: transform 0.1s;
}

.btn-create:active {
  transform: translateY(1px);
}

.utility-icons {
  display: flex;
  gap: 8px;
}

.btn-icon {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #64748b;
  transition: all 0.2s;
}

.btn-icon:hover {
  background: #f1f5f9;
  color: #0b1020;
  border-color: #cbd5e1;
}

/* === STATS CARDS === */
.content-area {
  padding: 0 1.5rem;
}

.stats-row {
  margin-bottom: 1.5rem;
}

.stats-card {
  background: white;
  border-radius: 12px;
  padding: 1.25rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #f1f5f9;
  max-width: 300px;
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
  color: white;
  font-size: 1.25rem;
  box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
}

.bg-indigo {
  background: #4F46E5;
}

.stats-label {
  font-size: 0.875rem;
  color: #64748b;
  margin-bottom: 4px;
}

.stats-number {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
}

/* === DATA TABLE CUSTOMIZATION === */
.table-wrapper {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #f1f5f9;
  overflow: hidden;
}

.table-header {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #f1f3f5;
}

.table-title {
  font-size: 1.1rem;
  color: #334155;
  margin: 0;
  font-weight: 600;
}

/* Avatar Styling */
.customer-avatar {
  display: flex;
  justify-content: center;
}

.avatar-img {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #fff;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.avatar-placeholder {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.85rem;
  font-weight: 700;
  color: #64748b;
  border: 1px solid #e2e8f0;
}

/* Text & Utils */
.customer-name {
  font-weight: 600;
  color: #334155;
}

.text-truncate {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  display: block;
  max-width: 100%;
}

.action-group {
  display: flex;
  gap: 8px;
  justify-content: flex-end;
}

.btn-action {
  width: 32px !important;
  height: 32px !important;
}

/* === RESPONSIVE MEDIA QUERIES === */
@media (max-width: 768px) {
  .customers-page {
    padding-bottom: 1rem;
  }

  .header-control-bar {
    padding: 1rem;
  }

  /* Chuyển Header thành cột dọc */
  .controls-section {
    flex-direction: column;
    align-items: stretch;
  }

  .title-section {
    text-align: center;
    margin-bottom: 0.5rem;
  }

  .search-container {
    width: 100%;
    margin-bottom: 0.75rem;
  }

  .utility-options {
    justify-content: space-between;
    width: 100%;
  }

  .content-area {
    padding: 0 1rem;
  }

  .stats-card {
    max-width: 100%;
  }
}

@media (max-width: 480px) {

  /* Ẩn chữ trên nút Thêm mới, chỉ hiện icon */
  :deep(.btn-create .p-button-label) {
    display: none;
  }

  .utility-icons {
    gap: 4px;
  }

  /* Điều chỉnh kích thước text bảng */
  :deep(.p-datatable .p-datatable-tbody > tr > td) {
    font-size: 0.85rem;
    padding: 0.5rem 0.75rem;
  }
}
</style>

<style>
/* Global Styles Overrides for PrimeVue within this page */
/* Giữ nguyên logic cũ nhưng tinh chỉnh font-size */
:deep(.p-datatable) {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
}

:deep(.p-datatable .p-datatable-thead > tr > th) {
  font-size: 0.85rem;
  font-weight: 600;
  padding: 0.75rem 1rem;
  background: #f8fafc;
  color: #64748b;
  border-bottom: 2px solid #e2e8f0;
  white-space: nowrap;
}

:deep(.p-datatable .p-datatable-tbody > tr > td) {
  padding: 0.75rem 1rem;
  color: #334155;
  border-bottom: 1px solid #f1f5f9;
}

:deep(.p-datatable .p-datatable-tbody > tr:hover) {
  background: #f8fafc !important;
}

/* Pagination */
:deep(.p-paginator) {
  border-top: 1px solid #f1f5f9;
  justify-content: flex-end;
  padding: 0.75rem;
}

:deep(.p-paginator .p-paginator-pages .p-paginator-page),
:deep(.p-paginator .p-paginator-first),
:deep(.p-paginator .p-paginator-prev),
:deep(.p-paginator .p-paginator-next),
:deep(.p-paginator .p-paginator-last) {
  min-width: 2rem;
  height: 2rem;
  margin: 0 2px;
  border-radius: 6px;
  font-size: 0.85rem;
}

:deep(.p-paginator .p-paginator-pages .p-paginator-page.p-highlight) {
  background: #eff6ff;
  color: #4F46E5;
  border-color: #eff6ff;
}
</style>
