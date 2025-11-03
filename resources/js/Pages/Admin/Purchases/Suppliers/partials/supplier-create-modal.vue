<template>
  <Dialog v-model:visible="dialogVisible" header="Thêm Nhà Cung Cấp Mới" :style="{ width: '900px' }" :modal="true"
    :closable="true" @hide="resetForm">
    <form @submit.prevent="handleSubmit">
      <div class="modal-body">
        <div class="row">
          <!-- Cột trái -->
          <div class="col-md-6">
            <!-- Tên nhà cung cấp -->
            <div class="mb-3">
              <label for="ten_nha_cung_cap" class="form-label">
                Tên nhà cung cấp <span class="text-danger">*</span>
              </label>
              <InputText id="ten_nha_cung_cap" v-model="form.ten_nha_cung_cap" class="w-100"
                :class="{ 'p-invalid': errors.ten_nha_cung_cap }" placeholder="Nhập tên nhà cung cấp" maxlength="255" />
              <small v-if="errors.ten_nha_cung_cap" class="p-error">{{ errors.ten_nha_cung_cap[0] }}</small>
            </div>

            <!-- Mã nhà cung cấp -->
            <div class="mb-3">
              <label for="ma_nha_cung_cap" class="form-label">
                Mã nhà cung cấp <span class="text-danger">*</span>
              </label>
              <InputText id="ma_nha_cung_cap" v-model="form.ma_nha_cung_cap" class="w-100"
                :class="{ 'p-invalid': errors.ma_nha_cung_cap }" placeholder="VD: NCC001" maxlength="50" />
              <small class="form-text text-muted">Mã định danh duy nhất trong hệ thống</small>
              <small v-if="errors.ma_nha_cung_cap" class="p-error">{{ errors.ma_nha_cung_cap[0] }}</small>
            </div>

            <!-- Điện thoại -->
            <div class="mb-3">
              <label for="dien_thoai" class="form-label">
                Điện thoại <span class="text-danger">*</span>
              </label>
              <InputText id="dien_thoai" v-model="form.dien_thoai" class="w-100"
                :class="{ 'p-invalid': errors.dien_thoai }" placeholder="0123 456 789" maxlength="20" />
              <small v-if="errors.dien_thoai" class="p-error">{{ errors.dien_thoai[0] }}</small>
            </div>

            <!-- Email -->
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <InputText id="email" v-model="form.email" type="email" class="w-100"
                :class="{ 'p-invalid': errors.email }" placeholder="supplier@example.com" maxlength="100" />
              <small v-if="errors.email" class="p-error">{{ errors.email[0] }}</small>
            </div>

            <!-- Nhóm nhà cung cấp -->
            <div class="mb-3">
              <label for="nhom_nha_cung_cap_id" class="form-label">
                Nhóm nhà cung cấp <span class="text-danger">*</span>
              </label>
              <Dropdown id="nhom_nha_cung_cap_id" v-model="form.nhom_nha_cung_cap_id" :options="supplierGroups"
                optionLabel="name" optionValue="id" placeholder="-- Chọn nhóm --" class="w-100"
                :class="{ 'p-invalid': errors.nhom_nha_cung_cap_id }" :filter="true" />
              <small v-if="errors.nhom_nha_cung_cap_id" class="p-error">{{ errors.nhom_nha_cung_cap_id[0] }}</small>
            </div>
          </div>

          <!-- Cột phải -->
          <div class="col-md-6">
            <!-- Địa chỉ chi tiết -->
            <div class="mb-3">
              <label for="dia_chi" class="form-label">
                Địa chỉ chi tiết <span class="text-danger">*</span>
              </label>
              <Textarea id="dia_chi" v-model="form.dia_chi" class="w-100" :class="{ 'p-invalid': errors.dia_chi }"
                placeholder="Số nhà, đường, phố..." rows="2" />
              <small v-if="errors.dia_chi" class="p-error">{{ errors.dia_chi[0] }}</small>
            </div>

            <!-- Tỉnh/Thành phố và Quận/Huyện -->
            <!-- Tỉnh/Thành phố -->
            <div class="form-field">
              <label for="province" class="field-label">Tỉnh/Thành phố *</label>
              <Dropdown id="province" v-model="formData.province" :options="provinceOptions" optionLabel="name"
                placeholder="-- Chọn tỉnh/thành --" class="field-input" :class="{ 'p-invalid': errors.province }"
                style="width: 100% !important;" :loading="loadingProvinces" />
              <small v-if="errors.province" class="p-error">{{ errors.province[0] }}</small>
            </div>
            <!-- Quận/Huyện -->
            <div class="form-field">
              <label for="ward" class="field-label">Quận/Huyện *</label>
              <Dropdown id="ward" v-model="formData.ward" :options="wardOptions" optionLabel="name"
                placeholder="-- Chọn tỉnh/thành trước --" class="field-input" :class="{ 'p-invalid': errors.ward }"
                style="width: 100% !important;" :disabled="!formData.province || loadingWards"
                :loading="loadingWards" />
              <small v-if="errors.ward" class="p-error">{{ errors.ward[0] }}</small>
            </div>

            <!-- Tên công ty -->
            <div class="mb-3">
              <label for="ten_cong_ty" class="form-label">Tên công ty (xuất hóa đơn)</label>
              <InputText id="ten_cong_ty" v-model="form.ten_cong_ty" class="w-100"
                :class="{ 'p-invalid': errors.ten_cong_ty }" placeholder="Công ty TNHH ABC" maxlength="255" />
              <small class="form-text text-muted">Tên chính thức cho việc xuất hóa đơn</small>
              <small v-if="errors.ten_cong_ty" class="p-error">{{ errors.ten_cong_ty[0] }}</small>
            </div>

            <!-- Mã số thuế -->
            <div class="mb-3">
              <label for="ma_so_thue" class="form-label">Mã số thuế</label>
              <InputText id="ma_so_thue" v-model="form.ma_so_thue" class="w-100"
                :class="{ 'p-invalid': errors.ma_so_thue }" placeholder="0123456789" maxlength="20" />
              <small class="form-text text-muted">MST duy nhất theo pháp luật</small>
              <small v-if="errors.ma_so_thue" class="p-error">{{ errors.ma_so_thue[0] }}</small>
            </div>
          </div>
        </div>

        <!-- Ghi chú -->
        <div class="row">
          <div class="col-12">
            <div class="mb-3">
              <label for="ghi_chu" class="form-label">Ghi chú</label>
              <Textarea id="ghi_chu" v-model="form.ghi_chu" class="w-100"
                placeholder="Ghi chú bổ sung về nhà cung cấp..." rows="3" />
            </div>
          </div>
        </div>

        <!-- Trạng thái -->
        <div class="row">
          <div class="col-12">
            <div class="form-check mb-3">
              <Checkbox id="trang_thai" v-model="form.trang_thai" inputId="trang_thai" binary />
              <label for="trang_thai" class="form-check-label ms-2">
                Kích hoạt ngay
              </label>
            </div>
          </div>
        </div>
      </div>
    </form>

    <template #footer>
      <div class="flex justify-content-end gap-2">
        <Button type="button" label="Hủy" severity="secondary" @click="closeModal" />
        <Button type="button" label="Lưu nhà cung cấp" :loading="submitting" icon="pi pi-save" @click="handleSubmit" />
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
import Checkbox from 'primevue/checkbox'
import { useToast } from 'primevue/usetoast'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

export default {
  name: 'SupplierCreateModal',
  components: {
    Dialog,
    Button,
    InputText,
    Textarea,
    Dropdown,
    Checkbox
  },

  props: {
    visible: {
      type: Boolean,
      default: false
    },
    supplierGroups: {
      type: Array,
      default: () => []
    }
  },

  emits: ['update:visible', 'supplier-created'],

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
        ten_nha_cung_cap: '',
        ma_nha_cung_cap: '',
        dien_thoai: '',
        email: '',
        nhom_nha_cung_cap_id: null,
        dia_chi: '',
        ten_cong_ty: '',
        ma_so_thue: '',
        ghi_chu: '',
        trang_thai: true
      },
      formData: {
        province: null,
        ward: null
      },
      submitting: false,
      errors: {},
      provinceOptions: [],
      wardOptions: [],
      loadingProvinces: false,
      loadingWards: false
    }
  },

  watch: {
    visible(newVal) {
      this.dialogVisible = newVal
      if (newVal) {
        this.loadProvinces()
        // Reset wards khi mở modal
        this.wardOptions = []
        this.formData.ward = null
        this.formData.province = null
      }
    },
    dialogVisible(newVal) {
      if (!newVal) {
        this.$emit('update:visible', false)
      }
    },
    'formData.province'(newProvince) {
      if (newProvince && newProvince.code) {
        this.onProvinceChange(newProvince.code)
      } else {
        // Reset wards nếu không có province
        this.wardOptions = []
        this.formData.ward = null
      }
    }
  },

  methods: {
    async loadProvinces() {
      if (this.provinceOptions.length > 0) return // Đã load rồi

      this.loadingProvinces = true
      try {
        // Sử dụng shared ProvinceService nếu có
        if (window.provinceService) {
          const provinces = await window.provinceService.loadProvinces()
          this.provinceOptions = provinces.map(province => ({
            name: province.name,
            code: province.code
          }))
        } else {
          // Fallback: gọi API trực tiếp
          const response = await fetch('https://provinces.open-api.vn/api/?depth=1')
          const data = await response.json()

          this.provinceOptions = data.map(province => ({
            name: province.name,
            code: province.code
          }))
        }
      } catch (error) {
        console.error('Error loading provinces:', error)
        this.toast.add({
          severity: 'error',
          summary: 'Lỗi',
          detail: 'Không thể tải danh sách tỉnh/thành',
          life: 3000
        })
      } finally {
        this.loadingProvinces = false
      }
    },

    async onProvinceChange(provinceCode) {
      if (!provinceCode) {
        this.wardOptions = []
        this.formData.ward = null
        return
      }

      this.loadingWards = true
      this.wardOptions = []
      this.formData.ward = null

      try {
        // Sử dụng shared ProvinceService nếu có
        if (window.provinceService) {
          const wards = await window.provinceService.loadWards(provinceCode)
          this.wardOptions = wards.map(ward => ({
            name: ward.name,
            code: ward.code
          }))
        } else {
          // Fallback: gọi API trực tiếp
          const response = await fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=3`)
          const data = await response.json()

          // Collect all wards from all districts in the province
          let allWards = []
          if (data.districts && Array.isArray(data.districts)) {
            data.districts.forEach(district => {
              if (district.wards && Array.isArray(district.wards)) {
                district.wards.forEach(ward => {
                  allWards.push({
                    name: `${ward.name} (${district.name})`,
                    code: ward.code
                  })
                })
              }
            })
          }

          // Sort wards by name
          allWards.sort((a, b) => a.name.localeCompare(b.name))

          this.wardOptions = allWards
        }

        // Reset ward
        this.formData.ward = null
      } catch (error) {
        console.error('Error loading wards:', error)
        this.toast.add({
          severity: 'error',
          summary: 'Lỗi',
          detail: 'Không thể tải danh sách quận/huyện',
          life: 3000
        })
      } finally {
        this.loadingWards = false
      }
    },

    validateForm() {
      this.errors = {}
      let isValid = true

      // Required fields
      if (!this.form.ten_nha_cung_cap?.trim()) {
        this.errors.ten_nha_cung_cap = ['Trường này là bắt buộc']
        isValid = false
      }

      if (!this.form.ma_nha_cung_cap?.trim()) {
        this.errors.ma_nha_cung_cap = ['Trường này là bắt buộc']
        isValid = false
      }

      if (!this.form.dien_thoai?.trim()) {
        this.errors.dien_thoai = ['Trường này là bắt buộc']
        isValid = false
      }

      if (!this.form.dia_chi?.trim()) {
        this.errors.dia_chi = ['Trường này là bắt buộc']
        isValid = false
      }

      if (!this.form.nhom_nha_cung_cap_id) {
        this.errors.nhom_nha_cung_cap_id = ['Trường này là bắt buộc']
        isValid = false
      }

      if (!this.formData.province) {
        this.errors.province = ['Trường này là bắt buộc']
        isValid = false
      }

      if (!this.formData.ward) {
        this.errors.ward = ['Trường này là bắt buộc']
        isValid = false
      }

      // Phone validation
      if (this.form.dien_thoai) {
        const phonePattern = /^[0-9+\-\s\(\)\.]{10,20}$/
        if (!phonePattern.test(this.form.dien_thoai)) {
          this.errors.dien_thoai = ['Định dạng điện thoại không hợp lệ']
          isValid = false
        }
      }

      // Email validation
      if (this.form.email) {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
        if (!emailPattern.test(this.form.email)) {
          this.errors.email = ['Định dạng email không hợp lệ']
          isValid = false
        }
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
        ten_nha_cung_cap: this.form.ten_nha_cung_cap,
        ma_nha_cung_cap: this.form.ma_nha_cung_cap,
        dien_thoai: this.form.dien_thoai,
        email: this.form.email || null,
        nhom_nha_cung_cap_id: this.form.nhom_nha_cung_cap_id,
        dia_chi: this.form.dia_chi,
        khu_vuc: this.formData.province ? this.formData.province.name : null,
        phuong_xa: this.formData.ward ? this.formData.ward.name : null,
        ten_cong_ty: this.form.ten_cong_ty || null,
        ma_so_thue: this.form.ma_so_thue || null,
        ghi_chu: this.form.ghi_chu || null,
        trang_thai: this.form.trang_thai ? 'active' : 'inactive'
      }

      try {
        router.post('/admin/suppliers', submitData, {
          preserveScroll: true,
          onSuccess: (page) => {
            this.toast.add({
              severity: 'success',
              summary: 'Thành công',
              detail: 'Tạo nhà cung cấp thành công!',
              life: 3000
            })

            this.resetForm()
            this.closeModal()
            // Emit event với page props để Dashboard có thể access suppliers mới
            this.$emit('supplier-created', page?.props)
          },
          onError: (errors) => {
            this.errors = errors
            this.toast.add({
              severity: 'error',
              summary: 'Lỗi',
              detail: 'Có lỗi xảy ra khi tạo nhà cung cấp',
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
        ten_nha_cung_cap: '',
        ma_nha_cung_cap: '',
        dien_thoai: '',
        email: '',
        nhom_nha_cung_cap_id: null,
        dia_chi: '',
        ten_cong_ty: '',
        ma_so_thue: '',
        ghi_chu: '',
        trang_thai: true
      }
      this.formData = {
        province: null,
        ward: null
      }
      this.errors = {}
      this.wardOptions = []
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

.form-text {
  font-size: 0.875rem;
  margin-top: 0.25rem;
}

.text-danger {
  color: #dc3545;
}

.text-muted {
  color: #6c757d;
}

.form-check {
  display: flex;
  align-items: center;
}

.form-check-label {
  margin-left: 0.5rem;
  cursor: pointer;
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

/* Address Row Styles */
.address-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px 12px;
  margin-top: 8px;
}

.form-field {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.field-label {
  font-weight: 600;
  font-size: 15px;
  color: #333;
  margin-bottom: 4px;
}

.field-input {
  width: 100%;
}
</style>
