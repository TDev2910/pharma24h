<template>
  <div class="settings-content">
    <!-- Profile Photo Section -->
    <div class="profile-section">
      <div class="profile-photo">
        <div class="profile-avatar" :class="{ uploading: uploadingAvatar }">
          <img 
            v-if="auth?.user?.avatar" 
            :src="`/storage/avatars/${auth.user.avatar}`" 
            alt="Avatar" 
            class="avatar-img"
          />
          <i v-else class="fas fa-user"></i>
        </div>
        <h5>Ảnh đại diện</h5>
        <p>Ảnh đại diện sẽ được hiển thị trên hồ sơ của bạn</p>
        <div class="photo-buttons">
          <button class="btn-upload" @click="triggerFileInput">
            <i class="fas fa-camera me-1"></i>Tải lên ảnh mới
          </button>
          <button class="btn-remove" @click="removeAvatar">Xóa</button>
        </div>
        <input 
          ref="avatarInput" 
          type="file" 
          accept="image/*" 
          style="display: none;" 
          @change="handleFileSelect"
        />
      </div>
    </div>

    <!-- Form Section -->
    <div class="form-section">
      <!-- Success Alert -->
      <div v-if="$page.props.flash?.success" class="alert alert-success">
        <i class="fas fa-check-circle me-2"></i>{{ $page.props.flash.success }}
      </div>

      <!-- Error Alert -->
      <div v-if="Object.keys(errors).length > 0" class="alert alert-danger">
        <i class="fas fa-exclamation-circle me-2"></i>
        <ul class="mb-0">
          <li v-for="(error, key) in errors" :key="key">
            {{ Array.isArray(error) ? error[0] : error }}
          </li>
        </ul>
      </div>

      <h3 class="section-title">Thông tin cá nhân</h3>
      
      <form @submit.prevent="submitForm">
        <div class="form-group">
          <label class="form-label">Họ và tên</label>
          <input 
            type="text" 
            v-model="form.name" 
            class="form-control" 
            :class="{ 'is-invalid': errors.name }"
            required
          />
          <div v-if="errors.name" class="invalid-feedback">{{ errors.name[0] }}</div>
        </div>

        <div class="form-group">
          <label class="form-label">Email</label>
          <input 
            type="email" 
            v-model="form.email" 
            class="form-control" 
            :class="{ 'is-invalid': errors.email }"
            required
          />
          <div v-if="errors.email" class="invalid-feedback">{{ errors.email[0] }}</div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Số điện thoại</label>
            <input 
              type="text" 
              v-model="form.phone" 
              class="form-control" 
              :class="{ 'is-invalid': errors.phone }"
              placeholder="+84 xxx xxx xxx"
            />
            <div v-if="errors.phone" class="invalid-feedback">{{ errors.phone[0] }}</div>
          </div>
          <div class="form-group">
            <label class="form-label">Vai trò</label>
            <input 
              type="text" 
              class="form-control" 
              :value="auth?.user?.role ? auth.user.role.charAt(0).toUpperCase() + auth.user.role.slice(1) : 'User'" 
              readonly
            />
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Địa chỉ</label>
          <input 
            type="text" 
            v-model="form.address" 
            class="form-control" 
            :class="{ 'is-invalid': errors.address }"
            placeholder="Địa chỉ của bạn"
          />
          <div v-if="errors.address" class="invalid-feedback">{{ errors.address[0] }}</div>
        </div>

        <!-- Address Details Section -->
        <div class="form-group">
          <label class="form-label">Địa chỉ chi tiết (tùy chọn)</label>
          <div class="address-details">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Tỉnh/Thành phố</label>
                <select 
                  v-model="form.province" 
                  class="form-control address-select"
                  :class="{ 'is-invalid': errors.province }"
                >
                  <option value="">-- Chọn tỉnh/thành phố --</option>
                  <option v-for="province in provinces" :key="province.code" :value="province.code">
                    {{ province.name }}
                  </option>
                </select>
                <div v-if="errors.province" class="invalid-feedback">{{ errors.province[0] }}</div>
              </div>
              <div class="form-group">
                <label class="form-label">Quận/Huyện</label>
                <select 
                  v-model="form.district" 
                  class="form-control address-select"
                  :class="{ 'is-invalid': errors.district }"
                  :disabled="!form.province || loadingDistricts"
                >
                  <option value="">{{ loadingDistricts ? 'Đang tải...' : '-- Chọn quận/huyện --' }}</option>
                  <option v-for="district in districts" :key="district.code" :value="district.code">
                    {{ district.name }}
                  </option>
                </select>
                <div v-if="errors.district" class="invalid-feedback">{{ errors.district[0] }}</div>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Xã/Phường</label>
              <select 
                v-model="form.ward" 
                class="form-control address-select"
                :class="{ 'is-invalid': errors.ward }"
                :disabled="!form.district || loadingWards"
              >
                <option value="">{{ loadingWards ? 'Đang tải...' : '-- Chọn xã/phường --' }}</option>
                <option v-for="ward in wards" :key="ward.code" :value="ward.code">
                  {{ ward.name }}
                </option>
              </select>
              <div v-if="errors.ward" class="invalid-feedback">{{ errors.ward[0] }}</div>
            </div>
          </div>
        </div>

        <div style="margin-top: 32px;">
          <h3 class="section-title">Thay đổi mật khẩu</h3>
          
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Mật khẩu hiện tại</label>
              <input 
                type="password" 
                v-model="form.current_password" 
                class="form-control" 
                :class="{ 'is-invalid': errors.current_password }"
                placeholder="Nhập mật khẩu hiện tại"
              />
              <div v-if="errors.current_password" class="invalid-feedback">{{ errors.current_password[0] }}</div>
            </div>
            <div class="form-group">
              <label class="form-label">Mật khẩu mới</label>
              <input 
                type="password" 
                v-model="form.new_password" 
                class="form-control" 
                :class="{ 'is-invalid': errors.new_password }"
                placeholder="Nhập mật khẩu mới"
              />
              <div v-if="errors.new_password" class="invalid-feedback">{{ errors.new_password[0] }}</div>
            </div>
          </div>
          
          <div class="form-group">
            <label class="form-label">Xác nhận mật khẩu mới</label>
            <input 
              type="password" 
              v-model="form.new_password_confirmation" 
              class="form-control" 
              :class="{ 'is-invalid': errors.new_password_confirmation }"
              placeholder="Nhập lại mật khẩu mới"
            />
            <div v-if="errors.new_password_confirmation" class="invalid-feedback">{{ errors.new_password_confirmation[0] }}</div>
          </div>
        </div>

        <div class="d-flex justify-content-end gap-3 mt-4">
          <Link href="/user/dashboard" class="btn-secondary">
            Hủy bỏ
          </Link>
          <button type="submit" class="btn-primary" :disabled="submitting">
            <span v-if="submitting">Đang lưu...</span>
            <span v-else>Lưu thay đổi</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed, watch } from 'vue'
import { Link, router, usePage, useForm } from '@inertiajs/vue3'
import { useToast } from 'primevue/usetoast'
import axios from 'axios'

const page = usePage()
const toast = useToast()

// Props
const props = defineProps({
  auth: {
    type: Object,
    default: () => ({ user: null })
  }
})

// Form data
const form = useForm({
  name: props.auth?.user?.name || '',
  email: props.auth?.user?.email || '',
  phone: props.auth?.user?.phone || '',
  address: props.auth?.user?.address || '',
  province: props.auth?.user?.province || '',
  district: props.auth?.user?.district || '',
  ward: props.auth?.user?.ward || '',
  current_password: '',
  new_password: '',
  new_password_confirmation: '',
})

// Errors
const errors = computed(() => form.errors)

// States
const submitting = ref(false)
const uploadingAvatar = ref(false)
const avatarInput = ref(null)

// Address API states
const provinces = ref([])
const districts = ref([])
const wards = ref([])
const loadingDistricts = ref(false)
const loadingWards = ref(false)

// Avatar functions
const triggerFileInput = () => {
  avatarInput.value?.click()
}

const handleFileSelect = (event) => {
  const file = event.target.files[0]
  if (!file) return

  if (file.size > 10 * 1024 * 1024) {
    toast.add({
      severity: 'error',
      summary: 'Lỗi',
      detail: 'Kích thước file phải nhỏ hơn 10MB',
      life: 3000
    })
    return
  }

  if (!file.type.startsWith('image/')) {
    toast.add({
      severity: 'error',
      summary: 'Lỗi',
      detail: 'Vui lòng chọn file hình ảnh',
      life: 3000
    })
    return
  }

  uploadAvatar(file)
}

const uploadAvatar = async (file) => {
  uploadingAvatar.value = true
  const formData = new FormData()
  formData.append('avatar', file)

  try {
    const response = await axios.post('/user/upload/avatar', formData)
    
    if (response.data.success) {
      toast.add({
        severity: 'success',
        summary: 'Thành công',
        detail: 'Cập nhật ảnh đại diện thành công!',
        life: 3000
      })
      
      // Reload page to update avatar
      router.reload({ only: ['auth'] })
    } else {
      toast.add({
        severity: 'error',
        summary: 'Lỗi',
        detail: response.data.message || 'Tải ảnh lên thất bại',
        life: 3000
      })
    }
  } catch (error) {
    console.error('Error uploading avatar:', error)
    toast.add({
      severity: 'error',
      summary: 'Lỗi',
      detail: 'Tải ảnh lên thất bại',
      life: 3000
    })
  } finally {
    uploadingAvatar.value = false
    if (avatarInput.value) {
      avatarInput.value.value = ''
    }
  }
}

const removeAvatar = async () => {
  if (!confirm('Bạn có chắc chắn muốn xóa ảnh đại diện?')) {
    return
  }

  try {
    const response = await axios.post('/user/remove/avatar')
    
    if (response.data.success) {
      toast.add({
        severity: 'success',
        summary: 'Thành công',
        detail: 'Xóa ảnh đại diện thành công!',
        life: 3000
      })
      
      // Reload page to update avatar
      router.reload({ only: ['auth'] })
    } else {
      toast.add({
        severity: 'error',
        summary: 'Lỗi',
        detail: response.data.message || 'Xóa ảnh thất bại',
        life: 3000
      })
    }
  } catch (error) {
    console.error('Error removing avatar:', error)
    toast.add({
      severity: 'error',
      summary: 'Lỗi',
      detail: 'Xóa ảnh thất bại',
      life: 3000
    })
  }
}

// Form submission
const submitForm = () => {
  submitting.value = true
  
  form.post('/user/profile-settings', {
    preserveScroll: true,
    onSuccess: () => {
      toast.add({
        severity: 'success',
        summary: 'Thành công',
        detail: 'Cập nhật thông tin thành công!',
        life: 3000
      })
    },
    onError: (errors) => {
      toast.add({
        severity: 'error',
        summary: 'Lỗi',
        detail: 'Vui lòng kiểm tra lại thông tin nhập vào',
        life: 5000
      })
    },
    onFinish: () => {
      submitting.value = false
    }
  })
}

// Helper function để fetch với fallback HTTP khi HTTPS bị lỗi SSL
const fetchWithFallback = async (url) => {
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
}

// Address API functions
const loadProvinces = async () => {
  try {
    // Sử dụng shared ProvinceService nếu có sẵn
    if (window.provinceService) {
      const provincesData = await window.provinceService.loadProvinces()
      provinces.value = provincesData.map(province => ({
        name: province.name,
        code: province.code
      }))
    } else {
      // Gọi API trực tiếp với fallback
      const response = await fetchWithFallback('https://provinces.open-api.vn/api/?depth=1')
      const data = await response.json()
      provinces.value = data.map(province => ({
        name: province.name,
        code: province.code
      }))
    }
  } catch (error) {
    console.error('Error loading provinces:', error)
    toast.add({
      severity: 'error',
      summary: 'Lỗi',
      detail: 'Không thể tải danh sách tỉnh/thành phố',
      life: 3000
    })
  }
}

const loadDistricts = async (provinceCode = null) => {
  const code = provinceCode || form.province
  if (!code) {
    districts.value = []
    wards.value = []
    form.district = ''
    form.ward = ''
    return
  }

  loadingDistricts.value = true
  try {
    const response = await fetchWithFallback(`https://provinces.open-api.vn/api/p/${code}?depth=2`)
    const data = await response.json()
    districts.value = (data.districts || []).map(district => ({
      name: district.name,
      code: district.code
    }))
    wards.value = []
    form.district = ''
    form.ward = ''
  } catch (error) {
    console.error('Error loading districts:', error)
    toast.add({
      severity: 'error',
      summary: 'Lỗi',
      detail: 'Không thể tải danh sách quận/huyện',
      life: 3000
    })
  } finally {
    loadingDistricts.value = false
  }
}

// Watch for province changes
watch(() => form.province, (newProvince) => {
  if (newProvince) {
    loadDistricts(newProvince)
  } else {
    districts.value = []
    wards.value = []
    form.district = ''
    form.ward = ''
  }
}, { immediate: false })

// Watch for district changes
watch(() => form.district, (newDistrict) => {
  if (newDistrict) {
    loadWards(newDistrict)
  } else {
    wards.value = []
    form.ward = ''
  }
}, { immediate: false })

const loadWards = async (districtCode = null) => {
  const code = districtCode || form.district
  if (!code) {
    wards.value = []
    form.ward = ''
    return
  }

  loadingWards.value = true
  try {
    const response = await fetchWithFallback(`https://provinces.open-api.vn/api/d/${code}?depth=2`)
    const data = await response.json()
    wards.value = (data.wards || []).map(ward => ({
      name: ward.name,
      code: ward.code
    }))
    form.ward = ''
  } catch (error) {
    console.error('Error loading wards:', error)
    toast.add({
      severity: 'error',
      summary: 'Lỗi',
      detail: 'Không thể tải danh sách xã/phường',
      life: 3000
    })
  } finally {
    loadingWards.value = false
  }
}

// Load current address on mount
const loadCurrentAddress = async () => {
  if (form.province) {
    // Load districts without resetting current district/ward
    const provinceCode = form.province
    loadingDistricts.value = true
    try {
      const response = await fetchWithFallback(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`)
      const data = await response.json()
      districts.value = (data.districts || []).map(district => ({
        name: district.name,
        code: district.code
      }))
      
      // Only load wards if district is already set
      if (form.district) {
        loadingWards.value = true
        try {
          const response = await fetchWithFallback(`https://provinces.open-api.vn/api/d/${form.district}?depth=2`)
          const data = await response.json()
          wards.value = (data.wards || []).map(ward => ({
            name: ward.name,
            code: ward.code
          }))
        } catch (error) {
          console.error('Error loading wards:', error)
        } finally {
          loadingWards.value = false
        }
      }
    } catch (error) {
      console.error('Error loading districts:', error)
    } finally {
      loadingDistricts.value = false
    }
  }
}

// Lifecycle
onMounted(async () => {
  await loadProvinces()
  await loadCurrentAddress()
})
</script>

<style scoped>
/* Settings Content */
.settings-content {
  display: grid;
  grid-template-columns: 1fr 2fr;
  gap: 40px;
  max-width: 1200px;
}

/* Profile Section */
.profile-section {
  background: white;
  border-radius: 16px;
  padding: 30px;
  border: 1px solid #e2e8f0;
  height: fit-content;
}

.profile-photo {
  text-align: center;
  margin-bottom: 30px;
}

.profile-avatar {
  width: 120px;
  height: 120px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 20px;
  margin: 0 auto 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 48px;
  color: white;
  position: relative;
  overflow: hidden;
}

.profile-avatar.uploading {
  opacity: 0.7;
  pointer-events: none;
}

.avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 20px;
}

.profile-photo h5 {
  color: #1e293b;
  font-weight: 600;
  margin-bottom: 8px;
}

.profile-photo p {
  color: #64748b;
  font-size: 14px;
  margin-bottom: 20px;
}

.photo-buttons {
  display: flex;
  justify-content: center;
  gap: 12px;
}

.btn-upload {
  background: #3b82f6;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-upload:hover {
  background: #2563eb;
}

.btn-remove {
  background: transparent;
  color: #ef4444;
  border: none;
  padding: 8px 16px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-remove:hover {
  background: #fef2f2;
  border-radius: 8px;
}

/* Form Section */
.form-section {
  background: white;
  border-radius: 16px;
  padding: 30px;
  border: 1px solid #e2e8f0;
}

.section-title {
  color: #1e293b;
  font-weight: 600;
  font-size: 18px;
  margin-bottom: 20px;
}

.form-group {
  margin-bottom: 24px;
}

.form-label {
  display: block;
  color: #374151;
  font-weight: 500;
  font-size: 14px;
  margin-bottom: 8px;
}

.form-control {
  width: 100%;
  padding: 12px 16px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  transition: all 0.2s ease;
  background: #ffffff;
}

.form-control:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-control.is-invalid {
  border-color: #ef4444;
}

.invalid-feedback {
  display: block;
  color: #ef4444;
  font-size: 12px;
  margin-top: 4px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

/* Address Details */
.address-details {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 20px;
  margin-top: 8px;
}

.address-select {
  background: white;
}

.address-select:disabled {
  background: #f1f5f9;
  color: #94a3b8;
  cursor: not-allowed;
}

.btn-primary {
  background: #3b82f6;
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 8px;
  font-weight: 500;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-primary:hover:not(:disabled) {
  background: #2563eb;
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-secondary {
  background: #f8fafc;
  color: #64748b;
  border: 1px solid #e2e8f0;
  padding: 12px 24px;
  border-radius: 8px;
  font-weight: 500;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
  display: inline-block;
}

.btn-secondary:hover {
  background: #f1f5f9;
  color: #475569;
  text-decoration: none;
}

.alert {
  padding: 16px;
  border-radius: 8px;
  margin-bottom: 24px;
  font-size: 14px;
}

.alert-success {
  background: #f0fdf4;
  color: #166534;
  border: 1px solid #bbf7d0;
}

.alert-danger {
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fecaca;
}

.alert ul {
  margin: 0;
  padding-left: 20px;
}

/* Responsive */
@media (max-width: 1024px) {
  .settings-content {
    grid-template-columns: 1fr;
    gap: 30px;
  }
}

@media (max-width: 768px) {
  .form-row {
    grid-template-columns: 1fr;
  }
}
</style>