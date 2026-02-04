<template>
  <div class="settings-content">
    <!-- Left Column: Profile Card -->
    <div class="profile-card">
      <div class="profile-header">
        <div class="avatar-container">
          <div class="avatar-wrapper" :class="{ uploading: uploadingAvatar }">
            <img v-if="user?.avatar" :src="`/storage/avatars/${user.avatar}`" alt="Avatar" class="avatar-img" />
            <div v-else class="avatar-placeholder">
              <i class="fas fa-user"></i>
            </div>

            <button class="btn-camera" @click="triggerFileInput" title="Đổi ảnh đại diện">
              <i class="fas fa-camera"></i>
            </button>
          </div>
          <input ref="avatarInput" type="file" accept="image/*" class="d-none" @change="handleFileSelect" />
        </div>

        <h5 class="profile-title">Ảnh đại diện</h5>
        <p class="profile-subtitle">JPG, PNG tối đa 2MB</p>
      </div>

      <div class="profile-info-list">
        <div class="info-item">
          <span class="info-label">Trạng thái</span>
          <span class="info-value status-active"><span class="dot"></span> Hoạt động</span>
        </div>
        <div class="info-item">
          <span class="info-label">Ngày tham gia</span>
          <span class="info-value">{{ formatDate(user?.created_at) }}</span>
        </div>
      </div>
    </div>

    <!-- Right Column: Main Form -->
    <div class="form-card">
      <form @submit.prevent="submitForm">
        <!-- Section: Personal Info -->
        <div class="form-section">
          <div class="section-header">
            <div class="section-icon icon-user">
              <i class="far fa-user"></i>
            </div>
            <div class="section-text">
              <h4>Thông tin cá nhân</h4>
              <p>Cập nhật thông tin cơ bản của bạn</p>
            </div>
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">Họ và tên</label>
              <input type="text" v-model="form.name" class="form-control" placeholder="Nhập họ tên" />
              <div v-if="form.errors.name" class="invalid-feedback d-block">{{ form.errors.name }}</div>
            </div>
            <div class="form-group">
              <label class="form-label"><i class="far fa-envelope me-1"></i> Email</label>
              <input type="email" v-model="form.email" class="form-control" readonly disabled />
              <!-- Email usually shouldn't be changed or needs verification -->
              <div v-if="form.errors.email" class="invalid-feedback d-block">{{ form.errors.email }}</div>
            </div>
            <div class="form-group">
              <label class="form-label"><i class="fas fa-phone-alt me-1"></i> Số điện thoại</label>
              <input type="text" v-model="form.phone" class="form-control" placeholder="Nhập số điện thoại" />
            </div>
            <div class="form-group">
              <label class="form-label"><i class="far fa-id-badge me-1"></i> Vai trò</label>
              <div class="select-wrapper">
                <select class="form-control" disabled>
                  <option>{{ user?.role === 'admin' ? 'Quản trị viên' : 'Người dùng' }}</option>
                </select>
                <i class="fas fa-chevron-down select-arrow"></i>
              </div>
            </div>
          </div>
        </div>

        <hr class="divider">

        <!-- Section: Address -->
        <div class="form-section">
          <div class="section-header">
            <div class="section-icon icon-location">
              <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="section-text">
              <h4>Địa chỉ</h4>
              <p>Thông tin địa chỉ liên hệ</p>
            </div>
          </div>

          <div class="form-group full-width">
            <label class="form-label">Địa chỉ (Số nhà, đường)</label>
            <input type="text" v-model="form.address" class="form-control" placeholder="Số nhà, tên đường..." />
          </div>

          <div class="form-row three-cols">
            <div class="form-group">
              <label class="form-label">Tỉnh/Thành phố</label>
              <div class="select-wrapper">
                <select v-model="form.province" class="form-control">
                  <option value="">Chọn Tỉnh/Thành</option>
                  <option v-for="p in provinces" :key="p.code" :value="p.code">{{ p.name }}</option>
                </select>
                <i class="fas fa-chevron-down select-arrow"></i>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Quận/Huyện</label>
              <div class="select-wrapper">
                <select v-model="form.district" class="form-control" :disabled="!form.province || loadingDistricts">
                  <option value="">{{ loadingDistricts ? 'Đang tải...' : 'Chọn Quận/Huyện' }}</option>
                  <option v-for="d in districts" :key="d.code" :value="d.code">{{ d.name }}</option>
                </select>
                <i class="fas fa-chevron-down select-arrow"></i>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Xã/Phường</label>
              <div class="select-wrapper">
                <select v-model="form.ward" class="form-control" :disabled="!form.district || loadingWards">
                  <option value="">{{ loadingWards ? 'Đang tải...' : 'Chọn Xã/Phường' }}</option>
                  <option v-for="w in wards" :key="w.code" :value="w.code">{{ w.name }}</option>
                </select>
                <i class="fas fa-chevron-down select-arrow"></i>
              </div>
            </div>
          </div>
        </div>

        <hr class="divider">

        <!-- Section: Password -->
        <div class="form-section">
          <div class="section-header">
            <div class="section-icon icon-lock">
              <i class="fas fa-lock"></i>
            </div>
            <div class="section-text">
              <h4>Đổi mật khẩu</h4>
              <p>Cập nhật mật khẩu để bảo mật tài khoản</p>
            </div>
          </div>

          <div class="form-group full-width">
            <label class="form-label">Mật khẩu mới</label>
            <div class="password-input-wrapper">
              <input :type="showPassword ? 'text' : 'password'" v-model="form.new_password" class="form-control"
                placeholder="Nhập mật khẩu mới" autocomplete="new-password">
              <i class="far fa-eye toggle-password" @click="showPassword = !showPassword"></i>
            </div>
            <small class="form-text text-muted mt-2 d-block">Mật khẩu tối thiểu 6 ký tự</small>
          </div>
        </div>

        <hr class="divider">

        <div class="form-footer">
          <button type="submit" class="btn-save" :disabled="form.processing">
            <i class="far fa-save me-2"></i>
            {{ form.processing ? 'Đang lưu...' : 'Lưu thay đổi' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { useToast } from 'primevue/usetoast'
import axios from 'axios'

const props = defineProps({
  user: Object,
  auth: Object
})

const toast = useToast()
const userData = props.user || props.auth.user

const form = useForm({
  name: userData.name || '',
  email: userData.email || '',
  phone: userData.phone || '',
  address: userData.address || '',
  province: userData.province ? String(userData.province) : '',
  district: userData.district ? String(userData.district) : '',
  ward: userData.ward ? String(userData.ward) : '',
  new_password: '',
})

// UI States
const uploadingAvatar = ref(false)
const showPassword = ref(false)
const avatarInput = ref(null)

// Location Data States
const provinces = ref([])
const districts = ref([])
const wards = ref([])
const loadingDistricts = ref(false)
const loadingWards = ref(false)

// Formatting
const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('en-GB')
}

// --- Address API Logic ---
const fetchApi = async (url) => {
  const res = await fetch(url);
  if (!res.ok) throw new Error('API Error');
  return await res.json();
}

const loadProvinces = async () => {
  try {
    const data = await fetchApi('https://provinces.open-api.vn/api/?depth=1');
    provinces.value = data.map(p => ({ name: p.name, code: String(p.code) }));
  } catch (e) { console.error(e); }
}

const loadDistricts = async (provinceCode) => {
  if (!provinceCode) return;
  loadingDistricts.value = true;
  try {
    const data = await fetchApi(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`);
    districts.value = data.districts.map(d => ({ name: d.name, code: String(d.code) }));
  } catch (e) { console.error(e); }
  finally { loadingDistricts.value = false; }
}

const loadWards = async (districtCode) => {
  if (!districtCode) return;
  loadingWards.value = true;
  try {
    const data = await fetchApi(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`);
    wards.value = data.wards.map(w => ({ name: w.name, code: String(w.code) }));
  } catch (e) { console.error(e); }
  finally { loadingWards.value = false; }
}

watch(() => form.province, (newVal, oldVal) => {
  if (oldVal !== undefined && newVal !== oldVal) {
    form.district = '';
    form.ward = '';
    districts.value = [];
    wards.value = [];
    if (newVal) loadDistricts(newVal);
  }
});

watch(() => form.district, (newVal, oldVal) => {
  if (oldVal !== undefined && newVal !== oldVal) {
    form.ward = '';
    wards.value = [];
    if (newVal) loadWards(newVal);
  }
});

onMounted(async () => {
  await loadProvinces();
  if (form.province) {
    await loadDistricts(form.province);
    if (form.district) {
      await loadWards(form.district);
    }
  }
});

// --- Actions ---
const submitForm = () => {
  form.post('/user/profile-settings', {
    preserveScroll: true,
    onSuccess: () => toast.add({ severity: 'success', summary: 'Thành công', detail: 'Đã cập nhật hồ sơ', life: 3000 }),
    onError: () => toast.add({ severity: 'error', summary: 'Lỗi', detail: 'Vui lòng kiểm tra lại', life: 3000 }),
    onFinish: () => form.reset('new_password'),
  });
}

const triggerFileInput = () => avatarInput.value?.click();

const handleFileSelect = (event) => {
  const file = event.target.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append('avatar', file);
  uploadingAvatar.value = true;

  axios.post('/user/upload/avatar', formData)
    .then(res => {
      if (res.data.success) {
        toast.add({ severity: 'success', summary: 'Thành công', detail: 'Đã đổi ảnh đại diện' });
        router.reload({ only: ['user', 'auth'] });
      }
    })
    .catch(err => toast.add({ severity: 'error', summary: 'Lỗi', detail: 'Upload thất bại' }))
    .finally(() => uploadingAvatar.value = false);
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');

.settings-content {
  display: grid;
  grid-template-columns: 320px 1fr;
  gap: 24px;
  max-width: 1200px;
  font-family: 'Inter', sans-serif;
  color: #1e293b;
}

/* --- Card Styles --- */
.profile-card,
.form-card {
  background: #ffffff;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 24px;
  height: fit-content;
}

/* --- Left Profile Card --- */
.profile-header {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding-bottom: 24px;
  border-bottom: 1px solid #f1f5f9;
  margin-bottom: 24px;
}

.avatar-container {
  position: relative;
  margin-bottom: 16px;
}

.avatar-wrapper {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  border: 4px solid #fff;
  box-shadow: 0 0 0 1px #e2e8f0;
  overflow: visible;
  /* Changed to visible for btn-camera absolute position */
  position: relative;
  background: #f8fafc;
}

.avatar-img {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
}

.avatar-placeholder {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 48px;
  color: #cbd5e1;
}

.btn-camera {
  position: absolute;
  bottom: 0;
  right: 0;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #0F9D58;
  /* Green brand color */
  color: white;
  border: 2px solid #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-camera:hover {
  background: #0b7a43;
  transform: scale(1.05);
}

.profile-title {
  font-weight: 600;
  font-size: 16px;
  margin-bottom: 4px;
  color: #0f172a;
}

.profile-subtitle {
  font-size: 13px;
  color: #64748b;
  margin: 0;
}

.profile-info-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.info-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 14px;
}

.info-label {
  color: #64748b;
}

.info-value {
  font-weight: 500;
  color: #0f172a;
}

.status-active {
  color: #0F9D58;
  display: flex;
  align-items: center;
  gap: 6px;
}

.status-active .dot {
  width: 8px;
  height: 8px;
  background: #0F9D58;
  border-radius: 50%;
}

/* --- Right Form Card --- */
.section-header {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  margin-bottom: 24px;
}

.section-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
}

.icon-user {
  background: #E6F4EA;
  color: #0F9D58;
}

.icon-location {
  background: #E6F4EA;
  color: #0F9D58;
}

/* Changed to match image green theme */
.icon-lock {
  background: #E6F4EA;
  color: #0F9D58;
}

.section-text h4 {
  font-size: 16px;
  font-weight: 600;
  margin: 0 0 4px 0;
  color: #0f172a;
}

.section-text p {
  font-size: 13px;
  color: #64748b;
  margin: 0;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.form-group {
  margin-bottom: 16px;
  position: relative;
}

.form-group.full-width {
  grid-column: span 2;
  width: 100%;
}

.form-row.three-cols {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 20px;
}

.form-label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: #334155;
  margin-bottom: 6px;
}

.form-control {
  width: 100%;
  padding: 10px 14px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  background: #f8fafc;
  color: #0f172a;
  font-size: 14px;
  transition: all 0.2s;
  outline: none;
}

.form-control:focus {
  background: #fff;
  border-color: #0F9D58;
  box-shadow: 0 0 0 3px rgba(15, 157, 88, 0.1);
}

.form-control:disabled {
  background: #f1f5f9;
  color: #94a3b8;
  cursor: default;
}

.select-wrapper {
  position: relative;
}

.select-arrow {
  position: absolute;
  right: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  pointer-events: none;
  font-size: 12px;
}

select.form-control {
  appearance: none;
  -webkit-appearance: none;
}

.password-input-wrapper {
  position: relative;
}

.toggle-password {
  position: absolute;
  right: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  cursor: pointer;
}

.toggle-password:hover {
  color: #64748b;
}

.divider {
  border: none;
  border-top: 1px solid #f1f5f9;
  margin: 32px 0;
}

.form-footer {
  display: flex;
  justify-content: flex-end;
}

.btn-save {
  background: #0F9D58;
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 8px;
  font-weight: 500;
  font-size: 14px;
  cursor: pointer;
  display: flex;
  align-items: center;
  transition: all 0.2s;
}

.btn-save:hover:not(:disabled) {
  background: #0b7a43;
  box-shadow: 0 4px 6px -1px rgba(15, 157, 88, 0.2);
}

.btn-save:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.invalid-feedback {
  color: #ef4444;
  font-size: 12px;
  margin-top: 4px;
}

/* Responsive */
@media (max-width: 900px) {
  .settings-content {
    grid-template-columns: 1fr;
  }

  .form-row.three-cols {
    grid-template-columns: 1fr;
    gap: 16px;
  }

  .form-grid {
    grid-template-columns: 1fr;
    gap: 16px;
  }

  .form-group.full-width {
    grid-column: span 1;
  }
}
</style>