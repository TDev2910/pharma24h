<script setup>
import Button from 'primevue/button'

const props = defineProps({
  searchQuery: String,
  showDropdown: Boolean
})

const emit = defineEmits([
  'update:searchQuery',
  'update:showDropdown',
  'toggle-dropdown',
  'create-medicine',
  'create-goods',
  'create-service',
  'export-file',
  'debounce-search'
])

const onSearchInput = (event) => {
  emit('update:searchQuery', event.target.value)
  emit('debounce-search')
}
</script>

<template>
  <div class="header-control-bar">
    <div class="controls-section"
      style="width:100%; display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
      
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
            <input type="text" 
              class="form-control" 
              style="border-radius:8px;" 
              placeholder="Theo mã, tên hàng"
              :value="searchQuery" 
              @input="onSearchInput"
            >
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="ultility-options">
        <!-- Dropdown Tạo mới -->
        <div class="dropdown" :class="{ 'show': showDropdown }">
          <Button icon="pi pi-plus" label="Tạo mới" @click="$emit('toggle-dropdown')" severity="secondary"
            style="background:#0b1020; border:none; color:white; font-weight:600; padding:6px 18px; border-radius:8px;" />

          <!-- Dropdown menu -->
          <div class="dropdown-menu" :class="{ 'show': showDropdown }" v-if="showDropdown">
            <div class="dropdown-item" @click="$emit('create-medicine')">
              <i class="pi pi-pill"></i>
              Thuốc
            </div>
            <div class="dropdown-item" @click="$emit('create-goods')">
              <i class="pi pi-box"></i>
              Vật tư y tế
            </div>
            <div class="dropdown-item" @click="$emit('create-service')">
              <i class="pi pi-cog"></i>
              Dịch vụ
            </div>
          </div>
        </div>

        <!-- Xuất file -->
        <Button icon="pi pi-upload" label="Xuất file" @click="$emit('export-file')" severity="secondary"
          style="background:#3A6F43; border:none; color:white; font-weight:600; padding:6px 18px; border-radius:8px;" />

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
</template>
