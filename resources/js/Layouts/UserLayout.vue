<template>
  <div class="user-layout">
    <div class="dashboard-container">
      <!-- Sidebar -->
      <div class="sidebar">
        <div class="sidebar-header">
          <h3><i class="fas fa-heartbeat me-2"></i>Sức Khỏe 24h</h3>
        </div>
        
        <div class="user-info">
          <div class="user-avatar">
            <img 
              v-if="auth?.user?.avatar" 
              :src="`/storage/avatars/${auth.user.avatar}`" 
              alt="Avatar" 
              class="avatar-img"
            />
            <i v-else class="fas fa-user"></i>
          </div>
          <div class="user-details">
            <h5>{{ auth?.user?.name || 'User' }}</h5>
            <small>{{ auth?.user?.email || 'user@example.com' }}</small>
          </div>
        </div>

        <ul class="sidebar-menu">
          <!-- Dashboard Overview -->
          <li>
            <Link href="/user/dashboard" :class="{ active: page.url.startsWith('/user/dashboard') }">
              <i class="fas fa-tachometer-alt"></i>Tổng quan
            </Link>
          </li>
          
          <!-- Account Settings -->
          <li>
            <Link href="/user/profile-settings" :class="{ active: page.url.startsWith('/user/profile-settings') }">
              <i class="fas fa-user-cog"></i>Cài đặt hồ sơ
            </Link>
          </li>
          
          <!-- Đơn hàng của tôi -->
          <li>
            <Link href="/user/orders" :class="{ active: page.url.startsWith('/user/orders') }">
              <i class="fas fa-clipboard-list"></i>Đơn hàng
            </Link>
          </li>
          
          <!-- Hồ sơ sức khỏe -->
          <li>
            <Link href="/user/health-profile" :class="{ active: page.url.startsWith('/user/health-profile') }">
              <i class="fas fa-file-medical"></i>Hồ sơ sức khỏe
            </Link>
          </li>
          
          <!-- Thông báo -->
          <li>
            <Link href="/user/notifications" :class="{ active: page.url.startsWith('/user/notifications') }">
              <i class="fas fa-bell"></i>Thông báo
            </Link>
          </li>
          
          <!-- Divider -->
          <li class="divider"></li>
          
          <!-- Trang chủ -->
          <li>
            <Link href="/">
              <i class="fas fa-home"></i>Trang chủ
            </Link>
          </li>
          
          <!-- Đăng xuất -->
          <li>
            <form @submit.prevent="handleLogout" style="display: inline;">
              <button type="submit" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>Đăng xuất
              </button>
            </form>
          </li>
        </ul>
      </div>

      <!-- Main Content -->
      <div class="main-content">
        <!-- Page Header -->
        <div v-if="pageTitle || pageDescription" class="page-header">
          <h1>{{ pageTitle || 'Dashboard' }}</h1>
          <p v-if="pageDescription">{{ pageDescription }}</p>
        </div>

        <!-- Page Content -->
        <slot />
      </div>
    </div>
    
    <!-- Toast for notifications -->
    <Toast />
  </div>
</template>

<script setup>
import { Link, router, usePage } from '@inertiajs/vue3'
import Toast from 'primevue/toast'

// Props từ Inertia
const props = defineProps({
  auth: {
    type: Object,
    default: () => ({ user: null })
  },
  pageTitle: {
    type: String,
    default: ''
  },
  pageDescription: {
    type: String,
    default: ''
  }
})

// Get current page from Inertia
const page = usePage()

const pageTitle = page.props.pageTitle || ''
const pageDescription = page.props.pageDescription || ''

// Handle logout
const handleLogout = () => {
  router.post('/logout')
}
</script>

<style scoped>
/* USER LAYOUT STYLES */

.user-layout {
  font-family: 'Inter', sans-serif;
  background: #f8fafc;
  min-height: 100vh;
}

.dashboard-container {
  display: flex;
  min-height: 100vh;
}

/* Sidebar */
.sidebar {
  width: 280px;
  background: white;
  border-right: 1px solid #e2e8f0;
  padding: 30px 0;
  position: fixed;
  height: 100vh;
  overflow-y: auto;
}

.sidebar-header {
  padding: 0 30px 30px;
  border-bottom: 1px solid #e2e8f0;
  margin-bottom: 30px;
}

.sidebar-header h3 {
  color: #1e293b;
  font-weight: 700;
  font-size: 24px;
  margin: 0;
}

.user-info {
  display: flex;
  align-items: center;
  padding: 0 30px 20px;
  margin-bottom: 20px;
}

.user-avatar {
  width: 50px;
  height: 50px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 20px;
  margin-right: 15px;
  overflow: hidden;
}

.avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 12px;
}

.user-details h5 {
  margin: 0;
  font-weight: 600;
  color: #1e293b;
  font-size: 14px;
}

.user-details small {
  color: #64748b;
  font-size: 12px;
  display: block;
}

.sidebar-menu {
  list-style: none;
  padding: 0;
  margin: 0;
}

.sidebar-menu li {
  margin-bottom: 2px;
}

.sidebar-menu li.divider {
  margin: 20px 0;
  border-top: 1px solid #e2e8f0;
  padding: 0;
}

.sidebar-menu a,
.logout-btn {
  display: flex;
  align-items: center;
  padding: 12px 30px;
  color: #64748b;
  text-decoration: none;
  transition: all 0.2s ease;
  font-size: 14px;
  font-weight: 500;
  background: none;
  border: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
  font-family: inherit;
}

.logout-btn {
  color: #64748b;
}

.sidebar-menu a:hover,
.sidebar-menu a.active,
.logout-btn:hover {
  background: #f1f5f9;
  color: #3b82f6;
}

.sidebar-menu a i,
.logout-btn i {
  width: 20px;
  margin-right: 12px;
  font-size: 16px;
}

/* Main Content */
.main-content {
  flex: 1;
  margin-left: 280px;
  padding: 40px;
}

.page-header {
  margin-bottom: 40px;
}

.page-header h1 {
  color: #1e293b;
  font-weight: 700;
  font-size: 32px;
  margin-bottom: 8px;
}

.page-header p {
  color: #64748b;
  font-size: 16px;
  margin: 0;
}

/* Responsive */
@media (max-width: 768px) {
  .sidebar {
    transform: translateX(-100%);
    transition: transform 0.3s ease;
  }
  
  .main-content {
    margin-left: 0;
    padding: 20px;
  }
  
  .page-header h1 {
    font-size: 24px;
  }
}
</style>

