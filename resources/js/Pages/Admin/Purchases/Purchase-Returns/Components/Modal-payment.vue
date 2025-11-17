<template>
  <!-- Modal: Thanh toán nhà cung cấp -->
  <div class="modal fade" id="paySupplierModal" tabindex="-1" aria-labelledby="paySupplierLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="paySupplierLabel">Tiền trả nhà cung cấp</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Thanh toán</label>
            <input type="number" class="form-control form-control-lg text-end" id="payModalAmount"
              v-model="paymentAmount" value="0" min="0" @input="updatePaymentDisplay">
          </div>
          <div class="d-flex gap-2 mb-3">
            <button type="button" class="btn btn-outline-primary flex-fill pay-method"
              :class="{ active: selectedMethod === 'cash' }" @click="selectPaymentMethod('cash')" data-method="cash">
              Tiền mặt
            </button>
            <button type="button" class="btn btn-outline-primary flex-fill pay-method"
              :class="{ active: selectedMethod === 'card' }" @click="selectPaymentMethod('card')" data-method="card">
              Thẻ
            </button>
            <button type="button" class="btn btn-outline-primary flex-fill pay-method"
              :class="{ active: selectedMethod === 'transfer' }" @click="selectPaymentMethod('transfer')"
              data-method="transfer">
              Chuyển khoản
            </button>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Cần trả nhà cung cấp</span>
            <span id="payModalPayable" class="fw-bold">{{ formatCurrency(payableAmount) }}</span>
          </div>
          <div class="d-flex justify-content-between">
            <span class="text-muted">Tiền trả nhà cung cấp</span>
            <span id="payModalPaid" class="fw-bold">{{ formatCurrency(paymentAmount) }}</span>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bỏ qua</button>
          <button type="button" class="btn btn-primary" @click="confirmPayment">Xong</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ModalPayment',

  props: {
    payableAmount: {
      type: Number,
      default: 0
    }
  },

  data() {
    return {
      paymentAmount: 0,
      selectedMethod: 'cash'
    }
  },

  methods: {
    selectPaymentMethod(method) {
      this.selectedMethod = method
    },

    updatePaymentDisplay() {
      // Emit event để parent component có thể cập nhật
      this.$emit('payment-updated', {
        amount: this.paymentAmount,
        method: this.selectedMethod
      })
    },

    confirmPayment() {
      // Emit event với thông tin thanh toán
      this.$emit('payment-confirmed', {
        amount: this.paymentAmount,
        method: this.selectedMethod
      })

      // Đóng modal
      const modal = document.getElementById('paySupplierModal')
      if (modal) {
        const bsModal = bootstrap.Modal.getInstance(modal)
        if (bsModal) {
          bsModal.hide()
        }
      }
    },

    formatCurrency(amount) {
      if (!amount) return '0'
      return new Intl.NumberFormat('vi-VN').format(amount)
    },

    // Method để reset modal khi mở
    resetModal() {
      this.paymentAmount = this.payableAmount
      this.selectedMethod = 'cash'
    }
  },

  watch: {
    payableAmount(newVal) {
      this.paymentAmount = newVal
    }
  },

  mounted() {
    // Lắng nghe event khi modal được mở
    const modal = document.getElementById('paySupplierModal')
    if (modal) {
      modal.addEventListener('show.bs.modal', () => {
        this.resetModal()
      })
    }
  }
}
</script>

<style scoped>
.modal-backdrop {
  z-index: 9998 !important;
}

.modal {
  z-index: 9999 !important;
  pointer-events: auto !important;
}

.pay-method.active {
  background-color: #007bff;
  color: white;
  border-color: #007bff;
}

.pay-method:hover {
  background-color: #e9ecef;
}

.pay-method.active:hover {
  background-color: #0056b3;
}
</style>
