<template>
  <Dialog 
    :visible="visible" 
    @update:visible="$emit('close')"
    header="Quản lý đường dùng" 
    :style="{ width: '800px' }"
    modal
    :closable="true"
    @hide="resetForm"
  >
    <div class="modal-body p-4">
      <!-- Search and Add Section -->
      <div class="row g-2 mb-3">
        <div class="col">
          <InputText
            v-model="searchQuery"
            placeholder="Tìm theo tên…"
            class="field-input"
            @input="filterDrugRoutes"
          />
        </div>
        <div class="col-auto">
          <Button 
            label="Thêm" 
            icon="pi pi-plus"
            @click="openAddModal"
            severity="success"
          />
        </div>
      </div>

      <!-- Table -->
      <div class="table-responsive">
        <DataTable 
          :value="filteredDrugRoutes" 
          :loading="loading"
          class="p-datatable-sm"
          :paginator="true"
          :rows="10"
          paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
          :rowsPerPageOptions="[5,10,20]"
          currentPageReportTemplate="Hiển thị {first} đến {last} trong {totalRecords} bản ghi"
        >
          <Column field="id" header="Mã" :style="{ width: '90px' }">
            <template #body="slotProps">
              <span class="text-muted">{{ slotProps.data.id }}</span>
            </template>
          </Column>
          
          <Column field="name" header="Tên đường dùng">
            <template #body="slotProps">
              <span>{{ slotProps.data.name }}</span>
            </template>
          </Column>
          
          <Column field="description" header="Mô tả">
            <template #body="slotProps">
              <span class="text-muted">{{ slotProps.data.description || '-' }}</span>
            </template>
          </Column>
          
          <Column field="created_at" header="Ngày tạo" :style="{ width: '150px' }">
            <template #body="slotProps">
              <span class="text-muted">{{ formatDate(slotProps.data.created_at) }}</span>
            </template>
          </Column>
          
          <Column header="Thao tác" :style="{ width: '120px' }">
            <template #body="slotProps">
              <div class="d-flex gap-1">
                <Button 
                  icon="pi pi-pencil" 
                  size="small"
                  severity="info"
                  @click="editDrugRoute(slotProps.data)"
                />
                <Button 
                  icon="pi pi-trash" 
                  size="small"
                  severity="danger"
                  @click="deleteDrugRoute(slotProps.data)"
                />
              </div>
            </template>
          </Column>
        </DataTable>
      </div>
    </div>

    <!-- Add/Edit Form Modal -->
    <Dialog 
      :visible="showFormModal" 
      @update:visible="showFormModal = $event"
      :header="isEdit ? 'Chỉnh sửa đường dùng' : 'Thêm đường dùng mới'"
      :style="{ width: '500px' }"
      modal
      :closable="true"
      @hide="resetFormData"
    >
      <form @submit.prevent="submitForm" class="p-4">
        <div class="mb-3">
          <label for="drugRouteName" class="form-label">Tên đường dùng <span class="text-danger">*</span></label>
          <InputText
            id="drugRouteName"
            v-model="formData.name"
            class="field-input"
            :class="{ 'p-invalid': errors.name }"
            placeholder="Nhập tên đường dùng"
          />
          <small v-if="errors.name" class="p-error">{{ errors.name }}</small>
        </div>
        
        <div class="mb-3">
          <label for="drugRouteDescription" class="form-label">Mô tả</label>
          <Textarea
            id="drugRouteDescription"
            v-model="formData.description"
            class="field-input"
            rows="3"
            placeholder="Nhập mô tả (tùy chọn)"
          />
        </div>
        
        <div class="d-flex justify-content-end gap-2">
          <Button 
            label="Hủy" 
            severity="secondary"
            @click="showFormModal = false"
          />
          <Button 
            :label="isEdit ? 'Cập nhật' : 'Thêm'" 
            type="submit"
            :loading="submitting"
            severity="success"
          />
        </div>
      </form>
    </Dialog>

    <!-- Delete Confirmation Modal -->
    <Dialog 
      :visible="showDeleteModal" 
      @update:visible="showDeleteModal = $event"
      header="Xác nhận xóa"
      :style="{ width: '400px' }"
      modal
    >
      <div class="p-4">
        <p>Bạn có chắc chắn muốn xóa đường dùng <strong>{{ selectedDrugRoute?.name }}</strong>?</p>
        <p class="text-danger small">Hành động này không thể hoàn tác.</p>
        
        <div class="d-flex justify-content-end gap-2 mt-3">
          <Button 
            label="Hủy" 
            severity="secondary"
            @click="showDeleteModal = false"
          />
          <Button 
            label="Xóa" 
            severity="danger"
            :loading="deleting"
            @click="confirmDelete"
          />
        </div>
      </div>
    </Dialog>
  </Dialog>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useToast } from 'primevue/usetoast'
import Dialog from 'primevue/dialog'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'
import Button from 'primevue/button'
import axios from 'axios'

const props = defineProps({
  visible: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'drug-route-added', 'drug-route-updated'])

const toast = useToast()

// State
const loading = ref(false)
const drugRoutes = ref([])
const filteredDrugRoutes = ref([])
const searchQuery = ref('')

// Form modal state
const showFormModal = ref(false)
const isEdit = ref(false)
const submitting = ref(false)
const formData = ref({
  id: null,
  name: '',
  description: ''
})

// Delete modal state
const showDeleteModal = ref(false)
const deleting = ref(false)
const selectedDrugRoute = ref(null)

// Validation errors
const errors = ref({})

// Computed
const filteredDrugRoutesComputed = computed(() => {
  if (!searchQuery.value) return drugRoutes.value
  
  return drugRoutes.value.filter(drugRoute => 
    drugRoute.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    (drugRoute.description && drugRoute.description.toLowerCase().includes(searchQuery.value.toLowerCase()))
  )
})

// Watch for search query changes
watch(searchQuery, () => {
  filteredDrugRoutes.value = filteredDrugRoutesComputed.value
})

// Methods
const fetchDrugRoutes = async () => {
  loading.value = true
  try {
    const response = await axios.get('/admin/products/drugroute')
    drugRoutes.value = response.data.data || []
    filteredDrugRoutes.value = drugRoutes.value
  } catch (error) {
    console.error('Error fetching drug routes:', error)
    toast.add({
      severity: 'error',
      summary: 'Lỗi',
      detail: 'Không thể tải danh sách đường dùng',
      life: 3000
    })
  } finally {
    loading.value = false
  }
}

const filterDrugRoutes = () => {
  filteredDrugRoutes.value = filteredDrugRoutesComputed.value
}

const openAddModal = () => {
  isEdit.value = false
  resetFormData()
  showFormModal.value = true
}

const editDrugRoute = (drugRoute) => {
  isEdit.value = true
  formData.value = {
    id: drugRoute.id,
    name: drugRoute.name,
    description: drugRoute.description || ''
  }
  showFormModal.value = true
}

const deleteDrugRoute = (drugRoute) => {
  selectedDrugRoute.value = drugRoute
  showDeleteModal.value = true
}

const submitForm = async () => {
  errors.value = {}
  
  // Validation
  if (!formData.value.name.trim()) {
    errors.value.name = 'Tên đường dùng là bắt buộc'
    return
  }
  
  submitting.value = true
  try {
    const url = isEdit.value 
      ? `/admin/products/drugroute/${formData.value.id}`
      : '/admin/products/drugroute'
    
    const method = isEdit.value ? 'PUT' : 'POST'
    
    await axios({
      method,
      url,
      data: formData.value
    })
    
    toast.add({
      severity: 'success',
      summary: 'Thành công',
      detail: isEdit.value ? 'Cập nhật đường dùng thành công' : 'Thêm đường dùng thành công',
      life: 3000
    })
    
    showFormModal.value = false
    await fetchDrugRoutes()
    
    // Emit event to parent component
    if (isEdit.value) {
      emit('drug-route-updated', formData.value)
    } else {
      emit('drug-route-added', formData.value)
    }
    
  } catch (error) {
    console.error('Error submitting drug route:', error)
    
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors || {}
    }
    
    toast.add({
      severity: 'error',
      summary: 'Lỗi',
      detail: error.response?.data?.message || 'Có lỗi xảy ra khi xử lý yêu cầu',
      life: 3000
    })
  } finally {
    submitting.value = false
  }
}

const confirmDelete = async () => {
  deleting.value = true
  try {
    await axios.delete(`/admin/products/drugroute/${selectedDrugRoute.value.id}`)
    
    toast.add({
      severity: 'success',
      summary: 'Thành công',
      detail: 'Xóa đường dùng thành công',
      life: 3000
    })
    
    showDeleteModal.value = false
    await fetchDrugRoutes()
    
  } catch (error) {
    console.error('Error deleting drug route:', error)
    toast.add({
      severity: 'error',
      summary: 'Lỗi',
      detail: error.response?.data?.message || 'Có lỗi xảy ra khi xóa đường dùng',
      life: 3000
    })
  } finally {
    deleting.value = false
  }
}

const resetForm = () => {
  searchQuery.value = ''
  filteredDrugRoutes.value = drugRoutes.value
  showFormModal.value = false
  showDeleteModal.value = false
  selectedDrugRoute.value = null
}

const resetFormData = () => {
  formData.value = {
    id: null,
    name: '',
    description: ''
  }
  errors.value = {}
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('vi-VN')
}

// Lifecycle
onMounted(() => {
  if (props.visible) {
    fetchDrugRoutes()
  }
})

// Watch for modal visibility
watch(() => props.visible, (newVal) => {
  if (newVal) {
    fetchDrugRoutes()
  } else {
    resetForm()
  }
})
</script>

<style scoped>
.modal-body {
  max-height: 70vh;
  overflow-y: auto;
}

.field-input {
  width: 100%;
}

.p-datatable-sm {
  font-size: 0.9rem;
}

.text-muted {
  color: #6c757d;
}

.p-error {
  color: #dc3545;
  font-size: 0.875rem;
}
</style>
