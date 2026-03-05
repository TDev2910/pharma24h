<template>
  <Dialog :visible="visible" @update:visible="$emit('close')" header="Thêm khách hàng mới" :style="{ width: '690px' }"
    modal :closable="true">
    <div class="flex gap-6">
      <!-- Left Section: Form Fields Grid -->
      <div class="form-grid" style="flex: 1;">
        <!-- Tên khách hàng -->
        <div class="form-field">
          <label for="name" class="field-label">Tên khách hàng *</label>
          <InputText id="name" v-model="form.name" type="text" placeholder="Nhập tên khách hàng" class="field-input"
            :class="{ 'p-invalid': form.errors.name }" />
          <small v-if="form.errors.name" class="p-error">{{ form.errors.name }}</small>
        </div>

        <!-- Email -->
        <div class="form-field">
          <label for="email" class="field-label">Email *</label>
          <InputText id="email" v-model="form.email" type="email" placeholder="email@gmail.com" class="field-input"
            :class="{ 'p-invalid': form.errors.email }" />
          <small v-if="form.errors.email" class="p-error">{{ form.errors.email }}</small>
        </div>

        <!-- Mật khẩu -->
        <div class="form-field">
          <label for="password" class="field-label">Mật khẩu *</label>
          <InputText id="password" v-model="form.password" type="password" placeholder="Nhập mật khẩu"
            class="field-input" :class="{ 'p-invalid': form.errors.password }" />
          <small v-if="form.errors.password" class="p-error">{{ form.errors.password }}</small>
        </div>

        <!-- Xác nhận mật khẩu -->
        <div class="form-field">
          <label for="password_confirmation" class="field-label">Xác nhận mật khẩu *</label>
          <InputText id="password_confirmation" v-model="form.password_confirmation" type="password"
            placeholder="Nhập lại mật khẩu" class="field-input"
            :class="{ 'p-invalid': form.errors.password_confirmation }" />
          <small v-if="form.errors.password_confirmation" class="p-error">{{ form.errors.password_confirmation
            }}</small>
        </div>

        <!-- Số điện thoại -->
        <div class="form-field">
          <label for="phone" class="field-label">Số điện thoại</label>
          <InputText id="phone" v-model="form.phone" type="tel" placeholder="Nhập số điện thoại" class="field-input"
            :class="{ 'p-invalid': form.errors.phone }" />
          <small v-if="form.errors.phone" class="p-error">{{ form.errors.phone }}</small>
        </div>

        <!-- Địa chỉ -->
        <div class="form-field">
          <label for="address" class="field-label">Địa chỉ</label>
          <InputText id="address" v-model="form.address" type="text" placeholder="Nhập địa chỉ" class="field-input"
            :class="{ 'p-invalid': form.errors.address }" />
          <small v-if="form.errors.address" class="p-error">{{ form.errors.address }}</small>
        </div>

        <!-- Địa chỉ chi tiết Section -->
        <div class="address-section">
          <div class="address-container">
            <div class="address-header">
              <span class="address-title">Địa chỉ chi tiết (tùy chọn)</span>
            </div>

            <!-- Tỉnh/Thành phố, Quận/Huyện, Xã/Phường -->
            <div class="address-row">
              <!-- Tỉnh/Thành phố -->
              <div class="form-field">
                <label for="province" class="field-label">Tỉnh/Thành phố</label>
                <Dropdown id="province" v-model="form.province" :options="provinceOptions" optionLabel="name"
                  placeholder="-- Chọn tỉnh/thành phố --" class="field-input"
                  :class="{ 'p-invalid': form.errors.province }" style="width: 185px;" @change="onProvinceChange" />
                <small v-if="form.errors.province" class="p-error">{{ form.errors.province }}</small>
              </div>

              <!-- Quận/Huyện -->
              <div class="form-field">
                <label for="district" class="field-label">Quận/Huyện</label>
                <Dropdown id="district" v-model="form.district" :options="districtOptions" optionLabel="name"
                  placeholder="-- Chọn quận/huyện --" class="field-input" :class="{ 'p-invalid': form.errors.district }"
                  :disabled="!form.province" style="width: 185px;" @change="onDistrictChange" />
                <small v-if="form.errors.district" class="p-error">{{ form.errors.district }}</small>
              </div>

              <!-- Xã/Phường -->
              <div class="form-field">
                <label for="ward" class="field-label">Xã/Phường</label>
                <Dropdown id="ward" v-model="form.ward" :options="wardOptions" optionLabel="name"
                  placeholder="-- Chọn xã/phường --" class="field-input" :class="{ 'p-invalid': form.errors.ward }"
                  :disabled="!form.district" style="width: 185px;" />
                <small v-if="form.errors.ward" class="p-error">{{ form.errors.ward }}</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <template #footer>
      <div class="flex justify-end gap-2">
        <Button type="button" label="Hủy" severity="secondary" @click="closeModal" />
        <Button type="button" label="Lưu khách hàng" @click="saveCustomer" :loading="form.processing" />
      </div>
    </template>
  </Dialog>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useToast } from 'primevue/usetoast'
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Dropdown from 'primevue/dropdown'

const props = defineProps({
  visible: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'created'])
const toast = useToast()

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  phone: '',
  address: '',
  province: null,
  district: null,
  ward: null
})

const provinceOptions = ref([])
const districtOptions = ref([])
const wardOptions = ref([])

const fetchWithFallback = async (url) => {
  try {
    const response = await fetch(url)
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`)
    return await response.json()
  } catch (error) {
    if (error.message.includes('CERT') || error.message.includes('Failed to fetch') || error.name === 'TypeError') {
      const httpUrl = url.replace('https://', 'http://')
      try {
        const response = await fetch(httpUrl)
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`)
        return await response.json()
      } catch (fallbackError) {
        throw new Error(`Cả HTTPS và HTTP đều thất bại: ${fallbackError.message}`)
      }
    }
    throw error
  }
}

const loadProvinces = async () => {
  if (provinceOptions.value.length > 0) return
  try {
    if (window.provinceService) {
      const provinces = await window.provinceService.loadProvinces()
      provinceOptions.value = provinces.map(p => ({ name: p.name, code: p.code }))
    } else {
      const data = await fetchWithFallback('https://provinces.open-api.vn/api/?depth=1')
      provinceOptions.value = data.map(p => ({ name: p.name, code: p.code }))
    }
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Lỗi', detail: 'Không thể tải danh sách tỉnh thành', life: 3000 })
  }
}

const onProvinceChange = async () => {
  districtOptions.value = []
  wardOptions.value = []
  form.district = null
  form.ward = null
  if (!form.province?.code) return

  try {
    const data = await fetchWithFallback(`https://provinces.open-api.vn/api/p/${form.province.code}?depth=2`)
    districtOptions.value = data.districts.map(d => ({ name: d.name, code: d.code }))
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Lỗi', detail: 'Không thể tải danh sách quận/huyện', life: 3000 })
  }
}

const onDistrictChange = async () => {
  wardOptions.value = []
  form.ward = null
  if (!form.district?.code) return

  try {
    const data = await fetchWithFallback(`https://provinces.open-api.vn/api/d/${form.district.code}?depth=2`)
    wardOptions.value = data.wards.map(w => ({ name: w.name, code: w.code }))
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Lỗi', detail: 'Không thể tải danh sách xã/phường', life: 3000 })
  }
}

watch(() => props.visible, (newVal) => {
  if (newVal) loadProvinces()
})

const closeModal = () => {
  form.reset()
  form.clearErrors()
  emit('close')
}

const saveCustomer = () => {
  form.transform((data) => ({
    ...data,
    province: data.province?.name || null,
    district: data.district?.name || null,
    ward: data.ward?.name || null
  })).post('/admin/customers', {
    preserveScroll: true,
    onSuccess: (page) => {
      toast.add({ severity: 'success', summary: 'Thành công', detail: page.props.flash?.success || 'Thêm khách hàng thành công!', life: 3000 })
      emit('created')
      closeModal()
    },
    onError: () => {
      toast.add({ severity: 'error', summary: 'Lỗi validation', detail: 'Vui lòng kiểm tra lại thông tin nhập vào', life: 5000 })
    }
  })
}
</script>

<style scoped>
/* Form Grid Layout */
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px 20px;
  align-items: start;
}

.form-field {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

/* Address Section */
.address-section {
  grid-column: 1 / -1;
  /* Span across both columns */
  margin-top: 8px;
}

.address-container {
  border: 1px solid #e9ecef;
  border-radius: 8px;
  padding: 16px;
  background-color: #fff;
}

.address-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  padding-bottom: 8px;
  border-bottom: 1px solid #f1f3f4;
}

.address-title {
  font-weight: 600;
  font-size: 16px;
  color: #333;
}

.address-icon {
  color: #6c757d;
  font-size: 14px;
}

.address-input {
  margin-bottom: 16px;
}

.address-row {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 16px 12px;
}


/* Custom placeholder styling for dropdown */
:deep(.p-select-label) {
  margin-top: -5px !important;
  margin-left: -5px !important;
  line-height: 1.2 !important;
}

/* Hoặc có thể dùng cách này */
:deep(.p-placeholder) {
  margin-top: -9px !important;
  margin-left: -9px !important;
  line-height: 1.2 !important;
  font-size: 15px;
}

.field-label {
  font-weight: 600;
  font-size: 15px;
  color: #333;
  margin-bottom: 4px;
}

.field-input {
  width: 100%;
  height: 32px !important;
  border-radius: 6px !important;
  font-size: 15px !important;
  padding: 6px 10px !important;
  border: 1px solid #ced4da !important;
}

.readonly-input {
  background-color: #f8f9fa !important;
  border-color: #e9ecef !important;
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

/* Responsive */
@media (max-width: 768px) {
  .form-grid {
    grid-template-columns: 1fr;
  }

  .email-field {
    grid-column: 1;
  }
}
</style>