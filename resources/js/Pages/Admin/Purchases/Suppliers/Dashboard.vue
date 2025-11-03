<template>
  <div class="suppliers-page">
    <!-- Header Control Bar -->
    <div class="header-control-bar">
      <div class="controls-section"
        style="width:100%; display:flex; align-items:center; justify-content:center; gap:16px; flex-wrap:wrap;">
        <!-- Title Section -->
        <div class="title-section">
          <h3>Nhà cung cấp</h3>
        </div>
        <!-- Search Section -->
        <div style="flex:1; display:flex; justify-content:center;">
          <div class="search-wrapper" style="width: 100%; max-width: 500px;">
            <div class="input-group">
              <span class="input-group-text">
                <i class="pi pi-search"></i>
              </span>
              <input type="text" class="form-control" style="border-radius:8px;"
                placeholder="Tìm kiếm theo mã, tên nhà cung cấp" v-model="searchQuery" @input="debounceSearch">
            </div>
          </div>

          <!-- Thông báo số kết quả -->
          <div v-if="isSearching" class="search-results-info mt-2 text-center">
            <small class="text-muted">
              Hiển thị {{ searchResultsCount }} / {{ suppliers.length }} kết quả
              <span v-if="!hasSearchResults" class="text-warning"> - Không tìm thấy kết quả nào</span>
            </small>
          </div>
        </div>
        <!-- Utility Options -->
        <div class="ultility-options">
          <!-- Thêm nhà cung cấp -->
          <Button icon="pi pi-plus" label="Nhà cung cấp" @click="openCreateModal" severity="secondary"
            style="background:#0b1020; border:none; color:white; font-weight:600; padding:6px 18px; border-radius:8px;" />
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

    <!-- Main Content with 2 columns -->
    <div class="main-content">
      <!-- Left Sidebar -->
      <div class="left-sidebar">
        <div class="filter-section">
          <h5>Nhóm nhà cung cấp</h5>
          <div class="filter-options">
            <div class="filter-item">
              <label>
                Nhóm
                <a href="#" class="create-link" style="margin-left: 65px;" @click.prevent="openCreateCategoryModal">Tạo
                  mới</a>
              </label>
              <select class="form-select form-select-sm" v-model="filters.supplierGroupId" @change="applyFilters"
                style="margin-top: 8px;">
                <option value="">Chọn nhóm</option>
                <option v-for="group in supplierGroups" :key="group.id" :value="group.id">
                  {{ group.name }}
                </option>
              </select>
            </div>
          </div>
        </div>

        <div class="filter-section">
          <h5>Trạng thái</h5>
          <div class="filter-options">
            <div class="checkbox-item">
              <input type="checkbox" id="active" v-model="filters.active" />
              <label for="active">Đang hoạt động</label>
            </div>
            <div class="checkbox-item">
              <input type="checkbox" id="inactive" v-model="filters.inactive" />
              <label for="inactive">Tạm ngưng</label>
            </div>
            <div class="checkbox-item">
              <input type="checkbox" id="pending" v-model="filters.pending" />
              <label for="pending">Chờ duyệt</label>
            </div>
          </div>
        </div>

        <div class="filter-section">
          <h5>Tỉnh/Thành phố</h5>
          <div class="filter-options">
            <select class="form-select form-select-sm" v-model="filters.province" @change="applyFilters">
              <option value="">Chọn tỉnh/thành</option>
              <option v-for="province in provinces" :key="province.id" :value="province.id">
                {{ province.name }}
              </option>
            </select>
          </div>
        </div>

      </div>

      <!-- Right Main Content -->
      <div class="right-content">
        <!-- DataTable -->
        <div class="table-container">
          <DataTable :value="filteredSuppliers" v-model:expandedRows="expandedRows" stripedRows
            responsiveLayout="scroll" tableStyle="min-width: 50rem" :paginator="true" :row="5"
            :rows="pagination.per_page" :totalRecords="pagination.total"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
            :rowsPerPageOptions="[5, 10, 25]"
            currentPageReportTemplate="Hiển thị {first} đến {last} trong tổng số {totalRecords} nhà cung cấp"
            dataKey="id" loadingIcon="pi pi-spinner" emptyMessage="Không có dữ liệu nhà cung cấp">
            <Column expander style="width: 3rem" />
            <Column header="Mã NCC" style="min-width: 100px;">
              <template #body="slotProps">
                <span class="supplier-code">{{ slotProps.data.ma_nha_cung_cap }}</span>
              </template>
            </Column>
            <Column header="Tên nhà cung cấp" style="min-width: 200px;">
              <template #body="slotProps">
                <span class="supplier-name">{{ slotProps.data.ten_nha_cung_cap }}</span>
              </template>
            </Column>
            <Column header="Số điện thoại">
              <template #body="slotProps">
                <div class="contact-info">
                  <div>{{ slotProps.data.dien_thoai }}</div>
                  <div v-if="slotProps.data.email" class="text-muted small">{{ slotProps.data.email }}</div>
                </div>
              </template>
            </Column>
            <Column header="Địa chỉ">
              <template #body="slotProps">
                <div class="text-truncate" style="max-width: 200px;">
                  <div>{{ slotProps.data.dia_chi }}</div>
                  <small v-if="slotProps.data.khu_vuc" class="text-muted">{{ slotProps.data.khu_vuc }}</small>
                </div>
              </template>
            </Column>
            <Column header="Trạng thái">
              <template #body="slotProps">
                <span v-if="slotProps.data.trang_thai === 'active'" class="badge bg-success">
                  Đang hoạt động
                </span>
                <span v-else class="badge bg-secondary">
                  Không hoạt động
                </span>
              </template>
            </Column>
            <Column header="Thời gian tạo">
              <template #body="slotProps">
                {{ formatDate(slotProps.data.created_at) }}
              </template>
            </Column>

            <!-- Chi tiết supplier (expansion) -->
            <template #expansion="slotProps">
              <div class="supplier-detail-container">
                <div class="detail-tabs">
                  <button class="tab" :class="{ active: activeTab === 'info' }" @click="switchTab('info')">
                    Thông tin
                  </button>
                  <button class="tab" :class="{ active: activeTab === 'imports' }" @click="switchTab('imports')">
                    Lịch sử nhập hàng
                  </button>
                  <button class="tab" :class="{ active: activeTab === 'returns' }" @click="switchTab('returns')">
                    Lịch sử trả hàng
                  </button>
                </div>

                <div class="detail-content">
                  <!-- Tab Thông tin -->
                  <div v-if="activeTab === 'info'" class="tab-content">
                    <div class="row">
                      <!-- Thông tin chung -->
                      <div class="col-md-6">
                        <h6 class="text-primary mb-3">Thông tin chung</h6>
                        <table class="table table-sm table-borderless">
                          <tbody>
                            <tr>
                              <td class="fw-bold" style="width: 140px;">Mã NCC:</td>
                              <td>{{ slotProps.data.ma_nha_cung_cap }}</td>
                            </tr>
                            <tr>
                              <td class="fw-bold">Tên NCC:</td>
                              <td>{{ slotProps.data.ten_nha_cung_cap }}</td>
                            </tr>
                            <tr>
                              <td class="fw-bold">Điện thoại:</td>
                              <td>{{ slotProps.data.dien_thoai }}</td>
                            </tr>
                            <tr v-if="slotProps.data.email">
                              <td class="fw-bold">Email:</td>
                              <td>{{ slotProps.data.email }}</td>
                            </tr>
                            <tr>
                              <td class="fw-bold">Nhóm NCC:</td>
                              <td>
                                <span v-if="slotProps.data.category" class="badge bg-info">
                                  {{ slotProps.data.category.name }}
                                </span>
                                <span v-else class="text-muted">Chưa phân nhóm</span>
                              </td>
                            </tr>
                            <tr>
                              <td class="fw-bold">Trạng thái:</td>
                              <td>
                                <span v-if="slotProps.data.trang_thai === 'active'" class="badge bg-success">
                                  Đang hoạt động
                                </span>
                                <span v-else class="badge bg-secondary">Không hoạt động</span>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>

                      <!-- Thông tin liên hệ -->
                      <div class="col-md-6">
                        <h6 class="text-primary mb-3">Thông tin liên hệ</h6>
                        <table class="table table-sm table-borderless">
                          <tbody>
                            <tr>
                              <td class="fw-bold" style="width: 140px;">Địa chỉ:</td>
                              <td>{{ slotProps.data.dia_chi }}</td>
                            </tr>
                            <tr>
                              <td class="fw-bold">Tỉnh/Thành:</td>
                              <td>{{ slotProps.data.khu_vuc }}</td>
                            </tr>
                            <tr>
                              <td class="fw-bold">Phường/Xã:</td>
                              <td>{{ slotProps.data.phuong_xa || '-' }}</td>
                            </tr>
                            <tr v-if="slotProps.data.ten_cong_ty">
                              <td class="fw-bold">Tên công ty:</td>
                              <td>{{ slotProps.data.ten_cong_ty }}</td>
                            </tr>
                            <tr v-if="slotProps.data.ma_so_thue">
                              <td class="fw-bold">Mã số thuế:</td>
                              <td>{{ slotProps.data.ma_so_thue }}</td>
                            </tr>
                            <tr v-if="slotProps.data.ghi_chu">
                              <td class="fw-bold">Ghi chú:</td>
                              <td>{{ slotProps.data.ghi_chu }}</td>
                            </tr>
                          </tbody>
                        </table>

                        <!-- Action buttons -->
                        <div class="mt-3">
                          <Button icon="pi pi-pencil" label="Chỉnh sửa" @click="openEditModal(slotProps.data)"
                            severity="success" size="small" class="me-2" />
                          <Button icon="pi pi-trash" label="Xóa" @click="deleteSupplier(slotProps.data)"
                            severity="danger" size="small" />
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Tab Lịch sử nhập hàng -->
                  <div v-if="activeTab === 'imports'" class="tab-content">
                    <DataTable 
                      :value="products" 
                      tableStyle="min-width: 50rem" 
                      :loading="loadingImports"
                      loadingIcon="pi pi-spinner" 
                      emptyMessage="Chưa có lịch sử nhập hàng"
                      stripedRows>
                      <Column field="Code" header="Mã phiếu" style="min-width: 120px;">
                        <template #body="slotProps">
                          <span class="fw-bold">{{ slotProps.data.Code }}</span>
                        </template>
                      </Column>
                      
                      <Column field="DayImport" header="Ngày nhập" style="min-width: 120px;"></Column>
                      
                      <Column field="User" header="Người nhập" style="min-width: 150px;">
                        <template #body="slotProps">
                          <span>{{ slotProps.data.User || 'N/A' }}</span>
                        </template>
                      </Column>
                      
                      <Column field="TotalAmount" header="Tổng cộng" style="min-width: 150px;">
                        <template #body="slotProps">
                          <span class="fw-semibold text-end d-block">{{ slotProps.data.TotalAmount }}</span>
                        </template>
                      </Column>
                      
                      <Column field="Status" header="Trạng thái" style="min-width: 120px;">
                        <template #body="slotProps">
                          <span 
                            :class="getStatusClass(slotProps.data.Status)"
                            class="badge">
                            {{ getStatusText(slotProps.data.Status) }}
                          </span>
                        </template>
                      </Column>
                      <Column field="ActionReturn" header="Hành động" style="min-width: 120px;">
                        <template #body="slotProps">
                          <Button icon="pi pi-eye" label="Xem" @click="viewReturn(slotProps.data)"
                            severity="info" size="small" class="me-2" />
                        </template>
                      </Column>                    
                    </DataTable>
                  </div>
                  <!-- Tab Lịch sử trả hàng nhập -->
                  <div v-if="activeTab === 'returns'" class="tab-content">
                    <DataTable 
                      :value="returns" 
                      tableStyle="min-width: 50rem" 
                      :loading="loadingReturns"
                      loadingIcon="pi pi-spinner" 
                      emptyMessage="Chưa có lịch sử trả hàng"
                      stripedRows>
                      <Column field="Code" header="Mã phiếu" style="min-width: 120px;">
                        <template #body="slotProps">
                          <span class="fw-bold">{{ slotProps.data.Code }}</span>
                        </template>
                      </Column>

                      <Column field="DayReturn" header="Ngày trả" style="min-width: 120px;"></Column>
                      
                      <Column field="User" header="Người trả" style="min-width: 150px;">
                        <template #body="slotProps">
                          <span>{{ slotProps.data.UserReturn || 'N/A' }}</span>
                        </template>
                      </Column>
                      
                      <Column field="TotalAmount" header="Tổng cộng" style="min-width: 150px;">
                        <template #body="slotProps">
                          <span class="fw-semibold text-end d-block">{{ slotProps.data.TotalAmountReturn }}</span>
                        </template>
                      </Column>
                      
                      <Column field="Status" header="Trạng thái" style="min-width: 120px;">
                        <template #body="slotProps">
                          <span 
                            :class="getStatusClass(slotProps.data.StatusReturn)"
                            class="badge">
                            {{ getStatusText(slotProps.data.StatusReturn) }}
                          </span>
                        </template>
                      </Column>
                      <Column field="ActionReturn" header="Hành động" style="min-width: 120px;">
                        <template #body="slotProps">
                          <Button icon="pi pi-eye" label="Xem" @click="viewReturn(slotProps.data)"
                            severity="info" size="small" class="me-2" />
                        </template>
                      </Column>
                    </DataTable>
                  </div>
                </div>
              </div>
            </template>
          </DataTable>
        </div>
      </div>
    </div>

    <!-- Supplier Create Modal -->
    <SupplierCreateModal :visible="showCreateModal" :supplierGroups="supplierGroups"
      @update:visible="showCreateModal = $event" @supplier-created="handleSupplierCreated" />

    <SupplierEditModal :visible="showEditModal" :supplier="editingSupplier" :supplierGroups="supplierGroups"
      @update:visible="onEditModalVisibleChange" @supplier-edited="handleSupplierEdited" />

    <!-- Supplier Category Create Modal -->
    <SupplierCategoryCreateModal :visible="showCreateCategoryModal" @update:visible="showCreateCategoryModal = $event"
      @category-created="handleCategoryCreated" />

  </div>
</template>

<script>
import { usePage } from '@inertiajs/vue3'
import { useToast } from 'primevue/usetoast'
import Button from 'primevue/button'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import SupplierCreateModal from './partials/supplier-create-modal.vue'
import SupplierCategoryCreateModal from './partials/modal-category.vue'
import SupplierEditModal from './partials/supplier-edit-modal.vue'
import 'primeicons/primeicons.css'
import axios from 'axios'

export default {
  name: 'SuppliersDashboard',
  components: {
    Button,
    DataTable,
    Column,
    SupplierCreateModal,
    SupplierEditModal,
    SupplierCategoryCreateModal
  },

  setup() {
    const { props } = usePage()
    const toast = useToast()

    return {
      suppliers: props.suppliers || [],
      supplierGroups: props.supplierGroups || [],
      provinces: props.provinces || [],
      paginationData: props.pagination || {},
      toast
    }
  },

  data() {
    return {
      filteredSuppliers: [],
      expandedRows: {},
      activeTab: 'info',
      searchQuery: '',
      isExporting: false,
      showCreateModal: false,
      showEditModal: false,
      showCreateCategoryModal: false,
      editingSupplier: null,
      products: [], // Danh sách imports
      returns: [], // Danh sách returns
      currentSupplierId: null, // Supplier đang xem
      loadingImports: false, // Trạng thái loading
      loadingReturns: false, // Trạng thái loading
      filters: {
        supplierGroupId: '',
        active: true,
        inactive: true,
        pending: false,
        province: ''
      },
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 15,
        total: 0,
        from: 1,
        to: 0
      },
      debounceTimer: null
    }
  },

  methods: {
    // Lọc kết quả
    debounceSearch() {
      clearTimeout(this.debounceTimer)
      this.debounceTimer = setTimeout(() => {
        this.searchSuppliers()
      }, 200)
    },

    matches(item, term) {
      const code = (item.ma_nha_cung_cap || '').toLowerCase()
      const name = (item.ten_nha_cung_cap || '').toLowerCase()
      return code.includes(term) || name.includes(term)
    },

    // Function tìm kiếm 
    searchSuppliers() {
      const term = this.searchQuery.toLowerCase().trim()

      if (!term) {
        // Nếu không có từ khóa, áp dụng filter
        this.applyFilters()
      } else {
        // Lọc kết quả sử dụng hàm matches
        let filtered = [...this.suppliers]

        // Apply other filters first
        if (this.filters.supplierGroupId) {
          filtered = filtered.filter(s => s.nhom_nha_cung_cap_id == this.filters.supplierGroupId)
        }

        // Filter theo trạng thái
        const statusFilters = []
        if (this.filters.active) statusFilters.push('active')
        if (this.filters.inactive) statusFilters.push('inactive')
        if (this.filters.pending) statusFilters.push('pending')

        if (statusFilters.length > 0) {
          filtered = filtered.filter(s => statusFilters.includes(s.trang_thai))
        }

        // Filter theo tỉnh/thành
        if (this.filters.province) {
          filtered = filtered.filter(s => s.khu_vuc_id == this.filters.province)
        }

        // Apply search
        this.filteredSuppliers = filtered.filter(r => this.matches(r, term))
      }
    },

    applyFilters() {
      let filtered = [...this.suppliers]

      // Filter theo nhóm
      if (this.filters.supplierGroupId) {
        filtered = filtered.filter(s => s.nhom_nha_cung_cap_id == this.filters.supplierGroupId)
      }

      // Filter theo trạng thái
      const statusFilters = []
      if (this.filters.active) statusFilters.push('active')
      if (this.filters.inactive) statusFilters.push('inactive')
      if (this.filters.pending) statusFilters.push('pending')

      if (statusFilters.length > 0) {
        filtered = filtered.filter(s => statusFilters.includes(s.trang_thai))
      }

      // Filter theo tỉnh/thành
      if (this.filters.province) {
        filtered = filtered.filter(s => s.khu_vuc_id == this.filters.province)
      }

      // Apply search
      const term = this.searchQuery.toLowerCase().trim()
      if (term) {
        filtered = filtered.filter(supplier => this.matches(supplier, term))
      }

      this.filteredSuppliers = filtered
    },

    // Tab switching
    switchTab(tab) 
    {
      this.activeTab = tab
      // Nếu chuyển sang tab imports và có supplier đang xem, tải dữ liệu imports
      if (tab === 'imports' && this.currentSupplierId) 
      {
        this.loadSupplierImports(this.currentSupplierId)
      }
      // Nếu chuyển sang tab returns và có supplier đang xem, tải dữ liệu returns
      if (tab === 'returns' && this.currentSupplierId) {
        this.loadSupplierReturns(this.currentSupplierId)
      }
    },

    async loadSupplierImports(supplierId) 
    {
      if (!supplierId) return
      this.loadingImports = true
      try 
      {
        const response = await axios.get(`/admin/suppliers/${supplierId}/imports`)
        if (response.data.success) {
          this.products = response.data.data
        }
      } 
      catch (error) 
      {
        console.error('Error loading imports:', error)
        this.products = []
        this.toast.add({
          severity: 'error',
          summary: 'Lỗi',
          detail: 'Không thể tải lịch sử nhập hàng',
          life: 3000
        })
      } 
      finally 
      {
        this.loadingImports = false
      }
    },

    async loadSupplierReturns(supplierId) {
      if (!supplierId) return
      this.loadingReturns = true
      try {
        const response = await axios.get(`/admin/suppliers/${supplierId}/returns`)
        if (response.data.success) {
          this.returns = response.data.data
        }
      } catch (error) {
        console.error('Error loading returns:', error)
        this.returns = []
        this.toast.add({
          severity: 'error',
          summary: 'Lỗi',
          detail: 'Không thể tải lịch sử trả hàng nhập',
          life: 3000
        })
      } finally {
        this.loadingReturns = false
      }
    },

    // Format date
    formatDate(date) {
      if (!date) return '-'
      return new Date(date).toLocaleDateString('vi-VN')
    },

    // Helper methods cho Status
    getStatusText(status) 
    {
      const statusMap = 
      {
        'pending': 'Đang chờ',
        'completed': 'Hoàn thành',
        'cancelled': 'Đã hủy',
        'imported': 'Đã nhập hàng',
        'returned': 'Đã trả hàng',
      }
      return statusMap[status?.toLowerCase()] || status || '-'
    },

    getStatusClass(status) {
      const classMap = {
        'pending': 'bg-warning text-dark',
        'completed': 'bg-success',
        'cancelled': 'bg-danger',
        'imported': 'bg-info',
        'returned': 'bg-info',
        'ordered': 'bg-secondary'
      }
      return classMap[status?.toLowerCase()] || 'bg-secondary'
    },
    

    // Show create modal
    openCreateModal() {
      this.showCreateModal = true
    },

    openEditModal(supplier) {
      this.editingSupplier = supplier
      this.showEditModal = true
    },

    handleSupplierCreated(pageProps = null) {
      // Reload dữ liệu từ server để có supplier mới nhất
      this.$inertia.reload({
        only: ['suppliers', 'supplierGroups', 'pagination'],
        onSuccess: (page) => {
          // Update suppliers từ props mới
          this.suppliers = page.props.suppliers || []

          // Update supplierGroups nếu có thay đổi
          if (page.props.supplierGroups) {
            this.supplierGroups = page.props.supplierGroups
          }

          // Update pagination
          if (page.props.pagination) {
            this.pagination = {
              current_page: page.props.pagination.current_page || 1,
              last_page: page.props.pagination.last_page || 1,
              per_page: page.props.pagination.per_page || 15,
              total: page.props.pagination.total || 0,
              from: page.props.pagination.from || 1,
              to: page.props.pagination.to || 0
            }
          }

          // Cập nhật filteredSuppliers với dữ liệu mới (bao gồm supplier mới tạo)
          this.filteredSuppliers = [...this.suppliers]

          // Áp dụng lại filters nếu có
          if (this.searchQuery || this.filters.supplierGroupId || !this.filters.active || !this.filters.inactive) {
            this.applyFilters()
          }

          // Toast thứ 2
          this.toast.add({
            severity: 'success',
            summary: 'Thành công',
            detail: 'Nhà cung cấp đã được thêm vào danh sách!',
            life: 3000
          })
        }
      })
    },

    onEditModalVisibleChange(visible) {
      this.showEditModal = visible
      if (!visible) {
        // Reset editingSupplier khi đóng modal
        this.editingSupplier = null
      }
    },

    handleSupplierEdited() {
      // Reload data sau khi chỉnh sửa thành công
      this.$inertia.reload({
        only: ['suppliers', 'supplierGroups', 'pagination'],
        onSuccess: (page) => {
          // Update suppliers từ props mới
          this.suppliers = page.props.suppliers || []

          // Update filteredSuppliers
          this.filteredSuppliers = [...this.suppliers]

          // Áp dụng lại filters nếu có
          if (this.searchQuery || this.filters.supplierGroupId || !this.filters.active || !this.filters.inactive) {
            this.applyFilters()
          }

          // Reset editingSupplier
          this.editingSupplier = null

          this.toast.add({
            severity: 'success',
            summary: 'Thành công',
            detail: 'Nhà cung cấp đã được cập nhật!',
            life: 3000
          })
        }
      })
    },

    // Show create category modal
    openCreateCategoryModal() {
      this.showCreateCategoryModal = true
    },

    handleCategoryCreated() {
      // Reload data sau khi tạo category thành công
      this.$inertia.reload({
        only: ['supplierGroups', 'suppliers', 'pagination'],
        onSuccess: (page) => {
          // Update supplierGroups từ page props
          this.supplierGroups = page.props.supplierGroups || []
          this.filteredSuppliers = [...this.suppliers]
          this.toast.add({
            severity: 'success',
            summary: 'Thành công',
            detail: 'Nhóm nhà cung cấp đã được thêm vào danh sách!',
            life: 3000
          })
        }
      })
    },

    handleSupplierEdited() {
      // Reload data sau khi tạo category thành công
      this.$inertia.reload({
        only: ['suppliers', 'supplierGroups', 'pagination'],
      })
    },

    // Delete supplier
    deleteSupplier(supplier) {
      if (confirm(`Bạn có chắc muốn xóa nhà cung cấp ${supplier.ten_nha_cung_cap}?`)) {
        // TODO: Implement delete supplier
        console.log('Delete supplier:', supplier)
        this.$inertia.delete(`/admin/suppliers/${supplier.id}`)
      }
    },


    // Print supplier
    printSupplier(supplier) {
      // TODO: Implement print supplier
      console.log('Print supplier:', supplier)
    },

    // Export to Excel
    exportToExcel() {
      // TODO: Implement export to Excel
      console.log('Export suppliers to Excel')
    },

    // Row toggle handler
    onRowToggle(event) {
      // Reset active tab when expanding a row
      if (event.data) {
        this.activeTab = 'info'
      }
    }
  },

  computed: {
    // Computed property để tối ưu performance
    searchResultsCount() {
      return this.filteredSuppliers.length
    },

    hasSearchResults() {
      return this.searchQuery && this.filteredSuppliers.length > 0
    },

    isSearching() {
      return this.searchQuery && this.searchQuery.trim().length > 0
    }
  },

  watch: {
    'filters.active'() {
      this.applyFilters()
    },
    'filters.inactive'() {
      this.applyFilters()
    },
    'filters.pending'() {
      this.applyFilters()
    },
    expandedRows: {
      handler(newVal) {
        const expandedSupplierIds = Object.keys(newVal).filter(key => newVal[key])
        if (expandedSupplierIds.length > 0) {
          this.currentSupplierId = parseInt(expandedSupplierIds[0])
          // Load imports nếu đang ở tab imports
          if (this.activeTab === 'imports') {
            this.loadSupplierImports(this.currentSupplierId)
          }
          // Nếu chuyển sang tab returns và có supplier đang xem, tải dữ liệu returns
          if (this.activeTab === 'returns') {
            this.loadSupplierReturns(this.currentSupplierId)
          }
        }
        else {
          this.currentSupplierId = null
          this.products = []
          this.returns = [] 
        }
      },
      deep: true
    }
  },

  mounted() {
    // Khởi tạo filteredSuppliers với dữ liệu gốc
    this.filteredSuppliers = [...this.suppliers]

    // Cập nhật pagination từ props
    if (this.paginationData) {
      this.pagination = {
        current_page: this.paginationData.current_page || 1,
        last_page: this.paginationData.last_page || 1,
        per_page: this.paginationData.per_page || 15,
        total: this.paginationData.total || 0,
        from: this.paginationData.from || 1,
        to: this.paginationData.to || 0
      }
    }
  }
}
</script>

<style scoped>
.suppliers-page {
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

.filter-item {
  display: flex;
  flex-direction: column;
}

.filter-item label {
  font-weight: 600;
  font-size: 14px;
  color: #495057;
  margin-bottom: 8px;
}

.checkbox-item,
.radio-item {
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

.create-link {
  color: #007bff;
  text-decoration: none;
  font-size: 12px;
}

.create-link:hover {
  text-decoration: underline;
}

/* Right Content */
.right-content {
  flex: 1;
  flex-direction: column;
  background: #fff;
  border-radius: 12px;
  padding: 15px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  border: 1px solid #e9ecef;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Table Container */
.table-container {
  width: 100%;
}

/* DataTable Styling */
:deep(.p-datatable) {
  border-radius: 8px;
  overflow: hidden;
  border: 2px solid #000;
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

.supplier-code {
  font-weight: 600;
  color: #495057;
}

.supplier-name {
  font-weight: 500;
  color: #2c3e50;
}

.contact-info {
  font-size: 14px;
}

/* Supplier Detail Container */
.supplier-detail-container {
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
  from {
    opacity: 0;
    transform: translateY(10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
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

/* Responsive */
@media (max-width: 768px) {
  .main-content {
    flex-direction: column;
  }

  .left-sidebar {
    width: 100%;
  }

  .right-content {
    padding: 20px;
  }
}
</style>