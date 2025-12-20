<template>
  <aside class="sidebar">
    <div class="sidebar-content">
      <div class="user-card">
        <img v-if="auth?.user?.avatar" :src="`/storage/avatars/${auth.user.avatar}`" class="avatar" />
        <div v-else class="avatar-placeholder">{{ auth?.user?.name?.charAt(0) || 'U' }}</div>
        <div class="user-meta">
          <span class="name">{{ auth?.user?.name || 'Người dùng' }}</span>
          <span class="role">{{ getUserRoleDisplay() }}</span>
        </div>
      </div>

      <nav class="nav-menu">
        <Link href="/user/dashboard" :class="{ 'active': isActive('/user/dashboard') }" class="nav-item">
          <i class="fas fa-th-large"></i> <span>Tổng quan</span>
        </Link>
        <Link href="/user/orders" :class="{ 'active': isActive('/user/orders') }" class="nav-item">
          <div class="nav-item-content">
            <span><i class="fas fa-box"></i> Đơn hàng</span>
          </div>
        </Link>
        <Link href="/user/services" :class="{ 'active': isActive('/user/services') }" class="nav-item">
          <i class="fas fa-file-medical"></i> <span>Dịch vụ</span>
        </Link>
        <Link href="/user/notifications" :class="{ 'active': isActive('/user/notifications') }" class="nav-item">
          <div class="nav-item-content">
            <span><i class="fas fa-bell"></i> Thông báo</span>
            <span class="dot" v-if="unreadNotificationsCount > 0"></span>
          </div>
        </Link>
        <Link href="/user/profile-settings" :class="{ 'active': isActive('/user/profile-settings') }" class="nav-item">
          <i class="fas fa-user-cog"></i> <span>Cài đặt hồ sơ</span>
        </Link>
        <Link href="/" :class="{ 'active': isActive('/') }" class="nav-item">
          <i class="fas fa-home"></i> <span>Trang chủ</span>
        </Link>
      </nav>
    </div>

    <div class="sidebar-footer">
      <Link href="/logout" method="post" as="button" class="nav-item logout">
        <i class="fas fa-sign-out-alt"></i> <span>Đăng xuất</span>
      </Link>
    </div>
  </aside>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'

const props = defineProps({
  auth: Object,
  unreadNotificationsCount: {
    type: Number,
    default: 0
  }
})

const page = usePage()

// Hàm hiển thị role bằng tiếng Việt
const getUserRoleDisplay = () => {
  const role = props.auth?.user?.role
  if (!role) return 'Khách hàng'
  
  const roleMap = {
    'staff': 'Nhân viên',
    'admin': 'Quản trị viên',
    'user': 'Khách hàng'
  }
  
  return roleMap[role] || 'Khách hàng'
}

// Kiểm tra active route
const isActive = (path) => {
  return page.url.startsWith(path)
}
</script>

<style scoped>
/* --- SIDEBAR --- */
.sidebar {
  width: 260px;
  background: white;
  border-right: 1px solid #E2E8F0;
  display: flex;
  flex-direction: column;
  position: fixed;
  height: 100vh;
  z-index: 50;
}

.sidebar-content { padding: 20px 16px; flex: 1; overflow-y: auto; }

/* User Card in Sidebar */
.user-card {
  background: #F1F5F9;
  border-radius: 12px;
  padding: 12px;
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 24px;
}
.avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
.avatar-placeholder { width: 40px; height: 40px; border-radius: 50%; background: #CBD5E1; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; }
.user-meta { display: flex; flex-direction: column; }
.user-meta .name { font-weight: 600; font-size: 14px; color: #1E293B; }
.user-meta .role { font-size: 12px; color: #64748B; }

/* Nav Menu */
.nav-item {
  display: flex;
  align-items: center;
  padding: 12px 16px;
  color: #64748B;
  text-decoration: none;
  border-radius: 8px;
  margin-bottom: 4px;
  font-weight: 500;
  transition: all 0.2s;
  cursor: pointer;
  border: none;
  background: none;
  width: 100%;
  font-size: 14px;
}

.nav-item i { width: 24px; font-size: 16px; }

.nav-item:hover { background: #F8FAFC; color: #3B82F6; }
.nav-item.active { background: #EFF6FF; color: #3B82F6; font-weight: 600; }

.nav-item-content { display: flex; justify-content: space-between; width: 100%; align-items: center; }
.dot { width: 8px; height: 8px; background: #3B82F6; border-radius: 50%; }

.sidebar-footer { padding: 16px; border-top: 1px solid #E2E8F0; }
.logout { color: #EF4444; }
.logout:hover { background: #FEF2F2; color: #DC2626; }

/* Responsive */
@media (max-width: 1024px) {
  .sidebar { width: 80px; }
  .user-meta, .nav-item span:not(.badge), .sidebar-footer span { display: none; }
  .user-card { justify-content: center; padding: 8px; }
  .nav-item { justify-content: center; padding: 16px 0; }
  .nav-item i { margin: 0; }
}
</style>

