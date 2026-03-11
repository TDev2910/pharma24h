<template>
  <div class="doctors-page">
    <!-- Header Control Bar -->
    <div class="header-control-bar">
      <div class="controls-section"
        style="width:100%; display:flex; align-items:center; justify-content:center; gap:16px; flex-wrap:wrap;">
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
              <input type="text" class="form-control" style="border-radius:8px;" placeholder="Theo mã, tên bác sĩ"
                v-model="searchQuery" @input="debounceSearch">
            </div>
          </div>
        </div>
        <!-- Utility Options -->
        <div class="ultility-options">
          <!-- Thêm bác sĩ -->
          <Button icon="pi pi-plus" label="Bác sĩ" @click="showCreateModal" severity="secondary"
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
      <DataTable :value="doctors.data" v-model:expandedRows="expandedRows" stripedRows :lazy="true"
        :paginator="true" :rows="doctors.per_page" :totalRecords="doctors.total"
        :first="(doctors.current_page - 1) * doctors.per_page" @page="onPageChange"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[5, 10, 25]"
        currentPageReportTemplate="Hiển thị {first} đến {last} trong tổng số {totalRecords} bác sĩ" dataKey="id"
        loadingIcon="pi pi-spinner" emptyMessage="Không có dữ liệu bác sĩ">
        <Column expander style="width: 3rem" />
        <Column field="avatar" header="Ảnh">
          <template #body="slotProps">
            <img v-if="slotProps.data.avatar" :src="getAvatarUrl(slotProps.data.avatar)" alt="Avatar"
              class="avatar-image" @error="handleImageError" />
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
            <span v-if="slotProps.data.specialty === 'General' || slotProps.data.specialty === 'general'">Tổng
              quát</span>
            <span v-else-if="slotProps.data.specialty === 'Cardiology' || slotProps.data.specialty === 'cardiology'">Tim
              mạch</span>
            <span v-else-if="slotProps.data.specialty === 'Neurology' || slotProps.data.specialty === 'neurology'">Thần
              kinh</span>
            <span v-else-if="slotProps.data.specialty === 'Pediatrics' || slotProps.data.specialty === 'pediatrics'">Nhi
              khoa</span>
            <span
              v-else-if="slotProps.data.specialty === 'Orthopedics' || slotProps.data.specialty === 'orthopedics'">Chỉnh
              hình</span>
            <span
              v-else-if="slotProps.data.specialty === 'Dermatology' || slotProps.data.specialty === 'dermatology'">Da
              liễu</span>
            <span
              v-else-if="slotProps.data.specialty === 'Ophthalmology' || slotProps.data.specialty === 'ophthalmology'">Mắt</span>
            <span v-else-if="slotProps.data.specialty === 'ENT' || slotProps.data.specialty === 'ent'">Tai mũi
              họng</span>
            <span v-else-if="slotProps.data.specialty === 'Gynecology' || slotProps.data.specialty === 'gynecology'">Sản
              phụ khoa</span>
            <span v-else-if="slotProps.data.specialty === 'Urology' || slotProps.data.specialty === 'urology'">Tiết
              niệu</span>
            <span v-else>{{ slotProps.data.specialty || '-' }}</span>
          </template>
        </Column>
        <Column field="qualification" header="Trình độ">
          <template #body="slotProps">
            <span v-if="slotProps.data.qualification === 'Master' || slotProps.data.qualification === 'master'">Thạc
              sĩ</span>
            <span v-else-if="slotProps.data.qualification === 'Doctor' || slotProps.data.qualification === 'doctor'">Bác
              sĩ</span>
            <span
              v-else-if="slotProps.data.qualification === 'Professor' || slotProps.data.qualification === 'professor'">Giáo
              sư</span>
            <span
              v-else-if="slotProps.data.qualification === 'Specialist' || slotProps.data.qualification === 'specialist'">Chuyên
              khoa</span>
            <span
              v-else-if="slotProps.data.qualification === 'Resident' || slotProps.data.qualification === 'resident'">Nội
              trú</span>
            <span v-else>{{ slotProps.data.qualification || '-' }}</span>
          </template>
        </Column>

        <!-- Hiển thị chi tiết thông tin khi nhấn vào dropdown-->
        <template #expansion="slotProps">
          <div class="doctor-detail-container">
            <!-- 2 danh mục thông tin và lịch làm việc-->
            <div class="detail-tabs">
              <button class="tab active" @click="switchTab('info')">Thông tin</button>
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
                            <span class="badge bg-success">{{ getQualificationText(slotProps.data.qualification)
                              }}</span>
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
                      <Button label="Chỉnh sửa" icon="pi pi-pencil" class="p-button-success p-button-sm me-2"
                        @click="editDoctor(slotProps.data)" />
                      <Button label="Xóa" icon="pi pi-trash" class="p-button-danger p-button-sm"
                        @click="deleteDoctor(slotProps.data)" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </template>
      </DataTable>
    </div>

    <!-- Create Doctor Modal -->
    <CreateDoctorModal :visible="showModal" @close="showModal = false" @created="refreshPage" />

    <!-- Edit Doctor Modal -->
    <EditDoctorModal :visible="showEditModal" :doctorId="selectedDoctorId" @close="showEditModal = false"
      @edited="refreshPage" />
  </div>
</template>

<script>
import ButtonGroup from 'primevue/buttongroup'
import Button from 'primevue/button'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import CreateDoctorModal from './Modals/Create.vue'
import EditDoctorModal from './Modals/Edit.vue'
import '@Admin/doctors/doctors.css';
import { router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import debounce from 'lodash/debounce'

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

  props: {
    doctors: Object,
    filters: Object,
  },

  data() {
    return {
      showModal: false,
      showEditModal: false,
      selectedDoctorId: null,
      searchQuery: this.filters.search || '',
      isLoading: false,
      selectedDoctors: [],
      expandedRows: {},
      activeTab: 'info',
    }
  },

  computed: {},

  methods: {
    onPageChange(event) {
      this.isLoading = true;
      const page = event.page + 1;
      const rows = event.rows;

      router.get('/admin/doctors', {
        search: this.searchQuery,
        page: page,
        per_page: rows
      }, {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => this.isLoading = false
      });
    },

    debounceSearch: debounce(function () {
      this.isLoading = true;
      router.get('/admin/doctors', {
        search: this.searchQuery,
        page: 1
      }, {
        preserveState: true,
        replace: true,
        onFinish: () => this.isLoading = false
      });
    }, 300),

    refreshPage() {
      router.reload({ only: ['doctors'] });
    },

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
      this.$nextTick(() => {
        this.showEditModal = true
      })
    },

    // Delete doctor
    deleteDoctor(doctor) {
      Swal.fire({
        title: 'Xác nhận xóa',
        text: `Bạn có chắc chắn muốn xóa bác sĩ "${doctor.name}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy'
      }).then((result) => {
        if (result.isConfirmed) {
          router.delete(`/admin/doctors/${doctor.id}`, {
            onSuccess: () => {
              Swal.fire('Thành công!', 'Đã xóa bác sĩ.', 'success');
            },
            onError: () => {
              Swal.fire('Lỗi!', 'Không thể xóa bác sĩ.', 'error');
            }
          });
        }
      });
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

<style>
@import "@Admin/doctors/doctors.css";
</style>
