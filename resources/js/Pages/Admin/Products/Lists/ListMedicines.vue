
<template>
  <div class="medicines-page">
    <!-- Header Control Bar -->
    <div class="header-control-bar">
        <div class="controls-section" style="width:100%; display:flex; align-items:center; justify-content:center; gap:16px; flex-wrap:wrap;">
            <!-- Title Section -->
            <div class="title-section">
                <h4>Danh sách thuốc</h4>
            </div>
            <!-- Search Section -->
            <div style="flex:1; display:flex; justify-content:center;">
                <div class="search-wrapper">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="pi pi-search"></i>
                        </span>
                        <input type="text" class="form-control" style="border-radius:8px;background-color: #f2f4f7;" placeholder="Theo mã, tên hàng" v-model="searchQuery">
                    </div>
                </div>
            </div>
            <!-- Utility Options -->
            <div class="ultility-options">
                <!-- Xuất file -->
                <div class="text-end pb-4">
                  <Button 
                    icon="pi pi-external-link"
                    label="Export"
                    @click="exportData"
                    severity="secondary"
                    style="margin-top: 20px;background:#0b1020; border:none; color:white; font-weight:600; padding:6px 18px; border-radius:8px;"
                  />
                </div>
            </div>
        </div>
    </div>

    <!-- PrimeVue DataTable -->
    <div class="table-container">
      <DataTable 
        :value="paginatedMedicines" 
        dataKey="id" 
        :paginator="false"
        :loading="loading"
        :pt="{
          table: { style: 'min-width: 50rem' }
        }"
      >
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
      </DataTable>
    </div>  

    <!-- Custom Paginator -->
    <Paginator :rows="10" :totalRecords="120" :rowsPerPageOptions="[10, 20, 30]"style="margin-top: 50px;"></Paginator>
    <!-- Toast for notifications -->
    <Toast />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import Paginator from 'primevue/paginator'
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'
import AdminLayout from '@/Layouts/AdminLayout.vue'

// Sử dụng Vue layout
defineOptions({ layout: AdminLayout })

// Props từ Inertia
const props = defineProps({
  medicines: {
    type: Array,
    default: () => []
  }
})

// Reactive data
const searchQuery = ref('')
const loading = ref(false)
const toast = useToast()
const first = ref(0)
const rows = ref(10)

// Computed properties
const filteredMedicines = computed(() => {
  if (!searchQuery.value) return props.medicines
  
  const query = searchQuery.value.toLowerCase()
  return props.medicines.filter(medicine => 
    medicine.ma_hang?.toLowerCase().includes(query) ||
    medicine.ten_thuoc?.toLowerCase().includes(query) ||
    medicine.hoat_chat?.toLowerCase().includes(query)
  )
})

const paginatedMedicines = computed(() => {
  const start = first.value
  const end = start + rows.value
  return filteredMedicines.value.slice(start, end)
})

// Methods
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

onMounted(() => {
  console.log('Medicines data:', props.medicines)
})
</script>

<style scoped>
/* Modern UI Variables */
:root {
  --primary-color: #4f46e5;
  --primary-hover: #4338ca;
  --secondary-color: #64748b;
  --success-color: #10b981;
  --border-color: #e2e8f0;
  --bg-light: #f8fafc;
  --text-primary: #1e293b;
  --text-secondary: #64748b;
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

/* Page Layout */
.medicines-page {
  padding: 0;
  background: var(--bg-light);
  min-height: 100vh;
  font-family: system-ui, -apple-system, BlinkMacSystemFont, 
               "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
}

/* Header Control Bar */
.header-control-bar {
  background: white;
  border-radius: 12px;
  padding: 20px 24px;
  margin-bottom: 24px;
  box-shadow: var(--shadow-sm);
  border: 1px solid var(--border-color);
}

.controls-section {
  display: flex;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
}

/* Title Section */
.title-section h4 {
  color: var(--text-primary);
  font-weight: 700;
  font-size: 20px;
  margin: 0;
  white-space: nowrap;
  font-family: system-ui, -apple-system, BlinkMacSystemFont, 
               "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
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
  z-index: 10;
  color: var(--text-secondary);
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
}

.search-wrapper .form-control {
  padding-left: 40px !important;
  padding-right: 16px !important;
  border: 1.5px solid var(--border-color) !important;
  border-radius: 8px !important;
  height: 42px !important;
  font-size: 14px !important;
  transition: all 0.2s ease !important;
  font-family: system-ui, -apple-system, BlinkMacSystemFont, 
               "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
}

.search-wrapper .form-control:focus {
  border-color: var(--primary-color) !important;
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1) !important;
  outline: none !important;
}

/* Utility Options */
.ultility-options {
  display: flex;
  gap: 8px;
  margin-left: auto;
}

.btn-export {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  border: 1.5px solid var(--border-color);
  border-radius: 8px;
  background: white;
  color: var(--text-primary);
  font-size: 14px;
  font-weight: 500;
  transition: all 0.2s ease;
  cursor: pointer;
}

.btn-export:hover {
  background: var(--bg-light);
  border-color: var(--primary-color);
  color: var(--primary-color);
}

.utility-icons {
  display: flex;
  gap: 4px;
}

.utility-icons .btn {
  width: 38px;
  height: 38px;
  border-radius: 8px;
  border: 1.5px solid var(--border-color);
  background: white;
  color: var(--text-secondary);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  padding: 0;
}

.utility-icons .btn:hover {
  background: var(--bg-light);
  border-color: var(--primary-color);
  color: var(--primary-color);
}

/* Table Container */
.table-container {
  background: white;
  border-radius: 12px;
  box-shadow: var(--shadow-sm);
  border: 1px solid var(--border-color);
  overflow: hidden;
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

/* PrimeVue DataTable Header Styling */
:deep(.p-datatable .p-datatable-header) {
  background: #1db46a;
  color: #fff;
  border: none;
  padding: 16px 20px;
}

:deep(.p-datatable .p-datatable-thead > tr > th) {
  background: #1db46a !important;
  color: #fff !important;
  font-weight: 600;
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  padding: 16px 20px;
  border: none;
  text-align: center;
  position: relative;
  white-space: nowrap;
  font-family: system-ui, -apple-system, BlinkMacSystemFont, 
               "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
}

:deep(.p-datatable .p-datatable-thead > tr > th:first-child) {
  border-top-left-radius: 12px;
}

:deep(.p-datatable .p-datatable-thead > tr > th:last-child) {
  border-top-right-radius: 12px;
}

:deep(.p-datatable .p-datatable-thead > tr > th:hover) {
  background: #1db46a;
}

/* Table body styling */
:deep(.p-datatable .p-datatable-tbody > tr) {
  transition: all 0.2s ease;
  border-bottom: 1px solid var(--border-color);
}

:deep(.p-datatable .p-datatable-tbody > tr:hover) {
  background: #fafbfc;
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
}

:deep(.p-datatable .p-datatable-tbody > tr:last-child) {
  border-bottom: none;
}

:deep(.p-datatable .p-datatable-tbody > tr > td) {
  padding: 16px 20px;
  color: var(--text-primary);
  font-size: 14px;
  vertical-align: middle;
  border: none;
  text-align: left;
  font-family: system-ui, -apple-system, BlinkMacSystemFont, 
               "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
}

/* Pagination Styling */
:deep(.p-paginator) {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 0 0 12px 12px;
  padding: 16px 20px;
}

:deep(.p-paginator .p-paginator-page) {
  background: white;
  border: 1px solid #d1d5db;
  color: #6b7280;
  border-radius: 6px;
  margin: 0 2px;
  transition: all 0.2s ease;
}

:deep(.p-paginator .p-paginator-page:hover) {
  background: var(--bg-light);
  border-color: var(--primary-color);
  color: var(--primary-color);
}

:deep(.p-paginator .p-paginator-page.p-highlight) {
  background: var(--primary-color);
  border-color: var(--primary-color);
  color: white;
}

:deep(.p-paginator .p-dropdown) {
  border: 1px solid #d1d5db;
  border-radius: 6px;
}

/* Responsive Design */
@media (max-width: 768px) {
  .medicines-page {
    padding: 16px;
  }

  .controls-section {
    flex-direction: column;
    align-items: stretch;
  }

  .search-wrapper {
    max-width: 100%;
  }

  .ultility-options {
    margin-left: 0;
    justify-content: space-between;
  }

  .table-container {
    overflow-x: auto;
  }
}

@media (max-width: 576px) {
  .header-control-bar {
    padding: 16px;
  }

  .title-section h4 {
    font-size: 18px;
  }
}

/* Scrollbar Styling */
.table-container::-webkit-scrollbar {
  height: 8px;
}

.table-container::-webkit-scrollbar-track {
  background: var(--bg-light);
  border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb {
  background: var(--border-color);
  border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb:hover {
  background: var(--text-secondary);
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
