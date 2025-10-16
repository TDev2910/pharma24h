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
            :value="filteredDoctors" 
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
            currentPageReportTemplate="Hiển thị {first} đến {last} trong tổng số {totalRecords} bác sĩ"
            dataKey="id"
            loadingIcon="pi pi-spinner"
            emptyMessage="Không có dữ liệu bác sĩ">
            <Column expander style="width: 3rem" />
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
            
            <!-- Hiển thị chi tiết thông tin khi nhấn vào dropdown-->
            <template #expansion="slotProps">
                <div class="doctor-detail-container">
                  <!-- 2 danh mục thông tin và lịch làm việc-->
                    <div class="detail-tabs">  
                        <button class="tab active" @click="switchTab('info')">Thông tin</button>
                        <button class="tab" @click="switchTab('schedule')">Lịch làm việc</button>
                    </div>
                    
                    <!-- Danh mục thông tin và lịch làm việc-->
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
                                                <td class="fw-bold" style="width: 140px;">Mã bác sĩ:</td>
                                                <td>{{ slotProps.data.doctor_code }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Tên bác sĩ:</td>
                                                <td>{{ slotProps.data.name }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Giới tính:</td>
                                                <td>{{ slotProps.data.gender }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Điện thoại:</td>
                                                <td>{{ slotProps.data.phone }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Email:</td>
                                                <td>{{ slotProps.data.email }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Chuyên khoa:</td>
                                                <td>
                                                    <span class="badge bg-info">{{ getSpecialtyText(slotProps.data.specialty) }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Trình độ:</td>
                                                <td>
                                                    <span class="badge bg-success">{{ getQualificationText(slotProps.data.qualification) }}</span>
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
                                                <td class="fw-bold">Địa chỉ:</td>
                                                <td>{{ slotProps.data.address || '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Trạng thái:</td>
                                                <td>
                                                    <span class="badge bg-success">Đang hoạt động</span>
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
                                            @click="editDoctor(slotProps.data)" />                                       
                                        <Button 
                                            label="Xóa" 
                                            icon="pi pi-trash" 
                                            class="p-button-danger p-button-sm"
                                            @click="deleteDoctor(slotProps.data)" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tab Lịch làm việc -->
                        <div v-if="activeTab === 'schedule'" class="tab-content">
                            <div class="text-center text-muted py-4">
                                <i class="pi pi-calendar" style="font-size: 2rem;"></i>
                                <p class="mt-2">Chức năng lịch làm việc đang được phát triển</p>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </DataTable>
    </div>

    <!-- Create Doctor Modal -->
    <CreateDoctorModal 
      :visible="showModal"
      @close="closeModal"
      @created="handleDoctorCreated"
    />
    
    <!-- Edit Doctor Modal -->
    <EditDoctorModal 
      :visible="showEditModal" 
      :doctorId="selectedDoctorId" 
      @close="showEditModal = false" 
      @edited="handleDoctorEdited"
    />
  </div>
</template>

<script>
import ButtonGroup from 'primevue/buttongroup'
import Button from 'primevue/button'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import CreateDoctorModal from './Modals/Create.vue'
import EditDoctorModal from './Modals/Edit.vue' 
import axios from 'axios'

export default {
  name: 'DoctorDashboard',
  components: {
    ButtonGroup,
    Button,
    DataTable,
    Column,
    CreateDoctorModal,
    EditDoctorModal  
  },
  
  data() {
    return {
      showModal: false,
      showEditModal: false,  
      selectedDoctorId: null,  
      searchQuery: '',
      loading: false,
      selectedDoctors: [],
      doctors: [],
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
    filteredDoctors() {
      //Nếu không nhập từ khóa , thông tin bác sĩ sẽ hiển thị tất cả
      if(!this.searchQuery || !this.searchQuery.trim()) {
        return this.doctors; //Trả về tất cả bác sĩ có trong datatable
      }

      const term = this.searchQuery.toLowerCase().trim();
      return this.doctors.filter(doctor => {
        const name = (doctor.name || '').toLowerCase();
        const code = (doctor.doctor_code || '').toLowerCase();
        const phone = (doctor.phone || '').toLowerCase();
        const email = (doctor.email || '').toLowerCase();
        return name.includes(term) || code.includes(term) || phone.includes(term) || email.includes(term);
      });
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
        // Filter sẽ tự động áp dụng thông qua computed property
      }, 200)
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
      this.selectedDoctorId = doctor.id  
      
      // Đảm bảo selectedDoctorId được set trước khi mở modal
      this.$nextTick(() => {
        this.showEditModal = true
      })
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

    // Handle doctor edited event
    handleDoctorEdited(updatedDoctor) {
      // Cập nhật danh sách doctors
      const index = this.doctors.findIndex(d => d.id === updatedDoctor.id)
      if (index !== -1) {
        this.doctors.splice(index, 1, updatedDoctor)
      }
      
      this.showEditModal = false
      this.selectedDoctorId = null
    },

    // Delete selected doctors (one by one)
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
          let deletedCount = 0
          
          // Xóa từng doctor một
          for (const doctor of this.selectedDoctors) {
            try {
              const response = await axios.delete(`/admin/doctors/${doctor.id}`)
              if (response.data.success) {
                deletedCount++
              }
            } catch (error) {
              console.error(`Error deleting doctor ${doctor.id}:`, error)
            }
          }
          
          this.$toast.add({
            severity: 'success',
            summary: 'Thành công',
            detail: `Đã xóa ${deletedCount}/${this.selectedDoctors.length} bác sĩ`,
            life: 3000
          })
          
          this.selectedDoctors = []
          await this.loadDoctors()
          
        } catch (error) {
          console.error('Error deleting doctors:', error)
          this.$toast.add({
            severity: 'error',
            summary: 'Lỗi',
            detail: 'Có lỗi xảy ra khi xóa các bác sĩ',
            life: 3000
          })
        }
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
    },

    // Tab switching
    switchTab(tab) {
      this.activeTab = tab
    },

    // Get specialty text
    getSpecialtyText(specialty) {
      const specialtyMap = {
        'General': 'Tổng quát',
        'general': 'Tổng quát',
        'Cardiology': 'Tim mạch',
        'cardiology': 'Tim mạch',
        'Neurology': 'Thần kinh',
        'neurology': 'Thần kinh',
        'Pediatrics': 'Nhi khoa',
        'pediatrics': 'Nhi khoa',
        'Orthopedics': 'Chỉnh hình',
        'orthopedics': 'Chỉnh hình',
        'Dermatology': 'Da liễu',
        'dermatology': 'Da liễu',
        'Ophthalmology': 'Mắt',
        'ophthalmology': 'Mắt',
        'ENT': 'Tai mũi họng',
        'ent': 'Tai mũi họng',
        'Gynecology': 'Sản phụ khoa',
        'gynecology': 'Sản phụ khoa',
        'Urology': 'Tiết niệu',
        'urology': 'Tiết niệu'
      }
      return specialtyMap[specialty] || specialty || '-'
    },

    // Get qualification text
    getQualificationText(qualification) {
      const qualificationMap = {
        'Master': 'Thạc sĩ',
        'master': 'Thạc sĩ',
        'Doctor': 'Bác sĩ',
        'doctor': 'Bác sĩ',
        'Professor': 'Giáo sư',
        'professor': 'Giáo sư',
        'Specialist': 'Chuyên khoa',
        'specialist': 'Chuyên khoa',
        'Resident': 'Nội trú',
        'resident': 'Nội trú'
      }
      return qualificationMap[qualification] || qualification || '-'
    },

    // Format date
    formatDate(date) {
      if (!date) return '-'
      return new Date(date).toLocaleDateString('vi-VN')
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