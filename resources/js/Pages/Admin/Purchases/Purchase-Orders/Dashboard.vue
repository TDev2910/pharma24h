<template>
  <div class="purchase-orders-page">
    <!-- Header Control Bar -->
    <div class="header-control-bar">
        <div class="controls-section" style="width:100%; display:flex; align-items:center; justify-content:center; gap:16px; flex-wrap:wrap;">
            <!-- Title Section -->
            <div class="title-section">
                <h3>Đặt hàng</h3>
            </div>
            <!-- Search Section -->
            <div style="flex:1; display:flex; justify-content:center;">
                <div class="search-wrapper" style="width: 100%; max-width: 500px;">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="pi pi-search"></i>
                        </span>
                        <input 
                            type="text" 
                            class="form-control" 
                            style="border-radius:8px;" 
                            placeholder="Tìm kiếm theo mã đặt hàng, tên nhà cung cấp" 
                            v-model="searchQuery" 
                            @input="debounceSearch"
                        >
                    </div>
                </div>
                
                <!-- Thông báo số kết quả -->
                <div v-if="isSearching" class="search-results-info mt-2 text-center">
                  <small class="text-muted">
                    Hiển thị {{ searchResultsCount }} / {{ orders.length }} kết quả
                    <span v-if="!hasSearchResults" class="text-warning"> - Không tìm thấy kết quả nào</span>
                  </small>
                </div>
            </div>
    <!-- Utility Options -->
    <div class="ultility-options">
      <!-- Thêm đặt hàng -->
        <Button 
          icon="pi pi-plus"
          label="Đặt hàng"
          @click="showCreate"
          severity="secondary"
          style="background:#0b1020; border:none; color:white; font-weight:600; padding:6px 18px; border-radius:8px;"
        />
        
        <!-- Xuất file Excel -->
        <Button 
          icon="pi pi-file-excel"
          :label="isExporting ? 'Đang xuất...' : 'Xuất file'"
          :disabled="isExporting"
          @click="exportToExcel"
          severity="secondary"
          style="background:#3A6F43; border:none; color:white; font-weight:600; padding:6px 18px; border-radius:8px;"
        />
        
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

    <!-- Main Content with 2 columns -->
    <div class="main-content">
      <!-- Left Sidebar -->
      <div class="left-sidebar">
        <div class="filter-section">
          <h5>Trạng thái</h5>
          <div class="filter-options">
            <div class="checkbox-item">
              <input type="checkbox" id="ordered" v-model="filters.ordered" />
              <label for="ordered">Đã đặt hàng</label>
            </div>
            <div class="checkbox-item">
              <input type="checkbox" id="cancelled" v-model="filters.cancelled" />
              <label for="cancelled">Đã hủy</label>
            </div>
          </div>
        </div>

        <div class="filter-section">
          <h5>Thời gian</h5>
          <div class="radio-options">
          </div>
          <div v-if="filters.timeRange === 'thisMonth'" class="date-picker-container d-flex align-items-center" style="gap:8px;">
            <DatePicker 
              v-model="filters.fromDate" 
              showIcon 
              fluid 
              iconDisplay="input" 
              placeholder="Từ ngày" 
            />
            <span class="text-muted">→</span>
            <DatePicker 
              v-model="filters.toDate" 
              showIcon 
              fluid 
              iconDisplay="input" 
              placeholder="Đến ngày" 
            />
          </div>
        </div>
      </div>

      <!-- Right Main Content -->
      <div class="right-content">
        <!-- DataTable -->
        <div class="table-container">
            <DataTable 
                :value="filteredOrders" 
                v-model:expandedRows="expandedRows"
                stripedRows
                responsiveLayout="scroll"
                tableStyle="min-width: 50rem"
                :paginator="true"
                :row="5"
                :rows="pagination.per_page"
                :totalRecords="pagination.total"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5,10,25]"
                currentPageReportTemplate="Hiển thị {first} đến {last} trong tổng số {totalRecords} phiếu đặt hàng"
                dataKey="id"
                loadingIcon="pi pi-spinner"
                emptyMessage="Không có dữ liệu phiếu đặt hàng">
                <Column expander style="width: 3rem" />
                <Column field="order_code" style="width:155px;" header="Mã đặt hàng"></Column>
                <Column field="created_at" header="Thời gian">
                    <template #body="slotProps">
                        {{ formatDate(slotProps.data.created_at) }}
                    </template>
                </Column>
                <Column field="supplier_name" header="Nhà cung cấp"></Column>
                <Column field="ma_nha_cung_cap" style="width:120px;" header="Mã NCC"></Column>
                <Column field="total_amount" style="width:120px;" header="Cần trả NCC">
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.total_amount) }}
                    </template>
                </Column>
                <Column field="status" style="width:120px;" header="Trạng thái">
                    <template #body="slotProps">
                        {{ getStatusText(slotProps.data.status) }}
                    </template>
                </Column>
                
                <!-- Hiển thị chi tiết thông tin khi nhấn vào dropdown-->
                <template #expansion="slotProps">
                    <div class="purchase-order-detail-container">
                      <!-- 2 danh mục thông tin và sản phẩm-->
                        <div class="detail-tabs">  
                            <button class="tab active" @click="switchTab('info')">Thông tin</button>
                            <button class="tab" @click="switchTab('products')">Lịch sử đặt hàng</button>
                        </div>
                        
                        <!-- Danh mục thông tin và sản phẩm-->
                        <div class="detail-content">
                            <!-- Tab Thông tin -->
                            <div v-if="activeTab === 'info'" class="tab-content">
                                <div class="row">
                                    <!-- Thông tin chung -->
                                    <div class="col-md-6">
                                        <h6 class="text-primary mb-3">
                                            <i></i>Thông tin chung
                                        </h6>
                                        <table class="table table-sm table-borderless">
                                            <tbody>
                                                <tr>
                                                    <td class="fw-bold" style="width: 140px;">Mã đặt hàng:</td>
                                                    <td>{{ slotProps.data.order_code }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Nhà cung cấp:</td>
                                                    <td>{{ slotProps.data.supplier_name }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Mã NCC:</td>
                                                    <td>{{ slotProps.data.ma_nha_cung_cap }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Ghi chú:</td>
                                                    <td>{{ slotProps.data.note }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Tổng tiền hàng:</td>
                                                    <td>
                                                        <span class="badge bg-info">{{ formatCurrency(slotProps.data.total_amount) }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Giảm giá:</td>
                                                    <td>
                                                        <span class="badge bg-warning">{{ formatCurrency(slotProps.data.remaining_amount) }}</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Thông tin bổ sung -->
                                    <div class="col-md-6">
                                        <h6 class="text-primary mb-3">
                                            <i></i>Thông tin bổ sung
                                        </h6>
                                        <table class="table table-sm table-borderless">
                                            <tbody>
                                                <tr>
                                                    <td class="fw-bold">Cần trả nhà cung cấp:</td>
                                                    <td>
                                                        <span class="badge bg-success">{{ formatCurrency(slotProps.data.total_amount) }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Trạng thái:</td>
                                                    <td>
                                                        <span class="badge bg-success">{{ getStatusText(slotProps.data.status) }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Ngày tạo:</td>
                                                    <td>{{ formatDate(slotProps.data.created_at) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        
                                        <!-- Action buttons chỉnh sửa và xóa-->
                                        <div class="mt-3">
                                          <Button 
                                              icon="pi pi-file-excel"
                                              :label="isExporting ? 'Đang xuất...' : 'Xuất file'"
                                              :disabled="isExporting"
                                              @click="exportSinglePurchaseOrder(slotProps.data)"
                                              severity="secondary"
                                              style="background:#3A6F43; border:none; color:white; font-weight:600; padding:6px 18px; border-radius:8px;"/>                                           
                                            <Button 
                                              icon="pi pi-trash" 
                                              label="Xóa"
                                              @click="deletePurchaseOrder(slotProps.data)"
                                              severity="secondary"
                                              style="background:#DC143C; border:none; color:white; font-weight:600; padding:6px 18px; border-radius:8px;"/>                                                                                          
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tab Sản phẩm -->
                            <div v-if="activeTab === 'products'" class="tab-content">
                                <div class="text-center text-muted py-4">
                                    <i class="pi pi-box" style="font-size: 2rem;"></i>
                                    <p class="mt-2">Danh sách sản phẩm đặt hàng</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </DataTable>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Button from 'primevue/button'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import DatePicker from 'primevue/datepicker'
import { usePage } from '@inertiajs/vue3'
import { useToast } from 'primevue/usetoast'
import axios from 'axios'

export default {
  name: 'PurchaseOrdersDashboard',
  components: {
    Button,
    DataTable,
    Column,
    DatePicker
  },
  
  setup() {
    const { props } = usePage()
    const toast = useToast()
    
    return {
      orders: props.orders || [],
      toast
    }
  },
  
  data() {
    return {
      filteredOrders: [],
      showModal: false,
      expandedRows: {},
      activeTab: 'info',
      filters: {
        temp: true,
        ordered: true,
        cancelled: false,
        timeRange: 'thisMonth',
        fromDate: null,
        toDate: null
      },      
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
        from: 1,
        to: 0
      },
      isExporting: false
    }
  },

  computed: {
    // Computed property để tối ưu performance
    searchResultsCount() {
      return this.filteredOrders.length
    },
    
    hasSearchResults() {
      return this.searchQuery && this.filteredOrders.length > 0
    },
    
    isSearching() {
      return this.searchQuery && this.searchQuery.trim().length > 0
    }
  },

  methods: {
    // Format date to local timezone
    formatDateToLocal(dateValue) {
      if (!dateValue) return null;
      const date = new Date(dateValue);
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const day = String(date.getDate()).padStart(2, '0');
      return `${year}-${month}-${day}`;
    },
    
    //Lọc kết quả 
    debounceSearch() {
      clearTimeout(this.debounceTimer)
      this.debounceTimer = setTimeout(() => {
        this.searchOrders()
      }, 200) 
    },

    matches(item, term) {
      const code = (item.order_code || '').toLowerCase()
      const name = (item.supplier_name || '').toLowerCase()
      const note = (item.note || '').toLowerCase()
      return code.includes(term) || name.includes(term) || note.includes(term)
    },

    // Function tìm kiếm 
    searchOrders() {
      const term = this.searchQuery.toLowerCase().trim()
      
      if (!term) {
        // Nếu không có từ khóa, hiển thị tất cả
        this.filteredOrders = [...this.orders]
      } else {
        // Lọc kết quả sử dụng hàm matches
        this.filteredOrders = this.orders.filter(o => this.matches(o, term))
      }
    },

    // Chuyển hướng đến trang tạo phiếu đặt hàng
    showCreate() {
      this.$inertia.visit('/admin/purchase-orders/create')
    },

    // Tab switching
    switchTab(tab) {
      this.activeTab = tab
    },

    // Format date
    formatDate(date) {
      if (!date) return '-'
      return new Date(date).toLocaleDateString('vi-VN')
    },

    // Format currency
    formatCurrency(amount) {
      if (!amount) return '0 VNĐ'
      return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
      }).format(amount)
    },

    // Get status text
    getStatusText(status) {
      const statusMap = {
        'pending': 'Chờ xử lý',
        'ordered': 'Đã đặt hàng',
        'completed': 'Hoàn thành',
        'cancelled': 'Đã hủy',
        'imported': 'Đã đặt hàng'
      }
      return statusMap[status] || status || '-'
    },

    // Edit purchase order
    editPurchaseOrder(purchaseOrder) {
      console.log('Edit purchase order:', purchaseOrder)
      // TODO: Implement edit functionality
      this.$inertia.visit(`/admin/purchase-orders/${purchaseOrder.id}/edit`)
    },

    // Delete purchase order
    deletePurchaseOrder(purchaseOrder) {
      if (confirm(`Bạn có chắc muốn xóa phiếu đặt hàng ${purchaseOrder.order_code}?`)) {
        console.log('Delete purchase order:', purchaseOrder)
        // TODO: Implement delete functionality
        this.$inertia.delete(`/admin/purchase-orders/${purchaseOrder.id}`)
      }
    },

    // Export to Excel
    async exportToExcel() {
      try {
        this.isExporting = true;
        
        // Tạo URL với filter
        const params = new URLSearchParams();
        
        // Filter theo search
        if (this.searchQuery?.trim()) {
          params.append('search', this.searchQuery.trim());
        }
        
        // Filter theo status - map đúng với database
        const statusFilters = [];
        if (this.filters.temp) statusFilters.push('temp');
        if (this.filters.ordered) statusFilters.push('imported'); // Map ordered -> imported
        if (this.filters.cancelled) statusFilters.push('cancelled');
        // Thêm status 'imported' nếu không có filter nào được chọn
        if (statusFilters.length === 0) {
          statusFilters.push('imported', 'pending', 'completed');
        }
        if (statusFilters.length) {
          params.append('status', statusFilters.join(','));
        }
        
        // Filter theo thời gian
        if(this.filters.fromDate)
        {
          const fromDate = this.formatDateToLocal(this.filters.fromDate);
          if (fromDate) params.append('from_date', fromDate);
        }
        if(this.filters.toDate)
        {
          const toDate = this.formatDateToLocal(this.filters.toDate);
          if (toDate) params.append('to_date', toDate);
        }
        
        this.toast.add({ severity: 'info', summary: 'Đang xuất file...', detail: 'Vui lòng chờ', life: 2000 });
        
        const url = `/admin/purchase-orders/export${params.toString() ? '?' + params.toString() : ''}`;
        const res = await axios.get(url, { 
          responseType: 'blob',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
          }
        });
        
        // Tạo và download file
        const blob = new Blob([res.data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = `phieu_dat_hang_${new Date().toISOString().split('T')[0]}.xlsx`;
        document.body.appendChild(a);
        a.click();
        URL.revokeObjectURL(a.href);
        a.remove();
        
        this.toast.add({ severity: 'success', summary: 'Thành công', detail: 'Đã tải Excel', life: 3000 });
      } catch (error) {
        this.toast.add({ severity: 'error', summary: 'Lỗi', detail: error.message || 'Xuất file thất bại', life: 5000 });
      } finally {
        this.isExporting = false;
      }
    },

    // Export single purchase order to Excel
    async exportSinglePurchaseOrder(purchaseOrder) {
      try {
        this.isExporting = true;
        
        this.toast.add({ 
          severity: 'info', 
          summary: 'Đang xuất file...', 
          detail: 'Vui lòng chờ', 
          life: 2000 
        });
        
        const url = `/admin/purchase-orders/${purchaseOrder.id}/export`;
        const res = await axios.get(url, { 
          responseType: 'blob',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
          }
        });
        
        // Tạo và download file
        const blob = new Blob([res.data], { 
          type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' 
        });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = `phieu_dat_hang_${purchaseOrder.order_code}_${new Date().toISOString().split('T')[0]}.xlsx`;
        document.body.appendChild(a);
        a.click();
        URL.revokeObjectURL(a.href);
        a.remove();
        
        this.toast.add({ 
          severity: 'success', 
          summary: 'Thành công', 
          detail: 'Đã tải Excel', 
          life: 3000 
        });
      } catch (error) {
        this.toast.add({ 
          severity: 'error', 
          summary: 'Lỗi', 
          detail: error.message || 'Xuất file thất bại', 
          life: 5000 
        });
      } finally {
        this.isExporting = false;
      }
    },
    
      async loadOrders() {
      try {
        // Format dates theo local timezone
        const fromDate = this.formatDateToLocal(this.filters.fromDate);
        const toDate = this.formatDateToLocal(this.filters.toDate);
        
        const response = await axios.get('/admin/purchase-orders/api', {
          params: {
            from_date: fromDate,
            to_date: toDate,
            search: this.searchQuery,
            per_page: this.pagination.per_page,
            page: this.pagination.current_page
          }
        });

        if (response.data.success) {
          // Cập nhật danh sách orders từ API
          this.orders = response.data.data;
          this.filteredOrders = response.data.data;
          
          // Cập nhật pagination
          this.pagination = response.data.pagination;
        }
      } catch (error) {
        this.toast.add({ 
          severity: 'error', 
          summary: 'Lỗi', 
          detail: 'Không thể tải dữ liệu nhập hàng', 
          life: 3000 
        });
      }
    }
  },

  watch: {
    // Watch khi user chọn ngày "Từ ngày"
    'filters.fromDate': {
      handler() {
        this.loadOrders();
      }
    },
    // Watch khi user chọn ngày "Đến ngày"
    'filters.toDate': {
      handler() {
        this.loadOrders();
      }
    }
  },

  mounted() {
    // Khởi tạo filteredOrders với dữ liệu gốc
    this.filteredOrders = [...this.orders]
  }
};
</script>

<style scoped>
.purchase-orders-page {
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

/* Main Content Layout */
.main-content {
  display: flex;
  gap: 20px;
  margin-top: 20px;
}

/* Left Sidebar */
.left-sidebar {
  width: 300px;
  background: #fff;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  border: 1px solid #e9ecef;
  height: fit-content;
}

.filter-section {
  margin-bottom: 25px;
}

.filter-section h5 {
  color: #2c3e50;
  margin-bottom: 15px;
  font-weight: 600;
  font-size: 16px;
}

.filter-options {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.checkbox-item, .radio-item {
  display: flex;
  align-items: center;
  gap: 8px;
}

.checkbox-item input[type="checkbox"],
.radio-item input[type="radio"] {
  margin: 0;
}

.checkbox-item label,
.radio-item label {
  margin: 0;
  font-size: 14px;
  color: #495057;
  cursor: pointer;
}

.radio-options {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

/* Right Content */
.right-content {
  flex: 1;
  flex-direction: column;
  background: #fff;
  border-radius: 12px;
  padding:15px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  border: 1px solid #e9ecef;
  display: flex;
  align-items: center;
  justify-content: center;
}

.no-results {
  text-align: center;
}

.no-results-icon {
  margin-bottom: 20px;
}

.no-results-icon i {
  font-size: 64px;
  color: #007bff;
  background: rgba(0, 123, 255, 0.1);
  padding: 20px;
  border-radius: 50%;
}

.no-results h3 {
  color: #2c3e50;
  margin-bottom: 10px;
  font-weight: 600;
}

.no-results p {
  color: #6c757d;
  margin-bottom: 10px;
}

.no-results a {
  color: #007bff;
  text-decoration: underline;
  cursor: pointer;
}

.no-results a:hover {
  color: #0056b3;
}

/* Responsive */
@media (max-width: 768px) {
  .main-content {
    flex-direction: column;
  }
  
  .left-sidebar {
    width: 100%;
  }
  
  .right-content {
    padding: 20px;
  }
}

/* Table Container */
.table-container {
  width: 100%;
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

/* Purchase Order Detail Container */
.purchase-order-detail-container {
  background: #f8f9fa;
  border-top: 1px solid #e9ecef;
  padding: 0;
}

.detail-tabs {
  display: flex;
  border-bottom: 1px solid #e9ecef;
  background: #ffffff;
}

.detail-tabs .tab {
  background: none;
  border: none;
  padding: 12px 20px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  color: #6c757d;
  border-bottom: 2px solid transparent;
  transition: all 0.2s ease;
}

.detail-tabs .tab:hover {
  color: #495057;
  background: #f8f9fa;
}

.detail-tabs .tab.active {
  color: #007bff;
  border-bottom-color: #007bff;
  background: #ffffff;
}

.detail-content {
  padding: 20px;
  background: #ffffff;
}

.tab-content {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.detail-content .table {
  margin-bottom: 0;
}

.detail-content .table td {
  padding: 8px 0;
  border: none;
  vertical-align: top;
}

.detail-content .fw-bold {
  color: #495057;
  font-weight: 600;
}

.detail-content .badge {
  font-size: 12px;
  padding: 4px 8px;
}

.detail-content .text-primary {
  color: #007bff !important;
  font-weight: 600;
}

.detail-content .text-primary i {
  font-size: 16px;
}

/* Action buttons in detail */
.detail-content .p-button {
  margin-right: 8px;
  margin-bottom: 8px;
}

.detail-content .p-button-sm {
  padding: 6px 12px;
  font-size: 12px;
}

/* Date Picker Container */
.date-picker-container {
  margin-top: 15px;
}
</style>
