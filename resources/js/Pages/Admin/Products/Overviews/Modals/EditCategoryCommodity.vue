<template>
  <!-- Modal Chỉnh sửa nhóm hàng -->
  <Dialog 
    :visible="visible" 
    @update:visible="$emit('update:visible', $event)"
    modal 
    header="Chỉnh sửa nhóm hàng" 
    :style="{ width: '450px' }"
    :closable="true"
    @hide="onHide"
  >
    <div class="modal-content">
      <div class="form-group">
        <label for="editCategoryName" class="form-label">Tên nhóm hàng</label>
        <InputText 
          id="editCategoryName"
          v-model="formData.name" 
          placeholder="Nhập tên nhóm hàng"
          class="w-full"
          :class="{ 'p-invalid': !formData.name.trim() }"
        />
      </div>
      
      <div class="form-group">
        <label for="editParentCategory" class="form-label">Nhóm cha (nếu có)</label>
        <Dropdown 
          id="editParentCategory"
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
          label="Xóa" 
          icon="pi pi-trash" 
          @click="deleteCategory" 
          severity="danger"
          class="mr-2"
        />
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
import axios from 'axios'

export default {
  name: 'EditCategoryCommodityModal',
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
    },
    categoryData: {
      type: Object,
      default: () => ({})
    }
  },
  
  emits: ['update:visible', 'save', 'cancel', 'delete'],
  
  data() {
    return {
      formData: {
        id: null,
        name: '',
        parentId: null
      },
      parentCategoryOptions: []
    }
  },
  
  watch: {
    visible(newVal) {
      if (newVal && this.categoryData.id) {
        this.loadCategoryData()
        this.loadParentCategoryOptions()
      }
    },
    
    categoryData: {
      handler(newVal) {
        if (newVal && newVal.id) {
          this.loadCategoryData()
        }
      },
      deep: true
    }
  },
  
  methods: {
    loadCategoryData() {
      this.formData = {
        id: this.categoryData.id,
        name: this.categoryData.name || '',
        parentId: this.categoryData.parent_id || null
      }
    },
    
    loadParentCategoryOptions() {
      // Tạo danh sách parent categories từ tree nodes, loại trừ chính nó
      this.parentCategoryOptions = [
        { label: 'Không có nhóm cha', value: null }
      ]
      
      const addToOptions = (nodes, level = 0) => {
        nodes.forEach(node => {
          // Không cho phép chọn chính nó làm parent
          if (node.data.id !== this.formData.id) {
            const prefix = '─ '.repeat(level)
            this.parentCategoryOptions.push({
              label: prefix + node.label,
              value: node.data.id
            })
            if (node.children) {
              addToOptions(node.children, level + 1)
            }
          }
        })
      }
      
      addToOptions(this.categoryTreeNodes)
    },
    
    async save() {
      if (!this.formData.name.trim()) {
        alert('Vui lòng nhập tên nhóm hàng!')
        return
      }
      
      try {
        const response = await axios.put(`/admin/categories/${this.formData.id}`, {
          name: this.formData.name,
          parent_id: this.formData.parentId,
          sort_order: 0
        })
        
        if (response.status === 200) {
          this.$emit('save', { ...this.formData })
          this.close()
          console.log('Cập nhật nhóm hàng thành công!')
        }
      } catch (error) {
        console.error('Lỗi khi cập nhật nhóm hàng:', error)
        if (error.response && error.response.data && error.response.data.errors) {
          const errors = error.response.data.errors
          if (errors.name) {
            alert('Lỗi: ' + errors.name[0])
          } else if (errors.parent_id) {
            alert('Lỗi: ' + errors.parent_id[0])
          }
        } else {
          alert('Có lỗi xảy ra khi cập nhật nhóm hàng!')
        }
      }
    },
    
    async deleteCategory() {
      if (!this.formData.name) {
        alert('Không thể xóa nhóm hàng!')
        return
      }
      
      if (confirm(`Bạn có chắc chắn muốn xóa nhóm hàng "${this.formData.name}"?`)) {
        try {
          const response = await axios.delete(`/admin/categories/${this.formData.id}`)
          
          if (response.status === 200) {
            this.$emit('delete', this.formData.id)
            this.close()
            console.log('Xóa nhóm hàng thành công!')
          }
        } catch (error) {
          console.error('Lỗi khi xóa nhóm hàng:', error)
          alert('Có lỗi xảy ra khi xóa nhóm hàng!')
        }
      }
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
    },
    
    resetForm() {
      this.formData = {
        id: null,
        name: '',
        parentId: null
      }
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
