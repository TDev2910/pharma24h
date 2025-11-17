<template>
  <!-- Modal: Thanh toán nhà cung cấp -->
  <div v-if="showModal" class="modal fade show" :class="{ 'd-block': showModal }" tabindex="-1"
    aria-labelledby="paySupplierLabel" aria-hidden="false" style="background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="paySupplierLabel">Tiền trả nhà cung cấp</h5>
          <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Thanh toán</label>
            <input type="number" class="form-control form-control-lg text-end" v-model="paymentAmount" min="0"
              @input="updatePaymentDisplay">
          </div>
          <div class="d-flex gap-2 mb-3">
            <button type="button" class="btn btn-outline-primary flex-fill pay-method"
              :class="{ active: selectedMethod === 'cash' }" @click="selectPaymentMethod('cash')">
              Tiền mặt
            </button>
            <button type="button" class="btn btn-outline-primary flex-fill pay-method"
              :class="{ active: selectedMethod === 'card' }" @click="selectPaymentMethod('card')">
              Thẻ
            </button>
            <button type="button" class="btn btn-outline-primary flex-fill pay-method"
              :class="{ active: selectedMethod === 'transfer' }" @click="selectPaymentMethod('transfer')">
              Chuyển khoản
            </button>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Cần trả nhà cung cấp</span>
            <span class="fw-bold">{{ formatCurrency(payableAmount) }}</span>
          </div>
          <div class="d-flex justify-content-between">
            <span class="text-muted">Tiền trả nhà cung cấp</span>
            <span class="fw-bold">{{ formatCurrency(paymentAmount) }}</span>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="closeModal">Bỏ qua</button>
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
    },
    show: {
      type: Boolean,
      default: false
    }
  },

  emits: ['close', 'payment-confirmed', 'payment-updated'],

  data() {
    return {
      paymentAmount: 0,
      selectedMethod: 'cash'
    }
  },

  computed: {
    showModal() {
      return this.show
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
      this.closeModal()
    },

    closeModal() {
      this.$emit('close')
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
    },

    show(newVal) {
      if (newVal) {
        this.resetModal()
      }
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
