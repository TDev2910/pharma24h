<template>
  <div class="order-details-container">
    <!-- Header -->
    <div class="details-header">
      <Link href="/user/orders" class="back-button">
        <i class="fas fa-arrow-left"></i> Quay lại
      </Link>
      <h1 class="details-title">Chi tiết đơn hàng</h1>
    </div>

    <!-- Order Info -->
    <div v-if="order" class="order-info-card">
      <!-- Order Header -->
      <div class="order-header-section">
        <div class="order-code-section">
          <span class="order-code-label">Mã đơn hàng:</span>
          <span class="order-code-value">{{ order.order_code || formatOrderCode(order.id) }}</span>
        </div>
        <span class="order-status-badge" :class="getStatusClass(order.order_status)">
          {{ getStatusLabel(order.order_status) }}
        </span>
      </div>

      <div class="order-date">
        <i class="fas fa-calendar"></i>
        Ngày đặt: {{ formatDateTime(order.created_at) }}
      </div>

      <div class="cancellation-section">
        <div v-if="cancellationStatusLabel" class="cancellation-status" :class="`cancellation-${order.cancellation_status}`">
          <i class="fas fa-info-circle"></i>
          <span>{{ cancellationStatusLabel }}</span>
        </div>
        <div v-if="order.cancellation_admin_note && order.cancellation_status === 'rejected'" class="cancellation-note">
          <strong>Lý do từ chối:</strong> {{ order.cancellation_admin_note }}
        </div>
        <div v-if="order.cancellation_reason && order.cancellation_status" class="cancellation-note user-note">
          <strong>Lý do khách hàng:</strong> {{ order.cancellation_reason }}
          <p v-if="order.cancellation_user_note">{{ order.cancellation_user_note }}</p>
        </div>
        <button
          v-if="canRequestCancellation"
          type="button"
          class="btn cancel-btn"
          @click="openCancelModal"
        >
          <i class="fas fa-ban"></i>
          Yêu cầu hủy đơn hàng
        </button>
      </div>

      <!-- Pharmacy Info -->
      <div v-if="order.pickup_location" class="pharmacy-info-section">
        <i class="fas fa-map-marker-alt"></i>
        <span>Nhà thuốc: {{ order.pickup_location }}</span>
      </div>

      <!-- GHN Shipping Info -->
      <div
        v-if="order.delivery_method === 'shipping'"
        class="shipping-info-section"
      >
        <h3 class="section-title">Thông tin vận chuyển GHN</h3>
        <div class="shipping-row">
          <span>Mã vận đơn:</span>
          <strong>{{ order.ghn_order_code || 'Đang tạo' }}</strong>
        </div>
        <div class="shipping-row">
          <span>Trạng thái:</span>
          <span class="ghn-status-badge" :class="getGHNStatusClass(order.ghn_status)">
            {{ getGHNStatusLabel(order.ghn_status) }}
          </span>
        </div>
        <div class="shipping-row" v-if="order.ghn_expected_delivery_time">
          <span>Dự kiến giao:</span>
          <span>{{ formatDateTime(order.ghn_expected_delivery_time) }}</span>
        </div>
        <div class="shipping-row" v-if="order.ghn_fee">
          <span>Phí vận chuyển:</span>
          <span>{{ formatCurrency(order.ghn_fee) }} ₫</span>
        </div>
        <div class="shipping-row" v-if="order.ghn_cod_amount">
          <span>Số tiền thu hộ:</span>
          <span>{{ formatCurrency(order.ghn_cod_amount) }} ₫</span>
        </div>
        <div class="shipping-row" v-if="order.ghn_order_code">
          <span>Tra cứu:</span>
          <a
            class="tracking-link"
            :href="getGHNTrackingUrl(order)"
            target="_blank"
            rel="noopener"
          >
            Xem trên GHN
          </a>
        </div>
        <div
          class="shipping-row"
          v-if="order.ghn_shipper_name || order.ghn_shipper_phone"
        >
          <span>Nhân viên giao:</span>
          <span>
            {{ order.ghn_shipper_name || 'Đang cập nhật' }}
            <template v-if="order.ghn_shipper_phone">
              - {{ order.ghn_shipper_phone }}
            </template>
          </span>
        </div>
      </div>

      <!-- Order Items -->
      <div class="order-items-section">
        <h3 class="section-title">Sản phẩm</h3>
        <div class="items-list">
          <div v-for="(item, index) in order.items" :key="item.id || index" class="item-card">
            <div class="item-image">
              <img :src="getImageUrl(item)" :alt="item.product_name || item.name" @error="handleImageError" />
            </div>
            <div class="item-details">
              <div class="item-name">{{ item.product_name || item.name }}</div>
              <div class="item-category">Phân loại: Viên</div>
              <div class="item-quantity">Số lượng: {{ item.quantity || 1 }}</div>
            </div>
            <div class="item-price">
              {{ formatCurrency(item.price) }} ₫
            </div>
          </div>
        </div>
      </div>

      <!-- Order Summary -->
      <div class="order-summary-section">
        <div class="summary-row">
          <span>Tạm tính:</span>
          <span>{{ formatCurrency(order.total_amount) }} ₫</span>
        </div>
        <div class="summary-row total-row">
          <span>Tổng tiền:</span>
          <span class="total-amount">{{ formatCurrency(order.total_amount) }} ₫</span>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-else class="loading-state">
      <i class="fas fa-spinner fa-spin"></i>
      <p>Đang tải thông tin đơn hàng...</p>
    </div>
  </div>

  <!-- Cancel Request Modal -->
  <div v-if="showCancelModal" class="modal-overlay">
    <div class="modal-card">
      <div class="modal-header">
        <h3>Yêu cầu hủy đơn hàng</h3>
        <button class="modal-close" @click="closeCancelModal">&times;</button>
      </div>
      <div class="modal-body">
        <form @submit.prevent="submitCancel">
          <div class="form-group">
            <label for="cancel-reason">Lý do hủy *</label>
            <select id="cancel-reason" v-model="cancelForm.reason" class="form-control">
              <option value="">-- Chọn lý do --</option>
              <option value="change_of_mind">Đổi ý</option>
              <option value="wrong_product">Đặt nhầm sản phẩm</option>
              <option value="found_better">Tìm được nơi khác phù hợp hơn</option>
              <option value="other">Khác</option>
            </select>
            <small v-if="cancelForm.errors.reason" class="text-error">{{ cancelForm.errors.reason }}</small>
          </div>
          <div class="form-group">
            <label for="cancel-note">Ghi chú thêm</label>
            <textarea
              id="cancel-note"
              v-model="cancelForm.note"
              class="form-control"
              rows="4"
              placeholder="Bạn có thể mô tả chi tiết hơn lý do hủy..."
            ></textarea>
            <small v-if="cancelForm.errors.note" class="text-error">{{ cancelForm.errors.note }}</small>
          </div>
          <div class="modal-actions">
            <button type="button" class="btn secondary" @click="closeCancelModal">
              Đóng
            </button>
            <button type="submit" class="btn primary" :disabled="cancelForm.processing">
              <span v-if="cancelForm.processing">
                <i class="fas fa-spinner fa-spin"></i> Đang gửi...
              </span>
              <span v-else>Gửi yêu cầu</span>
            </button>
          </div>
        </form>
      </div>
      <div v-if="order.is_shipping && order.ghn_order_code" class="shipping-info-section">
     <h3 class="section-title">Vận chuyển GHN</h3>
     <div class="shipping-row">
       <span>Mã vận đơn:</span>
       <strong>{{ order.ghn_order_code }}</strong>
     </div>
     <div class="shipping-row">
       <span>Trạng thái:</span>
       <span class="badge">{{ order.ghn_status_text || 'Đang cập nhật' }}</span>
     </div>
     <div class="shipping-row" v-if="order.ghn_expected_delivery_time">
       <span>Dự kiến giao:</span>
       <span>{{ formatDateTime(order.ghn_expected_delivery_time) }}</span>
     </div>
     <div class="shipping-row">
       <span>Tra cứu:</span>
       <a
         :href="order.ghn_tracking_url || `https://donhang.ghn.vn/?order_code=${order.ghn_order_code}`"
         target="_blank"
         rel="noopener"
       >Xem trên GHN</a>
     </div>
     <Button
       v-if="order.ghn_order_code"
       label="Cập nhật trạng thái"
       icon="pi pi-refresh"
       class="refresh-btn"
       :loading="refreshing"
       @click="refreshGHNStatus"
     />
   </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'

// Props từ Inertia
const props = defineProps({
  order: {
    type: Object,
    default: null
  }
})

const showCancelModal = ref(false)

const cancelForm = useForm({
  reason: '',
  note: ''
})

const canRequestCancellation = computed(() => {
  if (!props.order) return false
  const status = props.order.order_status || ''
  return ['pending', 'new'].includes(status) && props.order.cancellation_status !== 'requested'
})

const cancellationStatusLabel = computed(() => {
  if (!props.order) return ''
  const map = {
    requested: 'Đang chờ Admin xử lý',
    approved: 'Đã hủy thành công',
    rejected: 'Yêu cầu hủy bị từ chối'
  }
  return map[props.order.cancellation_status] || null
})

const openCancelModal = () => {
  showCancelModal.value = true
}

const closeCancelModal = () => {
  showCancelModal.value = false
  cancelForm.reset()
  cancelForm.clearErrors()
}

const submitCancel = () => {
  if (!props.order) return
  cancelForm.post(`/user/orders/${props.order.id}/request-cancel`, {
  preserveScroll: true,
  onSuccess: () => closeCancelModal()
})
}
// Helper functions
const formatOrderCode = (id) => {
  return String(id).padStart(4, '0')
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('vi-VN').format(amount || 0)
}

const formatDateTime = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  const hours = String(date.getHours()).padStart(2, '0')
  const minutes = String(date.getMinutes()).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const year = date.getFullYear()
  return `${hours}:${minutes} ${day}/${month}/${year}`
}

const getImageUrl = (item) => {
  if (item.image) {
    if (item.image.startsWith('http://') || item.image.startsWith('https://')) {
      return item.image
    }
    if (item.image.startsWith('storage/')) {
      return `/storage/${item.image.replace('storage/', '')}`
    }
    return `/storage/${item.image}`
  }
  return 'https://placehold.co/80x80?text=No+Image'
}

const handleImageError = (event) => {
  event.target.src = 'https://placehold.co/80x80?text=No+Image'
}

const getStatusClass = (status) => {
  const statusMap = {
    'new': 'status-new',
    'delivered': 'status-delivered',
    'completed': 'status-completed',
    'cancelled': 'status-cancelled'
  }
  return statusMap[status] || 'status-default'
}

const getStatusLabel = (status) => {
  const labelMap = {
    'new': 'Chờ xử lý',
    'delivered': 'Đã giao',
    'completed': 'Hoàn thành',
    'cancelled': 'Đã hủy'
  }
  return labelMap[status] || status
}

const normalizeStatus = (status) => (status ? status.toString().toLowerCase() : '')

const getGHNStatusLabel = (status) => {
  if (!status) return 'Chưa có'
  const normalized = normalizeStatus(status)
  const map = {
    'ready_to_pick': 'Sẵn sàng lấy hàng',
    'picking': 'Đang lấy hàng',
    'storing': 'Đang lưu kho',
    'transporting': 'Đang vận chuyển',
    'delivering': 'Đang giao',
    'delivered': 'Đã giao hàng',
    'return': 'Đang hoàn trả',
    'cancel': 'Đã hủy'
  }
  return map[normalized] || status
}

const getGHNStatusClass = (status) => {
  const s = normalizeStatus(status)
  if (!s) return 'ghn-status-default'
  const map = {
    'ready_to_pick': 'ghn-status-info',
    'picking': 'ghn-status-info',
    'storing': 'ghn-status-secondary',
    'transporting': 'ghn-status-warning',
    'delivering': 'ghn-status-primary',
    'delivered': 'ghn-status-success',
    'return': 'ghn-status-danger',
    'cancel': 'ghn-status-danger'
  }
  return map[s] || 'ghn-status-default'
}

const getGHNTrackingUrl = (order) => {
  if (!order?.ghn_order_code) return '#'
  return order.ghn_tracking_url || `https://donhang.ghn.vn/?order_code=${order.ghn_order_code}`
}
</script>

<style scoped>
.order-details-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.details-header {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-bottom: 24px;
}

.back-button {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #0d6efd;
  text-decoration: none;
  font-size: 14px;
  transition: color 0.3s ease;
}

.back-button:hover {
  color: #0a58ca;
  text-decoration: none;
}

.details-title {
  font-size: 28px;
  font-weight: 700;
  color: #2c3e50;
  margin: 0;
}

.order-info-card {
  background: #ffffff;
  border: 1px solid #e9ecef;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.order-header-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  padding-bottom: 16px;
  border-bottom: 1px solid #e9ecef;
}

.order-code-section {
  display: flex;
  align-items: center;
  gap: 8px;
}

.order-code-label {
  color: #6c757d;
  font-size: 14px;
}

.order-code-value {
  color: #2c3e50;
  font-size: 16px;
  font-weight: 600;
}

.order-status-badge {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
}

.status-new {
  background-color: #fff3cd;
  color: #856404;
}

.status-delivered,
.status-completed {
  background-color: #d4edda;
  color: #155724;
}

.status-cancelled {
  background-color: #f8d7da;
  color: #721c24;
}

.order-date {
  color: #6c757d;
  font-size: 14px;
  margin-bottom: 12px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.pharmacy-info-section {
  color: #495057;
  font-size: 14px;
  margin-bottom: 24px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.shipping-info-section {
  margin-bottom: 24px;
  padding: 16px;
  border: 1px solid #e9ecef;
  border-radius: 10px;
  background: #f8f9fa;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.shipping-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 14px;
  color: #495057;
}

.shipping-row span:first-child {
  color: #6c757d;
}

.tracking-link {
  color: #0d6efd;
  text-decoration: none;
  font-weight: 600;
}

.tracking-link:hover {
  text-decoration: underline;
}

.ghn-status-badge {
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
}

.ghn-status-default {
  background: #e9ecef;
  color: #495057;
}

.ghn-status-info {
  background: #cff4fc;
  color: #055160;
}

.ghn-status-secondary {
  background: #e2e3e5;
  color: #41464b;
}

.ghn-status-warning {
  background: #fff3cd;
  color: #664d03;
}

.ghn-status-primary {
  background: #cfe2ff;
  color: #084298;
}

.ghn-status-success {
  background: #d1e7dd;
  color: #0f5132;
}

.ghn-status-danger {
  background: #f8d7da;
  color: #842029;
}

.order-items-section {
  margin-bottom: 24px;
}

.section-title {
  font-size: 18px;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 16px;
}

.items-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.item-card {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px;
  background-color: #f8f9fa;
  border-radius: 8px;
}

.item-image {
  width: 80px;
  height: 80px;
  flex-shrink: 0;
  border-radius: 8px;
  overflow: hidden;
  background-color: #e9ecef;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-details {
  flex: 1;
  min-width: 0;
}

.item-name {
  font-size: 14px;
  font-weight: 500;
  color: #2c3e50;
  margin-bottom: 4px;
}

.item-category,
.item-quantity {
  font-size: 12px;
  color: #6c757d;
  margin-bottom: 2px;
}

.item-price {
  font-size: 16px;
  font-weight: 600;
  color: #0d6efd;
  white-space: nowrap;
}

.order-summary-section {
  padding-top: 16px;
  border-top: 1px solid #e9ecef;
}

.cancellation-section {
  margin-top: 24px;
  padding: 16px;
  border: 1px dashed #f1c40f;
  border-radius: 8px;
  background: #fffdf5;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.cancellation-status {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 600;
  font-size: 14px;
}

.cancellation-requested {
  color: #b7791f;
}

.cancellation-approved {
  color: #2d6a4f;
}

.cancellation-rejected {
  color: #c92a2a;
}

.cancellation-note {
  font-size: 14px;
  color: #495057;
}

.cancellation-note.user-note p {
  margin: 4px 0 0;
}

.btn.cancel-btn {
  align-self: flex-start;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: transparent;
  color: #c92a2a;
  border: 1px solid #c92a2a;
  padding: 8px 16px;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn.cancel-btn:hover {
  background: #c92a2a;
  color: #fff;
}

.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 16px;
}

.modal-card {
  width: 100%;
  max-width: 520px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
  overflow: hidden;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 20px;
  border-bottom: 1px solid #f1f3f5;
}

.modal-header h3 {
  margin: 0;
  font-size: 18px;
  color: #2c3e50;
}

.modal-close {
  background: transparent;
  border: none;
  font-size: 24px;
  line-height: 1;
  cursor: pointer;
  color: #6c757d;
}

.modal-body {
  padding: 20px;
}

.form-group {
  margin-bottom: 16px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-control {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #ced4da;
  border-radius: 6px;
  font-size: 14px;
}

.form-control:focus {
  outline: none;
  border-color: #0d6efd;
  box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.15);
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 24px;
}

.btn.secondary {
  background: #f1f3f5;
  border: none;
  color: #495057;
}

.btn.primary {
  background: #d62828;
  color: #fff;
  border: none;
}

.btn.primary[disabled] {
  opacity: 0.7;
  cursor: not-allowed;
}

.text-error {
  color: #dc3545;
  font-size: 12px;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
  font-size: 14px;
  color: #495057;
}

.total-row {
  font-size: 18px;
  font-weight: 600;
  color: #2c3e50;
  margin-top: 8px;
  padding-top: 8px;
  border-top: 1px solid #e9ecef;
}

.total-amount {
  color: #dc3545;
  font-size: 20px;
}

.loading-state {
  text-align: center;
  padding: 60px 20px;
  color: #6c757d;
}

.loading-state i {
  font-size: 48px;
  margin-bottom: 16px;
}

/* Responsive */
@media (max-width: 768px) {
  .details-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .order-header-section {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }

  .item-card {
    flex-wrap: wrap;
  }

  .item-price {
    width: 100%;
    text-align: right;
  }
}
</style>