<template>
  <div class="admin-layout">
    <!-- Admin Navigation - Giữ nguyên cấu trúc navbar phức tạp -->
    <nav class="navbar">
      <div class="nav-menu">
        <a href="/admin/dashboard" class="nav-item active">Tổng Quan</a>
        <div class="nav-item dropdown">
          <span class="nav-item">Hàng hóa</span>
          <div class="nav-dropdown">
            <div class="dropdown-col">
              <div class="dropdown-title">Hàng hóa</div>
                <a href="/admin/products" class="dropdown-link">Tổng quan</a>
                <a href="/admin/medicines/list" class="dropdown-link">Danh sách thuốc</a>
                <a href="/admin/goods/list" class="dropdown-link">Danh sách hàng hóa</a>
            </div>
            <div class="dropdown-col">
              <div class="dropdown-title">Kho hàng</div>
              <a href="#" class="dropdown-link">Kiểm kho sản phẩm</a>
            </div>
            <div class="dropdown-col">
              <div class="dropdown-title">Nhập hàng</div>
              <a href="/admin/suppliers" class="dropdown-link">Nhà cung cấp</a>
              <a href="/admin/import" class="dropdown-link">Nhập hàng</a>
              <a href="/admin/purchase-returns" class="dropdown-link">Trả hàng nhập</a>
            </div>
          </div>
        </div>
        <div class="nav-item dropdown">
          <span class="nav-item">Đơn hàng</span>
          <div class="nav-dropdown nav-dropdown-short">
            <div class="dropdown-col">
              <div class="dropdown-title">Tổng quan</div>
              <a href="/admin/orders" class="dropdown-link">Quản lý đơn hàng</a>
            </div>
          </div>
        </div>
        <div class="nav-item dropdown">
          <span class="nav-item">Khách hàng</span>
          <div class="nav-dropdown nav-dropdown-short">
            <div class="dropdown-col">
              <div class="dropdown-title">Tổng quan</div>
              <a href="/admin/customers" class="dropdown-link">Quản lý khách hàng</a>
            </div>
          </div>
        </div>
        <div class="nav-item dropdown">
          <span class="nav-item">Bác sĩ</span>
          <div class="nav-dropdown nav-dropdown-short">
            <div class="dropdown-col">
              <div class="dropdown-title">Bác sĩ</div>
              <a href="/admin/doctors" class="dropdown-link">Quản lý bác sĩ</a>
            </div>
          </div>
        </div>
        <a href="#" class="nav-item">Nhân viên</a>
        <a href="/logout" class="nav-item">Đăng xuất</a>
      </div>
      <button class="sell-btn">
        <i class="fas fa-cart-shopping cart-icon"></i> Bán hàng
      </button>
    </nav>

    <!-- Main Content -->
    <div>
      <slot />
    </div>
    
    <!-- Toast for notifications -->
    <Toast />
  </div>
</template>

<script setup>
import Toast from 'primevue/toast'

// Admin layout component - có thể đặt provide/inject, breadcrumb… ở đây
</script>

<style>
/* CSS cho Admin Layout - không import external CSS để tránh lỗi build */

/* Reset global styles để header nằm sát trên cùng */
html, body {
  margin: 0 !important;
  padding: 0 !important;
}

.admin-layout {
  margin: 0;
  padding: 0;
  position: relative;
}

.navbar {
  background: #1db46a;
  padding: 0 24px;
  min-height: 44px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: sticky;
  top: 0;
  z-index: 1000;
  margin: 0;
}

.nav-menu {
  display: flex;
  align-items: center;
  gap: 32px;
}

.nav-item {
  color: #fff;
  text-decoration: none;
  font-weight: 600;
  font-size: 17px;
  padding: 0 8px;
  transition: all 0.2s ease;
  cursor: pointer;
}

/* .nav-item:hover {
  background: #1db46a;
  color: white;
} */

.nav-item.active {
  color: white;
}

.dropdown {
  position: relative;
}

.nav-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  background: white;
  border-radius: 8px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  padding: 16px;
  display: none;
  min-width: 300px;
  z-index: 1000;
  flex-direction: row;
  gap: 24px;
}

.dropdown:hover .nav-dropdown {
  display: flex;
}

.nav-dropdown-short {
  min-width: 200px;
  flex-direction: column;
}

.dropdown-col {
  flex: 1;
  min-width: 120px;
}

.dropdown-col:last-child {
  margin-bottom: 0;
}

.dropdown-title {
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 8px;
  font-size: 14px;
}

.dropdown-link {
  display: block;
  color: #64748b;
  text-decoration: none;
  padding: 6px 0;
  font-size: 14px;
  transition: color 0.2s ease;
}

/* .dropdown-link:hover {
  color: #3b82f6;
} */

.sell-btn {
  background: #10b981;
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s ease;
}

.sell-btn:hover {
  background: #059669;
}

/* Scoped styles cho các component con */
:deep(.nav-item) {
  color: #fff;
  text-decoration: none;
  font-weight: 600;
  font-size: 17px;
  padding: 0 8px;
  transition: all 0.2s ease;
  cursor: pointer;
}

:deep(.nav-item:hover) {
  background: #1db46a;
  color: white;
}

:deep(.nav-item.active) {
  color: white;
}

:deep(.dropdown) {
  position: relative;
}

:deep(.nav-dropdown) {
  position: absolute;
  top: 100%;
  left: 0;
  background: white;
  border-radius: 8px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  padding: 16px;
  display: none;
  min-width: 300px;
  z-index: 1000;
}

:deep(.nav-dropdown-short) {
  min-width: 200px;
}

:deep(.dropdown:hover .nav-dropdown) {
  display: block;
}

:deep(.dropdown-col) {
  margin-bottom: 16px;
}

:deep(.dropdown-col:last-child) {
  margin-bottom: 0;
}

:deep(.dropdown-title) {
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 8px;
  font-size: 14px;
}

:deep(.dropdown-link) {
  display: block;
  color: #64748b;
  text-decoration: none;
  padding: 6px 0;
  font-size: 14px;
  transition: color 0.2s ease;
}

/* :deep(.dropdown-link:hover) {
  color: #09f884;
} */

/* Chỉ override Bootstrap button styles để không xung đột với PrimeVue */
.admin-layout :deep(.p-button-group .p-button.btn) {
  /* Reset Bootstrap button styles */
  background: initial !important;
  border: initial !important;
  color: initial !important;
}
</style>
