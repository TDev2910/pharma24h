<template>
  <Dialog 
    v-model:visible="dialogVisible"
    header="Tạo Nhóm Nhà Cung Cấp Mới"
    :style="{ width: '600px' }"
    :modal="true"
    :closable="true"
    @hide="resetForm"
  >
    <form @submit.prevent="handleSubmit">
      <div class="modal-body">
        <!-- Tên nhóm -->
        <div class="mb-3">
          <label for="category_name" class="form-label">
            Tên nhóm <span class="text-danger">*</span>
          </label>
          <InputText
            id="category_name"
            v-model="form.name"
            class="w-100"
            :class="{ 'p-invalid': errors.name }"
            placeholder="Ví dụ: Nhà cung cấp thuốc, Nhà cung cấp thiết bị y tế..."
            maxlength="255"
          />
          <small v-if="errors.name" class="p-error">{{ errors.name[0] }}</small>
        </div>

        <!-- Mô tả -->
        <div class="mb-3">
          <label for="category_description" class="form-label">Mô tả</label>
          <Textarea
            id="category_description"
            v-model="form.description"
            class="w-100"
            :class="{ 'p-invalid': errors.description }"
            placeholder="Mô tả chi tiết về nhóm nhà cung cấp này..."
            rows="3"
            maxlength="500"
          />
          <small v-if="errors.description" class="p-error">{{ errors.description[0] }}</small>
        </div>

        <!-- Trạng thái -->
        <div class="mb-3">
          <label for="category_status" class="form-label">Trạng thái</label>
          <Dropdown
            id="category_status"
            v-model="form.status"
            :options="statusOptions"
            optionLabel="label"
            optionValue="value"
            placeholder="-- Chọn trạng thái --"
            class="w-100"
            :class="{ 'p-invalid': errors.status }"
          />
          <small v-if="errors.status" class="p-error">{{ errors.status[0] }}</small>
        </div>
      </div>
    </form>
    
    <template #footer>
      <div class="flex justify-content-end gap-2">
        <Button 
          type="button" 
          label="Hủy" 
          severity="secondary"
          @click="closeModal"
        />
        <Button 
          type="button" 
          label="Tạo nhóm"
          :loading="submitting"
          icon="pi pi-save"
          @click="handleSubmit"
        />
      </div>
    </template>
  </Dialog>
</template>

<script>
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'
import Dropdown from 'primevue/dropdown'
import { useToast } from 'primevue/usetoast'
import { router } from '@inertiajs/vue3'

export default {
  name: 'SupplierCategoryCreateModal',
  components: {
    Dialog,
    Button,
    InputText,
    Textarea,
    Dropdown
  },
  
  props: {
    visible: {
      type: Boolean,
      default: false
    }
  },
  
  emits: ['update:visible', 'category-created'],
  
  setup(props, { emit }) {
    const toast = useToast()
    
    return {
      toast,
      emit
    }
  },
  
  data() {
    return {
      dialogVisible: false,
      form: {
        name: '',
        description: '',
        status: 'active'
      },
      submitting: false,
      errors: {},
      statusOptions: [
        { label: 'Kích hoạt', value: 'active' },
        { label: 'Tạm ngưng', value: 'inactive' }
      ]
    }
  },
  
  watch: {
    visible(newVal) {
      this.dialogVisible = newVal
    },
    dialogVisible(newVal) {
      if (!newVal) {
        this.$emit('update:visible', false)
      }
    }
  },
  
  methods: {
    validateForm() {
      this.errors = {}
      let isValid = true
      
      // Required fields
      if (!this.form.name?.trim()) {
        this.errors.name = ['Trường này là bắt buộc']
        isValid = false
      }
      
      // Name length validation
      if (this.form.name && this.form.name.length > 255) {
        this.errors.name = ['Tên nhóm không được vượt quá 255 ký tự']
        isValid = false
      }
      
      // Description length validation
      if (this.form.description && this.form.description.length > 500) {
        this.errors.description = ['Mô tả không được vượt quá 500 ký tự']
        isValid = false
      }
      
      return isValid
    },
    
    async handleSubmit() {
      if (!this.validateForm()) {
        return
      }
      
      this.submitting = true
      
      // Prepare form data
      const submitData = {
        name: this.form.name.trim(),
        description: this.form.description?.trim() || null,
        status: this.form.status || 'active'
      }
      
      try {
        router.post('/admin/supplier-categories', submitData, {
          preserveScroll: true,
          onSuccess: (page) => {
            this.toast.add({
              severity: 'success',
              summary: 'Thành công',
              detail: 'Tạo nhóm nhà cung cấp thành công!',
              life: 3000
            })
            
            this.resetForm()
            this.closeModal()
            this.$emit('category-created')
          },
          onError: (errors) => {
            this.errors = errors
            this.toast.add({
              severity: 'error',
              summary: 'Lỗi',
              detail: 'Có lỗi xảy ra khi tạo nhóm nhà cung cấp',
              life: 5000
            })
          },
          onFinish: () => {
            this.submitting = false
          }
        })
      } catch (error) {
        console.error('Submit error:', error)
        this.submitting = false
        this.toast.add({
          severity: 'error',
          summary: 'Lỗi',
          detail: error.message || 'Có lỗi xảy ra',
          life: 5000
        })
      }
    },
    
    resetForm() {
      this.form = {
        name: '',
        description: '',
        status: 'active'
      }
      this.errors = {}
    },
    
    closeModal() {
      this.emit('update:visible', false)
    }
  }
}
</script>

<style scoped>
.modal-body {
  padding: 1.5rem;
}

.form-label {
  font-weight: 600;
  color: #495057;
  margin-bottom: 0.5rem;
  display: block;
}

.text-danger {
  color: #dc3545;
}

:deep(.p-inputtext),
:deep(.p-dropdown),
:deep(.p-textarea) {
  width: 100%;
}

:deep(.p-invalid) {
  border-color: #dc3545;
}

.p-error {
  color: #dc3545;
  font-size: 0.875rem;
  display: block;
  margin-top: 0.25rem;
}
</style>

