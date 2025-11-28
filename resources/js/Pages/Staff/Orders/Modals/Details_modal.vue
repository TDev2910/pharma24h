<template>
  <Dialog :visible="visible" @update:visible="$emit('close')" :header="modalTitle" :style="{ width: '900px' }" modal
    :closable="true" :loading="loading">
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
        <DataTable :value="items" class="order-items-table" :paginator="false" :rows="10">
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
      <!-- Thông tin GHN -->
      <div v-if="order.delivery_method === 'shipping'" class="ghn-info-section mt-4">
        <div class="d-flex align-items-center gap-2 mb-3">
          <i class="pi pi-truck text-primary"></i>
          <h6 class="fw-bold mb-0">Thông tin Giao hàng nhanh (GHN)</h6>
        </div>

        <div v-if="order.ghn_order_code" class="ghn-details">
          <div class="row">
            <div class="col-md-6">
              <p class="mb-2">
                <strong>Mã vận đơn GHN:</strong>
                <span class="text-primary fw-bold">{{ order.ghn_order_code }}</span>
              </p>
              <p class="mb-2">
                <strong>Trạng thái GHN:</strong>
                <span :class="getGHNStatusBadgeClass(order.ghn_status)">
                  {{ getGHNStatusText(order.ghn_status) }}
                </span>
              </p>
              <p class="mb-2" v-if="order.ghn_fee">
                <strong>Phí vận chuyển:</strong>
                <span>{{ formatCurrency(order.ghn_fee) }}</span>
              </p>
            </div>
            <div class="col-md-6">
              <p class="mb-2" v-if="order.ghn_expected_delivery_time">
                <strong>Dự kiến giao hàng:</strong>
                <span>{{ formatDate(order.ghn_expected_delivery_time) }}</span>
              </p>
              <p class="mb-2" v-if="order.ghn_shipper_name">
                <strong>Tên shipper:</strong>
                <span>{{ order.ghn_shipper_name }}</span>
              </p>
              <p class="mb-2" v-if="order.ghn_shipper_phone">
                <strong>SĐT shipper:</strong>
                <span>{{ order.ghn_shipper_phone }}</span>
              </p>
              <p class="mb-0" v-if="order.ghn_tracking_url">
                <a :href="order.ghn_tracking_url" target="_blank" class="btn btn-sm btn-outline-primary">
                  <i class="pi pi-external-link"></i> Theo dõi đơn hàng
                </a>
              </p>
            </div>
          </div>
        </div>

        <div v-else class="ghn-create-section">
          <div class="alert alert-info d-flex align-items-center gap-2">
            <i class="pi pi-info-circle"></i>
            <span>Đơn hàng chưa được tạo trên GHN. Vui lòng tạo đơn GHN để giao hàng.</span>
          </div>
          <div v-if="!order.district_id || !order.ward_code" class="alert alert-warning mt-2">
            <i class="pi pi-exclamation-triangle"></i>
            <span>Đơn hàng thiếu thông tin địa chỉ (district_id hoặc ward_code). Vui lòng kiểm tra lại địa chỉ giao
              hàng.</span>
          </div>
        </div>
      </div>
    </div>

    <template #footer>
      <div class="flex justify-end gap-2">
        <Button label="Đóng" severity="secondary" @click="closeModal" />
        <Button v-if="order && order.delivery_method === 'shipping' && order.ghn_order_code"
          label="Đồng bộ trạng thái GHN" icon="pi pi-sync" severity="info" :loading="syncingGHN"
          @click="syncGhnStatus" />
        <Button
          v-if="order && order.delivery_method === 'shipping' && !order.ghn_order_code && order.district_id && order.ward_code"
          label="Tạo đơn GHN" icon="pi pi-truck" severity="info" :loading="creatingGHN" @click="createGHNOrder" />
        <Button v-if="order" label="In hóa đơn" icon="pi pi-print" severity="success" @click="printInvoice" />
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
      items: [],
      creatingGHN: false,
      syncingGHN: false,
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
        const response = await axios.get(`/staff/orders/${this.orderId}`);

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
      this.creatingGHN = false;
      this.syncingGHN = false;
    },

    printInvoice() {
      if (!this.order?.id) return;
      const url = `/staff/orders/${this.order.id}/invoice`;
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

    async createGHNOrder() {
      if (!this.order?.id) return;

      if (!confirm('Bạn có chắc chắn muốn tạo đơn GHN cho đơn hàng này?')) {
        return;
      }

      try {
        this.creatingGHN = true;
        const response = await axios.post(`/staff/ghn/orders/${this.order.id}/create`);

        if (response.data?.success) {
          alert('Tạo đơn GHN thành công!');

          // Reload order details để cập nhật thông tin GHN
          await this.loadOrderDetails();
        } else {
          alert(response.data?.message || 'Không thể tạo đơn GHN.');
        }
      } catch (error) {
        console.error('createGHNOrder error:', error);
        alert(error.response?.data?.message || 'Đã xảy ra lỗi khi tạo đơn GHN.');
      } finally {
        this.creatingGHN = false;
      }
    },

    async syncGhnStatus() {
      if (!this.order?.id || !this.order?.ghn_order_code) return;

      try {
        this.syncingGHN = true;
        const response = await axios.post(`/staff/ghn/orders/${this.order.id}/sync-status`);

        if (response.data?.success) {
          alert('Đồng bộ trạng thái GHN thành công!');

          // Reload order details để cập nhật thông tin
          await this.loadOrderDetails();
        } else {
          alert(response.data?.message || 'Không thể đồng bộ trạng thái GHN.');
        }
      } catch (error) {
        console.error('syncGhnStatus error:', error);
        alert(error.response?.data?.message || 'Đã xảy ra lỗi khi đồng bộ trạng thái GHN.');
      } finally {
        this.syncingGHN = false;
      }
    },

    getGHNStatusBadgeClass(status) {
      if (!status) return 'badge bg-secondary';
      const s = status.toString().toLowerCase();
      const statusMap = {
        'ready_to_pick': 'badge bg-info',
        'picking': 'badge bg-primary',
        'storing': 'badge bg-secondary',
        'transporting': 'badge bg-warning text-dark',
        'delivering': 'badge bg-primary',
        'delivered': 'badge bg-success',
        'return': 'badge bg-danger',
        'cancel': 'badge bg-danger'
      };
      return statusMap[s] || 'badge bg-secondary';
    },

    getGHNStatusText(status) {
      if (!status) return 'Chưa có';
      const s = status.toString().toLowerCase();
      const statusMap = {
        'ready_to_pick': 'Sẵn sàng lấy hàng',
        'picking': 'Đang lấy hàng',
        'storing': 'Đang lưu kho',
        'transporting': 'Đang vận chuyển',
        'delivering': 'Đang giao hàng',
        'delivered': 'Đã giao hàng',
        'return': 'Hoàn trả',
        'cancel': 'Đã hủy'
      };
      return statusMap[s] || status;
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
      switch (m) {
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

.ghn-info-section {
  border: 1px solid #dee2e6;
  border-radius: 8px;
  padding: 16px;
  background: #f8f9fa;
}

.ghn-details {
  margin-top: 12px;
}

.ghn-create-section {
  margin-top: 12px;
}

.alert-info {
  color: #055160;
  background-color: #cff4fc;
  border-color: #b6effb;
}

.alert-warning {
  color: #664d03;
  background-color: #fff3cd;
  border-color: #ffecb5;
}

.text-primary {
  color: #0d6efd !important;
}

.gap-2 {
  gap: 0.5rem;
}

.bg-primary {
  background-color: #0d6efd !important;
  color: #fff !important;
}

.btn-sm {
  padding: 4px 12px;
  font-size: 13px;
  border-radius: 4px;
}

.btn-outline-primary {
  color: #0d6efd;
  border-color: #0d6efd;
  background-color: transparent;
  text-decoration: none;
  display: inline-block;
  padding: 4px 12px;
  border-radius: 4px;
  border: 1px solid;
  transition: all 0.2s;
  cursor: pointer;
}

.btn-outline-primary:hover {
  color: #fff;
  background-color: #0d6efd;
  border-color: #0d6efd;
}

.d-flex {
  display: flex !important;
}

.align-items-center {
  align-items: center !important;
}

.justify-content-end {
  justify-content: flex-end !important;
}

.justify-content-between {
  justify-content: space-between !important;
}
</style>