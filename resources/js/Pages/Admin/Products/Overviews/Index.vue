<template>
  <div class="products-page">
    <!-- Header Control Bar Component -->
    <ProductHeader v-model:searchQuery="searchQuery" v-model:showDropdown="showDropdown"
      @toggle-dropdown="toggleDropdown" @create-medicine="createMedicine" @create-goods="createGoods"
      @create-service="createService" @export-file="exportFile" @debounce-search="debounceSearch" />

    <!-- Main Content với 2 columns (Sidebar & DataTable) -->
    <div class="main-content">
      <!-- Left Sidebar Component -->
      <ProductSidebar :loadingCategories="loadingCategories" :categoryTreeNodes="categoryTreeNodes"
        v-model:selectedCategoryKeys="selectedCategoryKeys" :selectedCategoryName="selectedCategoryName"
        :filters="filters" @node-select="onCategorySelect" @node-unselect="onCategoryUnselect"
        @reset-category="resetCategorySelection" @create-category="createCategory" @edit-category="editCategory" />

      <!-- Right Main Content Component (DataTable) -->
      <ProductDataTable :products="filteredProducts" :pagination="pagination" v-model:expandedRows="expandedRows"
        :activeTab="activeTab" :getImageUrl="getImageUrl" :handleImageError="handleImageError"
        :getProductTypeBadgeClass="getProductTypeBadgeClass" :getProductTypeText="getProductTypeText"
        :formatCurrency="formatCurrency" :formatDate="formatDate" :getInventoryStatus="getInventoryStatus"
        @switch-tab="switchTab" @edit-product="editProduct" @delete-product="deleteProduct" />
    </div>

    <!-- Modals Section (Giữ ở Root để quản lý tập trung) -->
    <CategoryCommodity v-model:visible="showCreateCategoryModal" :categoryTreeNodes="categoryTreeNodes"
      @save="onSaveCategory" @cancel="onCancelCategory" />

    <EditCategoryCommodity v-model:visible="showEditCategoryModal" :categoryTreeNodes="categoryTreeNodes"
      :categoryData="editingCategory" @save="onUpdateCategory" @cancel="onCancelEditCategory"
      @delete="onDeleteCategory" />

    <CreateMedicine :visible="showCreateMedicineModal" @close="showCreateMedicineModal = false"
      @created="onMedicineCreated" />

    <CreateGoods :visible="showCreateGoodsModal" @close="showCreateGoodsModal = false" @created="onGoodsCreated" />

    <CreateService :visible="showCreateServiceModal" @close="showCreateServiceModal = false"
      @created="onServiceCreated" />

    <MedicineEditModal :visible="showEditMedicineModal" :medicine-data="editingMedicine"
      @close="showEditMedicineModal = false" @edited="onMedicineEdited" />

    <GoodsEditModal :visible="showEditGoodsModal" :goods-data="editingGoods" @close="showEditGoodsModal = false"
      @edited="onGoodsEdited" />
  </div>
</template>

<script setup>
// PrimeVue components (used by Index or Layout)
import { onMounted, watch, ref } from 'vue'

// Project internal components
import ProductHeader from './Components/ProductHeader.vue'
import ProductSidebar from './Components/ProductSidebar.vue'
import ProductDataTable from './Components/ProductDataTable.vue'

// Modals Section
import CategoryCommodity from './Modals/CategoryCommodity.vue'
import EditCategoryCommodity from './Modals/EditCategoryCommodity.vue'
import CreateMedicine from './Create/Medicine.vue'
import CreateGoods from './Create/Goods.vue'
import CreateService from './Create/Services.vue'
import MedicineEditModal from './Edit/Medicine.vue'
import GoodsEditModal from './Edit/Goods.vue'

// Composables
import { useModals } from '@/Composables/Products/Overviews/useModals'
import { useCategories } from '@/Composables/Products/Overviews/useCategories'
import { useProducts } from '@/Composables/Products/Overviews/useProducts'

// CSS Imports
import '@Admin/products/overviews/layout.css'
import '@Admin/products/overviews/header.css'
import '@Admin/products/overviews/sidebar.css'
import '@Admin/products/overviews/datatable.css'

// Initializing hooks
const {
  showCreateCategoryModal, showEditCategoryModal, showCreateMedicineModal,
  showCreateGoodsModal, showCreateServiceModal, showEditMedicineModal,
  showEditGoodsModal, showDropdown, editingCategory, editingMedicine,
  editingGoods, toggleDropdown, openCreateMedicine, openCreateGoods,
  openCreateService, openCreateCategory
} = useModals()

// Alias names to match template
const createMedicine = openCreateMedicine
const createGoods = openCreateGoods
const createService = openCreateService
const createCategory = openCreateCategory

const {
  products, searchQuery, loading, filters, pagination, filteredProducts,
  loadProducts, deleteProduct
} = useProducts()

const {
  loadingCategories, selectedCategoryId, selectedCategoryName, selectedCategoryKeys,
  categoryTreeNodes, loadCategories, onCategorySelect, onCategoryUnselect,
  resetCategorySelection, editCategory: getCategoryData, saveCategory, updateCategory, deleteCategory
} = useCategories(() => loadProducts(selectedCategoryId.value))


// UI State (Table expansion and tabs)
const expandedRows = ref({})
const activeTab = ref('info')
const switchTab = (tab) => { activeTab.value = tab }

// UI Helpers
const getInventoryStatus = (row) => {
  const qty = Number(row?.ton_kho ?? 0)
  const min = Number(row?.ton_thap_nhat ?? 0)
  const max = Number(row?.ton_cao_nhat ?? 0)
  if (qty === 0) return { label: 'Hết hàng', class: 'bg-secondary' }
  if (min > 0 && max > 0 && max >= min) {
    if (qty <= min) return { label: 'Sắp hết hàng', class: 'bg-warning text-dark' }
    if (qty > max) return { label: 'Tồn vượt mức', class: 'bg-danger' }
    return { label: 'Còn hàng', class: 'bg-success' }
  }
  return { label: 'Còn hàng', class: 'bg-success' }
}

const getProductTypeText = (type) => {
  const typeMap = { medicine: 'Thuốc', goods: 'Hàng hóa', service: 'Dịch vụ', combo: 'Combo' }
  return typeMap[type] || type || '-'
}

const getProductTypeBadgeClass = (type) => {
  const classMap = { medicine: 'bg-primary', goods: 'bg-success', service: 'bg-info', combo: 'bg-warning' }
  return classMap[type] || 'bg-secondary'
}

const formatCurrency = (value) => {
  if (!value) return '0 ₫'
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value)
}

const formatDate = (date) => date ? new Date(date).toLocaleDateString('vi-VN') : '-'

const getImageUrl = (imagePath) => {
  if (!imagePath) return null
  return imagePath.startsWith('http') ? imagePath : `/storage/${imagePath}`
}

const handleImageError = (event) => { event.target.style.display = 'none' }

// Modal & Action Callbacks
const editProduct = (product) => {
  if (product.product_type === 'medicine') {
    editingMedicine.value = product
    showEditMedicineModal.value = true
  } else if (product.product_type === 'goods') {
    editingGoods.value = product
    showEditGoodsModal.value = true
  }
}
const editCategory = (node) => {
  editingCategory.value = getCategoryData(node)
  showEditCategoryModal.value = true
}

const onSaveCategory = async (data) => {
  if (await saveCategory(data)) showCreateCategoryModal.value = false
}

const onCancelCategory = () => { showCreateCategoryModal.value = false }

const onUpdateCategory = async (data) => {
  if (await updateCategory(data)) showEditCategoryModal.value = false
}

const onCancelEditCategory = () => {
  showEditCategoryModal.value = false
  editingCategory.value = {}
}

const onDeleteCategory = async (id) => {
  if (await deleteCategory(id)) showEditCategoryModal.value = false
}

const onMedicineCreated = async (data) => {
  showCreateMedicineModal.value = false
  if (data?.id) products.value.unshift(data)
  else await loadProducts()
}

const onGoodsCreated = async () => {
  showCreateGoodsModal.value = false
  await loadProducts()
}

const onServiceCreated = async () => {
  showCreateServiceModal.value = false
  await loadProducts()
}

const onMedicineEdited = async () => {
  showEditMedicineModal.value = false
  editingMedicine.value = null
  await loadProducts()
}

const onGoodsEdited = async () => {
  showEditGoodsModal.value = false
  editingGoods.value = null
  await loadProducts()
}


// Lifecycle and Watchers
onMounted(() => {
  loadCategories()
  loadProducts()
  document.addEventListener('click', (event) => {
    if (!event.target.closest('.dropdown')) showDropdown.value = false
  })
})

const debounceSearch = () => {
  clearTimeout(window.searchTimer)
  window.searchTimer = setTimeout(() => {
    loadProducts(selectedCategoryId.value)
  }, 300)
}

watch([() => filters.fromDate, () => filters.toDate], () => {
  loadProducts(selectedCategoryId.value)
})
</script>