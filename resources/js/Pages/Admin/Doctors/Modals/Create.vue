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
                />
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
                    class="field-input readonly-input" style="margin-left: -145px;"
                />
            </div>
              <!-- Right Section: Image Upload -->
            <div class="upload-section">
                <div class="image-upload-container"style="margin-left: 1100px;margin-top: -168px;">
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
                <label for="gender" class="field-label" style="margin-left: -385px;">Giới tính</label>
                <Dropdown
                    id="gender"
                    v-model="formData.gender"
                    :options="genderOptions"
                    optionLabel="label"
                    optionValue="value"
                    placeholder= "Chọn giới tính"
                    class="field-input" style="margin-left: -385px;"
                />
            </div>

            <!-- Điện thoại -->
            <div class="form-field">
                <label for="phone" class="field-label" style="margin-left: 240px;margin-top: -210px;">Điện thoại</label>
                <InputText
                    id="phone"
                    v-model="formData.phone"
                    type="tel"
                    placeholder="Nhập số điện thoại"
                    class="field-input" style="margin-left: 240px;margin-top: -6px;"
                />
            </div>

            <!-- Email (span 2 columns) -->
            <div class="form-field email-field">
                <label for="email" class="field-label"style="margin-top:-150px;width:60%;">Email</label>
                <InputText
                    id="email"
                    v-model="formData.email"
                    type="email"
                    placeholder="email@gmail.com"
                    class="field-input" style="width: 445px !important;"
                />
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
                            class="field-input" style="width: 100% !important;"
                        />
                    </div>

                    <!-- Khu vực và Phường/Xã -->
                    <div class="address-row">
                        <!-- Khu vực -->
                        <div class="form-field">
                            <label for="district" class="field-label">Khu vực</label>
                            <InputText
                                id="district"
                                v-model="formData.district"
                                type="text"
                                placeholder="Tìm Tỉnh/Thành phố - Quận/Huyện"
                                class="field-input" style="width: 350px !important;"
                            />
                        </div>

                        <!-- Phường/Xã -->
                        <div class="form-field">
                            <label for="ward" class="field-label">Phường/Xã</label>
                            <InputText
                                id="ward"
                                v-model="formData.ward"
                                type="text"
                                placeholder="Tìm Phường/Xã"
                                class="field-input" style="width: 350px !important;"
                            />
                        </div>
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

export default {
  name: 'CreateDoctorModal',
  components: {
    Dialog,
    Button,
    InputText,
    Dropdown
  },
  props: {
    visible: {
      type: Boolean,
      default: false
    }
  },
  emits: ['close', 'created'],
  data() {
    return {
      loading: false,
      formData: {
        name: '',
        doctorCode: '',
        gender: null,
        phone: '',
        email: '',
        specialty: '',
        address: '',
        district: '',
        ward: ''
      },
      genderOptions: [
        { label: 'Nam', value: 'male' },
        { label: 'Nữ', value: 'female' },
        { label: 'Khác', value: 'other' }
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
      try {
        // TODO: Gọi API để tạo bác sĩ
        // const response = await this.$http.post('/api/doctors', this.formData)
        
        // Tạm thời giả lập thành công
        await new Promise(resolve => setTimeout(resolve, 1000))
        
        this.$emit('created', { ...this.formData })
        this.closeModal()
      } catch (error) {
        console.error('Error creating doctor:', error)
        // TODO: Hiển thị thông báo lỗi
      } finally {
        this.loading = false
      }
    },
    resetForm() {
      this.formData = {
        name: '',
        doctorCode: '',
        gender: null,
        phone: '',
        email: '',
        specialty: '',
        address: '',
        district: '',
        ward: ''
      }
    },
    handleImageUpload() {
      // TODO: Implement image upload logic
      console.log('Image upload clicked')
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
  width: 150px;
  height: 150px;
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