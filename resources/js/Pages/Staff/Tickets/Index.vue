<template>

    <Head title="Danh sách Yêu cầu Hỗ trợ" />

    <div class="tickets-page">
        <div class="header-control-bar mb-4 d-flex justify-content-between align-items-center">
            <h4 class="m-0 fw-bold text-primary-dark">Danh sách Yêu cầu</h4>

            <span class="p-input-icon-left">
                <InputText v-model="search" placeholder="Tìm kiếm..." class="p-inputtext-sm" />
            </span>

        </div>

        <div class="table-container bg-white p-3 rounded shadow-sm">
            <DataTable :value="tickets.data" :lazy="true" :paginator="true" :rows="tickets.per_page"
                :totalRecords="tickets.total" :first="(tickets.current_page - 1) * tickets.per_page" @page="onPage"
                stripedRows responsiveLayout="scroll" tableStyle="min-width: 50rem">
                <Column field="ticket_id" header="Mã Ticket">
                    <template #body="{ data }">
                        <span class="fw-bold text-primary">#{{ data.ticket_id }}</span>
                    </template>
                </Column>

                <Column field="full_name" header="Người gửi" />

                <Column field="message" header="Nội dung" style="max-width: 300px;">
                    <template #body="{ data }">
                        <div class="text-truncate" v-tooltip.top="data.message">
                            {{ data.message }}
                        </div>
                    </template>
                </Column>

                <Column field="subject" header="Chủ đề" />

                <Column field="status" header="Trạng thái">
                    <template #body="{ data }">
                        <Tag :value="getStatusLabel(data.status)" :severity="getStatusSeverity(data.status)" />
                    </template>
                </Column>

                <Column header="Thao tác" class="text-center">
                    <template #body="{ data }">
                        <Button icon="pi pi-eye" rounded severity="info" text @click="openDetailModal(data)"
                            v-tooltip.top="'Xem chi tiết'" />
                    </template>
                </Column>
            </DataTable>
        </div>

        <TicketDetailModal :visible="showModal" :ticket="selectedTicket" @close="showModal = false" />
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import InputText from 'primevue/inputtext';
import TicketDetailModal from './Modals/TicketDetailModal.vue';
import debounce from 'lodash/debounce';
import Swal from 'sweetalert2';

// 1. Nhận Props từ Controller
const props = defineProps({
    tickets: Object,
    filters: Object
});

// 2. State quản lý UI
const showModal = ref(false);
const selectedTicket = ref(null);
const search = ref(props.filters.search || '');

// 3. Logic tìm kiếm (Debounce)
watch(search, debounce((value) => {
    router.get(window.location.pathname, { search: value }, {
        preserveState: true,
        replace: true,
        preserveScroll: true
    });
}, 300));

// 4. Logic phân trang
const onPage = (event) => {
    router.get(window.location.pathname, {
        page: event.page + 1,
        search: search.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

// 5. Modal Logic
const openDetailModal = (ticket) => {
    selectedTicket.value = ticket;
    showModal.value = true;
};

// 6. Helpers
const getStatusLabel = (status) => {
    const map = { 'pending': 'Chờ xử lý', 'replied': 'Đã trả lời', 'closed': 'Đã đóng' };
    return map[status] || status;
};
const getStatusSeverity = (status) => {
    const map = { 'pending': 'warning', 'replied': 'success', 'closed': 'secondary' };
    return map[status] || 'info';
};

// 7. Lắng nghe Flash Message (Success) từ Controller
const page = usePage();
watch(() => page.props.flash, (flash) => {
    if (flash?.success) {
        Swal.fire({
            icon: 'success',
            title: 'Thành công',
            text: flash.success,
            timer: 3000,
            showConfirmButton: false
        });
    }
}, { deep: true });
</script>

<style scoped>
@import url('../../../../css/Staff/tickets/dashboard.css');

.text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>