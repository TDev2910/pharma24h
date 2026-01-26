<template>
  <div class="page-wrapper">
    <div class="mobile-header d-md-none">
      <Link href="/user/orders" class="back-btn">
        <i class="fas fa-arrow-left"></i>
      </Link>
      <span class="header-title">Chi tiết đơn hàng</span>
      <div class="header-actions">
        <Link href="/" class="home-btn"><i class="fas fa-home"></i></Link>
      </div>
    </div>

    <div class="container main-content">
      <div class="desktop-header d-none d-md-flex">
        <Link href="/user/orders" class="back-link">
          <i class="fas fa-chevron-left"></i> Trở lại danh sách
        </Link>
        <h1>Chi tiết đơn hàng #{{ order?.order_code || order?.id }}</h1>
      </div>

      <div v-if="!order" class="loading-state">
         <div class="spinner"></div>
         <p>Đang tải thông tin đơn hàng...</p>
      </div>

      <div v-else class="content-grid">
        <div class="left-col">
          <div class="card status-card">
            <div class="stepper-wrapper">
              <div v-for="(step, index) in orderSteps" :key="index"
                   class="step-item"
                   :class="{ 'active': isStepActive(step.status), 'completed': isStepCompleted(step.status) }">
                <div class="step-circle">
                  <i class="fas" :class="step.icon"></i>
                </div>
                <div class="step-label">{{ step.label }}</div>
                <div class="step-line" v-if="index < orderSteps.length - 1"></div>
              </div>
            </div>
            <div class="current-status-text">
               <span class="status-badge" :class="getStatusClass(order.order_status)">
                 {{ getStatusLabel(order.order_status) }}
               </span>
               <span class="update-time" v-if="order.updated_at">
                 Cập nhật: {{ formatDateTime(order.updated_at) }}
               </span>
            </div>
          </div>

          <div v-if="order.delivery_method === 'shipping'" class="card shipping-card">
             <div class="card-header-sm">
                <i class="fas fa-shipping-fast text-primary"></i> Thông tin vận chuyển
             </div>
             <div class="shipping-details">
                <div class="timeline-item">
                   <div class="timeline-dot active"></div>
                   <div class="timeline-content">
                      <div class="fw-bold text-success">{{ order.ghn_status_text || 'Đang xử lý vận chuyển' }}</div>
                      <div class="small text-muted">
                        {{ order.ghn_shipper_name ? `Shipper: ${order.ghn_shipper_name}` : 'GHN Express' }}
                        <span v-if="order.ghn_shipper_phone"> - {{ order.ghn_shipper_phone }}</span>
                      </div>
                   </div>
                </div>
                <a v-if="order.ghn_order_code" :href="getGHNTrackingUrl(order)" target="_blank" class="tracking-btn-full">
                    Xem hành trình trên GHN <i class="fas fa-external-link-alt"></i>
                 </a>
             </div>
          </div>

          <div class="card products-card">
            <div class="card-header-sm">Sản phẩm ({{ order.items?.length || 0 }})</div>
            <div class="product-list">
              <div v-for="item in order.items" :key="item.id" class="product-item">
                <div class="product-thumb">
                  <img :src="getImageUrl(item)" @error="handleImageError" :alt="item.product_name"/>
                </div>
                <div class="product-info">
                  <div class="product-name">{{ item.product_name || item.name }}</div>
                  <div class="product-meta">Đơn vị: {{ item.unit || 'Sản phẩm' }} • SL: x{{ item.quantity }}</div>
                  <div class="product-price-mobile d-md-none">
                     {{ formatCurrency(item.price) }}₫
                  </div>
                </div>
                <div class="product-price d-none d-md-block">
                  {{ formatCurrency(item.price) }}₫
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="right-col">
          <div class="card info-card">
             <div class="card-header-sm">Địa chỉ nhận hàng</div>
             <div class="info-row">
                <i class="fas fa-map-marker-alt icon-width"></i>
                <div class="info-content">
                   <div class="customer-name">{{ order.customer_name || order.name }} <span class="phone-sep">|</span> {{ order.customer_phone || order.phone }}</div>
                   <div class="address-text">{{ order.pickup_location || order.shipping_address || 'Địa chỉ chưa cập nhật' }}</div>
                </div>
             </div>
          </div>

          <div class="card summary-card">
            <div class="summary-row">
              <span>Tạm tính</span>
              <span>{{ formatCurrency(order.total_amount) }}₫</span>
            </div>
            <div class="summary-row" v-if="order.ghn_fee">
              <span>Phí vận chuyển</span>
              <span>{{ formatCurrency(order.ghn_fee) }}₫</span>
            </div>
            <div class="summary-divider"></div>
           <div class="summary-row total-row">
          <span>Tổng tiền:</span>
            <span class="total-amount">{{ formatCurrency(order.total_amount) }} ₫</span>
            </div>
            <div class="payment-status">
               <span class="payment-label">Thanh toán:</span>
               <span class="badge-payment">{{ getPaymentMethodText(order.payment_method) }}</span>
            </div>
          </div>

          <div class="desktop-actions d-none d-md-flex">
             <button v-if="canRequestCancellation" @click="openCancelModal" class="btn-cancel">
               <i></i> Yêu cầu hủy đơn
             </button>
             <button class="btn-primary-action">
               <i class="fas fa-redo"></i> Mua lại đơn hàng
             </button>
          </div>
        </div>
      </div>
    </div>

    <div class="mobile-bottom-actions d-md-none" v-if="order">
       <div class="action-grid" :class="{ 'single-col': !canRequestCancellation }">
          <button v-if="canRequestCancellation" @click="openCancelModal" class="btn-mobile-secondary">
            Hủy đơn
          </button>
          <button class="btn-mobile-primary">Mua lại</button>
       </div>
    </div>

    <div v-if="showCancelModal" class="modal-backdrop">
       <div class="modal-content-custom">
          <div class="modal-header-custom">
             <h3>Yêu cầu hủy đơn</h3>
             <button @click="closeCancelModal" class="close-btn">&times;</button>
          </div>
          <div class="modal-body-custom">
             <p class="modal-note">Vui lòng cho chúng tôi biết lý do bạn muốn hủy đơn hàng này.</p>
             <form @submit.prevent="submitCancel">
                 <div class="form-group">
                    <label>Lý do hủy <span class="text-danger">*</span></label>
                    <select v-model="cancelForm.reason" class="modern-select" required>
                       <option value="" disabled>-- Chọn lý do --</option>
                       <option value="change_of_mind">Tôi muốn đổi ý</option>
                       <option value="wrong_product">Đặt nhầm sản phẩm</option>
                       <option value="shipping_fee">Phí vận chuyển cao</option>
                       <option value="other">Lý do khác</option>
                    </select>
                 </div>
                 <div class="form-group">
                    <label>Ghi chú thêm</label>
                    <textarea v-model="cancelForm.note" class="modern-textarea" rows="3" placeholder="Nhập chi tiết..."></textarea>
                 </div>
                 <div class="modal-footer-custom">
                    <button type="button" @click="closeCancelModal" class="btn-text">Đóng</button>
                    <button type="submit" class="btn-danger-block" :disabled="cancelForm.processing">
                      {{ cancelForm.processing ? 'Đang xử lý...' : 'Xác nhận hủy' }}
                    </button>
                 </div>
             </form>
          </div>
       </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'

// --- PROPS ---
const props = defineProps({
  order: {
    type: Object,
    default: null
  }
})

// --- STATE ---
const showCancelModal = ref(false)
const cancelForm = useForm({
  reason: '',
  note: ''
})

// --- CONFIGURATION ---
// Cấu hình các bước cho Stepper
const orderSteps = [
  { status: 'new', label: 'Đặt hàng', icon: 'fa-file-invoice' },
  { status: 'confirmed', label: 'Đã xác nhận', icon: 'fa-box-open' },
  { status: 'shipping', label: 'Đang giao', icon: 'fa-shipping-fast' },
  { status: 'completed', label: 'Hoàn thành', icon: 'fa-check-circle' }
]

// --- COMPUTED LOGIC ---

// Kiểm tra xem đơn hàng có được phép hủy không
const canRequestCancellation = computed(() => {
  if (!props.order) return false
  const status = props.order.order_status
  // Chỉ cho hủy khi mới đặt hoặc chưa giao cho vận chuyển
  return ['new', 'pending', 'confirmed'].includes(status) && props.order.cancellation_status !== 'requested'
})

// Logic xác định step đã hoàn thành (để tô màu xanh)
const isStepCompleted = (stepStatus) => {
   if (!props.order) return false
   const statusMap = {
     'new': 1, 'pending': 1,
     'confirmed': 2,
     'shipping': 3, 'delivering': 3,
     'delivered': 4, 'completed': 5,
     'cancelled': 0
   };

   // Map trạng thái đơn hàng hiện tại sang số
   const currentScore = statusMap[props.order.order_status] || 0;
   // Map bước step sang số
   const stepScore = statusMap[stepStatus];

   if (props.order.order_status === 'cancelled') return false; // Nếu hủy thì không hiện xanh
   return currentScore >= stepScore;
}

// Logic xác định step đang active
const isStepActive = (stepStatus) => {
   if (!props.order) return false
   // Đơn giản hóa: Trùng tên status thì active
   return props.order.order_status === stepStatus;
}

// --- HELPER FUNCTIONS ---

const getStatusLabel = (status) => {
  const map = {
    'new': 'Chờ xác nhận',
    'pending': 'Chờ xử lý',
    'confirmed': 'Đã xác nhận',
    'shipping': 'Đang giao hàng',
    'delivered': 'Đã giao thành công',
    'completed': 'Hoàn thành',
    'cancelled': 'Đã hủy'
  }
  return map[status] || status
}

const getStatusClass = (status) => {
   if(status === 'cancelled') return 'text-danger';
   if(status === 'completed' || status === 'delivered') return 'text-success';
   return 'text-primary';
}

const getPaymentMethodText = (method) => {
  return method === 'cod' ? 'Tiền mặt khi nhận hàng (COD)' : 'Chuyển khoản / VNPAY'
}

const formatCurrency = (value) => {
  if (value === undefined || value === null) return '0'
  return new Intl.NumberFormat('vi-VN').format(value)
}

const formatDateTime = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return `${date.getHours()}:${String(date.getMinutes()).padStart(2, '0')} ${date.getDate()}/${date.getMonth() + 1}/${date.getFullYear()}`
}

const getImageUrl = (item) => {
  // Logic lấy ảnh, fallback nếu lỗi
  let img = item.image || item.product?.image;
  if (!img) return 'https://placehold.co/100x100?text=No+Img';

  if (img.startsWith('http')) return img;
  if (img.startsWith('storage/')) return '/' + img;
  return '/storage/' + img;
}

const handleImageError = (e) => {
  e.target.src = 'https://placehold.co/100x100?text=Error';
}

const getGHNTrackingUrl = (order) => {
  return order.ghn_tracking_url || `https://donhang.ghn.vn/?order_code=${order.ghn_order_code}`
}

// --- ACTION HANDLERS ---
const openCancelModal = () => showCancelModal.value = true
const closeCancelModal = () => showCancelModal.value = false

const submitCancel = () => {
  if (!props.order) return
  cancelForm.post(`/user/orders/${props.order.id}/request-cancel`, {
    preserveScroll: true,
    onSuccess: () => {
      closeCancelModal()
      // Có thể thêm toast thông báo ở đây
    }
  })
}
</script>

<style scoped>
/* --- 1. VARIABLES & RESET --- */
:root {
  --primary: #0056b3;      /* Xanh dương đậm chủ đạo */
  --primary-light: #e0f2fe;
  --secondary: #64748b;    /* Xám text */
  --success: #16a34a;      /* Xanh lá */
  --danger: #dc2626;       /* Đỏ */
  --bg-body: #f1f5f9;      /* Nền trang xám nhạt */
  --white: #ffffff;
  --radius: 12px;          /* Bo góc chuẩn */
  --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
}

.page-wrapper {
  background-color: #f1f5f9; /* Fallback variable */
  background-color: var(--bg-body);
  min-height: 100vh;
  padding-bottom: 90px; /* Chừa khoảng trống cho thanh Mobile Bottom */
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  color: #334155;
}

.container {
  max-width: 1140px;
  margin: 0 auto;
  padding: 16px;
}

/* --- 2. LAYOUT GRID --- */
.content-grid {
  display: grid;
  grid-template-columns: 2fr 1fr; /* Trái 66%, Phải 33% */
  gap: 20px;
  align-items: start;
}

/* --- 3. COMMON COMPONENTS --- */
.card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  padding: 20px;
  margin-bottom: 16px;
  border: 1px solid #e2e8f0;
}

.card-header-sm {
  font-weight: 600;
  font-size: 15px;
  color: #1e293b;
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  gap: 8px;
  border-bottom: 1px dashed #e2e8f0;
  padding-bottom: 8px;
}

/* Text helpers */
.text-primary { color: #0056b3; }
.text-success { color: #16a34a; }
.text-danger { color: #dc2626; }
.text-muted { color: #64748b; }
.fw-bold { font-weight: 600; }

/* --- 4. STEPPER (Thanh tiến trình) --- */
.stepper-wrapper {
  display: flex;
  justify-content: space-between;
  margin-bottom: 24px;
  position: relative;
  padding: 0 10px;
}

.step-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
  flex: 1;
  z-index: 1;
}

.step-circle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #f1f5f9;
  color: #94a3b8;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 8px;
  transition: all 0.3s;
  font-size: 16px;
  border: 2px solid #e2e8f0;
}

.step-label {
  font-size: 12px;
  color: #64748b;
  font-weight: 500;
  text-align: center;
}

.step-line {
  position: absolute;
  top: 20px;
  left: 50%;
  width: 100%;
  height: 2px;
  background: #e2e8f0;
  z-index: -1;
}

/* Active/Completed State Stepper */
.step-item.completed .step-circle,
.step-item.active .step-circle {
  background: #0056b3;
  color: white;
  border-color: #0056b3;
  box-shadow: 0 0 0 4px rgba(0, 86, 179, 0.1);
}

.step-item.completed .step-line {
  background: #0056b3;
}

.step-item.active .step-label {
  color: #0056b3;
  font-weight: 700;
}

.current-status-text {
  text-align: center;
  background: #f8fafc;
  padding: 10px;
  border-radius: 8px;
}
.status-badge { font-weight: 700; font-size: 15px; }
.update-time { display: block; font-size: 12px; color: #94a3b8; margin-top: 4px; }

/* --- 5. PRODUCT LIST --- */
.product-item {
  display: flex;
  gap: 16px;
  padding: 16px 0;
  border-bottom: 1px solid #f1f5f9;
}
.product-item:last-child { border-bottom: none; padding-bottom: 0; }

.product-thumb img {
  width: 70px;
  height: 70px;
  border-radius: 8px;
  object-fit: cover;
  border: 1px solid #e2e8f0;
}

.product-info { flex: 1; }
.product-name { font-size: 14px; font-weight: 600; color: #1e293b; line-height: 1.4; margin-bottom: 4px; }
.product-meta { font-size: 13px; color: #64748b; }
.product-price { font-weight: 700; color: #1e293b; font-size: 15px; }
.product-price-mobile { font-weight: 700; color: #0056b3; margin-top: 6px; font-size: 15px; }

/* --- 6. SHIPPING & INFO --- */
.timeline-item { display: flex; gap: 12px; align-items: flex-start; }
.timeline-dot { width: 10px; height: 10px; background: #22c55e; border-radius: 50%; margin-top: 6px; box-shadow: 0 0 0 3px #dcfce7; }
.tracking-btn-full {
  display: block;
  background: #f0f9ff;
  color: #0369a1;
  text-align: center;
  padding: 12px;
  border-radius: 8px;
  margin-top: 16px;
  text-decoration: none;
  font-weight: 600;
  font-size: 14px;
  transition: 0.2s;
}
.tracking-btn-full:hover { background: #e0f2fe; }

.info-row { display: flex; gap: 12px; }
.icon-width { width: 20px; text-align: center; color: #94a3b8; margin-top: 2px; }
.customer-name { font-weight: 600; font-size: 14px; color: #1e293b; margin-bottom: 2px; }
.phone-sep { color: #cbd5e1; margin: 0 4px; }
.address-text { font-size: 13px; color: #64748b; line-height: 1.4; }

/* --- 7. SUMMARY CARD --- */
.summary-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; color: #475569; }
.summary-divider { border-top: 1px dashed #e2e8f0; margin: 16px 0; }
.summary-total { display: flex; justify-content: space-between; align-items: center; font-weight: 700; font-size: 16px; color: #1e293b; }
.total-price { color: #dc2626; font-size: 20px; }
.payment-status { margin-top: 16px; background: #f8fafc; padding: 10px; border-radius: 8px; font-size: 13px; }
.badge-payment { font-weight: 600; color: #0056b3; display: block; margin-top: 4px; }

/* --- 8. MOBILE HEADER & FOOTER --- */
.mobile-header {
  position: sticky;
  top: 0;
  background: white;
  z-index: 100;
  padding: 12px 16px;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 2px 4px rgba(0,0,0,0.03);
}
.header-title { font-weight: 700; font-size: 17px; color: #1e293b; }
.back-btn, .home-btn { color: #475569; font-size: 18px; padding: 4px; }

.mobile-bottom-actions {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  background: white;
  padding: 12px 16px;
  box-shadow: 0 -4px 12px rgba(0,0,0,0.05);
  z-index: 99;
  border-top: 1px solid #f1f5f9;
}

.action-grid {
  display: grid;
  grid-template-columns: 1fr 2fr; /* Nút hủy nhỏ, nút Mua lại to */
  gap: 12px;
}
.action-grid.single-col { grid-template-columns: 1fr; }

.btn-mobile-secondary {
  background: white;
  color: #475569;
  border: 1px solid #cbd5e1;
  padding: 12px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 14px;
}
.btn-mobile-primary {
  background: #0056b3;
  color: white;
  border: none;
  padding: 12px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 14px;
}

/* --- 9. DESKTOP HEADER & ACTIONS --- */
.desktop-header {
  margin: 24px 0;
  flex-direction: column;
  gap: 8px;
}
.desktop-header h1 { margin: 0; font-size: 24px; color: #1e293b; }
.back-link { color: #64748b; text-decoration: none; font-size: 14px; display: inline-flex; align-items: center; gap: 4px; }
.back-link:hover { color: #0056b3; }

.desktop-actions { flex-direction: column; gap: 12px; margin-top: 16px; }
.btn-primary-action {
  background: #0056b3; color: white; border: none;
  width: 100%; padding: 14px; border-radius: 8px; font-weight: 600; cursor: pointer;
  display: flex; align-items: center; justify-content: center; gap: 8px;
  transition: 0.2s;
}
.btn-primary-action:hover { background: #004494; }

.btn-cancel {
  background: white; border: 1px solid #e2e8f0; color: #64748b;
  width: 100%; padding: 12px; border-radius: 8px; font-weight: 500; cursor: pointer;
  display: flex; align-items: center; justify-content: center; gap: 8px;
  transition: 0.2s;
}
.btn-cancel:hover { background: #fee2e2; color: #dc2626; border-color: #fee2e2; }

/* --- 10. MODAL --- */
.modal-backdrop {
  position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000;
  display: flex; align-items: center; justify-content: center; padding: 16px;
  backdrop-filter: blur(2px);
}
.modal-content-custom {
  background: white; width: 100%; max-width: 500px; border-radius: 16px;
  overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
}
.modal-header-custom {
  padding: 16px 20px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;
}
.modal-header-custom h3 { margin: 0; font-size: 18px; color: #1e293b; }
.close-btn { background: none; border: none; font-size: 24px; color: #94a3b8; cursor: pointer; }

.modal-body-custom { padding: 20px; }
.modal-note { font-size: 14px; color: #64748b; margin-bottom: 16px; }
.form-group { margin-bottom: 16px; }
.form-group label { display: block; margin-bottom: 6px; font-size: 14px; font-weight: 500; color: #334155; }
.modern-select, .modern-textarea {
  width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px;
  font-size: 14px; outline: none; transition: 0.2s;
}
.modern-select:focus, .modern-textarea:focus { border-color: #0056b3; box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.1); }

.modal-footer-custom { display: flex; gap: 12px; margin-top: 24px; }
.btn-text { background: none; border: none; color: #64748b; cursor: pointer; padding: 0 16px; font-weight: 500; }
.btn-danger-block {
  flex: 1; background: #dc2626; color: white; border: none; padding: 12px;
  border-radius: 8px; font-weight: 600; cursor: pointer;
}
.btn-danger-block:disabled { opacity: 0.7; cursor: not-allowed; }
.loading-state { text-align: center; padding: 40px; }
.spinner { /* Add spinner CSS here if needed */ }

/* --- RESPONSIVE ADJUSTMENTS --- */
@media (max-width: 767px) {
  .content-grid { grid-template-columns: 1fr; gap: 12px; }
  .container { padding: 12px; }
  .stepper-wrapper { padding: 0 4px; }
  .step-label { font-size: 10px; }
  .step-circle { width: 32px; height: 32px; font-size: 12px; }
  .step-line { top: 16px; }
  .card { padding: 16px; border-radius: 12px; box-shadow: none; border: 1px solid #f1f5f9; }
  .page-wrapper { padding-top: 0; }
}
</style>
