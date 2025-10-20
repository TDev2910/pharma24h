<template>
  <Dialog 
    :visible="visible" 
    @update:visible="$emit('close')"
    header="Quản lý hãng sản xuất" 
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
            @input="filterManufacturers"
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
          :value="filteredManufacturers" 
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
          
          <Column field="name" header="Tên hãng sản xuất">
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
                  @click="editManufacturer(slotProps.data)"
                />
                <Button 
                  icon="pi pi-trash" 
                  size="small"
                  severity="danger"
                  @click="deleteManufacturer(slotProps.data)"
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
      :header="isEdit ? 'Chỉnh sửa hãng sản xuất' : 'Thêm hãng sản xuất mới'"
      :style="{ width: '500px' }"
      modal
      :closable="true"
      @hide="resetFormData"
    >
      <form @submit.prevent="submitForm" class="p-4">
        <div class="mb-3">
          <label for="manufacturerName" class="form-label">Tên hãng sản xuất <span class="text-danger">*</span></label>
          <InputText
            id="manufacturerName"
            v-model="formData.name"
            class="field-input"
            :class="{ 'p-invalid': errors.name }"
            placeholder="Nhập tên hãng sản xuất"
          />
          <small v-if="errors.name" class="p-error">{{ errors.name }}</small>
        </div>
        
        <div class="mb-3">
          <label for="manufacturerDescription" class="form-label">Mô tả</label>
          <Textarea
            id="manufacturerDescription"
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
        <p>Bạn có chắc chắn muốn xóa hãng sản xuất <strong>{{ selectedManufacturer?.name }}</strong>?</p>
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

const emit = defineEmits(['close', 'manufacturer-added', 'manufacturer-updated'])

const toast = useToast()

// State
const loading = ref(false)
const manufacturers = ref([])
const filteredManufacturers = ref([])
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
const selectedManufacturer = ref(null)

// Validation errors
const errors = ref({})

// Computed
const filteredManufacturersComputed = computed(() => {
  if (!searchQuery.value) return manufacturers.value
  
  return manufacturers.value.filter(manufacturer => 
    manufacturer.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    (manufacturer.description && manufacturer.description.toLowerCase().includes(searchQuery.value.toLowerCase()))
  )
})

// Watch for search query changes
watch(searchQuery, () => {
  filteredManufacturers.value = filteredManufacturersComputed.value
})

// Methods
const fetchManufacturers = async () => {
  loading.value = true
  try {
    const response = await axios.get('/admin/products/manufacturer')
    manufacturers.value = response.data.data || []
    filteredManufacturers.value = manufacturers.value
  } catch (error) {
    console.error('Error fetching manufacturers:', error)
    toast.add({
      severity: 'error',
      summary: 'Lỗi',
      detail: 'Không thể tải danh sách hãng sản xuất',
      life: 3000
    })
  } finally {
    loading.value = false
  }
}

const filterManufacturers = () => {
  filteredManufacturers.value = filteredManufacturersComputed.value
}

const openAddModal = () => {
  isEdit.value = false
  resetFormData()
  showFormModal.value = true
}

const editManufacturer = (manufacturer) => {
  isEdit.value = true
  formData.value = {
    id: manufacturer.id,
    name: manufacturer.name,
    description: manufacturer.description || ''
  }
  showFormModal.value = true
}

const deleteManufacturer = (manufacturer) => {
  selectedManufacturer.value = manufacturer
  showDeleteModal.value = true
}

const submitForm = async () => {
  errors.value = {}
  
  // Validation
  if (!formData.value.name.trim()) {
    errors.value.name = 'Tên hãng sản xuất là bắt buộc'
    return
  }
  
  submitting.value = true
  try {
    const url = isEdit.value 
      ? `/admin/products/manufacturer/${formData.value.id}`
      : '/admin/products/manufacturer'
    
    const method = isEdit.value ? 'PUT' : 'POST'
    
    await axios({
      method,
      url,
      data: formData.value
    })
    
    toast.add({
      severity: 'success',
      summary: 'Thành công',
      detail: isEdit.value ? 'Cập nhật hãng sản xuất thành công' : 'Thêm hãng sản xuất thành công',
      life: 3000
    })
    
    showFormModal.value = false
    await fetchManufacturers()
    
    // Emit event to parent component
    if (isEdit.value) {
      emit('manufacturer-updated', formData.value)
    } else {
      emit('manufacturer-added', formData.value)
    }
    
  } catch (error) {
    console.error('Error submitting manufacturer:', error)
    
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
    await axios.delete(`/admin/products/manufacturer/${selectedManufacturer.value.id}`)
    
    toast.add({
      severity: 'success',
      summary: 'Thành công',
      detail: 'Xóa hãng sản xuất thành công',
      life: 3000
    })
    
    showDeleteModal.value = false
    await fetchManufacturers()
    
  } catch (error) {
    console.error('Error deleting manufacturer:', error)
    toast.add({
      severity: 'error',
      summary: 'Lỗi',
      detail: error.response?.data?.message || 'Có lỗi xảy ra khi xóa hãng sản xuất',
      life: 3000
    })
  } finally {
    deleting.value = false
  }
}

const resetForm = () => {
  searchQuery.value = ''
  filteredManufacturers.value = manufacturers.value
  showFormModal.value = false
  showDeleteModal.value = false
  selectedManufacturer.value = null
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
    fetchManufacturers()
  }
})

// Watch for modal visibility
watch(() => props.visible, (newVal) => {
  if (newVal) {
    fetchManufacturers()
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
