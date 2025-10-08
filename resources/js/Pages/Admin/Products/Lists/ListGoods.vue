<template>
  <div class="goods-page">
    <!-- Header Control Bar -->
    <div class="header-control-bar">
        <div class="controls-section" style="width:100%; display:flex; align-items:center; justify-content:center; gap:16px; flex-wrap:wrap;">
            <!-- Title Section -->
            <div class="title-section">
                <h4>Danh sách hàng hóa</h4>
            </div>
            <!-- Search Section -->
            <div style="flex:1; display:flex; justify-content:center;">
                <div class="search-wrapper">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="pi pi-search"></i>
                        </span>
                        <input type="text" class="form-control" style="border-radius:8px;" placeholder="Theo mã, tên hàng" v-model="searchQuery">
                    </div>
                </div>
            </div>
            <!-- Utility Options -->
            <div class="ultility-options">
                <!-- Xuất file -->
                <button class="btn-export" @click="exportData">
                    <i class="pi pi-upload"></i>
                    Xuất file
                </button>
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
      <DataTable 
        :value="paginatedGoods" 
        dataKey="id" 
        :paginator="false"
        :loading="loading"
        :pt="{
          table: { style: 'min-width: 50rem' }
        }"
      >
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
            <Tag 
              :value="slotProps.data.ban_truc_tiep ? 'Bán trực tiếp' : 'Không bán'" 
              :severity="getStatusSeverity(slotProps.data.ban_truc_tiep)"
              style="white-space: nowrap; font-size: 12px; padding: 6px 12px;"
            />
          </template>
        </Column>
      </DataTable>
    </div>


    <!-- Custom Paginator -->
    <Paginator :rows="10" :totalRecords="120" :rowsPerPageOptions="[10, 20, 30]"style></Paginator>

    <!-- <div class="custom-paginator">
      <Paginator 
        :rows="10" 
        :totalRecords="filteredGoods.length" 
        :rowsPerPageOptions="[10, 20, 30]"
        @page="onPageChange"
        :first="first"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
      ></Paginator>
    </div> -->

    <!-- Toast for notifications -->
    <Toast />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import Tag from 'primevue/tag'
import Paginator from 'primevue/paginator'
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'
import AdminLayout from '@/Layouts/AdminLayout.vue'

// Sử dụng Vue layout thay vì Blade layout
defineOptions({ layout: AdminLayout })

// Props từ Inertia
const props = defineProps({
  goods: {
    type: Array,
    default: () => []
  },
  categories: {
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
const filteredGoods = computed(() => {
  if (!searchQuery.value) return props.goods
  
  const query = searchQuery.value.toLowerCase()
  return props.goods.filter(good => 
    good.ma_hang?.toLowerCase().includes(query) ||
    good.ten_hang_hoa?.toLowerCase().includes(query) ||
    good.category?.name?.toLowerCase().includes(query)
  )
})

const paginatedGoods = computed(() => {
  const start = first.value
  const end = start + rows.value
  return filteredGoods.value.slice(start, end)
})

// Methods
const formatCurrency = (value) => {
  if (!value) return '0 VND'
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

onMounted(() => {
  console.log('Goods data:', props.goods)
  console.log('Categories:', props.categories)
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
.goods-page {
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

.search-wrapper .search-input:focus {
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
  text-align: center;
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
  .goods-page {
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
</style>
