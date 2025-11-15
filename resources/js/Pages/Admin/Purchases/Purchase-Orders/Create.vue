<template>
  <div class="create-purchase-order-page">
    <!-- Main content area -->
    <div class="row g-3">
      <!-- Products table - Left side (main content) -->
      <div class="col-lg-8">
        <Toolbar 
          @search="handleSearch"
          @file-upload="handleFileUpload"
          @settings="handleSettings"
          @item-created="handleItemCreated"
        />
      <ItemsTable 
        :items="items"
        @update-items="handleUpdateItems"
        @update-total="handleUpdateTotal"
      />
      </div>
      
      <!-- Form details - Right sidebar -->
      <div class="col-lg-4">
        <SummaryPanel 
          ref="summaryPanel"
          :suppliers="suppliers"
          :total-amount="totalAmount"
          @form-submit="handleFormSubmit"
          @show-payment-modal="handleShowPaymentModal"
        />
      </div>
    </div>
    
    <!-- Modal Payment -->
    <ModalPayment 
      :payable-amount="payableAmount"
      :show="showPaymentModal"
      @close="closePaymentModal"
      @payment-confirmed="handlePaymentConfirmed"
    />
  </div>
</template>
  
<script>
import { useToast } from 'primevue/usetoast'
import Toolbar from './Components/Toolbar.vue'
import ItemsTable from './Components/Items-table.vue'
import SummaryPanel from './Components/Summary-panel.vue'
import ModalPayment from './Components/Modal-payment.vue'
import CreateMedicine from './Components/Modals/CreateMedicine.vue'
import CreateGoods from './Components/Modals/CreateGoods.vue'

export default {
  name: 'CreatePurchaseOrder',
  
  components: {
    Toolbar,
    ItemsTable,
    SummaryPanel,
    ModalPayment,
    CreateMedicine,
    CreateGoods
  },

  setup() {
    const toast = useToast()
    return { toast }
  },
  
  props: {
    suppliers: {
      type: Array,
      default: () => []
    },
    medicines: {
      type: Array,
      default: () => []
    },
    goods: {
      type: Array,
      default: () => []
    }
  },
  
  data() {
    return {
      totalAmount: 0,
      payableAmount: 0,
      items: [], // Store items from ItemTable
      showPaymentModal: false
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

    handleUpdateItems(items) {
      this.items = items
    },

    async handleFormSubmit(formData) {
      // Validate required fields
      if (!formData.supplier_id) {
        this.toast.add({
          severity: 'error',
          summary: 'Lỗi',
          detail: 'Vui lòng chọn nhà cung cấp',
          life: 3000
        })
        return
      }

      if (this.items.length === 0) {
        this.toast.add({
          severity: 'error',
          summary: 'Lỗi',
          detail: 'Vui lòng thêm ít nhất một sản phẩm',
          life: 3000
        })
        return
      }

      try {
        // Prepare data for backend
        const processedItems = this.items.map(item => {
          const processedItem = {
            product_type: item.product_type || 'goods', // Use product_type from Excel import
            product_id: item.product_id || item.id,
            quantity: item.so_luong || item.quantity,
            unit_price: item.don_gia || item.unit_price,
            discount: 0, // Individual item discount
            note: item.note || null
          }
          
          // Validate each item
          if (!processedItem.product_id) {
            throw new Error(`Sản phẩm "${item.ten_hang || item.ma_hang}" không có ID`)
          }
          if (!processedItem.quantity || processedItem.quantity <= 0) {
            throw new Error(`Sản phẩm "${item.ten_hang || item.ma_hang}" có số lượng không hợp lệ`)
          }
          if (!processedItem.unit_price || processedItem.unit_price <= 0) {
            throw new Error(`Sản phẩm "${item.ten_hang || item.ma_hang}" có đơn giá không hợp lệ`)
          }
          
          return processedItem
        })

        const submitData = {
          import_code: formData.import_code,
          supplier_id: formData.supplier_id,
          import_date: formData.import_date,
          note: formData.note,
          discount: formData.discount || 0,
          items: processedItems
        }

        // Submit to backend
        const response = await fetch('/admin/purchase-orders', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify(submitData)
        })

        if (response.ok) {
          this.toast.add({
            severity: 'success',
            summary: 'Thành công',
            detail: 'Phiếu nhập hàng đã được lưu thành công',
            life: 3000
          })
            
          // Redirect to dashboard after successful save
          setTimeout(() => {
            window.location.href = '/admin/import'
          }, 1500)
        } else {
          let errorMessage = 'Có lỗi xảy ra khi lưu phiếu nhập hàng'
          
          try {
            const errorData = await response.json()
            
            if (errorData.message) {
              errorMessage = errorData.message
            } else if (errorData.errors) {
              // Handle validation errors
              const errorMessages = Object.values(errorData.errors).flat()
              errorMessage = errorMessages.join(', ')
            }
          } catch (e) {
            errorMessage = `HTTP ${response.status}: ${response.statusText}`
          }
          
          this.toast.add({
            severity: 'error',
            summary: 'Lỗi',
            detail: errorMessage,
            life: 5000
          })
        }
      } catch (error) {
        this.toast.add({
          severity: 'error',
          summary: 'Lỗi',
          detail: 'Có lỗi xảy ra khi gửi dữ liệu',
          life: 3000
        })
      }
    },

    handleShowPaymentModal(paymentData) {
      // Show payment modal with current data
      this.payableAmount = paymentData.payable
      this.showPaymentModal = true
    },

    closePaymentModal() {
      this.showPaymentModal = false
    },

    handlePaymentConfirmed(paymentData) {
      // Update summary panel with payment data
      console.log('Payment confirmed:', paymentData)
      this.showPaymentModal = false
      
      // Emit event to SummaryPanel to update cash_paid
      this.$refs.summaryPanel?.updatePayment(paymentData)
    },

    handleItemCreated(itemData, productType) {
      console.log('Item created received:', itemData, productType)
      
      let newItem = null
      let successMessage = ''

      // Transform dữ liệu theo productType
      if (productType === 'medicine') {
        newItem = {
          id: itemData.id,
          ma_hang: itemData.ma_hang,
          ten_hang: itemData.ten_thuoc, // Medicine dùng ten_thuoc
          don_vi_tinh: itemData.don_vi_tinh || '',
          so_luong: 1, // Số lượng mặc định
          don_gia: itemData.gia_von || 0, // Giá nhập = gia_von
          thanh_tien: itemData.gia_von || 0, // thanh_tien = so_luong * don_gia (1 * gia_von)
          product_type: 'medicine',
          product_id: itemData.id
        }
        successMessage = 'Thuốc đã được thêm vào danh sách'
      } else if (productType === 'goods') {
        newItem = {
          id: itemData.id,
          ma_hang: itemData.ma_hang,
          ten_hang: itemData.ten_hang_hoa, // Goods dùng ten_hang_hoa
          don_vi_tinh: itemData.don_vi_tinh || '',
          so_luong: 1, // Số lượng mặc định
          don_gia: itemData.gia_von || 0, // Giá nhập = gia_von
          thanh_tien: itemData.gia_von || 0, // thanh_tien = so_luong * don_gia (1 * gia_von)
          product_type: 'goods',
          product_id: itemData.id
        }
        successMessage = 'Vật tư y tế đã được thêm vào danh sách'
      } else {
        console.log('Unknown product type:', productType)
        return
      }

      console.log('New item to add:', newItem)
      
      // Thêm item mới vào mảng items
      this.items.push(newItem)
      
      // Cập nhật tổng tiền
      const newTotal = this.calculateTotal()
      this.handleUpdateTotal(newTotal)
      
      // Hiển thị thông báo thành công
      this.toast.add({
        severity: 'success',
        summary: 'Thành công',
        detail: successMessage,
        life: 3000
      })
    },

    calculateTotal() {
      return this.items.reduce((total, item) => {
        const quantity = item.so_luong || item.quantity || 0
        const price = item.don_gia || item.unit_price || 0
        return total + (quantity * price)
      }, 0)
    }

  },

  mounted() {
    // Initialize any required data
  }
}
</script>
  
<style scoped>
.create-purchase-order-page {
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