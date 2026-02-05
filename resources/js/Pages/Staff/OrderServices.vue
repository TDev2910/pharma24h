<template>
    <div class="services-page">
        <div class="header-control-bar">
            <div class="controls-section">
                <div class="title-section">
                    <h4>Quản lý đặt lịch</h4>
                    <span class="subtitle">Danh sách yêu cầu từ khách hàng</span>
                </div>

                <div class="search-filter-container">
                    <div class="search-wrapper">
                        <div class="input-group">
                            <span class="input-group-text"><i class="pi pi-search"></i></span>
                            <input type="text" class="form-control search-input" placeholder="Tên, SĐT khách..."
                                v-model="searchQuery" @input="debounceSearch">
                        </div>
                    </div>
                    <Button icon="pi pi-filter" @click="showFilterModal"
                        class="btn-filter p-button-outlined p-button-secondary" />
                </div>

                <div class="utility-options">
                    <div class="utility-icons">
                        <button class="btn-icon" title="Xuất Excel"><i class="pi pi-file-excel"></i></button>
                        <button class="btn-icon" title="Trợ giúp"><i class="pi pi-question-circle"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-area">
            <div class="table-container">
                <DataTable :value="bookings" :lazy="true" @page="onPage" v-model:expandedRows="expandedRows"
                    dataKey="id" class="mobile-responsive-table" :paginator="true" :rows="pagination.per_page"
                    :totalRecords="pagination.total" :rowsPerPageOptions="[5, 10, 25]"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                    currentPageReportTemplate="{first}-{last} / {totalRecords}" loadingIcon="pi pi-spinner"
                    emptyMessage="Không có dữ liệu đặt lịch">

                    <Column field="customer_name" header="Khách hàng" class="col-customer">
                        <template #body="slotProps">
                            <div class="customer-info">
                                <div>
                                    <span class="fw-bold text-primary d-block">{{ slotProps.data.customer_name }}</span>
                                    <small class="text-muted"><i class="pi pi-phone me-1" style="font-size:10px"></i>{{
                                        slotProps.data.customer_phone }}</small>
                                </div>
                                <span class="status-badge d-md-none" :class="getStatusClass(slotProps.data.status)">
                                    {{ getStatusText(slotProps.data.status) }}
                                </span>
                            </div>
                        </template>
                    </Column>

                    <Column field="service_name" header="Dịch vụ">
                        <template #body="slotProps">
                            <span class="mobile-label">Dịch vụ:</span>
                            <span class="service-name fw-bold">{{ slotProps.data.service?.ten_dich_vu || '-' }}</span>
                        </template>
                    </Column>

                    <Column field="booking_date" header="Thời gian">
                        <template #body="slotProps">
                            <span class="mobile-label">Thời gian:</span>
                            <div class="text-end text-md-start">
                                <div>{{ formatDate(slotProps.data.booking_date) }}</div>
                                <small class="text-muted">{{ slotProps.data.booking_time }}</small>
                            </div>
                        </template>
                    </Column>

                    <Column field="price" header="Giá">
                        <template #body="slotProps">
                            <span class="mobile-label">Giá tiền:</span>
                            <span class="fw-bold text-success">{{ formatCurrency(slotProps.data.price) }}</span>
                        </template>
                    </Column>

                    <Column field="status" header="Trạng thái" class="d-none d-md-table-cell">
                        <template #body="slotProps">
                            <span :class="['status-badge', getStatusClass(slotProps.data.status)]">
                                {{ getStatusText(slotProps.data.status) }}
                            </span>
                        </template>
                    </Column>

                    <Column field="payment_status" header="Thanh toán">
                        <template #body="slotProps">
                            <span class="mobile-label">Thanh toán:</span>
                            <span :class="['status-badge', getPaymentStatusClass(slotProps.data.payment_status)]">
                                {{ getPaymentStatusText(slotProps.data.payment_status) }}
                            </span>
                        </template>
                    </Column>


                    <Column expander style="width: 3.5rem" class="col-expander" />

                    <template #expansion="slotProps">
                        <div class="booking-detail-container">
                            <div class="detail-tabs">
                                <button class="tab-btn" :class="{ active: activeTab === 'info' }"
                                    @click="switchTab('info')">
                                    <i class="pi pi-info-circle me-1"></i> Thông tin
                                </button>
                                <button class="tab-btn" :class="{ active: activeTab === 'actions' }"
                                    @click="switchTab('actions')">
                                    <i class="pi pi-cog me-1"></i> Xử lý
                                </button>
                            </div>

                            <div class="detail-content">
                                <div v-if="activeTab === 'info'" class="tab-pane fade-in">
                                    <div class="info-grid">
                                        <div class="info-card">
                                            <h6 class="info-header"><i class="pi pi-user me-2"></i>Khách hàng</h6>
                                            <ul class="info-list">
                                                <li><span>Họ tên:</span> <strong>{{ slotProps.data.customer_name
                                                }}</strong></li>
                                                <li><span>SĐT:</span> <strong>{{ slotProps.data.customer_phone
                                                }}</strong></li>
                                                <li><span>Email:</span> <span>{{ slotProps.data.customer_email || '-'
                                                }}</span></li>
                                            </ul>
                                        </div>

                                        <div class="info-card">
                                            <h6 class="info-header"><i class="pi pi-calendar me-2"></i>Chi tiết dịch vụ
                                            </h6>
                                            <ul class="info-list">
                                                <li><span>Dịch vụ:</span> <strong>{{ slotProps.data.service?.ten_dich_vu
                                                }}</strong></li>
                                                <li><span>Ngày đặt:</span> <span>{{
                                                    formatDate(slotProps.data.booking_date) }} - {{
                                                            slotProps.data.booking_time }}</span></li>
                                                <li><span>Ghi chú:</span> <span class="text-italic">{{
                                                    slotProps.data.notes || 'Không có'
                                                        }}</span></li>
                                                <li><span>Tổng tiền:</span> <strong class="text-success">{{
                                                    formatCurrency(slotProps.data.price)
                                                        }}</strong></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="activeTab === 'actions'" class="tab-pane fade-in">
                                    <div class="actions-wrapper">
                                        <h6 class="info-header mb-3">Thao tác xử lý đơn hàng</h6>
                                        <div class="action-buttons-grid">
                                            <Button v-if="slotProps.data.status === 'pending'" label="Xác nhận lịch"
                                                icon="pi pi-check" class="p-button-success"
                                                @click="confirmBooking(slotProps.data)" />

                                            <Button
                                                v-if="slotProps.data.status === 'confirmed' && slotProps.data.payment_status === 'unpaid'"
                                                label="Đã thu tiền" icon="pi pi-wallet" class="p-button-warning"
                                                @click="markAsPaid(slotProps.data)" />

                                            <Button
                                                v-if="slotProps.data.status === 'confirmed' && slotProps.data.payment_status === 'paid'"
                                                label="Hoàn thành dịch vụ" icon="pi pi-check-circle"
                                                class="p-button-success" @click="completeBooking(slotProps.data)" />

                                            <Button v-if="['pending', 'confirmed'].includes(slotProps.data.status)"
                                                label="Hủy lịch" icon="pi pi-times"
                                                class="p-button-danger p-button-outlined"
                                                @click="cancelBooking(slotProps.data)" />

                                            <Button label="Xem chi tiết" icon="pi pi-eye"
                                                class="p-button-info p-button-outlined"
                                                @click="viewBookingDetail(slotProps.data)" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </DataTable>
            </div>
        </div>

        <Dialog v-model:visible="showFilterDialog" header="Bộ lọc tìm kiếm"
            :breakpoints="{ '960px': '75vw', '640px': '90vw' }" :style="{ width: '400px' }" modal closable>
            <div class="filter-content p-fluid">
                <div class="mb-3">
                    <label class="form-label fw-bold">Trạng thái đặt lịch</label>
                    <select v-model="statusFilter" class="form-select custom-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending">Chờ xác nhận</option>
                        <option value="confirmed">Đã xác nhận</option>
                        <option value="completed">Hoàn thành</option>
                        <option value="cancelled">Đã hủy</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Thanh toán</label>
                    <select v-model="paymentStatusFilter" class="form-select custom-select">
                        <option value="">Tất cả</option>
                        <option value="unpaid">Chưa thanh toán</option>
                        <option value="paid">Đã thanh toán</option>
                    </select>
                </div>
            </div>
            <template #footer>
                <div class="flex justify-end gap-2">
                    <Button label="Đặt lại" icon="pi pi-refresh" @click="resetFilters"
                        class="p-button-text p-button-secondary" />
                    <Button label="Áp dụng" icon="pi pi-check" @click="applyFilter" autofocus />
                </div>
            </template>
        </Dialog>
    </div>
</template>

<script>
// Giữ nguyên toàn bộ phần script của bạn
import Button from 'primevue/button'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Dialog from 'primevue/dialog'
import '@Staff/order-services.css';
import axios from 'axios'

export default {
    name: 'OrderServices',
    components: { Button, DataTable, Column, Dialog },
    data() {
        return {
            showFilterDialog: false,
            searchQuery: '',
            loading: false,
            bookings: [],
            expandedRows: {},
            activeTab: 'info',
            statusFilter: '',
            paymentStatusFilter: '',
            pagination: { current_page: 1, last_page: 1, per_page: 10, total: 0 },
            searchTimeout: null
        }
    },
    computed: {
        filteredBookings() {
            let filtered = this.bookings
            if (this.searchQuery && this.searchQuery.trim()) {
                const term = this.searchQuery.toLowerCase().trim()
                filtered = filtered.filter(b => {
                    const name = (b.customer_name || '').toLowerCase()
                    const phone = (b.customer_phone || '').toLowerCase()
                    const sName = (b.service?.ten_dich_vu || '').toLowerCase()
                    return name.includes(term) || phone.includes(term) || sName.includes(term)
                })
            }
            if (this.statusFilter) filtered = filtered.filter(b => b.status === this.statusFilter)
            if (this.paymentStatusFilter) filtered = filtered.filter(b => b.payment_status === this.paymentStatusFilter)
            return filtered
        }
    },
    mounted() { this.loadBookings() },
    methods: {
        async loadBookings() {
            this.loading = true
            try {
                const response = await axios.get('/staff/service-bookings/api', {
                    params: {
                        search: this.searchQuery,
                        per_page: this.pagination.per_page,
                        page: this.pagination.current_page
                    }
                })

                if (response.data.success) {
                    this.bookings = response.data.data
                    this.pagination = response.data.pagination
                } else {
                    console.error('API returned success: false')
                }
            } catch (error) {
                console.error('Error loading bookings:', error)
                this.$toast.add({
                    severity: 'error',
                    summary: 'Lỗi',
                    detail: error.response?.data?.message || 'Không thể tải danh sách ',
                    life: 5000
                })
            } finally {
                this.loading = false
            }
        },
        onPage(event) {
            this.pagination.current_page = event.page + 1;
            this.pagination.per_page = event.rows;
            this.loadBookings();
        },
        debounceSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.pagination.current_page = 1; // Reset về trang 1
                this.loadBookings();
            }, 500) // Tăng delay lên chút cho đỡ spam request
        }, showFilterModal() { this.showFilterDialog = true },
        applyFilter() { this.showFilterDialog = false },
        resetFilters() { this.statusFilter = ''; this.paymentStatusFilter = ''; this.showFilterDialog = false; },
        switchTab(tab) { this.activeTab = tab },
        async confirmBooking(booking) { alert('Call API confirm: ' + booking.id) },
        async markAsPaid(booking) { },
        async completeBooking(booking) { },
        async cancelBooking(booking) { },
        viewBookingDetail(booking) { console.log(booking) },
        getStatusText(s) { const map = { 'pending': 'Chờ xác nhận', 'confirmed': 'Đã xác nhận', 'completed': 'Hoàn thành', 'cancelled': 'Đã hủy' }; return map[s] || s },
        getStatusClass(s) { const map = { 'pending': 'bg-warning', 'confirmed': 'bg-info', 'completed': 'bg-success', 'cancelled': 'bg-danger' }; return map[s] || 'bg-secondary' },
        getPaymentStatusText(s) { return s === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' },
        getPaymentStatusClass(s) { return s === 'paid' ? 'bg-success' : 'bg-danger' },
        formatDate(d) { return d ? new Date(d).toLocaleDateString('vi-VN') : '-' },
        formatCurrency(v) { return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(v || 0) }
    }
}
</script>

<style scoped>
/* CSS Scoped xử lý riêng cho logic biến đổi Table -> Card */

/* Trên PC: Giữ nguyên */
:deep(.p-datatable-tbody > tr) {
    transition: background-color 0.2s;
}

/* Trên Mobile: Override mạnh mẽ */
@media (max-width: 768px) {

    /* Ẩn Header bảng */
    :deep(.p-datatable-thead) {
        display: none !important;
    }

    /* Mỗi dòng thành 1 Card */
    :deep(.p-datatable-tbody > tr) {
        display: block;
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        margin-bottom: 16px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
    }

    /* Khi dòng mở rộng (Row Expansion) thì bỏ margin bottom của dòng chính để nó dính vào phần chi tiết */
    :deep(.p-datatable-tbody > tr.p-datatable-row-expanded) {
        margin-bottom: 0;
        border-bottom: none;
        border-radius: 12px 12px 0 0;
    }

    /* Mỗi ô td thành 1 dòng flex */
    :deep(.p-datatable-tbody > tr > td) {
        display: flex;
        justify-content: space-between;
        align-items: center;
        text-align: right;
        padding: 12px 16px;
        border-bottom: 1px solid #f8f9fa;
        width: 100% !important;
        box-sizing: border-box;
    }

    /* Ẩn border ô cuối cùng trong card */
    :deep(.p-datatable-tbody > tr > td:last-child) {
        border-bottom: none;
    }

    /* Hiển thị label */
    :deep(.mobile-label) {
        display: inline-block;
    }
}
</style>
