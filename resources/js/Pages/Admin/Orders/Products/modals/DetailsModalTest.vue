<script setup>
import { computed, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

// PrimeVue Components
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';
import Message from 'primevue/message';
import Textarea from 'primevue/textarea';

const props = defineProps({
    show: Boolean,
    order: Object // Dữ liệu order được truyền từ cha (Inertia load)
});

const emit = defineEmits(['close', 'updated']);

// State nội bộ
const loading = ref(false);
const processing = ref(false);
const creatingGHN = ref(false);
const syncingGHN = ref(false);
const adminNote = ref('');
const localOrder = ref(null); // Dùng để lưu trữ và cập nhật data cục bộ nếu cần

// Two-way binding cho Dialog
const isVisible = computed({
    get: () => props.show,
    set: (value) => {
        if (!value) emit('close');
    }
});

// Sync data khi mở modal
watch(() => props.order, (newVal) => {
    if (newVal) {
        localOrder.value = newVal;
        adminNote.value = newVal.cancellation_admin_note || '';
    }
}, { immediate: true });

// --- FORMATTERS ---
const formatCurrency = (val) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val);
const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
};

// --- HELPER UI ---
const getStatusSeverity = (status) => {
    const map = {
        'pending': 'warning', 'confirmed': 'info', 'delivering': 'primary',
        'completed': 'success', 'cancelled': 'danger'
    };
    return map[status] || 'secondary';
};

const getStatusLabel = (status) => {
    const map = {
        'pending': 'Chờ xử lý', 'confirmed': 'Đã xác nhận', 'delivering': 'Đang giao',
        'completed': 'Hoàn thành', 'cancelled': 'Đã hủy'
    };
    return map[status] || status;
};

const getPaymentStatusSeverity = (status) => {
    const map = { 'paid': 'success', 'unpaid': 'warning', 'refunded': 'info', 'failed': 'danger' };
    return map[status] || 'secondary';
};

// --- ACTIONS ---

// Reload lại dữ liệu đơn hàng (nếu cần cập nhật sau khi thao tác)
const reloadOrder = async () => {
    if (!localOrder.value?.id) return;
    try {
        // Gọi API show của OrdersTestController (trả về JSON)
        // Lưu ý: Controller cần hỗ trợ trả JSON nếu request là AJAX
        // Tuy nhiên ở DashboardTest chúng ta dùng Inertia reload, nên ở đây ta có thể emit event để cha reload
        emit('updated');

        // Hoặc nếu muốn tự load lại bằng axios:
        /*
        const res = await axios.get(`/admin/orders-test/${localOrder.value.id}`);
        if(res.data.success) localOrder.value = res.data.order;
        */
    } catch (e) { console.error(e); }
};

// In hóa đơn
const printInvoice = () => {
    if (!localOrder.value?.id) return;
    window.open(`/admin/orders-test/${localOrder.value.id}/invoice`, '_blank');
};

// Tạo đơn GHN
const createGHNOrder = () => {
    if (!props.order.district_id || !props.order.ward_code) {
        alert('Lỗi: Đơn hàng thiếu thông tin Quận/Huyện hoặc Phường/Xã.');
        return;
    }
    if (!confirm('Xác nhận tạo đơn hàng trên hệ thống Giao Hàng Nhanh?')) return;

    // Dùng router của Inertia thay vì axios
    router.post(`/admin/orders-test/${props.order.id}/ghn/create`, {}, {
        preserveScroll: true, // Giữ vị trí cuộn trang
        preserveState: true,  // Giữ trạng thái các biến khác
        onStart: () => {
            creatingGHN.value = true; // Bật loading
        },
        onSuccess: (page) => {
            // Inertia tự động nhận Flash Message từ backend
            // Nếu bạn dùng thư viện Toast, có thể hiển thị ở đây
            // page.props.flash.success chứa message: "Tạo đơn GHN thành công..."

            // Tắt modal hoặc cập nhật lại localOrder
            // Vì Inertia đã reload props.order mới từ server, ta cần cập nhật vào localOrder
            if (props.order) {
                // localOrder được watch từ props.order nên nó sẽ tự cập nhật giao diện
                alert('Tạo đơn GHN thành công!');
            }
        },
        onError: (errors) => {
            // Xử lý khi backend trả về lỗi validation hoặc lỗi 500
            alert(errors.error || 'Có lỗi xảy ra khi tạo đơn.');
        },
        onFinish: () => {
            creatingGHN.value = false; // Tắt loading dù thành công hay thất bại
        }
    });
};

// Đồng bộ trạng thái GHN (Giả lập logic, bạn cần thêm route sync nếu có)
const syncGhnStatus = async () => {
    if(!props.order.ghn_order_code) return;

    router.post(`/admin/orders-test/${props.order.id}/ghn/sync`, {}, {
        preserveScroll: true,
        preserveState: true,
        onStart: () => { syncingGHN.value = true; },
        onSuccess: (page) => {

        },
        onError: (errors) => {
            alert(errors.error || 'Có lỗi xảy ra khi đồng bộ trạng thái GHN.');
        },
        onFinish: () => { syncingGHN.value = false; }
    });
};

// Xử lý hủy đơn (Duyệt/Từ chối) - Logic cũ
const processCancellation = async (action) => {
    if (action === 'reject' && !adminNote.value.trim()) {
        alert('Vui lòng nhập ghi chú khi từ chối yêu cầu hủy.');
        return;
    }
    if (!confirm(`Bạn có chắc chắn muốn ${action === 'approve' ? 'DUYỆT' : 'TỪ CHỐI'} yêu cầu hủy?`)) return;

    processing.value = true;
    try {
        // Lưu ý: Bạn cần thêm route approve/reject vào routes/admin.php cho module test nếu muốn dùng
        // Ví dụ: axios.post(`/admin/orders-test/${localOrder.value.id}/cancellations/${action}`, { admin_note: adminNote.value })
        alert('Chức năng xử lý hủy cần thêm Route Backend.');
    } catch (error) {
        console.error(error);
    } finally {
        processing.value = false;
    }
};

</script>

<template>
    <Dialog v-model:visible="isVisible" modal dismissableMask :style="{ width: '60rem' }"
        :header="`Chi tiết đơn hàng #${localOrder?.order_code}`"
        :pt="{
            root: { class: 'rounded-xl shadow-2xl border border-gray-100' },
            header: { class: 'px-6 py-4 border-b border-gray-100 bg-white' },
            content: { class: 'p-0 bg-gray-50/50' },
            footer: { class: 'px-6 py-4 bg-white border-t border-gray-100 flex justify-end gap-2' }
        }"
    >
        <div v-if="localOrder" class="p-6 flex flex-col gap-6 overflow-y-auto max-h-[70vh]">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex flex-col gap-4">
                    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                        <div class="flex items-center gap-2 mb-3 text-gray-800 font-bold border-b pb-2">
                            <i class="pi pi-box text-blue-500"></i> Thông tin đơn hàng
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between"><span class="text-gray-500">Mã đơn:</span> <span class="font-mono font-bold">{{ localOrder.order_code }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Ngày đặt:</span> <span>{{ formatDate(localOrder.created_at) }}</span></div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Trạng thái:</span>
                                <Tag :value="getStatusLabel(localOrder.order_status)" :severity="getStatusSeverity(localOrder.order_status)" rounded class="px-2 py-0.5 text-xs"></Tag>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Thanh toán:</span>
                                <Tag :value="localOrder.payment_status" :severity="getPaymentStatusSeverity(localOrder.payment_status)" rounded class="px-2 py-0.5 text-xs"></Tag>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                        <div class="flex items-center gap-2 mb-3 text-gray-800 font-bold border-b pb-2">
                            <i class="pi pi-user text-green-500"></i> Khách hàng
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between"><span class="text-gray-500">Họ tên:</span> <span class="font-medium">{{ localOrder.customer_name }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">SĐT:</span> <span class="font-medium">{{ localOrder.customer_phone }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Email:</span> <span>{{ localOrder.customer_email || '---' }}</span></div>
                            <div class="mt-2 pt-2 border-t text-gray-600">
                                <div class="font-semibold text-xs text-gray-400 uppercase mb-1">Địa chỉ giao hàng</div>
                                <div>{{ localOrder.shipping_address || 'Nhận tại cửa hàng' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                        <div class="flex items-center gap-2 mb-3 text-gray-800 font-bold border-b pb-2">
                            <i class="pi pi-wallet text-purple-500"></i> Thanh toán & Vận chuyển
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between"><span class="text-gray-500">Hình thức TT:</span> <span class="uppercase font-bold">{{ localOrder.payment_method }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Vận chuyển:</span> <span>{{ localOrder.delivery_method === 'shipping' ? 'Giao hàng tận nơi' : 'Nhận tại cửa hàng' }}</span></div>
                        </div>
                    </div>

                    <div v-if="localOrder.delivery_method === 'shipping'" class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                        <div class="flex items-center gap-2 mb-3 text-gray-800 font-bold border-b pb-2">
                            <i class="pi pi-truck text-orange-500"></i> Giao Hàng Nhanh
                        </div>

                        <div v-if="localOrder.shipping_code || localOrder.ghn_order_code" class="space-y-2 text-sm">
                            <div class="flex justify-between"><span class="text-gray-500">Mã vận đơn:</span> <span class="font-bold text-blue-600">{{ localOrder.shipping_code || localOrder.ghn_order_code }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Trạng thái GHN:</span> <Tag :value="localOrder.ghn_status || '---'" severity="info" rounded class="text-xs" /></div>
                            <div class="flex justify-between" v-if="localOrder.ghn_fee"><span class="text-gray-500">Phí ship:</span> <span>{{ formatCurrency(localOrder.ghn_fee) }}</span></div>
                            <div class="mt-2" v-if="localOrder.ghn_tracking_url">
                                <a :href="localOrder.ghn_tracking_url" target="_blank" class="text-blue-600 hover:underline text-xs flex items-center gap-1">
                                    <i class="pi pi-external-link"></i> Theo dõi đơn hàng
                                </a>
                            </div>
                        </div>

                        <div v-else class="flex flex-col gap-2">
                            <Message severity="info" :closable="false" class="m-0 text-xs">Chưa tạo đơn vận chuyển</Message>
                            <div v-if="!localOrder.district_id && !localOrder.ward_code" class="text-xs text-red-500">
                                <i class="pi pi-exclamation-triangle"></i> Thiếu thông tin quận/huyện để tạo đơn.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
                <DataTable :value="localOrder.items" showGridlines class="text-sm">
                    <Column header="STT" class="w-12 text-center">
                        <template #body="slotProps">{{ slotProps.index + 1 }}</template>
                    </Column>
                    <Column field="product_name" header="Sản phẩm">
                        <template #body="slotProps">
                            <div class="font-medium">{{ slotProps.data.product_name }}</div>
                            <small class="text-gray-400" v-if="slotProps.data.item?.sku">SKU: {{ slotProps.data.item.sku }}</small>
                        </template>
                    </Column>
                    <Column field="quantity" header="SL" class="w-16 text-center font-bold" />
                    <Column field="price" header="Đơn giá" class="text-right">
                        <template #body="slotProps">{{ formatCurrency(slotProps.data.price) }}</template>
                    </Column>
                    <Column header="Thành tiền" class="text-right font-bold">
                        <template #body="slotProps">{{ formatCurrency(slotProps.data.price * slotProps.data.quantity) }}</template>
                    </Column>

                    <template #footer>
                        <div class="flex justify-end items-center gap-4 py-2">
                            <span class="text-gray-500">Tổng tiền hàng:</span>
                            <span class="text-xl font-bold text-blue-600">{{ formatCurrency(localOrder.total_amount) }}</span>
                        </div>
                    </template>
                </DataTable>
            </div>

            <div v-if="localOrder.cancellation_status" class="bg-orange-50 p-4 rounded-lg border border-orange-100">
                <div class="flex items-center gap-2 mb-2 font-bold text-orange-700">
                    <i class="pi pi-exclamation-circle"></i> Yêu cầu hủy đơn
                </div>
                <div class="grid grid-cols-2 gap-4 text-sm text-gray-700">
                    <div>Lý do: <b>{{ localOrder.cancellation_reason }}</b></div>
                    <div>Ghi chú khách: <i>{{ localOrder.cancellation_user_note }}</i></div>
                    <div>Ngày yêu cầu: {{ formatDate(localOrder.cancellation_requested_at) }}</div>
                </div>
            </div>

        </div>

        <template #footer>
            <div v-if="localOrder?.cancellation_status === 'requested'" class="w-full mb-3">
                <Textarea v-model="adminNote" rows="2" placeholder="Ghi chú phản hồi cho khách (bắt buộc khi từ chối)..." class="w-full" />
            </div>

            <div class="flex gap-2 w-full justify-end">
                <Button label="Đóng" icon="pi pi-times" text severity="secondary" @click="emit('close')" />

                <template v-if="localOrder?.cancellation_status === 'requested'">
                    <Button label="Từ chối hủy" icon="pi pi-ban" severity="warning" :loading="processing" @click="processCancellation('reject')" />
                    <Button label="Xác nhận hủy" icon="pi pi-check" severity="danger" :loading="processing" @click="processCancellation('approve')" />
                </template>

                <Button label="In Hóa Đơn" icon="pi pi-print" severity="success" outlined @click="printInvoice" />

                <template v-if="localOrder?.delivery_method === 'shipping'">
                    <Button
                        v-if="!localOrder.shipping_code && !localOrder.ghn_order_code"
                        label="Tạo đơn GHN"
                        icon="pi pi-send"
                        severity="warning"
                        :loading="creatingGHN"
                        @click="createGHNOrder"
                        :disabled="!localOrder.district_id && !localOrder.ward_code"
                    />
                    <Button
                        v-if="order.ghn_order_code"
                        label="Đồng bộ GHN"
                        icon="pi pi-sync"
                        severity="info"
                        :loading="syncingGHN"
                        @click="syncGhnStatus"
                        class="p-button-sm"
                    />
                    <Button
                        v-else
                        label="In Vận Đơn GHN"
                        icon="pi pi-file-pdf"
                        severity="info"
                        as="a"
                        :href="`/admin/orders-test/${localOrder.id}/ghn/print`"
                        target="_blank"
                    />
                </template>
            </div>
        </template>
    </Dialog>
</template>

<style scoped>
/* Không cần CSS Animation thủ công nữa vì PrimeVue Dialog đã tự xử lý rất mượt */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: #e2e8f0;
    border-radius: 20px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: #cbd5e1;
}
</style>
