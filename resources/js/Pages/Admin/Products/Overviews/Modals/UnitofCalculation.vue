<template>
  <Dialog 
    :visible="visible" 
    @update:visible="$emit('close')"
    header="Thêm đơn vị cơ bản" 
    :style="{ width: '500px' }"
    modal
    :closable="true"
    @hide="resetForm"
  >
    <div class="modal-body p-4">
      <p class="text-muted small mb-3">
        Đơn vị cơ bản là đơn vị bán phổ biến nhất hoặc đơn vị chính dùng để quản lý tồn kho
      </p>
      
      <div class="mb-3">
        <label for="unitName" class="form-label">Tên đơn vị cơ bản</label>
        <InputText
          v-model="formData.unitName"
          placeholder="Tên đơn vị cơ bản"
          class="field-input"
          :class="{ 'p-invalid': errors.unitName }"
        />
        <small v-if="errors.unitName" class="p-error">{{ errors.unitName[0] }}</small>
      </div>
      
      <div class="mb-3">
        <label for="unitPrice" class="form-label">Giá bán</label>
        <InputNumber
          v-model="formData.unitPrice"
          mode="currency"
          currency="VND"
          locale="vi-VN"
          :min="0"
          class="price-input"
        />
      </div>
    </div>

    <template #footer>
      <div class="flex justify-end gap-2">
        <Button 
          type="button" 
          label="Bỏ qua" 
          severity="secondary" 
          @click="closeModal"
        />
        <Button 
          type="button" 
          label="Xong" 
          @click="saveUnit"
          :loading="loading"
        />
      </div>
    </template>
  </Dialog>
</template>

<script>
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import InputNumber from 'primevue/inputnumber'
import Checkbox from 'primevue/checkbox'

export default {
  name: 'UnitOfCalculationModal',
  components: {
    Dialog,
    Button,
    InputText,
    InputNumber,
    Checkbox
  },
  props: {
    visible: {
      type: Boolean,
      default: false
    }
  },
  emits: ['close', 'saved'],
  data() {
    return {
      loading: false,
      formData: {
        unitName: '',
        unitPrice: 0,
        directSale: true
      },
      errors: {}
    }
  },
  methods: {
    closeModal() {
      this.resetForm()
      this.$emit('close')
    },
    
    saveUnit() {
      this.loading = true
      this.errors = {}
      
      // Validation
      if (!this.formData.unitName.trim()) {
        this.errors.unitName = ['Vui lòng nhập tên đơn vị cơ bản']
        this.loading = false
        return
      }
      
      // Emit data to parent
      this.$emit('saved', {
        unitName: this.formData.unitName,
        unitPrice: this.formData.unitPrice,
        directSale: this.formData.directSale
      })
      
      this.closeModal()
      this.loading = false
    },
    
    resetForm() {
      this.formData = {
        unitName: '',
        unitPrice: 0,
        directSale: true
      }
      this.errors = {}
    }
  }
}
</script>

<style scoped>
/* Field Styling */
.field-label {
  font-weight: 600;
  font-size: 14px;
  color: #333;
  margin-bottom: 4px;
}

.field-input {
  width: 100%;
  height: 38px;
  border-radius: 6px;
  font-size: 14px;
  padding: 8px 12px;
  border: 1px solid #ced4da;
}

/* Price Input Styling */
.price-input {
  width: 100%;
  height: 38px;
  border-radius: 6px;
  font-size: 14px;
  padding: 8px 12px;
  border: 1px solid #ced4da;
  background: #fff;
}

:deep(.price-input .p-inputnumber-input) {
  border: none !important;
  background: transparent !important;
  padding: 0 !important;
  height: auto !important;
  font-size: 14px !important;
  color: #333 !important;
  box-shadow: none !important;
}

:deep(.price-input .p-inputnumber-input:focus) {
  border: none !important;
  box-shadow: none !important;
  outline: none !important;
}

/* Checkbox */
.form-check {
  display: flex;
  align-items: center;
  gap: 8px;
}

.form-check-label {
  font-weight: 500;
  color: #333;
  cursor: pointer;
}

/* Error styling */
.p-error {
  color: #e24c4c;
  font-size: 12px;
  margin-top: 4px;
}

.p-invalid {
  border-color: #e24c4c !important;
}

.text-muted {
  color: #6c757d;
  font-size: 12px;
}

/* PrimeVue Dialog customization */
:deep(.p-dialog .p-dialog-header) {
  background: #f8f9fa;
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
</style>
