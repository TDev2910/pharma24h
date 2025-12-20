<template>
  <div class="form-section">
    <!-- Success Alert -->
    <div v-if="flash?.success" class="alert alert-success">
      <i class="fas fa-check-circle me-2"></i>{{ flash.success }}
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
    
    <form @submit.prevent="$emit('submit', formData)">
      <div class="form-group">
        <label class="form-label">Họ và tên</label>
        <input 
          type="text" 
          v-model="formData.name" 
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
          v-model="formData.email" 
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
            v-model="formData.phone" 
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
            :value="roleDisplay" 
            readonly
          />
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Địa chỉ</label>
        <input 
          type="text" 
          v-model="formData.address" 
          class="form-control" 
          :class="{ 'is-invalid': errors.address }"
          placeholder="Địa chỉ của bạn"
        />
        <div v-if="errors.address" class="invalid-feedback">{{ errors.address[0] }}</div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-save" :disabled="loading">
          <i class="fas fa-save me-2"></i>
          {{ loading ? 'Đang lưu...' : 'Lưu thay đổi' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { reactive, computed, watch } from 'vue'

const props = defineProps({
  auth: {
    type: Object,
    required: true
  },
  errors: {
    type: Object,
    default: () => ({})
  },
  flash: {
    type: Object,
    default: () => ({})
  },
  loading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['submit'])

const formData = reactive({
  name: props.auth?.user?.name || '',
  email: props.auth?.user?.email || '',
  phone: props.auth?.user?.phone || '',
  address: props.auth?.user?.address || ''
})

const roleDisplay = computed(() => {
  const role = props.auth?.user?.role
  if (!role) return 'User'
  return role.charAt(0).toUpperCase() + role.slice(1)
})

// Watch for auth changes to update form
watch(() => props.auth?.user, (newUser) => {
  if (newUser) {
    formData.name = newUser.name || ''
    formData.email = newUser.email || ''
    formData.phone = newUser.phone || ''
    formData.address = newUser.address || ''
  }
}, { immediate: true })
</script>

<style scoped>
.form-section {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.alert {
  padding: 12px 16px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.alert-success {
  background: #DCFCE7;
  color: #16A34A;
  border: 1px solid #86EFAC;
}

.alert-danger {
  background: #FEE2E2;
  color: #DC2626;
  border: 1px solid #FCA5A5;
}

.alert ul {
  list-style: none;
  padding-left: 0;
}

.section-title {
  font-size: 20px;
  font-weight: 600;
  color: #1E293B;
  margin-bottom: 24px;
}

.form-group {
  margin-bottom: 20px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.form-label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: #334155;
  margin-bottom: 8px;
}

.form-control {
  width: 100%;
  padding: 10px 14px;
  border: 1px solid #CBD5E1;
  border-radius: 8px;
  font-size: 14px;
  color: #1E293B;
  transition: border-color 0.2s;
}

.form-control:focus {
  outline: none;
  border-color: #3B82F6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-control:read-only {
  background: #F1F5F9;
  cursor: not-allowed;
}

.form-control.is-invalid {
  border-color: #DC2626;
}

.invalid-feedback {
  display: block;
  font-size: 12px;
  color: #DC2626;
  margin-top: 4px;
}

.form-actions {
  margin-top: 32px;
  display: flex;
  justify-content: flex-end;
}

.btn-save {
  padding: 10px 24px;
  background: #3B82F6;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-save:hover:not(:disabled) {
  background: #2563EB;
}

.btn-save:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .form-row {
    grid-template-columns: 1fr;
  }
}
</style>

