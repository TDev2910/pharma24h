<template>
  <div class="services-container">
    <!-- Header Section -->
    <div class="services-header">
      <h1 class="services-title">Dịch vụ đã đặt</h1>
      <div class="search-container">
        <i class="fas fa-search search-icon"></i>
        <input 
          type="text" 
          v-model="searchQuery" 
          class="search-input"
          placeholder="Tìm kiếm theo tên dịch vụ hoặc mã đặt lịch" 
        />
      </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="status-tabs">
      <button 
        v-for="tab in statusTabs" 
        :key="tab.status" 
        @click="activeTab = tab.status"
        :class="['status-tab', { active: activeTab === tab.status }]"
      >
        {{ tab.label }} ({{ getBookingsCountByStatus(tab.status) }})
      </button>
    </div>

    <!-- Services List -->
    <div class="services-list" v-if="filteredBookings.length > 0">
      <div 
        v-for="booking in filteredBookings" 
        :key="booking.id" 
        class="service-card"
        @click="goToServiceDetails(booking.id)"
      >
        <!-- Service Header -->
        <div class="service-header">
          <div class="service-status-badge" :class="getStatusClass(booking.status)">
            <i :class="getStatusIcon(booking.status)"></i>
            {{ getStatusLabel(booking.status) }}
          </div>
          <span class="service-date">{{ formatDateTime(booking.created_at) }}</span>
        </div>

        <!-- Service Info -->
        <div class="service-info-section">
          <div class="service-image" v-if="booking.service?.image">
            <img 
              :src="getServiceImageUrl(booking.service)" 
              :alt="booking.service?.ten_dich_vu" 
              @error="handleImageError" 
            />
          </div>
          <div class="service-details">
            <h3 class="service-name">{{ booking.service?.ten_dich_vu || 'Dịch vụ không xác định' }}</h3>
            <div class="service-meta">
              <div class="meta-item">
                <i class="fas fa-calendar-alt"></i>
                <span>Ngày hẹn: {{ formatDate(booking.booking_date) }}</span>
              </div>
              <div class="meta-item">
                <i class="fas fa-clock"></i>
                <span>Giờ hẹn: {{ formatTime(booking.booking_time) }}</span>
              </div>
              <div class="meta-item" v-if="booking.service?.thoi_gian_thuc_hien">
                <i class="fas fa-hourglass-half"></i>
                <span>Thời gian: {{ booking.service.thoi_gian_thuc_hien }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Booking Details -->
        <div class="booking-details">
          <div class="detail-row">
            <span class="detail-label">Mã đặt lịch:</span>
            <span class="detail-value booking-code">#{{ formatBookingCode(booking.id) }}</span>
          </div>
          <div class="detail-row" v-if="booking.customer_name">
            <span class="detail-label">Khách hàng:</span>
            <span class="detail-value">{{ booking.customer_name }}</span>
          </div>
          <div class="detail-row" v-if="booking.customer_phone">
            <span class="detail-label">Số điện thoại:</span>
            <span class="detail-value">{{ booking.customer_phone }}</span>
          </div>
          <div class="detail-row" v-if="booking.notes">
            <span class="detail-label">Ghi chú:</span>
            <span class="detail-value notes">{{ booking.notes }}</span>
          </div>
        </div>

        <!-- Payment & Price -->
        <div class="service-footer">
          <div class="payment-status" :class="getPaymentStatusClass(booking.payment_status)">
            <i :class="getPaymentStatusIcon(booking.payment_status)"></i>
            {{ getPaymentStatusLabel(booking.payment_status) }}
          </div>
          <div class="service-price">
            <span class="price-label">Giá dịch vụ:</span>
            <span class="price-value">{{ formatCurrency(booking.price) }} ₫</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div class="empty-state" v-else>
      <i class="fas fa-calendar-times empty-icon"></i>
      <p class="empty-text">Bạn chưa đặt dịch vụ nào.</p>
      <Link href="/services" class="empty-action">
        Đặt dịch vụ ngay
      </Link>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'

// Props từ Inertia
const props = defineProps({
  bookings: {
    type: Array,
    default: () => []
  }
})

// Search and filter state
const searchQuery = ref('')
const activeTab = ref('all')

// Status tabs configuration
const statusTabs = [
  { status: 'all', label: 'Tất cả' },
  { status: 'pending', label: 'Chờ xác nhận' },
  { status: 'confirmed', label: 'Đã xác nhận' },
  { status: 'completed', label: 'Hoàn thành' },
  { status: 'cancelled', label: 'Đã hủy' },
]

// Computed: Filtered bookings
const filteredBookings = computed(() => {
  let filtered = props.bookings

  // Filter by status
  if (activeTab.value !== 'all') {
    filtered = filtered.filter(booking => booking.status === activeTab.value)
  }

  // Filter by search query
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase().trim()
    filtered = filtered.filter(booking => {
      // Search by service name
      const serviceName = (booking.service?.ten_dich_vu || '').toLowerCase()
      if (serviceName.includes(query)) return true

      // Search by booking code
      const bookingCode = formatBookingCode(booking.id).toLowerCase()
      if (bookingCode.includes(query)) return true

      // Search by customer name
      const customerName = (booking.customer_name || '').toLowerCase()
      if (customerName.includes(query)) return true

      return false
    })
  }

  return filtered
})

// Navigation function
const goToServiceDetails = (bookingId) => {
  router.visit(`/user/services/${bookingId}`)
}

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
  // timeString có thể là "HH:mm:ss" hoặc "HH:mm"
  const parts = timeString.split(':')
  return `${parts[0]}:${parts[1]}`
}

const getServiceImageUrl = (service) => {
  if (!service?.image) {
    return 'https://placehold.co/120x120?text=No+Image'
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
  event.target.src = 'https://placehold.co/120x120?text=No+Image'
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

const getBookingsCountByStatus = (status) => {
  if (status === 'all') {
    return props.bookings.length
  }
  return props.bookings.filter(b => b.status === status).length
}
</script>

<style scoped>
.services-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

/* Header Section */
.services-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  flex-wrap: wrap;
  gap: 16px;
}

.services-title {
  font-size: 28px;
  font-weight: 700;
  color: #2c3e50;
  margin: 0;
}

.search-container {
  position: relative;
  flex: 1;
  max-width: 500px;
  min-width: 300px;
}

.search-icon {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #6c757d;
  font-size: 16px;
}

.search-input {
  width: 100%;
  padding: 12px 16px 12px 44px;
  border: 1px solid #dee2e6;
  border-radius: 8px;
  font-size: 14px;
  transition: all 0.3s ease;
}

.search-input:focus {
  outline: none;
  border-color: #0d6efd;
  box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
}

/* Status Tabs */
.status-tabs {
  display: flex;
  gap: 8px;
  margin-bottom: 24px;
  flex-wrap: wrap;
  border-bottom: 2px solid #e9ecef;
  padding-bottom: 8px;
}

.status-tab {
  padding: 10px 20px;
  border: none;
  background: transparent;
  color: #6c757d;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  border-radius: 8px 8px 0 0;
  transition: all 0.3s ease;
  position: relative;
}

.status-tab:hover {
  background-color: #f8f9fa;
  color: #0d6efd;
}

.status-tab.active {
  color: #0d6efd;
  font-weight: 600;
}

.status-tab.active::after {
  content: '';
  position: absolute;
  bottom: -10px;
  left: 0;
  right: 0;
  height: 3px;
  background-color: #0d6efd;
  border-radius: 2px 2px 0 0;
}

/* Services List */
.services-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

/* Service Card */
.service-card {
  background: #ffffff;
  border: 1px solid #e9ecef;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
  cursor: pointer;
}

.service-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
  border-color: #0d6efd;
}

/* Service Header */
.service-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 16px;
  border-bottom: 1px solid #e9ecef;
}

.service-status-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
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
}

/* Service Info Section */
.service-info-section {
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
}

.service-image {
  width: 120px;
  height: 120px;
  flex-shrink: 0;
  border-radius: 12px;
  overflow: hidden;
  background-color: #e9ecef;
}

.service-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.service-details {
  flex: 1;
  min-width: 0;
}

.service-name {
  font-size: 18px;
  font-weight: 600;
  color: #2c3e50;
  margin: 0 0 12px 0;
  line-height: 1.4;
}

.service-meta {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #6c757d;
  font-size: 14px;
}

.meta-item i {
  color: #0d6efd;
  width: 16px;
}

/* Booking Details */
.booking-details {
  background-color: #f8f9fa;
  border-radius: 8px;
  padding: 16px;
  margin-bottom: 16px;
}

.detail-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 8px;
  font-size: 14px;
}

.detail-row:last-child {
  margin-bottom: 0;
}

.detail-label {
  color: #6c757d;
  font-weight: 500;
  min-width: 120px;
}

.detail-value {
  color: #2c3e50;
  text-align: right;
  flex: 1;
}

.booking-code {
  font-weight: 600;
  color: #0d6efd;
  font-family: 'Courier New', monospace;
}

.notes {
  font-style: italic;
  color: #495057;
}

/* Service Footer */
.service-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 16px;
  border-top: 1px solid #e9ecef;
}

.payment-status {
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

.service-price {
  display: flex;
  align-items: center;
  gap: 8px;
}

.price-label {
  color: #6c757d;
  font-size: 14px;
}

.price-value {
  color: #dc3545;
  font-size: 20px;
  font-weight: 700;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 60px 20px;
}

.empty-icon {
  font-size: 64px;
  color: #dee2e6;
  margin-bottom: 16px;
}

.empty-text {
  font-size: 16px;
  color: #6c757d;
  margin: 0 0 20px 0;
}

.empty-action {
  display: inline-block;
  padding: 12px 24px;
  background-color: #0d6efd;
  color: white;
  text-decoration: none;
  border-radius: 8px;
  font-weight: 500;
  transition: all 0.3s ease;
}

.empty-action:hover {
  background-color: #0a58ca;
  text-decoration: none;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
}

/* Responsive */
@media (max-width: 768px) {
  .services-header {
    flex-direction: column;
    align-items: stretch;
  }

  .search-container {
    max-width: 100%;
    min-width: 100%;
  }

  .status-tabs {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  .status-tab {
    white-space: nowrap;
    font-size: 12px;
    padding: 8px 16px;
  }

  .service-info-section {
    flex-direction: column;
  }

  .service-image {
    width: 100%;
    height: 200px;
  }

  .service-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }

  .service-footer {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }

  .detail-row {
    flex-direction: column;
    gap: 4px;
  }

  .detail-value {
    text-align: left;
  }

  .services-title {
    font-size: 24px;
  }
}
</style>
