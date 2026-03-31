<script setup>
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'

const props = defineProps({
  products: Array,
  pagination: Object,
  expandedRows: Object,
  activeTab: String,
  // Helper functions
  getImageUrl: Function,
  handleImageError: Function,
  getProductTypeBadgeClass: Function,
  getProductTypeText: Function,
  formatCurrency: Function,
  formatDate: Function,
  getInventoryStatus: Function
})

const emit = defineEmits([
  'update:expandedRows',
  'switch-tab',
  'edit-product',
  'delete-product'
])
</script>

<template>
  <div class="right-content">
    <DataTable :value="products" :expandedRows="expandedRows"
      @update:expandedRows="$emit('update:expandedRows', $event)" stripedRows responsiveLayout="scroll"
      tableStyle="min-width: 50rem" :paginator="true" :rows="pagination.per_page" :totalRecords="pagination.total"
      paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
      :rowsPerPageOptions="[5, 10, 25]"
      currentPageReportTemplate="Hiển thị {first} đến {last} trong tổng số {totalRecords} sản phẩm" dataKey="id"
      loadingIcon="pi pi-spinner" emptyMessage="Không có dữ liệu sản phẩm">

      <Column expander style="width: 3rem" />
      <Column field="image" header="Ảnh">
        <template #body="slotProps">
          <img v-if="slotProps.data.image" :src="getImageUrl(slotProps.data.image)" alt="Product Image"
            class="product-image" @error="handleImageError" />
          <div v-else class="no-image">
            <i class="pi pi-image"></i>
          </div>
        </template>
      </Column>
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

      <!-- Expander Detail Area -->
      <template #expansion="slotProps">
        <div class="product-detail-container">
          <div class="detail-tabs">
            <button class="tab" :class="{ active: activeTab === 'info' }" @click="$emit('switch-tab', 'info')">Thông
              tin</button>
            <button class="tab" :class="{ active: activeTab === 'inventory' }"
              @click="$emit('switch-tab', 'inventory')">Tồn kho</button>
          </div>

          <div class="detail-content">
            <!-- Tab Information -->
            <div v-if="activeTab === 'info'" class="tab-content">
              <div class="row">
                <div class="col-md-6">
                  <h6 class="text-primary mb-3"><i class="pi pi-info-circle"></i> Thông tin chung</h6>
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
                          <span class="badge" :class="getProductTypeBadgeClass(slotProps.data.product_type)">{{
                            getProductTypeText(slotProps.data.product_type) }}</span>
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
                <div class="col-md-6">
                  <h6 class="text-primary mb-3"><i class="pi pi-cog"></i> Thông tin bổ sung</h6>
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
                  <div class="mt-3">
                    <Button label="Chỉnh sửa" icon="pi pi-pencil" class="p-button-success p-button-sm me-2"
                      @click="$emit('edit-product', slotProps.data)" />
                    <Button label="Xóa" icon="pi pi-trash" class="p-button-danger p-button-sm"
                      @click="$emit('delete-product', slotProps.data)" />
                  </div>
                </div>
              </div>
            </div>

            <!-- Tab Inventory -->
            <div v-if="activeTab === 'inventory'" class="tab-content">
              <div class="row">
                <div class="col-md-6">
                  <h6 class="text-primary mb-3"><i class="pi pi-box"></i> Thông tin tồn kho</h6>
                  <table class="table table-sm table-borderless">
                    <tbody>
                      <tr>
                        <td class="fw-bold">Tồn kho hiện tại:</td>
                        <td><span class="badge bg-primary">{{ slotProps.data.ton_kho || 0 }}</span></td>
                      </tr>
                      <tr>
                        <td class="fw-bold">Tồn khuyến mãi:</td>
                        <td>{{ slotProps.data.ton_khuyen_mai || '-' }}</td>
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
                          <span :class="['badge', getInventoryStatus(slotProps.data).class]">{{
                            getInventoryStatus(slotProps.data).label }}</span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>
    </DataTable>
  </div>
</template>
