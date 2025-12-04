<template>
    <Dialog
      :visible="visible"
      @update:visible="$emit('close')"
      :header="modalTitle"
      :style="{ width: '900px', maxWidth: '95vw' }"
      modal
      :closable="true"
      class="invoice-modal"
    >
      <div v-if="loading" class="text-center py-5">
        <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
        <p class="mt-3">Đang tải hóa đơn...</p>
      </div>
  
      <div v-else-if="error" class="alert alert-danger">
        {{ error }}
      </div>
  
      <div v-else-if="order" class="invoice-content">
        <div class="invoice-header text-center mb-4">
          <h2 class="mb-2">HÓA ĐƠN BÁN HÀNG</h2>
          <p class="mb-1"><strong>Mã đơn hàng:</strong> {{ order.order_code }}</p>
          <p class="mb-0"><strong>Ngày:</strong> {{ formatDate(order.created_at) }}</p>
        </div>
  
        <div class="invoice-section mb-4">
          <h6 class="section-title">Thông tin khách hàng</h6>
          <p><strong>Khách hàng:</strong> {{ order.customer_name }}</p>
          <p><strong>SĐT:</strong> {{ order.customer_phone }}</p>
          <p v-if="order.customer_email"><strong>Email:</strong> {{ order.customer_email }}</p>
          <p>
            <strong>Địa chỉ:</strong> 
            {{ getFullAddress() }}
          </p>
        </div>
  
        <div class="invoice-section mb-4">
          <DataTable 
            :value="items" 
            class="invoice-table"
            :paginator="false"
          >
            <Column header="STT" style="width: 60px">
              <template #body="slotProps">
                {{ slotProps.index + 1 }}
              </template>
            </Column>
            <Column field="product_name" header="Tên sản phẩm"></Column>
            <Column field="quantity" header="Số lượng" style="width: 100px" class="text-center">
              <template #body="slotProps">
                {{ slotProps.data.quantity }}
              </template>
            </Column>
            <Column field="price" header="Đơn giá" style="width: 150px" class="text-end">
              <template #body="slotProps">
                {{ formatCurrency(slotProps.data.price) }}
              </template>
            </Column>
            <Column field="subtotal" header="Thành tiền" style="width: 150px" class="text-end">
              <template #body="slotProps">
                {{ formatCurrency(slotProps.data.subtotal) }}
              </template>
            </Column>
          </DataTable>
  
          <div class="invoice-total mt-3">
            <div class="d-flex justify-content-between">
              <strong>Tổng cộng:</strong>
              <strong class="total-amount">{{ formatCurrency(order.total_amount) }}</strong>
            </div>
          </div>
        </div>
  
        <div class="invoice-section">
          <p><strong>Phương thức thanh toán:</strong> {{ getPaymentMethodText(order.payment_method) }}</p>
          <p v-if="order.note"><strong>Ghi chú:</strong> {{ order.note }}</p>
        </div>
      </div>
  
      <template #footer>
        <Button 
          label="Đóng" 
          severity="secondary" 
          icon="pi pi-times"
          @click="$emit('close')"
        />
        <Button 
          label="In hóa đơn" 
          icon="pi pi-print" 
          @click="printInvoice"
          class="p-button-primary"
        />
        <Button 
          label="Tải PDF" 
          icon="pi pi-download" 
          @click="downloadPDF"
          class="p-button-success"
        />
      </template>
    </Dialog>
  </template>
  
  <script>
  import Dialog from 'primevue/dialog'
  import Button from 'primevue/button'
  import DataTable from 'primevue/datatable'
  import Column from 'primevue/column'
  import axios from 'axios'
  
  export default {
    name: 'InvoiceModal',
    components: {
      Dialog,
      Button,
      DataTable,
      Column
    },
    props: {
      visible: {
        type: Boolean,
        default: false
      },
      orderId: {
        type: Number,
        default: null
      }
    },
    emits: ['close'],
    data() {
      return {
        loading: false,
        error: null,
        order: null,
        items: []
      }
    },
    computed: {
      modalTitle() {
        return this.order ? `Hóa đơn #${this.order.order_code}` : 'Hóa đơn';
      }
    },
    watch: {
      orderId: {
        immediate: true,
        handler(newVal) {
          if (newVal && this.visible) {
            this.loadInvoice();
          } else {
            this.resetModal();
          }
        }
      },
      visible(newVal) {
        if (newVal && this.orderId) {
          this.loadInvoice();
        } else {
          this.resetModal();
        }
      }
    },
    methods: {
      async loadInvoice() {
        if (!this.orderId) return;
  
        this.loading = true;
        this.error = null;
  
        try {
          const response = await axios.get(`/admin/orders/${this.orderId}`);
          
          if (response.data?.success) {
            this.order = response.data.order;
            this.items = response.data.items || [];
          } else {
            this.error = 'Không thể tải hóa đơn. Vui lòng thử lại!';
          }
        } catch (error) {
          console.error('Error loading invoice:', error);
          this.error = error.response?.data?.message || 'Đã xảy ra lỗi khi tải hóa đơn.';
        } finally {
          this.loading = false;
        }
      },
      
      printInvoice() {
        if (this.orderId) {
          window.open(`/admin/orders/${this.orderId}/invoice`, '_blank');
        }
      },
      
      downloadPDF() {
        if (this.orderId) {
          const link = document.createElement('a');
          link.href = `/admin/orders/${this.orderId}/invoice?download=1`;
          link.download = `invoice-${this.order?.order_code || this.orderId}.pdf`;
          link.click();
        }
      },
      
      getFullAddress() {
        if (!this.order) return 'N/A';
        
        if (this.order.delivery_method === 'pickup') {
          return this.order.pickup_location || 'N/A';
        }
        
        const parts = [
          this.order.shipping_address,
          this.order.ward,
          this.order.district,
          this.order.province
        ].filter(Boolean);
        
        return parts.length > 0 ? parts.join(', ') : 'N/A';
      },
      
      formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('vi-VN', {
          year: 'numeric',
          month: '2-digit',
          day: '2-digit',
          hour: '2-digit',
          minute: '2-digit'
        });
      },
      
      formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN', {
          style: 'currency',
          currency: 'VND'
        }).format(amount);
      },
      
      getPaymentMethodText(method) {
        const m = (method || '').toString().toLowerCase();
        switch(m) {
          case 'cash':
            return 'Tiền mặt';
          case 'transfer':
            return 'Chuyển khoản';
          case 'vnpay':
            return 'VNPay';
          case 'momo':
            return 'Ví MoMo';
          case 'zalopay':
            return 'ZaloPay';
          default:
            return method || 'Không xác định';
        }
      },
      
      resetModal() {
        this.order = null;
        this.items = [];
        this.error = null;
        this.loading = false;
      }
    }
  }
  </script>
  
<style>
@import '@Admin/orders/modals.css';
</style>