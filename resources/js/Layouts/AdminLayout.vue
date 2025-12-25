<template>
    <div class="admin-layout">
        <nav class="navbaradmin">
            <div class="nav-menu">
                <a href="/admin/admindashboard" class="nav-item active">Tổng Quan</a>
                <div class="nav-item dropdown">
                    <span class="nav-item">Hàng hóa</span>
                    <div class="nav-dropdown">
                        <div class="dropdown-col">
                            <div class="dropdown-title">Hàng hóa</div>
                            <a href="/admin/products" class="dropdown-link">Cập nhật thông tin </a>
                            <a href="/admin/products/list" class="dropdown-link">Xem danh sách hàng hóa</a>
                            <a href="/admin/products/stock" class="dropdown-link">Kiểm tra tồn kho</a>
                        </div>
                        <div class="dropdown-col">
                            <div class="dropdown-title">Nhập hàng</div>
                            <a href="/admin/import" class="dropdown-link">Đặt hàng</a>
                            <a href="/admin/purchase-returns" class="dropdown-link">Trả hàng </a>
                        </div>
                    </div>
                </div>
                <div class="nav-item dropdown">
                    <span class="nav-item">Trung tâm dịch vụ</span>
                    <div class="nav-dropdown">
                        <div class="dropdown-col">
                            <div class="dropdown-title">Tổng quan</div>
                            <a href="/admin/orders" class="dropdown-link">Quản lý đơn hàng</a>
                            <a href="/admin/orders/transport" class="dropdown-link">Vận đơn</a>
                        </div>
                        <div class="dropdown-col">
                            <div class="dropdown-title">Dịch vụ </div>
                            <a href="/admin/services-dashboard" class="dropdown-link">Quản lý dịch vụ</a>
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
                    <span class="nav-item">Đối tác</span>
                    <div class="nav-dropdown nav-dropdown-short">
                        <div class="dropdown-col">
                            <a href="/admin/suppliers" class="dropdown-link">Quản lý cung cấp</a>
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
                <div class="nav-item dropdown">
                    <span class="nav-item">Hỗ trợ</span>
                    <div class="nav-dropdown nav-dropdown-short">
                        <div class="dropdown-col">
                            <div class="dropdown-title">Yêu cầu</div>
                            <a href="/admin/tickets" class="dropdown-link">Danh sách yêu cầu</a>
                        </div>
                    </div>
                </div>
                <div class="nav-item dropdown">
                    <span class="nav-item">Nội bộ</span>
                    <div class="nav-dropdown nav-dropdown-short">
                        <div class="dropdown-col">
                            <div class="dropdown-title">Tổng quan</div>
                            <a href="/admin/employees" class="dropdown-link">Quản lý nhân viên</a>
                            <a href="/admin/employee-schedules" class="dropdown-link">Lịch làm việc</a>
                            <a href="/admin/employee-salary" class="dropdown-link">Bảng lương</a>
                        </div>
                    </div>
                </div>
                <div class="nav-item dropdown">
                    <span class="nav-item">Báo cáo</span>
                    <div class="nav-dropdown">
                        <div class="dropdown-col">
                            <a href="/admin/reports/top-stock-imports" class="dropdown-link">Đặt hàng</a>
                            <a href="/admin/reports/top-stock-returns" class="dropdown-link">Trả hàng</a>
                            <a href="/admin/reports/top-products-sell" class="dropdown-link">Hàng hóa</a>
                        </div>
                        <div class="dropdown-col">
                            <a href="/admin/reports/top-customers" class="dropdown-link">Khách hàng</a>
                            <a href="/admin/reports/top-suppliers" class="dropdown-link">Nhà cung cấp</a>
                            <a href="" class="dropdown-link">Nhân viên</a>
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

</script>

<style>
@import '@Admin/AdminLayout.css';
</style>

<style>
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
    background: #696FC7;
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

/* Chỉ override Bootstrap button styles để không xung đột với PrimeVue */
.admin-layout :deep(.p-button-group .p-button.btn) {
    /* Reset Bootstrap button styles */
    background: initial !important;
    border: initial !important;
    color: initial !important;
}
</style>
