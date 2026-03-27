<script setup>
import { ref, watch } from 'vue';
import { Head, router, Link } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Swal from 'sweetalert2';

// Components
import EditModal from './modals/edit_modal.vue';
import DetailsModal from './modals/details_modal.vue';

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
    router.get('/admin/orders',
        { ...form.value, order_id: id },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['selectedOrder'],
            onSuccess: () => {
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
        'cancellation_requested': 'status-cancellation-requested',
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
        'cancellation_requested': 'Yêu cầu hủy',
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
            <div class="header-titles" style="margin-bottom: -20px;">
                <h1 class="main-title">Quản Lý Đơn Hàng</h1>
                <p class="sub-title">Theo dõi và quản lý tất cả đơn hàng của bạn</p>
            </div>

            <div class="header-actions">
                <div class="search-input-wrapper">
                    <i class="fas fa-search search-icon" style="margin-left: 140px;"></i>
                    <input v-model="form.search" type="text" placeholder="Tìm kiếm đơn hàng..." class="custom-input"
                        style="margin-left: 80px;" />
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
                            <th class="col-action text-center">Hành Động</th>
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
                                    {{ getStatusLabel(order.order_status) }}
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
@import '@/../../resources/css/Admin/orders/dashboard.css';
</style>