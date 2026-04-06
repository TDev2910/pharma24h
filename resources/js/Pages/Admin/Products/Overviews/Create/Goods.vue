<template>
  <Dialog :visible="visible" @update:visible="$emit('close')" header="Vật tư y tế" :style="{ width: '900px' }" modal
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
        <TabInfo v-show="activeTab === 'info'" :form="form" :selectedCategoryKey="selectedCategoryKey"
          :categoryTreeNodes="categoryTreeNodes" :manufacturerOptions="manufacturerOptions"
          :positionOptions="positionOptions" @generate-code="generateGoodsCode" @generate-barcode="generateGoodsBarcode"
          @category-change="onCategoryChange" @open-unit-modal="showUnitModal = true"
          @open-manufacturer-modal="showManufacturerModal = true" @open-position-modal="showPositionModal = true">
          <!-- Plugin the ImageUploader using named slot -->
          <template #image>
            <ImageUploader :imagePreview="imagePreview" @upload="handleImageUpload" @remove="removeImage" />
          </template>
        </TabInfo>

        <TabDescription v-show="activeTab === 'description'" :form="form" />
      </div>
    </div>

    <!-- Thông báo lỗi -->
    <div v-if="Object.keys(form.errors).length > 0" class="error-messages">
      <div class="error-title">Vui lòng kiểm tra lại thông tin:</div>
      <ul class="error-list">
        <li v-for="(errorMessage, field) in form.errors" :key="field" class="error-item">
          {{ errorMessage }}
        </li>
      </ul>
    </div>

    <!-- Modals con -->
    <UnitOfCalculation :visible="showUnitModal" @close="showUnitModal = false" @saved="onUnitSaved" />
    <ModalManufacturer :visible="showManufacturerModal" @close="showManufacturerModal = false"
      @manufacturer-added="onManufacturerUpdated" @manufacturer-updated="onManufacturerUpdated" />
    <ModalPosition :visible="showPositionModal" @close="showPositionModal = false" @position-added="onPositionUpdated"
      @position-updated="onPositionUpdated" />

    <template #footer>
      <FormFooter :processing="form.processing" @cancel="closeModal" @save="saveGoods" />
    </template>
  </Dialog>
  <Toast />
</template>

<script setup>
import { ref, watch } from 'vue'

// Import PrimeVue Core
import Dialog from 'primevue/dialog'
import Toast from 'primevue/toast'

// Import Modals (Catalogs)
import UnitOfCalculation from '../Modals/UnitofCalculation.vue'
import ModalManufacturer from '../Modals/Catalogs/ModalCatalogManufacturer.vue'
import ModalPosition from '../Modals/Catalogs/ModalCatalogPosition.vue'

// Import Sub-components
import TabInfo from '../../../../../Components/Goods/TabInfo.vue'
import TabDescription from '../../../../../Components/Goods/TabDescription.vue'
import ImageUploader from '../../../../../Components/Goods/ImageUploader.vue'
import FormFooter from '../../../../../Components/Goods/FormFooter.vue'

// Import Composables
import { useGoodsForm } from '../../../../../Composables/Goods/useGoodsForm'
import { useGoodsOptions } from '../../../../../Composables/Goods/useGoodsOptions'
import { useGoodsImage } from '../../../../../Composables/Goods/useGoodsImage'

// Import CSS
import '../../../../../../css/Admin/goods/goods-form.css'
import '../../../../../../css/Admin/primevue-overrides.css'

const props = defineProps({
  visible: { type: Boolean, default: false },
  categories: { type: Array, default: () => [] },
  manufacturers: { type: Array, default: () => [] },
  positions: { type: Array, default: () => [] }
})

const emit = defineEmits(['close', 'created', 'update:visible'])

// UI States
const activeTab = ref('info')
const showUnitModal = ref(false)
const showManufacturerModal = ref(false)
const showPositionModal = ref(false)

// Logic: Form
const { form, saveGoods, resetForm } = useGoodsForm(() => {
  emit('created')
  closeModal()
})

// Logic: Options
const {
  categoryTreeNodes,
  manufacturerOptions,
  positionOptions,
  selectedCategoryKey,
  loadInitialData,
  onManufacturerUpdated,
  onPositionUpdated,
  onUnitSaved,
  generateGoodsCode,
  generateGoodsBarcode,
  onCategoryChange
} = useGoodsOptions(form)

// Logic: Image
const { imagePreview, handleImageUpload, removeImage } = useGoodsImage(form)

// Theo dõi visible để tải dữ liệu ban đầu
watch(() => props.visible, (newVal) => {
  if (newVal) {
    loadInitialData(props)
  }
})

const closeModal = () => {
  resetForm()
  removeImage()
  activeTab.value = 'info'
  selectedCategoryKey.value = null
  emit('close')
  emit('update:visible', false)
}
</script>