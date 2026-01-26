<template>
  <div class="service-details-container">
    <!-- Header -->
    <div class="details-header">
      <Link href="/user/services" class="back-button">
        <i class="fas fa-arrow-left"></i> Quay lại
      </Link>
      <h1 class="details-title">Chi tiết dịch vụ</h1>
    </div>

    <!-- Service Info -->
    <div v-if="booking" class="service-info-card">
      <!-- Service Header -->
      <div class="service-header-section">
        <div class="service-code-section">
          <span class="service-code-label">Mã đặt lịch:</span>
          <span class="service-code-value">#{{ formatBookingCode(booking.id) }}</span>
        </div>
        <span class="service-status-badge" :class="getStatusClass(booking.status)">
          <i :class="getStatusIcon(booking.status)"></i>
          {{ getStatusLabel(booking.status) }}
        </span>
      </div>

      <div class="service-date">
        <i class="fas fa-calendar"></i>
        Ngày đặt: {{ formatDateTime(booking.created_at) }}
      </div>

      <!-- Service Information -->
      <div class="service-information-section">
        <h3 class="section-title">
          <i class="fas fa-info-circle"></i>
          Thông tin dịch vụ
        </h3>

        <div class="service-detail-card">
          <div class="service-image-large" v-if="booking.service?.image">
            <img :src="getServiceImageUrl(booking.service)" :alt="booking.service?.ten_dich_vu"
              @error="handleImageError" />
          </div>
          <div class="service-info-content">
            <h2 class="service-name-large">{{ booking.service?.ten_dich_vu || 'Dịch vụ không xác định' }}</h2>

            <div class="info-grid">
              <div class="info-item">
                <div class="info-icon">
                  <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="info-content">
                  <div class="info-label">Ngày hẹn</div>
                  <div class="info-value">{{ formatDate(booking.booking_date) }}</div>
                </div>
              </div>

              <div class="info-item">
                <div class="info-icon">
                  <i class="fas fa-clock"></i>
                </div>
                <div class="info-content">
                  <div class="info-label">Giờ hẹn</div>
                  <div class="info-value">{{ formatTime(booking.booking_time) }}</div>
                </div>
              </div>

              <div class="info-item" v-if="booking.service?.thoi_gian_thuc_hien">
                <div class="info-icon">
                  <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="info-content">
                  <div class="info-label">Thời gian thực hiện</div>
                  <div class="info-value">{{ booking.service.thoi_gian_thuc_hien }}</div>
                </div>
              </div>

              <div class="info-item" v-if="booking.service?.hinh_thuc">
                <div class="info-icon">
                  <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="info-content">
                  <div class="info-label">Hình thức</div>
                  <div class="info-value">{{ getServiceTypeLabel(booking.service.hinh_thuc) }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div v-if="booking.service?.mo_ta" class="service-description">
          <div class="description-label">Mô tả dịch vụ:</div>
          <div v-html="sanitizedDescription" class="description-content html-content"></div>
        </div>
      </div>

      <!-- Customer Information -->
      <div class="customer-information-section">
        <h3 class="section-title">
          <i class="fas fa-user"></i>
          Thông tin khách hàng
        </h3>

        <div class="info-card">
          <div class="info-row">
            <span class="info-label">Họ và tên:</span>
            <span class="info-value">{{ booking.customer_name }}</span>
          </div>
          <div class="info-row" v-if="booking.customer_phone">
            <span class="info-label">Số điện thoại:</span>
            <span class="info-value">{{ booking.customer_phone }}</span>
          </div>
          <div class="info-row" v-if="booking.customer_email">
            <span class="info-label">Email:</span>
            <span class="info-value">{{ booking.customer_email }}</span>
          </div>
        </div>
      </div>

      <!-- Booking Notes -->
      <div class="notes-section" v-if="booking.notes">
        <h3 class="section-title">
          <i class="fas fa-sticky-note"></i>
          Ghi chú
        </h3>
        <div class="notes-card">
          <p class="notes-content">{{ booking.notes }}</p>
        </div>
      </div>

      <!-- Payment & Price Summary -->
      <div class="payment-summary-section">
        <h3 class="section-title">
          <i class="fas fa-receipt"></i>
          Thông tin thanh toán
        </h3>

        <div class="summary-card">
          <div class="summary-row">
            <span class="summary-label">Trạng thái thanh toán:</span>
            <span class="payment-status-badge" :class="getPaymentStatusClass(booking.payment_status)">
              <i :class="getPaymentStatusIcon(booking.payment_status)"></i>
              {{ getPaymentStatusLabel(booking.payment_status) }}
            </span>
          </div>

          <div class="summary-row" v-if="booking.payment_method">
            <span class="summary-label">Phương thức thanh toán:</span>
            <span class="summary-value">{{ getPaymentMethodLabel(booking.payment_method) }}</span>
          </div>

          <div class="summary-row price-row">
            <span class="summary-label">Giá dịch vụ:</span>
            <span class="price-value-large">{{ formatCurrency(booking.price) }} ₫</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-else class="loading-state">
      <i class="fas fa-spinner fa-spin"></i>
      <p>Đang tải thông tin dịch vụ...</p>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import DOMPurify from 'dompurify'

// Props từ Inertia
const props = defineProps({
  booking: {
    type: Object,
    default: null
  }
})


//Sanitize HTML từ mo_ta để tránh XSS
const sanitizedDescription = computed(() => {
  const dirtyHTML = props.booking.service.mo_ta || ''
  return DOMPurify.sanitize(dirtyHTML, {
    ALLOWED_TAGS: ['p', 'br', 'strong', 'em', 'u', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
      'ul', 'ol', 'li', 'a', 'img', 'blockquote', 'code', 'pre', 'span', 'div'],
    ALLOWED_ATTR: ['href', 'target', 'src', 'alt', 'class', 'style'],
    ALLOW_DATA_ATTR: false
  })
})

// Helper functions
const formatBookingCode = (id) => {
  return String(id).padStart(6, '0')
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

const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  const day = String(date.getDate()).padStart(2, '0')
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const year = date.getFullYear()
  return `${day}/${month}/${year}`
}

const formatTime = (timeString) => {
  if (!timeString) return ''
  const parts = timeString.split(':')
  return `${parts[0]}:${parts[1]}`
}

const getServiceImageUrl = (service) => {
  if (!service?.image) {
    return 'https://placehold.co/400x300?text=No+Image'
  }

  if (service.image.startsWith('http://') || service.image.startsWith('https://')) {
    return service.image
  }

  if (service.image.startsWith('storage/')) {
    return `/storage/${service.image.replace('storage/', '')}`
  }

  return `/storage/${service.image}`
}

const handleImageError = (event) => {
  event.target.src = 'https://placehold.co/400x300?text=No+Image'
}

const getStatusClass = (status) => {
  const statusMap = {
    'pending': 'status-pending',
    'confirmed': 'status-confirmed',
    'completed': 'status-completed',
    'cancelled': 'status-cancelled'
  }
  return statusMap[status] || 'status-default'
}

const getStatusIcon = (status) => {
  const iconMap = {
    'pending': 'fas fa-clock',
    'confirmed': 'fas fa-check-circle',
    'completed': 'fas fa-check-double',
    'cancelled': 'fas fa-times-circle'
  }
  return iconMap[status] || 'fas fa-question-circle'
}

const getStatusLabel = (status) => {
  const labelMap = {
    'pending': 'Chờ xác nhận',
    'confirmed': 'Đã xác nhận',
    'completed': 'Hoàn thành',
    'cancelled': 'Đã hủy'
  }
  return labelMap[status] || status
}

const getPaymentStatusClass = (paymentStatus) => {
  return paymentStatus === 'paid' ? 'payment-paid' : 'payment-unpaid'
}

const getPaymentStatusIcon = (paymentStatus) => {
  return paymentStatus === 'paid' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'
}

const getPaymentStatusLabel = (paymentStatus) => {
  return paymentStatus === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán'
}

const getPaymentMethodLabel = (method) => {
  const methodMap = {
    'pay_at_pharmacy': 'Thanh toán tại nhà thuốc',
    'online': 'Thanh toán online',
    'cod': 'Thanh toán khi nhận hàng'
  }
  return methodMap[method] || method
}

const getServiceTypeLabel = (type) => {
  const typeMap = {
    'tai_nha_thuoc': 'Tại nhà thuốc',
    'tai_nha_khach': 'Tại nhà khách'
  }
  return typeMap[type] || type
}
</script>

<style scoped>
.service-details-container {
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
  padding: 8px 12px;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.back-button:hover {
  color: #0a58ca;
  background-color: #f8f9fa;
  text-decoration: none;
}

.details-title {
  font-size: 28px;
  font-weight: 700;
  color: #2c3e50;
  margin: 0;
}

.service-info-card {
  background: #ffffff;
  border: 1px solid #e9ecef;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.service-header-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  padding-bottom: 16px;
  border-bottom: 1px solid #e9ecef;
}

.service-code-section {
  display: flex;
  align-items: center;
  gap: 8px;
}

.service-code-label {
  color: #6c757d;
  font-size: 14px;
}

.service-code-value {
  color: #2c3e50;
  font-size: 16px;
  font-weight: 600;
  font-family: 'Courier New', monospace;
}

.service-status-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 14px;
  font-weight: 600;
}

.status-pending {
  background-color: #fff3cd;
  color: #856404;
}

.status-confirmed {
  background-color: #d1ecf1;
  color: #0c5460;
}

.status-completed {
  background-color: #d4edda;
  color: #155724;
}

.status-cancelled {
  background-color: #f8d7da;
  color: #721c24;
}

.service-date {
  color: #6c757d;
  font-size: 14px;
  margin-bottom: 24px;
  display: flex;
  align-items: center;
  gap: 8px;
}

/* Section Title */
.section-title {
  font-size: 18px;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.section-title i {
  color: #0d6efd;
}

/* Service Information Section */
.service-information-section {
  margin-bottom: 32px;
}

.service-detail-card {
  background-color: #f8f9fa;
  border-radius: 12px;
  padding: 24px;
  display: flex;
  gap: 24px;
}

.service-image-large {
  width: 300px;
  height: 225px;
  flex-shrink: 0;
  border-radius: 12px;
  overflow: hidden;
  background-color: #e9ecef;
}

.service-image-large img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.service-info-content {
  flex: 1;
  min-width: 0;
}

.service-name-large {
  font-size: 24px;
  font-weight: 700;
  color: #2c3e50;
  margin: 0 0 20px 0;
  line-height: 1.4;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
  margin-bottom: 20px;
}

.info-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 12px;
  background-color: white;
  border-radius: 8px;
  border: 1px solid #e9ecef;
}

.info-icon {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #e7f3ff;
  border-radius: 8px;
  color: #0d6efd;
  flex-shrink: 0;
}

.info-content {
  flex: 1;
  min-width: 0;
}

.info-label {
  font-size: 12px;
  color: #6c757d;
  margin-bottom: 4px;
}

.info-value {
  font-size: 14px;
  font-weight: 600;
  color: #2c3e50;
}

.service-description {
  margin-top: 20px;
  padding: 16px;
  background-color: white;
  border-radius: 8px;
  border: 1px solid #e9ecef;
}

.description-label {
  font-size: 12px;
  color: #6c757d;
  margin-bottom: 8px;
  font-weight: 600;
}

.description-content {
  font-size: 14px;
  color: #495057;
  line-height: 1.6;
  word-wrap: break-word;
  overflow-wrap: break-word;
  word-break: break-word;
  max-width: 100%;
  overflow: hidden;
}

.html-content {
  word-wrap: break-word;
  overflow-wrap: break-word;
  word-break: break-word;
  max-width: 100%;
  overflow: hidden;
}

.html-content * {
  max-width: 100%;
  word-wrap: break-word;
  overflow-wrap: break-word;
}

/* Customer Information Section */
.customer-information-section {
  margin-bottom: 32px;
}

.info-card {
  background-color: #f8f9fa;
  border-radius: 12px;
  padding: 20px;
}

.info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #e9ecef;
}

.info-row:last-child {
  border-bottom: none;
}

.info-label {
  font-size: 14px;
  color: #6c757d;
  font-weight: 500;
}

.info-value {
  font-size: 14px;
  color: #2c3e50;
  font-weight: 600;
}

/* Notes Section */
.notes-section {
  margin-bottom: 32px;
}

.notes-card {
  background-color: #fff3cd;
  border: 1px solid #ffc107;
  border-radius: 12px;
  padding: 20px;
}

.notes-content {
  font-size: 14px;
  color: #856404;
  line-height: 1.6;
  margin: 0;
  font-style: italic;
}

/* Payment Summary Section */
.payment-summary-section {
  margin-bottom: 24px;
}

.summary-card {
  background-color: #f8f9fa;
  border-radius: 12px;
  padding: 24px;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #e9ecef;
}

.summary-row:last-child {
  border-bottom: none;
}

.summary-label {
  font-size: 14px;
  color: #6c757d;
  font-weight: 500;
}

.summary-value {
  font-size: 14px;
  color: #2c3e50;
  font-weight: 600;
}

.payment-status-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
}

.payment-paid {
  background-color: #d4edda;
  color: #155724;
}

.payment-unpaid {
  background-color: #fff3cd;
  color: #856404;
}

.price-row {
  margin-top: 8px;
  padding-top: 16px;
  border-top: 2px solid #0d6efd;
}

.price-value-large {
  color: #dc3545;
  font-size: 24px;
  font-weight: 700;
}

/* Loading State */
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

  .service-header-section {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }

  .service-detail-card {
    flex-direction: column;
  }

  .service-image-large {
    width: 100%;
    height: 250px;
  }

  .info-grid {
    grid-template-columns: 1fr;
  }

  .info-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 4px;
  }

  .summary-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }

  .details-title {
    font-size: 24px;
  }
}
</style>
