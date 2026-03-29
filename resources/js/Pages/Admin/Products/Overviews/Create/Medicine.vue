<template>
  <Dialog :visible="visible" @update:visible="$emit('close')" header="Tạo thuốc" :style="{ width: '900px' }" modal
    :closable="true">
    <div class="flex gap-6">
      <!-- Left Section: Form Fields -->
      <div class="form-grid" style="flex: 1;">
        <!-- Tabs -->
        <div class="tabs-container">
          <div class="tabs-header">
            <button class="tab-button" :class="{ active: activeTab === 'info' }" @click="activeTab = 'info'">
              Thông tin
            </button>
            <button class="tab-button" :class="{ active: activeTab === 'description' }"
              @click="activeTab = 'description'">
              Mô tả
            </button>
          </div>
          <div class="tabs-divider"></div>
        </div>

        <!-- Tab Content -->
        <div v-if="activeTab === 'info'" class="tab-content">
          <!-- THÔNG TIN CƠ BẢN -->
          <div class="form-section">
            <div class="form-row">
              <!-- Left inputs -->
              <div class="form-column">
                <div class="form-row">
                  <div class="form-field">
                    <label class="field-label">Mã hàng</label>
                    <div class="input-group">
                      <InputText v-model="form.ma_hang" placeholder="Tự động" readonly
                        class="field-input readonly-input" />
                      <button type="button" class="p-button p-component p-button-secondary p-button-sm" @click="generateMedicineCode">
                         <span class="p-button-icon pi pi-refresh mr-2"></span>
                         <span class="p-button-label">Tạo mã</span>
                      </button>
                    </div>
                  </div>

                  <div class="form-field">
                    <label class="field-label">Mã vạch</label>
                    <div class="input-group">
                      <InputText v-model="form.ma_vach" placeholder="Tự động" readonly
                        class="field-input readonly-input" />
                      <button type="button" class="p-button p-component p-button-secondary p-button-sm" @click="generateMedicineBarcode">
                         <span class="p-button-icon pi pi-refresh mr-2"></span>
                         <span class="p-button-label">Tạo mã</span>
                      </button>
                    </div>
                  </div>
                </div>

                <div class="form-field full-width">
                  <label class="field-label">Tên thuốc <span class="text-danger">*</span></label>
                  <InputText v-model="form.ten_thuoc" placeholder="Nhập tên thuốc" class="field-input"
                    :class="{ 'p-invalid': form.errors.ten_thuoc }" />
                  <small v-if="form.errors.ten_thuoc" class="p-error">{{ form.errors.ten_thuoc }}</small>
                </div>

                <div class="form-row">
                  <div class="form-field">
                    <label class="field-label">Tên viết tắt</label>
                    <InputText v-model="form.ten_viet_tat" placeholder="Nhập tên viết tắt" class="field-input"
                      style="width: 240px;" />
                  </div>

                  <div class="form-field">
                    <label class="field-label">Nhóm hàng <span class="text-danger">*</span></label>
                    
                    <TreeSelect v-model="selectedCategoryKey" :options="categoryTreeNodes" placeholder="Chọn nhóm hàng"
                      class="modern-treeselect w-full" :class="{ 'p-invalid': form.errors.nhom_hang_id }"
                      selectionMode="single" @change="onCategoryChange" />
                    
                    <small v-if="form.errors.nhom_hang_id" class="p-error">{{ form.errors.nhom_hang_id }}</small>
                  </div>
                </div>
              </div>

              <!-- Right image upload -->
              <div class="image-upload-section">
                <div class="image-upload-container" @click="handleImageUpload">
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
                      <Button label="Xóa" @click.stop="removeImage" size="small" severity="danger" class="remove-btn" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Giá vốn, giá bán -->
          <fieldset class="mb-4 border rounded p-3">
            <legend class="float-none w-auto px-2 fs-6">Giá vốn, giá bán</legend>
            <div class="form-row">
              <div class="form-field">
                <label class="field-label">Giá vốn <span class="text-danger">*</span></label>
                <InputNumber v-model="form.gia_von" mode="currency" currency="VND" locale="vi-VN"
                  class="price-input" :class="{ 'p-invalid': form.errors.gia_von }" />
                <small v-if="form.errors.gia_von" class="p-error">{{ form.errors.gia_von }}</small>
              </div>

              <div class="form-field">
                <label class="field-label">Giá bán <span class="text-danger">*</span></label>
                <div class="input-group">
                  <InputNumber v-model="form.gia_ban" mode="currency" currency="VND" locale="vi-VN"
                    class="price-input" :class="{ 'p-invalid': form.errors.gia_ban }" />
                </div>
                <small v-if="form.errors.gia_ban" class="p-error">{{ form.errors.gia_ban }}</small>
              </div>
            </div>
          </fieldset>

          <!-- Thông tin thuốc -->
          <div class="form-section" style="margin-top: 20px;">
            <div class="section-header">
              <span class="section-title">Thông tin thuốc</span>
            </div>
            <div class="form-row three-columns">
              <div class="form-field">
                <label class="field-label">Số đăng ký <span class="text-danger">*</span></label>
                <InputText v-model="form.so_dang_ky" placeholder="Bắt buộc" class="field-input"
                  :class="{ 'p-invalid': form.errors.so_dang_ky }" />
                <small v-if="form.errors.so_dang_ky" class="p-error">{{ form.errors.so_dang_ky }}</small>
              </div>

              <div class="form-field">
                <label class="field-label">Hoạt chất <span class="text-danger">*</span></label>
                <InputText v-model="form.hoat_chat" placeholder="Bắt buộc" class="field-input"
                  :class="{ 'p-invalid': form.errors.hoat_chat }" />
                <small v-if="form.errors.hoat_chat" class="p-error">{{ form.errors.hoat_chat }}</small>
              </div>

              <div class="form-field">
                <label class="field-label">Hàm lượng <span class="text-danger">*</span></label>
                <InputText v-model="form.ham_luong" placeholder="Bắt buộc" class="field-input"
                  :class="{ 'p-invalid': form.errors.ham_luong }" />
                <small v-if="form.errors.ham_luong" class="p-error">{{ form.errors.ham_luong }}</small>
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="field-label">Đường dùng <span class="text-danger">*</span></label>
                <div class="input-group">
                  <Dropdown v-model="form.drugusage_id" :options="drugRouteOptions" optionLabel="name"
                    optionValue="id" placeholder="Bắt buộc" class="field-input"
                    :class="{ 'p-invalid': form.errors.drugusage_id }" />
                  <Button icon="pi pi-cog" @click="openDrugRouteModal" severity="secondary" size="small"
                    title="Quản lý" />
                </div>
                <small class="text-muted">Thêm/Sửa/Xóa thực hiện trong cửa sổ quản lý.</small>
                <small v-if="form.errors.drugusage_id" class="p-error">{{ form.errors.drugusage_id }}</small>
              </div>

              <div class="form-field">
                <label class="field-label">Hãng sản xuất <span class="text-danger">*</span></label>
                <div class="input-group">
                  <Dropdown v-model="form.manufacturer_id" :options="manufacturerOptions" optionLabel="name"
                    optionValue="id" placeholder="Tìm hãng sản xuất" class="field-input"
                    :class="{ 'p-invalid': form.errors.manufacturer_id }" />
                  <Button icon="pi pi-cog" @click="openManufacturerModal" severity="secondary" size="small"
                    title="Quản lý" />
                </div>
                <small class="text-muted">Thêm/Sửa/Xóa thực hiện trong cửa sổ quản lý.</small>
                <small v-if="form.errors.manufacturer_id" class="p-error">{{ form.errors.manufacturer_id }}</small>
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="field-label">Quy cách đóng gói <span class="text-danger">*</span></label>
                <InputText v-model="form.quy_cach_dong_goi" placeholder="Bắt buộc" class="field-input"
                  :class="{ 'p-invalid': form.errors.quy_cach_dong_goi }" />
                <small v-if="form.errors.quy_cach_dong_goi" class="p-error">{{ form.errors.quy_cach_dong_goi }}</small>
              </div>

              <div class="form-field">
                <label class="field-label">Nước sản xuất</label>
                <InputText v-model="form.nuoc_san_xuat" placeholder="Tìm nước sản xuất" class="field-input" />
              </div>
            </div>
          </div>

          <!-- Tồn kho -->
          <fieldset class="mb-4 border rounded p-3">
            <legend class="float-none w-auto px-2 fs-6">Tồn kho</legend>
            <div class="form-row three-columns">
              <div class="form-field">
                <label class="field-label">Tồn kho</label>
                <InputText v-model="form.ton_kho" placeholder="0" readonly class="field-input readonly-input" />
                <small class="text-muted">Số lượng hiện có trong kho</small>
              </div>

              <div class="form-field">
                <label class="field-label">Định mức tồn thấp nhất</label>
                <InputText v-model="form.ton_thap_nhat" placeholder="Nhập số lượng" class="field-input" />
                <small class="text-muted">Cảnh báo khi ≤ số này</small>
              </div>

              <div class="form-field">
                <label class="field-label">Định mức tồn cao nhất</label>
                <InputText v-model="form.ton_cao_nhat" placeholder="Nhập số lượng" class="field-input" />
                <small class="text-muted">Cảnh báo khi ≥ số này</small>
              </div>
            </div>
          </fieldset>

          <!-- Vị trí, trọng lượng -->
          <div class="form-section" style="margin-top: 20px;">
            <div class="section-header">
              <span class="section-title">Vị trí, trọng lượng</span>
            </div>
            <div class="form-row">
              <div class="form-field">
                <label class="field-label">Vị trí <span class="text-danger">*</span></label>
                <div class="input-group">
                  <Dropdown v-model="form.position_id" :options="positionOptions" optionLabel="name"
                    optionValue="id" placeholder="Chọn vị trí" class="field-input"
                    :class="{ 'p-invalid': form.errors.position_id }" />
                  <Button icon="pi pi-cog" @click="openPositionModal" severity="secondary" size="small"
                    title="Quản lý" />
                </div>
                <small class="text-muted">Thêm/Sửa/Xóa thực hiện trong cửa sổ quản lý.</small>
                <small v-if="form.errors.position_id" class="p-error">{{ form.errors.position_id }}</small>
              </div>

              <div class="form-field">
                <label class="field-label">Trọng lượng</label>
                <div style="display: flex; align-items: center; max-width: 350px;">
                  <InputText v-model="form.trong_luong" :min="0" class="field-input"
                    style="flex: 1 1 0; border-top-right-radius: 0; border-bottom-right-radius: 0;" />
                  <span class="input-group-text"
                    style="background: #f6f7f9; border: 1px solid #ced4da; border-left: none; border-radius: 0 6px 6px 0; padding: 0 14px; font-size: 14px; height: 38px; display: flex; align-items: center;">g</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Thiết lập đơn vị tính -->
          <div class="form-section">
            <div class="section-header">
              <span class="section-title">Thiết lập đơn vị tính</span>
            </div>
            <div class="form-row">
              <div class="form-field">
                <InputText v-model="form.don_vi_tinh" placeholder="Nhập đơn vị tính" readonly
                  class="field-input readonly-input" />
              </div>
              <div class="form-field">
                <Button label="Thiết lập" @click="openUnitModal" severity="secondary" class="w-full" />
              </div>
            </div>
          </div>

          <!-- Bán trực tiếp -->
          <div class="form-section">
            <div class="checkbox-field">
              <Checkbox v-model="form.ban_truc_tiep" :binary="true" inputId="ban_truc_tiep" />
              <label for="ban_truc_tiep" class="checkbox-label">Bán trực tiếp</label>
            </div>
          </div>
        </div>

        <!-- Tab Mô tả -->
        <div v-if="activeTab === 'description'" class="tab-content">
          <div class="form-section">
            <div class="form-field full-width">
              <label class="field-label">Mô tả sản phẩm</label>
              <Editor v-model="form.mo_ta" editorStyle="height: 320px" placeholder="Nhập mô tả sản phẩm"
                class="field-editor" :class="{ 'p-invalid': form.errors.mo_ta }" />
              <small v-if="form.errors.mo_ta" class="p-error">{{ form.errors.mo_ta }}</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Unit of Calculation Modal -->
    <UnitOfCalculation :visible="showUnitModal" @close="showUnitModal = false" @saved="onUnitSaved" />

    <!-- Drug Route Modal -->
    <ModalUsageRoute :visible="showDrugRouteModal" @close="showDrugRouteModal = false"
      @drug-route-added="onDrugRouteUpdated" @drug-route-updated="onDrugRouteUpdated" />

    <!-- Manufacturer Modal -->
    <ModalManufacturer :visible="showManufacturerModal" @close="showManufacturerModal = false"
      @manufacturer-added="onManufacturerUpdated" @manufacturer-updated="onManufacturerUpdated" />

    <!-- Position Modal -->
    <ModalPosition :visible="showPositionModal" @close="showPositionModal = false" @position-added="onPositionUpdated"
      @position-updated="onPositionUpdated" />

    <!-- Thông báo lỗi -->
    <div v-if="Object.keys(form.errors).length > 0" class="error-messages">
      <div class="error-title">Vui lòng kiểm tra lại thông tin:</div>
      <ul class="error-list">
        <li v-for="(errorMessage, field) in form.errors" :key="field" class="error-item">
          {{ errorMessage }}
        </li>
      </ul>
    </div>

    <template #footer>
      <div class="flex justify-end gap-2">
        <Button type="button" label="Hủy" severity="secondary" @click="closeModal" />
        <Button type="button" label="Lưu thuốc" @click="saveMedicine" :loading="form.processing" />
      </div>
    </template>

  </Dialog>
  <Toast />
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { useForm, usePage, router } from '@inertiajs/vue3'
import axios from 'axios'
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import InputNumber from 'primevue/inputnumber'
import Dropdown from 'primevue/dropdown'
import Checkbox from 'primevue/checkbox'
import Editor from 'primevue/editor'
import TreeSelect from 'primevue/treeselect'
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'
import UnitOfCalculation from '../Modals/UnitofCalculation.vue'
import ModalUsageRoute from '../Modals/Catalogs/ModalCatalogUsageRoute.vue'
import ModalManufacturer from '../Modals/Catalogs/ModalCatalogManufacturer.vue'
import ModalPosition from '../Modals/Catalogs/ModalCatalogPosition.vue'

const props = defineProps({
  visible: {
    type: Boolean,
    default: false
  },
  // Nhận dữ liệu từ Controller trả về qua Inertia
  categories: { type: Array, default: () => [] },
  manufacturers: { type: Array, default: () => [] },
  drugRoutes: { type: Array, default: () => [] },
  positions: { type: Array, default: () => [] }
})

const emit = defineEmits(['close', 'created', 'update:visible'])

const toast = useToast()
const page = usePage()

const activeTab = ref('info')
const imagePreview = ref(null)

const categoryOptions = ref([])
const categoryTreeNodes = ref([])
const filteredCategoryNodes = ref([])
const drugRouteOptions = ref([])
const manufacturerOptions = ref([])
const positionOptions = ref([])

const selectedCategoryKey = ref(null)
const selectedCategoryName = ref('')

const showUnitModal = ref(false)
const showDrugRouteModal = ref(false)
const showManufacturerModal = ref(false)
const showPositionModal = ref(false)

const form = useForm({
  ma_hang: '',
  ma_vach: '',
  ten_thuoc: '',
  ten_viet_tat: '',
  nhom_hang_id: null,
  gia_von: 0,
  gia_ban: 0,
  so_dang_ky: '',
  hoat_chat: '',
  ham_luong: '',
  drugusage_id: null,
  manufacturer_id: null,
  quy_cach_dong_goi: '',
  nuoc_san_xuat: '',
  ton_kho: '0',
  ton_thap_nhat: '',
  ton_cao_nhat: '',
  position_id: null,
  trong_luong: 0,
  don_vi_tinh: '',
  ban_truc_tiep: false,
  mo_ta: '',
  image: null
})

// Theo dõi khi modal mở để load dữ liệu ban đầu từ Props
watch(() => props.visible, (newVal) => {
  if (newVal) {
    loadInitialData()
  }
})

// Xử lý logic chọn nhóm hàng từ TreeSelect
watch(selectedCategoryKey, (newVal) => {
  if (newVal && Object.keys(newVal).length > 0) {
    const id = Object.keys(newVal)[0]
    form.nhom_hang_id = id
    form.clearErrors('nhom_hang_id')
  } else {
    form.nhom_hang_id = null
  }
}, { deep: true })

const closeModal = () => {
  resetForm()
  emit('close')
  emit('update:visible', false)
}

const saveMedicine = async (event) => {
  if (event) {
    event.preventDefault()
    event.stopPropagation()
  }

  // Dùng FormData để hỗ trợ upload ảnh
  const data = new FormData()
  Object.keys(form).forEach(key => {
    if (key.startsWith('_') || typeof form[key] === 'function') return
    if (form[key] !== null && form[key] !== undefined) {
      data.append(key, form[key])
    }
  })
  // Ghi đè _method nếu cần
  data.append('_method', 'POST')

  // Reset lỗi cũ
  form.clearErrors()
  form.processing = true

  try {
    await axios.post('/admin/medicines', data, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    toast.add({ severity: 'success', summary: 'Thành công', detail: 'Thuốc đã được thêm thành công!', life: 3000 })
    emit('created')
    closeModal()

    // Reload trang để cập nhật danh sách sản phẩm
    setTimeout(() => {
      router.reload({ only: [] })
    }, 300)

  } catch (err) {
    if (err.response && err.response.status === 422) {
      // Validation errors từ Laravel
      const errors = err.response.data.errors || {}
      Object.keys(errors).forEach(field => {
        form.setError(field, errors[field][0])
      })
      toast.add({ severity: 'error', summary: 'Lỗi validation', detail: 'Vui lòng kiểm tra lại thông tin nhập vào', life: 5000 })
    } else {
      toast.add({ severity: 'error', summary: 'Lỗi', detail: 'Có lỗi xảy ra, vui lòng thử lại!', life: 5000 })
      console.error('Error saving medicine:', err)
    }
  } finally {
    form.processing = false
  }
}

const loadInitialData = async () => {
  // Ưu tiên lấy từ props do Controller truyền sang
  if (props.categories && props.categories.length > 0) {
    categoryOptions.value = convertToDropdownOptions(props.categories)
    categoryTreeNodes.value = convertToTreeSelectNodes(props.categories)
    filteredCategoryNodes.value = flattenTreeNodes(convertToTreeNodes(props.categories))
  } else {
    try {
      const res = await axios.get('/admin/categories/modal/data')
      if (res.data.success) {
        categoryOptions.value = convertToDropdownOptions(res.data.data)
        categoryTreeNodes.value = convertToTreeSelectNodes(res.data.data)
        filteredCategoryNodes.value = flattenTreeNodes(convertToTreeNodes(res.data.data))
      }
    } catch (e) {
      console.error('Error fetching categories fallback:', e)
    }
  }
  
  if (props.drugRoutes && props.drugRoutes.length > 0) {
    drugRouteOptions.value = props.drugRoutes
  } else {
    const res = await axios.get('/admin/products/drugroute')
    drugRouteOptions.value = res.data.data || []
  }

  if (props.manufacturers && props.manufacturers.length > 0) {
    manufacturerOptions.value = props.manufacturers
  } else {
    const res = await axios.get('/admin/products/manufacturer')
    manufacturerOptions.value = res.data.data || []
  }

  if (props.positions && props.positions.length > 0) {
    positionOptions.value = props.positions
  } else {
    const res = await axios.get('/admin/products/position')
    positionOptions.value = res.data.data || []
  }
}

// Các hàm Helper chuyển đổi dữ liệu Tree
const convertToDropdownOptions = (categories) => {
  const options = []
  const addToOptions = (nodes, level = 0) => {
    nodes.forEach(node => {
      const prefix = '─ '.repeat(level)
      options.push({ label: prefix + node.name, value: node.id })
      if (node.children && node.children.length > 0) addToOptions(node.children, level + 1)
    })
  }
  addToOptions(categories)
  return options
}

const convertToTreeNodes = (categories) => {
  return categories.map(cat => ({
    key: cat.id.toString(),
    label: cat.name,
    data: { id: cat.id, name: cat.name },
    children: cat.children ? convertToTreeNodes(cat.children) : undefined
  }))
}

const convertToTreeSelectNodes = (categories) => {
  return categories.map(cat => ({
    key: cat.id.toString(),
    label: cat.name,
    data: cat.id,
    children: cat.children ? convertToTreeSelectNodes(cat.children) : undefined,
    selectable: true
  }))
}

const flattenTreeNodes = (nodes, level = 0) => {
  let result = []
  nodes.forEach(node => {
    result.push({ ...node, level, expanded: false })
    if (node.children && node.children.length > 0) result = result.concat(flattenTreeNodes(node.children, level + 1))
  })
  return result
}

const onCategoryChange = (event) => {
  const selectedKey = event.value ? Object.keys(event.value)[0] : null
  if (selectedKey) {
    const findNodeById = (nodes, key) => {
      for (const node of nodes) {
        if (node.key === key) return node
        if (node.children) {
          const found = findNodeById(node.children, key)
          if (found) return found
        }
      }
      return null
    }
    const selectedNode = findNodeById(categoryTreeNodes.value, selectedKey)
    if (selectedNode) {
      form.nhom_hang_id = selectedNode.data
      selectedCategoryName.value = selectedNode.label
    }
    form.clearErrors('nhom_hang_id')
  } else {
    form.nhom_hang_id = null
    selectedCategoryName.value = ''
  }
}

const generateMedicineCode = () => {
  axios.get('/admin/medicines/generate-codes').then(res => {
    if (res.data.ma_hang) form.ma_hang = res.data.ma_hang
  })
}

const generateMedicineBarcode = () => {
  axios.get('/admin/medicines/generate-codes').then(res => {
    if (res.data.ma_vach) form.ma_vach = res.data.ma_vach
  })
}

const handleImageUpload = () => {
  const input = document.createElement('input')
  input.type = 'file'
  input.accept = 'image/*'
  input.onchange = (event) => {
    const file = event.target.files[0]
    if (!file) return
    if (!file.type.startsWith('image/')) return alert('Chỉ chấp nhận file ảnh!')
    if (file.size > 2 * 1024 * 1024) return alert('File quá lớn! Tối đa 2MB')
    
    form.image = file
    const reader = new FileReader()
    reader.onload = (e) => imagePreview.value = e.target.result
    reader.readAsDataURL(file)
  }
  input.click()
}

const removeImage = () => {
  form.image = null
  imagePreview.value = null
}

const openDrugRouteModal = () => showDrugRouteModal.value = true
const onDrugRouteUpdated = async (newData) => {
  const res = await axios.get('/admin/products/drugroute')
  drugRouteOptions.value = res.data.data || []
  if (newData && newData.id) form.drugusage_id = newData.id
}

const openManufacturerModal = () => showManufacturerModal.value = true
const onManufacturerUpdated = async (newData) => {
  const res = await axios.get('/admin/products/manufacturer')
  manufacturerOptions.value = res.data.data || []
  if (newData && newData.id) form.manufacturer_id = newData.id
}

const openPositionModal = () => showPositionModal.value = true
const onPositionUpdated = async (newData) => {
  const res = await axios.get('/admin/products/position')
  positionOptions.value = res.data.data || []
  if (newData && newData.id) form.position_id = newData.id
}

const openUnitModal = () => showUnitModal.value = true
const onUnitSaved = (unitData) => {
  form.don_vi_tinh = unitData.unitName
}

const resetForm = () => {
  form.reset()
  form.clearErrors()
  imagePreview.value = null
  activeTab.value = 'info'
  selectedCategoryKey.value = null
}
</script>

<style scoped>
/* Form Grid Layout */
.form-grid {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Tabs */
.tabs-container {
  margin-bottom: 20px;
}

.tabs-header {
  display: flex;
  gap: 0;
}

.tab-button {
  padding: 12px 24px;
  border: none;
  background: #f8f9fa;
  color: #6c757d;
  font-weight: 600;
  cursor: pointer;
  border-radius: 8px 8px 0 0;
  transition: all 0.2s ease;
}

.tab-button.active {
  background: #fff;
  color: #2c3e50;
  border-bottom: 2px solid #007bff;
}

.tabs-divider {
  height: 2px;
  background: #dee2e6;
  margin-top: -2px;
}

.tab-content {
  padding: 20px 0;
}

/* Form Sections */
.form-section {
  margin-bottom: 24px;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  padding: 16px;
  background: #fff;
  margin-top: -30px;
}

.section-header {
  margin-bottom: 16px;
  padding-bottom: 8px;
  border-bottom: 1px solid #f1f3f4;
}

.section-title {
  font-weight: 600;
  font-size: 16px;
  color: #333;
}

/* Form Layout */
.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-bottom: 16px;
}

.form-row.three-columns {
  grid-template-columns: 1fr 1fr 1fr;
}

.form-row:last-child {
  margin-bottom: 0;
}

.form-column {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.form-field {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-field.full-width {
  grid-column: 1 / -1;
}

/* Image Upload Section */
.image-upload-section {
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding-left: 20px;
}

.image-upload-container {
  width: 200px;
  height: 200px;
  border: 2px dashed #dee2e6;
  border-radius: 8px;
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
  margin-top: 25px;
  margin-left: 35px;
}

.image-upload-container:hover {
  border-color: #007bff;
  background: linear-gradient(135deg, #f0f8ff 0%, #e3f2fd 100%);
}

.upload-content {
  text-align: center;
  color: #007bff;
}

.upload-icon {
  font-size: 48px;
  margin-bottom: 12px;
}

.upload-text {
  font-weight: 600;
  margin-bottom: 8px;
}

.upload-hint {
  color: #6c757d;
  margin-bottom: 12px;
}

.upload-badge .badge {
  background: #fff;
  color: #333;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
}

.image-preview-content {
  position: relative;
  width: 100%;
  height: 100%;
}

.preview-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 6px;
}

.image-overlay {
  position: absolute;
  bottom: 8px;
  left: 50%;
  transform: translateX(-50%);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.image-upload-container:hover .image-overlay {
  opacity: 1;
}

.remove-btn {
  font-size: 12px !important;
  padding: 4px 8px !important;
}

/* Input Groups */
.input-group {
  display: flex;
  gap: 8px;
}

.input-group .field-input {
  flex: 1;
}

.input-group .p-button {
  white-space: nowrap;
}

/* Field Styling */
.field-label {
  font-weight: 600;
  font-size: 14px;
  color: #333;
  margin-bottom: 4px;
}

.field-input {
  width: 100%;
  height: 38px;
  border-radius: 6px;
  font-size: 14px;
  padding: 8px 12px;
  border: 1px solid #ced4da;
}

.field-textarea {
  width: 100%;
  border-radius: 6px;
  font-size: 14px;
  padding: 10px 12px;
  border: 1px solid #ced4da;
  resize: vertical;
  font-family: inherit;
}

.readonly-input {
  background-color: #f8f9fa !important;
  border-color: #e9ecef !important;
}

/* Checkbox */
.checkbox-field {
  display: flex;
  align-items: center;
  gap: 8px;
}

.checkbox-label {
  font-weight: 500;
  color: #333;
  cursor: pointer;
}

/* Error styling */
.p-error {
  color: #e24c4c;
  font-size: 12px;
  margin-top: 4px;
}

.p-invalid {
  border-color: #e24c4c !important;
}

.text-danger {
  color: #dc3545;
}

.text-muted {
  color: #6c757d;
  font-size: 12px;
}

/* Price Input Styling */
.price-input {
  width: 100%;
  height: 38px;
  border-radius: 6px;
  font-size: 14px;
  padding: 8px 12px;
  border: 1px solid #ced4da;
  background: #fff;
}

:deep(.price-input .p-inputnumber-input) {
  border: none !important;
  background: transparent !important;
  padding: 0 !important;
  height: auto !important;
  font-size: 14px !important;
  color: #333 !important;
  box-shadow: none !important;
}

:deep(.price-input .p-inputnumber-input:focus) {
  border: none !important;
  box-shadow: none !important;
  outline: none !important;
}

:deep(.price-input .p-inputnumber-input::placeholder) {
  color: #6c757d !important;
}

/* PrimeVue Dialog customization */
:deep(.p-dialog .p-dialog-header) {
  background: #f8f9fa;
  border-bottom: 1px solid #e9ecef;
  padding: 20px 24px;
}

:deep(.p-dialog .p-dialog-content) {
  padding: 24px;
  background: #fff;
}

:deep(.p-dialog .p-dialog-footer) {
  background: #fff;
  border-top: 1px solid #e9ecef;
  padding: 20px 24px;
}

:deep(.p-dialog .p-dialog-title) {
  font-weight: 600;
  color: #2c3e50;
  font-size: 18px;
}

/* Custom TreeSelect Style */
:deep(.modern-treeselect) {
  width: 100%;
  height: 38px;
  border-radius: 6px;
  border: 1px solid #ced4da;
  padding: 0;
  display: flex;
  align-items: center;
}

:deep(.modern-treeselect:hover) {
  border-color: #007bff;
}

:deep(.modern-treeselect.p-focus) {
  box-shadow: 0 0 0 1px #007bff;
  border-color: #007bff;
}

:deep(.modern-treeselect.p-invalid) {
  border-color: #e24c4c;
}

:deep(.modern-treeselect .p-treeselect-label-container) {
  padding: 8px 12px;
}

:deep(.modern-treeselect .p-treeselect-label) {
  padding: 0;
  font-size: 14px;
  color: #333;
}

:deep(.modern-treeselect .p-treeselect-token) {
  padding: 2px 8px;
  margin-right: 0.5rem;
  background: #e3f2fd;
  color: #007bff;
  border-radius: 4px;
}

/* Dropdown Panel Style */
:deep(.p-treeselect-panel) {
  border: 1px solid #ced4da;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  border-radius: 6px;
}

:deep(.p-treeselect-panel .p-treeselect-items-wrapper) {
  padding: 8px;
  max-height: 250px;
}

/* Tree Node Style bên trong Dropdown */
:deep(.p-treeselect-panel .p-treenode-content) {
  padding: 8px 10px !important;
  border-radius: 6px;
  transition: all 0.2s ease;
  margin-bottom: 2px;
  border: 1px solid transparent;
}

:deep(.p-treeselect-panel .p-treenode-content:hover) {
  background-color: #f8f9fa !important;
  color: #000;
}

:deep(.p-treeselect-panel .p-treenode-content.p-highlight) {
  background-color: #eef6ff !important;
  color: #007bff !important;
  font-weight: 600;
}

:deep(.p-treeselect-panel .p-tree-toggler) {
  width: 24px;
  height: 24px;
  margin-right: 4px;
  color: #adb5bd;
}

/* Editor styling */
.field-editor {
  width: 100%;
  border-radius: 6px;
  border: 1px solid #ced4da;
}

.field-editor.p-invalid {
  border-color: #e24c4c;
}

:deep(.field-editor .ql-editor) {
  font-size: 14px;
  font-family: inherit;
  line-height: 1.5;
}

:deep(.field-editor .ql-toolbar) {
  border-bottom: 1px solid #e9ecef;
  border-radius: 6px 6px 0 0;
}

:deep(.field-editor .ql-container) {
  border-radius: 0 0 6px 6px;
  font-family: inherit;
}

/* Responsive */
@media (max-width: 768px) {
  .form-row {
    grid-template-columns: 1fr;
  }

  .form-row.three-columns {
    grid-template-columns: 1fr;
  }

  .image-upload-section {
    padding-left: 0;
    margin-top: 20px;
  }

  .category-dropdown {
    max-height: 150px;
  }
}

fieldset {
  border: none !important;
}
</style>