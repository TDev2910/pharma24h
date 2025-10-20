<template>
  <!-- Modal Tạo nhóm hàng -->
  <Dialog 
    :visible="visible" 
    @update:visible="$emit('update:visible', $event)"
    modal 
    header="Tạo nhóm hàng" 
    :style="{ width: '450px' }"
    :closable="true"
    @hide="onHide"
  >
    <div class="modal-content">
      <div class="form-group">
        <label for="categoryName" class="form-label">Tên nhóm hàng</label>
        <InputText 
          id="categoryName"
          v-model="formData.name" 
          placeholder="Nhập tên nhóm hàng"
          class="w-full"
          :class="{ 'p-invalid': !formData.name.trim() }"
        />
      </div>
      
      <div class="form-group">
        <label for="parentCategory" class="form-label">Nhóm cha (nếu có)</label>
        <Dropdown 
          id="parentCategory"
          v-model="formData.parentId" 
          :options="parentCategoryOptions"
          optionLabel="label"
          optionValue="value"
          placeholder="Chọn nhóm cha"
          class="w-full"
        />
      </div>
    </div>
    
    <template #footer>
      <div class="modal-footer">
        <Button 
          label="Bỏ qua" 
          icon="pi pi-times" 
          @click="cancel" 
          severity="secondary"
          class="mr-2"
        />
        <Button 
          label="Lưu" 
          icon="pi pi-check" 
          @click="save" 
          severity="success"
        />
      </div>
    </template>
  </Dialog>
</template>

<script>
import Dialog from 'primevue/dialog'
import InputText from 'primevue/inputtext'
import Dropdown from 'primevue/dropdown'
import Button from 'primevue/button'

export default {
  name: 'CategoryCommodityModal',
  components: {
    Dialog,
    InputText,
    Dropdown,
    Button
  },
  
  props: {
    visible: {
      type: Boolean,
      default: false
    },
    categoryTreeNodes: {
      type: Array,
      default: () => []
    }
  },
  
  emits: ['update:visible', 'save', 'cancel'],
  
  data() {
    return {
      formData: {
        name: '',
        parentId: null
      },
      parentCategoryOptions: []
    }
  },
  
  watch: {
    visible(newVal) {
      if (newVal) {
        this.resetForm()
        this.loadParentCategoryOptions()
      }
    }
  },
  
  methods: {
    resetForm() {
      this.formData = {
        name: '',
        parentId: null
      }
    },
    
    loadParentCategoryOptions() {
      // Tạo danh sách parent categories từ tree nodes
      this.parentCategoryOptions = [
        { label: 'Không có nhóm cha', value: null }
      ]
      
      const addToOptions = (nodes, level = 0) => {
        nodes.forEach(node => {
          const prefix = '─ '.repeat(level)
          this.parentCategoryOptions.push({
            label: prefix + node.label,
            value: node.data.id
          })
          if (node.children) {
            addToOptions(node.children, level + 1)
          }
        })
      }
      
      addToOptions(this.categoryTreeNodes)
    },
    
    save() {
      if (!this.formData.name.trim()) {
        alert('Vui lòng nhập tên nhóm hàng!')
        return
      }
      
      this.$emit('save', { ...this.formData })
      this.close()
    },
    
    cancel() {
      this.$emit('cancel')
      this.close()
    },
    
    close() {
      this.$emit('update:visible', false)
    },
    
    onHide() {
      this.resetForm()
    }
  }
}
</script>

<style scoped>
/* Modal Styles */
.modal-content {
  padding: 0;
}

.form-group {
  margin-bottom: 20px;
}

.form-label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #495057;
  font-size: 14px;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
  padding-top: 20px;
  border-top: 1px solid #e9ecef;
}

/* PrimeVue Dialog customization */
:deep(.p-dialog .p-dialog-header) {
  background: #fff;
  border-bottom: 1px solid #e9ecef;
  padding: 20px 24px;
}

:deep(.p-dialog .p-dialog-content) {
  padding: 24px;
  background: #fff;
}

:deep(.p-dialog .p-dialog-footer) {
  background: #fff;
  border-top: 1px solid #e9ecef;
  padding: 20px 24px;
}

:deep(.p-dialog .p-dialog-title) {
  font-weight: 600;
  color: #2c3e50;
  font-size: 18px;
}

/* Form validation */
:deep(.p-inputtext.p-invalid) {
  border-color: #dc3545;
}

:deep(.p-inputtext.p-invalid:focus) {
  box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
}

/* Responsive */
@media (max-width: 768px) {
  :deep(.p-dialog) {
    width: 95% !important;
    margin: 0 auto;
  }
}
</style>
