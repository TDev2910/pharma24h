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

    <!-- Information Form -->
    <InforForm 
      :auth="auth" 
      :errors="errors" 
      :flash="$page.props.flash"
      :loading="loading"
      @submit="handleInfoSubmit"
    />

    <!-- Password Form -->
    <PasswordForm 
      :errors="errors" 
      :flash="$page.props.flash"
      :loading="loading"
      @submit="handlePasswordSubmit"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import InforForm from '@/Components/User/Profile/InforForm.vue'
import PasswordForm from '@/Components/User/Profile/PasswordForm.vue'

const props = defineProps({
  auth: Object,
  errors: {
    type: Object,
    default: () => ({})
  }
})

const page = usePage()
const loading = ref(false)
const uploadingAvatar = ref(false)
const avatarInput = ref(null)

// Avatar functions
const triggerFileInput = () => {
  avatarInput.value?.click()
}

const handleFileSelect = async (event) => {
  const file = event.target.files?.[0]
  if (!file) return

  const formData = new FormData()
  formData.append('avatar', file)

  uploadingAvatar.value = true
  
  router.post('/user/profile/avatar', formData, {
    preserveScroll: true,
    onFinish: () => {
      uploadingAvatar.value = false
      event.target.value = ''
    }
  })
}

const removeAvatar = () => {
  if (!confirm('Bạn có chắc muốn xóa ảnh đại diện?')) return

  router.delete('/user/profile/avatar', {
    preserveScroll: true
  })
}

// Form submissions
const handleInfoSubmit = (formData) => {
  loading.value = true
  router.put('/user/profile', formData, {
    preserveScroll: true,
    onFinish: () => {
      loading.value = false
    }
  })
}

const handlePasswordSubmit = (formData) => {
  loading.value = true
  router.put('/user/profile/password', formData, {
    preserveScroll: true,
    onFinish: () => {
      loading.value = false
    },
    onSuccess: () => {
      // Reset password fields on success
      formData.current_password = ''
      formData.new_password = ''
      formData.new_password_confirmation = ''
    }
  })
}
</script>

<style scoped>
.settings-content {
  max-width: 900px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 24px;
}

/* Profile Photo Section */
.profile-section {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.profile-photo {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.profile-avatar {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  background: #F1F5F9;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 16px;
  position: relative;
  overflow: hidden;
}

.profile-avatar.uploading::after {
  content: '';
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
}

.profile-avatar i {
  font-size: 48px;
  color: #94A3B8;
}

.avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.profile-photo h5 {
  font-size: 18px;
  font-weight: 600;
  color: #1E293B;
  margin-bottom: 8px;
}

.profile-photo p {
  font-size: 14px;
  color: #64748B;
  margin-bottom: 16px;
}

.photo-buttons {
  display: flex;
  gap: 12px;
}

.btn-upload,
.btn-remove {
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 14px;
}

.btn-upload {
  background: #3B82F6;
  color: white;
}

.btn-upload:hover {
  background: #2563EB;
}

.btn-remove {
  background: #F1F5F9;
  color: #64748B;
}

.btn-remove:hover {
  background: #E2E8F0;
  color: #475569;
}

@media (max-width: 768px) {
  .photo-buttons {
    flex-direction: column;
    width: 100%;
  }

  .btn-upload,
  .btn-remove {
    width: 100%;
  }
}
</style>
