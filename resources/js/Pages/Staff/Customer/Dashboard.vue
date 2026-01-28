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
              <span class="input-group-text"><i class="pi pi-search"></i></span>
              <input type="text" class="form-control" placeholder="Tìm kiếm theo tên, email, sđt..."
                v-model="searchQuery" @input="handleSearch">
            </div>
          </div>
        </div>

        <div class="ultility-options">
          <Button icon="pi pi-plus" label="Khách hàng" @click="openCreateModal" class="btn-create-customer" />

          <div class="utility-icons">
            <button class="btn-icon"><i class="pi pi-list"></i></button>
            <button class="btn-icon"><i class="pi pi-cog"></i></button>
          </div>
        </div>
      </div>
    </div>

    <div class="content-area">
      <div class="stats-row">
        <div class="stats-card">
          <div class="stats-card-inner">
            <div class="stats-icon" style="background: #4F46E5;">
              <i class="fas fa-users"></i>
            </div>
            <div>
              <div class="stats-label">Tổng khách hàng</div>
              <div class="stats-number">{{ stats.totalCustomers }}</div>
            </div>
          </div>
        </div>
      </div>

      <div>
        <div class="table-header">
          <h3 class="table-title">Danh sách dữ liệu khách hàng</h3>
        </div>

        <div class="table-container">
          <DataTable :value="customers.data" :lazy="true" :paginator="true" :rows="customers.per_page"
            :totalRecords="customers.total" :first="(customers.current_page - 1) * customers.per_page"
            @page="onPageChange" removableSort tableStyle="min-width: 50rem" class="customers-table"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
            :rowsPerPageOptions="[5, 10, 20, 50]"
            currentPageReportTemplate="Hiển thị {first} đến {last} trong tổng số {totalRecords} khách hàng">

            <Column field="avatar" header="Avatar" style="width: 10%">
              <template #body="slotProps">
                <div class="customer-avatar">
                  <img v-if="slotProps.data.avatar_url" :src="slotProps.data.avatar_url"
                    style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                  <div v-else class="avatar-placeholder">
                    {{ slotProps.data.name ? slotProps.data.name.substring(0, 2).toUpperCase() : 'N/A' }}
                  </div>
                </div>
              </template>
            </Column>

            <Column field="name" header="Tên khách hàng" style="width: 15%"></Column>
            <Column field="email" header="Email" style="width: 20%"></Column>
            <Column field="phone" header="Số điện thoại" style="width: 12%"></Column>
            <Column field="address" header="Địa chỉ" style="width: 20%">
              <template #body="slotProps">{{ slotProps.data.address || 'N/A' }}</template>
            </Column>

            <Column field="total_amount" header="Chi tiêu" style="width: 10%" class="text-center">
              <template #body="slotProps">
                <span>{{ formatCurrency(slotProps.data.total_amount) }}</span>
              </template>
            </Column>

            <Column header="Thao tác" style="width: 15%; text-align: center;">
              <template #body="slotProps">
                <div class="flex justify-content-center gap-2">
                  <Button icon="pi pi-pencil" text rounded severity="warning" size="small"
                    @click="editCustomer(slotProps.data)" v-tooltip.top="'Chỉnh sửa'" />
                  <Button icon="pi pi-trash" text rounded severity="danger" size="small"
                    @click="deleteCustomer(slotProps.data)" v-tooltip.top="'Xóa'" />
                </div>
              </template>
            </Column>
          </DataTable>
        </div>
      </div>
    </div>
  </div>

  <StaffCreateCustomerModal :visible="showCreateModal" @close="showCreateModal = false" @created="refreshPage" />

  <StaffEditCustomerModal :visible="showEditModal" :customer="selectedCustomer" @close="showEditModal = false"
    @updated="refreshPage" />
</template>

<script>
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import StaffCreateCustomerModal from './Modals/Create.vue'
import StaffEditCustomerModal from './Modals/Edit.vue'
import { router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import debounce from 'lodash/debounce'

export default {
  name: 'StaffCustomerDashboard',
  components: {
    Button, InputText, DataTable, Column, StaffCreateCustomerModal, StaffEditCustomerModal
  },

  props: {
    stats: Object,
    customers: Object, // Object chứa pagination data từ Controller
    filters: Object,   // Chứa search query
  },

  data() {
    return {
      searchQuery: this.filters.search || '',
      isLoading: false,
      showCreateModal: false,
      showEditModal: false,
      selectedCustomer: null,
    }
  },

  methods: {
    onPageChange(event) {
      this.isLoading = true;
      const page = event.page + 1;
      const rows = event.rows;

      router.get('/staff/customers', {
        search: this.searchQuery,
        page: page,
        per_page: rows
      }, {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => this.isLoading = false
      });
    },

    // tìm kiếm: Debounce 
    handleSearch: debounce(function () {
      this.isLoading = true;
      router.get('/staff/customers', {
        search: this.searchQuery,
        page: 1 // Reset về trang 1 khi tìm
      }, {
        preserveState: true,
        replace: true,
        onFinish: () => this.isLoading = false
      });
    }, 300),

    // 3. RELOAD: Gọi khi Modal thêm/sửa thành công
    refreshPage() {
      router.reload({ only: ['customers', 'stats'] });
    },

    formatCurrency(amount) {
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
    },

    openCreateModal() { this.showCreateModal = true },

    editCustomer(customer) {
      this.selectedCustomer = customer;
      this.showEditModal = true;
    },

    // 4. xóa khách hàng
    deleteCustomer(customer) {
      Swal.fire({
        title: 'Xác nhận xóa',
        text: `Bạn có chắc chắn muốn xóa "${customer.name}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy'
      }).then((result) => {
        if (result.isConfirmed) {
          router.delete(`/staff/customers/${customer.id}`, {
            onSuccess: () => {
              Swal.fire('Thành công!', 'Đã xóa khách hàng.', 'success');
            },
            onError: () => {
              Swal.fire('Lỗi!', 'Không thể xóa khách hàng.', 'error');
            }
          });
        }
      });
    }
  }
}
</script>

<style scoped>
@import url('../../../../css/Staff/customer/dashboard.css');
</style>