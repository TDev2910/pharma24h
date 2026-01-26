<script setup>
import { ref, watch } from 'vue';
import { Head, router, Link } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';

// Components
import EditModal from './modals/EditModalTest.vue';
import DetailsModal from './modals/DetailsModalTest.vue';

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

// URL Cứng
const PAGE_URL = '/admin/orders-test';

// LOGIC
const handleSearch = debounce(() => {
    router.get(PAGE_URL, form.value, { preserveState: true, replace: true });
}, 300);
watch(form, () => handleSearch(), { deep: true });

// Mở Modal Xem
const openDetail = (id) => {
    // Gọi Inertia reload lại trang nhưng chỉ lấy dữ liệu 'selectedOrder'
    router.get('/admin/orders-test',
        { ...form.value, order_id: id }, // Truyền ID lên URL
        {
            preserveState: true,   // Giữ nguyên bộ lọc/search hiện tại
            preserveScroll: true,  // Không bị cuộn trang lên đầu
            only: ['selectedOrder'], // Quan trọng: Chỉ tải dữ liệu selectedOrder (Lazy Load)
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
    router.get('/admin/orders-test', { ...form.value }, { preserveState: true, replace: true });
};

// Mở Modal Sửa
const openEdit = (order) => {
    editingOrder.value = order;
    isEditOpen.value = true;
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
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });
};
</script>

<template>
    <AdminLayout>

        <Head title="Quản lý đơn hàng" />

        <div class="dashboard-container">
            <div class="dashboard-header">
                <div>
                    <h1 class="page-title">Quản Lý Đơn Hàng</h1>
                    <p class="page-subtitle">Theo dõi và xử lý đơn hàng hiệu quả</p>
                </div>
                <button class="btn-primary">
                    <i class="fas fa-file-export mr-2"></i> Xuất Báo Cáo
                </button>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon bg-blue-100 text-blue-600">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="stat-details">
                        <span class="stat-label">Tổng đơn hàng</span>
                        <h3 class="stat-value">{{ stats?.total || 0 }}</h3>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon bg-amber-100 text-amber-600">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-details">
                        <span class="stat-label">Chờ xử lý</span>
                        <h3 class="stat-value">{{ stats?.pending || 0 }}</h3>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon bg-emerald-100 text-emerald-600">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-details">
                        <span class="stat-label">Hoàn thành</span>
                        <h3 class="stat-value">{{ stats?.completed || 0 }}</h3>
                    </div>
                </div>
            </div>

            <div class="content-panel">
                <div class="toolbar">
                    <div class="search-box">
                        <i class="fas fa-search search-icon"></i>
                        <input v-model="form.search" type="text" placeholder="Tìm kiếm mã đơn, khách hàng..."
                            class="search-input" />
                    </div>

                    <div class="filter-wrapper">
                        <select v-model="form.status" class="filter-select">
                            <option value="">Tất cả trạng thái</option>
                            <option value="pending">Chờ xử lý</option>
                            <option value="confirmed">Đã xác nhận</option>
                            <option value="delivering">Đang giao hàng</option>
                            <option value="completed">Hoàn thành</option>
                            <option value="cancelled">Đã hủy</option>
                        </select>
                        <i class="fas fa-chevron-down select-arrow"></i>
                    </div>
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="w-1/6">Mã Đơn</th>
                                <th class="w-1/4">Khách Hàng</th>
                                <th class="w-1/6 text-right">Tổng Tiền</th>
                                <th class="w-1/6 text-center">Trạng Thái</th>
                                <th class="w-1/6 text-right">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="order in orders.data" :key="order.id">
                                <td>
                                    <div class="order-code-wrapper" @click="openDetail(order.id)">
                                        <span class="order-code">{{ order.order_code }}</span>
                                        <span class="order-date">{{ formatDate(order.created_at) }}</span>
                                    </div>
                                </td>

                                <td>
                                    <div class="customer-info">
                                        <span class="customer-name">{{ order.customer_name }}</span>
                                        <div class="customer-contact">
                                            <i class="fas fa-phone-alt text-xs"></i>
                                            <span>{{ order.customer_phone }}</span>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-right">
                                    <div class="amount-info">
                                        <span class="amount-value">{{ formatCurrency(order.total_amount) }}</span>
                                        <span
                                            :class="['payment-status', order.payment_status === 'paid' ? 'paid' : 'unpaid']">
                                            {{ order.payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                                        </span>
                                    </div>
                                </td>

                                <td class="text-center">
                                    <span :class="['status-badge', getStatusClass(order.order_status)]">
                                        {{ getStatusLabel(order.order_status) }}
                                    </span>
                                </td>

                                <td>
                                    <div class="actions-group">
                                        <button @click="openDetail(order.id)" class="action-btn view"
                                            title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button @click="printInvoice(order.id)" class="action-btn print"
                                            title="In hóa đơn">
                                            <i class="fas fa-print"></i>
                                        </button>
                                        <button @click="openEdit(order)" class="action-btn edit" title="Cập nhật">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="orders.data.length === 0">
                                <td colspan="5" class="empty-state">
                                    <div class="empty-content">
                                        <i class="fas fa-box-open empty-icon"></i>
                                        <h3>Không tìm thấy dữ liệu</h3>
                                        <p>Vui lòng thử lại với từ khóa hoặc bộ lọc khác</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="pagination-container" v-if="orders.links.length > 3">
                    <div class="pagination">
                        <template v-for="(link, k) in orders.links" :key="k">
                            <Link v-if="link.url" :href="link.url" v-html="link.label"
                                :class="['page-link', { 'active': link.active }]" />
                            <span v-else v-html="link.label" class="page-link disabled"></span>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <DetailsModal v-if="isDetailOpen && selectedOrder" :show="isDetailOpen" :order="selectedOrder"
            @close="closeDetail" />
        <EditModal v-if="isEditOpen" :show="isEditOpen" :order="editingOrder" @close="isEditOpen = false" />
    </AdminLayout>
</template>

<style scoped>
/* ===== VARIABLES ===== */
:root {
    --primary-color: #3b82f6;
    --primary-hover: #2563eb;
    --text-primary: #1e293b;
    --text-secondary: #64748b;
    --bg-page: #f8fafc;
    --bg-card: #ffffff;
    --border-color: #e2e8f0;
}

/* ===== LAYOUT ===== */
.dashboard-container {
    padding: 24px;
    background-color: var(--bg-page);
    min-height: 100vh;
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    color: var(--text-primary);
}

/* ===== HEADER ===== */
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
}

.page-title {
    font-size: 24px;
    font-weight: 700;
    margin: 0;
    color: var(--text-primary);
    letter-spacing: -0.025em;
}

.page-subtitle {
    font-size: 14px;
    color: var(--text-secondary);
    margin-top: 4px;
}

.btn-primary {
    background-color: var(--primary-color);
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2);
}

.btn-primary:hover {
    background-color: var(--primary-hover);
    transform: translateY(-1px);
    box-shadow: 0 6px 8px -1px rgba(59, 130, 246, 0.3);
}

/* ===== STATS GRID ===== */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.stat-card {
    background: var(--bg-card);
    padding: 24px;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    gap: 20px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
}

.stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}

.stat-details {
    display: flex;
    flex-direction: column;
}

.stat-label {
    font-size: 13px;
    color: var(--text-secondary);
    font-weight: 500;
    margin-bottom: 4px;
}

.stat-value {
    font-size: 28px;
    font-weight: 700;
    line-height: 1;
    color: var(--text-primary);
    margin: 0;
}

/* ===== CONTENT PANEL ===== */
.content-panel {
    background: var(--bg-card);
    border-radius: 12px;
    border: 1px solid var(--border-color);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

/* ===== TOOLBAR ===== */
.toolbar {
    padding: 20px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    background-color: #ffffff;
}

.search-box {
    position: relative;
    flex: 1;
    min-width: 250px;
}

.search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    pointer-events: none;
}

.search-input {
    width: 100%;
    padding: 10px 14px 10px 40px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s;
    background-color: #f8fafc;
}

.search-input:focus {
    outline: none;
    border-color: var(--primary-color);
    background-color: #ffffff;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.filter-wrapper {
    position: relative;
    min-width: 200px;
}

.filter-select {
    width: 100%;
    padding: 10px 36px 10px 14px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    font-size: 14px;
    appearance: none;
    background-color: #f8fafc;
    cursor: pointer;
    transition: all 0.2s;
    color: var(--text-primary);
}

.filter-select:focus {
    outline: none;
    border-color: var(--primary-color);
    background-color: #ffffff;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.select-arrow {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    pointer-events: none;
    font-size: 12px;
}

/* ===== DATA TABLE ===== */
.table-container {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    padding: 16px 20px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    color: var(--text-secondary);
    background-color: #f8fafc;
    border-bottom: 1px solid var(--border-color);
    letter-spacing: 0.05em;
}

.data-table td {
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    color: var(--text-primary);
    transition: background-color 0.15s;
}

.data-table tr:hover td {
    background-color: #f8fafc;
}

/* Column Specific Styles */
.order-code-wrapper {
    cursor: pointer;
}

.order-code {
    display: inline-block;
    font-family: 'Monaco', monospace;
    font-weight: 600;
    color: var(--primary-color);
    background-color: #eff6ff;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 13px;
    margin-bottom: 4px;
    transition: all 0.2s;
}

.order-code:hover {
    background-color: #dbeafe;
    text-decoration: underline;
}

.order-date {
    display: block;
    font-size: 12px;
    color: #94a3b8;
}

.customer-name {
    display: block;
    font-weight: 500;
    margin-bottom: 2px;
}

.customer-contact {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--text-secondary);
}

.amount-value {
    display: block;
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 2px;
}

.payment-status {
    font-size: 11px;
    font-weight: 500;
}

.payment-status.paid {
    color: #10b981;
}

.payment-status.unpaid {
    color: #f59e0b;
}

/* Status Badges */
.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    white-space: nowrap;
}

.status-pending {
    background-color: #fff7ed;
    color: #c2410c;
    border: 1px solid #ffedd5;
}

.status-confirmed {
    background-color: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #dbeafe;
}

.status-delivering {
    background-color: #f5f3ff;
    color: #7c3aed;
    border: 1px solid #ede9fe;
}

.status-completed {
    background-color: #f0fdf4;
    color: #15803d;
    border: 1px solid #dcfce7;
}

.status-cancelled {
    background-color: #fef2f2;
    color: #b91c1c;
    border: 1px solid #fee2e2;
}

.status-default {
    background-color: #f1f5f9;
    color: #475569;
}

/* Action Buttons */
.actions-group {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
}

.action-btn {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    border: 1px solid transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    background-color: transparent;
    color: #64748b;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.action-btn.view:hover {
    background-color: #eff6ff;
    color: #2563eb;
    border-color: #dbeafe;
}

.action-btn.print:hover {
    background-color: #f3e8ff;
    color: #9333ea;
    border-color: #e9d5ff;
}

.action-btn.edit:hover {
    background-color: #fff7ed;
    color: #d97706;
    border-color: #ffedd5;
}

/* Empty State */
.empty-state {
    padding: 64px 24px;
    text-align: center;
}

.empty-content {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.empty-icon {
    font-size: 48px;
    color: #cbd5e1;
    margin-bottom: 16px;
}

.empty-content h3 {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 8px 0;
}

.empty-content p {
    color: var(--text-secondary);
    font-size: 14px;
    margin: 0;
}

/* ===== PAGINATION ===== */
.pagination-container {
    padding: 16px 20px;
    border-top: 1px solid var(--border-color);
    background-color: #ffffff;
    display: flex;
    justify-content: flex-end;
}

.pagination {
    display: flex;
    gap: 6px;
}

.page-link {
    min-width: 32px;
    height: 32px;
    padding: 0 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    font-size: 13px;
    color: var(--text-secondary);
    background-color: transparent;
    border: 1px solid var(--border-color);
    transition: all 0.2s;
    text-decoration: none;
}

.page-link:hover:not(.disabled) {
    background-color: #f1f5f9;
    color: var(--text-primary);
    border-color: #cbd5e1;
}

.page-link.active {
    background-color: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
    font-weight: 600;
}

.page-link.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background-color: #f8fafc;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .dashboard-container {
        padding: 16px;
    }

    .dashboard-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }

    .btn-primary {
        width: 100%;
        justify-content: center;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .toolbar {
        flex-direction: column;
    }

    .search-box,
    .filter-wrapper {
        min-width: 100%;
    }
}
</style>
