<template>
  <div class="card shadow-sm" style="margin-left: 15px;">
    <div class="card-body p-0">
      <table class="table mb-0">
        <thead class="table-light">
          <tr>
            <th style="width:60px;">STT</th>
            <th style="min-width:140px;">Mã hàng</th>
            <th style="min-width:240px;">Tên hàng</th>
            <th style="width:90px;">ĐVT</th>
            <th style="width:120px;">Số lượng</th>
            <th style="width:140px;">Giá nhập</th>
            <th class="text-end" style="width:130px;">Thành tiền</th>
          </tr>
        </thead>
        <tbody id="importItemsBody">
          <!-- Empty state -->
          <tr v-if="items.length === 0">
            <td colspan="7" class="empty-state-cell">
              <div class="empty-wrapper">
                <div class="fw-semibold mb-2" style="font-size:20px;color:#2b2f33;">
                  Thêm sản phẩm từ file excel
                </div>
                <button type="button" class="btn btn-primary btn-lg" style="border-radius: 5px ; background:#0b1020; border:none; color:white; font-weight:600; padding:6px 18px;" @click="selectFile"
                  :disabled="isImporting">
                  {{ isImporting ? 'Đang xử lý...' : 'Chọn file dữ liệu' }}
                </button>
              </div>
            </td>
          </tr>

          <!-- Items list -->
          <tr v-for="(item, index) in items" :key="index" :data-product-type="item.product_type"
            :data-product-id="item.product_id">
            <td>{{ index + 1 }}</td>
            <td>{{ item.ma_hang }}</td>
            <td>{{ item.ten_hang }}</td>
            <td>{{ item.don_vi_tinh }}</td>
            <td>
              <input type="number" class="form-control form-control-sm quantity-input"
                :value="item.so_luong || item.quantity" min="1" @input="updateQuantity(index, $event.target.value)">
            </td>
            <td>
              <input type="number" class="form-control form-control-sm price-input"
                :value="item.don_gia || item.unit_price" min="0" step="0.01"
                @input="updatePrice(index, $event.target.value)">
            </td>
            <td class="text-end">
              <span class="total-price">{{ formatCurrency(getItemTotal(item, index)) }}</span>
            </td>
          </tr>

          <!-- Loading state -->
          <tr v-if="isImporting">
            <td colspan="7" class="text-center py-5">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Đang xử lý...</span>
              </div>
              <div class="mt-2">Đang xử lý file Excel...</div>
            </td>
          </tr>

          <!-- Error state -->
          <tr v-if="errors.length > 0">
            <td colspan="7" class="text-danger">
              <strong>Lỗi:</strong>
              <div v-for="error in errors" :key="error" class="text-danger">• {{ error }}</div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Hidden file input -->
  <input ref="fileInput" type="file" accept=".xlsx,.xls,.csv" style="display: none" @change="handleFileSelect">
</template>

<script>
export default {
  name: 'ItemsTable',

  props: {
    items: {
      type: Array,
      default: () => []
    }
  },

  emits: ['update-items', 'update-total'],

  data() {
    return {
      isImporting: false,
      errors: []
    }
  },

  methods: {
    selectFile() {
      this.$refs.fileInput.click()
    },

    async handleFileSelect(event) {
      const file = event.target.files[0]
      if (!file) return

      this.isImporting = true
      this.errors = []

      const formData = new FormData()
      formData.append('excel_file', file)
      formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'))

      try {
        const response = await fetch('/admin/process-excel', {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        })

        const data = await response.json()

        if (data.success) {
          this.$emit('update-items', data.items)
          if (data.errors && data.errors.length > 0) {
            this.errors = data.errors
          }
        } else {
          this.errors = [data.message || 'Có lỗi xảy ra khi xử lý file']
        }
      } catch (error) {
        console.error('Error:', error)
        this.errors = ['Có lỗi xảy ra khi xử lý file']
      } finally {
        this.isImporting = false
        event.target.value = '' // Reset file input
      }
    },

    updateQuantity(index, value) {
      const quantity = parseInt(value) || 0
      this.items[index].so_luong = quantity
      this.items[index].quantity = quantity
      this.updateItemTotal(index)
      this.emitUpdates()
    },

    updatePrice(index, value) {
      const price = parseFloat(value) || 0
      this.items[index].don_gia = price
      this.items[index].unit_price = price
      this.updateItemTotal(index)
      this.emitUpdates()
    },

    updateItemTotal(index) {
      const item = this.items[index]
      const quantity = item.so_luong || item.quantity || 0
      const price = item.don_gia || item.unit_price || 0
      item.thanh_tien = quantity * price
    },

    getItemTotal(item, index) {
      const quantity = item.so_luong || item.quantity || 0
      const price = item.don_gia || item.unit_price || 0
      return quantity * price
    },

    emitUpdates() {
      this.$emit('update-items', this.items)
      this.$emit('update-total', this.calculateTotal())
    },

    calculateTotal() {
      return this.items.reduce((total, item) => {
        return total + this.getItemTotal(item, 0)
      }, 0)
    },

    formatCurrency(amount) {
      if (!amount) return '0'
      return new Intl.NumberFormat('vi-VN').format(amount)
    }
  },

  watch: {
    items: {
      handler() {
        this.$emit('update-total', this.calculateTotal())
      },
      deep: true
    }
  }
}
</script>

<style scoped>
.empty-state-cell {
  height: 100%;
  padding: 0;
}

.empty-wrapper {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  min-height: 615px;
}

#importItemsBody tr:hover {
  background-color: #f8f9fa;
}

.table th {
  font-weight: 600;
}
</style>
