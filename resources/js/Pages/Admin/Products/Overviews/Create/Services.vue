<template>
  <Dialog :visible="visible" @update:visible="$emit('close')" header="Tạo Dịch Vụ Mới" :style="{ width: '900px' }" modal
    :closable="true" @hide="closeModal">
    
    <div class="flex gap-6 form-grid">
      <!-- Left Section: Form Fields -->
      <div style="flex: 1;">
        <!-- Tabs -->
        <div class="tabs-container">
          <div class="tabs-header">
            <button type="button" class="tab-button" :class="{ active: activeTab === 'info' }"
              @click="activeTab = 'info'">
              Thông tin
            </button>
            <button type="button" class="tab-button" :class="{ active: activeTab === 'description' }"
              @click="activeTab = 'description'">
              Mô tả
            </button>
          </div>
          <div class="tabs-divider"></div>
        </div>

        <!-- Tab Content -->
        <TabInfo v-show="activeTab === 'info'" 
          :form="form" 
          :errors="form.errors"
          :categoryTreeNodes="categoryTreeNodes" 
          :doctorOptions="doctorOptions" 
          :serviceTypes="SERVICE_TYPES"
          :statusOptions="STATUS_OPTIONS" 
          v-model:selectedCategoryKey="selectedCategoryKey"
          @categoryChange="onCategoryChange" 
          @generateCode="generateServiceCode">
          
          <!-- Plugin the ImageUploader using named slot -->
          <template #image>
            <ImageUploader 
              :imagePreview="imagePreview" 
              :imageError="imageError" 
              :errors="form.errors"
              @upload="handleImageUpload" 
              @remove="removeImage" 
            />
          </template>
        </TabInfo>

        <TabDescription v-show="activeTab === 'description'" :form="form" />

      </div>
    </div>

    <!-- Thông báo lỗi Validate -->
    <div v-if="Object.keys(form.errors).length > 0" class="error-messages">
      <div class="error-title">Vui lòng kiểm tra lại thông tin:</div>
      <ul class="error-list">
        <li v-for="(errorMessage, field) in form.errors" :key="field" class="error-item">
          {{ errorMessage }}
        </li>
      </ul>
    </div>

    <template #footer>
      <FormFooter :processing="form.processing" @cancel="closeModal" @save="saveService" />
    </template>
  </Dialog>
  <Toast />
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import Dialog from 'primevue/dialog'
import Toast from 'primevue/toast'

// Components
import TabInfo from '../../../../../Components/Services/TabInfo.vue'
import TabDescription from '../../../../../Components/Services/TabDescription.vue'
import ImageUploader from '../../../../../Components/Services/ImageUploader.vue'
import FormFooter from '../../../../../Components/Services/FormFooter.vue'

// Composables
import { useServiceForm } from '../../../../../Composables/Services/useServiceForm'
import { useServiceOptions } from '../../../../../Composables/Services/useServiceOptions'
import { useServiceImage } from '../../../../../Composables/Services/useServiceImage'

// Import CSS Styles from Services (mẫu gốc Medicine đã thiết lập lại)
import '../../../../../../css/Admin/services/service-form.css'
import '../../../../../../css/Admin/services/image-upload.css'
import '../../../../../../css/Admin/primevue-overrides.css'

const props = defineProps({
  visible: { type: Boolean, default: false },
  categories: { type: Array, default: () => [] },
  doctors: { type: Array, default: () => [] }
})

const emit = defineEmits(['close', 'created'])

// 1. Logic Form
const { form, saveService, resetForm } = useServiceForm(() => {
  emit('created', form.data())
  closeModal()
})

// 2. Logic Options
const {
  categoryTreeNodes,
  doctorOptions,
  selectedCategoryKey,
  loadInitialData,
  generateServiceCode,
  onCategoryChange,
  SERVICE_TYPES,
  STATUS_OPTIONS
} = useServiceOptions(form)

// 3. Logic Image
const {
  imagePreview,
  imageError,
  handleImageUpload,
  removeImage
} = useServiceImage(form)

const activeTab = ref('info')

// Tải dữ liệu API khi Dialog được mở
watch(() => props.visible, (newVal) => {
  if (newVal) {
    loadInitialData(props)
    // Tự động sinh mã ngay khi mở modal
    generateServiceCode()
  }
})

// Hàm đóng Modal dùng chung
const closeModal = () => {
  resetForm()
  removeImage()
  activeTab.value = 'info'
  selectedCategoryKey.value = null
  emit('close')
}
</script>