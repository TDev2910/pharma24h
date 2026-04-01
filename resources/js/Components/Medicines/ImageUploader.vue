<template>
  <div class="image-upload-section">
    <div class="image-upload-container" @click="$emit('upload')">
      <div v-if="!imagePreview" class="upload-content">
        <i class="pi pi-image upload-icon"></i>
        <div class="upload-text">Thêm ảnh sản phẩm</div>
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
  </div>
</template>

<script setup>
import Button from 'primevue/button'

defineProps({
  imagePreview: {
    type: [String, null],
    default: null
  }
})

defineEmits(['upload', 'remove'])
</script>
