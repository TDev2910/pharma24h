<template>
  <form @submit.prevent="handleSubmit" class="card shadow-sm summary-card">
    <div class="card-body d-flex flex-column summary-right">

      <!-- Header tìm Ncc + nút thêm -->
      <div class="mb-3">
        <label class="form-label">Nhà cung cấp</label>
        <div class="d-flex gap-2">
          <select v-model="formData.supplier_id" class="form-select">
            <option value="">Tìm nhà cung cấp</option>
            <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
              {{ supplier.ten_nha_cung_cap }} ({{ supplier.ma_nha_cung_cap }})
            </option>
          </select>
        </div>
      </div>

      <!-- Mã phiếu nhập -->
      <div class="mb-3 d-flex align-items-center">
        <label class="form-label mb-0 me-3" style="min-width: 130px;">Mã phiếu nhập</label>
        <div class="input-group">
          <input type="text" v-model="formData.import_code" class="form-control text-muted"
            placeholder="Mã phiếu tự động" readonly>
          <button type="button" class="btn btn-outline-secondary" @click="generateCode" :disabled="isGeneratingCode">
            <i :class="isGeneratingCode ? 'fas fa-spinner fa-spin' : 'fas fa-sync-alt'"></i>
            {{ isGeneratingCode ? 'Đang tạo...' : 'Tạo mã' }}
          </button>
        </div>
      </div>

      <!-- Ngày đặt hàng -->
      <div class="mb-3 d-flex align-items-center">
        <label class="form-label mb-0 me-3" style="min-width: 130px;">Ngày nhập</label>
        <input type="date" v-model="formData.import_date" class="form-control" style="max-width: 200px;">
      </div>

      <!-- Khu tổng tiền giống mẫu -->
      <div class="mt-2 pt-3" style="border-top:1px solid #e9ecef;">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="d-flex align-items-center gap-2">
            <span class="form-label m-0">Tổng tiền hàng</span>
            <input type="number" v-model="formData.subtotal_raw" class="form-control form-control-sm text-center"
              style="width:64px;background:#f8f9fa;" min="0" readonly>
          </div>
          <span class="fw-bold value">{{ formatCurrency(formData.subtotal_raw) }}</span>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="form-label m-0">Giảm giá</span>
          <input type="number" v-model="formData.discount" class="form-control text-end"
            style="width:120px;background:#fff;" min="0" @input="recalculate">
        </div>
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="form-label m-0">Cần trả nhà cung cấp</span>
          <span class="link-number">{{ formatCurrency(formData.payable) }}</span>
        </div>

        <div class="mb-1 d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center gap-2">
            <span class="form-label m-0">Tiền trả nhà cung cấp</span>
            <button type="button" class="btn btn-sm btn-outline-primary" @click="showPaymentModal" title="Thanh toán">
              <i class="fas fa-credit-card"></i>
            </button>
          </div>
          <input type="number" v-model="formData.cash_paid" class="form-control text-end" style="width:120px;" min="0"
            @input="recalculate">
        </div>

        <div class="d-flex justify-content-between align-items-center mt-2">
          <span class="form-label m-0">Tính vào công nợ</span>
          <span class="value text-muted">{{ formatCurrency(formData.debt) }}</span>
        </div>
      </div>

      <!-- Ghi chú -->
      <div class="mb-3 mt-3">
        <textarea v-model="formData.note" rows="3" class="form-control" placeholder="Nhập ghi chú..."></textarea>
      </div>

      <!-- Buttons -->
      <div class="d-flex justify-content-end gap-2 mt-3">
        <button type="submit" name="action" value="draft" class="btn btn-primary px-4">
          <i class="fas fa-save me-1"></i>Lưu tạm
        </button>
        <button type="submit" name="action" value="complete" class="btn btn-success px-4">
          <i class="fas fa-check me-1"></i>Hoàn thành
        </button>
      </div>
    </div>
  </form>
</template>

<script>
export default {
  name: 'SummaryPanel',

  props: {
    suppliers: {
      type: Array,
      default: () => []
    },
    totalAmount: {
      type: Number,
      default: 0
    }
  },

  data() {
    return {
      formData: {
        supplier_id: '',
        import_code: '',
        import_date: new Date().toISOString().split('T')[0],
        subtotal_raw: 0,
        discount: 0,
        payable: 0,
        cash_paid: 0,
        debt: 0,
        note: '',
        status: 'imported'
      },
      isGeneratingCode: false
    }
  },

  methods: {
    generateCode() {
      this.isGeneratingCode = true

      // Gọi API để tạo mã từ server
      fetch('/admin/generate-import-code')
        .then(response => response.json())
        .then(data => {
          this.formData.import_code = data.code
          this.isGeneratingCode = false
        })
        .catch(error => {
          console.error('Error:', error)
          // Fallback: tạo mã client-side
          this.formData.import_code = this.generateRandom7DigitCode()
          this.isGeneratingCode = false
        })
    },

    generateRandom7DigitCode() {
      return Math.floor(1000000 + Math.random() * 9000000)
    },

    recalculate() {
      const subtotal = Math.max(this.formData.subtotal_raw, 0)
      const discount = Math.max(this.formData.discount, 0)
      const cashPaid = Math.max(this.formData.cash_paid, 0)

      this.formData.payable = Math.max(subtotal - discount, 0)
      this.formData.debt = cashPaid ? (cashPaid * -1) : (this.formData.payable * -1)
    },

    formatCurrency(amount) {
      if (!amount) return '0'
      return new Intl.NumberFormat('vi-VN').format(amount)
    },

    handleSubmit(event) {
      const action = event.submitter.value

      // Emit event với form data
      this.$emit('form-submit', {
        ...this.formData,
        action: action
      })
    },

    // Method để cập nhật từ parent component
    updateTotal(amount) {
      this.formData.subtotal_raw = amount
      this.recalculate()
    },

    showPaymentModal() {
      this.$emit('show-payment-modal', {
        payable: this.formData.payable,
        currentPaid: this.formData.cash_paid
      })
    },

    // Method để cập nhật payment từ modal
    updatePayment(paymentData) {
      this.formData.cash_paid = paymentData.amount
      this.recalculate()
    }
  },

  watch: {
    totalAmount(newVal) {
      this.formData.subtotal_raw = newVal
      this.recalculate()
    }
  },

  mounted() {
    // Tự động tạo mã khi component mount
    this.generateCode()

    // Lắng nghe event từ modal payment
    this.$parent.$on('payment-confirmed', (paymentData) => {
      this.updatePayment(paymentData)
    })
  }
}
</script>

<style scoped>
.summary-card {
  height: fit-content;
}

.summary-right {
  min-height: 400px;
}

.value {
  font-weight: 600;
  color: #333;
}

.link-number {
  font-weight: 600;
  color: #007bff;
}

.form-control:read-only {
  background-color: #f8f9fa;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>
