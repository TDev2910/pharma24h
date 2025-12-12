<template>
    <div class="services-page">
      <!-- Header Control Bar -->
      <div class="header-control-bar">
        <div class="controls-section"
          style="width:100%; display:flex; align-items:center; justify-content:center; gap:16px; flex-wrap:wrap;">
          <!-- Title Section -->
          <div class="title-section">
            <h4>Quản lý đặt lịch dịch vụ</h4>
          </div>
          <!-- Search Section -->
          <div style="flex:1; display:flex; justify-content:center;">
            <div class="search-wrapper">
              <div class="input-group">
                <span class="input-group-text">
                  <i class="pi pi-search"></i>
                </span>
                <input type="text" class="form-control" style="border-radius:8px;" placeholder="Theo tên khách hàng, SĐT"
                  v-model="searchQuery" @input="debounceSearch">
              </div>
            </div>
          </div>
          <!-- Utility Options -->
          <div class="ultility-options">
            <!-- Filter Status -->
            <Button icon="pi pi-filter" label="Lọc trạng thái" @click="showFilterModal" severity="secondary"
              style="background:#0b1020; border:none; color:white; font-weight:600; padding:6px 18px; border-radius:8px;" />
            <!-- Utility Icons -->
            <div class="utility-icons">
              <button class="btn" title="Chế độ xem">
                <i class="pi pi-list"></i>
              </button>
              <button class="btn" title="Xuất Excel">
                <i class="pi pi-file-excel"></i>
              </button>
              <button class="btn" title="Trợ giúp">
                <i class="pi pi-question-circle"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
  
      <!-- DataTable -->
      <div class="table-container">
        <DataTable :value="filteredBookings" v-model:expandedRows="expandedRows" stripedRows responsiveLayout="scroll"
          tableStyle="min-width: 50rem" :paginator="true" :row="5" :rows="pagination.per_page"
          :totalRecords="pagination.total"
          paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
          :rowsPerPageOptions="[5, 10, 25]"
          currentPageReportTemplate="Hiển thị {first} đến {last} trong tổng số {totalRecords} đặt lịch" dataKey="id"
          loadingIcon="pi pi-spinner" emptyMessage="Không có dữ liệu đặt lịch">
          <Column expander style="width: 3rem" />
          <Column field="customer_name" header="Tên khách hàng"></Column>
          <Column field="customer_phone" header="Số điện thoại"></Column>
          <Column field="service_name" header="Dịch vụ">
            <template #body="slotProps">
              <span class="service-name">{{ slotProps.data.service?.ten_dich_vu || '-' }}</span>
            </template>
          </Column>
          <Column field="booking_date" header="Ngày đặt">
            <template #body="slotProps">
              {{ formatDate(slotProps.data.booking_date) }}
            </template>
          </Column>
          <Column field="booking_time" header="Giờ đặt"></Column>
          <Column field="price" header="Giá">
            <template #body="slotProps">
              {{ formatCurrency(slotProps.data.price) }}
            </template>
          </Column>
          <Column field="status" header="Trạng thái">
            <template #body="slotProps">
              <span :class="getStatusClass(slotProps.data.status)">
                {{ getStatusText(slotProps.data.status) }}
              </span>
            </template>
          </Column>
          <Column field="payment_status" header="Thanh toán">
            <template #body="slotProps">
              <span :class="getPaymentStatusClass(slotProps.data.payment_status)">
                {{ getPaymentStatusText(slotProps.data.payment_status) }}
              </span>
            </template>
          </Column>
  
          <!-- Hiển thị chi tiết thông tin khi nhấn vào dropdown-->
          <template #expansion="slotProps">
            <div class="booking-detail-container">
              <!-- 2 danh mục thông tin và hành động-->
              <div class="detail-tabs">
                <button class="tab active" @click="switchTab('info')">Thông tin</button>
                <button class="tab" @click="switchTab('actions')">Hành động</button>
              </div>
  
              <!-- Danh mục thông tin và hành động-->
              <div class="detail-content">
                <!-- Tab Thông tin -->
                <div v-if="activeTab === 'info'" class="tab-content">
                  <div class="row">
                    <!-- Thông tin khách hàng -->
                    <div class="col-md-6">
                      <h6 class="text-primary mb-3">
                        <i class="pi pi-user"></i>Thông tin khách hàng
                      </h6>
                      <table class="table table-sm table-borderless">
                        <tbody>
                          <tr>
                            <td class="fw-bold" style="width: 140px;">Tên khách hàng:</td>
                            <td>{{ slotProps.data.customer_name }}</td>
                          </tr>
                          <tr>
                            <td class="fw-bold">Số điện thoại:</td>
                            <td>{{ slotProps.data.customer_phone }}</td>
                          </tr>
                          <tr>
                            <td class="fw-bold">Email:</td>
                            <td>{{ slotProps.data.customer_email || '-' }}</td>
                          </tr>
                          <tr>
                            <td class="fw-bold">Ngày đặt:</td>
                            <td>{{ formatDate(slotProps.data.booking_date) }}</td>
                          </tr>
                          <tr>
                            <td class="fw-bold">Giờ đặt:</td>
                            <td>{{ slotProps.data.booking_time }}</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
  
                    <!-- Thông tin dịch vụ -->
                    <div class="col-md-6">
                      <h6 class="text-primary mb-3">
                        <i class="pi pi-calendar"></i>Thông tin dịch vụ
                      </h6>
                      <table class="table table-sm table-borderless">
                        <tbody>
                          <tr>
                            <td class="fw-bold">Dịch vụ:</td>
                            <td>{{ slotProps.data.service?.ten_dich_vu || '-' }}</td>
                          </tr>
                          <tr>
                            <td class="fw-bold">Giá dịch vụ:</td>
                            <td>{{ formatCurrency(slotProps.data.price) }}</td>
                          </tr>
                          <tr>
                            <td class="fw-bold">Phương thức thanh toán:</td>
                            <td>{{ getPaymentMethodText(slotProps.data.payment_method) }}</td>
                          </tr>
                          <tr>
                            <td class="fw-bold">Trạng thái:</td>
                            <td>
                              <span :class="getStatusClass(slotProps.data.status)">
                                {{ getStatusText(slotProps.data.status) }}
                              </span>
                            </td>
                          </tr>
                          <tr>
                            <td class="fw-bold">Thanh toán:</td>
                            <td>
                              <span :class="getPaymentStatusClass(slotProps.data.payment_status)">
                                {{ getPaymentStatusText(slotProps.data.payment_status) }}
                              </span>
                            </td>
                          </tr>
                          <tr>
                            <td class="fw-bold">Ghi chú:</td>
                            <td>{{ slotProps.data.notes || '-' }}</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
  
                <!-- Tab Hành động -->
                <div v-if="activeTab === 'actions'" class="tab-content">
                  <div class="action-buttons-container">
                    <h6 class="text-primary mb-3">
                      <i></i>Quản lý đặt lịch
                    </h6>
  
                    <!-- Action buttons dựa trên trạng thái -->
                    <div class="action-buttons">
                      <Button v-if="slotProps.data.status === 'pending'" label="Xác nhận" icon="pi pi-check"
                        class="p-button-success p-button-sm me-2" @click="confirmBooking(slotProps.data)" />
  
                      <Button v-if="slotProps.data.status === 'confirmed' && slotProps.data.payment_status === 'unpaid'"
                        label="Đánh dấu đã thanh toán" icon="pi pi-money-bill" class="p-button-warning p-button-sm me-2"
                        @click="markAsPaid(slotProps.data)" />
  
                      <Button v-if="slotProps.data.status === 'confirmed' && slotProps.data.payment_status === 'paid'"
                        label="Hoàn thành" icon="pi pi-check-circle" class="p-button-success p-button-sm me-2"
                        @click="completeBooking(slotProps.data)" />
  
                      <Button v-if="['pending', 'confirmed'].includes(slotProps.data.status)" label="Hủy"
                        icon="pi pi-times" class="p-button-danger p-button-sm me-2"
                        @click="cancelBooking(slotProps.data)" />
  
                      <Button label="Xem chi tiết" icon="pi pi-eye" class="p-button-info p-button-sm"
                        @click="viewBookingDetail(slotProps.data)" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </DataTable>
      </div>
  
      <!-- Filter Modal -->
      <Dialog v-model:visible="showFilterDialog" header="Lọc theo trạng thái" :style="{ width: '500px' }" modal closable>
        <div class="filter-content">
          <div class="mb-3">
            <label class="form-label">Trạng thái đặt lịch:</label>
            <select v-model="statusFilter" class="form-select">
              <option value="">Tất cả</option>
              <option value="pending">Chờ xác nhận</option>
              <option value="confirmed">Đã xác nhận</option>
              <option value="completed">Hoàn thành</option>
              <option value="cancelled">Đã hủy</option>
            </select>
          </div>
  
          <div class="mb-3">
            <label class="form-label">Trạng thái thanh toán:</label>
            <select v-model="paymentStatusFilter" class="form-select">
              <option value="">Tất cả</option>
              <option value="unpaid">Chưa thanh toán</option>
              <option value="paid">Đã thanh toán</option>
            </select>
          </div>
        </div>
  
        <template #footer>
          <Button label="Hủy" icon="pi pi-times" @click="showFilterDialog = false" severity="secondary" />
          <Button label="Áp dụng" icon="pi pi-check" @click="applyFilter" />
        </template>
      </Dialog>
    </div>
  </template>
  
  <script>
  import Button from 'primevue/button'
  import DataTable from 'primevue/datatable'
  import Column from 'primevue/column'
  import Dialog from 'primevue/dialog'
  import axios from 'axios'
  
  export default {
    name: 'OrderServices',
    components: {
      Button,
      DataTable,
      Column,
      Dialog
    },
  
    data() {
      return {
        showFilterDialog: false,
        searchQuery: '',
        loading: false,
        bookings: [],
        expandedRows: {},
        activeTab: 'info',
        statusFilter: '',
        paymentStatusFilter: '',
        pagination: {
          current_page: 1,
          last_page: 1,
          per_page: 10,
          total: 0,
          from: 0,
          to: 0
        },
        searchTimeout: null
      }
    },
  
    computed: {
      filteredBookings() {
        let filtered = this.bookings
  
        if (this.searchQuery && this.searchQuery.trim()) {
          const term = this.searchQuery.toLowerCase().trim()
          filtered = filtered.filter(booking => {
            const name = (booking.customer_name || '').toLowerCase()
            const phone = (booking.customer_phone || '').toLowerCase()
            const serviceName = (booking.service?.ten_dich_vu || '').toLowerCase()
            return name.includes(term) || phone.includes(term) || serviceName.includes(term)
          })
        }
  
        // Filter by status
        if (this.statusFilter) {
          filtered = filtered.filter(booking => booking.status === this.statusFilter)
        }
  
        // Filter by payment status
        if (this.paymentStatusFilter) {
          filtered = filtered.filter(booking => booking.payment_status === this.paymentStatusFilter)
        }
  
        return filtered
      }
    },
  
    mounted() {
      this.loadBookings()
    },
  
    methods: {
      // Load danh sách booking từ API
      async loadBookings() {
        this.loading = true
        try {
          const response = await axios.get('/staff/service-bookings/api', {
            params: {
              search: this.searchQuery,
              per_page: this.pagination.per_page,
              page: this.pagination.current_page
            }
          })
  
          if (response.data.success) {
            this.bookings = response.data.data
            this.pagination = response.data.pagination
          } else {
            console.error('API returned success: false')
          }
        } catch (error) {
          console.error('Error loading bookings:', error)
          this.$toast.add({
            severity: 'error',
            summary: 'Lỗi',
            detail: error.response?.data?.message || 'Không thể tải danh sách ',
            life: 5000
          })
        } finally {
          this.loading = false
        }
      },
  
      // Search functionality với debounce
      debounceSearch() {
        clearTimeout(this.searchTimeout)
        this.searchTimeout = setTimeout(() => {
          // Filter sẽ tự động áp dụng thông qua computed property
        }, 200)
      },
  
      // Modal methods
      showFilterModal() {
        this.showFilterDialog = true
      },
  
      applyFilter() {
        this.showFilterDialog = false
        // Filter sẽ tự động áp dụng thông qua computed property
      },
  
      // Tab switching
      switchTab(tab) {
        this.activeTab = tab
      },
  
      // Xác nhận đặt lịch
      async confirmBooking(booking) {
        if (confirm(`Xác nhận đặt lịch cho ${booking.customer_name}?`)) {
          try {
            const response = await axios.post(`/staff/service-bookings/${booking.id}/confirm`)
            if (response.data.success) {
              this.$toast.add({
                severity: 'success',
                summary: 'Thành công',
                detail: 'Đã xác nhận đặt lịch',
                life: 3000
              })
              await this.loadBookings()
            }
          } catch (error) {
            console.error('Error confirming booking:', error)
            this.$toast.add({
              severity: 'error',
              summary: 'Lỗi',
              detail: error.response?.data?.message || 'Không thể xác nhận đặt lịch',
              life: 3000
            })
          }
        }
      },
  
      // Đánh dấu đã thanh toán
      async markAsPaid(booking) {
        if (confirm(`Đánh dấu đã thanh toán cho ${booking.customer_name}?`)) {
          try {
            const response = await axios.post(`/staff/service-bookings/${booking.id}/mark-paid`)
            if (response.data.success) {
              this.$toast.add({
                severity: 'success',
                summary: 'Thành công',
                detail: 'Đã đánh dấu thanh toán',
                life: 3000
              })
              await this.loadBookings()
            }
          } catch (error) {
            console.error('Error marking as paid:', error)
            this.$toast.add({
              severity: 'error',
              summary: 'Lỗi',
              detail: error.response?.data?.message || 'Không thể đánh dấu thanh toán',
              life: 3000
            })
          }
        }
      },
  
      // Hoàn thành dịch vụ
      async completeBooking(booking) {
        if (confirm(`Hoàn thành dịch vụ cho ${booking.customer_name}?`)) {
          try {
            const response = await axios.post(`/staff/service-bookings/${booking.id}/complete`)
            if (response.data.success) {
              this.$toast.add({
                severity: 'success',
                summary: 'Thành công',
                detail: 'Đã hoàn thành dịch vụ',
                life: 3000
              })
              await this.loadBookings()
            }
          } catch (error) {
            console.error('Error completing booking:', error)
            this.$toast.add({
              severity: 'error',
              summary: 'Lỗi',
              detail: error.response?.data?.message || 'Không thể hoàn thành dịch vụ',
              life: 3000
            })
          }
        }
      },
  
      // Hủy đặt lịch
      async cancelBooking(booking) {
        if (confirm(`Hủy đặt lịch cho ${booking.customer_name}?`)) {
          try {
            const response = await axios.post(`/staff/service-bookings/${booking.id}/cancel`)
            if (response.data.success) {
              this.$toast.add({
                severity: 'success',
                summary: 'Thành công',
                detail: 'Đã hủy đặt lịch',
                life: 3000
              })
              await this.loadBookings()
            }
          } catch (error) {
            console.error('Error cancelling booking:', error)
            this.$toast.add({
              severity: 'error',
              summary: 'Lỗi',
              detail: error.response?.data?.message || 'Không thể hủy đặt lịch',
              life: 3000
            })
          }
        }
      },
  
      // Xem chi tiết đặt lịch
      viewBookingDetail(booking) {
        // TODO: Implement view detail modal
        console.log('View booking detail:', booking)
      },
  
      // Helper methods
      getStatusText(status) {
        const statusMap = {
          'pending': 'Chờ xác nhận',
          'confirmed': 'Đã xác nhận',
          'completed': 'Hoàn thành',
          'cancelled': 'Đã hủy'
        }
        return statusMap[status] || status || '-'
      },
  
      getStatusClass(status) {
        const classMap = {
          'pending': 'badge bg-warning',
          'confirmed': 'badge bg-info',
          'completed': 'badge bg-success',
          'cancelled': 'badge bg-danger'
        }
        return classMap[status] || 'badge bg-secondary'
      },
  
      getPaymentStatusText(status) {
        const statusMap = {
          'unpaid': 'Chưa thanh toán',
          'paid': 'Đã thanh toán'
        }
        return statusMap[status] || status || '-'
      },
  
      getPaymentStatusClass(status) {
        const classMap = {
          'unpaid': 'badge bg-danger',
          'paid': 'badge bg-success'
        }
        return classMap[status] || 'badge bg-secondary'
      },
  
      getPaymentMethodText(method) {
        const methodMap = {
          'pay_at_pharmacy': 'Thanh toán tại quầy',
          'online': 'Thanh toán online'
        }
        return methodMap[method] || method || '-'
      },
  
      formatDate(date) {
        if (!date) return '-'
        return new Date(date).toLocaleDateString('vi-VN')
      },
  
      formatCurrency(amount) {
        if (!amount) return '0 VNĐ'
        return new Intl.NumberFormat('vi-VN', {
          style: 'currency',
          currency: 'VND'
        }).format(amount)
      }
    }
  }
  </script>
  
  <style>
  /* Import CSS file - CSS thông thường được tách ra */
  @import '@Staff/order-services.css';
  </style>
  
  <style>
  /* Giữ nguyên tất cả :deep() trong file Vue */
  :deep(.p-datatable .p-button),
  :deep(.p-datatable .p-button .p-button-icon),
  :deep(.p-datatable .p-button .p-button-label) {
    opacity: 1 !important;
    visibility: visible !important;
    display: inline-flex !important;
  }
  
  /* Đảm bảo button trong DataTable luôn hiển thị */
  :deep(.p-datatable tbody tr td .p-button) {
    opacity: 1 !important;
    visibility: visible !important;
  }
  
  /* DataTable Styling */
  :deep(.p-datatable) {
    border-radius: 8px;
    overflow: hidden;
    border: 2px solid #000;
  }
  
  :deep(.p-datatable .p-datatable-header) {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 16px 20px;
  }
  
  :deep(.p-datatable .p-datatable-thead > tr > th) {
    background: #B4DEBD;
    color: #495057;
    font-weight: 600;
    border-bottom: 2px solid #e9ecef;
    padding: 16px 20px;
    font-size: 14px;
  }
  
  :deep(.p-datatable .p-datatable-tbody > tr) {
    transition: all 0.2s ease;
    border-bottom: 1px solid #f1f3f4;
  }
  
  /* Striped rows styling */
  :deep(.p-datatable .p-datatable-tbody > tr:nth-child(even)) {
    background: #f8f9fa;
  }
  
  :deep(.p-datatable .p-datatable-tbody > tr:nth-child(odd)) {
    background: #ffffff;
  }
  
  :deep(.p-datatable .p-datatable-tbody > tr:hover) {
    background: #e3f2fd !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  }
  
  :deep(.p-datatable .p-datatable-tbody > tr > td) {
    padding: 16px 20px;
    color: #495057;
    font-size: 14px;
    vertical-align: middle;
    border-right: 1px solid #e9ecef;
    border-bottom: 1px solid #f1f3f4;
  }
  
  /* Loại bỏ viền dọc của cột cuối cùng */
  :deep(.p-datatable .p-datatable-tbody > tr > td:last-child) {
    border-right: none;
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
  
  /* Responsive improvements */
  @media (max-width: 768px) {
    :deep(.p-datatable .p-datatable-thead > tr > th),
    :deep(.p-datatable .p-datatable-tbody > tr > td) {
      padding: 12px 8px;
      font-size: 13px;
    }
  }
  </style>