<template>
  <div class="dashboard-container">
    <!-- Stats Row -->
    <div class="stats-row">
      <StatsCard 
        :value="processingOrderCount"
        label="Đơn hàng đang xử lý"
        trend="+1 hôm nay"
        trend-type="positive"
        icon="fas fa-shopping-cart"
        icon-color="blue"
      />
      
      <StatsCard 
        value="1,250"
        label="Điểm thưởng tích lũy"
        trend="+50 điểm"
        trend-type="positive"
        icon="fas fa-star"
        icon-color="purple"
      />
      
      <StatsCard 
        value="15.4tr"
        label="Công nợ hiện tại"
        trend="Đến hạn: 30/12"
        trend-type="neutral"
        icon="fas fa-wallet"
        icon-color="orange"
      />
      
      <StatsCard 
        :value="newNotificationsCount"
        label="Thông báo mới"
        trend="Xem tất cả"
        link-to="/user/notifications"
        icon="fas fa-envelope"
        icon-color="red"
      />
    </div>

    <!-- Dashboard Grid -->
    <div class="dashboard-grid">
      <!-- Left Column -->
      <div class="left-col">
        <!-- Promo Banner -->
        <div class="promo-banner">
          <div class="banner-content">
            <span class="banner-tag">MỚI</span>
            <h3>Gói tư vấn quản lý nhà thuốc chuẩn GPP</h3>
            <p>Đăng ký ngay để nhận ưu đãi giảm 20% phí dịch vụ trong tháng này.</p>
            <button class="btn-white">Tìm hiểu thêm</button>
          </div>
        </div>

        <!-- Recent Orders -->
        <RecentOrders :orders="recentOrders" />
      </div>

      <!-- Right Column -->
      <div class="right-col">
        <!-- Quick Access -->
        <div class="section-card">
          <div class="card-header">
            <h3><i class="fas fa-th"></i> Truy cập nhanh</h3>
          </div>
          <div class="quick-access-grid">
            <Link href="/user/orders/create" class="quick-btn">
              <div class="q-icon bg-blue-100 text-blue"><i class="fas fa-box"></i></div>
              <span>Đặt hàng nhanh</span>
            </Link>
            <Link href="/user/invoices" class="quick-btn">
              <div class="q-icon bg-purple-100 text-purple"><i class="fas fa-file-invoice"></i></div>
              <span>Hóa đơn điện tử</span>
            </Link>
            <Link href="/user/support" class="quick-btn">
              <div class="q-icon bg-green-100 text-green"><i class="fas fa-headset"></i></div>
              <span>Hỗ trợ 24/7</span>
            </Link>
            <Link href="/user/rewards" class="quick-btn">
              <div class="q-icon bg-orange-100 text-orange"><i class="fas fa-gift"></i></div>
              <span>Đổi quà</span>
            </Link>
          </div>
        </div>

        <!-- Notifications -->
        <div class="section-card">
          <div class="card-header">
            <h3><i class="fas fa-bell"></i> Mới cập nhật</h3>
            <span class="link-text sm" @click="markAllAsRead">Đánh dấu đã đọc</span>
          </div>
          <div class="notification-list">
            <div v-if="recentNotifications.length === 0" class="no-notifications">
              <p class="text-muted">Không có thông báo mới</p>
            </div>
            <div v-for="notif in recentNotifications" :key="notif.id" class="notif-item"
              :class="{ 'unread': !notif.isRead }">
              <div :class="['notif-icon', notif.iconColor]">
                <i :class="notif.icon"></i>
              </div>
              <div class="notif-text">
                <p v-html="notif.message"></p>
                <small>{{ notif.time }}</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { computed } from 'vue'
import StatsCard from '@/Components/User/Dashboard/StatsCard.vue'
import RecentOrders from '@/Components/User/Dashboard/RecentOrders.vue'

const props = defineProps({
  auth: Object,
  ordersCount: Number,
  bookingsCount: Number,
  unreadNotificationsCount: Number,
  recentNotifications: {
    type: Array,
    default: () => []
  },
  recentOrders: {
    type: Array,
    default: () => []
  },
  processingOrderCount: {
    type: Number,
    default: 0
  },
  newNotificationsCount: {
    type: Number,
    default: 0
  }
})

const markAllAsRead = () => {
  router.post('/user/notifications/mark-all-read', {}, {
    preserveScroll: true,
    onSuccess: () => {
      router.reload({ only: ['recentNotifications', 'unreadNotificationsCount'] })
    }
  })
}
</script>

<style scoped>
/* --- GRID LAYOUT --- */
.dashboard-container {
  max-width: 1400px;
  margin: 0 auto;
}

.dashboard-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 24px;
}

/* --- STATS ROW --- */
.stats-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  margin-bottom: 24px;
}

/* --- PROMO BANNER --- */
.promo-banner {
  background: linear-gradient(135deg, #1E40AF 0%, #3B82F6 100%);
  border-radius: 16px;
  padding: 30px;
  color: white;
  margin-bottom: 24px;
  position: relative;
  overflow: hidden;
}

.banner-content {
  position: relative;
  z-index: 2;
}

.banner-tag {
  background: rgba(255, 255, 255, 0.2);
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 11px;
  font-weight: bold;
  margin-bottom: 12px;
  display: inline-block;
}

.promo-banner h3 {
  font-size: 22px;
  font-weight: 700;
  margin: 0 0 10px 0;
  max-width: 80%;
}

.promo-banner p {
  opacity: 0.9;
  margin-bottom: 20px;
  font-size: 14px;
  max-width: 70%;
}

.btn-white {
  background: white;
  color: #1E40AF;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.2s;
}

.btn-white:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* --- SECTION CARDS --- */
.section-card {
  background: white;
  border-radius: 16px;
  padding: 24px;
  border: 1px solid #E2E8F0;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
  margin-bottom: 24px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.card-header h3 {
  font-size: 16px;
  font-weight: 700;
  color: #1E293B;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 8px;
}

.card-header h3 i {
  color: #3B82F6;
}

.link-text {
  color: #3B82F6;
  text-decoration: none;
  font-size: 13px;
  font-weight: 500;
}

.link-text.sm {
  font-size: 12px;
  color: #94A3B8;
  cursor: pointer;
}

/* Quick Access Grid */
.quick-access-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.quick-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  padding: 16px;
  background: #F8FAFC;
  border-radius: 12px;
  text-decoration: none;
  color: #334155;
  font-size: 13px;
  font-weight: 500;
  transition: 0.2s;
}

.quick-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.q-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
}

.bg-blue-100 { background: #DBEAFE; }
.text-blue { color: #2563EB; }
.bg-purple-100 { background: #EDE9FE; }
.text-purple { color: #7C3AED; }
.bg-green-100 { background: #D1FAE5; }
.text-green { color: #059669; }
.bg-orange-100 { background: #FED7AA; }
.text-orange { color: #EA580C; }

/* Notifications */
.notification-list {
  max-height: 400px;
  overflow-y: auto;
}

.no-notifications {
  text-align: center;
  padding: 40px 20px;
}

.text-muted {
  color: #94A3B8;
  font-size: 14px;
}

.notif-item {
  display: flex;
  gap: 12px;
  padding: 12px;
  border-radius: 8px;
  margin-bottom: 8px;
  transition: background 0.2s;
}

.notif-item:hover {
  background: #F8FAFC;
}

.notif-item.unread {
  background: #EFF6FF;
}

.notif-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  color: white;
}

.notif-icon.blue { background: #3B82F6; }
.notif-icon.green { background: #10B981; }
.notif-icon.orange { background: #F59E0B; }
.notif-icon.red { background: #EF4444; }

.notif-text p {
  font-size: 13px;
  color: #334155;
  margin: 0 0 4px;
  line-height: 1.5;
}

.notif-text small {
  font-size: 11px;
  color: #94A3B8;
}

/* Responsive */
@media (max-width: 1024px) {
  .stats-row {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .dashboard-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 640px) {
  .stats-row {
    grid-template-columns: 1fr;
  }
}
</style>
