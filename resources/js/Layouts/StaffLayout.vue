<template>
  <div class="layout-wrapper">
    <aside class="sidebar">
      <div class="sidebar-header">
        <div class="app-name">
          <h1 class="brand">Dashboard Staff</h1>
          <span class="sub-brand">Xin chào, {{ auth?.user?.name || 'Nhân viên' }}</span>
        </div>
      </div>

      <div class="sidebar-menu">
        <ul class="menu-list">
          <li>
            <a href="/staff/dashboard" class="menu-item active">
              <i class="pi pi-th-large"></i>
              <span>Tổng quan</span>
            </a>
          </li>

          <li class="menu-label">KHO HÀNG</li>
          <li>
            <a href="/staff/products/stock" class="menu-item">
              <i class="pi pi-box"></i>
              <span>Kiểm kho sản phẩm</span>
            </a>
          </li>

          <li class="menu-label">DỊCH VỤ</li>
          <li>
            <a href="/staff/orders" class="menu-item">
              <i class="pi pi-shopping-cart"></i>
              <span>Quản lý đơn hàng</span>
            </a>
            <a href="#" class="menu-item">
              <i class="pi pi-truck"></i>
              <span>Vận đơn</span>
            </a>
          </li>
          <li>
            <a href="/staff/service-bookings" class="menu-item">
              <i class="pi pi-calendar-plus"></i>
              <span>Đặt lịch dịch vụ</span>
            </a>
          </li>

          <li class="menu-label">KHÁCH HÀNG</li>
          <li>
            <a href="/staff/customers" class="menu-item">
              <i class="pi pi-users"></i>
              <span>Danh sách khách hàng</span>
            </a>
          </li>

          <li class="menu-label">Tickets</li>
          <li>
            <a href="/staff/tickets" class="menu-item">
              <i class="pi pi-ticket"></i>
              <span>Quản lý Tickets</span>
            </a>
          </li>

          <li class="menu-label">Tin Tức</li>
          <li>
            <a href="/staff/categories" class="menu-item">
              <i class="pi pi-list"></i>
              <span>Quản lý danh mục</span>
            </a>
            <a href="/staff/posts" class="menu-item">
              <i class="pi pi-file"></i>
              <span>Quản lý bài viết</span>
            </a>
          </li>
          <li class="menu-label">CÁ NHÂN</li>
          <li>
            <a href="/staff/my-schedule" class="menu-item">
              <i class="pi pi-calendar"></i>
              <span>Lịch làm việc</span>
            </a>
          </li>
        </ul>

        <div class="bottom-menu">
          <li class="menu-label">HỆ THỐNG</li>
          <li>
            <a href="/settings" class="menu-item">
              <i class="pi pi-cog"></i>
              <span>Cài đặt</span>
            </a>
          </li>
          <li>
            <a href="/logout" class="menu-item logout-item">
              <i class="pi pi-sign-out"></i>
              <span>Đăng xuất</span>
            </a>
          </li>
        </div>
      </div>

      <div class="user-profile">
        <div class="avatar">
          <img v-if="auth?.user?.avatar" :src="`/storage/avatars/${auth.user.avatar}`" alt="User" />
          <i v-else class="pi pi-user" style="font-size: 1.5rem; color: #64748b;"></i>
        </div>
        <div class="user-info">
          <span class="user-name">{{ auth?.user?.name || 'Nhân viên' }}</span>
          <span class="user-role">{{ getUserRoleDisplay() }}</span>
        </div>
      </div>
    </aside>

    <main class="main-content">
      <header class="topbar">
        <div class="page-breadcrumb">
          <span class="font-bold text-xl">Dashboard</span>
        </div>
        <div class="topbar-actions">
          <button class="btn-icon"><i class="pi pi-bell"></i></button>
          <button class="btn-icon"><i class="pi pi-question-circle"></i></button>
        </div>
      </header>

      <div class="content-body">
        <slot />
      </div>
    </main>

    <Toast />
  </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';
import Toast from 'primevue/toast';

// Lấy thông tin auth từ Inertia
const page = usePage();
const auth = page.props.auth;

// Hàm hiển thị role bằng tiếng Việt
const getUserRoleDisplay = () => {
  const role = auth?.user?.role;
  if (!role) return 'Nhân viên';

  const roleMap = {
    'staff': 'Nhân viên',
    'admin': 'Quản trị viên',
    'user': 'Khách hàng'
  };

  return roleMap[role] || 'Nhân viên';
};
</script>

<style scoped>
.layout-wrapper {
  display: flex;
  min-height: 100vh;
  background-color: #f8fafc;
  /* Màu nền xám nhạt tổng thể */
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
}

/* --- 1. SIDEBAR STYLES --- */
.sidebar {
  width: 260px;
  /* Chiều rộng cố định sidebar */
  background: white;
  border-right: 1px solid #e2e8f0;
  display: flex;
  flex-direction: column;
  position: fixed;
  /* Cố định bên trái */
  top: 0;
  bottom: 0;
  left: 0;
  z-index: 1000;
}

.sidebar-header {
  padding: 24px;
  display: flex;
  align-items: center;
  gap: 12px;
  border-bottom: 1px solid transparent;
}

.logo-icon {
  width: 32px;
  height: 32px;
  background: #3b82f6;
  /* Màu xanh logo */
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.app-name {
  display: flex;
  flex-direction: column;
}

.brand {
  font-size: 18px;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
}

.sub-brand {
  font-size: 11px;
  color: #64748b;
}

/* Menu List */
.sidebar-menu {
  flex: 1;
  overflow-y: auto;
  padding: 10px 16px;
}

.menu-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.menu-label {
  font-size: 11px;
  font-weight: 600;
  color: #94a3b8;
  margin-top: 24px;
  margin-bottom: 8px;
  padding-left: 12px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.menu-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  border-radius: 8px;
  text-decoration: none;
  color: #475569;
  font-weight: 500;
  font-size: 14px;
  transition: all 0.2s ease;
  margin-bottom: 4px;
}

.menu-item i {
  font-size: 18px;
}

.menu-item:hover {
  background-color: #f1f5f9;
  color: #1e293b;
}

/* Active State (Giống ảnh mẫu) */
.menu-item.active {
  background-color: #3b82f6;
  /* Màu xanh dương chủ đạo */
  color: white;
  font-weight: 600;
  box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.5);
}

.logout-item:hover {
  background-color: #fee2e2;
  color: #ef4444;
}

/* User Profile Bottom */
.user-profile {
  padding: 16px;
  border-top: 1px solid #f1f5f9;
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 0 16px 16px 16px;
  background: #f8fafc;
  border-radius: 12px;
}

.avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f1f5f9;
  overflow: hidden;
}

.avatar img {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
}

.user-info {
  display: flex;
  flex-direction: column;
}

.user-name {
  font-size: 14px;
  font-weight: 600;
  color: #1e293b;
}

.user-role {
  font-size: 12px;
  color: #64748b;
}

/* --- 2. MAIN CONTENT STYLES --- */
.main-content {
  margin-left: 260px;
  /* Đẩy nội dung sang phải bằng width sidebar */
  flex: 1;
  display: flex;
  flex-direction: column;
  width: calc(100% - 260px);
}

.topbar {
  background: white;
  /* Hoặc trong suốt tùy thích */
  height: 64px;
  padding: 0 32px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: sticky;
  top: 0;
  z-index: 999;
  /* box-shadow: 0 1px 2px rgba(0,0,0,0.03); */
}

.page-breadcrumb {
  display: flex;
  align-items: center;
  gap: 12px;
}

.version-badge {
  font-size: 12px;
  background: #e2e8f0;
  color: #64748b;
  padding: 2px 6px;
  border-radius: 4px;
}

.topbar-actions {
  display: flex;
  align-items: center;
  gap: 16px;
}

.search-box {
  background: #f1f5f9;
  padding: 8px 16px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 8px;
  color: #94a3b8;
  width: 300px;
}

.search-box input {
  border: none;
  background: transparent;
  outline: none;
  width: 100%;
  color: #1e293b;
  font-size: 14px;
}

.btn-icon {
  background: transparent;
  border: none;
  color: #64748b;
  font-size: 18px;
  cursor: pointer;
  padding: 8px;
  border-radius: 50%;
  transition: background 0.2s;
}

.btn-icon:hover {
  background: #f1f5f9;
  color: #1e293b;
}

.content-body {
  padding: 0;
  /* Padding do component con tự xử lý */
  flex: 1;
}

/* Responsive Tablet/Mobile */
@media (max-width: 1024px) {
  .sidebar {
    width: 80px;
    /* Thu nhỏ sidebar */
  }

  .app-name,
  .menu-item span,
  .menu-label,
  .user-info {
    display: none;
    /* Ẩn chữ */
  }

  .sidebar-header,
  .sidebar-menu,
  .menu-item {
    justify-content: center;
    padding-left: 0;
    padding-right: 0;
  }

  .main-content {
    margin-left: 80px;
    width: calc(100% - 80px);
  }
}
</style>