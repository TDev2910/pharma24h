<template>
  <Dialog :visible="visible" @update:visible="$emit('close')" header="Tạo dịch vụ" :style="{ width: '900px' }" modal
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
                    <label class="field-label">Mã dịch vụ</label>
                    <div class="input-group">
                      <InputText v-model="formData.ma_dich_vu" placeholder="Tự động" readonly
                        class="field-input readonly-input" />
                      <Button label="Tạo mã" icon="pi pi-refresh" @click="generateServiceCode" severity="secondary"
                        size="small" />
                    </div>
                  </div>

                  <div class="form-field">
                    <label class="field-label">Nhóm hàng <span class="text-danger">*</span></label>
                    <div class="custom-category-selector" :class="{ 'p-invalid': errors.nhom_hang_id }">
                      <div class="category-input-container" @click="toggleCategoryDropdown">
                        <div class="category-input">
                          <i class="pi pi-search search-icon"></i>
                          <input type="text" v-model="categorySearchText"
                            :placeholder="selectedCategoryName || 'Chọn nhóm hàng (Bắt buộc)'" readonly
                            class="category-search-input" />
                          <i class="pi pi-chevron-up dropdown-icon" :class="{ 'rotated': showCategoryDropdown }"></i>
                        </div>
                      </div>

                      <div v-if="showCategoryDropdown" class="category-dropdown">
                        <div class="category-dropdown-content">
                          <div v-for="node in filteredCategoryNodes" :key="node.key" class="category-option" :class="{
                            'selected': selectedCategoryKeys[node.key],
                            'has-children': node.children && node.children.length > 0
                          }" :style="{ paddingLeft: (getNodeLevel(node) * 20 + 12) + 'px' }"
                            @click="selectCategory(node)">
                            <i v-if="node.children && node.children.length > 0" class="pi pi-chevron-right expand-icon"
                              :class="{ 'expanded': expandedNodes[node.key] }" @click.stop="toggleNode(node)"
                              style="cursor: pointer;"></i>
                            <span class="category-label">{{ node.label }}</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <small v-if="errors.nhom_hang_id" class="p-error">{{ errors.nhom_hang_id[0] }}</small>
                  </div>
                </div>

                <div class="form-field full-width">
                  <label class="field-label">Tên dịch vụ <span class="text-danger">*</span></label>
                  <InputText v-model="formData.ten_dich_vu" placeholder="Nhập tên dịch vụ" class="field-input"
                    :class="{ 'p-invalid': errors.ten_dich_vu }" />
                  <small v-if="errors.ten_dich_vu" class="p-error">{{ errors.ten_dich_vu[0] }}</small>
                </div>

                <div class="form-field full-width">
                  <label class="field-label">Bác sĩ đảm nhận dịch vụ <span class="text-danger">*</span></label>
                  <Dropdown v-model="formData.doctor_id" :options="doctorOptions" optionLabel="name" optionValue="id"
                    placeholder="-- Chọn bác sĩ --" class="field-input" :class="{ 'p-invalid': errors.doctor_id }"
                    showClear filter>
                    <template #option="slotProps">
                      <div>
                        <div>{{ slotProps.option.name }}</div>
                        <small class="text-muted">{{ slotProps.option.doctor_code }} - {{ slotProps.option.specialty
                        }}</small>
                      </div>
                    </template>
                  </Dropdown>
                  <small v-if="errors.doctor_id" class="p-error">{{ errors.doctor_id[0] }}</small>
                </div>


                <div class="form-row">
                  <div class="form-field">
                    <label class="field-label">Hình thức dịch vụ <span class="text-danger">*</span></label>
                    <Dropdown v-model="formData.hinh_thuc" :options="serviceTypes" optionLabel="label"
                      optionValue="value" placeholder="Chọn hình thức" class="field-input"
                      :class="{ 'p-invalid': errors.hinh_thuc }" />
                    <small v-if="errors.hinh_thuc" class="p-error">{{ errors.hinh_thuc[0] }}</small>
                  </div>

                  <div class="form-field">
                    <label class="field-label">Thời gian thực hiện (phút)</label>
                    <InputNumber v-model="formData.thoi_gian_thuc_hien" placeholder="VD: 30" :min="1"
                      class="field-input" />
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-field">
                    <label class="field-label">Chi phí thực hiện <span class="text-danger">*</span></label>
                    <div class="form-field">
                      <InputNumber v-model="formData.gia_dich_vu" mode="currency" currency="VND" locale="vi-VN"
                        class="price-input" :class="{ 'p-invalid': errors.gia_dich_vu }" />
                    </div>
                    <small v-if="errors.gia_dich_vu" class="p-error">{{ errors.gia_dich_vu[0] }}</small>
                  </div>

                  <div class="form-field">
                    <label class="field-label">Trạng thái <span class="text-danger">*</span></label>
                    <Dropdown v-model="formData.trang_thai" :options="statusOptions" optionLabel="label"
                      optionValue="value" placeholder="Chọn trạng thái" class="field-input"
                      :class="{ 'p-invalid': errors.trang_thai }" />
                    <small v-if="errors.trang_thai" class="p-error">{{ errors.trang_thai[0] }}</small>
                  </div>
                </div>
              </div>

              <!-- Right Section: Image Upload -->
              <div class="image-section">
                <div class="image-upload-container" @click="triggerFileInput">
                  <input type="file" ref="fileInput" accept="image/*" @change="handleImageChange" style="display: none">
                  <div v-if="!imagePreview" class="image-placeholder">
                    <i class="pi pi-image"></i>
                    <div class="upload-text">Thêm ảnh dịch vụ</div>
                    <small class="upload-hint">Click để chọn ảnh</small>
                    <div class="upload-badge">
                      <span class="badge">Tối đa 2MB</span>
                    </div>
                  </div>
                  <img v-else :src="imagePreview" alt="Preview" class="image-preview">
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tab Mô tả -->
        <div v-if="activeTab === 'description'" class="tab-content">
          <div class="form-section">
            <div class="form-field full-width">
              <label class="field-label">Mô tả dịch vụ</label>
              <Editor v-model="formData.mo_ta" editorStyle="height: 320px" placeholder="Nhập mô tả sản phẩm"
                class="field-editor" :class="{ 'p-invalid': errors.mo_ta }" />
              <small v-if="errors.mo_ta" class="p-error">{{ errors.mo_ta[0] }}</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <template #footer>
      <div class="flex justify-content-end gap-2">
        <Button type="button" label="Hủy" icon="pi pi-times" @click="$emit('close')" severity="secondary" />
        <Button type="button" label="Lưu dịch vụ" icon="pi pi-check" @click="submitForm" :loading="loading"
          :disabled="loading" />
      </div>
    </template>
  </Dialog>
  <Toast />
</template>

<script>
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
/* Form Layout */
.form-grid {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-section {
  background: #f8f9fa;
  border-radius: 12px;
  padding: 20px;
  border: 1px solid #e9ecef;
}

.form-row {
  display: flex;
  gap: 20px;
  align-items: flex-start;
}

.form-column {
  flex: 1;
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
  width: 100%;
}

.field-label {
  font-weight: 600;
  color: #495057;
  font-size: 14px;
  margin-bottom: 4px;
}

.field-input {
  width: 100%;
}

.readonly-input {
  background-color: #f8f9fa !important;
  color: #6c757d !important;
}

.input-group {
  display: flex;
  gap: 8px;
  align-items: center;
}

.input-group .field-input {
  flex: 1;
}

/* Tabs */
.tabs-container {
  margin-bottom: 20px;
}

.tabs-header {
  display: flex;
  gap: 0;
  border-bottom: 2px solid #e9ecef;
}

.tab-button {
  background: none;
  border: none;
  padding: 12px 24px;
  font-weight: 600;
  color: #6c757d;
  cursor: pointer;
  border-bottom: 3px solid transparent;
  transition: all 0.2s ease;
  font-size: 14px;
}

.tab-button:hover {
  color: #495057;
  background: #f8f9fa;
}

.tab-button.active {
  color: #007bff;
  border-bottom-color: #007bff;
  background: white;
}

.tabs-divider {
  height: 2px;
  background: #e9ecef;
  margin-top: -2px;
}

.tab-content {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Image Upload */
.image-section {
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding-top: 20px;
}

.image-upload-container {
  border: 2px dashed #007bff;
  border-radius: 12px;
  width: 200px;
  height: 200px;
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  position: relative;
  transition: all 0.3s ease;
}

.image-upload-container:hover {
  border-color: #0056b3;
  background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
}

.image-placeholder {
  text-align: center;
  color: #007bff;
}

.image-placeholder i {
  font-size: 3rem;
  margin-bottom: 12px;
  display: block;
}

.upload-text {
  font-weight: 600;
  font-size: 14px;
  margin-bottom: 4px;
}

.upload-hint {
  color: #6c757d;
  font-size: 12px;
}

.upload-badge {
  margin-top: 12px;
}

.badge {
  background: #e9ecef;
  color: #495057;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 11px;
  font-weight: 500;
}

.image-preview {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 8px;
}

/* Error States */
.text-danger {
  color: #dc3545 !important;
}

.p-error {
  color: #dc3545;
  font-size: 12px;
  margin-top: 4px;
}

.p-invalid {
  border-color: #dc3545 !important;
}

/* Responsive */
@media (max-width: 768px) {
  .form-row {
    flex-direction: column;
    gap: 16px;
  }

  .image-section {
    align-items: center;
    padding-top: 0;
  }

  .image-upload-container {
    width: 150px;
    height: 150px;
  }

  .tabs-header {
    flex-direction: column;
  }

  .tab-button {
    text-align: left;
    border-bottom: 1px solid #e9ecef;
  }

  .tab-button.active {
    border-bottom-color: #007bff;
  }
}

/* PrimeVue Component Overrides */
:deep(.p-dialog) {
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

:deep(.p-dialog-header) {
  background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
  color: white;
  border-radius: 16px 16px 0 0;
  padding: 20px 24px;
}

:deep(.p-dialog-title) {
  font-weight: 600;
  font-size: 18px;
}

:deep(.p-dialog-content) {
  padding: 24px;
}

:deep(.p-dialog-footer) {
  padding: 20px 24px;
  border-top: 1px solid #e9ecef;
  background: #f8f9fa;
  border-radius: 0 0 16px 16px;
}

:deep(.p-inputtext),
:deep(.p-dropdown),
:deep(.p-inputnumber),
:deep(.p-inputtextarea) {
  border-radius: 8px;
  border: 1px solid #dee2e6;
  transition: all 0.2s ease;
}

:deep(.p-inputtext:focus),
:deep(.p-dropdown:focus),
:deep(.p-inputnumber:focus),
:deep(.p-inputtextarea:focus) {
  border-color: #007bff;
  box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

:deep(.p-button) {
  border-radius: 8px;
  font-weight: 500;
  transition: all 0.2s ease;
}

:deep(.p-button.p-button-success) {
  background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
  border: none;
}

:deep(.p-button.p-button-success:hover:not(:disabled)) {
  background: linear-gradient(135deg, #218838 0%, #1ea085 100%);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

:deep(.p-button.p-button-secondary) {
  background: #6c757d;
  border: none;
}

:deep(.p-button.p-button-secondary:hover) {
  background: #5a6268;
  transform: translateY(-1px);
}

:deep(.p-button:disabled) {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none !important;
}

/* Custom Category Selector */
.custom-category-selector {
  position: relative;
  width: 100%;
}

.category-input-container {
  position: relative;
  width: 100%;
}

.category-input {
  position: relative;
  display: flex;
  align-items: center;
  border: 1px solid #ced4da;
  border-radius: 6px;
  background: #fff;
  transition: border-color 0.2s ease;
  cursor: pointer;
}

.category-input:hover {
  border-color: #007bff;
}

.category-input.p-invalid {
  border-color: #e24c4c;
}

.search-icon {
  position: absolute;
  left: 12px;
  color: #6c757d;
  font-size: 14px;
  z-index: 1;
}

.category-search-input {
  width: 100%;
  height: 38px;
  padding: 8px 40px 8px 40px;
  border: none;
  outline: none;
  background: transparent;
  font-size: 14px;
  color: #333;
  cursor: pointer;
}

.category-search-input::placeholder {
  color: #6c757d;
}

.dropdown-icon {
  position: absolute;
  right: 12px;
  color: #6c757d;
  font-size: 12px;
  transition: transform 0.2s ease;
}

.dropdown-icon.rotated {
  transform: rotate(180deg);
}

.category-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: #fff;
  border: 1px solid #ced4da;
  border-top: none;
  border-radius: 0 0 6px 6px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  max-height: 300px;
  overflow-y: auto;
}

.category-dropdown-content {
  padding: 8px 0;
}

.category-option {
  display: flex;
  align-items: center;
  padding: 8px 12px;
  cursor: pointer;
  transition: background-color 0.2s ease;
  position: relative;
}

.category-option:hover {
  background-color: #f8f9fa;
}

.category-option.selected {
  background-color: #e3f2fd;
  color: #1976d2;
}

.expand-icon {
  margin-right: 8px;
  font-size: 12px;
  color: #6c757d;
  transition: transform 0.2s ease;
  width: 16px;
  text-align: center;
}

.expand-icon.expanded {
  transform: rotate(90deg);
}

.category-label {
  font-size: 14px;
  color: #333;
  flex: 1;
}

.category-option.selected .category-label {
  color: #1976d2;
  font-weight: 500;
}

.category-option.has-children .category-label {
  margin-left: 0;
}
</style>