<template>
  <Dialog :visible="visible" @update:visible="$emit('close')" :header="modalTitle"
    :style="{ width: '1000px', maxWidth: '95vw' }" modal :closable="true" :draggable="false" :loading="loading"
    class="order-edit-modal">
    <div v-if="loading && !order" class="text-center py-5">
      <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
      <p class="mt-3">Đang tải dữ liệu...</p>
    </div>

    <div v-else-if="error && !order" class="alert alert-danger">
      {{ error }}
    </div>

    <div v-else-if="order">
      <!-- Quick Status Update Section -->
      <div class="status-quick-update mb-4">
        <div class="status-header">
          <h6 class="fw-bold mb-2">
            <i class="pi pi-info-circle me-2"></i>
            Cập nhật trạng thái nhanh
          </h6>
          <p class="text-muted small mb-3">Chọn trạng thái để cập nhật ngay lập tức</p>
        </div>
        <div class="status-buttons">
          <Button v-for="status in statusOptions" :key="status.value" :label="status.label" :icon="status.icon"
            :severity="status.severity" :class="{ 'status-active': order.order_status === status.value }"
            @click="updateStatusQuick(status.value)" :loading="statusLoading === status.value"
            :disabled="order.order_status === status.value" class="status-btn" />
        </div>
      </div>

      <Divider />

      <!-- Form Edit Section -->
      <div class="form-edit-section">
        <h6 class="section-title mb-3">
          <i class="pi pi-pencil me-2"></i>
          Thông tin đơn hàng
        </h6>

        <div class="form-grid">
          <!-- Thông tin khách hàng -->
          <div class="form-section">
            <h6 class="form-section-title">Thông tin khách hàng</h6>

            <div class="form-row">
              <div class="form-field">
                <label for="customerName" class="field-label">
                  Họ tên <span class="text-danger">*</span>
                </label>
                <InputText id="customerName" v-model="formData.customer_name" placeholder="Nhập họ tên khách hàng"
                  :class="{ 'p-invalid': errors.customer_name }" />
                <small v-if="errors.customer_name" class="p-error">
                  {{ errors.customer_name[0] }}
                </small>
              </div>

              <div class="form-field">
                <label for="customerPhone" class="field-label">
                  Số điện thoại <span class="text-danger">*</span>
                </label>
                <InputText id="customerPhone" v-model="formData.customer_phone" placeholder="Nhập số điện thoại"
                  :class="{ 'p-invalid': errors.customer_phone }" />
                <small v-if="errors.customer_phone" class="p-error">
                  {{ errors.customer_phone[0] }}
                </small>
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label for="customerEmail" class="field-label">Email</label>
                <InputText id="customerEmail" v-model="formData.customer_email" type="email" placeholder="Nhập email"
                  :class="{ 'p-invalid': errors.customer_email }" />
                <small v-if="errors.customer_email" class="p-error">
                  {{ errors.customer_email[0] }}
                </small>
              </div>

              <div class="form-field">
                <label for="paymentMethod" class="field-label">Phương thức thanh toán</label>
                <Dropdown id="paymentMethod" v-model="formData.payment_method" :options="paymentMethodOptions"
                  optionLabel="label" optionValue="value" placeholder="Chọn phương thức thanh toán"
                  :class="{ 'p-invalid': errors.payment_method }" />
                <small v-if="errors.payment_method" class="p-error">
                  {{ errors.payment_method[0] }}
                </small>
              </div>
            </div>
          </div>

          <!-- Thông tin giao hàng -->
          <div class="form-section">
            <h6 class="form-section-title">Thông tin giao hàng</h6>

            <div class="form-row">
              <div class="form-field">
                <label for="deliveryMethod" class="field-label">Phương thức nhận hàng</label>
                <Dropdown id="deliveryMethod" v-model="formData.delivery_method" :options="deliveryMethodOptions"
                  optionLabel="label" optionValue="value" placeholder="Chọn phương thức"
                  :class="{ 'p-invalid': errors.delivery_method }" @change="onDeliveryMethodChange" />
                <small v-if="errors.delivery_method" class="p-error">
                  {{ errors.delivery_method[0] }}
                </small>
              </div>

              <div class="form-field" v-if="formData.delivery_method === 'pickup'">
                <label for="pickupLocation" class="field-label">Địa điểm nhận hàng</label>
                <InputText id="pickupLocation" v-model="formData.pickup_location" placeholder="Nhập địa điểm nhận hàng"
                  :class="{ 'p-invalid': errors.pickup_location }" />
                <small v-if="errors.pickup_location" class="p-error">
                  {{ errors.pickup_location[0] }}
                </small>
              </div>
            </div>

            <div v-if="formData.delivery_method === 'shipping'" class="shipping-address-fields">
              <div class="form-row">
                <div class="form-field">
                  <label for="shippingAddress" class="field-label">Địa chỉ giao hàng</label>
                  <InputText id="shippingAddress" v-model="formData.shipping_address" placeholder="Nhập địa chỉ"
                    :class="{ 'p-invalid': errors.shipping_address }" />
                  <small v-if="errors.shipping_address" class="p-error">
                    {{ errors.shipping_address[0] }}
                  </small>
                </div>
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label for="province" class="field-label">Tỉnh/Thành phố</label>
                  <InputText id="province" v-model="formData.province" placeholder="Nhập tỉnh/thành phố"
                    :class="{ 'p-invalid': errors.province }" />
                  <small v-if="errors.province" class="p-error">
                    {{ errors.province[0] }}
                  </small>
                </div>

                <div class="form-field">
                  <label for="district" class="field-label">Quận/Huyện</label>
                  <InputText id="district" v-model="formData.district" placeholder="Nhập quận/huyện"
                    :class="{ 'p-invalid': errors.district }" />
                  <small v-if="errors.district" class="p-error">
                    {{ errors.district[0] }}
                  </small>
                </div>

                <div class="form-field">
                  <label for="ward" class="field-label">Phường/Xã</label>
                  <InputText id="ward" v-model="formData.ward" placeholder="Nhập phường/xã"
                    :class="{ 'p-invalid': errors.ward }" />
                  <small v-if="errors.ward" class="p-error">
                    {{ errors.ward[0] }}
                  </small>
                </div>
              </div>
            </div>
          </div>

          <!-- Ghi chú -->
          <div class="form-section full-width">
            <h6 class="form-section-title">Ghi chú</h6>
            <div class="form-field">
              <Textarea v-model="formData.note" rows="3" placeholder="Nhập ghi chú cho đơn hàng (nếu có)"
                :class="{ 'p-invalid': errors.note }" style="width: 100%" />
              <small v-if="errors.note" class="p-error">
                {{ errors.note[0] }}
              </small>
            </div>
          </div>
        </div>
      </div>

      <Divider />

      <!-- Order Details Section (Collapsible) -->
      <div class="order-details-section">
        <div class="details-toggle" @click="showDetails = !showDetails">
          <h6 class="section-title mb-0">
            <i :class="['pi', 'me-2', showDetails ? 'pi-chevron-down' : 'pi-chevron-right']"></i>
            Chi tiết đơn hàng
          </h6>
          <i :class="['pi', showDetails ? 'pi-chevron-up' : 'pi-chevron-down']"></i>
        </div>

        <div v-if="showDetails" class="details-content mt-3">
          <!-- Order Info -->
          <div class="row mb-3">
            <div class="col-md-6">
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
            </div>
            <div class="col-md-6">
              <p class="mb-2">
                <strong>Trạng thái thanh toán:</strong>
                <span :class="getPaymentStatusBadgeClass(order.payment_status)">
                  {{ getPaymentStatusText(order.payment_status) }}
                </span>
              </p>
              <p class="mb-2" v-if="order.transaction_id">
                <strong>Mã giao dịch:</strong>
                <span>{{ order.transaction_id }}</span>
              </p>
            </div>
          </div>

          <!-- Items Table -->
          <div v-if="items && items.length > 0" class="mt-3">
            <DataTable :value="items" class="order-items-table" :paginator="false">
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
        </div>
      </div>
    </div>

    <template #footer>
      <div class="flex justify-between align-items-center w-full">
        <div class="flex gap-2">
          <Button label="Đóng" severity="secondary" icon="pi pi-times" @click="closeModal" />
          <Button label="Lưu thông tin" icon="pi pi-check" @click="saveOrderInfo" :loading="saving" />
        </div>
      </div>
    </template>

    <Toast />
  </Dialog>
</template>

<script>
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Dropdown from 'primevue/dropdown'
import Textarea from 'primevue/textarea'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Divider from 'primevue/divider'
import Toast from 'primevue/toast'
import axios from 'axios'

export default {
  name: 'OrderEditModal',
  components: {
    Dialog,
    Button,
    InputText,
    Dropdown,
    Textarea,
    DataTable,
    Column,
    Divider,
    Toast
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
  emits: ['close', 'updated'],
  data() {
    return {
      loading: false,
      saving: false,
      statusLoading: null,
      error: null,
      order: null,
      items: [],
      showDetails: false,
      formData: {
        customer_name: '',
        customer_email: '',
        customer_phone: '',
        shipping_address: '',
        province: '',
        district: '',
        ward: '',
        pickup_location: '',
        note: '',
        delivery_method: 'shipping',
        payment_method: 'cash'
      },
      errors: {},
      statusOptions: [
        {
          label: 'Đang chờ xử lý',
          value: 'pending',
          icon: 'pi pi-clock',
          severity: 'warning'
        },
        {
          label: 'Hoàn thành',
          value: 'completed',
          icon: 'pi pi-check-circle',
          severity: 'success'
        },
        {
          label: 'Hủy đơn hàng',
          value: 'cancelled',
          icon: 'pi pi-times-circle',
          severity: 'danger'
        }
      ],
      paymentMethodOptions: [
        { label: 'Tiền mặt', value: 'cash' },
        { label: 'Chuyển khoản', value: 'transfer' },
        { label: 'VNPay', value: 'vnpay' },
        { label: 'Ví MoMo', value: 'momo' },
        { label: 'ZaloPay', value: 'zalopay' }
      ],
      deliveryMethodOptions: [
        { label: 'Giao hàng tận nơi', value: 'shipping' },
        { label: 'Nhận tại cửa hàng', value: 'pickup' }
      ]
    }
  },
  computed: {
    modalTitle() {
      if (this.order) {
        return `Chỉnh sửa đơn hàng #${this.order.order_code}`;
      }
      return 'Chỉnh sửa đơn hàng';
    },
    subtotal() {
      if (!this.items || this.items.length === 0) return 0;
      return this.items.reduce((sum, item) => sum + parseFloat(item.subtotal || 0), 0);
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
      this.errors = {};

      try {
        const response = await axios.get(`/staff/orders/${this.orderId}`);

        if (response.data?.success) {
          this.order = response.data.order;
          this.items = response.data.items || [];

          // Populate form data
          this.formData = {
            customer_name: this.order.customer_name || '',
            customer_email: this.order.customer_email || '',
            customer_phone: this.order.customer_phone || '',
            shipping_address: this.order.shipping_address || '',
            province: this.order.province || '',
            district: this.order.district || '',
            ward: this.order.ward || '',
            pickup_location: this.order.pickup_location || '',
            note: this.order.note || '',
            delivery_method: this.order.delivery_method || 'shipping',
            payment_method: this.order.payment_method || 'cash'
          };
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

    async updateStatusQuick(newStatus) {
      if (!this.orderId) return;

      this.statusLoading = newStatus;

      try {
        const response = await axios.post(`/staff/orders/${this.orderId}/update-status`, {
          status: newStatus
        });

        if (response.data?.success) {
          this.order.order_status = newStatus;

          this.$toast.add({
            severity: 'success',
            summary: 'Thành công',
            detail: 'Đã cập nhật trạng thái đơn hàng',
            life: 3000
          });

          this.$emit('updated', this.order);
        } else {
          this.$toast.add({
            severity: 'error',
            summary: 'Lỗi',
            detail: response.data?.message || 'Cập nhật trạng thái thất bại',
            life: 3000
          });
        }
      } catch (error) {
        console.error('Error updating status:', error);
        this.$toast.add({
          severity: 'error',
          summary: 'Lỗi',
          detail: error.response?.data?.message || 'Có lỗi xảy ra khi cập nhật trạng thái',
          life: 3000
        });
      } finally {
        this.statusLoading = null;
      }
    },

    async saveOrderInfo() {
      if (!this.orderId) return;

      this.saving = true;
      this.errors = {};

      try {
        const payload = {
          customer_name: this.formData.customer_name,
          customer_email: this.formData.customer_email,
          customer_phone: this.formData.customer_phone,
          shipping_address: this.formData.shipping_address,
          province: this.formData.province,
          district: this.formData.district,
          ward: this.formData.ward,
          pickup_location: this.formData.pickup_location,
          note: this.formData.note,
          delivery_method: this.formData.delivery_method,
          payment_method: this.formData.payment_method
        };

        const response = await axios.post(`/staff/orders/${this.orderId}`, payload, {
          headers: {
            'X-HTTP-Method-Override': 'PATCH'
          }
        });

        if (response.data?.success) {
          this.$toast.add({
            severity: 'success',
            summary: 'Thành công',
            detail: 'Đã lưu thông tin đơn hàng',
            life: 3000
          });

          // Reload order details to get updated data
          await this.loadOrderDetails();

          this.$emit('updated', this.order);
        } else {
          this.$toast.add({
            severity: 'error',
            summary: 'Lỗi',
            detail: response.data?.message || 'Lưu thông tin thất bại',
            life: 3000
          });
        }
      } catch (error) {
        console.error('Error saving order info:', error);

        if (error.response?.status === 422) {
          this.errors = error.response.data.errors || {};
          this.$toast.add({
            severity: 'error',
            summary: 'Lỗi validation',
            detail: 'Vui lòng kiểm tra lại thông tin nhập vào',
            life: 5000
          });
        } else {
          this.$toast.add({
            severity: 'error',
            summary: 'Lỗi',
            detail: error.response?.data?.message || 'Có lỗi xảy ra khi lưu thông tin',
            life: 3000
          });
        }
      } finally {
        this.saving = false;
      }
    },

    onDeliveryMethodChange() {
      // Reset shipping fields if switching to pickup
      if (this.formData.delivery_method === 'pickup') {
        this.formData.shipping_address = '';
        this.formData.province = '';
        this.formData.district = '';
        this.formData.ward = '';
      }
      // Reset pickup location if switching to shipping
      if (this.formData.delivery_method === 'shipping') {
        this.formData.pickup_location = '';
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
      this.saving = false;
      this.statusLoading = null;
      this.showDetails = false;
      this.errors = {};
      this.formData = {
        customer_name: '',
        customer_email: '',
        customer_phone: '',
        shipping_address: '',
        province: '',
        district: '',
        ward: '',
        pickup_location: '',
        note: '',
        delivery_method: 'shipping',
        payment_method: 'cash'
      };
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
    }
  }
}
</script>

<style scoped>
/* Import CSS file - CSS thông thường được tách ra */
@import '@Staff/orders/modals.css';
</style>

<style>
/* Giữ nguyên tất cả :deep() trong file Vue */
.order-edit-modal :deep(.p-dialog-content) {
  max-height: 80vh;
  overflow-y: auto;
}

.order-items-table :deep(.p-datatable-thead > tr > th) {
  background: #f8f9fa;
  font-weight: 600;
  padding: 10px;
}

.order-items-table :deep(.p-datatable-tbody > tr > td) {
  padding: 10px;
}
</style>