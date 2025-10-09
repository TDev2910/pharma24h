<template>
  <div class="doctors-page">
    <!-- Header Control Bar -->
    <div class="header-control-bar">
        <div class="controls-section" style="width:100%; display:flex; align-items:center; justify-content:center; gap:16px; flex-wrap:wrap;">
            <!-- Title Section -->
            <div class="title-section">
                <h4>Danh sách bác sĩ</h4>
            </div>
            <!-- Search Section -->
            <div style="flex:1; display:flex; justify-content:center;">
                <div class="search-wrapper">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="pi pi-search"></i>
                        </span>
                        <input type="text" class="form-control" style="border-radius:8px;" placeholder="Theo mã, tên bác sĩ" v-model="searchQuery" @input="debounceSearch">
                    </div>
                </div>
            </div>
    <!-- Utility Options -->
    <div class="ultility-options">
      <!-- Thêm bác sĩ -->
        <Button 
          icon="pi pi-plus"
          label="Bác sĩ"
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

    <!-- DataTable -->
    <div class="table-container">
        <DataTable 
            :value="paginatedDoctors" 
            stripedRows
            responsiveLayout="scroll"
            tableStyle="min-width: 50rem"
            :paginator="false"
            dataKey="id"
            loadingIcon="pi pi-spinner"
            emptyMessage="Không có dữ liệu bác sĩ">
            <Column field="avatar" header="Ảnh">
                <template #body="slotProps">
                    <img 
                        v-if="slotProps.data.avatar" 
                        :src="getAvatarUrl(slotProps.data.avatar)" 
                        alt="Avatar" 
                        class="avatar-image"
                        @error="handleImageError"
                    />
                    <div v-else class="no-avatar">
                        <i class="pi pi-user"></i>
                    </div>
                </template>
            </Column>
            <Column field="doctor_code" header="Mã bác sĩ"></Column>
            <Column field="name" header="Tên bác sĩ"></Column>
            <Column field="gender" header="Giới tính"></Column>
            <Column field="phone" header="Điện thoại"></Column>
            <Column field="email" header="Email"></Column>
            <Column field="specialty" header="Chuyên khoa">
                <template #body="slotProps">
                    <span v-if="slotProps.data.specialty === 'General' || slotProps.data.specialty === 'general'">Tổng quát</span>
                    <span v-else-if="slotProps.data.specialty === 'Cardiology' || slotProps.data.specialty === 'cardiology'">Tim mạch</span>
                    <span v-else-if="slotProps.data.specialty === 'Neurology' || slotProps.data.specialty === 'neurology'">Thần kinh</span>
                    <span v-else-if="slotProps.data.specialty === 'Pediatrics' || slotProps.data.specialty === 'pediatrics'">Nhi khoa</span>
                    <span v-else-if="slotProps.data.specialty === 'Orthopedics' || slotProps.data.specialty === 'orthopedics'">Chỉnh hình</span>
                    <span v-else-if="slotProps.data.specialty === 'Dermatology' || slotProps.data.specialty === 'dermatology'">Da liễu</span>
                    <span v-else-if="slotProps.data.specialty === 'Ophthalmology' || slotProps.data.specialty === 'ophthalmology'">Mắt</span>
                    <span v-else-if="slotProps.data.specialty === 'ENT' || slotProps.data.specialty === 'ent'">Tai mũi họng</span>
                    <span v-else-if="slotProps.data.specialty === 'Gynecology' || slotProps.data.specialty === 'gynecology'">Sản phụ khoa</span>
                    <span v-else-if="slotProps.data.specialty === 'Urology' || slotProps.data.specialty === 'urology'">Tiết niệu</span>
                    <span v-else>{{ slotProps.data.specialty || '-' }}</span>
                </template>
            </Column>
            <Column field="qualification" header="Trình độ">
                <template #body="slotProps">
                    <span v-if="slotProps.data.qualification === 'Master' || slotProps.data.qualification === 'master'">Thạc sĩ</span>
                    <span v-else-if="slotProps.data.qualification === 'Doctor' || slotProps.data.qualification === 'doctor'">Bác sĩ</span>
                    <span v-else-if="slotProps.data.qualification === 'Professor' || slotProps.data.qualification === 'professor'">Giáo sư</span>
                    <span v-else-if="slotProps.data.qualification === 'Specialist' || slotProps.data.qualification === 'specialist'">Chuyên khoa</span>
                    <span v-else-if="slotProps.data.qualification === 'Resident' || slotProps.data.qualification === 'resident'">Nội trú</span>
                    <span v-else>{{ slotProps.data.qualification || '-' }}</span>
                </template>
            </Column>
        </DataTable>
    </div>

    <!-- Custom Paginator -->
    <Paginator 
        :rows="pagination.per_page" 
        :totalRecords="pagination.total" 
        :rowsPerPageOptions="[5, 10, 20, 50]"
        @page="onPageChange"
        style="margin-top: 20px;">
    </Paginator>
    
    <!-- Create Doctor Modal -->
    <CreateDoctorModal 
      :visible="showModal"
      @close="closeModal"
      @created="handleDoctorCreated"
    />
  </div>
</template>

<script>
import ButtonGroup from 'primevue/buttongroup'
import Button from 'primevue/button'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Paginator from 'primevue/paginator'
import CreateDoctorModal from './Modals/Create.vue'
import axios from 'axios'

export default {
  name: 'DoctorIndex',
  components: {
    ButtonGroup,
    Button,
    DataTable,
    Column,
    Paginator,
    CreateDoctorModal
  },
  data() {
    return {
      showModal: false,
      searchQuery: '',
      loading: false,
      selectedDoctors: [],
      doctors: [],
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
    // Computed property để lấy dữ liệu đã phân trang
    paginatedDoctors() {
      const start = (this.pagination.current_page - 1) * this.pagination.per_page
      const end = start + this.pagination.per_page
      return this.doctors.slice(start, end)
    }
  },

  mounted() {
    this.loadDoctors()
  },
  

  methods: {
    // Load danh sách bác sĩ từ API
    async loadDoctors() {
      this.loading = true
      try {
        const response = await axios.get('/admin/doctors/api', {
          params: {
            search: this.searchQuery,
            per_page: this.pagination.per_page,
            page: this.pagination.current_page
          }
        })
        
        if (response.data.success) {
          this.doctors = response.data.data
          this.pagination = response.data.pagination
        } else {
          console.error('API returned success: false')
        }
      } catch (error) {
        console.error('Error loading doctors:', error)
        this.$toast.add({
          severity: 'error',
          summary: 'Lỗi',
          detail: error.response?.data?.message || 'Không thể tải danh sách bác sĩ',
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
        this.searchDoctors()
      }, 500) // Delay 500ms
    },

    // Search doctors
    async searchDoctors() {
      this.pagination.current_page = 1 // Reset về trang đầu
      await this.loadDoctors()
    },

    // Pagination handling
    async onPageChange(event) {
      this.pagination.current_page = event.page + 1
      this.pagination.per_page = event.rows
    },

    // Modal methods
    showCreateModal() {
      this.showModal = true
    },
    
    closeModal() {
      this.showModal = false
    },
    
    // Handle doctor created
    async handleDoctorCreated(doctor) {
      console.log('Doctor created:', doctor)
      this.closeModal()
      await this.loadDoctors() // Reload danh sách
    },
    
    // Edit doctor
    editDoctor(doctor) {
      console.log('Edit doctor:', doctor)
      // TODO: Implement edit functionality
      // Có thể mở modal edit hoặc navigate đến trang edit
    },
    
    // Delete doctor
    async deleteDoctor(doctor) {
      if (confirm(`Bạn có chắc muốn xóa bác sĩ ${doctor.name}?`)) {
        try {
          const response = await axios.delete(`/admin/doctors/${doctor.id}`)
          if (response.data.success) {
            this.$toast.add({
              severity: 'success',
              summary: 'Thành công',
              detail: 'Đã xóa bác sĩ thành công',
              life: 3000
            })
            await this.loadDoctors() // Reload danh sách
          }
        } catch (error) {
          console.error('Error deleting doctor:', error)
          this.$toast.add({
            severity: 'error',
            summary: 'Lỗi',
            detail: error.response?.data?.message || 'Không thể xóa bác sĩ',
            life: 3000
          })
        }
      }
    },

    // Bulk delete selected doctors
    async deleteSelectedDoctors() {
      if (this.selectedDoctors.length === 0) {
        this.$toast.add({
          severity: 'warn',
          summary: 'Cảnh báo',
          detail: 'Vui lòng chọn bác sĩ cần xóa',
          life: 3000
        })
        return
      }

      if (confirm(`Bạn có chắc muốn xóa ${this.selectedDoctors.length} bác sĩ đã chọn?`)) {
        try {
          const ids = this.selectedDoctors.map(doctor => doctor.id)
          const response = await axios.delete('/admin/doctors/bulk-delete', {
            data: { ids }
          })
          
          if (response.data.success) {
            this.$toast.add({
              severity: 'success',
              summary: 'Thành công',
              detail: `Đã xóa ${response.data.deleted_count} bác sĩ`,
              life: 3000
            })
            this.selectedDoctors = []
            await this.loadDoctors()
          }
        } catch (error) {
          console.error('Error bulk deleting doctors:', error)
          this.$toast.add({
            severity: 'error',
            summary: 'Lỗi',
            detail: 'Không thể xóa các bác sĩ đã chọn',
            life: 3000
          })
        }
      }
    },

    // Export doctors
    async exportDoctors() {
      try {
        const response = await axios.get('/admin/doctors/export', {
          params: {
            search: this.searchQuery
          },
          responseType: 'blob'
        })
        
        // Tạo download link
        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', `doctors_${new Date().toISOString().split('T')[0]}.xlsx`)
        document.body.appendChild(link)
        link.click()
        link.remove()
        window.URL.revokeObjectURL(url)
        
        this.$toast.add({
          severity: 'success',
          summary: 'Thành công',
          detail: 'Đã xuất file danh sách bác sĩ',
          life: 3000
        })
      } catch (error) {
        console.error('Error exporting doctors:', error)
        this.$toast.add({
          severity: 'error',
          summary: 'Lỗi',
          detail: 'Không thể xuất file',
          life: 3000
        })
      }
    },

    // Get avatar URL
    getAvatarUrl(avatarPath) {
      if (!avatarPath) return null
      
      // Nếu đã có full URL thì trả về luôn
      if (avatarPath.startsWith('http')) {
        return avatarPath
      }
      
      // Nếu là relative path thì thêm base URL
      return `/storage/${avatarPath}`
    },

    // Handle image error
    handleImageError(event) {
      console.log('Image load error:', event.target.src)
      // Có thể thêm fallback image hoặc ẩn ảnh
      event.target.style.display = 'none'
    }
  }
}
</script>
  
<style scoped>
.doctors-page {
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
  border: 1px solid #e9ecef;
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
  border: none;
  border-bottom: 1px solid #f1f3f4;
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
}

/* Custom Paginator */
.custom-paginator {
  margin-top: 16px;
  display: flex;  
  justify-content: center;
}

:deep(.p-paginator) {
  background: transparent;
  border: 0;
  padding: 0;
  gap: 14px;                /* khoảng cách các nút */
  align-items: center;
}

/* Nút số trang */
:deep(.p-paginator .p-paginator-page) {
  width: 36px;
  height: 36px;
  border-radius: 999px;     /* tròn */
  border: 0;
  background: transparent;
  color: #6b7280;           /* xám nhạt */
  font-weight: 600;
  transition: all .15s ease;
}

:deep(.p-paginator .p-paginator-page:hover) {
  background: #f2f4f7;
  color: #111827;
}

/* Trang đang chọn = chấm tròn đen */
:deep(.p-paginator .p-paginator-page.p-highlight) {
  background: #0b1020;      /* đen/xanh đậm */
  color: #fff;
}

/* Mũi tên điều hướng « ‹ › » */
:deep(.p-paginator .p-paginator-first),
:deep(.p-paginator .p-paginator-prev),
:deep(.p-paginator .p-paginator-next),
:deep(.p-paginator .p-paginator-last) {
  width: 36px;
  height: 36px;
  border-radius: 999px;
  border: 0;
  color: #9aa3b2;
  background: transparent;
  transition: all .15s ease;
}

:deep(.p-paginator .p-paginator-first:hover),
:deep(.p-paginator .p-paginator-prev:hover),
:deep(.p-paginator .p-paginator-next:hover),
:deep(.p-paginator .p-paginator-last:hover) {
  background: #f2f4f7;
  color: #111827;
}

/* Dropdown chọn số dòng bên phải */
:deep(.p-paginator .p-dropdown) {
  margin-left: 12px;
  height: 36px;
  border: 1px solid #d5dbe6;
  border-radius: 10px;
  background: transparent;
}

:deep(.p-paginator .p-dropdown .p-inputtext) {
  padding: 0 10px;
  height: 34px;
  line-height: 34px;
  text-align: center;       /* số 10 căn giữa */
  color: #6b7280;
  font-weight: 600;
}

:deep(.p-paginator .p-dropdown .p-dropdown-trigger) {
  color: #9aa3b2;
}

/* Ẩn khung tiêu đề nếu bạn dùng CurrentPageReport */
:deep(.p-paginator .p-paginator-current) {
  display: none;
}
</style>