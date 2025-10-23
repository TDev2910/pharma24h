<template>
    <div class="products-page">
      <!-- Header Control Bar -->
      <div class="header-control-bar">
        <div class="controls-section" style="width:100%; display:flex; align-items:center; justify-content:center; gap:16px; flex-wrap:wrap;">
          <!-- Title Section -->
          <div class="title-section">
            <h3>Tổng quan </h3>
          </div>
          
          <!-- Search Section -->
          <div style="flex:1; display:flex; justify-content:center;">
            <div class="search-wrapper" style="width: 100%; max-width: 500px;">
              <div class="input-group">
                <span class="input-group-text">
                  <i class="pi pi-search"></i>
                </span>
                <input 
                  type="text" 
                  class="form-control" 
                  style="border-radius:8px;" 
                  placeholder="Theo mã, tên hàng" 
                  v-model="searchQuery" 
                  @input="debounceSearch"
                >
              </div>
            </div>
          </div>
          
          <!-- Action Buttons -->
          <div class="ultility-options">
            <!-- Dropdown Tạo mới -->
            <div class="dropdown" :class="{ 'show': showDropdown }">
              <Button 
                icon="pi pi-plus"
                label="Tạo mới"
                @click="toggleDropdown"
                severity="secondary"
                style="background:#0b1020; border:none; color:white; font-weight:600; padding:6px 18px; border-radius:8px;"
              />
              
              <!-- Dropdown menu -->
              <div class="dropdown-menu" :class="{ 'show': showDropdown }" v-if="showDropdown">
                <div class="dropdown-item" @click="createMedicine">
                  <i class="pi pi-pill"></i>
                  Thuốc
                </div>
                <div class="dropdown-item" @click="createGoods">
                  <i class="pi pi-box"></i>
                  Vật tư y tế
                </div>
                <div class="dropdown-item" @click="createService">
                  <i class="pi pi-cog"></i>
                  Dịch vụ
                </div>
              </div>
            </div>
            
            
            <!-- Xuất file -->
            <Button 
              icon="pi pi-upload"
              label="Xuất file"
              @click="exportFile"
              severity="secondary"
              style="background:#3A6F43; border:none; color:white; font-weight:600; padding:6px 18px; border-radius:8px;"
            />
            
            <!-- Utility Icons -->
            <div class="utility-icons">
              <button class="btn" title="Chế độ xem">
                <i class="pi pi-list"></i>
              </button>
              <button class="btn" title="Cài đặt">
                <i class="pi pi-cog"></i>
              </button>
              <button class="btn" title="Trợ giúp">
                <i class="pi pi-question-circle"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Main Content với 2 columns -->
      <div class="main-content">
        <!-- Left Sidebar -->
        <div class="left-sidebar">
          <div class="filter-section">
            <label>
              Nhóm hàng
              <a href="#" class="create-link" @click="createCategory">Tạo mới</a>
            </label>
            <div class="category-tree-container">
              <div v-if="loadingCategories" class="category-loading text-center py-3">
                <i class="pi pi-spinner pi-spin"></i>
                <small class="text-muted">Đang tải...</small>
              </div>
              <div v-else-if="categoryTreeNodes.length === 0" class="text-center py-3 text-muted">
                <i class="pi pi-folder-open"></i>
                <small>Không có nhóm hàng nào</small>
              </div>
              <Tree 
                v-else
                :value="categoryTreeNodes" 
                v-model:selectionKeys="selectedCategoryKeys"
                selectionMode="single"
                :metaKeySelection="false"
                @nodeSelect="onCategorySelect"
                @nodeUnselect="onCategoryUnselect"
                class="category-tree"
              >
                <template #default="slotProps">
                  <div class="category-tree-node">
                    <span class="category-tree-name">{{ slotProps.node.label }}</span>
                    <button 
                      class="category-tree-edit" 
                      @click.stop="editCategory(slotProps.node)" 
                      title="Chỉnh sửa"
                    >
                      <i class="pi pi-pencil"></i>
                    </button>
                  </div>
                </template>
              </Tree>
              <div v-if="selectedCategoryName" class="selected-category-info">
                <small class="text-muted">Đã chọn: <strong>{{ selectedCategoryName }}</strong></small>
                <button 
                  type="button" 
                  class="btn-reset-category" 
                  @click="resetCategorySelection" 
                  title="Xóa lựa chọn"
                >
                  <i class="pi pi-times"></i>
                </button>
              </div>
            </div>
          </div>
          
          <div class="filter-section">
            <label>Nhà cung cấp</label>
            <select class="form-select form-select-sm" v-model="filters.manufacturerId" @change="filterProducts">
              <option value="">Chọn nhà cung cấp</option>
              <option v-for="manufacturer in manufacturers" :key="manufacturer.id" :value="manufacturer.id">
                {{ manufacturer.name }}
              </option>
            </select>
          </div>
          
          <div class="filter-section">
            <label>Vị trí</label>
            <select class="form-select form-select-sm" v-model="filters.positionId" @change="filterProducts">
              <option value="">Chọn vị trí</option>
              <option v-for="position in positions" :key="position.id" :value="position.id">
                {{ position.name }}
              </option>
            </select>
          </div>
          <!-- test -->
          <div class="filter-section">
            <h5>Thời gian</h5>
            <div class="radio-options">
              <div class="radio-item">
                <label for="custom">Tùy chỉnh</label>
              </div>
            </div>
            <div v-if="filters.timeRange === 'thisMonth'" class="date-picker-container d-flex align-items-center" style="gap:8px;">
              <DatePicker 
                v-model="filters.fromDate" 
                showIcon 
                fluid 
                iconDisplay="input" 
                placeholder="Từ ngày" 
              />
              <span class="text-muted">→</span>
              <DatePicker 
                v-model="filters.toDate" 
                showIcon 
                fluid 
                iconDisplay="input" 
                placeholder="Đến ngày" 
              />
            </div>
          </div>
        </div>
  
        <!-- Right Main Content -->
        <div class="right-content">
          <DataTable 
            :value="filteredProducts" 
            v-model:expandedRows="expandedRows"
            stripedRows
            responsiveLayout="scroll"
            tableStyle="min-width: 50rem"
            :paginator="true"
            :row="5"
            :rows="pagination.per_page"
            :totalRecords="pagination.total"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
            :rowsPerPageOptions="[5,10,25]"
            currentPageReportTemplate="Hiển thị {first} đến {last} trong tổng số {totalRecords} sản phẩm"
            dataKey="id"
            loadingIcon="pi pi-spinner"
            emptyMessage="Không có dữ liệu sản phẩm">
            
            <Column expander style="width: 3rem" />
            <Column field="image" header="Ảnh">
              <template #body="slotProps">
                <img 
                  v-if="slotProps.data.image" 
                  :src="getImageUrl(slotProps.data.image)" 
                  alt="Product Image" 
                  class="product-image"
                  @error="handleImageError"
                />
                <div v-else class="no-image">
                  <i class="pi pi-image"></i>
                </div>
              </template>
            </Column>
            <Column field="ma_hang" header="Mã hàng"></Column>
            <Column field="ten_thuoc" header="Tên sản phẩm">
              <template #body="slotProps">
                {{ slotProps.data.ten_thuoc || slotProps.data.ten_hang_hoa || '-' }}
              </template>
            </Column>
            <Column field="product_type" header="Loại">
              <template #body="slotProps">
                <span class="badge" :class="getProductTypeBadgeClass(slotProps.data.product_type)">
                  {{ getProductTypeText(slotProps.data.product_type) }}
                </span>
              </template>
            </Column>
            <Column field="ton_kho" header="Tồn kho"></Column>
            <Column field="gia_von" header="Giá vốn">
              <template #body="slotProps">
                {{ formatCurrency(slotProps.data.gia_von) }}
              </template>
            </Column> 
            <Column field="gia_ban" header="Giá bán">
              <template #body="slotProps">
                {{ formatCurrency(slotProps.data.gia_ban) }}
              </template>
            </Column>
            <Column field="created_at" header="Ngày tạo">
              <template #body="slotProps">
                {{ formatDate(slotProps.data.created_at) }}
              </template>
            </Column>
            
            <!-- Hiển thị chi tiết thông tin khi nhấn vào expander -->
            <template #expansion="slotProps">
              <div class="product-detail-container">
                <!-- 2 danh mục thông tin và tồn kho -->
                <div class="detail-tabs">  
                  <button class="tab active" @click="switchTab('info')">Thông tin</button>
                  <button class="tab" @click="switchTab('inventory')">Tồn kho</button>
                </div>
                
                <!-- Danh mục thông tin và tồn kho -->
                <div class="detail-content">
                  <!-- Tab Thông tin -->
                  <div v-if="activeTab === 'info'" class="tab-content">
                    <div class="row">
                      <!-- Thông tin chung -->
                      <div class="col-md-6">
                        <h6 class="text-primary mb-3">
                          <i class="pi pi-info-circle"></i> Thông tin chung
                        </h6>
                        <table class="table table-sm table-borderless">
                          <tbody>
                            <tr>
                              <td class="fw-bold" style="width: 140px;">Mã hàng:</td>
                              <td>{{ slotProps.data.ma_hang || '-' }}</td>
                            </tr>
                            <tr>
                              <td class="fw-bold">Tên sản phẩm:</td>
                              <td>{{ slotProps.data.ten_thuoc || slotProps.data.ten_hang_hoa || '-' }}</td>
                            </tr>
                            <tr>
                              <td class="fw-bold">Tên viết tắt:</td>
                              <td>{{ slotProps.data.ten_viet_tat || '-' }}</td>
                            </tr>
                            <tr>
                              <td class="fw-bold">Loại sản phẩm:</td>
                              <td>
                                <span class="badge" :class="getProductTypeBadgeClass(slotProps.data.product_type)">
                                  {{ getProductTypeText(slotProps.data.product_type) }}
                                </span>
                              </td>
                            </tr>
                            <tr>
                              <td class="fw-bold">Giá vốn:</td>
                              <td>{{ formatCurrency(slotProps.data.gia_von) }}</td>
                            </tr>
                            <tr>
                              <td class="fw-bold">Giá bán:</td>
                              <td>{{ formatCurrency(slotProps.data.gia_ban) }}</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      
                      <!-- Thông tin bổ sung -->
                      <div class="col-md-6">
                        <h6 class="text-primary mb-3">
                          <i class="pi pi-cog"></i> Thông tin bổ sung
                        </h6>
                        <table class="table table-sm table-borderless">
                          <tbody>
                            <tr>
                              <td class="fw-bold">Nhà cung cấp:</td>
                              <td>{{ slotProps.data.manufacturer?.name || '-' }}</td>
                            </tr>
                            <tr>
                              <td class="fw-bold">Vị trí:</td>
                              <td>{{ slotProps.data.position?.name || '-' }}</td>
                            </tr>
                            <tr>
                              <td class="fw-bold">Đường dùng:</td>
                              <td>{{ slotProps.data.drug_route?.name || '-' }}</td>
                            </tr>
                            <tr>
                              <td class="fw-bold">Trọng lượng:</td>
                              <td>{{ slotProps.data.trong_luong ? slotProps.data.trong_luong + 'g' : '-' }}</td>
                            </tr>
                            <tr>
                              <td class="fw-bold">Đơn vị tính:</td>
                              <td>{{ slotProps.data.don_vi_tinh || '-' }}</td>
                            </tr>
                            <tr>
                              <td class="fw-bold">Ngày tạo:</td>
                              <td>{{ formatDate(slotProps.data.created_at) }}</td>
                            </tr>
                          </tbody>
                        </table>
                        
                        <!-- Action buttons chỉnh sửa và xóa -->
                        <div class="mt-3">
                          <Button 
                            label="Chỉnh sửa" 
                            icon="pi pi-pencil" 
                            class="p-button-success p-button-sm me-2"
                            @click="editProduct(slotProps.data)" />                                       
                          <Button 
                            label="Xóa" 
                            icon="pi pi-trash" 
                            class="p-button-danger p-button-sm"
                            @click="deleteProduct(slotProps.data)" />
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Tab Tồn kho -->
                  <div v-if="activeTab === 'inventory'" class="tab-content">
                    <div class="row">
                      <div class="col-md-6">
                        <h6 class="text-primary mb-3">
                          <i class="pi pi-box"></i> Thông tin tồn kho
                        </h6>
                        <table class="table table-sm table-borderless">
                          <tbody>
                            <tr>
                              <td class="fw-bold">Tồn kho hiện tại:</td>
                              <td><span class="badge bg-primary">{{ slotProps.data.ton_kho || 0 }}</span></td>
                            </tr>
                            <tr>
                              <td class="fw-bold">Tồn thấp nhất:</td>
                              <td>{{ slotProps.data.ton_thap_nhat || '-' }}</td>
                            </tr>
                            <tr>
                              <td class="fw-bold">Tồn cao nhất:</td>
                              <td>{{ slotProps.data.ton_cao_nhat || '-' }}</td>
                            </tr>
                            <tr>
                              <td class="fw-bold">Trạng thái tồn kho:</td>
                              <td>
                                <span :class="['badge', getInventoryStatus(slotProps.data).class]">
                                  {{ getInventoryStatus(slotProps.data).label }}
                                </span>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      
                      <div class="col-md-6">
                        <h6 class="text-primary mb-3">
                          <i class="pi pi-chart-line"></i> Thống kê
                        </h6>
                        <div class="text-center text-muted py-4">
                          <i class="pi pi-chart-bar" style="font-size: 2rem;"></i>
                          <p class="mt-2">Biểu đồ thống kê tồn kho sẽ được thêm sau</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </template>
          </DataTable>
        </div>
      </div>
  
      <!-- Modal Tạo nhóm hàng -->
      <CategoryCommodity 
        v-model:visible="showCreateCategoryModal"
        :categoryTreeNodes="categoryTreeNodes"
        @save="onSaveCategory"
        @cancel="onCancelCategory"
      />
      
      <!-- Modal Chỉnh sửa nhóm hàng -->
      <EditCategoryCommodity 
        v-model:visible="showEditCategoryModal"
        :categoryTreeNodes="categoryTreeNodes"
        :categoryData="editingCategory"
        @save="onUpdateCategory"
        @cancel="onCancelEditCategory"
        @delete="onDeleteCategory"
      />
      
      <!-- Modal Tạo thuốc -->
      <CreateMedicine 
        :visible="showCreateMedicineModal"
        @close="showCreateMedicineModal = false"
        @created="onMedicineCreated"
      />

      <!-- Modal Tạo hàng hóa -->
      <CreateGoods
        :visible="showCreateGoodsModal"
        @close="showCreateGoodsModal = false"
        @created="onGoodsCreated"
      />

      <!-- Modal Tạo dịch vụ -->
      <CreateService
        :visible="showCreateServiceModal"
        @close="showCreateServiceModal = false"
        @created="onServiceCreated"
      />

      <MedicineEditModal
        :visible="showEditMedicineModal"
        :medicine-data="editingMedicine"
        @close="showEditMedicineModal = false"
        @edited="onMedicineEdited"
      />

      <GoodsEditModal
        :visible="showEditGoodsModal"
        :goods-data="editingGoods"
        @close="showEditGoodsModal = false"
        @edited="onGoodsEdited"
      />
    </div>
  </template>
  
  <script>
  import Button from 'primevue/button'
  import DatePicker from 'primevue/datepicker'
  import DataTable from 'primevue/datatable'
  import Column from 'primevue/column'
  import Tree from 'primevue/tree'
  import CategoryCommodity from './Modals/CategoryCommodity.vue'
  import EditCategoryCommodity from './Modals/EditCategoryCommodity.vue'
  import CreateMedicine from './Create/Medicine.vue'
  import CreateGoods from './Create/Goods.vue'
  import CreateService from './Create/Services.vue'
  import MedicineEditModal from './Edit/Medicine.vue'
  import GoodsEditModal from './Edit/Goods.vue'
  import axios from 'axios'
  
  export default {
    name: 'ProductManagementDemo',
    components: {
      Button,
      DataTable,
      Column,
      Tree,
      CategoryCommodity,
      EditCategoryCommodity,
      CreateMedicine,
      CreateGoods,
      CreateService,
      MedicineEditModal,
      GoodsEditModal,
      DatePicker
    },
    
    data() {
      return {
        searchQuery: '',
        debounceTimer: null,
        showDropdown: false,
        
        // Sidebar filters
        loadingCategories: false,
        selectedCategoryId: null,
        selectedCategoryName: '',
        selectedCategoryKeys: {},
        categoryTreeNodes: [],
        manufacturers: [],
        positions: [],
        
        // Filter values
        filters: {
          manufacturerId: '',
          positionId: '',
          productType: '',
          timeRange: 'thisMonth',
          // Range filter: từ ngày - đến ngày
          fromDate: null,
          toDate: null
        },
        
        // Modal states
        showCreateCategoryModal: false,
        showEditCategoryModal: false,
        showCreateMedicineModal: false,
        showCreateGoodsModal: false,
        showCreateServiceModal: false,
        showEditMedicineModal: false,
        showEditGoodsModal: false,
        editingCategory: {},
        editingMedicine: null,
        editingGoods: null,
        
        // DataTable states
        products: [],
        expandedRows: {},
        activeTab: 'info',
        pagination: {
          current_page: 1,
          last_page: 1,
          per_page: 10,
          total: 0,
          from: 0,
          to: 0
        }
      }
    },

    computed: {
      //dùng để lọc trên thanh tim kiếm
      filteredProducts() {
        // Nếu không nhập từ khóa, hiển thị tất cả sản phẩm
        if (!this.searchQuery || !this.searchQuery.trim()) {
          return this.products;
        }

        const term = this.searchQuery.toLowerCase().trim();
        return this.products.filter(product => {
          const name = (product.ten_thuoc || '').toLowerCase();
          const code = (product.ma_hang || '').toLowerCase();
          return name.includes(term) || code.includes(term);
        });
      }
    },

    watch: {
      // Watch cho date filters
      'filters.fromDate': {
        handler() {
          this.loadProducts();
        }
      },
      'filters.toDate': {
        handler() {
          this.loadProducts();
        }
      }
    },

    methods: {
      // Format date to local timezone
      formatDateToLocal(dateValue) {
        if (!dateValue) return null;
        const date = new Date(dateValue);
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
      },
      // Xác định trạng thái tồn kho 
      getInventoryStatus(row) {
        const qty = Number(row?.ton_kho ?? 0)
        const min = Number(row?.ton_thap_nhat ?? 0)
        const max = Number(row?.ton_cao_nhat ?? 0)

        if (qty === 0) {
          return { label: 'Hết hàng', class: 'bg-secondary' }
        }

        // Nếu đã cấu hình ngưỡng
        if (min > 0 && max > 0 && max >= min) {
          if (qty <= min) {
            return { label: 'Sắp hết hàng', class: 'bg-warning text-dark' }
          }
          if (qty > max) {
            return { label: 'Tồn vượt mức', class: 'bg-danger' }
          }
          return { label: 'Còn hàng', class: 'bg-success' }
        }

        // Fallback khi chưa có ngưỡng: >0 coi là còn hàng
        return { label: 'Còn hàng', class: 'bg-success' }
      },
      
      // Tìm kiếm với debounce
      debounceSearch() {
        clearTimeout(this.debounceTimer)
        this.debounceTimer = setTimeout(() => {
          this.searchProducts()
        }, 200)
      },
      
      // Tìm kiếm sản phẩm
      searchProducts() {
        console.log('Tìm kiếm:', this.searchQuery)
        // Logic tìm kiếm sẽ được thêm sau
      },
      
      // Toggle dropdown tạo mới
      toggleDropdown() {
        this.showDropdown = !this.showDropdown
      },
      
      // Tạo thuốc mới
      createMedicine() {
        console.log('Tạo thuốc mới')
        this.showDropdown = false
        this.showCreateMedicineModal = true
      },

      editMedicine(medicineData) {
        console.log('Edit medicine:', medicineData)
        this.editingMedicine = medicineData 
        this.showEditMedicineModal = true
      },
      
      // Tạo hàng hóa mới
      createGoods() {
        console.log('Tạo hàng hóa mới')
        this.showDropdown = false
        this.showCreateGoodsModal = true
      },
      
      // Tạo dịch vụ mới
      createService() {
        console.log('Tạo dịch vụ mới')
        this.showDropdown = false
        this.showCreateServiceModal = true
      },
      
      // Import file
      importFile() {
        console.log('Import file')
        // Logic import sẽ được thêm sau
      },
      
      // Export file
      exportFile() {
        console.log('Export file')
        // Logic export sẽ được thêm sau
      },
      
      // Sidebar methods
      async loadCategories() {
        this.loadingCategories = true
        try {
          const response = await axios.get('/admin/categories/modal/data')
          if (response.data.success) {
            this.categoryTreeNodes = this.convertToTreeNodes(response.data.data)
          }
        } catch (error) {
          console.error('Lỗi khi tải danh mục:', error)
          // Fallback to empty array if API fails
          this.categoryTreeNodes = []
        } finally {
          this.loadingCategories = false
        }
      },
      
      // Convert API data to PrimeVue Tree format
      convertToTreeNodes(categories) {
        return categories.map(category => ({
          key: category.id.toString(),
          label: category.name,
          data: { id: category.id, name: category.name },
          children: category.children ? this.convertToTreeNodes(category.children) : undefined
        }))
      },
      
      onCategorySelect(event) {
        if (event && event.node && event.node.data) {
          this.selectedCategoryId = event.node.data.id
          this.selectedCategoryName = event.node.data.name
          this.filterProducts()
        }
      },
      
      onCategoryUnselect(event) {
        this.selectedCategoryId = null
        this.selectedCategoryName = ''
        this.filterProducts()
      },
      
      resetCategorySelection() {
        this.selectedCategoryKeys = {}
        this.selectedCategoryId = null
        this.selectedCategoryName = ''
        this.filterProducts()
      },
      
      editCategory(node) {
        console.log('Chỉnh sửa nhóm hàng:', node.data)
        this.editingCategory = {
          id: node.data.id,
          name: node.data.name,
          parent_id: node.parent ? node.parent.data.id : null
        }
        this.showEditCategoryModal = true
      },
      
      createCategory() {
        this.showCreateCategoryModal = true
      },
      
      async onSaveCategory(categoryData) {
        try {
          const response = await axios.post('/admin/categories', {
            name: categoryData.name,
            parent_id: categoryData.parentId,
            sort_order: 0
          })
          
          if (response.status === 200 || response.status === 201) {
            // Reload categories after successful save
            await this.loadCategories()
            this.showCreateCategoryModal = false
            
            // Show success message (you can use a toast notification library)
            console.log('Tạo nhóm hàng thành công!')
          }
        } catch (error) {
          console.error('Lỗi khi tạo nhóm hàng:', error)
          if (error.response && error.response.data && error.response.data.errors) {
            // Handle validation errors
            const errors = error.response.data.errors
            if (errors.name) {
              alert('Lỗi: ' + errors.name[0])
            } else if (errors.parent_id) {
              alert('Lỗi: ' + errors.parent_id[0])
            }
          } else {
            alert('Có lỗi xảy ra khi tạo nhóm hàng!')
          }
        }
      },
      
      onCancelCategory() {
        this.showCreateCategoryModal = false
      },
      
      async onUpdateCategory(categoryData) {
        try {
          const response = await axios.put(`/admin/categories/${categoryData.id}`, {
            name: categoryData.name,
            parent_id: categoryData.parentId,
            sort_order: 0
          })
          
          if (response.status === 200) {
            // Reload categories after successful update
            await this.loadCategories()
            this.showEditCategoryModal = false
            console.log('Cập nhật nhóm hàng thành công!')
          }
        } catch (error) {
          console.error('Lỗi khi cập nhật nhóm hàng:', error)
          if (error.response && error.response.data && error.response.data.errors) {
            const errors = error.response.data.errors
            if (errors.name) {
              alert('Lỗi: ' + errors.name[0])
            } else if (errors.parent_id) {
              alert('Lỗi: ' + errors.parent_id[0])
            }
          } else {
            alert('Có lỗi xảy ra khi cập nhật nhóm hàng!')
          }
        }
      },
      
      async onDeleteCategory(categoryId) {
        try {
          const response = await axios.delete(`/admin/categories/${categoryId}`)
          
          if (response.status === 200) {
            // Reload categories after successful delete
            await this.loadCategories()
            this.showEditCategoryModal = false
            console.log('Xóa nhóm hàng thành công!')
          }
        } catch (error) {
          console.error('Lỗi khi xóa nhóm hàng:', error)
          alert('Có lỗi xảy ra khi xóa nhóm hàng!')
        }
      },
      
      onCancelEditCategory() {
        this.showEditCategoryModal = false
        this.editingCategory = {}
      },
      
      //create
      //thuốc
      async onMedicineCreated(medicineData) {
        this.showCreateMedicineModal = false
        
        // Thêm item mới vào đầu danh sách thay vì reload toàn bộ
        if (medicineData && medicineData.id) {
          this.products.unshift(medicineData) // Thêm vào đầu danh sách
        } else {
          // Nếu không có data, reload toàn bộ
          await this.loadProducts()
        }
      },

      //vật tư y tế
      async onGoodsCreated(goodsData) {
        this.showCreateGoodsModal = false
        await this.loadProducts() // Reload danh sách
      },
      filterProducts() {
        // Logic lọc sẽ được thêm sau
      },

      async onServiceCreated(serviceData) {
        this.showCreateServiceModal = false
        await this.loadProducts() // Reload danh sách
      },

      //edit
      //thuốc
      async onMedicineEdited(editedData) {
        this.showEditMedicineModal = false
        this.editingMedicine = null
        
        // Reload toàn bộ danh sách để đảm bảo UI cập nhật đúng
        await this.loadProducts()
      },

      //vật tư y tế
      async onGoodsEdited(editedData) {
        this.showEditGoodsModal = false
        this.editingGoods = null
        await this.loadProducts() 
      },

      // DataTable methods
      async loadProducts() {
      try {
        // Format dates theo local timezone
        const fromDate = this.formatDateToLocal(this.filters.fromDate);
        const toDate = this.formatDateToLocal(this.filters.toDate);
          // Thuốc ( thực phẩm chức năng)
          const [medicinesResponse, goodsResponse] = await Promise.all([
              axios.get('/admin/medicines/api', {
                  params: {
                      search: this.searchQuery, //tìm kiếm tên sản phảm , mã sản phẩm
                      category_id: this.selectedCategoryId,
                      manufacturer_id: this.filters.manufacturerId,
                      position_id: this.filters.positionId,
                      from_date: fromDate, 
                      to_date: toDate,   
                      per_page: this.pagination.per_page, 
                      page: this.pagination.current_page,
                  }
              }),
              //vật tư y tế
              axios.get('/admin/goods/api', {
                  params: {
                      search: this.searchQuery,
                      category_id: this.selectedCategoryId,
                      manufacturer_id: this.filters.manufacturerId,
                      position_id: this.filters.positionId,
                      from_date: fromDate, 
                      to_date: toDate,     
                      per_page: this.pagination.per_page,
                      page: this.pagination.current_page,
                  }
              })
          ])
          
          let allProducts = [] // tất cả sản phẩm ( thuốc và vật tư y tế)
          
          // Xử lý medicines data
          if (medicinesResponse.data.success) {
              const medicines = medicinesResponse.data.data.map(medicine => ({
                  ...medicine,
                  product_type: 'medicine',
                  ten_thuoc: medicine.ten_thuoc, 
                  ten_hang_hoa: medicine.ten_thuoc 
              }))
              allProducts = [...allProducts, ...medicines] // thêm thuốc vào tất cả sản phẩm
          }
          
          // Xử lý goods data
          if (goodsResponse.data.success) {
              const goods = goodsResponse.data.data.map(good => ({
                  ...good,
                  product_type: 'goods',
                  ten_thuoc: good.ten_hang_hoa, 
                  ten_hang_hoa: good.ten_hang_hoa
              }))
              allProducts = [...allProducts, ...goods] // thêm vật tư y tế vào tất cả sản phẩm
          }
          
          // sử dụng sort để  xếp theo ngày tạo mới nhất
          this.products = allProducts.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
          
          // Cập nhật pagination (tạm thời dùng tổng số products)
          this.pagination = {
              current_page: 1,
              last_page: Math.ceil(this.products.length / this.pagination.per_page),
              per_page: this.pagination.per_page,
              total: this.products.length,
              from: 1,
              to: this.products.length
          }
          
      } catch (error) {
          console.error('Error loading products:', error)
          this.products = []
      }
    },

      // Tab switching
      switchTab(tab) {
        this.activeTab = tab
      },

      // Get product type text
      getProductTypeText(type) {
        const typeMap = {
          'medicine': 'Thuốc',
          'goods': 'Hàng hóa',
          'service': 'Dịch vụ',
          'combo': 'Combo'
        }
        return typeMap[type] || type || '-'
      },

      // Get product type badge class
      getProductTypeBadgeClass(type) {
        const classMap = {
          'medicine': 'bg-primary',
          'goods': 'bg-success',
          'service': 'bg-info',
          'combo': 'bg-warning'
        }
        return classMap[type] || 'bg-secondary'
      },

      // Format currency
      formatCurrency(value) {
        if (!value) return '0 ₫'
        return new Intl.NumberFormat('vi-VN', {
          style: 'currency',
          currency: 'VND'
        }).format(value)
      },

      // Format date
      formatDate(date) {
        if (!date) return '-'
        return new Date(date).toLocaleDateString('vi-VN')
      },

      // Get image URL
      getImageUrl(imagePath) {
        if (!imagePath) return null
        
        if (imagePath.startsWith('http')) {
          return imagePath
        }
        
        return `/storage/${imagePath}`
      },

      // Handle image error
      handleImageError(event) {
        console.log('Image load error:', event.target.src)
        event.target.style.display = 'none'
      },

      // Edit product
      editProduct(product) {
        // Mở modal edit tương ứng
        if (product.product_type === 'medicine') {
          this.editingMedicine = product
          this.showEditMedicineModal = true
        } else if (product.product_type === 'goods') {
          this.editingGoods = product
          this.showEditGoodsModal = true
        }
      },


      // Delete product
      async deleteProduct(product) {
        const productName = product.ten_thuoc || product.ten_hang_hoa || 'sản phẩm'
        if (confirm(`Bạn có chắc muốn xóa ${productName}?`)) {
          try {
            let response;
            
            // Gọi API xóa tương ứng với loại sản phẩm
            if (product.product_type === 'medicine') {
              response = await axios.delete(`/admin/medicines/${product.id}`)
            } else if (product.product_type === 'goods') {
              response = await axios.delete(`/admin/goods/${product.id}`)
            } else if (product.product_type === 'service') {
              response = await axios.delete(`/admin/services/${product.id}`)
            }
            
            // Kiểm tra phản hồi
            if (response && response.data.success) {
              // Xóa sản phẩm khỏi danh sách local (hiệu quả hơn reload)
              this.products = this.products.filter(p => 
                !(p.id === product.id && p.product_type === product.product_type)
              )
              
              console.log('Xóa sản phẩm thành công:', response.data.message)
              
              // Có thể thêm toast notification ở đây
              // this.$toast.add({severity:'success', summary: 'Thành công', detail: 'Xóa sản phẩm thành công!'})
            }
            
          } catch (error) {
            console.error('Error deleting product:', error)
            
            // Hiển thị thông báo lỗi chi tiết
            if (error.response?.data?.message) {
              alert('Lỗi: ' + error.response.data.message)
            } else {
              alert('Có lỗi xảy ra khi xóa sản phẩm!')
            }
          }
        }
      }
    },
    
    mounted() {
      // Đóng dropdown khi click bên ngoài
      document.addEventListener('click', (event) => {
        if (!event.target.closest('.dropdown')) {
          this.showDropdown = false
        }
      })
      
      // Load categories và products khi component mount
      this.loadCategories()
      this.loadProducts()
    }
  };
  </script>
  
  <style scoped>
  .products-page {
    padding: 20px;
  }
  
  /* Header Control Bar */
  .header-control-bar {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
  }
  
  .controls-section {
    display: flex;
    align-items: center;
    gap: 16px;
  }
  
  .title-section h3 {
    color: #2c3e50;
    margin: 0;
    font-weight: 600;
    font-size: 18px;
  }
  
  /* Search Box */
  .search-wrapper {
    flex: 1;
    max-width: 465px;
    min-width: 280px;
  }
  
  .search-wrapper .input-group {
    position: relative;
  }
  
  .search-wrapper .input-group-text {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    color: #6c757d;
    z-index: 2;
    pointer-events: none;
  }
  
  .search-wrapper .form-control {
    padding-left: 40px !important;
    padding-right: 16px !important;
    border: 2px solid #91C4C3 !important;
    border-radius: 8px !important;
    height: 42px !important;
    font-size: 14px !important;
    background: #fff !important;
    transition: all 0.2s ease !important;
  }
  
  .search-wrapper .form-control:focus {
    border-color: #007bff !important;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1) !important;
    outline: none !important;
  }
  
  /* Utility Options */
  .ultility-options {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  
  .utility-icons {
    display: flex;
    gap: 8px;
  }
  
  .btn {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 8px 10px;
    color: #6c757d;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .btn:hover {
    background: #e9ecef;
    color: #495057;
  }
  
  .btn i {
    font-size: 14px;
  }
  
  /* Dropdown Styles */
  .dropdown {
    position: relative;
    display: inline-block;
  }
  
  .dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    min-width: 150px;
    margin-top: 4px;
    opacity: 0;
    transform: translateY(-10px);
    transition: all 0.2s ease;
    pointer-events: none;
  }
  
  .dropdown-menu.show {
    opacity: 1;
    transform: translateY(0);
    pointer-events: auto;
  }
  
  .dropdown-item {
    padding: 12px 16px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #495057;
    transition: background-color 0.2s ease;
    border-bottom: 1px solid #f8f9fa;
  }
  
  .dropdown-item:last-child {
    border-bottom: none;
  }
  
  .dropdown-item:hover {
    background-color: #f8f9fa;
    color: #007bff;
  }
  
  .dropdown-item i {
    font-size: 14px;
    width: 16px;
  }
  
  /* Main Content Layout */
  .main-content {
    display: flex;
    gap: 20px;
    margin-top: 20px;
  }
  
  /* Left Sidebar */
  .left-sidebar {
    width: 300px;
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
    height: fit-content;
  }
  
  .filter-section {
    margin-bottom: 25px;
  }
  
  .filter-section label {
    color: #2c3e50;
    margin-bottom: 15px;
    font-weight: 600;
    font-size: 16px;
    display: block;
  }
  
  .create-link {
    color: #007bff;
    text-decoration: none;
    font-size: 12px;
    margin-left: 10px;
  }
  
  .create-link:hover {
    text-decoration: underline;
  }
  
  /* Category Tree */
  .category-tree-container {
    border: 1px solid #dee2e6;
    border-radius: 6px;
    background: #fff;
    max-height: 300px;
    overflow-y: auto;
  }
  
  .category-tree {
    border: none !important;
    background: transparent !important;
  }
  
  /* Tree node styling */
  :deep(.p-tree .p-treenode) {
    border: none;
  }
  
  :deep(.p-tree .p-treenode-content) {
    padding: 8px 12px;
    border-radius: 4px;
    margin: 2px 0;
    transition: all 0.2s ease;
  }
  
  
  :deep(.p-tree .p-treenode-content.p-highlight) {
    background-color: #e3f2fd;
    color: #007bff;
  }
  
  .category-tree-node {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
  }
  
  .category-tree-name {
    font-size: 14px;
    color: #495057;
    flex: 1;
  }
  
  .category-tree-edit {
    background: none;
    border: none;
    color: #6c757d;
    cursor: pointer;
    padding: 4px;
    border-radius: 3px;
    transition: all 0.2s ease;
    opacity: 0;
    margin-left: 8px;
  }
  
  .category-tree-node:hover .category-tree-edit {
    opacity: 1;
  }
  
  .category-tree-edit:hover {
    background: #e9ecef;
    color: #007bff;
  }
  
  /* Selected category info */
  .selected-category-info {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
    border-radius: 0 0 6px 6px;
  }
  
  .btn-reset-category {
    background: none;
    border: none;
    color: #6c757d;
    cursor: pointer;
    padding: 2px;
    border-radius: 3px;
    transition: all 0.2s ease;
  }
  
  .btn-reset-category:hover {
    background: #e9ecef;
    color: #dc3545;
  }
  
  /* Form Selects */
  .form-select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    font-size: 14px;
    background: #fff;
    transition: border-color 0.2s ease;
  }
  
  .form-select:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
  }
  
  .form-select-sm {
    padding: 6px 10px;
    font-size: 13px;
  }
  
  /* Right Content */
  .right-content {
    flex: 1;
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
    min-height: 400px;
  }
  
  /* DataTable Styling */
  :deep(.p-datatable) {
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #e9ecef;
  }

  :deep(.p-datatable .p-datatable-header) {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 16px 20px;
  }

  :deep(.p-datatable .p-datatable-thead > tr > th) {
    background: #B4DEBD;
    color: #495057;
    font-weight: 600;
    border-bottom: 2px solid #e9ecef;
    padding: 16px 20px;
    font-size: 14px;
  }

  :deep(.p-datatable .p-datatable-tbody > tr) {
    transition: all 0.2s ease;
    border-bottom: 1px solid #f1f3f4;
  }

  /* Striped rows styling */
  :deep(.p-datatable .p-datatable-tbody > tr:nth-child(even)) {
    background: #f8f9fa;
  }

  :deep(.p-datatable .p-datatable-tbody > tr:nth-child(odd)) {
    background: #ffffff;
  }

  :deep(.p-datatable .p-datatable-tbody > tr:hover) {
    background: #e3f2fd !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  }

  :deep(.p-datatable .p-datatable-tbody > tr > td) {
    padding: 16px 20px;
    color: #495057;
    font-size: 14px;
    vertical-align: middle;
    border-right: 1px solid #e9ecef;
    border-bottom: 1px solid #f1f3f4;
  }

  /* Loại bỏ viền dọc của cột cuối cùng */
  :deep(.p-datatable .p-datatable-tbody > tr > td:last-child) {
    border-right: none;
  }

  /* Product image styling */
  .product-image {
    width: 40px;
    height: 40px;
    border-radius: 6px;
    object-fit: cover;
    border: 2px solid #e9ecef;
  }

  .no-image {
    width: 40px;
    height: 40px;
    border-radius: 6px;
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
  }

  .no-image i {
    font-size: 18px;
  }

  /* Product Detail Container */
  .product-detail-container {
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
    padding: 0;
  }

  .detail-tabs {
    display: flex;
    border-bottom: 1px solid #e9ecef;
    background: #ffffff;
  }

  .detail-tabs .tab {
    background: none;
    border: none;
    padding: 12px 20px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    color: #6c757d;
    border-bottom: 2px solid transparent;
    transition: all 0.2s ease;
  }

  .detail-tabs .tab:hover {
    color: #495057;
    background: #f8f9fa;
  }

  .detail-tabs .tab.active {
    color: #007bff;
    border-bottom-color: #007bff;
    background: #ffffff;
  }

  .detail-content {
    padding: 20px;
    background: #ffffff;
  }

  .tab-content {
    animation: fadeIn 0.3s ease-in-out;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .detail-content .table {
    margin-bottom: 0;
  }

  .detail-content .table td {
    padding: 8px 0;
    border: none;
    vertical-align: top;
  }

  .detail-content .fw-bold {
    color: #495057;
    font-weight: 600;
  }

  .detail-content .badge {
    font-size: 12px;
    padding: 4px 8px;
  }

  .detail-content .text-primary {
    color: #007bff !important;
    font-weight: 600;
  }

  .detail-content .text-primary i {
    font-size: 16px;
  }

  /* Action buttons in detail */
  .detail-content .p-button {
    margin-right: 8px;
    margin-bottom: 8px;
  }

  .detail-content .p-button-sm {
    padding: 6px 12px;
    font-size: 12px;
  }

  /* Badge styling */
  .badge {
    font-size: 11px;
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: 500;
  }

  .bg-primary { background-color: #007bff !important; color: white; }
  .bg-success { background-color: #28a745 !important; color: white; }
  .bg-info { background-color: #17a2b8 !important; color: white; }
  .bg-warning { background-color: #ffc107 !important; color: black; }
  .bg-secondary { background-color: #6c757d !important; color: white; }

  /* Inventory Input styling */
  .inventory-input {
    width: 80px;
    text-align: center;
  }

  :deep(.inventory-input .p-inputnumber-input) {
    text-align: center;
    font-weight: 600;
    color: #007bff;
  }

  :deep(.inventory-input .p-inputnumber-button) {
    width: 20px;
    height: 20px;
    font-size: 12px;
  }

  :deep(.inventory-input .p-inputnumber-button-group) {
    flex-direction: column;
  }

  :deep(.inventory-input .p-inputnumber-button-group .p-button:first-child) {
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
  }

  :deep(.inventory-input .p-inputnumber-button-group .p-button:last-child) {
    border-top-left-radius: 0;
    border-top-right-radius: 0;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .main-content {
      flex-direction: column;
    }
    
    .left-sidebar {
      width: 100%;
    }

    :deep(.p-datatable .p-datatable-thead > tr > th),
    :deep(.p-datatable .p-datatable-tbody > tr > td) {
      padding: 12px 8px;
      font-size: 13px;
    }
    
    .product-image,
    .no-image {
      width: 32px;
      height: 32px;
    }

    .detail-content {
      padding: 15px;
    }

    .detail-tabs .tab {
      padding: 10px 15px;
      font-size: 13px;
    }

    .detail-content .row {
      margin: 0;
    }

    .detail-content .col-md-6 {
      margin-bottom: 20px;
    }
  }

  /* Filter Section */
  .filter-section {
    margin-bottom: 25px;
  }

  .filter-section h5 {
    color: #2c3e50;
    margin-bottom: 15px;
    font-weight: 600;
    font-size: 16px;
  }

  .filter-options {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .checkbox-item, .radio-item {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .checkbox-item input[type="checkbox"],
  .radio-item input[type="radio"] {
    margin: 0;
  }

  .checkbox-item label,
  .radio-item label {
    margin: 0;
    font-size: 14px;
    color: #495057;
    cursor: pointer;
  }

  .radio-options {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  /* Date Picker Container */
  .date-picker-container {
    margin-top: 15px;
  }
  </style>
  