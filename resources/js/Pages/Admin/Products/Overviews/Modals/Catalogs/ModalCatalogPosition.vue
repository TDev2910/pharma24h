<template>
  <Dialog 
    :visible="visible" 
    @update:visible="$emit('close')"
    header="Quản lý vị trí" 
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
            @input="filterPositions"
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
          :value="filteredPositions" 
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
          
          <Column field="name" header="Tên vị trí">
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
                  @click="editPosition(slotProps.data)"
                />
                <Button 
                  icon="pi pi-trash" 
                  size="small"
                  severity="danger"
                  @click="deletePosition(slotProps.data)"
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
      :header="isEdit ? 'Chỉnh sửa vị trí' : 'Thêm vị trí mới'"
      :style="{ width: '500px' }"
      modal
      :closable="true"
      @hide="resetFormData"
    >
      <form @submit.prevent="submitForm" class="p-4">
        <div class="mb-3">
          <label for="positionName" class="form-label">Tên vị trí <span class="text-danger">*</span></label>
          <InputText
            id="positionName"
            v-model="formData.name"
            class="field-input"
            :class="{ 'p-invalid': errors.name }"
            placeholder="Nhập tên vị trí"
          />
          <small v-if="errors.name" class="p-error">{{ errors.name }}</small>
        </div>
        
        <div class="mb-3">
          <label for="positionDescription" class="form-label">Mô tả</label>
          <Textarea
            id="positionDescription"
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
        <p>Bạn có chắc chắn muốn xóa vị trí <strong>{{ selectedPosition?.name }}</strong>?</p>
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

const emit = defineEmits(['close', 'position-added', 'position-updated'])

const toast = useToast()

// State
const loading = ref(false)
const positions = ref([])
const filteredPositions = ref([])
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
const selectedPosition = ref(null)

// Validation errors
const errors = ref({})

// Computed
const filteredPositionsComputed = computed(() => {
  if (!searchQuery.value) return positions.value
  
  return positions.value.filter(position => 
    position.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    (position.description && position.description.toLowerCase().includes(searchQuery.value.toLowerCase()))
  )
})

// Watch for search query changes
watch(searchQuery, () => {
  filteredPositions.value = filteredPositionsComputed.value
})

// Methods
const fetchPositions = async () => {
  loading.value = true
  try {
    const response = await axios.get('/admin/products/position')
    positions.value = response.data.data || []
    filteredPositions.value = positions.value
  } catch (error) {
    console.error('Error fetching positions:', error)
    toast.add({
      severity: 'error',
      summary: 'Lỗi',
      detail: 'Không thể tải danh sách vị trí',
      life: 3000
    })
  } finally {
    loading.value = false
  }
}

const filterPositions = () => {
  filteredPositions.value = filteredPositionsComputed.value
}

const openAddModal = () => {
  isEdit.value = false
  resetFormData()
  showFormModal.value = true
}

const editPosition = (position) => {
  isEdit.value = true
  formData.value = {
    id: position.id,
    name: position.name,
    description: position.description || ''
  }
  showFormModal.value = true
}

const deletePosition = (position) => {
  selectedPosition.value = position
  showDeleteModal.value = true
}

//nhận dữ liệu đầu vào và gưi form
const submitForm = async () => {
  errors.value = {}
  
  // Validation
  if (!formData.value.name.trim()) {
    errors.value.name = 'Tên vị trí là bắt buộc'
    return
  }
  
  submitting.value = true
  try {
    const url = isEdit.value 
      ? `/admin/products/position/${formData.value.id}`
      : '/admin/products/position'
    
    const method = isEdit.value ? 'PUT' : 'POST'
    
    await axios({
      method,
      url,
      data: formData.value
    })
    
    toast.add({
      severity: 'success',
      summary: 'Thành công',
      detail: isEdit.value ? 'Cập nhật vị trí thành công' : 'Thêm vị trí thành công',
      life: 3000
    })
    
    showFormModal.value = false
    await fetchPositions()
    
    // Emit event to parent component
    if (isEdit.value) {
      emit('position-updated', formData.value)
    } else {
      emit('position-added', formData.value)
    }
    
  } catch (error) {
    console.error('Error submitting position:', error)
    
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
    await axios.delete(`/admin/products/position/${selectedPosition.value.id}`)
    
    toast.add({
      severity: 'success',
      summary: 'Thành công',
      detail: 'Xóa vị trí thành công',
      life: 3000
    })
    
    showDeleteModal.value = false
    await fetchPositions()
    
  } catch (error) {
    console.error('Error deleting position:', error)
    toast.add({
      severity: 'error',
      summary: 'Lỗi',
      detail: error.response?.data?.message || 'Có lỗi xảy ra khi xóa vị trí',
      life: 3000
    })
  } finally {
    deleting.value = false
  }
}

const resetForm = () => {
  searchQuery.value = ''
  filteredPositions.value = positions.value
  showFormModal.value = false
  showDeleteModal.value = false
  selectedPosition.value = null
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
    fetchPositions()
  }
})

// Watch for modal visibility
watch(() => props.visible, (newVal) => {
  if (newVal) {
    fetchPositions()
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
