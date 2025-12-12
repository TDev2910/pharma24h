<template>
  <div class="dashboard-container">
    
    <div class="stats-row">
      <div class="stat-card">
        <div class="stat-content">
          <div class="stat-value">3</div>
          <div class="stat-label">Đơn hàng đang xử lý</div>
          <div class="stat-trend positive">+1 hôm nay</div>
        </div>
        <div class="stat-icon-box blue"><i class="fas fa-shopping-cart"></i></div>
      </div>

      <div class="stat-card">
        <div class="stat-content">
          <div class="stat-value">1,250</div>
          <div class="stat-label">Điểm thưởng tích lũy</div>
          <div class="stat-trend positive">+50 điểm</div>
        </div>
        <div class="stat-icon-box purple"><i class="fas fa-star"></i></div>
      </div>

      <div class="stat-card">
        <div class="stat-content">
          <div class="stat-value">15.4tr</div>
          <div class="stat-label">Công nợ hiện tại</div>
          <div class="stat-trend neutral">Đến hạn: 30/12</div>
        </div>
        <div class="stat-icon-box orange"><i class="fas fa-wallet"></i></div>
      </div>

      <div class="stat-card">
        <div class="stat-content">
          <div class="stat-value">5</div>
          <div class="stat-label">Thông báo mới</div>
          <Link href="/user/notifications" class="stat-link">Xem tất cả</Link>
        </div>
        <div class="stat-icon-box red"><i class="fas fa-envelope"></i></div>
      </div>
    </div>

    <div class="dashboard-grid">
      
      <div class="left-col">
        <div class="promo-banner">
          <div class="banner-content">
            <span class="banner-tag">MỚI</span>
            <h3>Gói tư vấn quản lý nhà thuốc chuẩn GPP</h3>
            <p>Đăng ký ngay để nhận ưu đãi giảm 20% phí dịch vụ trong tháng này.</p>
            <button class="btn-white">Tìm hiểu thêm</button>
          </div>
          </div>

        <div class="section-card">
          <div class="card-header">
            <h3><i class="fas fa-history"></i> Đơn hàng gần đây</h3>
            <Link href="/user/orders" class="link-text">Xem tất cả</Link>
          </div>
          
          <div class="table-responsive">
            <table class="custom-table">
              <thead>
                <tr>
                  <th>Mã đơn</th>
                  <th>Sản phẩm</th>
                  <th>Tổng tiền</th>
                  <th>Trạng thái</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="order in recentOrders" :key="order.id">
                  <td class="code">{{ order.code }} <br> <small>{{ order.date }}</small></td>
                  <td>{{ order.product }}</td>
                  <td class="price">{{ order.total }}</td>
                  <td>
                    <span :class="['status-badge', order.statusClass]">{{ order.status }}</span>
                  </td>
                  <td><button class="btn-icon-sm"><i class="fas fa-eye"></i></button></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="right-col">
        
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

        <div class="section-card">
          <div class="card-header">
            <h3><i class="fas fa-bell"></i> Mới cập nhật</h3>
            <span class="link-text sm">Đánh dấu đã đọc</span>
          </div>
          <div class="notification-list">
            <div class="notif-item">
              <div class="notif-icon blue"><i class="fas fa-truck"></i></div>
              <div class="notif-text">
                <p>Đơn hàng <strong>#PHARMA-2022</strong> đang được giao</p>
                <small>Dự kiến: 14:00 hôm nay</small>
              </div>
            </div>
            <div class="notif-item">
              <div class="notif-icon yellow"><i class="fas fa-exclamation-triangle"></i></div>
              <div class="notif-text">
                <p>Cập nhật thông tin giấy phép</p>
                <small>Giấy phép GPP sắp hết hạn sau 30 ngày.</small>
              </div>
            </div>
             <div class="notif-item">
              <div class="notif-icon green"><i class="fas fa-percentage"></i></div>
              <div class="notif-text">
                <p>Khuyến mãi tháng 12</p>
                <small>Giảm giá 10% cho nhóm thực phẩm chức năng.</small>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({ auth: Object })

// Mock Data cho Table
const recentOrders = ref([
  { id: 1, code: '#PHARMA-2023', date: '24/10/2025', product: 'Paracetamol 500mg, Vitamin C...', total: '5.200.000₫', status: 'Đang xử lý', statusClass: 'processing' },
  { id: 2, code: '#PHARMA-2022', date: '20/10/2025', product: 'Amoxicillin, Ibuprofen...', total: '3.450.000₫', status: 'Đang giao', statusClass: 'shipping' },
  { id: 3, code: '#PHARMA-2021', date: '15/10/2025', product: 'Thực phẩm chức năng...', total: '8.100.000₫', status: 'Hoàn thành', statusClass: 'completed' },
])
</script>

<style scoped>
/* --- GRID LAYOUT --- */
.dashboard-grid {
  display: grid;
  grid-template-columns: 2fr 1fr; /* 2 phần trái, 1 phần phải */
  gap: 24px;
}

/* --- STATS ROW --- */
.stats-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  margin-bottom: 24px;
}
.stat-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  box-shadow: 0 2px 4px rgba(0,0,0,0.02);
  border: 1px solid #F1F5F9;
}
.stat-value { font-size: 24px; font-weight: 700; color: #1E293B; margin-bottom: 4px; }
.stat-label { font-size: 13px; color: #64748B; margin-bottom: 8px; }
.stat-trend { font-size: 11px; font-weight: 600; padding: 2px 6px; border-radius: 4px; display: inline-block; }
.stat-trend.positive { background: #DCFCE7; color: #16A34A; }
.stat-trend.neutral { background: #F1F5F9; color: #64748B; }
.stat-link { font-size: 12px; color: #3B82F6; text-decoration: none; }

.stat-icon-box { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
.stat-icon-box.blue { background: #EFF6FF; color: #3B82F6; }
.stat-icon-box.purple { background: #F3E8FF; color: #9333EA; }
.stat-icon-box.orange { background: #FFEDD5; color: #F97316; }
.stat-icon-box.red { background: #FEE2E2; color: #EF4444; }

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
.banner-tag { background: rgba(255,255,255,0.2); padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; margin-bottom: 12px; display: inline-block; }
.promo-banner h3 { font-size: 22px; font-weight: 700; margin: 0 0 10px 0; max-width: 80%; }
.promo-banner p { opacity: 0.9; margin-bottom: 20px; font-size: 14px; max-width: 70%; }
.btn-white { background: white; color: #1E40AF; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s; }
.btn-white:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.2); }

/* --- SECTION CARDS --- */
.section-card {
  background: white;
  border-radius: 16px;
  padding: 24px;
  border: 1px solid #E2E8F0;
  box-shadow: 0 2px 4px rgba(0,0,0,0.02);
  margin-bottom: 24px;
}
.card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.card-header h3 { font-size: 16px; font-weight: 700; color: #1E293B; margin: 0; display: flex; align-items: center; gap: 8px; }
.card-header h3 i { color: #3B82F6; }
.link-text { color: #3B82F6; text-decoration: none; font-size: 13px; font-weight: 500; }
.link-text.sm { font-size: 12px; color: #94A3B8; cursor: pointer; }

/* --- TABLE --- */
.custom-table { width: 100%; border-collapse: collapse; }
.custom-table th { text-align: left; color: #94A3B8; font-size: 12px; font-weight: 600; text-transform: uppercase; padding-bottom: 12px; border-bottom: 1px solid #F1F5F9; }
.custom-table td { padding: 16px 0; border-bottom: 1px solid #F1F5F9; color: #334155; font-size: 14px; vertical-align: middle; }
.custom-table tr:last-child td { border-bottom: none; }
.custom-table .code { font-weight: 600; color: #1E293B; }
.custom-table .code small { color: #94A3B8; font-weight: 400; }
.custom-table .price { font-weight: 700; color: #1E293B; }

.status-badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
.status-badge.processing { background: #EFF6FF; color: #3B82F6; }
.status-badge.shipping { background: #FFEDD5; color: #F97316; }
.status-badge.completed { background: #DCFCE7; color: #16A34A; }

.btn-icon-sm { background: none; border: none; color: #94A3B8; cursor: pointer; }
.btn-icon-sm:hover { color: #3B82F6; }

/* --- QUICK ACCESS GRID --- */
.quick-access-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.quick-btn {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  background: #F8FAFC; padding: 20px; border-radius: 12px; text-decoration: none;
  transition: 0.2s; border: 1px solid transparent;
}
.quick-btn:hover { background: white; border-color: #E2E8F0; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
.q-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 12px; }
.quick-btn span { font-size: 13px; font-weight: 600; color: #475569; text-align: center; }

/* Colors for icons */
.bg-blue-100 { background: #DBEAFE; } .text-blue { color: #2563EB; }
.bg-purple-100 { background: #F3E8FF; } .text-purple { color: #9333EA; }
.bg-green-100 { background: #DCFCE7; } .text-green { color: #16A34A; }
.bg-orange-100 { background: #FFEDD5; } .text-orange { color: #EA580C; }

/* --- NOTIFICATIONS --- */
.notification-list { display: flex; flex-direction: column; gap: 16px; }
.notif-item { display: flex; gap: 12px; align-items: flex-start; padding-bottom: 12px; border-bottom: 1px solid #F1F5F9; }
.notif-item:last-child { border: none; padding: 0; }
.notif-icon { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 14px; }
.notif-icon.blue { background: #EFF6FF; color: #3B82F6; }
.notif-icon.yellow { background: #FEF9C3; color: #CA8A04; }
.notif-icon.green { background: #DCFCE7; color: #16A34A; }
.notif-text p { margin: 0 0 4px 0; font-size: 13px; color: #334155; line-height: 1.4; }
.notif-text small { color: #94A3B8; font-size: 11px; }

/* --- RESPONSIVE --- */
@media (max-width: 1024px) {
  .stats-row { grid-template-columns: 1fr 1fr; }
  .dashboard-grid { grid-template-columns: 1fr; }
}
@media (max-width: 640px) {
  .stats-row { grid-template-columns: 1fr; }
}
</style>