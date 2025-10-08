<template>
    <Dialog 
      :visible="visible" 
      @update:visible="$emit('close')"
      header="Thêm Bác sĩ" 
      :style="{ width: '800px' }"
      modal
      :closable="true"
    >
    <div class="flex gap-6">
        <!-- Left Section: Form Fields Grid -->
        <div class="form-grid" style="flex: 1;">
            <!-- Tên bác sĩ -->
            <div class="form-field">
                <label for="name" class="field-label">Tên bác sĩ</label>
                <InputText
                    id="name"
                    v-model="formData.name"
                    type="text"
                    placeholder="Bắt buộc"
                    class="field-input"
                    :class="{ 'p-invalid': errors.name }"
                />
                <small v-if="errors.name" class="p-error">{{ errors.name[0] }}</small>
            </div>

            <!-- Mã bác sĩ -->
            <div class="form-field">
                <label for="doctorCode" class="field-label" style="margin-left: -145px;">Mã bác sĩ</label>
                <InputText
                    id="doctorCode"
                    v-model="formData.doctorCode"
                    type="text" 
                    placeholder="Tự động"
                    readonly
                    class="field-input readonly-input" 
                    :class="{ 'p-invalid': errors.doctorCode }"
                    style="margin-left: -145px;"
                />
                <small v-if="errors.doctorCode" class="p-error">{{ errors.doctorCode[0] }}</small>
            </div>
              <!-- Right Section: Image Upload -->
            <div class="upload-section">
                <div class="image-upload-container"style="margin-left: 1100px;margin-top: -155px;">
                    <div class="image-upload-circle">
                        <Button 
                            label="Thêm ảnh"
                            severity="secondary"
                            class="upload-btn"
                            @click="handleImageUpload"
                        />
                    </div>
                    <p class="upload-text">Ảnh không được vượt quá 2MB</p>
                </div>
            </div>

            <!-- Giới tính -->
            <div class="form-field">
                <label for="gender" class="field-label" style="margin-left: -380px;">Giới tính</label>
                <Dropdown
                    id="gender"
                    v-model="formData.gender"
                    :options="genderOptions"
                    optionLabel="label"
                    optionValue="value"
                    placeholder= "Chọn giới tính"
                    class="field-input" 
                    :class="{ 'p-invalid': errors.gender }"
                    style="margin-left: -380px;"
                />
                <small v-if="errors.gender" class="p-error">{{ errors.gender[0] }}</small>
            </div>

            <!-- Điện thoại -->
            <div class="form-field">
                <label for="phone" class="field-label" style="margin-left: 235px;margin-top: -210px;">Điện thoại</label>
                <InputText
                    id="phone"
                    v-model="formData.phone"
                    type="tel"
                    placeholder="Nhập số điện thoại"
                    class="field-input" 
                    :class="{ 'p-invalid': errors.phone }"
                    style="margin-left: 235px;margin-top: -6px;"
                />
                <small v-if="errors.phone" class="p-error">{{ errors.phone[0] }}</small>
            </div>

            <!-- Email (span 2 columns) -->
            <div class="form-field email-field">
                <label for="email" class="field-label"style="margin-top:-150px;width:60%;">Email</label>
                <InputText
                    id="email"
                    v-model="formData.email"
                    type="email"
                    placeholder="email@gmail.com"
                    class="field-input" 
                    :class="{ 'p-invalid': errors.email }"
                    style="width: 440px !important;"
                />
                <small v-if="errors.email" class="p-error">{{ errors.email[0] }}</small>
            </div>

            <!-- Địa chỉ Section -->
            <div class="address-section" style="margin-top: -60px;">
                <div class="address-container">
                    <!-- Địa chỉ Input -->
                    <div class="form-field address-input">
                        <label for="address" class="field-label">Địa chỉ</label>
                        <InputText
                            id="address"
                            v-model="formData.address"
                            type="text"
                            placeholder="Nhập địa chỉ"
                            class="field-input" 
                            :class="{ 'p-invalid': errors.address }"
                            style="width: 100% !important;"
                        />
                        <small v-if="errors.address" class="p-error">{{ errors.address[0] }}</small>
                    </div>

                    <!-- Tỉnh/Thành phố và Quận/Huyện -->
                    <div class="address-row">
                        <!-- Tỉnh/Thành phố -->
                        <div class="form-field">
                            <label for="province" class="field-label">Tỉnh/Thành phố *</label>
                            <Dropdown
                                id="province"
                                v-model="formData.province"
                                :options="provinceOptions"
                                optionLabel="name"
                                placeholder="-- Chọn tỉnh/thành --"
                                class="field-input"
                                :class="{ 'p-invalid': errors.province }"
                                style="width: 350px !important;"
                            />
                            <small v-if="errors.province" class="p-error">{{ errors.province[0] }}</small>
                        </div>

                        <!-- Quận/Huyện -->
                        <div class="form-field">
                            <label for="ward" class="field-label">Quận/Huyện *</label>
                            <Dropdown
                                id="ward"
                                v-model="formData.ward"
                                :options="wardOptions"
                                optionLabel="name"
                                placeholder="-- Chọn tỉnh/thành trước --"
                                class="field-input"
                                :class="{ 'p-invalid': errors.ward }"
                                style="width: 350px !important;"
                                :disabled="!formData.province"
                            />
                            <small v-if="errors.ward" class="p-error">{{ errors.ward[0] }}</small>
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
                         <Dropdown
                             id="specialty"
                             v-model="formData.specialty"
                             :options="specialtyOptions"
                             optionLabel="label"
                             optionValue="value"
                             placeholder="Chọn chuyên khoa"
                             class="field-input"
                             :class="{ 'p-invalid': errors.specialty }"
                             style="width: 350px !important;"
                         />
                         <small v-if="errors.specialty" class="p-error">{{ errors.specialty[0] }}</small>
                         </div>

                         <!-- Trình độ -->
                         <div class="form-field">
                             <label for="degree" class="field-label">Trình độ</label>
                             <Dropdown
                                 id="degree"
                                 v-model="formData.degree"
                                 :options="degreeOptions"
                                 optionLabel="label"
                                 optionValue="value"
                                 placeholder="Chọn trình độ"
                                 class="field-input"
                                 :class="{ 'p-invalid': errors.degree }"
                                 style="width: 350px !important;"
                             />
                             <small v-if="errors.degree" class="p-error">{{ errors.degree[0] }}</small>
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
                         <Textarea
                             id="notes"
                             v-model="formData.notes"
                             rows="4"
                             placeholder="Nhập ghi chú"
                             class="field-textarea"
                             :class="{ 'p-invalid': errors.notes }"
                             style="width: 100% !important;"
                         />
                         <small v-if="errors.notes" class="p-error">{{ errors.notes[0] }}</small>
                     </div>
                 </div>
             </div>            
      </div>
    </div>
    <template #footer>
    <div class="flex justify-end gap-2">
          <Button 
            type="button" 
            label="Hủy" 
            severity="secondary" 
            @click="closeModal"
          />
          <Button 
            type="button" 
            label="Lưu" 
            @click="saveDoctor"
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
import Dropdown from 'primevue/dropdown'
import Textarea from 'primevue/textarea'
import axios from 'axios'

export default {
  name: 'CreateDoctorModal',
  components: {
    Dialog,
    Button,
    InputText,
    Dropdown,
    Textarea
  },
  props: {
    visible: {
      type: Boolean,
      default: false
    }
  },
  emits: ['close', 'created'],
  mounted() {
    // Auto generate doctor code when modal opens
    if (this.visible) {
      this.generateDoctorCode()
      this.loadProvinces()
    }
  },
  watch: {
    visible(newVal) {
      if (newVal) {
        this.generateDoctorCode()
        this.loadProvinces()
      }
    },
    'formData.province'(newProvince) {
      if (newProvince && newProvince.code) {
        this.onProvinceChange(newProvince.code)
      }
    }
  },
  data() 
  {
    return {
      loading: false,
      formData: {
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
        notes: ''
      },
      errors: {},
      provinceOptions: [],
      wardOptions: [],
      genderOptions: [
        { label: 'Nam', value: 'male' },
        { label: 'Nữ', value: 'female' }
      ],
      specialtyOptions: [
        { label: 'Nội tổng quát', value: 'general' },
        { label: 'Y học dự phòng', value: 'pediatrics' },
        { label: 'Nội tổng quát', value: 'cardiology' },
        { label: 'Xét nghiệm y học', value: 'dermatology' },
      ],
      degreeOptions: [
        { label: 'Đại học', value: 'doctor' },
        { label: 'Thạc sĩ', value: 'master' },
        { label: 'Tiến sĩ', value: 'phd' },
        { label: 'Bác sĩ chuyên khoa I', value: 'professor' },
        { label: 'Bác sĩ chuyên khoa II', value: 'professor' }
      ]
    }
  },
  methods: {
    closeModal() {
      this.resetForm()
      this.$emit('close')
    },
    async saveDoctor() {
      this.loading = true
      this.errors = {}
      
      try {
        const response = await axios.post('/admin/doctors', this.formData)
        
        if (response.data.success) {
          // Hiển thị thông báo thành công
          this.$toast.add({
            severity: 'success',
            summary: 'Thành công',
            detail: response.data.message,
            life: 3000
          })
          
          this.$emit('created', response.data.data)
          this.closeModal()
        }
      } catch (error) {
        console.error('Error creating doctor:', error)
        
        if (error.response && error.response.status === 422) {
          // Validation errors
          this.errors = error.response.data.errors
          this.$toast.add({
            severity: 'error',
            summary: 'Lỗi validation',
            detail: 'Vui lòng kiểm tra lại thông tin nhập vào',
            life: 5000
          })
        } else {
          // Other errors
          this.$toast.add({
            severity: 'error',
            summary: 'Lỗi',
            detail: error.response?.data?.message || 'Có lỗi xảy ra khi thêm bác sĩ',
            life: 5000
          })
        }
      } finally {
        this.loading = false
      }
    },

    async generateDoctorCode() { //tạo mã bác sĩ random 6 chữ số
      try {
        const response = await axios.get('/admin/doctors/generate-code')
        if (response.data.success) {
          this.formData.doctorCode = response.data.code
        }
      } catch (error) {
        console.error('Error generating doctor code:', error)
        this.$toast.add({
          severity: 'error',
          summary: 'Lỗi',
          detail: 'Không thể tạo mã bác sĩ',
          life: 3000
        })
      }
    },

    resetForm() { 
      this.formData = {
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
        notes: ''
      }
      this.errors = {}
    },

    handleImageUpload() {
      // TODO: Implement image upload logic
      console.log('Image upload clicked')
    },
    
    // API tỉnh thành 
    async loadProvinces() {
      try {
        // Sử dụng shared ProvinceService 
        if (window.provinceService) {
          const provinces = await window.provinceService.loadProvinces()
          this.provinceOptions = provinces.map(province => ({
            name: province.name,
            code: province.code
          }))
        } else {
          //gọi API trực tiếp
          const response = await fetch('https://provinces.open-api.vn/api/?depth=1')
          const data = await response.json()
          
          this.provinceOptions = data.map(province => ({
            name: province.name,
            code: province.code
          }))
        }
      } catch (error) {
        console.error('Error loading provinces:', error)
        this.$toast.add({
          severity: 'error',
          summary: 'Lỗi',
          detail: 'Không thể tải danh sách tỉnh thành',
          life: 3000
        })
      }
    },
    
    async onProvinceChange(provinceCode) {
      if (!provinceCode) {
        this.wardOptions = []
        this.formData.ward = null
        return
      }
      
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
        this.$toast.add({
          severity: 'error',
          summary: 'Lỗi',
          detail: 'Không thể tải danh sách quận/huyện',
          life: 3000
        })
      }
    }
  }
}
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
  grid-column: 1 / -1; /* Span across both columns */
}

/* Address Section */
.address-section {
  grid-column: 1 / -1; /* Span across both columns */
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
  grid-column: 1 / -1; /* Span across both columns */
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
  grid-column: 1 / -1; /* Span across both columns */
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
  width: 55% ;
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
}

.image-upload-circle:hover {
  border-color: #007bff;
  background-color: #f0f8ff;
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