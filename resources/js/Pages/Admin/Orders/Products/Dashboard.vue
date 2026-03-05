<script setup>
import { ref, watch } from 'vue';
import { Head, router, Link } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Swal from 'sweetalert2';

// Components
import EditModal from './Modals/Edit_modal.vue';
import DetailsModal from './Modals/Details_modal.vue';

// PROPS
const props = defineProps({
    stats: Object,
    orders: Object,
    filters: Object,
    selectedOrder: Object,
});

// STATE
const form = ref({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
});
const isDetailOpen = ref(false);
const isEditOpen = ref(false);
const editingOrder = ref(null);

const PAGE_URL = '/admin/orders';

// LOGIC
const handleSearch = debounce(() => {
    router.get(PAGE_URL, form.value, { preserveState: true, replace: true });
}, 300);
watch(form, () => handleSearch(), { deep: true });

// Mở Modal Xem
const openDetail = (id) => {
    // Gọi Inertia reload lại trang nhưng chỉ lấy dữ liệu 'selectedOrder'
    router.get('/admin/orders',
        { ...form.value, order_id: id }, // Truyền ID lên URL
        {
            preserveState: true,   // Giữ nguyên bộ lọc/search hiện tại
            preserveScroll: true,  // Không bị cuộn trang lên đầu
            only: ['selectedOrder'],
            onSuccess: () => {
                // Khi server trả về dữ liệu thành công => Mở Modal
                isDetailOpen.value = true;
            }
        }
    );
};
const closeDetail = () => {
    isDetailOpen.value = false;
    // Xóa order_id trên URL để sạch sẽ
    router.get('/admin/orders', { ...form.value }, { preserveState: true, replace: true });
};

// Mở Modal Sửa
const openEdit = (order) => {
    editingOrder.value = order;
    isEditOpen.value = true;
};

const deleteOrder = (id) => {
    Swal.fire({
        title: 'Bạn có chắc chắn?',
        text: "Hành động này không thể hoàn tác!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Xóa đơn hàng',
        cancelButtonText: 'Hủy bỏ',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(`${PAGE_URL}/${id}`, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: 'Đã xóa!',
                        text: 'Đơn hàng đã được xóa thành công.',
                        icon: 'success',
                        confirmButtonColor: '#3b82f6'
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: 'Lỗi!',
                        text: 'Không thể xóa đơn hàng. Vui lòng thử lại.',
                        icon: 'error',
                        confirmButtonColor: '#3b82f6'
                    });
                }
            });
        }
    });
};

// In hóa đơn
const printInvoice = (id) => {
    window.open(`${PAGE_URL}/${id}/invoice`, '_blank');
};

// Helper UI: Status Badge Color
const getStatusClass = (status) => {
    const map = {
        'pending': 'status-pending',
        'confirmed': 'status-confirmed',
        'delivering': 'status-delivering',
        'completed': 'status-completed',
        'cancelled': 'status-cancelled',
    };
    return map[status] || 'status-default';
};

const getStatusLabel = (status) => {
    const map = {
        'new': 'Đơn hàng mới',
        'pending': 'Chờ xử lý',
        'confirmed': 'Đã xác nhận',
        'delivering': 'Đang giao',
        'completed': 'Hoàn thành',
        'cancelled': 'Đã hủy',
    };
    return map[status] || status;
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('vi-VN', {
        day: '2-digit', month: '2-digit', year: 'numeric'
    });
};
</script>

<template>

    <Head title="Quản lý đơn hàng" />

    <div class="dashboard-wrapper">
        <div class="top-header">
            <div class="header-titles">
                <h1 class="main-title">Quản Lý Đơn Hàng</h1>
                <p class="sub-title">Theo dõi và quản lý tất cả đơn hàng của bạn</p>
            </div>

            <div class="header-actions">
                <div class="search-input-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input v-model="form.search" type="text" placeholder="Tìm kiếm đơn hàng..." class="custom-input" />
                </div>

                <div class="filter-wrapper" style="margin-left: 80px;">
                    <select v-model="form.status" class="custom-select">
                        <option value="">Lọc theo trạng thái</option>
                        <option value="pending">Chờ xử lý</option>
                        <option value="confirmed">Đã xác nhận</option>
                        <option value="delivering">Đang giao hàng</option>
                        <option value="completed">Hoàn thành</option>
                        <option value="cancelled">Đã hủy</option>
                    </select>
                    <i class="fas fa-filter filter-icon"></i>
                </div>
            </div>
        </div>

        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-content">
                    <span class="stat-title">Tổng Đơn Hàng</span>
                    <h2 class="stat-number">{{ stats?.total || 0 }}</h2>
                    <p class="stat-trend up">
                        <i class="fas fa-arrow-up"></i> 12% so với tháng trước
                    </p>
                </div>
                <div class="stat-icon-box blue">
                    <i class="fas fa-box"></i>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-content">
                    <span class="stat-title">Doanh Thu</span>
                    <h2 class="stat-number">{{ stats?.total_amount || 0 }}</h2>
                    <p class="stat-trend up">
                        <i class="fas fa-arrow-up"></i> 8% so với tháng trước
                    </p>
                </div>
                <div class="stat-icon-box green">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-content">
                    <span class="stat-title">Đơn Chờ Xử Lý</span>
                    <h2 class="stat-number">{{ stats?.pending || 0 }}</h2>
                </div>
                <div class="stat-icon-box orange">
                    <i class="fas fa-clock"></i>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-content">
                    <span class="stat-title">Đơn Hoàn Thành</span>
                    <h2 class="stat-number">{{ stats?.completed || 0 }}</h2>
                    <p class="stat-trend up">
                        <i class="fas fa-arrow-up"></i> 15% so với tháng trước
                    </p>
                </div>
                <div class="stat-icon-box purple">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="table-section">
            <div class="table-header-row">
                <h3 class="table-title">Danh Sách Đơn Hàng</h3>
                <span class="table-counter">Hiển thị {{ orders.data.length }} đơn hàng</span>
            </div>

            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th class="col-id">Mã Đơn</th>
                            <th class="col-customer">Khách Hàng</th>
                            <th class="col-amount">Tổng Tiền</th>
                            <th class="col-status">Trạng Thái</th>
                            <th class="col-date">Ngày Đặt</th>
                            <th class="col-action text-right">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="order in orders.data" :key="order.id">
                            <td class="col-id">
                                <a href="#" @click.prevent="openDetail(order.id)" class="order-link">
                                    {{ order.order_code }}
                                </a>
                            </td>

                            <td class="col-customer">
                                <div class="customer-cell">
                                    <span class="customer-name">{{ order.customer_name }}</span>
                                    <span class="customer-sub">{{ order.customer_phone }}</span>
                                </div>
                            </td>

                            <td class="col-amount">
                                <span class="font-bold text-gray-800">{{ formatCurrency(order.total_amount)
                                }}</span>
                            </td>

                            <td class="col-status">
                                <span :class="['status-pill', getStatusClass(order.order_status)]">
                                    <span class="dot">●</span> {{ getStatusLabel(order.order_status) }}
                                </span>
                            </td>

                            <td class="col-date text-gray-500">
                                {{ formatDate(order.created_at) }}
                            </td>

                            <td class="col-action text-right">
                                <div class="action-buttons">
                                    <button @click="openDetail(order.id)" class="btn-icon" title="Xem">
                                        <i class="far fa-eye"></i>
                                    </button>
                                    <button @click="openEdit(order)" class="btn-icon" title="Sửa">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button @click="deleteOrder(order.id)" class="btn-icon delete" title="Xóa">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="orders.data.length === 0">
                            <td colspan="6" class="text-center py-8 text-gray-500">
                                Không tìm thấy đơn hàng nào.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper" v-if="orders.links.length > 3">
                <div class="pagination">
                    <template v-for="(link, k) in orders.links" :key="k">
                        <Link v-if="link.url" :href="link.url" :class="['page-link', { 'active': link.active }]">
                            <span v-html="link.label"></span>
                        </Link>
                        <span v-else v-html="link.label" class="page-link disabled"></span>
                    </template>
                </div>
            </div>
        </div>

    </div>

    <DetailsModal v-if="isDetailOpen && selectedOrder" :show="isDetailOpen" :order="selectedOrder"
        @close="closeDetail" />
    <EditModal v-if="isEditOpen" :show="isEditOpen" :order="editingOrder" @close="isEditOpen = false" />
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.dashboard-wrapper {
    padding: 24px 40px;
    background-color: #f8fafc;
    min-height: 100vh;
    font-family: 'Inter', sans-serif;
    color: #334155;
}

/* --- HEADER --- */
.top-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 32px;
}

.main-title {
    font-size: 24px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 4px;
}

.sub-title {
    color: #64748b;
    font-size: 14px;
}

.header-actions {
    display: flex;
    gap: 12px;
    align-items: center;
}

/* Search Input Styled like Image */
.search-input-wrapper {
    position: relative;
    width: 280px;
}

.search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 14px;
}

.custom-input {
    width: 150%;
    padding: 10px 12px 10px 36px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background-color: #fff;
    font-size: 14px;
    outline: none;
    transition: border-color 0.2s;
}

.custom-input:focus {
    border-color: #3b82f6;
}

.custom-select {
    appearance: none;
    padding: 10px 36px 10px 12px;
    /* Space for icon */
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background-color: #fff;
    font-size: 14px;
    color: #334155;
    cursor: pointer;
    min-width: 50px;
    /* Adjust if hiding text */
    outline: none;
}

.filter-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
    pointer-events: none;
    font-size: 14px;
}

/* Primary Button */
.btn-add-order {
    background-color: #3b82f6;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 500;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    transition: background-color 0.2s;
    white-space: nowrap;
}

.btn-add-order:hover {
    background-color: #2563eb;
}

/* --- STATS CARDS --- */
.stats-container {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
    margin-bottom: 40px;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
}

.stat-content {
    display: flex;
    flex-direction: column;
}

.stat-title {
    color: #64748b;
    font-size: 13px;
    font-weight: 500;
    margin-bottom: 8px;
}

.stat-number {
    font-size: 28px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 8px;
    line-height: 1;
}

.stat-trend {
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.stat-trend.up {
    color: #16a34a;
    /* Green */
}

.stat-icon-box {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.stat-icon-box.blue {
    background-color: #3b82f6;
    color: white;
}

.stat-icon-box.green {
    background-color: #22c55e;
    color: white;
}

.stat-icon-box.orange {
    background-color: #f59e0b;
    color: white;
}

.stat-icon-box.purple {
    background-color: #a855f7;
    color: white;
}

.table-header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.table-title {
    font-size: 18px;
    font-weight: 600;
    color: #0f172a;
}

.table-counter {
    font-size: 13px;
    color: #94a3b8;
}

.table-responsive {
    background: white;
    border-radius: 12px;
    border: 1px solid #f1f5f9;
    overflow: hidden;
}

.custom-table {
    width: 100%;
    border-collapse: collapse;
}

.custom-table th {
    background-color: #f8fafc;
    text-align: left;
    padding: 16px 24px;
    font-size: 13px;
    font-weight: 600;
    color: #334155;
    border-bottom: 1px solid #e2e8f0;
}

.custom-table td {
    padding: 16px 24px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.custom-table tr:last-child td {
    border-bottom: none;
}

.custom-table tr:hover {
    background-color: #f8fafc;
}

/* Column Styles */
.order-link {
    color: #3b82f6;
    font-weight: 600;
    text-decoration: none;
}

.order-link:hover {
    text-decoration: underline;
}

.customer-cell {
    display: flex;
    flex-direction: column;
}

.customer-name {
    font-weight: 600;
    color: #1e293b;
    font-size: 14px;
}

.customer-sub {
    font-size: 12px;
    color: #64748b;
    margin-top: 2px;
}

/* Status Pills */
.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
}

.dot {
    font-size: 8px;
}

/* Status Colors Mapping */
.status-completed {
    background-color: #dcfce7;
    color: #16a34a;
}

/* Green */
.status-confirmed {
    background-color: #dbeafe;
    color: #2563eb;
}

/* Blue */
.status-pending {
    background-color: #fef3c7;
    color: #d97706;
}

/* Yellow/Orange */
.status-cancelled {
    background-color: #fee2e2;
    color: #dc2626;
}

/* Red */
.status-delivering {
    background-color: #e0e7ff;
    color: #4f46e5;
}

/* Indigo */
.status-default {
    background-color: #f1f5f9;
    color: #64748b;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

.btn-icon {
    background: none;
    border: none;
    cursor: pointer;
    color: #94a3b8;
    font-size: 16px;
    transition: color 0.2s;
}

.btn-icon:hover {
    color: #3b82f6;
}

.btn-icon.delete:hover {
    color: #ef4444;
}

/* Utilities */
.text-right {
    text-align: right;
}

.font-bold {
    font-weight: 600;
}

.text-gray-800 {
    color: #1e293b;
}

.text-gray-500 {
    color: #64748b;
    font-size: 13px;
}

/* Pagination Styles (Minimal update to fit theme) */
.pagination-wrapper {
    padding: 16px 24px;
    display: flex;
    justify-content: flex-end;
    border-top: 1px solid #f1f5f9;
    background: white;
}

.pagination {
    display: flex;
    gap: 4px;
}

.page-link {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 13px;
    color: #64748b;
    border: 1px solid #e2e8f0;
    text-decoration: none;
}

.page-link.active {
    background-color: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

/* Responsive */
@media (max-width: 1024px) {
    .stats-container {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .dashboard-wrapper {
        padding: 16px;
    }

    .top-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }

    .header-actions {
        width: 100%;
        flex-wrap: wrap;
    }

    .search-input-wrapper {
        flex: 1;
        width: auto;
    }

    .stats-container {
        grid-template-columns: 1fr;
    }

    .table-responsive {
        overflow-x: auto;
    }
}
</style>