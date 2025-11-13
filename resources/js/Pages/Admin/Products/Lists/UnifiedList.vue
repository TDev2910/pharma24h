<template>
  <div class="unified-list-page">
    <!-- Header Control Bar -->
    <div class="header-control-bar">
      <div class="controls-section"
        style="width:100%; display:flex; align-items:center; justify-content:center; gap:16px; flex-wrap:wrap;">
        <!-- Title Section -->
        <div class="title-section">
          <h4>{{ pageTitle }}</h4>
        </div>
        <!-- Search Section -->
        <div style="flex:1; display:flex; justify-content:center;">
          <div class="search-wrapper">
            <div class="input-group">
              <span class="input-group-text">
                <i class="pi pi-search"></i>
              </span>
              <input type="text" class="form-control" style="border-radius:8px;" :placeholder="searchPlaceholder"
                v-model="searchQuery" @input="debounceSearch">
            </div>
          </div>
        </div>
        <!-- Utility Options -->
        <div class="ultility-options">
          <!-- Dropdown Chọn loại -->
          <div class="dropdown" :class="{ 'show': showDropdown }">
            <Button icon="pi pi-list" :label="currentTypeLabel" @click="toggleDropdown" severity="secondary"
              style="background:#0b1020; border:none; color:white; font-weight:600; padding:6px 18px; border-radius:8px;" />

            <!-- Dropdown menu -->
            <div class="dropdown-menu" :class="{ 'show': showDropdown }" v-if="showDropdown">
              <div class="dropdown-item" @click="switchType('medicine')"
                :class="{ 'active': (selectedProductType || productType) === 'medicine' }">
                <i class="pi pi-pill"></i>
                Thuốc
              </div>
              <div class="dropdown-item" @click="switchType('goods')"
                :class="{ 'active': (selectedProductType || productType) === 'goods' }">
                <i class="pi pi-box"></i>
                Vật tư y tế
              </div>
              <div class="dropdown-item" @click="switchType('service')"
                :class="{ 'active': (selectedProductType || productType) === 'service' }">
                <i class="pi pi-cog"></i>
                Dịch vụ
              </div>
            </div>
          </div>

          <!-- Xuất file -->
          <Button icon="pi pi-upload" label="Xuất file" @click="exportData" severity="secondary"
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

    <!-- PrimeVue DataTable -->
    <div class="table-container">
      <!-- Message khi chưa chọn loại -->
      <div v-if="!selectedProductType && !productType" class="empty-state">
        <i class="pi pi-inbox" style="font-size: 48px; color: #9ca3af; margin-bottom: 16px;"></i>
        <p class="empty-message">Vui lòng chọn loại sản phẩm từ dropdown để xem danh sách</p>
      </div>

      <!-- DataTable khi đã chọn loại -->
      <DataTable v-else :value="paginatedData" dataKey="id" :paginator="false" :loading="loading" :pt="{
        table: { style: 'min-width: 50rem' }
      }">
        <!-- Columns cho Medicine -->
        <template v-if="(selectedProductType || productType) === 'medicine'">
          <!-- Mã thuốc -->
          <Column field="ma_hang" header="Mã thuốc" style="width: 18%">
            <template #body="{ data }">
              <strong>{{ data.ma_hang || 'N/A' }}</strong>
            </template>
          </Column>

          <!-- Tên thuốc -->
          <Column field="ten_thuoc" header="Tên thuốc" style="width: 25%">
          </Column>

          <!-- Hoạt chất chính -->
          <Column field="hoat_chat" header="Hoạt chất chính" style="width: 22%">
            <template #body="{ data }">
              <span class="badge-ingredient">{{ data.hoat_chat || 'N/A' }}</span>
            </template>
          </Column>

          <!-- Hàm lượng -->
          <Column field="ham_luong" header="Hàm lượng" style="width: 15%">
          </Column>

          <!-- Quy cách đóng gói -->
          <Column field="quy_cach_dong_goi" header="Quy cách đóng gói" style="width: 12%">
          </Column>

          <!-- ĐVT -->
          <Column field="don_vi_tinh" header="ĐVT" style="width: 8%">
          </Column>
        </template>

        <!-- Columns cho Goods -->
        <template v-if="(selectedProductType || productType) === 'goods'">
          <!-- Mã hàng -->
          <Column field="ma_hang" header="Mã hàng" style="width: 12%">
          </Column>

          <!-- Tên hàng hóa -->
          <Column field="ten_hang_hoa" header="Tên hàng hóa" style="width: 18%">
          </Column>

          <!-- Nhóm hàng -->
          <Column field="category_name" header="Nhóm hàng" style="width: 14%">
          </Column>

          <!-- Quy cách đóng gói -->
          <Column field="quy_cach_dong_goi" header="Quy cách đóng gói" style="width: 14%">
          </Column>

          <!-- ĐVT -->
          <Column field="don_vi_tinh" header="ĐVT" style="width: 8%">
          </Column>

          <!-- Giá vốn -->
          <Column field="gia_von" header="Giá vốn" style="width: 12%">
            <template #body="{ data, field }">
              {{ formatCurrency(data[field]) }}
            </template>
          </Column>

          <!-- Giá bán -->
          <Column field="gia_ban" header="Giá bán" style="width: 12%">
            <template #body="{ data, field }">
              {{ formatCurrency(data[field]) }}
            </template>
          </Column>

          <!-- Trạng thái -->
          <Column field="ban_truc_tiep" header="Trạng thái" style="width: 18%; min-width: 140px">
            <template #body="slotProps">
              <Tag :value="slotProps.data.ban_truc_tiep ? 'Bán trực tiếp' : 'Không bán'"
                :severity="getStatusSeverity(slotProps.data.ban_truc_tiep)"
                style="white-space: nowrap; font-size: 12px; padding: 6px 12px;" />
            </template>
          </Column>
        </template>

        <!-- Columns cho Service -->
        <template v-if="(selectedProductType || productType) === 'service'">
          <!-- Mã dịch vụ -->
          <Column field="ma_dich_vu" header="Mã dịch vụ" style="width: 18%">
            <template #body="{ data }">
              <strong>{{ data.ma_dich_vu || 'N/A' }}</strong>
            </template>
          </Column>

          <!-- Tên dịch vụ -->
          <Column field="ten_dich_vu" header="Tên dịch vụ" style="width: 25%">
          </Column>

          <!-- Bác sĩ đảm nhận -->
          <Column field="doctor.name" header="Bác sĩ đảm nhận" style="width: 12%">
            <template #body="{ data }">
              <strong>{{ data.doctor?.name || 'N/A' }}</strong>
            </template>
          </Column>

          <!-- Giá dịch vụ -->
          <Column field="gia_dich_vu" header="Giá dịch vụ" style="width: 8%">
            <template #body="{ data, field }">
              {{ formatCurrency(data[field]) }}
            </template>
          </Column>

          <!-- Hình thức -->
          <Column field="hinh_thuc" header="Hình thức" style="width: 8%">
            <template #body="{ data }">
              <strong>{{ data.hinh_thuc || 'N/A' }}</strong>
            </template>
          </Column>

          <!-- Trạng thái -->
          <Column field="trang_thai" header="Trạng thái" style="width: 8%">
          </Column>

          <!-- Ghi chú -->
          <Column field="ghi_chu" header="Ghi chú" style="width: 8%">
          </Column>
        </template>
      </DataTable>
    </div>

    <!-- Custom Paginator -->
    <Paginator :rows="rows" :totalRecords="totalRecords" :rowsPerPageOptions="rowsPerPageOptions" @page="onPageChange"
      style="margin-top: 20px;">
    </Paginator>

    <!-- Toast for notifications -->
    <Toast />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import Tag from 'primevue/tag'
import Paginator from 'primevue/paginator'
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'
import AdminLayout from '@/Layouts/AdminLayout.vue'

// Sử dụng Vue layout
defineOptions({ layout: AdminLayout })

// Props từ Inertia
const props = defineProps({
  productType: {
    type: String,
    required: false,
    default: null,
    validator: (value) => value === null || ['medicine', 'goods', 'service'].includes(value)
  },
  medicines: {
    type: Array,
    default: () => []
  },
  goods: {
    type: Array,
    default: () => []
  },
  services: {
    type: Array,
    default: () => []
  },
  categories: {
    type: Array,
    default: () => []
  },
  data: {
    type: Object,
    default: () => ({})
  }
})

// Reactive data
const searchQuery = ref('')
const loading = ref(false)
const toast = useToast()
const first = ref(0)
const rows = ref(10)
const showDropdown = ref(false)

// Dynamic data state (loaded from API)
const medicinesData = ref([])
const goodsData = ref([])
const servicesData = ref([])
const selectedProductType = ref(props.productType)

// Computed properties
const currentData = computed(() => {
  // Nếu có data từ props (khi vào từ route cụ thể), ưu tiên dùng props
  // Nếu không, dùng data đã load động
  const type = selectedProductType.value || props.productType

  switch (type) {
    case 'medicine':
      return props.medicines.length > 0 ? props.medicines : medicinesData.value
    case 'goods':
      return props.goods.length > 0 ? props.goods : goodsData.value
    case 'service':
      return props.services.length > 0 ? props.services : servicesData.value
    default:
      return []
  }
})

const filteredData = computed(() => {
  if (!searchQuery.value) return currentData.value

  const query = searchQuery.value.toLowerCase()
  const type = selectedProductType.value || props.productType

  switch (type) {
    case 'medicine':
      return currentData.value.filter(item =>
        item.ma_hang?.toLowerCase().includes(query) ||
        item.ten_thuoc?.toLowerCase().includes(query) ||
        item.hoat_chat?.toLowerCase().includes(query)
      )
    case 'goods':
      return currentData.value.filter(item =>
        item.ma_hang?.toLowerCase().includes(query) ||
        item.ten_hang_hoa?.toLowerCase().includes(query) ||
        item.category_name?.toLowerCase().includes(query)
      )
    case 'service':
      return currentData.value.filter(item =>
        item.ma_dich_vu?.toLowerCase().includes(query) ||
        item.ten_dich_vu?.toLowerCase().includes(query) ||
        item.nhom_hang_id?.toLowerCase().includes(query)
      )
    default:
      return currentData.value
  }
})

const paginatedData = computed(() => {
  const start = first.value
  const end = start + rows.value
  return filteredData.value.slice(start, end)
})

const totalRecords = computed(() => filteredData.value.length)

const rowsPerPageOptions = computed(() => {
  const type = selectedProductType.value || props.productType
  switch (type) {
    case 'service':
      return [5, 10, 20, 30]
    default:
      return [10, 20, 30]
  }
})

const pageTitle = computed(() => {
  const type = selectedProductType.value || props.productType
  switch (type) {
    case 'medicine':
      return 'Danh sách thuốc'
    case 'goods':
      return 'Danh sách hàng hóa'
    case 'service':
      return 'Danh sách dịch vụ'
    default:
      return 'Danh sách hàng hóa'
  }
})

const searchPlaceholder = computed(() => {
  const type = selectedProductType.value || props.productType
  switch (type) {
    case 'medicine':
      return 'Theo mã, tên hàng'
    case 'goods':
      return 'Theo mã, tên hàng'
    case 'service':
      return 'Theo mã dịch vụ, tên dịch vụ'
    default:
      return 'Tìm kiếm...'
  }
})

const currentTypeLabel = computed(() => {
  const type = selectedProductType.value || props.productType
  switch (type) {
    case 'medicine':
      return 'Thuốc'
    case 'goods':
      return 'Vật tư y tế'
    case 'service':
      return 'Dịch vụ'
    default:
      return 'Chọn loại'
  }
})

// Methods
const formatCurrency = (value) => {
  if (!value && value !== 0) return 'N/A'
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(value)
}

const getStatusSeverity = (value) => {
  return value ? 'success' : 'secondary'
}

const exportData = () => {
  toast.add({
    severity: 'info',
    summary: 'Xuất file',
    detail: 'Tính năng xuất file đang được phát triển',
    life: 3000
  })
}

const onPageChange = (event) => {
  first.value = event.first
  rows.value = event.rows
}

const debounceSearch = () => {
  // Reset pagination khi search
  first.value = 0
  // Debounce search functionality - có thể implement sau
}

const toggleDropdown = () => {
  showDropdown.value = !showDropdown.value
}

const switchType = async (type) => {
  showDropdown.value = false
  loading.value = true
  selectedProductType.value = type 

  // Reset pagination
  first.value = 0
  searchQuery.value = ''

  try {
    // Services không có API endpoint, điều hướng đến route
    if (type === 'service') {
      router.visit('/admin/services/list')
      return
    }

    const apiRoutes = {
      'medicine': '/admin/medicines/api',
      'goods': '/admin/goods/api'
    }

    if (apiRoutes[type]) {
      const response = await axios.get(apiRoutes[type], {
        params: {
          per_page: 1000 // Load tất cả để filter client-side
        }
      })

      if (response.data.success) {
        // Cập nhật data tương ứng
        if (type === 'medicine') {
          medicinesData.value = response.data.data || []
        } else if (type === 'goods') {
          goodsData.value = response.data.data || []
        }

        toast.add({
          severity: 'success',
          summary: 'Thành công',
          detail: `Đã tải danh sách ${currentTypeLabel.value}`,
          life: 2000
        })
      }
    }
  } catch (error) {
    console.error('Error loading data:', error)
    toast.add({
      severity: 'error',
      summary: 'Lỗi',
      detail: 'Không thể tải dữ liệu. Vui lòng thử lại.',
      life: 3000
    })
  } finally {
    loading.value = false
  }
}

// Đóng dropdown khi click bên ngoài
onMounted(() => {
  document.addEventListener('click', (event) => {
    if (!event.target.closest('.dropdown')) {
      showDropdown.value = false
    }
  })

  // Nếu có productType từ props, set selectedProductType
  if (props.productType) {
    selectedProductType.value = props.productType
  }

  console.log('Product Type:', props.productType)
  console.log('Selected Product Type:', selectedProductType.value)
  console.log('Current Data:', currentData.value)
})
</script>

<style scoped>
.unified-list-page {
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

/* Dropdown Styles */
.dropdown {
  position: relative;
  display: inline-block;
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  left: 0;
  background: white;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  z-index: 1000;
  min-width: 150px;
  margin-top: 4px;
  opacity: 0;
  transform: translateY(-10px);
  transition: all 0.2s ease;
  pointer-events: none;
}

.dropdown-menu.show {
  opacity: 1;
  transform: translateY(0);
  pointer-events: auto;
}

.dropdown-item {
  padding: 12px 16px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: #495057;
  transition: background-color 0.2s ease;
  border-bottom: 1px solid #f8f9fa;
}

.dropdown-item:last-child {
  border-bottom: none;
}

.dropdown-item:hover {
  background-color: #f8f9fa;
  color: #007bff;
}

.dropdown-item.active {
  background-color: #e3f2fd;
  color: #007bff;
  font-weight: 600;
}

.dropdown-item i {
  font-size: 14px;
  width: 16px;
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

/* Badge/Tag Styles for Active Ingredients */
.badge-ingredient {
  display: inline-block;
  padding: 4px 10px;
  background: #e0e7ff;
  color: #4338ca;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 500;
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

/* Empty State */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  text-align: center;
}

.empty-message {
  color: #6b7280;
  font-size: 16px;
  margin: 0;
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
}
</style>