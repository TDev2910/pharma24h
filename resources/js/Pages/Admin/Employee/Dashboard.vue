<template>
  <div class="employees-page">
    <!-- Header Control Bar -->
    <div class="header-control-bar">
      <div class="controls-section"
        style="width:100%; display:flex; align-items:center; justify-content:center; gap:16px; flex-wrap:wrap;">
        <!-- Title Section -->
        <div class="title-section">
          <h4>Quản lý nhân viên</h4>
        </div>
        
        <!-- Search Section -->
        <div style="flex:1; display:flex; justify-content:center;">
          <div class="search-wrapper">
            <div class="input-group">
              <span class="input-group-text">
                <i class="pi pi-search"></i>
              </span>
              <input type="text" class="form-control" style="border-radius:8px;" 
                placeholder="Theo mã, tên nhân viên, điện thoại"
                v-model="searchQuery" @input="debounceSearch">
            </div>
          </div>
        </div>
        
        <!-- Utility Options -->
        <div class="ultility-options">
          <!-- Thêm nhân viên -->
          <Button icon="pi pi-plus" label="Nhân viên" @click="showCreateModal" severity="secondary"
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

    <!-- DataTable -->
    <div class="table-container">
      <DataTable 
        :value="filteredEmployees" 
        v-model:expandedRows="expandedRows" 
        stripedRows
        responsiveLayout="scroll"
        tableStyle="min-width: 50rem" 
        :paginator="true" 
        :rows="pagination.per_page"
        :totalRecords="pagination.total"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[5, 10, 25]"
        currentPageReportTemplate="Hiển thị {first} đến {last} trong tổng số {totalRecords} nhân viên" 
        dataKey="id"
        loadingIcon="pi pi-spinner" 
        emptyMessage="Không có dữ liệu nhân viên">
        
        <Column expander style="width: 3rem" />
        <Column field="employee_code" header="Mã NV"></Column>
        <Column field="full_name" header="Họ tên"></Column>
        <Column field="phone_number" header="Điện thoại"></Column>
        <Column field="department.name" header="Phòng ban">
          <template #body="slotProps">
            <span>{{ slotProps.data.department?.name || '-' }}</span>
          </template>
        </Column>
        <Column field="job_title.name" header="Chức vụ">
          <template #body="slotProps">
            <span>{{ slotProps.data.job_title?.name || '-' }}</span>
          </template>
        </Column>
        <Column field="branch.name" header="Chi nhánh">
        <template #body="slotProps">
          <span>{{ slotProps.data.branch?.name || '-' }}</span>
        </template>
      </Column>
        <Column field="salary_level" header="Lương cơ bản">
          <template #body="slotProps">
            {{ formatCurrency(slotProps.data.salary_level) }}
          </template>
        </Column>

        <!-- Chi tiết khi mở rộng -->
        <template #expansion="slotProps">
          <div class="employee-detail-container">
            <!-- Tabs -->
            <div class="detail-tabs">
              <button class="tab" :class="{ active: activeTab === 'info' }" @click="switchTab('info')">
                Thông tin
              </button>
              <button class="tab" :class="{ active: activeTab === 'salary' }" @click="switchTab('salary')">
                Lương & Phụ cấp
              </button>
              <button class="tab" :class="{ active: activeTab === 'schedule' }" @click="switchTab('schedule')">
                Lịch làm việc
              </button>
            </div>

            <!-- Tab Content -->
            <div class="detail-content">
              <!-- Tab Thông tin -->
              <div v-if="activeTab === 'info'" class="tab-content">
                <div class="row">
                  <!-- Thông tin chung -->
                  <div class="col-md-6">
                    <h6 class="text-primary mb-3">
                      <i class="pi pi-user"></i> Thông tin chung
                    </h6>
                    <table class="table table-sm table-borderless">
                      <tbody>
                        <tr>
                          <td class="fw-bold" style="width: 140px;">Mã nhân viên:</td>
                          <td>{{ slotProps.data.employee_code }}</td>
                        </tr>
                        <tr>
                          <td class="fw-bold">Họ tên:</td>
                          <td>{{ slotProps.data.full_name }}</td>
                        </tr>
                        <tr>
                          <td class="fw-bold">Email:</td>
                          <td>{{ slotProps.data.user?.email || '-' }}</td>
                        </tr>
                        <tr>
                          <td class="fw-bold">Điện thoại:</td>
                          <td>{{ slotProps.data.phone_number || '-' }}</td>
                        </tr>
                        <tr>
                          <td class="fw-bold">Giới tính:</td>
                          <td>{{ slotProps.data.gender || '-' }}</td>
                        </tr>
                        <tr>
                          <td class="fw-bold">Ngày sinh:</td>
                          <td>{{ formatDate(slotProps.data.dob) }}</td>
                        </tr>
                        <tr>
                          <td class="fw-bold">CMND/CCCD:</td>
                          <td>{{ slotProps.data.id_card_number || '-' }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>

                  <!-- Thông tin công việc -->
                  <div class="col-md-6">
                    <h6 class="text-primary mb-3">
                      <i class="pi pi-briefcase"></i> Thông tin công việc
                    </h6>
                    <table class="table table-sm table-borderless">
                      <tbody>
                        <tr>
                          <td class="fw-bold" style="width: 140px;">Phòng ban:</td>
                          <td>
                            <span class="badge bg-info">{{ slotProps.data.department?.name || '-' }}</span>
                          </td>
                        </tr>
                        <tr>
                          <td class="fw-bold">Chức vụ:</td>
                          <td>
                            <span class="badge bg-success">{{ slotProps.data.job_title?.name || '-' }}</span>
                          </td>
                        </tr>
                        <tr>
                          <td class="fw-bold">Chi nhánh:</td>
                          <td>
                            <span class="badge bg-primary">{{ slotProps.data.branch?.name || '-' }}</span>
                          </td>
                        </tr>
                        <tr>
                          <td class="fw-bold">Ngày bắt đầu:</td>
                          <td>{{ formatDate(slotProps.data.start_date) }}</td>
                        </tr>
                        <tr>
                          <td class="fw-bold">Địa chỉ:</td>
                          <td>{{ slotProps.data.address || '-' }}</td>
                        </tr>
                      </tbody>
                    </table>

                    <!-- Action buttons -->
                    <div class="mt-3">
                      <Button label="Chỉnh sửa" icon="pi pi-pencil" class="p-button-success p-button-sm me-2"
                        @click="editEmployee(slotProps.data)" />
                      <Button label="Lập lịch" icon="pi pi-calendar" class="p-button-info p-button-sm me-2"
                        @click="scheduleEmployee(slotProps.data)" />
                      <Button label="Xóa" icon="pi pi-trash" class="p-button-danger p-button-sm"
                        @click="deleteEmployee(slotProps.data)" />
                    </div>
                  </div>
                </div>
              </div>

              <!-- Tab Lương & Phụ cấp -->
              <div v-if="activeTab === 'salary'" class="tab-content">
                <div class="row">
                  <!-- Thông tin lương -->
                  <div class="col-md-6">
                    <h6 class="text-primary mb-3">
                      <i class="pi pi-money-bill"></i> Thông tin lương
                    </h6>
                    <table class="table table-sm table-borderless">
                      <tbody>
                        <tr>
                          <td class="fw-bold" style="width: 140px;">Loại lương:</td>
                          <td>
                            <span class="badge bg-secondary">
                              {{ slotProps.data.salary_type === 'fixed' ? 'Lương cố định' : 'Theo giờ' }}
                            </span>
                          </td>
                        </tr>
                        <tr>
                          <td class="fw-bold">Mức lương:</td>
                          <td class="text-success fw-bold">{{ formatCurrency(slotProps.data.salary_level) }}</td>
                        </tr>
                      </tbody>
                    </table>

                    <!-- Phụ cấp -->
                    <h6 class="text-primary mb-2 mt-3">
                      <i class="pi pi-plus-circle"></i> Phụ cấp
                    </h6>
                    <div v-if="slotProps.data.allowances && slotProps.data.allowances.length > 0">
                      <div v-for="allowance in slotProps.data.allowances" :key="allowance.id" 
                        class="p-2 mb-2 border rounded bg-light">
                        <div class="d-flex justify-content-between">
                          <span class="fw-bold">{{ allowance.name }}</span>
                          <span class="text-success">{{ formatCurrency(allowance.amount) }}</span>
                        </div>
                        <small class="text-muted">{{ getAllowanceTypeText(allowance.type) }}</small>
                      </div>
                    </div>
                    <div v-else class="text-muted">
                      <i class="pi pi-info-circle"></i> Chưa có phụ cấp
                    </div>
                  </div>

                  <!-- Thưởng & Giảm trừ -->
                  <div class="col-md-6">
                    <!-- Thưởng chỉ tiêu -->
                    <h6 class="text-primary mb-2">
                      <i class="pi pi-star"></i> Thưởng chỉ tiêu
                    </h6>
                    <div v-if="slotProps.data.targets && slotProps.data.targets.length > 0">
                      <div v-for="target in slotProps.data.targets" :key="target.id" 
                        class="p-2 mb-2 border rounded bg-light">
                        <div class="fw-bold">{{ target.activity_type }}</div>
                        <small>Chỉ tiêu: {{ formatCurrency(target.target_amount) }}</small><br>
                        <small>Thưởng: {{ target.bonus_value }}{{ target.bonus_type === 'percent' ? '%' : ' ₫' }}</small>
                      </div>
                    </div>
                    <div v-else class="text-muted mb-3">
                      <i class="pi pi-info-circle"></i> Chưa có chỉ tiêu
                    </div>

                    <!-- Giảm trừ -->
                    <h6 class="text-primary mb-2 mt-3">
                      <i class="pi pi-minus-circle"></i> Giảm trừ
                    </h6>
                    <div v-if="slotProps.data.deductions && slotProps.data.deductions.length > 0">
                      <div v-for="deduction in slotProps.data.deductions" :key="deduction.id" 
                        class="p-2 mb-2 border rounded bg-light">
                        <div class="d-flex justify-content-between">
                          <span class="fw-bold">{{ deduction.reason }}</span>
                          <span class="text-danger">-{{ formatCurrency(deduction.amount) }}</span>
                        </div>
                        <small class="text-muted">{{ getDeductionFrequencyText(deduction.frequency) }}</small>
                      </div>
                    </div>
                    <div v-else class="text-muted">
                      <i class="pi pi-info-circle"></i> Chưa có giảm trừ
                    </div>
                  </div>
                </div>
              </div>

              <!-- Tab Lịch làm việc -->
              <div v-if="activeTab === 'schedule'" class="tab-content">
                <div class="text-center text-muted py-4">
                  <i class="pi pi-calendar" style="font-size: 2rem;"></i>
                  <p class="mt-2">Lịch làm việc sẽ hiển thị ở đây</p>
                  <Button label="Tạo lịch mới" icon="pi pi-plus" class="p-button-sm p-button-info"
                    @click="scheduleEmployee(slotProps.data)" />
                </div>
              </div>
            </div>
          </div>
        </template>
      </DataTable>
    </div>

    <!-- Create Employee Modal -->
    <CreateEmployeeModal 
      :visible="showModal" 
      @close="closeModal" 
      @created="handleEmployeeCreated" 
    />

    <!-- Edit Employee Modal -->
    <EditEmployeeModal 
      :visible="showEditModal" 
      :employeeId="selectedEmployeeId" 
      @close="showEditModal = false"
      @updated="handleEmployeeUpdated" 
    />

    <!-- Schedule Modal -->
    <ScheduleModal 
      :visible="showScheduleModal" 
      :employee="selectedEmployee"
      @close="showScheduleModal = false"
    />
  </div>
</template>

<script>
import Button from 'primevue/button'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import CreateEmployeeModal from './Modals/Create.vue'
import EditEmployeeModal from './Modals/Edit.vue'
import ScheduleModal from './Modals/Schedule.vue'
import axios from 'axios'

export default {
  name: 'EmployeeDashboard',
  components: {
    Button,
    DataTable,
    Column,
    CreateEmployeeModal,
    EditEmployeeModal,
    ScheduleModal
  },

  data() {
    return {
      showModal: false,
      showEditModal: false,
      showScheduleModal: false,
      selectedEmployeeId: null,
      selectedEmployee: null,
      searchQuery: '',
      loading: false,
      employees: [],
      expandedRows: {},
      activeTab: 'info',
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
    filteredEmployees() {
      if (!this.searchQuery || !this.searchQuery.trim()) {
        return this.employees
      }

      const term = this.searchQuery.toLowerCase().trim()
      return this.employees.filter(employee => {
        const name = (employee.full_name || '').toLowerCase()
        const code = (employee.employee_code || '').toLowerCase()
        const phone = (employee.phone_number || '').toLowerCase()
        const email = (employee.user?.email || '').toLowerCase()
        return name.includes(term) || code.includes(term) || phone.includes(term) || email.includes(term)
      })
    }
  },

  mounted() {
    this.loadEmployees()
  },

  methods: {
    // Load danh sách nhân viên
    async loadEmployees() {
      this.loading = true
      try {
        const response = await axios.get('/admin/employees/api', {
          params: {
            search: this.searchQuery,
            per_page: this.pagination.per_page,
            page: this.pagination.current_page
          }
        })

        // Xử lý response
        if (Array.isArray(response.data)) {
          this.employees = response.data
        } else if (response.data.data) {
          this.employees = response.data.data
          if (response.data.pagination) {
            this.pagination = response.data.pagination
          }
        }
      } catch (error) {
        console.error('Error loading employees:', error)
      } finally {
        this.loading = false
      }
    },

    // Search với debounce
    debounceSearch() {
      clearTimeout(this.searchTimeout)
      this.searchTimeout = setTimeout(() => {
        // Filter tự động qua computed property
      }, 200)
    },

    // Show create modal
    showCreateModal() {
      this.showModal = true
    },

    // Close modal
    closeModal() {
      this.showModal = false
    },

    // Handle employee created
    async handleEmployeeCreated() {
      this.closeModal()
      await this.loadEmployees()
    },

    // Edit employee
    editEmployee(employee) {
      this.selectedEmployeeId = employee.id
      this.$nextTick(() => {
        this.showEditModal = true
      })
    },

    // Handle employee updated
    async handleEmployeeUpdated() {
      this.showEditModal = false
      this.selectedEmployeeId = null
      await this.loadEmployees()
    },

    // Schedule employee
    scheduleEmployee(employee) {
      this.selectedEmployee = employee
      this.showScheduleModal = true
    },

    // Delete employee
    async deleteEmployee(employee) {
      if (confirm(`Bạn có chắc muốn xóa nhân viên ${employee.full_name}?`)) {
        try {
          await axios.delete(`/admin/employees/${employee.id}`)
          await this.loadEmployees()
        } catch (error) {
          console.error('Error deleting employee:', error)
          alert('Không thể xóa nhân viên')
        }
      }
    },

    // Switch tab
    switchTab(tab) {
      this.activeTab = tab
    },

    // Format currency
    formatCurrency(value) {
      if (!value) return '0 ₫'
      return new Intl.NumberFormat('vi-VN', { 
        style: 'currency', 
        currency: 'VND' 
      }).format(value)
    },

    // Format date
    formatDate(date) {
      if (!date) return '-'
      return new Date(date).toLocaleDateString('vi-VN')
    },

    // Get allowance type text
    getAllowanceTypeText(type) {
      const typeMap = {
        'fixed_daily': 'Cố định/Ngày',
        'fixed_monthly': 'Cố định/Tháng',
        'percent_salary': '% Lương'
      }
      return typeMap[type] || type
    },

    // Get deduction frequency text
    getDeductionFrequencyText(frequency) {
      const frequencyMap = {
        'one-time': 'Một lần',
        'monthly': 'Hàng tháng'
      }
      return frequencyMap[frequency] || frequency
    }
  }
}
</script>

<style scoped>
.employees-page {
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

/* Table Container */
.table-container {
  background: #fff;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  border: 1px solid #e9ecef;
  margin-top: 20px;
}

/* Action Buttons */
.action-buttons {
  display: flex;
  gap: 8px;
  justify-content: center;
  align-items: center;
}

.action-buttons .p-button {
  width: 32px;
  height: 32px;
}

/* Avatar styling */
.avatar-image {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #e9ecef;
}

.no-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #f8f9fa;
  border: 2px solid #e9ecef;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6c757d;
}

.no-avatar i {
  font-size: 18px;
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

/* Loading state styling */
:deep(.p-datatable .p-datatable-loading-overlay) {
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(2px);
}

:deep(.p-datatable .p-datatable-loading-icon) {
  color: #007bff;
  font-size: 24px;
}

/* Empty state styling */
:deep(.p-datatable .p-datatable-emptymessage) {
  padding: 40px 20px;
  color: #6c757d;
  font-size: 16px;
  text-align: center;
  background: #f8f9fa;
}

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

/* Doctor Detail Container */
.doctor-detail-container {
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
  from {
    opacity: 0;
    transform: translateY(10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
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

/* Responsive improvements */
@media (max-width: 768px) {
  .table-container {
    padding: 10px;
    margin-top: 10px;
  }

  :deep(.p-datatable .p-datatable-thead > tr > th),
  :deep(.p-datatable .p-datatable-tbody > tr > td) {
    padding: 12px 8px;
    font-size: 13px;
  }

  .avatar-image,
  .no-avatar {
    width: 32px;
    height: 32px;
  }

  .detail-content {
    padding: 15px;
  }

  .detail-tabs .tab {
    padding: 10px 15px;
    font-size: 13px;
  }

  .detail-content .row {
    margin: 0;
  }

  .detail-content .col-md-6 {
    margin-bottom: 20px;
  }
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
</style>