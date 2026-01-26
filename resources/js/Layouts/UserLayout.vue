<template>
  <div class="app-wrapper">
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
          <Link href="/user/dashboard" :class="{ 'active': page.url.startsWith('/user/dashboard') }" class="nav-item">
            <i class="fas fa-th-large"></i>
            <span class="nav-text">Tổng quan</span>
          </Link>

          <Link href="/user/orders" :class="{ 'active': page.url.startsWith('/user/orders') }" class="nav-item">
            <div class="nav-item-content">
              <i class="fas fa-box"></i>
              <span class="nav-text">Đơn hàng</span>
            </div>
          </Link>

          <Link href="/user/services" :class="{ 'active': page.url.startsWith('/user/services') }" class="nav-item">
            <i class="fas fa-file-medical"></i>
            <span class="nav-text">Dịch vụ</span>
          </Link>

          <Link href="/user/notifications" :class="{ 'active': page.url.startsWith('/user/notifications') }" class="nav-item">
            <div class="nav-item-content">
              <div class="icon-wrapper">
                <i class="fas fa-bell"></i>
                <span class="dot-floating" v-if="unreadNotificationsCount > 0"></span>
              </div>
              <span class="nav-text">Thông báo</span>
            </div>
          </Link>

          <Link href="/user/profile-settings" :class="{ 'active': page.url.startsWith('/user/profile-settings') }" class="nav-item">
            <i class="fas fa-user-cog"></i>
            <span class="nav-text">Cài đặt hồ sơ</span>
          </Link>
          <Link href="/" :class="{ 'active': page.url.startsWith('/') }" class="nav-item">
            <i class="fas fa-home"></i>
            <span class="nav-text">Trang chủ</span>
          </Link>
        </nav>
      </div>


      <div class="sidebar-footer">
        <Link href="/logout" method="post" as="button" class="nav-item logout">
          <i class="fas fa-sign-out-alt"></i> <span>Đăng xuất</span>
        </Link>
      </div>
    </aside>

    <div class="main-wrapper">
      <header class="topbar">
        <div class="page-title">
          <h2>{{ pageTitle || 'Dashboard' }}</h2>
          <span class="date">{{ currentDate }}</span>
        </div>

        <div class="topbar-actions">
           <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Tìm kiếm thuốc, đơn hàng..." />
          </div>
          <button class="icon-btn"><i class="fas fa-bell"></i></button>
          <button class="icon-btn"><i class="fas fa-question-circle"></i></button>
        </div>
      </header>

      <main class="page-content">
        <slot />
      </main>
    </div>

    <Toast />
  </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import Toast from 'primevue/toast'

const props = defineProps({
  auth: Object,
  pageTitle: String,
  unreadNotificationsCount: Number
})

// Láº¥y thĂ´ng tin auth tá»« Inertia
const page = usePage()
const auth = page.props.auth || props.auth

const pendingOrders = 3 // Mock data styling

// HĂ m hiá»ƒn thá»‹ role báº±ng tiáº¿ng Viá»‡t
const getUserRoleDisplay = () => {
  const role = auth?.user?.role
  if (!role) return 'Khách hàng'

  const roleMap = {
    'staff': 'Nhân viên',
    'admin': 'Quản trị viên',
    'user': 'Khách hàng'
  }

  return roleMap[role] || 'Khách hàng'
}

const currentDate = computed(() => {
  return new Date().toLocaleDateString('vi-VN', { weekday: 'long', day: '2-digit', month: '2-digit', year: 'numeric' })
})
</script>

<style scoped>
/* --- RESET & LAYOUT --- */
.app-wrapper {
  display: flex;
  min-height: 100vh;
  background-color: #F8FAFC;
  font-family: 'Inter', sans-serif;
}

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

.sidebar-header {
  height: 70px;
  display: flex;
  align-items: center;
  padding: 0 24px;
}

.logo { display: flex; align-items: center; gap: 10px; }
.logo-icon { width: 32px; height: 32px; background: #3B82F6; color: white; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
.brand-name { font-weight: 700; font-size: 18px; color: #1E293B; }

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
  gap: 12px;
}

.nav-item i {
  width: 24px;
  text-align: center;
  font-size: 18px;
  flex-shrink: 0;
}

.nav-item:hover { background: #F8FAFC; color: #3B82F6; }
.nav-item.active { background: #EFF6FF; color: #3B82F6; font-weight: 600; }

.nav-item-content {
  display: flex;
  align-items: center;
  gap: 12px;
  width: 100%;
}

.icon-wrapper {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}

.dot-floating {
  position: absolute;
  top: 0;
  right: 0;
  width: 8px;
  height: 8px;
  background: #EF4444;
  border-radius: 50%;
  border: 1px solid white;
}

.badge { background: #EF4444; color: white; font-size: 10px; padding: 2px 6px; border-radius: 10px; font-weight: bold; }

.sidebar-footer { padding: 16px; border-top: 1px solid #E2E8F0; }
.logout { color: #EF4444; }
.logout:hover { background: #FEF2F2; color: #DC2626; }

/* --- MAIN WRAPPER --- */
.main-wrapper {
  margin-left: 260px; /* Width of Sidebar */
  flex: 1;
  display: flex;
  flex-direction: column;
}

/* --- TOPBAR --- */
.topbar {
  height: 70px;
  background: white; /* Or transparent depending on preference */
  padding: 0 32px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid #E2E8F0;
  position: sticky;
  top: 0;
  z-index: 40;
}

.page-title h2 { font-size: 20px; font-weight: 700; margin: 0; color: #1E293B; }
.page-title .date { font-size: 13px; color: #94A3B8; }

.topbar-actions { display: flex; align-items: center; gap: 16px; }
.search-box { background: #F1F5F9; border-radius: 8px; padding: 8px 16px; display: flex; align-items: center; gap: 8px; width: 300px; }
.search-box input { border: none; background: transparent; outline: none; width: 100%; font-size: 14px; color: #334155; }
.search-box i { color: #94A3B8; }
.icon-btn { width: 40px; height: 40px; border-radius: 50%; border: 1px solid #E2E8F0; background: white; color: #64748B; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; }
.icon-btn:hover { background: #F8FAFC; color: #3B82F6; }

/* --- CONTENT --- */
.page-content { padding: 32px; }

/* Responsive */
@media (max-width: 1024px) {
  .sidebar {
    width: 80px;
    padding-top: 20px;
  }

  .nav-text,
  .logo span,
  .user-meta,
  .sidebar-footer span {
    display: none !important;
  }

  .nav-item {
    justify-content: center;
    padding: 16px 0;
  }

  .nav-item-content {
    justify-content: center;
    width: auto;
  }

  .nav-item i {
    margin: 0;
  }

  .search-box {
    display: none;
  }

  .main-wrapper {
    margin-left: 80px;
  }
}
</style>
