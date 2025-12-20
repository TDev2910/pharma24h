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

    <h3 class="section-title">Thay đổi mật khẩu</h3>
    
    <form @submit.prevent="$emit('submit', formData)">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Mật khẩu hiện tại</label>
          <input 
            type="password" 
            v-model="formData.current_password" 
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
            v-model="formData.new_password" 
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
          v-model="formData.new_password_confirmation" 
          class="form-control" 
          :class="{ 'is-invalid': errors.new_password_confirmation }"
          placeholder="Nhập lại mật khẩu mới"
        />
        <div v-if="errors.new_password_confirmation" class="invalid-feedback">{{ errors.new_password_confirmation[0] }}</div>
      </div>

      <div class="password-requirements">
        <p class="requirements-title"><i class="fas fa-info-circle"></i> Yêu cầu mật khẩu:</p>
        <ul>
          <li :class="{ 'valid': hasMinLength }">Tối thiểu 8 ký tự</li>
          <li :class="{ 'valid': hasUpperCase }">Có ít nhất 1 chữ hoa</li>
          <li :class="{ 'valid': hasLowerCase }">Có ít nhất 1 chữ thường</li>
          <li :class="{ 'valid': hasNumber }">Có ít nhất 1 số</li>
        </ul>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-save" :disabled="loading || !isValid">
          <i class="fas fa-lock me-2"></i>
          {{ loading ? 'Đang cập nhật...' : 'Đổi mật khẩu' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { reactive, computed } from 'vue'

const props = defineProps({
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
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
})

// Password validation
const hasMinLength = computed(() => formData.new_password.length >= 8)
const hasUpperCase = computed(() => /[A-Z]/.test(formData.new_password))
const hasLowerCase = computed(() => /[a-z]/.test(formData.new_password))
const hasNumber = computed(() => /[0-9]/.test(formData.new_password))

const isValid = computed(() => {
  return hasMinLength.value && 
         hasUpperCase.value && 
         hasLowerCase.value && 
         hasNumber.value &&
         formData.new_password === formData.new_password_confirmation
})
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

.form-control.is-invalid {
  border-color: #DC2626;
}

.invalid-feedback {
  display: block;
  font-size: 12px;
  color: #DC2626;
  margin-top: 4px;
}

.password-requirements {
  background: #F8FAFC;
  border-radius: 8px;
  padding: 16px;
  margin-bottom: 24px;
}

.requirements-title {
  font-size: 14px;
  font-weight: 600;
  color: #334155;
  margin-bottom: 12px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.password-requirements ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.password-requirements li {
  font-size: 13px;
  color: #64748B;
  padding: 4px 0;
  padding-left: 24px;
  position: relative;
}

.password-requirements li::before {
  content: '✗';
  position: absolute;
  left: 0;
  color: #DC2626;
  font-weight: bold;
}

.password-requirements li.valid {
  color: #16A34A;
}

.password-requirements li.valid::before {
  content: '✓';
  color: #16A34A;
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

