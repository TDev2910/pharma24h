<template>
  <Dialog 
    :visible="visible" 
    @update:visible="$emit('close')"
    :header="modalTitle" 
    :style="{ width: '900px' }"
    modal
    :closable="true"
    :loading="loading"
  >
    <div v-if="loading" class="text-center py-5">
      <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
      <p class="mt-3">Đang tải dữ liệu...</p>
    </div>

    <div v-else-if="error" class="alert alert-danger">
      {{ error }}
    </div>

    <div v-else-if="order">
      <!-- Thông tin đơn hàng và khách hàng -->
      <div class="row mb-4">
        <div class="col-md-6">
          <h6 class="fw-bold mb-3">Thông tin đơn hàng</h6>
          <div class="order-info">
            <p class="mb-2">
              <strong>Mã đơn hàng:</strong> 
              <span>{{ order.order_code }}</span>
            </p>
            <p class="mb-2">
              <strong>Ngày đặt:</strong> 
              <span>{{ formatDate(order.created_at) }}</span>
            </p>
            <p class="mb-2">
              <strong>Trạng thái:</strong> 
              <span :class="getStatusBadgeClass(order.order_status)">
                {{ getStatusText(order.order_status) }}
              </span>
            </p>
            <p class="mb-2">
              <strong>Trạng thái thanh toán:</strong> 
              <span v-if="isCancelled" class="badge bg-danger">Đơn hàng đã bị hủy</span>
              <span v-else :class="getPaymentStatusBadgeClass(order.payment_status)">
                {{ getPaymentStatusText(order.payment_status) }}
              </span>
            </p>
            <p class="mb-0">
              <strong>Ghi chú:</strong> 
              <span>{{ order.note || 'Không có' }}</span>
            </p>
          </div>
        </div>
        
        <div class="col-md-6">
          <h6 class="fw-bold mb-3">Thông tin khách hàng</h6>
          <div class="customer-info">
            <p class="mb-2">
              <strong>Tên khách hàng:</strong> 
              <span>{{ order.customer_name || 'N/A' }}</span>
            </p>
            <p class="mb-2">
              <strong>Số điện thoại:</strong> 
              <span>{{ order.customer_phone || 'N/A' }}</span>
            </p>
            <p class="mb-2">
              <strong>Email:</strong> 
              <span>{{ order.customer_email || 'N/A' }}</span>
            </p>
            <p class="mb-0" v-if="order.delivery_method === 'shipping'">
              <strong>Địa chỉ giao hàng:</strong> 
              <span>{{ getFullAddress() || 'N/A' }}</span>
            </p>
            <p class="mb-0" v-else-if="order.delivery_method === 'pickup'">
              <strong>Địa điểm nhận hàng:</strong> 
              <span>{{ order.pickup_location || 'N/A' }}</span>
            </p>
          </div>
        </div>
      </div>

      <!-- Danh sách sản phẩm -->
      <div class="mb-4">
        <h6 class="fw-bold mb-3">Danh sách sản phẩm</h6>
        <DataTable 
          :value="items" 
          class="order-items-table"
          :paginator="false"
          :rows="10"
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
          <template #empty>
            <div class="text-center py-3">Không có sản phẩm nào</div>
          </template>
        </DataTable>

        <!-- Tổng tiền -->
        <div class="order-summary mt-3">
          <div class="d-flex justify-content-end">
            <div style="min-width: 300px;">
              <div class="d-flex justify-content-between mb-2">
                <strong>Tạm tính:</strong>
                <strong>{{ formatCurrency(subtotal) }}</strong>
              </div>
              <div class="d-flex justify-content-between border-top pt-2">
                <strong class="fs-5">Tổng cộng:</strong>
                <strong class="fs-5 text-primary">{{ formatCurrency(order.total_amount) }}</strong>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Thông tin thanh toán và vận chuyển -->
      <div class="row mt-4">
        <div class="col-md-6">
          <h6 class="fw-bold mb-3">Thông tin thanh toán</h6>
          <div class="payment-info">
            <p class="mb-2">
              <strong>Phương thức:</strong> 
              <span>{{ getPaymentMethodText(order.payment_method) }}</span>
            </p>
            <p class="mb-2">
              <strong>Trạng thái:</strong> 
              <span v-if="isCancelled" class="badge bg-danger">Đơn hàng đã bị hủy</span>
              <span v-else :class="getPaymentStatusBadgeClass(order.payment_status)">
                {{ getPaymentStatusText(order.payment_status) }}
              </span>
            </p>
            <p class="mb-0" v-if="order.transaction_id">
              <strong>Mã giao dịch:</strong> 
              <span>{{ order.transaction_id }}</span>
            </p>
          </div>
        </div>
        
        <div class="col-md-6">
          <h6 class="fw-bold mb-3">Thông tin vận chuyển</h6>
          <div class="delivery-info">
            <p class="mb-2">
              <strong>Phương thức:</strong> 
              <span>{{ getDeliveryMethodText(order.delivery_method) }}</span>
            </p>
            <p class="mb-0" v-if="order.delivery_method === 'shipping'">
              <strong>Địa chỉ:</strong> 
              <span>{{ getFullAddress() || 'N/A' }}</span>
            </p>
            <p class="mb-0" v-else-if="order.delivery_method === 'pickup'">
              <strong>Địa điểm nhận hàng:</strong> 
              <span>{{ order.pickup_location || 'N/A' }}</span>
            </p>
          </div>
        </div>
      </div>
    </div>

    <template #footer>
      <div class="flex justify-end gap-2">
        <Button 
          label="Đóng" 
          severity="secondary" 
          @click="closeModal"
        />
        <Button 
          v-if="order"
          label="In hóa đơn" 
          icon="pi pi-print"
          severity="success"
          @click="printInvoice"
        />
      </div>
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
  name: 'OrderDetailsModal',
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
      if (this.order) {
        return `Chi tiết đơn hàng #${this.order.order_code}`;
      }
      return 'Chi tiết đơn hàng';
    },
    subtotal() {
      if (!this.items || this.items.length === 0) return 0;
      return this.items.reduce((sum, item) => sum + parseFloat(item.subtotal || 0), 0);
    },
    isCancelled() {
      return (this.order?.order_status || '').toLowerCase() === 'cancelled';
    }
  },
  watch: {
    visible(newVal) {
      if (newVal && this.orderId) {
        this.loadOrderDetails();
      } else {
        this.resetModal();
      }
    },
    orderId(newVal) {
      if (newVal && this.visible) {
        this.loadOrderDetails();
      }
    }
  },
  methods: {
    async loadOrderDetails() {
      if (!this.orderId) return;

      this.loading = true;
      this.error = null;
      this.order = null;
      this.items = [];

      try {
        const response = await axios.get(`/admin/orders/${this.orderId}`);
        
        if (response.data?.success) {
          this.order = response.data.order;
          this.items = response.data.items || [];
        } else {
          this.error = 'Không thể tải dữ liệu đơn hàng. Vui lòng thử lại!';
        }
      } catch (error) {
        console.error('Error loading order details:', error);
        this.error = error.response?.data?.message || 'Đã xảy ra lỗi khi tải dữ liệu. Vui lòng thử lại sau!';
      } finally {
        this.loading = false;
      }
    },
    
    closeModal() {
      this.$emit('close');
    },
    
    resetModal() {
      this.order = null;
      this.items = [];
      this.error = null;
      this.loading = false;
    },
    
    printInvoice() {
      if (!this.order?.id) return;
      const url = `/admin/orders/${this.order.id}/invoice`;
      window.open(url, '_blank');
    },
    
    formatDate(dateString) {
      if (!dateString) return 'N/A';
      const date = new Date(dateString);
      const day = String(date.getDate()).padStart(2, '0');
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const year = date.getFullYear();
      const hours = String(date.getHours()).padStart(2, '0');
      const minutes = String(date.getMinutes()).padStart(2, '0');
      return `${day}/${month}/${year} ${hours}:${minutes}`;
    },
    
    formatCurrency(amount) {
      return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
      }).format(amount);
    },
    
    getStatusBadgeClass(status) {
      const s = (status || '').toString().toLowerCase();
      if (s === 'pending' || s === 'new') {
        return 'badge bg-warning text-dark';
      } else if (s === 'completed') {
        return 'badge bg-success';
      } else if (s === 'cancelled') {
        return 'badge bg-danger';
      }
      return 'badge bg-secondary';
    },
    
    getStatusText(status) {
      const s = (status || '').toString().toLowerCase();
      if (s === 'pending' || s === 'new') {
        return 'Đang chờ xử lý';
      } else if (s === 'completed') {
        return 'Hoàn thành';
      } else if (s === 'cancelled') {
        return 'Đã hủy';
      }
      return 'Không xác định';
    },
    
    getPaymentStatusBadgeClass(status) {
      const s = (status || '').toString().toLowerCase();
      if (s === 'pending' || s === 'unpaid') {
        return 'badge bg-warning text-dark';
      } else if (s === 'paid') {
        return 'badge bg-success';
      } else if (s === 'failed') {
        return 'badge bg-danger';
      } else if (s === 'refunded') {
        return 'badge bg-info';
      } else if (s === 'cancelled') {
        return 'badge bg-danger';
      }
      return 'badge bg-secondary';
    },
    
    getPaymentStatusText(status) {
      const s = (status || '').toString().toLowerCase();
      if (s === 'pending' || s === 'unpaid') {
        return 'Chưa thanh toán';
      } else if (s === 'paid') {
        return 'Đã thanh toán';
      } else if (s === 'failed') {
        return 'Thanh toán thất bại';
      } else if (s === 'refunded') {
        return 'Đã hoàn tiền';
      } else if (s === 'cancelled') {
        return 'Đơn hàng đã bị hủy';
      }
      return 'Không xác định';
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
    
    getDeliveryMethodText(method) {
      const m = (method || '').toString().toLowerCase();
      if (m === 'shipping') {
        return 'Giao hàng tận nơi';
      } else if (m === 'pickup') {
        return 'Nhận tại cửa hàng';
      }
      return method || 'Không xác định';
    },
    
    getFullAddress() {
      if (!this.order) return '';
      const addressParts = [
        this.order.shipping_address,
        this.order.ward,
        this.order.district,
        this.order.province
      ].filter(Boolean);
      return addressParts.join(', ');
    }
  }
}
</script>

<style scoped>
.order-info p,
.customer-info p,
.payment-info p,
.delivery-info p {
  margin-bottom: 8px;
  font-size: 14px;
}

.order-info strong,
.customer-info strong,
.payment-info strong,
.delivery-info strong {
  min-width: 140px;
  display: inline-block;
  color: #495057;
}

.badge {
  display: inline-block;
  padding: 4px 8px;
  font-size: 12px;
  font-weight: 600;
  border-radius: 4px;
}

.bg-warning {
  background-color: #ffc107 !important;
  color: #000 !important;
}

.bg-success {
  background-color: #198754 !important;
  color: #fff !important;
}

.bg-danger {
  background-color: #dc3545 !important;
  color: #fff !important;
}

.bg-info {
  background-color: #0dcaf0 !important;
  color: #fff !important;
}

.bg-secondary {
  background-color: #6c757d !important;
  color: #fff !important;
}

.order-items-table {
  font-size: 14px;
}

.order-items-table :deep(.p-datatable-thead > tr > th) {
  background: #f8f9fa;
  font-weight: 600;
  padding: 10px;
}

.order-items-table :deep(.p-datatable-tbody > tr > td) {
  padding: 10px;
}

.order-summary {
  padding: 16px;
  background: #f8f9fa;
  border-radius: 8px;
}

.text-end {
  text-align: right !important;
}

.text-center {
  text-align: center !important;
}

.fw-bold {
  font-weight: 600 !important;
}

.fs-5 {
  font-size: 1.25rem !important;
}

.border-top {
  border-top: 1px solid #dee2e6 !important;
}

.pt-2 {
  padding-top: 0.5rem !important;
}

.mb-0 {
  margin-bottom: 0 !important;
}

.mb-2 {
  margin-bottom: 0.5rem !important;
}

.mb-3 {
  margin-bottom: 1rem !important;
}

.mb-4 {
  margin-bottom: 1.5rem !important;
}

.mt-3 {
  margin-top: 1rem !important;
}

.mt-4 {
  margin-top: 1.5rem !important;
}

.py-3 {
  padding-top: 0.75rem !important;
  padding-bottom: 0.75rem !important;
}

.py-5 {
  padding-top: 3rem !important;
  padding-bottom: 3rem !important;
}

.alert {
  padding: 1rem;
  margin-bottom: 1rem;
  border: 1px solid transparent;
  border-radius: 0.375rem;
}

.alert-danger {
  color: #842029;
  background-color: #f8d7da;
  border-color: #f5c2c7;
}
</style>
