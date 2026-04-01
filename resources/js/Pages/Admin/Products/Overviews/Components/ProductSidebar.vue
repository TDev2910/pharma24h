<script setup>
import Tree from 'primevue/tree'
import DatePicker from 'primevue/datepicker'

const props = defineProps({
  loadingCategories: Boolean,
  categoryTreeNodes: Array,
  selectedCategoryKeys: Object,
  selectedCategoryName: String,
  filters: Object
})

const emit = defineEmits([
  'update:selectedCategoryKeys',
  'node-select',
  'node-unselect',
  'reset-category',
  'create-category',
  'edit-category',
  'update:filters'
])
</script>

<template>
  <div class="left-sidebar">
    <div class="filter-card">
      <div class="filter-header d-flex justify-content-between align-items-center mb-3">
        <h6 class="m-0 fw-bold text-dark">Nhóm hàng</h6>
        <button class="btn-create-link" @click="$emit('create-category')">
          Tạo mới
        </button>
      </div>

      <div class="category-tree-wrapper">
        <div v-if="loadingCategories" class="text-center py-4">
          <i class="pi pi-spinner pi-spin text-primary" style="font-size: 1.5rem"></i>
        </div>

        <div v-else-if="categoryTreeNodes.length === 0" class="text-center py-4 text-muted">
          <small>Chưa có nhóm hàng</small>
        </div>

        <Tree v-else 
          :value="categoryTreeNodes" 
          :selectionKeys="selectedCategoryKeys"
          @update:selectionKeys="$emit('update:selectedCategoryKeys', $event)"
          selectionMode="single"
          :metaKeySelection="false" 
          @nodeSelect="$emit('node-select', $event)" 
          @nodeUnselect="$emit('node-unselect', $event)"
          class="modern-tree w-full">
          <template #default="slotProps">
            <div class="custom-tree-node">
              <span class="node-label">{{ slotProps.node.label }}</span>
              <div class="node-actions">
                <button class="btn-icon-action edit" @click.stop="$emit('edit-category', slotProps.node)" title="Chỉnh sửa">
                  <i class="pi pi-pencil"></i>
                </button>
              </div>
            </div>
          </template>
        </Tree>
      </div>

      <div v-if="selectedCategoryName" class="selected-filter-chip mt-3">
        <span>{{ selectedCategoryName }}</span>
        <i class="pi pi-times remove-icon" @click="$emit('reset-category')" title="Bỏ lọc"></i>
      </div>
    </div>

    <hr class="divider" />

    <div class="filter-card">
      <h6 class="fw-bold text-dark mb-3">Thời gian</h6>
      <div class="date-range-wrapper">
        <div class="d-flex flex-column gap-2">
          <DatePicker v-model="filters.fromDate" showIcon fluid iconDisplay="input" placeholder="Từ ngày"
            class="custom-datepicker" />
          <DatePicker v-model="filters.toDate" showIcon fluid iconDisplay="input" placeholder="Đến ngày"
            class="custom-datepicker" />
        </div>
      </div>
    </div>
  </div>
</template>
