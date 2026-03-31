<template>
  <div class="layout-wrapper">
    <aside class="sidebar admin-sidebar">
      <div class="sidebar-header">
        <div class="app-name">
          <h1 class="brand text-primary">Admin System</h1>
          <span class="sub-brand">Xin chào, {{ auth?.user?.name || 'Quản trị viên' }}</span>
        </div>
      </div>

      <div class="sidebar-menu">
        <ul class="menu-list">
          <!-- TỔNG QUAN -->
          <li class="menu-label">HỆ THỐNG GIAO DỊCH</li>
          <li>
            <Link href="/admin/admindashboard" class="menu-item"
              :class="{ 'active': $page.url === '/admin/admindashboard' }">
              <i class="pi pi-home"></i>
              <span>Tổng quan</span>
            </Link>
          </li>

          <!-- HÀNG HÓA -->
          <li class="menu-label">HÀNG HÓA</li>
          <li>
            <Link href="/admin/products" class="menu-item" :class="{ 'active': $page.url === '/admin/products' }">
              <i class="pi pi-box"></i>
              <span>Cập nhật thông tin</span>
            </Link>
            <Link href="/admin/products/list" class="menu-item"
              :class="{ 'active': $page.url.startsWith('/admin/products/list') }">
              <i class="pi pi-list"></i>
              <span>Danh sách hàng hóa</span>
            </Link>
            <Link href="/admin/products/stock" class="menu-item"
              :class="{ 'active': $page.url.startsWith('/admin/products/stock') }">
              <i class="pi pi-database"></i>
              <span>Kiểm tra tồn kho</span>
            </Link>
          </li>

          <!-- NHẬP HÀNG -->
          <li class="menu-label">NHẬP & TRẢ HÀNG</li>
          <li>
            <Link href="/admin/import" class="menu-item" :class="{ 'active': $page.url.startsWith('/admin/import') }">
              <i class="pi pi-cart-plus"></i>
              <span>Đặt hàng</span>
            </Link>
            <Link href="/admin/purchase-returns" class="menu-item"
              :class="{ 'active': $page.url.startsWith('/admin/purchase-returns') }">
              <i class="pi pi-replay"></i>
              <span>Trả hàng</span>
            </Link>
          </li>

          <!-- TRUNG TÂM DỊCH VỤ -->
          <li class="menu-label">TRUNG TÂM DỊCH VỤ</li>
          <li>
            <Link href="/admin/orders" class="menu-item"
              :class="{ 'active': $page.url.startsWith('/admin/orders') && !$page.url.includes('transport') }">
              <i class="pi pi-shopping-cart"></i>
              <span>Đơn hàng</span>
            </Link>
            <Link href="/admin/orders/transport" class="menu-item"
              :class="{ 'active': $page.url.startsWith('/admin/orders/transport') }">
              <i class="pi pi-truck"></i>
              <span>Vận đơn</span>
            </Link>
            <Link href="/admin/services-dashboard" class="menu-item"
              :class="{ 'active': $page.url.startsWith('/admin/services-dashboard') }">
              <i class="pi pi-briefcase"></i>
              <span>Quản lý dịch vụ</span>
            </Link>
          </li>

          <!-- KHÁCH HÀNG & ĐỐI TÁC -->
          <li class="menu-label">ĐỐI TÁC - KHÁCH HÀNG</li>
          <li>
            <Link href="/admin/customers" class="menu-item"
              :class="{ 'active': $page.url.startsWith('/admin/customers') }">
              <i class="pi pi-users"></i>
              <span>Khách hàng</span>
            </Link>
            <Link href="/admin/suppliers" class="menu-item"
              :class="{ 'active': $page.url.startsWith('/admin/suppliers') }">
              <i class="pi pi-building"></i>
              <span>Nhà cung cấp</span>
            </Link>
            <Link href="/admin/doctors" class="menu-item" :class="{ 'active': $page.url.startsWith('/admin/doctors') }">
              <i class="pi pi-heart-fill"></i>
              <span>Bác sĩ</span>
            </Link>
          </li>

          <!-- NỘI BỘ -->
          <li class="menu-label">NỘI BỘ & HỖ TRỢ</li>
          <li>
            <Link href="/admin/employees" class="menu-item"
              :class="{ 'active': $page.url.startsWith('/admin/employees') }">
              <i class="pi pi-id-card"></i>
              <span>Quản lý nhân viên</span>
            </Link>
            <Link href="/admin/employee-schedules" class="menu-item"
              :class="{ 'active': $page.url.startsWith('/admin/employee-schedules') }">
              <i class="pi pi-calendar"></i>
              <span>Lịch làm việc</span>
            </Link>
            <Link href="/admin/tickets" class="menu-item" :class="{ 'active': $page.url.startsWith('/admin/tickets') }">
              <i class="pi pi-ticket"></i>
              <span>Danh sách yêu cầu</span>
            </Link>
          </li>

          <!-- BÁO CÁO -->
          <li class="menu-label">BÁO CÁO THỐNG KÊ</li>
          <li>
            <Link href="/admin/reports/top-products-sell" class="menu-item"
              :class="{ 'active': $page.url.includes('top-products-sell') }">
              <i class="pi pi-chart-line"></i>
              <span>Báo cáo Hàng hóa</span>
            </Link>
            <Link href="/admin/reports/top-stock-imports" class="menu-item"
              :class="{ 'active': $page.url.includes('stock-imports') || $page.url.includes('stock-returns') }">
              <i class="pi pi-chart-bar"></i>
              <span>Báo cáo Đặt/Trả hàng</span>
            </Link>
            <Link href="/admin/reports/top-customers" class="menu-item"
              :class="{ 'active': $page.url.includes('top-customers') || $page.url.includes('top-suppliers') }">
              <i class="pi pi-chart-pie"></i>
              <span>Báo cáo Khách & Đối tác</span>
            </Link>
          </li>
        </ul>

        <div class="bottom-menu mt-4">
          <li class="menu-label">HỆ THỐNG</li>
          <li>
            <Link href="/settings" class="menu-item" :class="{ 'active': $page.url.startsWith('/settings') }">
              <i class="pi pi-cog"></i>
              <span>Cài đặt hệ thống</span>
            </Link>
          </li>
          <li>
            <Link href="/logout" method="post" as="button" class="menu-item logout-item"
              style="width: 100%; border: none; background: none;">
              <i class="pi pi-sign-out"></i>
              <span>Đăng xuất</span>
            </Link>
          </li>
        </div>
      </div>

      <div class="user-profile">
        <div class="avatar">
          <img v-if="auth?.user?.avatar" :src="`/storage/avatars/${auth.user.avatar}`" alt="User" />
          <i v-else class="pi pi-shield" style="font-size: 1.5rem; color: #64748b;"></i>
        </div>
        <div class="user-info">
          <span class="user-name">{{ auth?.user?.name || 'Quản trị viên' }}</span>
          <span class="user-role">{{ getUserRoleDisplay() }}</span>
        </div>
      </div>
    </aside>

    <main class="main-content">
      <div class="content-body">
        <slot />
      </div>
    </main>

    <Toast />
  </div>
</template>

<script setup>
import { usePage, Link } from '@inertiajs/vue3';
import Toast from 'primevue/toast';

// Lấy thông tin auth từ Inertia
const page = usePage();
const auth = page.props.auth;

// Hàm hiển thị role bằng tiếng Việt
const getUserRoleDisplay = () => {
  const role = auth?.user?.role;
  if (!role) return 'Quản trị viên';

  const roleMap = {
    'staff': 'Nhân viên',
    'admin': 'Quản trị tối cao',
    'user': 'Khách hàng'
  };

  return roleMap[role] || 'Quản trị viên';
};
</script>

<style scoped>
.layout-wrapper {
  display: flex;
  min-height: 100vh;
  background-color: #f8fafc;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
}

/* --- 1. SIDEBAR STYLES --- */
.sidebar {
  width: 260px;
  background: white;
  border-right: 1px solid #e2e8f0;
  display: flex;
  flex-direction: column;
  position: fixed;
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

.app-name {
  display: flex;
  flex-direction: column;
}

.brand {
  font-size: 20px;
  font-weight: 800;
  color: #1e293b;
  margin: 0;
}

.text-primary {
  color: #3b82f6;
}

.sub-brand {
  font-size: 11px;
  color: #64748b;
  margin-top: 2px;
}

/* Menu List */
.sidebar-menu {
  flex: 1;
  overflow-y: auto;
  padding: 10px 16px;
}

/* Hide scrollbar for a cleaner look */
.sidebar-menu::-webkit-scrollbar {
  width: 4px;
}

.sidebar-menu::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}

.menu-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.menu-label {
  font-size: 11px;
  font-weight: 700;
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

/* Active State */
.menu-item.active {
  background-color: #3b82f6;
  color: white;
  font-weight: 600;
  box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.4);
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
  background: #e2e8f0;
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
  font-weight: 500;
}

/* --- 2. MAIN CONTENT STYLES --- */
.main-content {
  margin-left: 260px;
  flex: 1;
  display: flex;
  flex-direction: column;
  width: calc(100% - 260px);
}

.topbar {
  background: white;
  height: 64px;
  padding: 0 32px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: sticky;
  top: 0;
  z-index: 999;
  border-bottom: 1px solid #e2e8f0;
}

.page-breadcrumb {
  display: flex;
  align-items: center;
  gap: 12px;
}

.topbar-actions {
  display: flex;
  align-items: center;
  gap: 16px;
}

.btn-icon {
  background: transparent;
  border: none;
  color: #64748b;
  font-size: 18px;
  cursor: pointer;
  padding: 8px;
  border-radius: 50%;
  transition: all 0.2s;
}

.btn-icon:hover {
  background: #f1f5f9;
  color: #1e293b;
}

.content-body {
  padding: 24px;
  flex: 1;
  margin-top: -70px;
}

/* Bỏ các background/border mặc định cho button primevue trong AdminLayout */
.main-content :deep(.p-button-group .p-button.btn) {
  background: initial !important;
  border: initial !important;
  color: initial !important;
}

@media (max-width: 1024px) {
  .sidebar {
    width: 80px;
  }

  .app-name,
  .menu-item span,
  .menu-label,
  .user-info {
    display: none;
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
