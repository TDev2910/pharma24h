<template>
  <div class="header-control-bar">
    <div class="controls-section">
      <!-- Title Section -->
      <div class="title-section">
        <h4>Danh sách nhập hàng</h4>
      </div>

      <!-- Utility Options -->
      <div class="ultility-options" style="margin-left: auto;">
        <!-- Dropdown Đặt hàng -->
        <div class="dropdown" :class="{ 'show': showOrderDropdown }">
          <button class="btn btn-primary" @click="toggleOrderDropdown">
            <i class="pi pi-plus"></i>
            Đặt hàng
          </button>
          <div class="dropdown-menu" :class="{ 'show': showOrderDropdown }" v-if="showOrderDropdown">
            <div class="dropdown-item" @click="openMedicineModal">
              <i class="pi pi-pill"></i>
              Thuốc
            </div>
            <div class="dropdown-item" @click="openGoodsModal">
              <i class="pi pi-box"></i>
              Vật tư y tế
            </div>
          </div>
        </div>

        <div class="utility-icons">
          <button class="btn" title="Danh sách" @click="handleList">
            <i class="pi pi-list"></i>
          </button>
          <button class="btn" title="Cài đặt" @click="handleSettings">
            <i class="pi pi-cog"></i>
          </button>
          <button class="btn" title="Trợ giúp" @click="handleHelp">
            <i class="pi pi-question-circle"></i>
          </button>
        </div>
      </div>
    </div>
    <!-- Modal CreateMedicine -->
    <CreateMedicine 
      v-if="showMedicineModal" 
      :visible="showMedicineModal" 
      @close="closeMedicineModal"
      @created="handleMedicineCreated"
    />
    <!-- Modal CreateGoods -->
    <CreateGoods 
      v-if="showGoodsModal" 
      :visible="showGoodsModal" 
      @close="closeGoodsModal"
      @created="handleGoodsCreated"
    />
    
  </div>
</template>


<script>
import CreateMedicine from './Modals/CreateMedicine.vue'
import CreateGoods from './Modals/CreateGoods.vue'

export default {
  name: 'Toolbar',
  components: {
    CreateMedicine,
    CreateGoods
  },
  data() {
    return {
      searchQuery: '',
      showOrderDropdown: false,
      showMedicineModal: false,
      showGoodsModal: false
    }
  },

  methods: {
    handleSearch() {
      // Emit search event với debounce
      clearTimeout(this.searchTimeout)
      this.searchTimeout = setTimeout(() => {
        this.$emit('search', this.searchQuery)
      }, 300)
    },

    toggleOrderDropdown() {
      this.showOrderDropdown = !this.showOrderDropdown
    },

    createOrder(type) {
      this.showOrderDropdown = false
      // Xử lý logic đặt hàng theo loại (medicine/goods/service)
      console.log('Đặt hàng:', type)
      // Ví dụ: mở modal đặt hàng hoặc chuyển trang
    },

    openMedicineModal() {
      this.showOrderDropdown = false
      this.showMedicineModal = true
      console.log('Opening modal:', this.showMedicineModal) // Debug log
    },

    closeMedicineModal() {
      this.showMedicineModal = false
    },

    handleMedicineCreated(medicineData) {
      console.log('Medicine created in Toolbar:', medicineData)
      // Emit event lên parent component (Create.vue) với dữ liệu medicine
      this.$emit('item-created', medicineData, 'medicine')
      this.closeMedicineModal()
    },

    openGoodsModal() {
      this.showOrderDropdown = false
      this.showGoodsModal = true
    },

    closeGoodsModal() {
      this.showGoodsModal = false
    },

    handleGoodsCreated(goodsData) {
      console.log('Goods created in Toolbar:', goodsData)
      // Emit event lên parent component (Create.vue) với dữ liệu goods
      this.$emit('item-created', goodsData, 'goods')
      this.closeGoodsModal()
    },

    handleKeydown(event) {
      // Handle F3 key để focus vào search
      if (event.key === 'F3') {
        event.preventDefault()
        this.$refs.searchInput.focus()
      }

      // Handle Enter key để search ngay lập tức
      if (event.key === 'Enter') {
        event.preventDefault()
        clearTimeout(this.searchTimeout)
        this.$emit('search', this.searchQuery)
      }

      // Handle Escape key để clear search
      if (event.key === 'Escape') {
        this.searchQuery = ''
        this.$emit('search', '')
      }
    },

    handleCreate() {
      // Emit event để parent component có thể handle create
      this.$emit('create')
    },

    handleList() {
      // Emit event để parent component có thể handle list/filter
      this.$emit('list')
    },

    handleSettings() {
      // Emit event để parent component có thể handle settings
      this.$emit('settings')
    },

    handleHelp() {
      // Emit event để parent component có thể handle help
      this.$emit('help')
    },

    // Method để focus vào search input từ parent
    focusSearch() {
      this.$refs.searchInput.focus()
    },

    // Method để clear search từ parent
    clearSearch() {
      this.searchQuery = ''
      this.$emit('search', '')
    }
  },

  mounted() {
    // Global keyboard listener cho F3
    document.addEventListener('keydown', (event) => {
      if (event.key === 'F3' && !event.ctrlKey && !event.altKey && !event.metaKey) {
        event.preventDefault()
        this.focusSearch()
      }
    })
  },
  beforeUnmount() {
    // Cleanup timeout
    if (this.searchTimeout) {
      clearTimeout(this.searchTimeout)
    }
  }
}
</script>

<style scoped>
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

.title-section h4 {
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
  height: 36px !important;
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

.btn-primary {
  background: #0b1020;
  border-color: #0b1020;
  color: white;
  font-weight: 600;
  padding: 5px 12px;
}

.btn-primary:hover {
  background: #1a2332;
  border-color: #1a2332;
  color: white;
}

.btn-primary i {
  margin-right: 6px;
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
/* Responsive adjustments */
@media (max-width: 768px) {
  .controls-section {
    flex-direction: column;
    gap: 12px;
  }

  .search-wrapper {
    max-width: 100%;
    min-width: 100%;
  }

  .ultility-options {
    width: 100%;
    justify-content: space-between;
  }
}
</style>
