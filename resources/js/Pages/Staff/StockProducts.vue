<template>
  <div class="stock-products-page">
    <div class="header-control-bar">
      <div class="controls-section">
        <div class="title-section">
          <h4>Kiểm tra tồn kho sản phẩm</h4>
        </div>
        <div class="search-section-wrapper" style="flex:1; display:flex; justify-content:center; width: 100%;">
          <div class="search-wrapper">
            <div class="input-group">
              <span class="input-group-text">
                <i class="pi pi-search"></i>
              </span>
              <input type="text" class="form-control" placeholder="Tìm theo mã, tên hàng..." v-model="searchQuery"
                @input="debounceSearch">
            </div>
          </div>
        </div>
        <div class="ultility-options">
          <Button icon="pi pi-upload" label="Xuất file" @click="exportData" severity="secondary"
            style="background:#0b1020; border:none; color:white; font-weight:600; padding:6px 18px; border-radius:8px; font-size: 13px;" />
          <Button icon="pi pi-filter" label="Lọc" @click="filterData" severity="secondary"
            style="background:#0b1020; border:none; color:white; font-weight:600; padding:6px 18px; border-radius:8px; font-size: 13px;" />
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

    <div class="table-container">
      <DataTable :value="paginatedProducts" dataKey="id" :paginator="false" :loading="loading"
        class="responsive-datatable" :pt="{ table: { style: 'min-width: auto' } }">

        <Column field="ma_hang" header="Mã hàng">
          <template #body="{ data }">
            <span class="mobile-label">Mã hàng:</span>
            <strong>{{ data.ma_hang || 'N/A' }}</strong>
          </template>
        </Column>

        <Column field="ten_san_pham" header="Tên sản phẩm" class="col-ten-sp">
          <template #body="{ data }">
            <span class="product-name-text">{{ data.ten_san_pham }}</span>
          </template>
        </Column>

        <Column field="type" header="Loại">
          <template #body="{ data }">
            <span class="mobile-label">Loại:</span>
            <span class="badge" :class="getProductTypeBadgeClass(data.type)">
              {{ getProductTypeText(data.type) }}
            </span>
          </template>
        </Column>

        <Column field="ton_kho" header="Tồn kho">
          <template #body="{ data }">
            <span class="mobile-label">Tồn kho:</span>
            <span class="fw-bold text-primary">
              {{ formatNumber(data.ton_kho || 0) }} {{ data.don_vi_tinh || '' }}
            </span>
          </template>
        </Column>

        <Column field="ton_thap_nhat" header="Tồn Min">
          <template #body="{ data }">
            <span class="mobile-label">Tồn Min:</span>
            {{ formatNumber(data.ton_thap_nhat || 0) }}
          </template>
        </Column>

        <Column field="ton_cao_nhat" header="Tồn Max">
          <template #body="{ data }">
            <span class="mobile-label">Tồn Max:</span>
            {{ formatNumber(data.ton_cao_nhat || 0) }}
          </template>
        </Column>

        <Column field="inventory_status" header="Trạng thái">
          <template #body="{ data }">
            <span class="mobile-label">Trạng thái:</span>
            <span :class="['badge', getInventoryStatus(data).class]">
              {{ getInventoryStatus(data).label }}
            </span>
          </template>
        </Column>
      </DataTable>
    </div>

    <Paginator :rows="10" :totalRecords="totalRecords" :rowsPerPageOptions="[10, 20, 30]" @page="onPageChange"
      style="margin-top: 30px;"></Paginator>

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

const filterData = () => {
  toast.add({
    severity: 'info',
    summary: 'Lọc dữ liệu',
    detail: 'Tính năng lọc dữ liệu đang được phát triển',
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

// Xác định trạng thái tồn kho
const getInventoryStatus = (row) => {
  const qty = Number(row?.ton_kho ?? 0)
  const min = Number(row?.ton_thap_nhat ?? 0)
  const max = Number(row?.ton_cao_nhat ?? 0)

  if (qty === 0) {
    return { label: 'Hết hàng', class: 'bg-secondary' }
  }

  if (min > 0 && max > 0 && max >= min) {
    if (qty <= min) {
      return { label: 'Sắp hết hàng', class: 'bg-warning text-dark' }
    }
    if (qty > max) {
      return { label: 'Tồn vượt mức', class: 'bg-danger' }
    }
    return { label: 'Còn hàng', class: 'bg-success' }
  }

  return { label: 'Còn hàng', class: 'bg-success' }
}

const getProductTypeText = (type) => {
  return type === 'medicine' ? 'Thuốc' : 'Hàng hóa'
}

const getProductTypeBadgeClass = (type) => {
  return type === 'medicine' ? 'bg-info' : 'bg-primary'
}

const formatNumber = (num) => {
  return new Intl.NumberFormat('vi-VN').format(num)
}

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

<style>
/* Import CSS file */
@import '@Staff/stock-products.css';
</style>

<style scoped>
/* Scoped Style để xử lý riêng cho bảng trong component này */

/* Desktop Styling mặc định */
:deep(.p-datatable) {
  border-radius: 8px;
  overflow: hidden;
  border: 1px solid #e9ecef;
  /* Chỉnh lại viền cho nhẹ nhàng hơn */
}

:deep(.p-datatable .p-datatable-header) {
  background: #f8f9fa;
  border-bottom: 1px solid #e9ecef;
  padding: 16px 20px;
}

:deep(.p-datatable .p-datatable-thead > tr > th) {
  background: #3b82f6;
  color: #fff;
  font-weight: 700;
  border-bottom: 2px solid #91C4C3;
  padding: 16px 20px;
  font-size: 14px;
  white-space: nowrap;
  /* Tránh xuống dòng tiêu đề */
}

:deep(.p-datatable .p-datatable-tbody > tr) {
  transition: all 0.2s ease;
  border-bottom: 1px solid #f1f3f4;
}

:deep(.p-datatable .p-datatable-tbody > tr:hover) {
  background: #e3f2fd !important;
}

:deep(.p-datatable .p-datatable-tbody > tr > td) {
  padding: 16px 20px;
  color: #495057;
  font-size: 14px;
  vertical-align: middle;
  border-right: 1px solid #f1f3f4;
}

/* --- MOBILE RESPONSIVE OVERRIDES --- */
@media (max-width: 768px) {

  /* Ẩn Header của bảng */
  :deep(.p-datatable-thead) {
    display: none !important;
  }

  /* Biến mỗi dòng tr thành một Card */
  :deep(.p-datatable-tbody > tr) {
    display: block;
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    margin-bottom: 16px;
    /* Khoảng cách giữa các card */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
    padding: 0;
    overflow: hidden;
  }

  /* Ẩn border right của ô cuối cùng khi ở dạng card */
  :deep(.p-datatable-tbody > tr > td),
  :deep(.p-datatable-tbody > tr > td:last-child) {
    border-right: none;
  }

  /* Biến mỗi ô td thành một dòng Flex */
  :deep(.p-datatable-tbody > tr > td) {
    display: flex;
    justify-content: space-between;
    align-items: center;
    text-align: right;
    padding: 12px 16px;
    border-bottom: 1px solid #f8f9fa;
    width: 100% !important;
    /* Bắt buộc full chiều rộng */
    box-sizing: border-box;
  }

  /* Hiển thị label trên mobile */
  :deep(.mobile-label) {
    display: inline-block;
  }

  /* Styling riêng cho Tên sản phẩm để làm Tiêu đề Card */
  :deep(.col-ten-sp) {
    background-color: #f1f8e9;
    /* Màu nền nhẹ cho tiêu đề */
    border-bottom: 1px solid #B4DEBD !important;
    padding: 12px 16px !important;
  }

  :deep(.col-ten-sp .product-name-text) {
    font-weight: 700;
    color: #2c3e50;
    font-size: 15px;
    text-align: left;
    width: 100%;
    display: block;
  }

  /* Ẩn border bottom của dòng cuối cùng trong card */
  :deep(.p-datatable-tbody > tr > td:last-child) {
    border-bottom: none;
  }

  /* Striped rows logic: Bỏ màu nền xen kẽ trên mobile để nhìn sạch hơn */
  :deep(.p-datatable .p-datatable-tbody > tr:nth-child(even)),
  :deep(.p-datatable .p-datatable-tbody > tr:nth-child(odd)) {
    background: #ffffff;
  }
}
</style>