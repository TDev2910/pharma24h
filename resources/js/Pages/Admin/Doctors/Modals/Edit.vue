<template>
  <Dialog :visible="visible" @update:visible="$emit('close')" header="Sửa Thông Tin Bác sĩ" :style="{ width: '800px' }"
    modal :closable="true">
    <div class="flex gap-6">
      <!-- Left Section: Form Fields Grid -->
      <div class="form-grid" style="flex: 1;">
        <!-- Tên bác sĩ -->
        <div class="form-field">
          <label for="name" class="field-label">Tên bác sĩ</label>
          <InputText id="name" v-model="form.name" type="text" placeholder="Bắt buộc" class="field-input"
            :class="{ 'p-invalid': form.errors.name }" />
          <small v-if="form.errors.name" class="p-error">{{ form.errors.name }}</small>
        </div>

        <!-- Mã bác sĩ -->
        <div class="form-field">
          <label for="doctorCode" class="field-label" style="margin-left: -145px;">Mã bác sĩ</label>
          <InputText id="doctorCode" v-model="form.doctorCode" type="text" placeholder="Tự động" readonly
            class="field-input readonly-input" :class="{ 'p-invalid': form.errors.doctorCode }"
            style="margin-left: -145px;" />
          <small v-if="form.errors.doctorCode" class="p-error">{{ form.errors.doctorCode }}</small>
        </div>
        <!-- Right Section: Tải ảnh -->
        <div class="upload-section">
          <div class="image-upload-container" style="margin-left: 1100px;margin-top: -155px;">
            <div class="image-upload-circle" @click="handleImageUpload"> <!-- Tạo hình tròn -->
              <!-- Hiển thị nút upload khi không có ảnh -->
              <div v-if="!imagePreview" class="upload-content"> <!-- Hiển thị nút upload -->
                <Button label="Thêm ảnh" severity="secondary" class="upload-btn" @click.stop="handleImageUpload" />
              </div>

              <!-- Hiển thị ảnh preview khi đã có ảnh -->
              <div v-if="imagePreview" class="image-preview-content">
                <img :src="imagePreview" alt="Preview" class="preview-image" />
                <div class="image-overlay">
                  <Button label="Xóa" @click.stop="removeImage" size="small" severity="danger" class="remove-btn" />
                </div>
              </div>
            </div>
            <p class="upload-text">Ảnh không được vượt quá 2MB</p>
          </div>
        </div>

        <!-- Giới tính -->
        <div class="form-field">
          <label for="gender" class="field-label" style="margin-left: -380px;">Giới tính</label>
          <Dropdown id="gender" v-model="form.gender" :options="genderOptions" optionLabel="label" optionValue="value"
            placeholder="Chọn giới tính" class="field-input" :class="{ 'p-invalid': form.errors.gender }"
            style="margin-left: -380px;" />
          <small v-if="form.errors.gender" class="p-error">{{ form.errors.gender }}</small>
        </div>

        <!-- Điện thoại -->
        <div class="form-field">
          <label for="phone" class="field-label" style="margin-left: 235px;margin-top: -210px;">Điện thoại</label>
          <InputText id="phone" v-model="form.phone" type="tel" placeholder="Nhập số điện thoại" class="field-input"
            :class="{ 'p-invalid': form.errors.phone }" style="margin-left: 235px;margin-top: -6px;" />
          <small v-if="form.errors.phone" class="p-error">{{ form.errors.phone }}</small>
        </div>

        <!-- Email (span 2 columns) -->
        <div class="form-field email-field">
          <label for="email" class="field-label" style="margin-top:-150px;width:60%;">Email</label>
          <InputText id="email" v-model="form.email" type="email" placeholder="email@gmail.com" class="field-input"
            :class="{ 'p-invalid': form.errors.email }" style="width: 440px !important;" />
          <small v-if="form.errors.email" class="p-error">{{ form.errors.email }}</small>
        </div>

        <!-- Địa chỉ Section -->
        <div class="address-section" style="margin-top: -60px;">
          <div class="address-container">
            <!-- Địa chỉ Input -->
            <div class="form-field address-input">
              <label for="address" class="field-label">Địa chỉ</label>
              <InputText id="address" v-model="form.address" type="text" placeholder="Nhập địa chỉ" class="field-input"
                :class="{ 'p-invalid': form.errors.address }" style="width: 100% !important;" />
              <small v-if="form.errors.address" class="p-error">{{ form.errors.address }}</small>
            </div>

            <!-- Tỉnh/Thành phố và Quận/Huyện -->
            <div class="address-row">
              <!-- Tỉnh/Thành phố -->
              <div class="form-field">
                <label for="province" class="field-label">Tỉnh/Thành phố *</label>
                <Dropdown id="province" v-model="form.province" :options="provinceOptions" optionLabel="name"
                  placeholder="-- Chọn tỉnh/thành --" class="field-input" :class="{ 'p-invalid': form.errors.province }"
                  style="width: 350px !important;" />
                <small v-if="form.errors.province" class="p-error">{{ form.errors.province }}</small>
              </div>

              <!-- Quận/Huyện -->
              <div class="form-field">
                <label for="ward" class="field-label">Quận/Huyện *</label>
                <Dropdown id="ward" v-model="form.ward" :options="wardOptions" optionLabel="name"
                  placeholder="-- Chọn tỉnh/thành trước --" class="field-input"
                  :class="{ 'p-invalid': form.errors.ward }" style="width: 350px !important;"
                  :disabled="!form.province" />
                <small v-if="form.errors.ward" class="p-error">{{ form.errors.ward }}</small>
              </div>
            </div>
          </div>
        </div>
        <!-- Thông tin chuyên môn -->
        <div class="specialty-section">
          <div class="specialty-container">
            <!-- Header với icon -->
            <div class="specialty-header">
              <span class="specialty-title">Thông tin chuyên môn</span>
              <i class="pi pi-chevron-up specialty-icon"></i>
            </div>

            <!-- Chuyên khoa và Trình độ -->
            <div class="specialty-row">
              <!-- Chuyên khoa -->
              <div class="form-field">
                <label for="specialty" class="field-label">Chuyên khoa</label>
                <Dropdown id="specialty" v-model="form.specialty" :options="specialtyOptions" optionLabel="label"
                  optionValue="value" placeholder="Chọn chuyên khoa" class="field-input"
                  :class="{ 'p-invalid': form.errors.specialty }" style="width: 350px !important;" />
                <small v-if="form.errors.specialty" class="p-error">{{ form.errors.specialty }}</small>
              </div>

              <!-- Trình độ -->
              <div class="form-field">
                <label for="degree" class="field-label">Trình độ</label>
                <Dropdown id="degree" v-model="form.degree" :options="degreeOptions" optionLabel="label"
                  optionValue="value" placeholder="Chọn trình độ" class="field-input"
                  :class="{ 'p-invalid': form.errors.degree }" style="width: 350px !important;" />
                <small v-if="form.errors.degree" class="p-error">{{ form.errors.degree }}</small>
              </div>
            </div>
          </div>
        </div>

        <!-- Ghi chú -->
        <div class="notes-section">
          <div class="notes-container">
            <!-- Ghi chú Input -->
            <div class="form-field notes-input">
              <label for="notes" class="field-label">Ghi chú</label>
              <Textarea id="notes" v-model="form.notes" rows="4" placeholder="Nhập ghi chú" class="field-textarea"
                :class="{ 'p-invalid': form.errors.notes }" style="width: 100% !important;" />
              <small v-if="form.errors.notes" class="p-error">{{ form.errors.notes }}</small>
            </div>
          </div>
        </div>
      </div>
    </div>
    <template #footer>
      <div class="flex justify-end gap-2">
        <Button type="button" label="Hủy" severity="secondary" @click="closeModal" />
        <Button type="button" label="Lưu" @click="saveDoctor" :loading="form.processing" />
      </div>
    </template>
  </Dialog>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Dropdown from 'primevue/dropdown'
import Textarea from 'primevue/textarea'
import axios from 'axios'

const props = defineProps({
  visible: {
    type: Boolean,
    default: false
  },
  doctorId: {
    type: [String, Number],
    required: true
  }
})

const emit = defineEmits(['close', 'edited'])

const form = useForm({
  name: '',
  doctorCode: '',
  gender: null,
  phone: '',
  email: '',
  specialty: null,
  address: '',
  province: null,
  ward: null,
  degree: null,
  notes: '',
  avatar: null
})

const provinceOptions = ref([])
const wardOptions = ref([])
const imagePreview = ref(null)

const genderOptions = [
  { label: 'Nam', value: 'male' },
  { label: 'Nữ', value: 'female' }
]

const specialtyOptions = [
  { label: 'Nội tổng quát', value: 'Nội tổng quát' },
  { label: 'Y học dự phòng', value: 'Y học dự phòng' },
  { label: 'Tim mạch', value: 'Tim mạch' },
  { label: 'Xét nghiệm y học', value: 'Xét nghiệm y học' },
  { label: 'Khoa nhi', value: 'Khoa nhi' },
]

const degreeOptions = [
  { label: 'Đại học', value: 'Đại học' },
  { label: 'Thạc sĩ', value: 'Thạc sĩ' },
  { label: 'Tiến sĩ', value: 'Tiến sĩ' },
  { label: 'Bác sĩ chuyên khoa I', value: 'Bác sĩ chuyên khoa I' },
  { label: 'Bác sĩ chuyên khoa II', value: 'Bác sĩ chuyên khoa II' }
]

const loadDoctorData = async () => {
  if (!props.doctorId) return
  try {
    const response = await axios.get(`/admin/doctors/${props.doctorId}/edit`)
    if (response.data && response.data.success) {
      const doctor = response.data.data
      form.name = doctor.name || ''
      form.doctorCode = doctor.doctor_code || ''
      form.gender = doctor.gender?.toLowerCase() || null
      form.phone = doctor.phone || ''
      form.email = doctor.email || ''
      form.specialty = doctor.specialty || null
      form.address = doctor.address || ''
      form.province = doctor.province_district ? { name: doctor.province_district } : null
      form.ward = doctor.ward_commune ? { name: doctor.ward_commune } : null
      form.degree = doctor.qualification || null
      form.notes = doctor.note || ''
      form.avatar = doctor.avatar || null
      if (doctor.avatar) {
        imagePreview.value = doctor.avatar.startsWith('http') ? doctor.avatar : `/storage/${doctor.avatar}`
      }
    }
  } catch (error) {
    console.error('Error loading doctor data:', error)
  }
}

const closeModal = () => {
  form.reset()
  form.clearErrors()
  imagePreview.value = null
  emit('close')
}

const saveDoctor = () => {
  form.put(`/admin/doctors/${props.doctorId}`, {
    preserveScroll: true,
    onSuccess: () => {
      emit('edited')
      emit('close')
    }
  })
}

const handleImageUpload = () => {
  const input = document.createElement('input')
  input.type = 'file'
  input.accept = 'image/*'
  input.onchange = async (event) => {
    const file = event.target.files[0]
    if (!file) return
    const formData = new FormData()
    formData.append('avatar', file)
    try {
      const response = await axios.post('/admin/doctors/upload-avatar', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      if (response.data.success) {
        form.avatar = response.data.data.path
        const reader = new FileReader()
        reader.onload = (e) => imagePreview.value = e.target.result
        reader.readAsDataURL(file)
      }
    } catch (error) {
      console.error('Upload error:', error)
    }
  }
  input.click()
}

const removeImage = () => {
  form.avatar = null
  imagePreview.value = null
}

const loadProvinces = async () => {
  try {
    if (window.provinceService) {
      const provinces = await window.provinceService.loadProvinces()
      provinceOptions.value = provinces.map(p => ({ name: p.name, code: p.code }))
    } else {
      const response = await fetch('https://provinces.open-api.vn/api/?depth=1')
      const data = await response.json()
      provinceOptions.value = data.map(p => ({ name: p.name, code: p.code }))
    }
  } catch (error) {
    console.error('Error loading provinces:', error)
  }
}

const onProvinceChange = async (provinceCode) => {
  if (!provinceCode) {
    wardOptions.value = []
    form.ward = null
    return
  }
  try {
    if (window.provinceService) {
      const wards = await window.provinceService.loadWards(provinceCode)
      wardOptions.value = wards.map(w => ({ name: w.name, code: w.code }))
    } else {
      const response = await fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=3`)
      const data = await response.json()
      let allWards = []
      if (data.districts) {
        data.districts.forEach(d => {
          if (d.wards) {
            d.wards.forEach(w => allWards.push({ name: `${w.name} (${d.name})`, code: w.code }))
          }
        })
      }
      allWards.sort((a, b) => a.name.localeCompare(b.name))
      wardOptions.value = allWards
    }
    form.ward = null
  } catch (error) {
    console.error('Error loading wards:', error)
  }
}

watch(() => props.visible, (newVal) => {
  if (newVal) {
    loadDoctorData()
    loadProvinces()
  }
})

watch(() => form.province, (newProvince) => {
  if (newProvince && newProvince.code) {
    onProvinceChange(newProvince.code)
  }
})
</script>

<style scoped>
/* Form Grid Layout */
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px 12px;
  align-items: start;
}

.form-field {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.email-field {
  grid-column: 1 / -1;
  /* Span across both columns */
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
  grid-template-columns: 1fr 1fr;
  gap: 16px 12px;
}

/* Specialty Section */
.specialty-section {
  grid-column: 1 / -1;
  /* Span across both columns */
  margin-top: 8px;
}

.specialty-container {
  border: 1px solid #e9ecef;
  border-radius: 8px;
  padding: 16px;
  background-color: #fff;
}

.specialty-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  padding-bottom: 8px;
  border-bottom: 1px solid #f1f3f4;
}

.specialty-title {
  font-weight: 600;
  font-size: 16px;
  color: #333;
}

.specialty-icon {
  color: #6c757d;
  font-size: 14px;
}

.specialty-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px 12px;
}

/* Notes Section */
.notes-section {
  grid-column: 1 / -1;
  /* Span across both columns */
  margin-top: 8px;
}

.notes-container {
  border: 1px solid #e9ecef;
  border-radius: 8px;
  padding: 16px;
  background-color: #fff;
}

.notes-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  padding-bottom: 8px;
  border-bottom: 1px solid #f1f3f4;
}

.notes-title {
  font-weight: 600;
  font-size: 16px;
  color: #333;
}

.notes-icon {
  color: #6c757d;
  font-size: 14px;
}

.field-textarea {
  width: 100% !important;
  border-radius: 6px !important;
  font-size: 15px !important;
  padding: 10px !important;
  border: 1px solid #ced4da !important;
  resize: vertical;
  font-family: inherit;
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
  width: 55%;
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

/* Upload Section */
.upload-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 200px;
  min-height: 200px;
}

.image-upload-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;

}

.image-upload-circle {
  width: 180px;
  height: 180px;
  border-radius: 50%;
  background-color: #f8f9fa;
  border: 2px dashed #dee2e6;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}

.image-upload-circle:hover {
  border-color: #007bff;
  background-color: #f0f8ff;
}

.upload-content {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
}

.upload-btn {
  background: white !important;
  border: 1px solid #dee2e6 !important;
  color: #333 !important;
  border-radius: 6px !important;
  padding: 8px 16px !important;
  font-size: 14px !important;
}

.upload-text {
  font-size: 12px;
  color: #6c757d;
  margin: 0;
  text-align: center;
}

/* Image Preview Content */
.image-preview-content {
  position: relative;
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.preview-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
}

.image-overlay {
  position: absolute;
  bottom: 8px;
  left: 50%;
  transform: translateX(-50%);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.image-upload-circle:hover .image-overlay {
  opacity: 1;
}

.remove-btn {
  font-size: 12px !important;
  padding: 4px 8px !important;
  background: rgba(220, 53, 69, 0.9) !important;
  border: none !important;
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