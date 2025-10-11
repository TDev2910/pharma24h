<template>
  <div class="create-purchase-return-page">
    <!-- Main content area -->
    <div class="row g-3">
      <!-- Products table - Left side (main content) -->
      <div class="col-lg-8">
        <Toolbar 
          @search="handleSearch"
          @file-upload="handleFileUpload"
          @settings="handleSettings"
        />
        <ItemTable 
          @update-total="handleUpdateTotal"
        />
      </div>
      
      <!-- Form details - Right sidebar -->
      <div class="col-lg-4">
        <SummaryPanel 
          :suppliers="suppliers"
          :total-amount="totalAmount"
          @form-submit="handleFormSubmit"
        />
      </div>
    </div>
    
    <!-- Modal Payment -->
    <ModalPayment 
      :payable-amount="payableAmount"
      @payment-confirmed="handlePaymentConfirmed"
    />
  </div>
</template>

<script>
import Toolbar from './Components/Toolbar.vue'
import ItemTable from './Components/Item-table.vue'
import SummaryPanel from './Components/Summary-panel.vue'
import ModalPayment from './Components/Modal-payment.vue'

export default {
  name: 'CreatePurchaseReturn',
  
  components: {
    Toolbar,
    ItemTable,
    SummaryPanel,
    ModalPayment
  },
  
  data() {
    return {
      totalAmount: 0,
      payableAmount: 0,
      suppliers: [
        { id: 1, ten_nha_cung_cap: 'Công ty Dược phẩm ABC', ma_nha_cung_cap: 'NCC001' },
        { id: 2, ten_nha_cung_cap: 'Nhà cung cấp XYZ', ma_nha_cung_cap: 'NCC002' },
        { id: 3, ten_nha_cung_cap: 'Công ty Thuốc DEF', ma_nha_cung_cap: 'NCC003' },
        { id: 4, ten_nha_cung_cap: 'Nhà cung cấp GHI', ma_nha_cung_cap: 'NCC004' },
        { id: 5, ten_nha_cung_cap: 'Công ty Dược JKL', ma_nha_cung_cap: 'NCC005' }
      ]
    }
  },

  methods: {
    handleSearch(query) {
      console.log('Search query:', query)
      // TODO: Implement search functionality
    },

    handleFileUpload() {
      console.log('File upload requested')
      // TODO: Implement file upload functionality
    },

    handleSettings() {
      console.log('Settings requested')
      // TODO: Implement settings functionality
    },

    handleUpdateTotal(amount) {
      this.totalAmount = amount
      this.payableAmount = amount // Initially payable = total, will be updated by discount
    },

    handleFormSubmit(formData) {
      console.log('Form submitted:', formData)
      // TODO: Implement form submission
      this.$toast.add({
        severity: 'success',
        summary: 'Thành công',
        detail: 'Phiếu trả hàng đã được lưu',
        life: 3000
      })
    },

    handlePaymentConfirmed(paymentData) {
      console.log('Payment confirmed:', paymentData)
      // TODO: Update summary panel with payment data
    }
  },

  mounted() {
    // Initialize any required data
    console.log('Create Purchase Return page mounted')
  }
}
</script>

<style scoped>
.create-purchase-return-page {
  padding: 20px;
}

.summary-card {
  position: sticky;
  top: 1rem;
}

#importItemsBody tr:hover {
  background-color: #f8f9fa;
}

.table th {
  font-weight: 600;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .create-purchase-return-page {
    padding: 10px;
  }
  
  .row.g-3 {
    margin: 0;
  }
  
  .col-lg-8,
  .col-lg-4 {
    padding: 0;
  }
}

/* Additional styling for better UX */
.card {
  border: 1px solid #e9ecef;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.btn-primary {
  background-color: #007bff;
  border-color: #007bff;
}

.btn-primary:hover {
  background-color: #0056b3;
  border-color: #0056b3;
}

.btn-success {
  background-color: #28a745;
  border-color: #28a745;
}

.btn-success:hover {
  background-color: #218838;
  border-color: #218838;
}

.form-control:focus {
  border-color: #86b7fe;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.table-light th {
  background-color: #f8f9fa;
  border-color: #dee2e6;
}
</style>