<template>
  <Dialog :visible="visible" @update:visible="(val) => $emit('update:visible', val)" header="Tạo thuốc" :style="{ width: '900px' }" modal
    :closable="true" @hide="closeModal">
    <div class="flex gap-6 form-grid">
      <!-- Left Section: Form Fields -->
      <div style="flex: 1;">
        <!-- Tabs -->
        <div class="tabs-container">
          <div class="tabs-header">
            <button type="button" class="tab-button" :class="{ active: activeTab === 'info' }" @click="activeTab = 'info'">
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
        <!-- Truyền các Options và nhận Events từ component TabInfo -->
        <TabInfo v-show="activeTab === 'info'"
          :form="form"
          :selectedCategoryKey="selectedCategoryKey"
          :categoryTreeNodes="categoryTreeNodes"
          :drugRouteOptions="drugRouteOptions"
          :manufacturerOptions="manufacturerOptions"
          :positionOptions="positionOptions"
          @generate-code="generateMedicineCode"
          @generate-barcode="generateMedicineBarcode"
          @category-change="onCategoryChange"
          @open-unit-modal="showUnitModal = true"
          @open-drug-route-modal="showDrugRouteModal = true"
          @open-manufacturer-modal="showManufacturerModal = true"
          @open-position-modal="showPositionModal = true"
        >
          <!-- Plugin the ImageUploader using named slot -->
          <template #image>
            <ImageUploader 
              :imagePreview="imagePreview" 
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

    <!-- Các Modals (Quản lý mục con) -->
    <UnitOfCalculation :visible="showUnitModal" @close="showUnitModal = false" @saved="(data) => form.don_vi_tinh = data.unitName" />
    <ModalUsageRoute :visible="showDrugRouteModal" @close="showDrugRouteModal = false" @drug-route-added="onDrugRouteUpdated" @drug-route-updated="onDrugRouteUpdated" />
    <ModalManufacturer :visible="showManufacturerModal" @close="showManufacturerModal = false" @manufacturer-added="onManufacturerUpdated" @manufacturer-updated="onManufacturerUpdated" />
    <ModalPosition :visible="showPositionModal" @close="showPositionModal = false" @position-added="onPositionUpdated" @position-updated="onPositionUpdated" />

    <template #footer>
      <FormFooter :processing="form.processing" @cancel="closeModal" @save="saveMedicine" />
    </template>
  </Dialog>
  <Toast />
</template>

<script setup>
import { ref, watch } from 'vue'

// Import PrimeVue Core Components
import Dialog from 'primevue/dialog'
import Toast from 'primevue/toast'

// Import Modals 
import UnitOfCalculation from '../Modals/UnitofCalculation.vue'
import ModalUsageRoute from '../Modals/Catalogs/ModalCatalogUsageRoute.vue'
import ModalManufacturer from '../Modals/Catalogs/ModalCatalogManufacturer.vue'
import ModalPosition from '../Modals/Catalogs/ModalCatalogPosition.vue'

// Import các UI Components
import TabInfo from '../../../../../Components/Medicines/TabInfo.vue'
import TabDescription from '../../../../../Components/Medicines/TabDescription.vue'
import ImageUploader from '../../../../../Components/Medicines/ImageUploader.vue'
import FormFooter from '../../../../../Components/Medicines/FormFooter.vue'

// Import Logic từ Composables 
import { useMedicineForm } from '../../../../../Composables/Medicines/useMedicineForm'
import { useMedicineOptions } from '../../../../../Composables/Medicines/useMedicineOptions'
import { useMedicineImage } from '../../../../../Composables/Medicines/useMedicineImage'

// Import CSS Styles
import '../../../../../../css/Admin/medicines/medicine-form.css'
import '../../../../../../css/Admin/medicines/image-upload.css'
import '../../../../../../css/Admin/primevue-overrides.css'

const props = defineProps({
  visible: { type: Boolean, default: false },
  categories: { type: Array, default: () => [] },
  manufacturers: { type: Array, default: () => [] },
  drugRoutes: { type: Array, default: () => [] },
  positions: { type: Array, default: () => [] }
})

const emit = defineEmits(['close', 'created', 'update:visible'])

// State Quản lý hiển thị Tab và Modal con
const activeTab = ref('info')
const showUnitModal = ref(false)
const showDrugRouteModal = ref(false)
const showManufacturerModal = ref(false)
const showPositionModal = ref(false)

// Khởi tạo Composable: Form Logic
const { form, saveMedicine, resetForm } = useMedicineForm(() => {
  emit('created')
  closeModal()
})

// Khởi tạo Composable: Image Logic
const { imagePreview, handleImageUpload, removeImage } = useMedicineImage(form)

// Khởi tạo Composable: Options & Dropdowns Logic
const {
  categoryTreeNodes,
  drugRouteOptions,
  manufacturerOptions,
  positionOptions,
  selectedCategoryKey,
  loadInitialData,
  onDrugRouteUpdated,
  onManufacturerUpdated,
  onPositionUpdated,
  generateMedicineCode,
  generateMedicineBarcode,
  onCategoryChange
} = useMedicineOptions(form)

// Tải dữ liệu API khi Dialog được mở
watch(() => props.visible, (newVal) => {
  if (newVal) {
    loadInitialData(props)
  }
})

// Hàm đóng Modal dùng chung
const closeModal = () => {
  resetForm()
  removeImage() // Đảm bảo URL ảnh cũ không bị kẹt lại
  activeTab.value = 'info'
  selectedCategoryKey.value = null
  emit('close')
  emit('update:visible', false)
}
</script>