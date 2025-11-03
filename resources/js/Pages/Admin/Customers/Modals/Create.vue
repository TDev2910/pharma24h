<template>
    <Dialog 
      :visible="visible" 
      @update:visible="$emit('close')"
      header="Thêm khách hàng mới" 
      :style="{ width: '690px' }"
      modal
      :closable="true"
    >
    <div class="flex gap-6">
        <!-- Left Section: Form Fields Grid -->
        <div class="form-grid" style="flex: 1;">
            <!-- Tên khách hàng -->
            <div class="form-field">
                <label for="name" class="field-label">Tên khách hàng *</label>
                <InputText
                    id="name"
                    v-model="formData.name"
                    type="text"
                    placeholder="Nhập tên khách hàng"
                    class="field-input"
                    :class="{ 'p-invalid': errors.name }"
                />
                <small v-if="errors.name" class="p-error">{{ errors.name[0] }}</small>
            </div>

            <!-- Email -->
            <div class="form-field">
                <label for="email" class="field-label">Email *</label>
                <InputText
                    id="email"
                    v-model="formData.email"
                    type="email"
                    placeholder="email@gmail.com"
                    class="field-input" 
                    :class="{ 'p-invalid': errors.email }"
                />
                <small v-if="errors.email" class="p-error">{{ errors.email[0] }}</small>
            </div>

            <!-- Mật khẩu -->
            <div class="form-field">
                <label for="password" class="field-label">Mật khẩu *</label>
                <InputText
                    id="password"
                    v-model="formData.password"
                    type="password"
                    placeholder="Nhập mật khẩu"
                    class="field-input" 
                    :class="{ 'p-invalid': errors.password }"
                />
                <small v-if="errors.password" class="p-error">{{ errors.password[0] }}</small>
            </div>

            <!-- Xác nhận mật khẩu -->
            <div class="form-field">
                <label for="password_confirmation" class="field-label">Xác nhận mật khẩu *</label>
                <InputText
                    id="password_confirmation"
                    v-model="formData.password_confirmation"
                    type="password"
                    placeholder="Nhập lại mật khẩu"
                    class="field-input" 
                    :class="{ 'p-invalid': errors.password_confirmation }"
                />
                <small v-if="errors.password_confirmation" class="p-error">{{ errors.password_confirmation[0] }}</small>
            </div>

            <!-- Số điện thoại -->
            <div class="form-field">
                <label for="phone" class="field-label">Số điện thoại</label>
                <InputText
                    id="phone"
                    v-model="formData.phone"
                    type="tel"
                    placeholder="Nhập số điện thoại"
                    class="field-input" 
                    :class="{ 'p-invalid': errors.phone }"
                />
                <small v-if="errors.phone" class="p-error">{{ errors.phone[0] }}</small>
            </div>

            <!-- Địa chỉ -->
            <div class="form-field">
                <label for="address" class="field-label">Địa chỉ</label>
                <InputText
                    id="address"
                    v-model="formData.address"
                    type="text"
                    placeholder="Nhập địa chỉ"
                    class="field-input" 
                    :class="{ 'p-invalid': errors.address }"
                />
                <small v-if="errors.address" class="p-error">{{ errors.address[0] }}</small>
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
                            <Dropdown
                                id="province"
                                v-model="formData.province"
                                :options="provinceOptions"
                                optionLabel="name"
                                placeholder="-- Chọn tỉnh/thành phố --"
                                class="field-input"
                                :class="{ 'p-invalid': errors.province }" style="width: 185px;"
                            />
                            <small v-if="errors.province" class="p-error">{{ errors.province[0] }}</small>
                        </div>

                        <!-- Quận/Huyện -->
                        <div class="form-field">
                            <label for="district" class="field-label">Quận/Huyện</label>
                            <Dropdown
                                id="district"
                                v-model="formData.district"
                                :options="districtOptions"
                                optionLabel="name"
                                placeholder="-- Chọn quận/huyện --"
                                class="field-input"
                                :class="{ 'p-invalid': errors.district }"
                                :disabled="!formData.province" style="width: 185px;"
                            />
                            <small v-if="errors.district" class="p-error">{{ errors.district[0] }}</small>
                        </div>

                        <!-- Xã/Phường -->
                        <div class="form-field">
                            <label for="ward" class="field-label">Xã/Phường</label>
                            <Dropdown
                                id="ward"
                                v-model="formData.ward"
                                :options="wardOptions"
                                optionLabel="name"
                                placeholder="-- Chọn xã/phường --"
                                class="field-input"
                                :class="{ 'p-invalid': errors.ward }"
                                :disabled="!formData.district" style="width: 185px;"
                            />
                            <small v-if="errors.ward" class="p-error">{{ errors.ward[0] }}</small>
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
            label="Lưu khách hàng" 
            @click="saveCustomer"
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
  name: 'CreateCustomerModal',
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
    // Load provinces when modal opens
    if (this.visible) {
      this.loadProvinces()
    }
  },
  watch: {
    visible(newVal) {
      if (newVal) {
        this.loadProvinces()
      }
    },
    'formData.province'(newProvince) {
      if (newProvince && newProvince.code) {
        this.onProvinceChange(newProvince.code)
      }
    },
    'formData.district'(newDistrict) {
      if (newDistrict && newDistrict.code) {
        this.onDistrictChange(newDistrict.code)
      }
    }
  },
  data() 
  {
    return {
      loading: false,
      formData: {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        phone: '',
        address: '',
        province: null,
        district: null,
        ward: null
      },
      errors: {},
      provinceOptions: [],
      districtOptions: [],
      wardOptions: []
    }
  },
  methods: {
    closeModal() 
    {
      this.resetForm()
      this.$emit('close')
    },
    async saveCustomer()  //lưu người dùng
    {
      this.loading = true
      this.errors = {}
      
      try 
      {
        // Chuẩn bị dữ liệu 
        const dataToSend = {
          name: this.formData.name,
          email: this.formData.email,
          password: this.formData.password,
          password_confirmation: this.formData.password_confirmation,
          phone: this.formData.phone,
          address: this.formData.address,
          // Xử lý dữ liệu tỉnh/thành phố
          province: this.formData.province?.name || null,
          district: this.formData.district?.name || null,
          ward: this.formData.ward?.name || null
        }
        
        //gửi post request đến admin/customers
        const response = await axios.post('/admin/customers', dataToSend)
        
        //xử lý dữ liệu đầu vào được nhập trong form
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
      } 
      catch (error) {
        console.error('Error creating customer:', error)
        
        if (error.response && error.response.status === 422) {
          // Lỗi validation
          this.errors = error.response.data.errors
          this.$toast.add({
            severity: 'error',
            summary: 'Lỗi validation',
            detail: 'Vui lòng kiểm tra lại thông tin nhập vào',
            life: 5000
          })
        } 
        else 
        {
          // Lỗi khác
          this.$toast.add({
            severity: 'error',
            summary: 'Lỗi',
            detail: error.response?.data?.message || 'Có lỗi xảy ra khi thêm khách hàng',
            life: 5000
          })
        }
      } 
      finally 
      {
        this.loading = false
      }
    },

    resetForm() { 
      this.formData = {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        phone: '',
        address: '',
        province: null,
        district: null,
        ward: null
      }
      this.errors = {}
      this.districtOptions = []
      this.wardOptions = []
    },
    
    // Helper function để fetch với fallback HTTP khi HTTPS bị lỗi SSL
    async fetchWithFallback(url) {
      try {
        // Thử HTTPS trước
        const response = await fetch(url)
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`)
        }
        return response
      } catch (error) {
        // Nếu lỗi SSL hoặc network, thử HTTP fallback
        if (error.message.includes('CERT') || error.message.includes('Failed to fetch') || error.name === 'TypeError') {
          console.warn('HTTPS failed, trying HTTP fallback...', error.message)
          const httpUrl = url.replace('https://', 'http://')
          try {
            const response = await fetch(httpUrl)
            if (!response.ok) {
              throw new Error(`HTTP error! status: ${response.status}`)
            }
            return response
          } catch (fallbackError) {
            console.error('HTTP fallback also failed:', fallbackError)
            throw new Error(`Cả HTTPS và HTTP đều thất bại: ${fallbackError.message}`)
          }
        }
        throw error
      }
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
          //gọi API trực tiếp với fallback
          const response = await this.fetchWithFallback('https://provinces.open-api.vn/api/?depth=1')
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
        this.districtOptions = []
        this.wardOptions = []
        this.formData.district = null
        this.formData.ward = null
        return
      }
      
      try {
        const response = await this.fetchWithFallback(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`)
        const data = await response.json()
        
        this.districtOptions = data.districts.map(district => ({
          name: district.name,
          code: district.code
        }))
        
        // Reset district and ward
        this.formData.district = null
        this.formData.ward = null
        this.wardOptions = []
      } catch (error) {
        console.error('Error loading districts:', error)
        this.$toast.add({
          severity: 'error',
          summary: 'Lỗi',
          detail: 'Không thể tải danh sách quận/huyện',
          life: 3000
        })
      }
    },

    async onDistrictChange(districtCode) {
      if (!districtCode) {
        this.wardOptions = []
        this.formData.ward = null
        return
      }
      
      try {
        const response = await this.fetchWithFallback(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`)
        const data = await response.json()
        
        this.wardOptions = data.wards.map(ward => ({
          name: ward.name,
          code: ward.code
        }))
        
        // Reset ward
        this.formData.ward = null
      } catch (error) {
        console.error('Error loading wards:', error)
        this.$toast.add({
          severity: 'error',
          summary: 'Lỗi',
          detail: 'Không thể tải danh sách xã/phường',
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
  width: 100% ;
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