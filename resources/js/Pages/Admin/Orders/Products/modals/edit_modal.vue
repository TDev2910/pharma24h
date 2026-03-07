<script setup>
import { computed, ref, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';

// PrimeVue Components
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Select from 'primevue/select';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';
import Panel from 'primevue/panel';

const props = defineProps({
    show: Boolean,
    order: Object
});

const emit = defineEmits(['close']);

const isVisible = computed({
    get: () => props.show,
    set: (value) => { if (!value) emit('close'); }
});

const showDetails = ref(false);
const statusLoading = ref(null);

// Form dữ liệu
const form = useForm({
    customer_name: '',
    customer_phone: '',
    customer_email: '',
    payment_method: 'cash',
    delivery_method: 'shipping',
    shipping_address: '',
    province: '',
    district: '',
    ward: '',
    pickup_location: '',
    note: '',
});

watch(() => props.order, (newVal) => {
    if (newVal) {
        form.customer_name = newVal.customer_name || '';
        form.customer_phone = newVal.customer_phone || '';
        form.customer_email = newVal.customer_email || '';
        form.payment_method = newVal.payment_method || 'cash';
        form.delivery_method = newVal.delivery_method || 'shipping';
        form.shipping_address = newVal.shipping_address || '';
        form.province = newVal.province || '';
        form.district = newVal.district || '';
        form.ward = newVal.ward || '';
        form.pickup_location = newVal.pickup_location || '';
        form.note = newVal.note || '';
        form.clearErrors();
    }
}, { deep: true, immediate: true });

// --- ACTIONS ---
const updateStatusQuick = (newStatus) => {
    if (props.order.order_status === newStatus) return;
    statusLoading.value = newStatus;
    router.post(`/admin/orders/${props.order.id}/update-status`, { status: newStatus }, {
        preserveScroll: true,
        onFinish: () => { statusLoading.value = null; }
    });
};

const submitForm = () => {
    form.post(`/admin/orders/${props.order.id}/update-info`, {
        preserveScroll: true,
        onSuccess: () => emit('close'),
    });
};

// --- OPTIONS ---
const paymentMethodOptions = [
    { label: 'Tiền mặt (COD)', value: 'cod' },
    { label: 'Chuyển khoản', value: 'transfer' },
    { label: 'VNPay', value: 'vnpay' },
    { label: 'Momo', value: 'momo' },
];

const deliveryMethodOptions = [
    { label: 'Giao hàng tận nơi', value: 'shipping' },
    { label: 'Nhận tại cửa hàng', value: 'pickup' }
];

const statusButtons = [
    { value: 'pending', label: 'Chờ xử lý', icon: 'pi pi-clock', severity: 'warn' },
    { value: 'confirmed', label: 'Đã xác nhận', icon: 'pi pi-thumbs-up', severity: 'info' },
    { value: 'delivering', label: 'Đang giao', icon: 'pi pi-truck', severity: 'primary' },
    { value: 'completed', label: 'Hoàn thành', icon: 'pi pi-check-circle', severity: 'success' },
    { value: 'cancelled', label: 'Hủy đơn', icon: 'pi pi-ban', severity: 'danger' },
];

const formatCurrency = (val) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val);
</script>

<template>
    <Dialog v-model:visible="isVisible" modal dismissableMask maximizable :style="{ width: '900px', maxWidth: '95vw' }"
        header="Chỉnh sửa đơn hàng" class="custom-order-modal">
        <template #header>
            <div class="modal-header-content">
                <div class="title-group">
                    <span class="modal-title">Chỉnh sửa đơn hàng</span>
                    <Tag :value="order?.order_status" class="status-tag" severity="secondary"></Tag>
                </div>
                <div class="order-code-group">
                    <span>Mã đơn:</span>
                    <span class="code">#{{ order?.order_code }}</span>
                </div>
            </div>
        </template>

        <div class="modal-body">

            <div class="quick-status-card">
                <h6 class="section-label">
                    <i class="pi pi-bolt"></i> Cập nhật trạng thái nhanh
                </h6>
                <div class="status-buttons">
                    <Button v-for="btn in statusButtons" :key="btn.value" :label="btn.label" :icon="btn.icon"
                        :severity="btn.severity" :outlined="order?.order_status !== btn.value"
                        :loading="statusLoading === btn.value" @click="updateStatusQuick(btn.value)" size="small"
                        class="status-btn" :class="{ 'active': order?.order_status === btn.value }" />
                </div>
            </div>

            <form @submit.prevent="submitForm" class="edit-form">

                <div class="form-column">
                    <div class="form-card">
                        <div class="card-header">
                            <i class="pi pi-user text-blue"></i> Thông tin khách hàng
                        </div>

                        <div class="form-group-container">
                            <div class="form-group">
                                <label>Họ tên <span class="required">*</span></label>
                                <InputText v-model="form.customer_name" class="input-full"
                                    :class="{ 'p-invalid': form.errors.customer_name }" />
                                <small class="error-text" v-if="form.errors.customer_name">{{ form.errors.customer_name
                                }}</small>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>SĐT <span class="required">*</span></label>
                                    <InputText v-model="form.customer_phone" class="input-full"
                                        :class="{ 'p-invalid': form.errors.customer_phone }" />
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <InputText v-model="form.customer_email" class="input-full" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Thanh toán</label>
                                <Select v-model="form.payment_method" :options="paymentMethodOptions"
                                    optionLabel="label" optionValue="value" class="input-full" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-column">
                    <div class="form-card">
                        <div class="card-header">
                            <i class="pi pi-map-marker text-orange"></i> Giao hàng & Ghi chú
                        </div>

                        <div class="form-group-container">
                            <div class="form-group">
                                <label>Hình thức giao</label>
                                <Select v-model="form.delivery_method" :options="deliveryMethodOptions"
                                    optionLabel="label" optionValue="value" class="input-full" />
                            </div>

                            <template v-if="form.delivery_method === 'shipping'">
                                <div class="form-group">
                                    <label>Địa chỉ chi tiết</label>
                                    <InputText v-model="form.shipping_address" placeholder="Số nhà, tên đường..."
                                        class="input-full" />
                                </div>

                                <div class="form-row three-cols">
                                    <InputText v-model="form.province" placeholder="Tỉnh/TP" class="input-full" />
                                    <InputText v-model="form.district" placeholder="Quận/Huyện" class="input-full" />
                                    <InputText v-model="form.ward" placeholder="P/Xã" class="input-full" />
                                </div>
                            </template>

                            <template v-if="form.delivery_method === 'pickup'">
                                <div class="form-group">
                                    <label>Cửa hàng nhận</label>
                                    <InputText v-model="form.pickup_location" placeholder="Nhập tên chi nhánh..."
                                        class="input-full" />
                                </div>
                            </template>

                            <div class="form-group mt-auto">
                                <label>Ghi chú đơn hàng</label>
                                <Textarea v-model="form.note" rows="3" autoResize class="input-full" />
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- <Panel toggleable :collapsed="!showDetails" @toggle="showDetails = !showDetails" class="details-panel">
                <template #header>
                    <div class="panel-header-custom">
                        <i class="pi pi-list"></i>
                        <span>Chi tiết sản phẩm</span>
                        <Tag :value="props.order?.items?.length || 0" severity="secondary" class="count-tag"></Tag>
                    </div>
                </template>

                <DataTable :value="props.order?.items || []" class="products-table" size="small" stripedRows>
                    <Column header="STT" class="col-stt">
                        <template #body="slotProps">{{ slotProps.index + 1 }}</template>
                    </Column>
                    <Column field="product_name" header="Sản phẩm" class="col-name"></Column>
                    <Column field="quantity" header="SL" class="col-qty">
                        <template #body="{ data }">
                            <span class="qty-badge">{{ data.quantity }}</span>
                        </template>
                    </Column>
                    <Column header="Đơn giá" class="col-price">
                        <template #body="{ data }">{{ formatCurrency(data.price) }}</template>
                    </Column>
                    <Column header="Thành tiền" class="col-total">
                        <template #body="{ data }">{{ formatCurrency(data.price * data.quantity) }}</template>
                    </Column>
                   <template #footer>
                        <div class="table-footer">
                            <span class="label">Phí vận chuyển:</span>
                            <span class="value">{{ formatCurrency(props.order?.ghn_fee) }}</span>
                            <span class="label">Tổng tiền:</span>
                            <span class="value">{{ formatCurrency(props.order?.total_amount) }}</span>
                        </div>
                    </template>
                </DataTable>
            </Panel> -->

        </div>

        <template #footer>
            <div class="modal-footer-custom">
                <div class="footer-left">
                    <div v-if="order?.order_status === 'cancelled'" class="cancelled-alert">
                        <i class="pi pi-exclamation-triangle"></i>
                        <span>Đơn hàng này đã bị hủy</span>
                    </div>
                </div>

                <div class="footer-right">
                    <Button label="Hủy bỏ" severity="secondary" @click="emit('close')" />
                    <Button label="Cập nhật" :loading="form.processing" @click="submitForm" />
                </div>
            </div>
        </template>
    </Dialog>
</template>

<style scoped>
@import '@/../../resources/css/Admin/orders/Modals/edit_modal.css';
</style>
