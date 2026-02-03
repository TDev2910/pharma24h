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
          <Button label="Khách hàng" icon="pi pi-plus" class="btn-create-customer" @click="openCreateModal" />
          <button class="btn-icon"><i class="pi pi-bars"></i></button>
          <button class="btn-icon"><i class="pi pi-cog"></i></button>
        </div>
      </div>
    </div>

    <div class="content-area">
      <div class="stats-row">
        <!-- Card 1: Total Customers -->
        <div class="stats-card">
          <div class="stats-card-inner">
            <div class="stats-icon icon-blue"><i class="fas fa-users"></i></div>
            <div class="stats-info">
              <div class="stats-label">Tổng khách hàng</div>
              <div class="stats-number">{{ stats.totalCustomers || 0 }}</div>
            </div>
          </div>
        </div>
        <!-- Card 2: Active Customers -->
        <div class="stats-card">
          <div class="stats-card-inner">
            <div class="stats-icon icon-orange"><i class="fas fa-user-check"></i></div>
            <div class="stats-info">
              <div class="stats-label">Khách hàng hoạt động</div>
              <div class="stats-number">{{ stats.activeCustomers || 0 }}</div>
            </div>
          </div>
        </div>
        <!-- Card 3: New Customers -->
        <div class="stats-card">
          <div class="stats-card-inner">
            <div class="stats-icon icon-dark"><i class="fas fa-clock"></i></div>
            <div class="stats-info">
              <div class="stats-label">Khách hàng mới</div>
              <div class="stats-number">{{ stats.newCustomers || 0 }}</div>
            </div>
          </div>
        </div>
      </div>

      <h4 class="table-section-title">Danh sách dữ liệu khách hàng</h4>

      <div class="table-container">
        <DataTable :value="customers.data" :lazy="true" :paginator="true" :rows="customers.per_page"
          :totalRecords="customers.total" :first="(customers.current_page - 1) * customers.per_page"
          @page="onPageChange" removableSort class="customers-table">

          <Column field="avatar" header="Avatar">
            <template #body="{ data }">
              <div class="customer-avatar">
                <img v-if="data.avatar_url" :src="data.avatar_url"
                  style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover;" />
                <div v-else class="avatar-placeholder" :style="{ backgroundColor: getAvatarColor(data.name) }">
                  {{ data.name.substring(0, 2).toUpperCase() }}
                </div>
              </div>
            </template>
          </Column>
          <Column field="name" header="Tên khách hàng" bodyClass="font-bold text-gray-900" />
          <Column field="email" header="Email" />
          <Column field="phone" header="Số điện thoại" />
          <Column field="address" header="Địa chỉ" :style="{ maxWidth: '300px', minWidth: '200px' }">
            <template #body="{ data }">
              <div class="address-content">
                {{ data.address || 'N/A' }}
              </div>
            </template>
          </Column>

          <Column header="Thao tác" style="text-align: center;">
            <template #body="{ data }">
              <div class="flex gap-2 justify-content-center">
                <Button icon="pi pi-pencil" text rounded class="text-blue-500 hover:bg-blue-50"
                  @click="editCustomer(data)" />
                <Button icon="pi pi-trash" text rounded class="text-red-500 hover:bg-red-50"
                  @click="deleteCustomer(data)" />
              </div>
            </template>
          </Column>
        </DataTable>
      </div>
    </div>

    <StaffCreateCustomerModal :visible="showCreateModal" @close="showCreateModal = false" />
    <StaffEditCustomerModal :visible="showEditModal" :customer="selectedCustomer" @close="showEditModal = false" />
  </div>
</template>

<script>
import { ref, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3'; // Import usePage
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Swal from 'sweetalert2';
import debounce from 'lodash/debounce';
import StaffCreateCustomerModal from './Modals/Create.vue';
import StaffEditCustomerModal from './Modals/Edit.vue';

export default {
  name: 'StaffCustomerDashboard',
  components: { Button, DataTable, Column, StaffCreateCustomerModal, StaffEditCustomerModal },
  props: {
    stats: Object,
    customers: Object,
    filters: Object,
  },
  // Dùng setup() để lắng nghe Flash Message
  setup() {
    const page = usePage();

    // Lắng nghe thay đổi của Flash message từ Server
    watch(() => page.props.flash, (flash) => {
      if (flash?.success) {
        Swal.fire({
          icon: 'success',
          title: 'Thành công!',
          text: flash.success,
          timer: 2000,
          showConfirmButton: false,
          position: 'top-end',
          toast: true
        });
      }
    }, { deep: true });

    return {};
  },
  data() {
    return {
      searchQuery: this.filters.search || '',
      showCreateModal: false,
      showEditModal: false,
      selectedCustomer: null,
    };
  },
  methods: {
    onPageChange(event) {
      router.get('/staff/customers', {
        search: this.searchQuery,
        page: event.page + 1,
        per_page: event.rows
      }, { preserveState: true, preserveScroll: true });
    },
    handleSearch: debounce(function () {
      router.get('/staff/customers', { search: this.searchQuery, page: 1 },
        { preserveState: true, replace: true });
    }, 300),

    openCreateModal() { this.showCreateModal = true; },

    editCustomer(customer) {
      this.selectedCustomer = customer;
      this.showEditModal = true;
    },

    deleteCustomer(customer) {
      Swal.fire({
        title: 'Xóa khách hàng?',
        text: `Bạn muốn xóa ${customer.name}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Xóa'
      }).then((result) => {
        if (result.isConfirmed) {
          router.delete(`/staff/customers/${customer.id}`, {
          });
        }
      });
    },
    formatCurrency(value) {
      if (!value) return '0 đ';
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
    },
    getAvatarColor(name) {
      if (!name) return '#e5e7eb';
      const colors = ['#FCD34D', '#F87171', '#60A5FA', '#34D399', '#A78BFA', '#F472B6'];
      let hash = 0;
      for (let i = 0; i < name.length; i++) {
        hash = name.charCodeAt(i) + ((hash << 5) - hash);
      }
      return colors[Math.abs(hash) % colors.length];
    }
  }
};
</script>

<style scoped>
@import url('../../../../css/Staff/customer/dashboard.css');
</style>