
<template>
    <div class="services-page">
      <!-- Header Control Bar -->
      <div class="header-control-bar">
          <div class="controls-section" style="width:100%; display:flex; align-items:center; justify-content:center; gap:16px; flex-wrap:wrap;">
              <!-- Title Section -->
              <div class="title-section">
                  <h4>Danh sách dịch vụ</h4>
              </div>
              <!-- Search Section -->
              <div style="flex:1; display:flex; justify-content:center;">
                  <div class="search-wrapper">
                      <div class="input-group">
                          <span class="input-group-text">
                              <i class="pi pi-search"></i>
                          </span>
                          <input type="text" class="form-control" style="border-radius:8px;" placeholder="Theo mã dịch vụ, tên dịch vụ" v-model="searchQuery" @input="debounceSearch">
                      </div>
                  </div>
              </div>
              <!-- Utility Options -->
              <div class="ultility-options">
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
          :value="paginatedServices" 
          dataKey="id" 
          :paginator="false"
          :loading="loading"
          :pt="{
            table: { style: 'min-width: 50rem' }
          }"
        >
          <!-- Mã dịch vụ -->
          <Column field="ma_dich_vu" header="Mã dịch vụ" style="width: 18%">
            <template #body="{ data }">
              <strong>{{ data.ma_dich_vu || 'N/A' }}</strong>
            </template>
          </Column>
  
          <!-- Tên dịch vụ -->
          <Column field="ten_dich_vu" header="Tên dịch vụ" style="width: 25%">
          </Column>
  
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
          
          
          <Column field="hinh_thuc" header="Hình thức" style="width: 8%">
            <template #body="{ data }">
              <strong>{{ data.hinh_thuc || 'N/A' }}</strong>
            </template>
          </Column> 
          
          <!-- Trạng thái -->
          <Column field="trang_thai" header="Trạng thái" style="width: 8%">
          </Column>

          <!-- Ghi chú -->
          <Column field="ghi_chu" header="Thao tác" style="width: 8%">
          </Column>
        </DataTable>
      </div>  
  
      <!-- Custom Paginator -->
      <Paginator :rows="10" :totalRecords="120" :rowsPerPageOptions="[5,10, 20, 30]"style="margin-top: 50px;"></Paginator>
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
    services: {
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
  const filteredServices = computed(() => {
    if (!searchQuery.value) return props.services
    
    const query = searchQuery.value.toLowerCase()
    return props.services.filter(service => 
      service.ma_dich_vu?.toLowerCase().includes(query) ||
      service.ten_dich_vu?.toLowerCase().includes(query) ||
      service.nhom_hang_id?.toLowerCase().includes(query)
    )
  })
  
  const paginatedServices = computed(() => {
    const start = first.value
    const end = start + rows.value
    return filteredServices.value.slice(start, end)
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

  const formatCurrency = (value) => {
    if (!value) return 'N/A'
    return new Intl.NumberFormat('vi-VN', {
      style: 'currency',
      currency: 'VND'
    }).format(value)
  }
  
  const onPageChange = (event) => {
    first.value = event.first
    rows.value = event.rows
  }
  
  const debounceSearch = () => {
    // Debounce search functionality - có thể implement sau
  }
  
  onMounted(() => {
    
    console.log('Services data:', props.services)
  })
  </script>
  
  <style scoped>
  .medicines-page {
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
  