<template>
  <div class="image-upload-section">
    <div class="image-upload-container" @click="handleUploadClick">
      <!-- Input ẩn để chọn file -->
      <input 
        type="file" 
        ref="fileInput" 
        style="display: none" 
        accept="image/*" 
        @change="onFileChange" 
      />

      <div v-if="!imagePreview" class="upload-content">
        <i class="pi pi-image upload-icon"></i>
        <div class="upload-text">Thêm ảnh sản phẩm</div>
        <small class="upload-hint">Click để chọn ảnh</small>
        <div class="upload-badge">
          <span class="badge">Tối đa 2MB</span>
        </div>
      </div>

      <div v-else class="image-preview-content">
        <img :src="imagePreview" alt="Preview" class="preview-image" />
        <div class="image-overlay">
          <Button 
            type="button"
            label="Xóa" 
            @click.stop="$emit('remove')" 
            size="small" 
            severity="danger" 
            class="remove-btn" 
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import Button from 'primevue/button'

const props = defineProps({
  imagePreview: { type: String, default: null }
})

const emit = defineEmits(['upload', 'remove'])
const fileInput = ref(null)

const handleUploadClick = () => {
  fileInput.value.click()
}

const onFileChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    emit('upload', file)
  }
}
</script>

<style scoped>
/* Image Upload Styles (Dựa trên pattern dự án) */
.image-upload-section {
  width: 240px;
  flex-shrink: 0;
}
</style>
