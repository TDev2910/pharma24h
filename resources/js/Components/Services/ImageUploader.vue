<template>
  <div class="image-upload-section">
    <div class="image-upload-container" @click="triggerUpload">
      <div v-if="!imagePreview" class="upload-content">
        <i class="pi pi-image upload-icon"></i>
        <div class="upload-text">Thêm ảnh dịch vụ</div>
        <small class="upload-hint">Click để chọn ảnh</small>
        <div class="upload-badge">
          <span class="badge">Tối đa 2MB</span>
        </div>
      </div>

      <div v-if="imagePreview" class="image-preview-content">
        <img :src="imagePreview" alt="Preview" class="preview-image" />
        <div class="image-overlay">
          <Button label="Xóa" @click.stop="$emit('remove')" size="small" severity="danger" class="remove-btn" />
        </div>
      </div>
    </div>
    
    <small v-if="imageError" class="p-error" style="display:block; text-align:center; margin-top: 8px;">{{ imageError }}</small>
    <small v-if="errors && errors.image" class="p-error" style="display:block; text-align:center; margin-top: 8px;">{{ errors.image }}</small>
  </div>
</template>

<script setup>
import Button from 'primevue/button'

defineProps({
  imagePreview: {
    type: [String, null],
    default: null
  },
  imageError: {
    type: [String, null],
    default: null
  },
  errors: Object
})

const emit = defineEmits(['upload', 'remove'])

const triggerUpload = () => {
    const input = document.createElement('input')
    input.type = 'file'
    input.accept = 'image/*'
    input.onchange = (event) => {
        const file = event.target.files[0]
        if (file) {
            emit('upload', file)
        }
    }
    input.click()
}
</script>
