<template>
  <div class="purchase-returns-page">
    <!-- Header Control Bar -->
    <div class="header-control-bar">
        <div class="controls-section" style="width:100%; display:flex; align-items:center; justify-content:center; gap:16px; flex-wrap:wrap;">
            <!-- Title Section -->
            <div class="title-section">
                <h3>Trả hàng</h3>
            </div>
            <!-- Search Section -->
            <div style="flex:1; display:flex; justify-content:center;">
                <div class="search-wrapper">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="pi pi-search"></i>
                        </span>
                        <input type="text" class="form-control" style="border-radius:8px;" placeholder="Theo mã, tên nhà cung cấp" v-model="searchQuery" @input="debounceSearch">
                    </div>
                </div>
            </div>
    <!-- Utility Options -->
    <div class="ultility-options">
      <!-- Thêm trả hàng -->
        <Button 
          icon="pi pi-plus"
          label="Trả hàng"
          @click="showCreateModal"
          severity="secondary"
          style="background:#0b1020; border:none; color:white; font-weight:600; padding:6px 18px; border-radius:8px;"
        />
        <Button 
          icon="pi pi-file-excel"
          label="Xuất file"
          @click="showCreateModal"
          severity="secondary"
          style="background:#0b1020; border:none; color:white; font-weight:600; padding:6px 18px; border-radius:8px;"
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
              <input type="checkbox" id="temp" v-model="filters.temp" />
              <label for="temp">Phiếu tạm</label>
            </div>
            <div class="checkbox-item">
              <input type="checkbox" id="returned" v-model="filters.returned" />
              <label for="returned">Đã trả hàng</label>
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
            <div class="radio-item">
              <input type="radio" id="thisMonth" value="thisMonth" v-model="filters.timeRange" />
              <label for="thisMonth">Tháng này</label>
            </div>
            <div class="radio-item">
              <input type="radio" id="custom" value="custom" v-model="filters.timeRange" />
              <label for="custom">Tùy chỉnh</label>
            </div>
          </div>
          <div v-if="filters.timeRange === 'thisMonth'" class="date-picker-container">
            <DatePicker 
              v-model="filters.thisMonthDate" 
              size="small" 
              placeholder="Tháng này" 
              showIcon 
              iconDisplay="input" 
            />
          </div>
          <div v-if="filters.timeRange === 'custom'" class="date-picker-container">
            <DatePicker 
              v-model="filters.customDate" 
              size="small" 
              placeholder="Chọn ngày" 
              showIcon 
              iconDisplay="input" 
            />
          </div>
        </div>

      </div>

      <!-- Right Main Content -->
      <div class="right-content">
        <!-- DataTable -->
        <div class="table-container">
            <DataTable 
                :value="purchaseReturns" 
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
                currentPageReportTemplate="Hiển thị {first} đến {last} trong tổng số {totalRecords} phiếu trả hàng"
                dataKey="id"
                loadingIcon="pi pi-spinner"
                emptyMessage="Không có dữ liệu phiếu trả hàng">
                <Column expander style="width: 3rem" />
                <Column field="return_code" header="Mã trả hàng nhập"></Column>
                <Column field="created_at" header="Thời gian">
                    <template #body="slotProps">
                        {{ formatDate(slotProps.data.created_at) }}
                    </template>
                </Column>
                <Column field="supplier_name" header="Nhà cung cấp"></Column>
                <Column field="total_amount" header="Tổng tiền hàng">
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.total_amount) }}
                    </template>
                </Column>
                <Column field="discount" header="Giảm giá">
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.discount) }}
                    </template>
                </Column>
                <Column field="supplier_pay" header="NCC cần trả">
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.supplier_pay) }}
                    </template>
                </Column>
                <Column field="supplier_paid" header="NCC đã trả">
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.supplier_paid) }}
                    </template>
                </Column>
                
                <!-- Hiển thị chi tiết thông tin khi nhấn vào dropdown-->
                <template #expansion="slotProps">
                    <div class="purchase-return-detail-container">
                      <!-- 2 danh mục thông tin và sản phẩm-->
                        <div class="detail-tabs">  
                            <button class="tab active" @click="switchTab('info')">Thông tin</button>
                            <button class="tab" @click="switchTab('products')">Sản phẩm</button>
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
                                                    <td class="fw-bold" style="width: 140px;">Mã trả hàng:</td>
                                                    <td>{{ slotProps.data.return_code }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Nhà cung cấp:</td>
                                                    <td>{{ slotProps.data.supplier_name }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Lý do trả hàng:</td>
                                                    <td>{{ slotProps.data.reason }}</td>
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
                                                        <span class="badge bg-warning">{{ formatCurrency(slotProps.data.discount) }}</span>
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
                                                    <td class="fw-bold">NCC cần trả:</td>
                                                    <td>
                                                        <span class="badge bg-danger">{{ formatCurrency(slotProps.data.supplier_pay) }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">NCC đã trả:</td>
                                                    <td>
                                                        <span class="badge bg-success">{{ formatCurrency(slotProps.data.supplier_paid) }}</span>
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
                                                label="Chỉnh sửa" 
                                                icon="pi pi-pencil" 
                                                class="p-button-success p-button-sm me-2"
                                                @click="editPurchaseReturn(slotProps.data)" />                                       
                                            <Button 
                                                label="Xóa" 
                                                icon="pi pi-trash" 
                                                class="p-button-danger p-button-sm"
                                                @click="deletePurchaseReturn(slotProps.data)" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tab Sản phẩm -->
                            <div v-if="activeTab === 'products'" class="tab-content">
                                <div class="text-center text-muted py-4">
                                    <i class="pi pi-box" style="font-size: 2rem;"></i>
                                    <p class="mt-2">Danh sách sản phẩm trả hàng</p>
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

export default {
  name: 'PurchaseReturnsDashboard',
  components: {
    Button,
    DataTable,
    Column,
    DatePicker
  },
  
  data() {
    return {
      searchQuery: '',
      showModal: false,
      expandedRows: {},
      activeTab: 'info',
      filters: {
        temp: true,
        returned: true,
        cancelled: false,
        timeRange: 'thisMonth',
        thisMonthDate: null,
        customDate: null
      },
      purchaseReturns: [
        {
          id: 1,
          return_code: 'TR20251010001',
          created_at: '2025-10-10',
          supplier_name: 'Công ty Dược phẩm ABC',
          total_amount: 15000000,
          discount: 500000,
          supplier_pay: 14500000,
          supplier_paid: 10000000,
          reason: 'Hàng hư hỏng',
          status: 'returned'
        },
        {
          id: 2,
          return_code: 'TR20251010002',
          created_at: '2025-10-09',
          supplier_name: 'Nhà cung cấp XYZ',
          total_amount: 8500000,
          discount: 200000,
          supplier_pay: 8300000,
          supplier_paid: 8300000,
          reason: 'Hàng không đúng mẫu mã',
          status: 'completed'
        },
        {
          id: 3,
          return_code: 'TR20251010003',
          created_at: '2025-10-08',
          supplier_name: 'Công ty Thuốc DEF',
          total_amount: 12000000,
          discount: 0,
          supplier_pay: 12000000,
          supplier_paid: 0,
          reason: 'Hàng hết hạn sử dụng',
          status: 'pending'
        },
        {
          id: 4,
          return_code: 'TR20251010004',
          created_at: '2025-10-07',
          supplier_name: 'Nhà cung cấp GHI',
          total_amount: 6500000,
          discount: 100000,
          supplier_pay: 6400000,
          supplier_paid: 3200000,
          reason: 'Lỗi đơn hàng',
          status: 'partial'
        },
        {
          id: 5,
          return_code: 'TR20251010005',
          created_at: '2025-10-06',
          supplier_name: 'Công ty Dược JKL',
          total_amount: 9500000,
          discount: 300000,
          supplier_pay: 9200000,
          supplier_paid: 9200000,
          reason: 'Hàng không đạt chất lượng',
          status: 'completed'
        }
      ],
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 5,
        from: 1,
        to: 5
      }
    }
  },

  methods: {
    // Search functionality với debounce
    debounceSearch() {
      // TODO: Implement search functionality
      console.log('Search query:', this.searchQuery)
    },

    // Modal methods
    showCreateModal() {
      this.showModal = true
      // TODO: Implement create modal
    },

    // Continue search
    continueSearch() {
      console.log('Continue searching...')
      // TODO: Implement continue search functionality
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
        'returned': 'Đã trả hàng',
        'completed': 'Hoàn thành',
        'partial': 'Trả một phần',
        'cancelled': 'Đã hủy'
      }
      return statusMap[status] || status || '-'
    },

    // Edit purchase return
    editPurchaseReturn(purchaseReturn) {
      console.log('Edit purchase return:', purchaseReturn)
      // TODO: Implement edit functionality
    },

    // Delete purchase return
    deletePurchaseReturn(purchaseReturn) {
      if (confirm(`Bạn có chắc muốn xóa phiếu trả hàng ${purchaseReturn.return_code}?`)) {
        console.log('Delete purchase return:', purchaseReturn)
        // TODO: Implement delete functionality
      }
    }
  }
};
</script>

<style scoped>
.purchase-returns-page {
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

/* Purchase Return Detail Container */
.purchase-return-detail-container {
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
