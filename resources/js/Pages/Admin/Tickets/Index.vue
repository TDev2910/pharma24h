<template>

    <Head title="Danh sách Yêu cầu Hỗ trợ" />

    <div class="tickets-page">
        <div class="header-control-bar mb-4">
            <div class="title-section">
                <h4 class="m-0 fw-bold text-primary-dark">Danh sách Yêu cầu</h4>
            </div>
        </div>

        <div class="table-container bg-white p-3 rounded shadow-sm">
            <DataTable :value="tickets" :lazy="true" :paginator="true" :rows="pagination.per_page"
                :totalRecords="pagination.total" @page="onPage" stripedRows responsiveLayout="scroll"
                tableStyle="min-width: 50rem">
                <Column field="ticket_id" header="Mã Ticket">
                    <template #body="slotProps">
                        <span class="font-bold text-blue-600">#{{ slotProps.data.ticket_id }}</span>
                    </template>
                </Column>

                <Column field="full_name" header="Người gửi"></Column>
                <Column field="message" header="Nội dung" style="max-width: 300px;">
                    <template #body="slotProps">
                        <div class="message-cell text-truncate" v-tooltip.top="{
                            value: slotProps.data.message,
                            class: 'custom-tooltip'
                        }">
                            {{ slotProps.data.message }}
                        </div>
                    </template>
                </Column>
                <Column field="subject" header="Chủ đề"></Column>

                <Column field="status" header="Trạng thái">
                    <template #body="slotProps">
                        <Tag :value="getStatusLabel(slotProps.data.status)"
                            :severity="getStatusSeverity(slotProps.data.status)" />
                    </template>
                </Column>

                <Column header="Thao tác" style="text-align: center">
                    <template #body="slotProps">
                        <Button icon="pi pi-eye" style="margin-left:-65px" rounded severity="info" text
                            @click="openDetailModal(slotProps.data)" v-tooltip.top="'Xem chi tiết & Phản hồi'" />
                    </template>
                </Column>

            </DataTable>
        </div>

        <TicketDetailModal :visible="showModal" :ticket="selectedTicket" @close="showModal = false"
            @replied="handleReplied" />

    </div>
</template>

<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import TicketDetailModal from './Modals/TicketDetailModal.vue';

export default {
    name: 'TicketIndex',
    components: {
        AdminLayout, Head, DataTable, Column, Button, Tag,
        TicketDetailModal // Đăng ký component modal
    },

    data() {
        return {
            tickets: [],
            loading: false,
            searchQuery: '',
            pagination: { current_page: 1, per_page: 10, total: 0 },

            // State cho Modal
            showModal: false,
            selectedTicket: null
        }
    },

    mounted() {
        this.loadTickets();
    },

    methods: {
        // ... (Các hàm loadTickets, onPage, debounceSearch giữ nguyên như cũ) ...
        async loadTickets() {
            this.loading = true;
            try {
                const response = await axios.get('/admin/tickets/api', {
                    params: { page: this.pagination.current_page }
                });
                this.tickets = response.data.data;
                this.pagination = response.data;
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        // --- CÁC HÀM MỚI CHO MODAL ---

        // 1. Mở Modal và gán dữ liệu dòng được chọn
        openDetailModal(ticketData) {
            this.selectedTicket = ticketData;
            this.showModal = true;
        },

        // 2. Xử lý khi phản hồi thành công (Reload lại bảng để cập nhật trạng thái)
        handleReplied() {
            this.loadTickets();
            // this.$toast.add(...) // Nếu muốn hiển thị toast ở trang cha
        },

        // ... (Các hàm Helper getStatusLabel, getStatusSeverity giữ nguyên) ...
        getStatusLabel(status) {
            const map = { 'pending': 'Chờ xử lý', 'replied': 'Đã trả lời', 'closed': 'Đã đóng' };
            return map[status] || status;
        },
        getStatusSeverity(status) {
            const map = { 'pending': 'warning', 'replied': 'success', 'closed': 'secondary' };
            return map[status] || 'info';
        }
    }
}
</script>

<style scoped>
/* Class cắt ngắn văn bản (nếu chưa có sẵn từ Bootstrap) */
.text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
    /* Quan trọng để truncate hoạt động */
}

/* Tùy chọn: Tăng độ rộng tooltip để dễ đọc hơn */
:global(.custom-tooltip .p-tooltip-text) {
    max-width: 400px;
    white-space: pre-line;
    /* Giữ xuống dòng nếu có */
}
</style>
