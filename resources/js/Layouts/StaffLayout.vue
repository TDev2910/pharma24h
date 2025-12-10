<template>
  <div class="admin-layout">
    <!-- Admin Navigation - Giữ nguyên cấu trúc navbar phức tạp -->
    <nav class="navbarstaff">
      <div class="nav-menu">
        <a href="/staff/dashboard" class="nav-item active">Tổng Quan</a>
        <div class="nav-item dropdown">
          <span class="nav-item">Hàng hóa</span>
          <div class="nav-dropdown">
            <div class="dropdown-col">
              <div class="dropdown-title">Kho hàng</div>
              <a href="/staff/products/stock" class="dropdown-link">Kiểm kho sản phẩm</a>
            </div>
          </div>
        </div>
        <div class="nav-item dropdown">
          <span class="nav-item">Trung tâm dịch vụ</span>
          <div class="nav-dropdown nav-dropdown-short">
            <div class="dropdown-col">
              <div class="dropdown-title">Tổng quan</div>
              <a href="/staff/orders" class="dropdown-link">Quản lý đơn hàng</a>
              <a href="/staff/service-bookings" class="dropdown-link">Quản lý dịch vụ</a>
            </div>
          </div>
        </div>
        <div class="nav-item dropdown">
          <span class="nav-item">Khách hàng</span>
          <div class="nav-dropdown nav-dropdown-short">
            <div class="dropdown-col">
              <div class="dropdown-title">Tổng quan</div>
              <a href="/staff/customers" class="dropdown-link">Quản lý khách hàng</a>
            </div>
          </div>
        </div>
        <div class="nav-item dropdown">
          <span class="nav-item">Lịch làm việc</span>
          <div class="nav-dropdown nav-dropdown-short">
            <div class="dropdown-col">
              <a href="/staff/my-schedule" class="dropdown-link">Xem lịch làm việc</a>
            </div>
          </div>
        </div>

        <a href="/logout" class="nav-item">Đăng xuất</a>
      </div>
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
  /* Reset global styles */
  html, body {
    margin: 0 !important;
    padding: 0 !important;
  }
  
  .admin-layout {
    position: relative;
    min-height: 100vh;
    background-color: #f8fafc;
  }
  
  /* --- NAVBAR STYLES --- */
  .navbarstaff {
    background: #5459AC;
    padding: 0 24px;
    height: 56px; /* Chiều cao cố định để căn giữa chuẩn xác */
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  }
  
  .nav-menu {
    display: flex;
    align-items: center;
    gap: 0; /* Loại bỏ khoảng cách thừa, dùng padding của item */
    height: 100%; /* Quan trọng: Menu cao bằng Navbar */
  }
  
  .nav-item {
    color: rgba(255, 255, 255, 0.9); /* Màu chữ trắng hơi mờ nhẹ cho dịu mắt */
    text-decoration: none;
    font-weight: 500;
    font-size: 16px;
    padding: 0 20px; /* Padding rộng để dễ di chuột */
    height: 100%;
    display: flex;
    align-items: center;
    position: relative;
    transition: all 0.2s ease;
    
  }
  

  /* Style riêng cho nút đang Active (Trang hiện tại) */
  .nav-item.active {
    color: #fff;
    font-weight: 600;
    border-bottom-color: rgba(255, 255, 255, 0.5); /* Gạch chân mờ hơn chút để phân biệt hover */
  }
  
  /* --- DROPDOWN CORE (Giữ nguyên kỹ thuật Safe Bridge) --- */
  .dropdown {
    position: relative;
    height: 100%;
    display: flex;
    align-items: center;
  }
  
  .nav-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    background: white;
    border-radius: 0 0 4px 4px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    padding: 12px;
    display: none;
    min-width: 220px;
    z-index: 1000;
    
    /* Animation nhẹ */
    opacity: 0;
    transform: translateY(10px);
    transition: opacity 0.2s ease, transform 0.2s ease;
  }
  
  /* Hiển thị Dropdown */
  .dropdown:hover .nav-dropdown {
    display: block;
    opacity: 1;
    transform: translateY(0);
  }
  
  /* --- SAFE BRIDGE: Cầu nối vô hình --- */
  /* Giúp không bị mất menu khi di chuột từ Navbar xuống Dropdown */
  .dropdown:hover .nav-dropdown::before {
    content: "";
    position: absolute;
    top: -15px; /* Phủ lên khoảng cách giữa menu và navbar */
    left: 0;
    width: 100%;
    height: 15px;
    background: transparent;
  }
  
  /* --- DROPDOWN CONTENT --- */
  .dropdown-col {
    min-width: 150px;
  }
  
  .dropdown-title {
    font-weight: 600;
    color: #64748b;
    margin-bottom: 10px;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 4px;
  }
  
  .dropdown-link {
    display: block;
    color: #334155;
    text-decoration: none;
    padding: 8px 10px;
    font-size: 14px;
    border-radius: 4px;
    transition: all 0.2s;
  }
  
  .dropdown-link:hover {
    background: #f8fafc;
    color: #5459AC;
    padding-left: 14px; /* Hiệu ứng đẩy chữ nhẹ sang phải */
  }
  </style>