<template>
  <div class="goods-page">
    <!-- Header Control Bar -->
    <div class="header-control-bar">
      <div class="controls-section"
        style="width:100%; display:flex; align-items:center; justify-content:center; gap:16px; flex-wrap:wrap;">
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
              <input type="text" class="form-control" style="border-radius:8px;" placeholder="Theo mã, tên hàng"
                v-model="searchQuery" @input="debounceSearch">
            </div>
          </div>
        </div>
        <!-- Utility Options -->
        <div class="ultility-options">
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
      <DataTable :value="paginatedGoods" dataKey="id" :paginator="false" :loading="loading" :pt="{
        table: { style: 'min-width: 50rem' }
      }">
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
      </DataTable>
    </div>


    <!-- Custom Paginator -->
    <Paginator :rows="10" :totalRecords="120" :rowsPerPageOptions="[10, 20, 30]" style></Paginator>

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

const debounceSearch = () => {
  // Debounce search functionality - có thể implement sau
}

onMounted(() => {
  console.log('Goods data:', props.goods)
  console.log('Categories:', props.categories)
})
</script>

<style scoped>
.goods-page {
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
