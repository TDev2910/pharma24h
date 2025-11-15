<template>
  <div class="modal-backdrop" @click.self="closeModal">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h3 class="modal-title">Vật tư y tế</h3>
        <button class="close-header-btn" @click="closeModal">
          <i class="pi pi-times"></i>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        <div class="form-container">
          <!-- Mã hàng -->
          <div class="form-field">
            <label class="field-label">Mã hàng</label>
            <div class="input-group">
              <InputText v-model="formData.ma_hang" placeholder="Tự động" readonly class="field-input readonly-input" />
              <Button label="Tạo mã" icon="pi pi-refresh" @click="generateGoodsCode" severity="secondary"
                size="small" />
            </div>
          </div>

          <!-- Mã vạch -->
          <div class="form-field">
            <label class="field-label">Mã vạch</label>
            <div class="input-group">
              <InputText v-model="formData.ma_vach" placeholder="Tự động" readonly class="field-input readonly-input" />
              <Button label="Tạo mã" icon="pi pi-refresh" @click="generateGoodsBarcode" severity="secondary"
                size="small" />
            </div>
          </div>

          <!-- Tên hàng -->
          <div class="form-field">
            <label class="field-label">Tên hàng hóa <span class="text-danger">*</span></label>
            <InputText v-model="formData.ten_hang_hoa" placeholder="Nhập tên hàng hóa" class="field-input"
              :class="{ 'p-invalid': errors.ten_hang_hoa }" />
            <small v-if="errors.ten_hang_hoa" class="p-error">{{ errors.ten_hang_hoa[0] }}</small>
          </div>

          <!-- Giá vốn -->
          <div class="form-field">
            <label class="field-label">Giá vốn <span class="text-danger">*</span></label>
            <InputText v-model.number="formData.gia_von" type="number" :min="0" placeholder="Nhập giá vốn"
              class="field-input" :class="{ 'p-invalid': errors.gia_von }" />
            <small v-if="errors.gia_von" class="p-error">{{ errors.gia_von[0] }}</small>
          </div>

          <!-- Tồn kho -->
          <div class="form-field">
            <label class="field-label">Tồn kho</label>
            <InputText v-model="formData.ton_kho" placeholder="0" readonly class="field-input readonly-input" />
            <small class="text-muted">Số lượng hiện có trong kho</small>
          </div>

          <!-- Định mức tồn thấp nhất -->
          <div class="form-field">
            <label class="field-label">Định mức tồn thấp nhất</label>
            <InputText v-model="formData.ton_thap_nhat" placeholder="Nhập số lượng" class="field-input" />
            <small class="text-muted">Cảnh báo khi ≤ số này</small>
          </div>

          <!-- Định mức tồn cao nhất -->
          <div class="form-field">
            <label class="field-label">Định mức tồn cao nhất</label>
            <InputText v-model="formData.ton_cao_nhat" placeholder="Nhập số lượng" class="field-input" />
            <small class="text-muted">Cảnh báo khi ≥ số này</small>
          </div>

          <!-- Thông báo lỗi -->
          <div v-if="Object.keys(errors).length > 0" class="error-messages">
            <div class="error-title">Vui lòng kiểm tra lại thông tin:</div>
            <ul class="error-list">
              <li v-for="(errorMessages, field) in errors" :key="field" class="error-item">
                {{ errorMessages[0] }}
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="modal-footer">
        <Button type="button" label="Hủy" severity="secondary" @click="closeModal" />
        <Button type="button" label="Lưu vật tư y tế" @click="saveGoods" :loading="loading" />
      </div>
    </div>
  </div>
  <Toast />
</template>

<script>
import { useToast } from 'primevue/usetoast'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Toast from 'primevue/toast'
import axios from 'axios'

export default {
  name: 'CreateGoods',
  components: {
    Button,
    InputText,
    Toast
  },
  props: {
    visible: {
      type: Boolean,
      default: false
    }
  },
  emits: ['close', 'created'],
  setup() {
    const toast = useToast()
    return { toast }
  },
  data() {
    return {
      loading: false,
      formData: {
        ma_hang: '',
        ma_vach: '',
        ten_hang_hoa: '',
        gia_von: 0,
        gia_ban: 0, // Required by backend
        nhom_hang_id: 1, // Required by backend - set default category ID
        quy_cach_dong_goi: 'Hộp', // Required by backend - set default value
        ton_kho: '0',
        ton_thap_nhat: '',
        ton_cao_nhat: ''
      },
      errors: {}
    }
  },
  watch: {
    visible(newVal) {
      if (newVal) {
        // Reset form khi modal mở
        this.resetForm()
      }
    }
  },

  methods: {
    closeModal() {
      this.resetForm()
      this.$emit('close')
    },

    async saveGoods(event) {
      if (event) {
        event.preventDefault()
        event.stopPropagation()
      }

      this.loading = true
      this.errors = {}

      try {
        const formData = new FormData()

        // Append các trường cần thiết
        if (this.formData.ma_hang) formData.append('ma_hang', this.formData.ma_hang)
        if (this.formData.ma_vach) formData.append('ma_vach', this.formData.ma_vach)
        if (this.formData.ten_hang_hoa) formData.append('ten_hang_hoa', this.formData.ten_hang_hoa)
        if (this.formData.gia_von) formData.append('gia_von', this.formData.gia_von)
        // Required fields
        formData.append('gia_ban', this.formData.gia_ban || 0)
        formData.append('nhom_hang_id', this.formData.nhom_hang_id || 1)
        formData.append('quy_cach_dong_goi', this.formData.quy_cach_dong_goi || 'Hộp')
        if (this.formData.ton_kho) formData.append('ton_kho', this.formData.ton_kho)
        if (this.formData.ton_thap_nhat) formData.append('ton_thap_nhat', this.formData.ton_thap_nhat)
        if (this.formData.ton_cao_nhat) formData.append('ton_cao_nhat', this.formData.ton_cao_nhat)

        const response = await axios.post('/admin/goods', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })

        if (response.data.success) {
          this.toast.add({
            severity: 'success',
            summary: 'Thành công',
            detail: response.data.message,
            life: 3000
          })

          this.$emit('created', response.data.data)
          this.closeModal()
        }
      } catch (error) {
        console.error('Error creating goods:', error)

        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors
          this.toast.add({
            severity: 'error',
            summary: 'Lỗi validation',
            detail: 'Vui lòng kiểm tra lại thông tin nhập vào',
            life: 5000
          })
        } else {
          this.toast.add({
            severity: 'error',
            summary: 'Lỗi',
            detail: error.response?.data?.message || 'Có lỗi xảy ra khi thêm vật tư y tế',
            life: 5000
          })
        }
      } finally {
        this.loading = false
      }
    },

    async generateGoodsCode() {
      try {
        const response = await axios.get('/admin/goods/generate-codes')
        if (response.data.ma_hang) {
          this.formData.ma_hang = response.data.ma_hang
        }
      } catch (error) {
        console.error('Error generating goods code:', error)
      }
    },

    async generateGoodsBarcode() {
      try {
        const response = await axios.get('/admin/goods/generate-codes')
        if (response.data.ma_vach) {
          this.formData.ma_vach = response.data.ma_vach
        }
      } catch (error) {
        console.error('Error generating goods barcode:', error)
      }
    },

    resetForm() {
      this.formData = {
        ma_hang: '',
        ma_vach: '',
        ten_hang_hoa: '',
        gia_von: 0,
        gia_ban: 0, // Required by backend
        nhom_hang_id: 1, // Required by backend - set default category ID
        quy_cach_dong_goi: 'Hộp', // Required by backend - set default value
        ton_kho: '0',
        ton_thap_nhat: '',
        ton_cao_nhat: ''
      }
      this.errors = {}
    }
  }
}
</script>

<style scoped>
/* Modal Backdrop */
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.modal-content {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.15);
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  border-bottom: 1px solid #e9ecef;
  background: #f8f9fa;
}

.modal-title {
  font-weight: 600;
  font-size: 18px;
  color: #2c3e50;
  margin: 0;
}

.close-header-btn {
  background: none;
  border: none;
  font-size: 20px;
  color: #6c757d;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 4px;
  transition: all 0.2s ease;
}

.close-header-btn:hover {
  background: #e9ecef;
  color: #495057;
}

.modal-body {
  padding: 24px;
  overflow-y: auto;
  flex: 1;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding: 20px 24px;
  border-top: 1px solid #e9ecef;
  background: #fff;
}

/* Form Container */
.form-container {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-field {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

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

.readonly-input {
  background-color: #f8f9fa !important;
  border-color: #e9ecef !important;
}

.input-group {
  display: flex;
  gap: 8px;
}

.input-group .field-input {
  flex: 1;
}

.input-group .p-button {
  white-space: nowrap;
}

/* Price Input */
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

/* Error styling */
.p-error {
  color: #e24c4c;
  font-size: 12px;
  margin-top: 4px;
}

.p-invalid {
  border-color: #e24c4c !important;
}

.text-danger {
  color: #dc3545;
}

.text-muted {
  color: #6c757d;
  font-size: 12px;
}

/* Error Messages */
.error-messages {
  margin-top: 10px;
  padding: 16px;
  background: #fff3cd;
  border: 1px solid #ffc107;
  border-radius: 6px;
}

.error-title {
  font-weight: 600;
  color: #856404;
  margin-bottom: 8px;
}

.error-list {
  margin: 0;
  padding-left: 20px;
}

.error-item {
  color: #856404;
  margin-bottom: 4px;
}

/* Responsive */
@media (max-width: 768px) {
  .modal-content {
    width: 95%;
    max-height: 95vh;
  }
}
</style>
