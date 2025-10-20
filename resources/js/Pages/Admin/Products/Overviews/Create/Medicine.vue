<template>
   <Dialog 
     :visible="visible" 
     @update:visible="$emit('close')"
     header="Tạo thuốc" 
     :style="{ width: '900px' }"
     modal
     :closable="true"
   >
    <div class="flex gap-6">
      <!-- Left Section: Form Fields -->
      <div class="form-grid" style="flex: 1;">
        <!-- Tabs -->
        <div class="tabs-container">
          <div class="tabs-header">
            <button 
              class="tab-button" 
              :class="{ active: activeTab === 'info' }"
              @click="activeTab = 'info'"
            >
              Thông tin
            </button>
            <button 
              class="tab-button" 
              :class="{ active: activeTab === 'description' }"
              @click="activeTab = 'description'"
            >
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
                      <InputText
                        v-model="formData.ma_hang"
                        placeholder="Tự động"
                        readonly
                        class="field-input readonly-input"
                      />
                      <Button 
                        label="Tạo mã"
                        icon="pi pi-refresh"
                        @click="generateMedicineCode"
                        severity="secondary"
                        size="small"
                      />
                    </div>
                  </div>
                  
                  <div class="form-field">
                    <label class="field-label">Mã vạch</label>
                    <div class="input-group">
                      <InputText
                        v-model="formData.ma_vach"
                        placeholder="Tự động"
                        readonly
                        class="field-input readonly-input"
                      />
                      <Button 
                        label="Tạo mã"
                        icon="pi pi-refresh"
                        @click="generateMedicineBarcode"
                        severity="secondary"
                        size="small"
                      />
                    </div>
                  </div>
                </div>

                <div class="form-field full-width">
                  <label class="field-label">Tên thuốc <span class="text-danger">*</span></label>
                  <InputText
                    v-model="formData.ten_thuoc"
                    placeholder="Nhập tên thuốc"
                    class="field-input"
                    :class="{ 'p-invalid': errors.ten_thuoc }"
                  />
                  <small v-if="errors.ten_thuoc" class="p-error">{{ errors.ten_thuoc[0] }}</small>
                </div>

                <div class="form-row">
                  <div class="form-field">
                    <label class="field-label">Tên viết tắt</label>
                    <InputText
                      v-model="formData.ten_viet_tat"
                      placeholder="Nhập tên viết tắt"
                      class="field-input" style="width: 240px;"
                    />
                  </div>
                  
                  <div class="form-field">
                    <label class="field-label">Nhóm hàng <span class="text-danger">*</span></label>
                    <div class="custom-category-selector" :class="{ 'p-invalid': errors.nhom_hang_id }">
                      <div class="category-input-container" @click="toggleCategoryDropdown">
                        <div class="category-input">
                          <i class="pi pi-search search-icon"></i>
                          <input 
                            type="text" 
                            v-model="categorySearchText"
                            :placeholder="selectedCategoryName || 'Chọn nhóm hàng (Bắt buộc)'"
                            readonly
                            class="category-input-field" style="width: 260px;"
                          />
                          <i class="pi pi-chevron-up dropdown-icon" :class="{ 'rotated': showCategoryDropdown }"></i>
                        </div>
                      </div>
                      
                      <div v-if="showCategoryDropdown" class="category-dropdown">
                        <div class="category-dropdown-content">
                          <div 
                            v-for="node in filteredCategoryNodes" 
                            :key="node.key"
                            class="category-option"
                            :class="{ 
                              'selected': selectedCategoryKeys[node.key],
                              'has-children': node.children && node.children.length > 0
                            }"
                            :style="{ paddingLeft: (getNodeLevel(node) * 20 + 12) + 'px' }"
                            @click="selectCategory(node)"
                          >
                            <i 
                              v-if="node.children && node.children.length > 0" 
                              class="pi pi-chevron-right expand-icon"
                              :class="{ 'expanded': node.expanded }"
                              @click.stop="toggleNode(node)"
                            ></i>
                            <span class="category-label">{{ node.label }}</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <small v-if="errors.nhom_hang_id" class="p-error">{{ errors.nhom_hang_id[0] }}</small>
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
                      <Button 
                        label="Xóa" 
                        @click.stop="removeImage" 
                        size="small" 
                        severity="danger"
                        class="remove-btn"
                      />
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
                <InputNumber
                  v-model="formData.gia_von"
                  mode="currency"
                  currency="VND"
                  locale="vi-VN"
                  class="price-input"
                  :class="{ 'p-invalid': errors.gia_von }"
                />
              </div>
              
              <div class="form-field">
                <label class="field-label">Giá bán <span class="text-danger">*</span></label>
                <div class="input-group">
                  <InputNumber
                    v-model="formData.gia_ban"
                    mode="currency"
                    currency="VND"
                    locale="vi-VN"
                    class="price-input"
                    :class="{ 'p-invalid': errors.gia_ban }"
                  />
                </div>
              </div>
            </div>
          </fieldset>

          <!-- Thông tin thuốc -->
          <div class="form-section" style="margin-top: 20px;" >
            <div class="section-header">
              <span class="section-title">Thông tin thuốc</span>
            </div>
            <div class="form-row three-columns">
              <div class="form-field">
                <label class="field-label">Số đăng ký <span class="text-danger">*</span></label>
                <InputText
                  v-model="formData.so_dang_ky"
                  placeholder="Bắt buộc"
                  class="field-input"
                  :class="{ 'p-invalid': errors.so_dang_ky }"
                />
                <small v-if="errors.so_dang_ky" class="p-error">{{ errors.so_dang_ky[0] }}</small>
              </div>
              
              <div class="form-field">
                <label class="field-label">Hoạt chất <span class="text-danger">*</span></label> 
                <InputText
                  v-model="formData.hoat_chat"
                  placeholder="Bắt buộc"
                  class="field-input"
                  :class="{ 'p-invalid': errors.hoat_chat }"
                />
                <small v-if="errors.hoat_chat" class="p-error">{{ errors.hoat_chat[0] }}</small>
              </div>
                          
              <div class="form-field">
                <label class="field-label">Hàm lượng <span class="text-danger">*</span></label>
                <InputText
                  v-model="formData.ham_luong"
                  placeholder="Bắt buộc"
                  class="field-input"
                  :class="{ 'p-invalid': errors.ham_luong }"
                />
                <small v-if="errors.ham_luong" class="p-error">{{ errors.ham_luong[0] }}</small>
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="field-label">Đường dùng <span class="text-danger">*</span></label>
                <div class="input-group">
                  <Dropdown
                    v-model="formData.drugusage_id"
                    :options="drugRouteOptions"
                    optionLabel="name"
                    optionValue="id"
                    placeholder="Bắt buộc"
                    class="field-input"
                    :class="{ 'p-invalid': errors.drugusage_id }"
                  />
                  <Button 
                    icon="pi pi-cog"
                    @click="openDrugRouteModal"
                    severity="secondary"
                    size="small"
                    title="Quản lý"
                  />
                </div>
                <small class="text-muted">Thêm/Sửa/Xóa thực hiện trong cửa sổ quản lý.</small>
                <small v-if="errors.drugusage_id" class="p-error">{{ errors.drugusage_id[0] }}</small>
              </div>
              
              <div class="form-field">
                <label class="field-label">Hãng sản xuất <span class="text-danger">*</span></label>
                <div class="input-group">
                  <Dropdown
                    v-model="formData.manufacturer_id"
                    :options="manufacturerOptions"
                    optionLabel="name"
                    optionValue="id"
                    placeholder="Tìm hãng sản xuất"
                    class="field-input"
                    :class="{ 'p-invalid': errors.manufacturer_id }"
                  />
                  <Button 
                    icon="pi pi-cog"
                    @click="openManufacturerModal"
                    severity="secondary"
                    size="small"
                    title="Quản lý"
                  />
                </div>
                <small class="text-muted">Thêm/Sửa/Xóa thực hiện trong cửa sổ quản lý.</small>
                <small v-if="errors.manufacturer_id" class="p-error">{{ errors.manufacturer_id[0] }}</small>
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="field-label">Quy cách đóng gói <span class="text-danger">*</span></label>
                <InputText
                  v-model="formData.quy_cach_dong_goi"
                  placeholder="Bắt buộc"
                  class="field-input"
                  :class="{ 'p-invalid': errors.quy_cach_dong_goi }"
                />
                <small v-if="errors.quy_cach_dong_goi" class="p-error">{{ errors.quy_cach_dong_goi[0] }}</small>
              </div>
              
              <div class="form-field">
                <label class="field-label">Nước sản xuất</label>
                <InputText
                  v-model="formData.nuoc_san_xuat"
                  placeholder="Tìm nước sản xuất"
                  class="field-input"
                />
              </div>
            </div>
          </div>

          <!-- Tồn kho -->
          <fieldset class="mb-4 border rounded p-3">
            <legend class="float-none w-auto px-2 fs-6">Tồn kho</legend>
            <div class="form-row three-columns">
              <div class="form-field">
                <label class="field-label">Tồn kho</label>
                <InputText
                  v-model="formData.ton_kho"
                  placeholder="0"
                  readonly
                  class="field-input readonly-input"
                />
                <small class="text-muted">Số lượng hiện có trong kho</small>
              </div>
              
              <div class="form-field">
                <label class="field-label">Định mức tồn thấp nhất</label>
                <InputText
                  v-model="formData.ton_thap_nhat"
                  placeholder="Nhập số lượng"
                  class="field-input"
                />
                <small class="text-muted">Cảnh báo khi ≤ số này</small>
              </div>
              
              <div class="form-field">
                <label class="field-label">Định mức tồn cao nhất</label>
                <InputText
                  v-model="formData.ton_cao_nhat"
                  placeholder="Nhập số lượng"
                  class="field-input"
                />
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
                  <Dropdown
                    v-model="formData.position_id"
                    :options="positionOptions"
                    optionLabel="name"
                    optionValue="id"
                    placeholder="Chọn vị trí"
                    class="field-input"
                    :class="{ 'p-invalid': errors.position_id }"
                  />
                  <Button 
                    icon="pi pi-cog"
                    @click="openPositionModal"
                    severity="secondary"
                    size="small"
                    title="Quản lý"
                  />
                </div>
                <small class="text-muted">Thêm/Sửa/Xóa thực hiện trong cửa sổ quản lý.</small>
                <small v-if="errors.position_id" class="p-error">{{ errors.position_id[0] }}</small>
              </div>
              
              <div class="form-field">
                <label class="field-label">Trọng lượng</label>
                <div style="display: flex; align-items: center; max-width: 350px;">
                  <InputText
                    v-model="formData.trong_luong"
                    :min="0"
                    class="field-input"
                    style="flex: 1 1 0; border-top-right-radius: 0; border-bottom-right-radius: 0;"
                  />
                  <span
                    class="input-group-text"
                    style="background: #f6f7f9; border: 1px solid #ced4da; border-left: none; border-radius: 0 6px 6px 0; padding: 0 14px; font-size: 14px; height: 38px; display: flex; align-items: center;"
                  >g</span>
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
                <InputText
                  v-model="formData.don_vi_tinh"
                  placeholder="Nhập đơn vị tính"
                  readonly
                  class="field-input readonly-input"
                />
              </div>
              <div class="form-field">
                <Button 
                  label="Thiết lập"
                  @click="openUnitModal"
                  severity="secondary"
                  class="w-full"
                />
              </div>
            </div>
          </div>

          <!-- Bán trực tiếp -->
          <div class="form-section">
            <div class="checkbox-field">
              <Checkbox
                v-model="formData.ban_truc_tiep"
                :binary="true"
                inputId="ban_truc_tiep"
              />
              <label for="ban_truc_tiep" class="checkbox-label">Bán trực tiếp</label>
            </div>
          </div>
        </div>

        <!-- Tab Mô tả -->
        <div v-if="activeTab === 'description'" class="tab-content">
          <div class="form-section">
            <div class="form-field full-width">
              <label class="field-label">Mô tả sản phẩm</label>
              <Editor
                v-model="formData.mo_ta"
                editorStyle="height: 320px"
                placeholder="Nhập mô tả sản phẩm"
                class="field-editor"
                :class="{ 'p-invalid': errors.mo_ta }"
              />
              <small v-if="errors.mo_ta" class="p-error">{{ errors.mo_ta[0] }}</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Unit of Calculation Modal -->
    <UnitOfCalculation 
      :visible="showUnitModal"
      @close="showUnitModal = false"
      @saved="onUnitSaved"
    />

    <!-- Drug Route Modal -->
    <ModalUsageRoute 
      :visible="showDrugRouteModal"
      @close="showDrugRouteModal = false"
      @drug-route-added="onDrugRouteUpdated" 
      @drug-route-updated="onDrugRouteUpdated"
    />

    <!-- Manufacturer Modal -->
    <ModalManufacturer 
      :visible="showManufacturerModal"
      @close="showManufacturerModal = false"
      @manufacturer-added="onManufacturerUpdated"
      @manufacturer-updated="onManufacturerUpdated"
    />

    <!-- Position Modal -->
    <ModalPosition 
      :visible="showPositionModal"
      @close="showPositionModal = false"
      @position-added="onPositionUpdated"
      @position-updated="onPositionUpdated"
    />

    <!-- Thông báo lỗi -->
    <div v-if="Object.keys(errors).length > 0" class="error-messages">
      <div class="error-title">Vui lòng kiểm tra lại thông tin:</div>
      <ul class="error-list">
        <li v-for="(errorMessages, field) in errors" :key="field" class="error-item">
          {{ errorMessages[0] }}
        </li>
      </ul>
    </div>

    <template #footer>
      <div class="flex justify-end gap-2">
        <Button 
          type="button" 
          label="Hủy" 
          severity="secondary" 
          @click="closeModal"
        />
        <Button 
          type="button" 
          label="Lưu thuốc" 
          @click="saveMedicine"
          :loading="loading"
        />
      </div>
    </template>
  </Dialog>
  <Toast />
</template>
  
<script>
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import InputNumber from 'primevue/inputnumber'
import Dropdown from 'primevue/dropdown'
import Textarea from 'primevue/textarea'
import Checkbox from 'primevue/checkbox'
import Editor from 'primevue/editor'
import Tree from 'primevue/tree'
import Toast from 'primevue/toast'
import UnitOfCalculation from '../Modals/UnitofCalculation.vue'
import ModalUsageRoute from '../Modals/Catalogs/ModalCatalogUsageRoute.vue'
import ModalManufacturer from '../Modals/Catalogs/ModalCatalogManufacturer.vue'
import ModalPosition from '../Modals/Catalogs/ModalCatalogPosition.vue'
import axios from 'axios'

  export default {
  name: 'CreateMedicineModal',
  components: {
    Dialog,
    Button,
    InputText,
    InputNumber,
    Dropdown,
    Textarea,
    Checkbox,
    Editor,
    Tree,
    Toast,
    UnitOfCalculation,
    ModalUsageRoute,
    ModalManufacturer,
    ModalPosition
  },
  props: {
    visible: {
      type: Boolean,
      default: false
    }
  },
  emits: ['close', 'created', 'update:visible'],
  data() {
    return {
      loading: false,
      activeTab: 'info',
      formData: {
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
      },
      errors: {},
      imagePreview: null,
      categoryOptions: [],
      categoryTreeNodes: [],
      selectedCategoryKeys: {},
      selectedCategoryName: '',
      showCategoryDropdown: false,
      categorySearchText: '',
      filteredCategoryNodes: [],
      drugRouteOptions: [],
      manufacturerOptions: [],
      positionOptions: [],
      showUnitModal: false,
      showDrugRouteModal: false,
      showManufacturerModal: false,
      showPositionModal: false
    }
  },
  watch: {
    visible(newVal) {
      if (newVal) {
        this.loadInitialData()
      }
    }
  },
  
  mounted() {
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
    
     async saveMedicine(event) {
       // Chặn submit mặc định
       if (event) {
         event.preventDefault()
         event.stopPropagation()
       }
       
       this.loading = true
       this.errors = {}
       
       try {
         const formData = new FormData()
         
         // Append all form data
         Object.keys(this.formData).forEach(key => {
           if (this.formData[key] !== null && this.formData[key] !== '') {
             formData.append(key, this.formData[key])
           }
         })
         
         const response = await axios.post('/admin/medicines', formData, {
           headers: {
             'Content-Type': 'multipart/form-data'
           }
         })
         
        if (response.data.success) {
          // Hiển thị thông báo thành công
          this.$toast.add({
            severity: 'success',
            summary: 'Thành công',
            detail: response.data.message,
            life: 3000
          })
          
          this.$emit('created', response.data.data)
          this.closeModal()
        }
       } catch (error) {
         console.error('Error creating medicine:', error)
         
        if (error.response && error.response.status === 422) {
          // Validation errors
          this.errors = error.response.data.errors
          this.$toast.add({
            severity: 'error',
            summary: 'Lỗi validation',
            detail: 'Vui lòng kiểm tra lại thông tin nhập vào',
            life: 5000
          })
        } else {
          // Other errors
          this.$toast.add({
            severity: 'error',
            summary: 'Lỗi',
            detail: error.response?.data?.message || 'Có lỗi xảy ra khi thêm thuốc',
            life: 5000
          })
        }
       } finally {
         this.loading = false
       }
     },
    
    async loadInitialData() {
      try {
        // Load categories
        const categoriesResponse = await axios.get('/admin/categories/modal/data')
        if (categoriesResponse.data.success) {
          this.categoryOptions = this.convertToDropdownOptions(categoriesResponse.data.data)
          this.categoryTreeNodes = this.convertToTreeNodes(categoriesResponse.data.data)
          this.filteredCategoryNodes = this.flattenTreeNodes(this.categoryTreeNodes)
        }
        
        // Load drug routes
        const drugRoutesResponse = await axios.get('/admin/products/drugroute')
        this.drugRouteOptions = drugRoutesResponse.data.data || []
        
        // Load manufacturers
        const manufacturersResponse = await axios.get('/admin/products/manufacturer')
        this.manufacturerOptions = manufacturersResponse.data.data || []
        
        // Load positions
        const positionsResponse = await axios.get('/admin/products/position')
        this.positionOptions = positionsResponse.data.data || []
        
      } catch (error) {
        console.error('Error loading initial data:', error)
      }
    },
    
    convertToDropdownOptions(categories) {
      const options = []
      const addToOptions = (nodes, level = 0) => {
        nodes.forEach(node => {
          const prefix = '─ '.repeat(level)
          options.push({
            label: prefix + node.name,
            value: node.id
          })
          if (node.children && node.children.length > 0) {
            addToOptions(node.children, level + 1)
          }
        })
      }
      addToOptions(categories)
      return options
    },
    
    convertToTreeNodes(categories) {
      return categories.map(category => ({
        key: category.id.toString(),
        label: category.name,
        data: { id: category.id, name: category.name },
        children: category.children ? this.convertToTreeNodes(category.children) : undefined
      }))
    },
    
    onCategorySelect(node) {
      this.formData.nhom_hang_id = node.data.id
      this.selectedCategoryName = node.data.name
      // Clear any existing errors
      if (this.errors.nhom_hang_id) {
        delete this.errors.nhom_hang_id
      }
    },
    
    flattenTreeNodes(nodes, level = 0) {
      let result = []
      nodes.forEach(node => {
        const flatNode = {
          ...node,
          level: level,
          expanded: false
        }
        result.push(flatNode)
        if (node.children && node.children.length > 0) {
          result = result.concat(this.flattenTreeNodes(node.children, level + 1))
        }
      })
      return result
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
      node.expanded = !node.expanded
      this.updateFilteredNodes()
    },
    
    updateFilteredNodes() {
      this.filteredCategoryNodes = this.flattenTreeNodes(this.categoryTreeNodes)
    },
    
    getNodeLevel(node) {
      return node.level || 0
    },
    
     handleClickOutside(event) {
       // Sử dụng document.querySelector thay vì this.$el.querySelector
       const categorySelector = document.querySelector('.custom-category-selector')
       if (categorySelector && !categorySelector.contains(event.target)) {
         this.showCategoryDropdown = false
       }
     },
     
    
    async generateMedicineCode() {
      try {
        const response = await axios.get('/admin/medicines/generate-codes')
        if (response.data.ma_hang) {
          this.formData.ma_hang = response.data.ma_hang
        }
      } catch (error) {
        console.error('Error generating medicine code:', error)
      }
    },
    
    async generateMedicineBarcode() {
      try {
        const response = await axios.get('/admin/medicines/generate-codes')
        if (response.data.ma_vach) {
          this.formData.ma_vach = response.data.ma_vach
        }
      } catch (error) {
        console.error('Error generating medicine barcode:', error)
      }
    },
    
    handleImageUpload() {
      const input = document.createElement('input')
      input.type = 'file'
      input.accept = 'image/*'
      
      input.onchange = (event) => {
        const file = event.target.files[0]
        if (!file) return
        
        // Validate file
        if (!file.type.startsWith('image/')) {
          alert('Chỉ chấp nhận file ảnh!')
          return
        }
        
        if (file.size > 2 * 1024 * 1024) { // 2MB
          alert('File quá lớn! Tối đa 2MB')
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
      
      input.click()
    },
    
    removeImage() {
      this.formData.image = null
      this.imagePreview = null
    },
    
    openDrugRouteModal() {
      this.showDrugRouteModal = true
    },
    
    async onDrugRouteUpdated(newData) {
      try {
        const drugRoutesResponse = await axios.get('/admin/products/drugroute')
        this.drugRouteOptions = drugRoutesResponse.data.data || []  
        // nhận dữ liệu đầu vào sau khi submit modal và cập nhật dữ liệu mới vào form
        if (newData && newData.id) {
          this.form.drug_route_id = newData.id
        }
      } catch (error) {
        console.error('Error reloading drug routes:', error)
      }
    },
    
    openManufacturerModal() {
      this.showManufacturerModal = true
    },
    
    async onManufacturerUpdated(newData) {
      // Reload manufacturers when updated
      try {
        const manufacturersResponse = await axios.get('/admin/products/manufacturer')
        this.manufacturerOptions = manufacturersResponse.data.data || []
        
        // nhận dữ liệu đầu vào sau khi submit modal và cập nhật dữ liệu mới vào form
        if (newData && newData.id) {
          this.form.manufacturer_id = newData.id
        }
      } catch (error) {
        console.error('Error reloading manufacturers:', error)
      }
    },
    
    openPositionModal() {
      this.showPositionModal = true
    },
    
    async onPositionUpdated(newData) {
      // Reload positions when updated
      try {
        const positionsResponse = await axios.get('/admin/products/position')
        this.positionOptions = positionsResponse.data.data || []
        
        // nhận dữ liệu đầu vào sau khi submit modal và cập nhật dữ liệu mới vào form
        if (newData && newData.id) {
          this.form.position_id = newData.id
        }
      } catch (error) {
        console.error('Error reloading positions:', error)
      }
    },
    
    openUnitModal() {
      this.showUnitModal = true
    },
    
    onUnitSaved(unitData) {
      this.formData.don_vi_tinh = unitData.unitName
      console.log('Unit saved:', unitData)
    },
    
    resetForm() {
      this.formData = {
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
      }
      this.errors = {}
      this.imagePreview = null
      this.activeTab = 'info'
      this.selectedCategoryKeys = {}
      this.selectedCategoryName = ''
      this.showCategoryDropdown = false
      this.categorySearchText = ''
      this.showUnitModal = false
      this.showDrugRouteModal = false
      this.showManufacturerModal = false
      this.showPositionModal = false
    }
  }
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

.category-input-field {
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

.category-input-field::placeholder {
  color: #6c757d;
}

.dropdown-icon {
  position: absolute;
  right: 12px;
  color: #6c757d;
  font-size: 12px;
  transition: transform 0.2s ease;
  z-index: 1;
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
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  max-height: 200px;
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