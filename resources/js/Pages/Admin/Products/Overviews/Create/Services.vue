<template>
  <Dialog :visible="visible" @update:visible="$emit('close')" header="Tạo Dịch Vụ Mới" :style="{ width: '950px' }" modal
    :closable="true" class="custom-service-modal" :draggable="false">

    <div class="modal-body-layout">
      <div class="left-panel">
        <div class="custom-tabs">
          <button class="tab-item" :class="{ active: activeTab === 'info' }" @click="activeTab = 'info'">
            <i class="pi pi-info-circle mr-2"></i> Thông tin chung
          </button>
          <button class="tab-item" :class="{ active: activeTab === 'description' }" @click="activeTab = 'description'">
            <i class="pi pi-align-left mr-2"></i> Chi tiết & Mô tả
          </button>
        </div>

        <div class="tab-panels">
          <div v-show="activeTab === 'info'" class="panel-content fade-in">

            <div class="input-grid">

              <div class="form-group">
                <label class="label">Mã dịch vụ</label>
                <div class="p-inputgroup">
                  <InputText v-model="formData.ma_dich_vu" placeholder="Tự động tạo..." readonly class="bg-gray-50" />
                  <Button icon="pi pi-sync" class="p-button-secondary" v-tooltip="'Tạo mã tự động'"
                    @click="generateServiceCode" />
                </div>
              </div>

              <div class="form-group">
                <label class="label" style="margin-left: -50px;">Nhóm hàng <span class="required">*</span></label>
                <div class="custom-category-selector" :class="{ 'p-invalid': errors.nhom_hang_id }">
                  <div class="category-input-wrapper" @click="toggleCategoryDropdown" style="margin-left: -50px; ">
                    <i class="pi pi-th-large input-icon"></i>
                    <input type="text" v-model="categorySearchText"
                      :placeholder="selectedCategoryName || 'Chọn nhóm hàng'" readonly class="category-display-input" />
                    <i class="pi pi-chevron-down dropdown-arrow" :class="{ 'rotated': showCategoryDropdown }"></i>
                  </div>

                  <div v-if="showCategoryDropdown" class="category-dropdown-panel shadow-4">
                    <div class="dropdown-scroll">
                      <div v-for="node in filteredCategoryNodes" :key="node.key" class="tree-node-item"
                        :class="{ 'selected': selectedCategoryKeys[node.key] }"
                        :style="{ paddingLeft: (getNodeLevel(node) * 20 + 12) + 'px' }" @click="selectCategory(node)">
                        <i v-if="node.children && node.children.length > 0" class="pi pi-chevron-right expander"
                          :class="{ 'expanded': expandedNodes[node.key] }" @click.stop="toggleNode(node)"></i>
                        <span class="node-label">{{ node.label }}</span>
                      </div>
                    </div>
                  </div>
                </div>
                <small v-if="errors.nhom_hang_id" class="error-msg">{{ errors.nhom_hang_id[0] }}</small>
              </div>

              <div class="form-group full-span">
                <label class="label">Tên dịch vụ <span class="required">*</span></label>
                <InputText v-model="formData.ten_dich_vu" placeholder="Nhập tên dịch vụ đầy đủ"
                  :class="{ 'p-invalid': errors.ten_dich_vu }" style="width: 635px; " />
                <small v-if="errors.ten_dich_vu" class="error-msg">{{ errors.ten_dich_vu[0] }}</small>
              </div>

              <div class="form-group full-span">
                <label class="label">Bác sĩ phụ trách <span class="required">*</span></label>
                <Dropdown v-model="formData.doctor_id" :options="doctorOptions" optionLabel="name" optionValue="id"
                  placeholder="Chọn bác sĩ..." showClear filter class="w-full" style="width: 635px; "
                  :class="{ 'p-invalid': errors.doctor_id }">
                  <template #option="slotProps">
                    <div class="doctor-item">
                      <div class="font-bold">{{ slotProps.option.name }}</div>
                      <div class="text-xs text-gray-500">{{ slotProps.option.doctor_code }} - {{
                        slotProps.option.specialty }}</div>
                    </div>
                  </template>
                </Dropdown>
                <small v-if="errors.doctor_id" class="error-msg">{{ errors.doctor_id[0] }}</small>
              </div>

              <div class="form-group">
                <label class="label">Hình thức</label>
                <Dropdown v-model="formData.hinh_thuc" :options="serviceTypes" optionLabel="label" optionValue="value"
                  placeholder="Chọn hình thức" class="w-full" :class="{ 'p-invalid': errors.hinh_thuc }"
                  style="width: 300px; " />
                <small v-if="errors.hinh_thuc" class="error-msg">{{ errors.hinh_thuc[0] }}</small>
              </div>

              <div class="form-group">
                <label class="label">Thời gian (phút)</label>
                <InputNumber v-model="formData.thoi_gian_thuc_hien" placeholder="Ví dụ: 30" :min="1" suffix=" phút"
                  showButtons style="width: 320px; " />
              </div>

              <div class="form-group">
                <label class="label">Chi phí dịch vụ <span class="required">*</span></label>
                <InputNumber v-model="formData.gia_dich_vu" mode="currency" currency="VND" locale="vi-VN"
                  class="w-full font-bold" :class="{ 'p-invalid': errors.gia_dich_vu }" style="width: 300px; " />
                <small v-if="errors.gia_dich_vu" class="error-msg">{{ errors.gia_dich_vu[0] }}</small>
              </div>

              <div class="form-group">
                <label class="label">Trạng thái</label>
                <Dropdown v-model="formData.trang_thai" :options="statusOptions" optionLabel="label" optionValue="value"
                  class="w-full" :class="{ 'p-invalid': errors.trang_thai }" style="width: 320px; ">
                  <template #value="slotProps">
                    <span :class="['status-badge', slotProps.value === 'kich_hoat' ? 'active' : 'inactive']">
                      {{ slotProps.value === 'kich_hoat' ? 'Đang hoạt động' : 'Ngừng hoạt động' }}
                    </span>
                  </template>
                </Dropdown>
              </div>

            </div>
          </div>

          <div v-show="activeTab === 'description'" class="panel-content fade-in">
            <div class="form-group">
              <label class="label">Mô tả chi tiết</label>
              <Editor v-model="formData.mo_ta" editorStyle="height: 380px"
                placeholder="Nhập mô tả chi tiết về dịch vụ..." />
              <small v-if="errors.mo_ta" class="error-msg">{{ errors.mo_ta[0] }}</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <template #footer>
      <div class="dialog-footer">
        <Button label="Hủy bỏ" icon="pi pi-times" class="p-button-text p-button-secondary" @click="$emit('close')" />
        <Button label="Lưu dịch vụ" icon="pi pi-check" class="p-button-primary" @click="submitForm"
          :loading="loading" />
      </div>
    </template>
  </Dialog>
  <Toast />
</template>

<script>
// --- GIỮ NGUYÊN LOGIC CŨ 100% ---
import axios from 'axios'
import Editor from 'primevue/editor'
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import InputNumber from 'primevue/inputnumber'
import Dropdown from 'primevue/dropdown'
import Toast from 'primevue/toast'
import TreeSelect from 'primevue/treeselect'
import Textarea from 'primevue/textarea'

export default {
  name: 'CreateService',
  components: {
    Dialog,
    Button,
    InputText,
    InputNumber,
    Dropdown,
    TreeSelect,
    Editor,
    Toast,
    Textarea
  },
  props: {
    visible: {
      type: Boolean,
      default: false
    }
  },
  emits: ['close', 'created'],
  data() {
    return {
      activeTab: 'info',
      loading: false,
      imagePreview: null,
      categories: [],
      categoryTreeNodes: [],
      doctorOptions: [],
      errors: {},
      showCategoryDropdown: false,
      categorySearchText: '',
      selectedCategoryName: '',
      selectedCategoryKeys: {},
      filteredCategoryNodes: [],
      expandedNodes: {},
      serviceTypes: [
        { label: 'Tại nhà thuốc', value: 'tai_nha_thuoc' },
        { label: 'Tại nhà khách', value: 'tai_nha_khach' },
      ],
      statusOptions: [
        { label: 'Kích hoạt', value: 'kich_hoat' },
      ],
      formData: {
        ma_dich_vu: '',
        nhom_hang_id: null,
        ten_dich_vu: '',
        doctor_id: null,
        hinh_thuc: null,
        thoi_gian_thuc_hien: null,
        gia_dich_vu: 0,
        trang_thai: 'kich_hoat',
        mo_ta: '',
        ghi_chu: '',
        image: null
      }
    }
  },
  async mounted() {
    await this.loadCategories()
    await this.loadDoctors()
    document.addEventListener('click', this.handleClickOutside)
  },

  beforeUnmount() {
    document.removeEventListener('click', this.handleClickOutside)
  },
  methods: {
    closeModal() {
      this.resetForm()
      this.$emit('close')
    },

    triggerFileInput() {
      this.$refs.fileInput.click()
    },

    handleImageChange(event) {
      const file = event.target.files[0]
      if (file) {
        // Validate file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
          alert('Kích thước file không được vượt quá 2MB')
          return
        }

        this.formData.image = file

        // Create preview
        const reader = new FileReader()
        reader.onload = (e) => {
          this.imagePreview = e.target.result
        }
        reader.readAsDataURL(file)
      }
    },

    toggleCategoryDropdown() {
      this.showCategoryDropdown = !this.showCategoryDropdown
      if (this.showCategoryDropdown) {
        this.filteredCategoryNodes = this.flattenTreeNodes(this.categoryTreeNodes)
      }
    },

    selectCategory(node) {
      this.formData.nhom_hang_id = node.data.id
      this.selectedCategoryName = node.data.name
      this.selectedCategoryKeys = { [node.key]: true }
      this.showCategoryDropdown = false
      this.categorySearchText = ''

      // Clear any existing errors
      if (this.errors.nhom_hang_id) {
        delete this.errors.nhom_hang_id
      }
    },

    toggleNode(node) {
      // Toggle expanded state in reactive object (Vue 3 way)
      this.expandedNodes[node.key] = !this.expandedNodes[node.key]

      // Update filtered nodes
      this.updateFilteredNodes()
    },


    updateFilteredNodes() {
      this.filteredCategoryNodes = this.flattenTreeNodes(this.categoryTreeNodes)
    },

    getNodeLevel(node) {
      return node.level || 0
    },

    flattenTreeNodes(nodes, level = 0) {
      let result = []
      nodes.forEach(node => {
        const flatNode = { ...node, level }
        result.push(flatNode)
        if (node.children && this.expandedNodes[node.key]) {
          result = result.concat(this.flattenTreeNodes(node.children, level + 1))
        }
      })
      return result
    },

    handleClickOutside(event) {
      // Sử dụng document.querySelector thay vì this.$el.querySelector
      const categorySelector = document.querySelector('.custom-category-selector')
      if (categorySelector && !categorySelector.contains(event.target)) {
        this.showCategoryDropdown = false
      }
    },
    //tạo mã dịch vụ random 6 số
    generateServiceCode() {
      // Generate service code logic
      const timestamp = Date.now().toString().slice(-6)
      this.formData.ma_dich_vu = `DV${timestamp}`
    },


    async submitForm(event) {
      // Chặn submit mặc định
      if (event) {
        event.preventDefault()
        event.stopPropagation()
      }
      this.loading = true
      this.errors = {}

      try {
        const formData = new FormData()

        // Add form data
        Object.keys(this.formData).forEach(key => {
          if (key === 'image' && this.formData[key]) {
            formData.append(key, this.formData[key])
          } else if (this.formData[key] !== null && this.formData[key] !== '') {
            formData.append(key, this.formData[key])
          }
        })

        const response = await axios.post('/admin/services', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
            'X-Requested-With': 'XMLHttpRequest'
          }
        })


        if (response.data.success) {
          // Thêm toast notification
          this.$toast.add({
            severity: 'success',
            summary: 'Thành công',
            detail: response.data.message,
            life: 3000
          })

          this.$emit('created', response.data.data)
          this.closeModal() // ← Gọi method closeModal() thay vì gọi trực tiếp
        }
      } catch (error) {
        console.error('Error creating service:', error)

        if (error.response?.data?.errors) {
          this.errors = error.response.data.errors
        } else if (error.response?.data?.message) {
          alert('Lỗi: ' + error.response.data.message)
        } else {
          alert('Có lỗi xảy ra khi tạo dịch vụ!')
        }
      } finally {
        this.loading = false
      }
    },

    resetForm() {
      this.formData = {
        ma_dich_vu: '',
        nhom_hang_id: null,
        ten_dich_vu: '',
        doctor_id: null,
        hinh_thuc: null,
        thoi_gian_thuc_hien: null,
        gia_dich_vu: 0,
        trang_thai: 'kich_hoat',
        mo_ta: '',
        ghi_chu: '',
        image: null
      }
      this.imagePreview = null
      this.activeTab = 'info'
      this.errors = {}
      this.selectedCategoryName = ''
      this.selectedCategoryKeys = {}
      this.expandedNodes = {}
      this.showCategoryDropdown = false
      this.categorySearchText = ''
    },

    async loadCategories() {
      try {
        const response = await axios.get('/admin/categories/modal/data')
        if (response.data.success) {
          this.categories = response.data.data
          this.categoryTreeNodes = this.convertToTreeNodes(response.data.data)
          this.filteredCategoryNodes = this.flattenTreeNodes(this.categoryTreeNodes)
        }
      } catch (error) {
        console.error('Error loading categories:', error)
      }
    },

    //tải dữ liệu thông tin của bác sĩ
    async loadDoctors() {
      try {
        const response = await axios.get('/admin/doctors/api', {
          params: {
            per_page: 100,
            search: ''
          }
        })
        if (response.data.success) {
          this.doctorOptions = response.data.data
        }
      } catch (error) {
        console.error('Error loading doctors:', error)
      }
    },

    convertToTreeNodes(categories) {
      return categories.map(category => ({
        key: category.id.toString(),
        label: category.name,
        data: { id: category.id, name: category.name },
        expanded: false,
        children: category.children ? this.convertToTreeNodes(category.children) : undefined
      }))
    }
  },

  watch: {
    visible(newVal) {
      if (newVal) {
        this.loadCategories()
        this.resetForm()
      }
    }
  }
}
</script>

<style scoped>
/* ================= GLOBAL LAYOUT ================= */
.modal-body-layout {
  display: flex;
  gap: 24px;
  min-height: 450px;
}

.left-panel {
  flex: 1;
  /* Chiếm phần lớn */
  display: flex;
  flex-direction: column;
}

.right-panel {
  width: 250px;
  /* Fixed width cho sidebar ảnh */
  flex-shrink: 0;
  border-left: 1px solid #f0f0f0;
  padding-left: 24px;
}

/* ================= TABS DESIGN ================= */
.custom-tabs {
  display: flex;
  gap: 8px;
  margin-bottom: 20px;
  border-bottom: 1px solid #eef2f6;
  padding-bottom: 8px;
}

.tab-item {
  background: transparent;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  color: #64748b;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  font-size: 14px;
}

.tab-item:hover {
  background: #f1f5f9;
  color: #334155;
}

.tab-item.active {
  background: #e0f2fe;
  /* Xanh nhạt */
  color: #0284c7;
  /* Xanh đậm */
  font-weight: 600;
}

.panel-content {
  flex: 1;
}

.fade-in {
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(5px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* ================= FORM STYLING ================= */
.input-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  /* 2 cột */
  gap: 16px;
}

.form-group {
  margin-bottom: 4px;
}

.form-group.full-span {
  grid-column: span 2;
  /* Chiếm hết 2 cột */
}

.label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 6px;
}

.required {
  color: #ef4444;
}

.error-msg {
  color: #ef4444;
  font-size: 12px;
  margin-top: 4px;
  display: block;
}

/* Custom cho Tree Select Dropdown */
.custom-category-selector {
  position: relative;
  width: 100%;
}

.category-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
  border: 1px solid #ced4da;
  border-radius: 6px;
  background: white;
  height: 42px;
  /* Khớp với height mặc định của PrimeVue input */
  cursor: pointer;
  transition: all 0.2s;
}

.category-input-wrapper:hover {
  border-color: #3b82f6;
}

.input-icon {
  position: absolute;
  left: 12px;
  color: #9ca3af;
}

.category-display-input {
  width: 100%;
  border: none;
  outline: none;
  padding: 0 35px;
  /* Để chừa chỗ cho 2 icon */
  font-size: 14px;
  color: #374151;
  cursor: pointer;
}

.dropdown-arrow {
  position: absolute;
  right: 12px;
  font-size: 12px;
  color: #9ca3af;
  transition: transform 0.2s;
}

.dropdown-arrow.rotated {
  transform: rotate(180deg);
}

/* Dropdown Content */
.category-dropdown-panel {
  position: absolute;
  top: 105%;
  left: 0;
  width: 100%;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  z-index: 1000;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.dropdown-scroll {
  max-height: 250px;
  overflow-y: auto;
  padding: 8px 0;
}

.tree-node-item {
  display: flex;
  align-items: center;
  padding: 8px 12px;
  cursor: pointer;
  transition: background 0.2s;
  font-size: 14px;
}

.tree-node-item:hover {
  background: #f8fafc;
}

.tree-node-item.selected {
  background: #eff6ff;
  color: #2563eb;
  font-weight: 500;
}

.expander {
  margin-right: 8px;
  font-size: 10px;
  width: 16px;
  text-align: center;
  transition: transform 0.2s;
}

.expander.expanded {
  transform: rotate(90deg);
}

/* ================= IMAGE UPLOAD ================= */
.image-upload-wrapper {
  width: 100%;
}

.upload-box {
  width: 100%;
  aspect-ratio: 1;
  /* Hình vuông */
  border: 2px dashed #cbd5e1;
  border-radius: 12px;
  background: #f8fafc;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  transition: all 0.3s;
}

.upload-box:hover {
  border-color: #3b82f6;
  background: #eff6ff;
}

.upload-box.has-image {
  border-style: solid;
  border-color: #e2e8f0;
}

.upload-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 20px;
}

.icon-circle {
  width: 48px;
  height: 48px;
  background: #e0f2fe;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #0284c7;
  font-size: 20px;
}

.preview-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.upload-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: white;
  opacity: 0;
  transition: opacity 0.2s;
}

.upload-box:hover .upload-overlay {
  opacity: 1;
}

/* ================= INFO BOX ================= */
.info-box {
  background: #f0f9ff;
  border: 1px solid #bae6fd;
  border-radius: 8px;
  padding: 12px;
  display: flex;
  align-items: flex-start;
  line-height: 1.4;
}

/* ================= OVERRIDES ================= */
/* Status Badge Custom */
.status-badge {
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 600;
}

.status-badge.active {
  background: #dcfce7;
  color: #166534;
}

.status-badge.inactive {
  background: #fce7f3;
  color: #9d174d;
}

/* Dialog Footer */
.dialog-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding-top: 16px;
  border-top: 1px solid #f0f0f0;
}
</style>