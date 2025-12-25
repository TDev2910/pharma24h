<template>
    <Dialog :visible="visible" @update:visible="$emit('close')" modal header="Chi tiết Yêu cầu Hỗ trợ"
        :style="{ width: '80vw', maxWidth: '1000px' }" :breakpoints="{ '960px': '95vw' }">
        <div v-if="ticket" class="ticket-detail">
            <div class="mb-4 pb-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold text-primary">Ticket #{{ ticket.ticket_id }}</h5>
                <Tag :value="getStatusLabel(ticket.status)" :severity="getStatusSeverity(ticket.status)" />
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="bg-light p-3 rounded h-100 border">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Thông tin người gửi</h6>
                        <div class="d-flex flex-column gap-2 text-sm">
                            <div>
                                <small class="text-muted d-block">Họ tên:</small>
                                <span class="fw-medium">{{ ticket.full_name }}</span>
                            </div>
                            <div>
                                <small class="text-muted d-block">Email:</small>
                                <a :href="`mailto:${ticket.email}`" class="text-primary text-decoration-none">
                                    {{ ticket.email }}
                                </a>
                            </div>
                            <div>
                                <small class="text-muted d-block">Ngày gửi:</small>
                                <span>{{ formatDate(ticket.created_at) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="mb-4">
                        <h6>Nội dung chi tiết</h6>
                        <div class="bg-white p-3 rounded border text-secondary"
                            style="white-space: pre-line; min-height: 100px;">
                            {{ ticket.message }}
                        </div>
                    </div>

                    <div class="reply-section">
                        <h6 class="fw-bold mb-3 border-top pt-3">Phản hồi từ Quản trị viên</h6>

                        <div v-if="ticket.admin_reply" class="alert alert-success border-0 bg-opacity-10">
                            <div class="d-flex align-items-center mb-2 text-success">
                                <i class="pi pi-check-circle me-2"></i>
                                <small class="fw-bold">Đã trả lời lúc {{ formatDate(ticket.responded_at) }}</small>
                            </div>
                            <div class="text-dark" style="white-space: pre-line;">{{ ticket.admin_reply }}</div>
                        </div>

                        <form v-else @submit.prevent="submitReply">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nội dung phản hồi:</label>
                                <Textarea v-model="replyMessage" rows="5" class="w-100"
                                    placeholder="Nhập câu trả lời để gửi email cho khách hàng..."
                                    :class="{ 'p-invalid': errorMessage }" />
                                <small v-if="errorMessage" class="text-danger">{{ errorMessage }}</small>
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <Button label="Đóng" severity="secondary" text @click="$emit('close')" />
                                <Button type="submit" label="Gửi phản hồi" icon="pi pi-send" :loading="loading" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </Dialog>
</template>

<script>
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import Textarea from 'primevue/textarea';
import axios from 'axios';

export default {
    components: { Dialog, Button, Tag, Textarea },
    props: {
        visible: Boolean,
        ticket: Object
    },
    emits: ['close', 'replied'],
    data() {
        return {
            replyMessage: '',
            loading: false,
            errorMessage: ''
        }
    },
    methods: {
        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleString('vi-VN');
        },
        getStatusLabel(status) {
            const map = { 'pending': 'Chờ xử lý', 'replied': 'Đã trả lời', 'closed': 'Đã đóng' };
            return map[status] || status;
        },
        getStatusSeverity(status) {
            const map = { 'pending': 'warning', 'replied': 'success', 'closed': 'secondary' };
            return map[status] || 'info';
        },
        async submitReply() {
            if (!this.replyMessage.trim()) {
                this.errorMessage = "Vui lòng nhập nội dung phản hồi.";
                return;
            }

            this.loading = true;
            this.errorMessage = '';

            try {
                // Gọi API reply
                const response = await axios.post(`/admin/tickets/${this.ticket.id}/reply`, {
                    message: this.replyMessage
                });
                // Emit sự kiện để Parent reload lại danh sách
                this.$emit('replied');
                this.$emit('close');
                this.replyMessage = ''; // Reset form
            } catch (error) {
                console.error(error);
                this.errorMessage = error.response?.data?.message || 'Có lỗi xảy ra.';
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>

<style scoped>
.text-primary-dark {
    color: #1a4f6e;
}

:deep(.p-dialog-content) {
    padding: 1.5rem;
}
</style>
