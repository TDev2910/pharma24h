<template>
    <div class="stock-products-page">
      <!-- Header Control Bar -->
      <div class="header-control-bar">
        <div class="controls-section"
          style="width:100%; display:flex; align-items:center; justify-content:center; gap:16px; flex-wrap:wrap;">
          <!-- Title Section -->
          <div class="title-section">
            <h4>Kiểm tra tồn kho sản phẩm</h4>
          </div>
          <!-- Search Section -->
          <div style="flex:1; display:flex; justify-content:center;">
            <div class="search-wrapper">
              <div class="input-group">
                <span class="input-group-text">
                  <i class="pi pi-search"></i>
                </span>
                <input type="text" class="form-control" style="border-radius:8px;" 
                  placeholder="Theo mã, tên hàng"
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
        <DataTable :value="paginatedProducts" dataKey="id" :paginator="false" :loading="loading" :pt="{
          table: { style: 'min-width: 50rem' }
        }">
          <!-- Mã hàng -->
          <Column field="ma_hang" header="Mã hàng" style="width: 12%">
            <template #body="{ data }">
              <strong>{{ data.ma_hang || 'N/A' }}</strong>
            </template>
          </Column>
  
          <!-- Tên sản phẩm -->
          <Column field="ten_san_pham" header="Tên sản phẩm" style="width: 25%">
          </Column>
  
          <!-- Loại sản phẩm -->
          <Column field="type" header="Loại" style="width: 10%">
            <template #body="{ data }">
              <span class="badge" :class="getProductTypeBadgeClass(data.type)">
                {{ getProductTypeText(data.type) }}
              </span>
            </template>
          </Column>
  
          <!-- Tồn kho -->
          <Column field="ton_kho" header="Tồn kho" style="width: 10%">
            <template #body="{ data }">
              {{ formatNumber(data.ton_kho || 0) }} {{ data.don_vi_tinh || '' }}
            </template>
          </Column>
  
          <!-- Tồn thấp nhất -->
          <Column field="ton_thap_nhat" header="Tồn thấp nhất" style="width: 10%">
            <template #body="{ data }">
              {{ formatNumber(data.ton_thap_nhat || 0) }} {{ data.don_vi_tinh || '' }}
            </template>
          </Column>
  
          <!-- Tồn cao nhất -->
          <Column field="ton_cao_nhat" header="Tồn cao nhất" style="width: 10%">
            <template #body="{ data }">
              {{ formatNumber(data.ton_cao_nhat || 0) }} {{ data.don_vi_tinh || '' }}
            </template>
          </Column>
  
          <!-- Trạng thái tồn kho -->
          <Column field="inventory_status" header="Trạng thái tồn kho" style="width: 15%">
            <template #body="{ data }">
              <span :class="['badge', getInventoryStatus(data).class]">
                {{ getInventoryStatus(data).label }}
              </span>
            </template>
          </Column>
        </DataTable>
      </div>
  
      <!-- Custom Paginator -->
      <Paginator :rows="10" :totalRecords="totalRecords" :rowsPerPageOptions="[10, 20, 30]" 
        @page="onPageChange" style="margin-top: 50px;"></Paginator>
      
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
  import StaffLayout from '@/Layouts/StaffLayout.vue'
  import axios from 'axios'
  
  // Sử dụng Vue layout
  defineOptions({ layout: StaffLayout })
  
  // Props từ Inertia
  const props = defineProps({
    products: {
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
  const products = ref([])
  
  // Computed properties
  const filteredProducts = computed(() => {
    if (!searchQuery.value) return products.value
  
    const query = searchQuery.value.toLowerCase()
    return products.value.filter(product =>
      product.ma_hang?.toLowerCase().includes(query) ||
      product.ten_san_pham?.toLowerCase().includes(query) ||
      product.category?.toLowerCase().includes(query)
    )
  })
  
  const paginatedProducts = computed(() => {
    const start = first.value
    const end = start + rows.value
    return filteredProducts.value.slice(start, end)
  })
  
  const totalRecords = computed(() => filteredProducts.value.length)
  
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
  
  const debounceSearch = () => {
    first.value = 0 // Reset về trang đầu khi search
  }
  
  // Xác định trạng thái tồn kho (giống Dashboard.vue)
  const getInventoryStatus = (row) => {
    const qty = Number(row?.ton_kho ?? 0)
    const min = Number(row?.ton_thap_nhat ?? 0)
    const max = Number(row?.ton_cao_nhat ?? 0)
  
    if (qty === 0) {
      return { label: 'Hết hàng', class: 'bg-secondary' }
    }
  
    // Nếu đã cấu hình ngưỡng
    if (min > 0 && max > 0 && max >= min) {
      if (qty <= min) {
        return { label: 'Sắp hết hàng', class: 'bg-warning text-dark' }
      }
      if (qty > max) {
        return { label: 'Tồn vượt mức', class: 'bg-danger' }
      }
      return { label: 'Còn hàng', class: 'bg-success' }
    }
  
    // Fallback khi chưa có ngưỡng: >0 coi là còn hàng
    return { label: 'Còn hàng', class: 'bg-success' }
  }
  
  // Loại sản phẩm
  const getProductTypeText = (type) => {
    return type === 'medicine' ? 'Thuốc' : 'Hàng hóa'
  }
  
  const getProductTypeBadgeClass = (type) => {
    return type === 'medicine' ? 'bg-info' : 'bg-primary'
  }
  
  // Format currency
  const formatCurrency = (amount) => {
    if (!amount) return '0 ₫'
    return new Intl.NumberFormat('vi-VN', {
      style: 'currency',
      currency: 'VND'
    }).format(amount)
  }
  
  // Format number
  const formatNumber = (num) => {
    return new Intl.NumberFormat('vi-VN').format(num)
  }
  
  // Load data
  const loadProducts = async () => {
    loading.value = true
    try {
      const response = await axios.get('/staff/products/stock/api')
      if (response.data.success) {
        products.value = response.data.data || []
      }
    } catch (error) {
      console.error('Error loading products:', error)
      toast.add({
        severity: 'error',
        summary: 'Lỗi',
        detail: 'Không thể tải dữ liệu tồn kho',
        life: 3000
      })
    } finally {
      loading.value = false
    }
  }
  
  onMounted(() => {
    if (props.products && props.products.length > 0) {
      products.value = props.products
    } else {
      loadProducts()
    }
  })
  </script>
  
  <style scoped>
  .stock-products-page {
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
    width: 100%;
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
  
  /* Badge Styling */
  .badge {
    font-size: 11px;
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: 500;
    display: inline-block;
  }
  
  .bg-secondary {
    background-color: #6c757d !important;
    color: white;
  }
  
  .bg-warning {
    background-color: #ffc107 !important;
  }
  
  .text-dark {
    color: #212529 !important;
  }
  
  .bg-danger {
    background-color: #dc3545 !important;
    color: white;
  }
  
  .bg-success {
    background-color: #28a745 !important;
    color: white;
  }
  
  .bg-info {
    background-color: #17a2b8 !important;
    color: white;
  }
  
  .bg-primary {
    background-color: #007bff !important;
    color: white;
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
  
  :deep(.p-datatable .p-datatable-tbody > tr > td:last-child) {
    border-right: none;
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