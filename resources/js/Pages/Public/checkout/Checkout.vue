<template>
    <div class="page-wrapper bg-light min-vh-100">
        <Header :auth="auth" />

        <section class="checkout-header py-4 bg-white shadow-sm mb-4" style="margin-top: 100px;">
            <div class="container">
                <div class="d-flex align-items-center text-muted small mb-2">
                    <i class="pi pi-home me-2"></i> Trang chủ
                    <i class="pi pi-angle-right mx-2"></i> Giỏ hàng
                    <i class="pi pi-angle-right mx-2"></i> Thanh toán
                </div>
                <h2 class="fw-bold text-primary m-0">Thanh Toán & Đặt Hàng</h2>
            </div>
        </section>

        <section class="checkout-content pb-5">
            <div class="container">
                <form @submit.prevent="submitOrder">
                    <div class="row g-4">

                        <div class="col-lg-8">

                            <div class="card border-0 shadow-sm rounded-3 mb-4">
                                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                                    <h5 class="fw-bold text-dark"><i class="pi pi-id-card text-primary me-2"></i>Thông
                                        tin nhận hàng</h5>
                                </div>

                                <div class="card-body p-4">
                                    <label class="form-label fw-medium mb-2 text-muted">Vui lòng chọn hình thức nhận
                                        hàng:</label>
                                    <div
                                        class="delivery-toggle d-flex justify-content-center mb-4 p-1 bg-light rounded-pill border">
                                        <div class="toggle-item px-4 py-2 rounded-pill cursor-pointer text-center flex-fill fw-bold transition-all"
                                            :class="form.deliveryMethod === 'shipping' ? 'bg-primary text-white shadow-sm' : 'text-muted'"
                                            @click="form.deliveryMethod = 'shipping'">
                                            <i class="pi pi-truck me-2"></i>Giao hàng tận nơi
                                        </div>
                                        <div class="toggle-item px-4 py-2 rounded-pill cursor-pointer text-center flex-fill fw-bold transition-all"
                                            :class="form.deliveryMethod === 'pickup' ? 'bg-primary text-white shadow-sm' : 'text-muted'"
                                            @click="form.deliveryMethod = 'pickup'">
                                            <i class="pi pi-building me-2"></i>Nhận tại nhà thuốc
                                        </div>
                                    </div>

                                    <hr class="text-muted opacity-25 my-4">

                                    <div v-if="form.deliveryMethod === 'shipping'" class="animate-fade">
                                        <h6 class="fw-bold mb-3 text-primary"><i class="pi pi-user me-2"></i>Thông tin
                                            người nhận</h6>
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6">
                                                <label class="form-label small text-muted">Họ và tên <span
                                                        class="text-danger">*</span></label>
                                                <InputText v-model="form.customerName" class="w-100"
                                                    placeholder="Nguyễn Văn A"
                                                    :class="{ 'p-invalid': errors.customerName }" />
                                                <small v-if="errors.customerName" class="text-danger">{{
                                                    errors.customerName }}</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small text-muted">Số điện thoại <span
                                                        class="text-danger">*</span></label>
                                                <InputText v-model="form.customerPhone" class="w-100"
                                                    placeholder="0909..."
                                                    :class="{ 'p-invalid': errors.customerPhone }" />
                                                <small v-if="errors.customerPhone" class="text-danger">{{
                                                    errors.customerPhone }}</small>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small text-muted">Email <span
                                                        class="text-muted fw-normal">(Nhận hóa đơn điện
                                                        tử)</span></label>
                                                <InputText v-model="form.customerEmail" class="w-100"
                                                    placeholder="email@example.com" />
                                            </div>
                                        </div>

                                        <h6 class="fw-bold mb-3 text-primary"><i class="pi pi-map me-2"></i>Địa chỉ giao
                                            hàng</h6>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label small text-muted">Tỉnh / Thành phố <span
                                                        class="text-danger">*</span></label>
                                                <Dropdown v-model="form.province" :options="locations.provinces"
                                                    optionLabel="name" filter placeholder="Chọn Tỉnh/Thành"
                                                    class="w-100 p-inputtext-sm"
                                                    :class="{ 'p-invalid': errors.province }"
                                                    @change="onProvinceChange" />
                                                <small v-if="errors.province" class="text-danger">{{ errors.province
                                                    }}</small>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small text-muted">Quận / Huyện <span
                                                        class="text-danger">*</span></label>
                                                <Dropdown v-model="form.district" :options="locations.districts"
                                                    optionLabel="name" filter placeholder="Chọn Quận/Huyện"
                                                    class="w-100 p-inputtext-sm" :disabled="!form.province"
                                                    @change="onDistrictChange" />
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small text-muted">Phường / Xã <span
                                                        class="text-danger">*</span></label>
                                                <Dropdown v-model="form.ward" :options="locations.wards"
                                                    optionLabel="name" filter placeholder="Chọn Phường/Xã"
                                                    class="w-100 p-inputtext-sm" :disabled="!form.district" />
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small text-muted">Địa chỉ cụ thể <span
                                                        class="text-danger">*</span></label>
                                                <InputText v-model="form.streetAddress" class="w-100"
                                                    placeholder="Số nhà, tên đường, tòa nhà..."
                                                    :class="{ 'p-invalid': errors.streetAddress }" />
                                                <small v-if="errors.streetAddress" class="text-danger">{{
                                                    errors.streetAddress }}</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-else class="animate-fade">
                                        <div
                                            class="alert alert-info border-0 bg-info-subtle text-info-emphasis mb-4 rounded-3">
                                            <div class="d-flex">
                                                <i class="pi pi-info-circle me-2 mt-1"></i>
                                                <div>
                                                    <strong>Lưu ý:</strong> Sản phẩm của bạn sẽ được giữ tại cửa hàng
                                                    trong vòng <strong>24h</strong>. Vui lòng đến đúng giờ.
                                                </div>
                                            </div>
                                        </div>

                                        <h6 class="fw-bold mb-3 text-primary"><i class="pi pi-user me-2"></i>Thông tin
                                            người đến lấy hàng</h6>
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6">
                                                <label class="form-label small text-muted">Họ và tên <span
                                                        class="text-danger">*</span></label>
                                                <InputText v-model="form.customerName" class="w-100"
                                                    placeholder="Nguyễn Văn A"
                                                    :class="{ 'p-invalid': errors.customerName }" />
                                                <small v-if="errors.customerName" class="text-danger">{{
                                                    errors.customerName }}</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small text-muted">Số điện thoại <span
                                                        class="text-danger">*</span></label>
                                                <InputText v-model="form.customerPhone" class="w-100"
                                                    placeholder="0909..."
                                                    :class="{ 'p-invalid': errors.customerPhone }" />
                                                <small v-if="errors.customerPhone" class="text-danger">{{
                                                    errors.customerPhone }}</small>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small text-muted">Email <span
                                                        class="text-muted fw-normal">(Nhận xác nhận đơn
                                                        hàng)</span></label>
                                                <InputText v-model="form.customerEmail" class="w-100"
                                                    placeholder="email@example.com" />
                                            </div>
                                        </div>

                                        <h6 class="fw-bold mb-3 text-primary"><i class="pi pi-building me-2"></i>Chọn
                                            cửa hàng gần bạn</h6>
                                        <div class="mb-3">
                                            <label class="form-label small text-muted">Tìm kiếm nhà thuốc <span
                                                    class="text-danger">*</span></label>
                                            <Dropdown v-model="form.pickupLocation" :options="pharmacyList"
                                                optionLabel="name" filter placeholder="Chọn cửa hàng..." class="w-100"
                                                :class="{ 'p-invalid': errors.pickupLocation }">
                                                <template #option="slotProps">
                                                    <div class="py-2">
                                                        <div class="fw-bold text-dark">{{ slotProps.option.name }}</div>
                                                        <div class="small text-muted"><i
                                                                class="pi pi-map-marker me-1 text-primary"></i>{{
                                                            slotProps.option.address }}</div>
                                                    </div>
                                                </template>
                                            </Dropdown>
                                            <small v-if="errors.pickupLocation" class="text-danger">{{
                                                errors.pickupLocation }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card border-0 shadow-sm rounded-3 mb-4">
                                <div class="card-body p-4">
                                    <label class="form-label fw-medium"><i
                                            class="pi pi-pencil text-primary me-2"></i>Ghi chú đơn hàng</label>
                                    <Textarea v-model="form.note" rows="2" class="w-100"
                                        placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi giao..." />
                                </div>
                            </div>

                            <div class="card border-0 shadow-sm rounded-3">
                                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                                    <h5 class="fw-bold text-dark"><i class="pi pi-wallet text-primary me-2"></i>Phương
                                        thức thanh toán</h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="payment-options d-flex flex-column gap-3">
                                        <label
                                            class="payment-item d-flex align-items-center p-3 border rounded-3 cursor-pointer transition-all"
                                            :class="{ 'border-primary bg-primary-subtle': form.paymentMethod === 'cod' }">
                                            <RadioButton v-model="form.paymentMethod" inputId="cod" value="cod" />
                                            <div class="ms-3 flex-fill">
                                                <span class="fw-bold d-block text-dark">Thanh toán khi nhận hàng
                                                    (COD)</span>
                                                <span class="small text-muted">Bạn chỉ phải thanh toán khi đã nhận được
                                                    hàng</span>
                                            </div>
                                            <i class="pi pi-money-bill fs-4 text-success"></i>
                                        </label>

                                        <label
                                            class="payment-item d-flex align-items-center p-3 border rounded-3 cursor-pointer transition-all"
                                            :class="{ 'border-primary bg-primary-subtle': form.paymentMethod === 'vnpay' }">
                                            <RadioButton v-model="form.paymentMethod" inputId="vnpay" value="vnpay" />
                                            <div class="ms-3 flex-fill">
                                                <span class="fw-bold d-block text-dark">Thanh toán qua VNPAY / Ngân
                                                    hàng</span>
                                                <span class="small text-muted">Quét mã QR để thanh toán nhanh
                                                    chóng</span>
                                            </div>
                                            <i class="pi pi-credit-card fs-4 text-primary"></i>
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="col-lg-4">
                            <div class="checkout-summary card border-0 shadow-lg rounded-3 position-sticky"
                                style="top: 20px;">
                                <div class="card-header bg-primary text-white py-3 rounded-top-3">
                                    <h5 class="m-0 fw-bold text-center">Tóm Tắt Đơn Hàng</h5>
                                </div>
                                <div class="card-body p-4">

                                    <div class="order-items mb-3 border-bottom pb-3">
                                        <div v-for="item in cartItems" :key="item.id"
                                            class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-light text-dark border me-2">{{
                                                    getItemQuantity(item) }}x</span>
                                                <span class="text-dark small fw-medium text-truncate"
                                                    style="max-width: 150px;">{{ item.name
                                                    }}</span>
                                            </div>
                                            <span class="small fw-bold">{{ formatCurrency(getItemPrice(item)) }}đ</span>
                                        </div>
                                    </div>

                                    <div class="summary-row d-flex justify-content-between mb-2">
                                        <span class="text-muted">Tạm tính:</span>
                                        <span class="fw-bold">{{ formatCurrency(cartTotal) }}đ</span>
                                    </div>
                                    <div class="summary-row d-flex justify-content-between mb-2">
                                        <span class="text-muted">Phí vận chuyển:</span>
                                        <span v-if="shippingFee === 0" class="text-success fw-bold">Miễn phí</span>
                                        <span v-else>{{ formatCurrency(shippingFee) }}đ</span>
                                    </div>

                                    <hr class="my-3 text-muted">

                                    <div class="total-row d-flex justify-content-between align-items-center mb-4">
                                        <span class="h5 fw-bold text-dark m-0">Tổng cộng:</span>
                                        <span class="h4 fw-bold text-danger m-0">{{ formatCurrency(finalTotal)
                                            }}đ</span>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="terms" checked>
                                        <label class="form-check-label small text-muted" for="terms">
                                            Đồng ý với các <a href="#" class="text-decoration-none">điều khoản & chính
                                                sách</a> của nhà thuốc.
                                        </label>
                                    </div>

                                    <button type="submit"
                                        class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm d-flex justify-content-center align-items-center"
                                        :disabled="isLoading">
                                        <i v-if="isLoading" class="pi pi-spin pi-spinner me-2"></i>
                                        <span v-else>HOÀN TẤT ĐẶT HÀNG</span>
                                    </button>

                                    <a href="/cart"
                                        class="d-block text-center mt-3 text-muted text-decoration-none small hover-underline">
                                        <i class="pi pi-arrow-left me-1"></i> Quay lại giỏ hàng
                                    </a>

                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </section>
    </div>
</template>

<script setup>
import Header from '@/Pages/Public/components/Header.vue' // Giữ component Header của bạn
import { ref, computed, onMounted, reactive, watch } from 'vue';
import axios from 'axios';
import { useToast } from 'primevue/usetoast'; // Nếu dùng Toast
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Dropdown from 'primevue/dropdown';
import RadioButton from 'primevue/radiobutton';
import {router} from '@inertiajs/vue3';
import Checkbox from 'primevue/checkbox';
import Button from 'primevue/button';

// --- PROPS & SETUP ---
const props = defineProps({
    auth: { type: Object, default: () => ({ user: null }) },
    cartData: {
        type: Object,
        default: () => ({
            items: [],
            total: 0
        })
    },
    pharmacyLocations: { type: Array, default: () => [] }
})

// --- STATE QUẢN LÝ FORM ---
const toast = useToast();

const form = reactive({
    // Thông tin khách hàng (dùng chung cho cả 2 method)
    customerName: props.auth.user?.name || '',
    customerPhone: props.auth.user?.phone || '',
    customerEmail: props.auth.user?.email || '',

    // Giao vận
    deliveryMethod: 'shipping', // 'shipping' | 'pickup'
    note: '',

    // Địa chỉ (cho Shipping)
    province: null,
    district: null,
    ward: null,
    streetAddress: '',
    districtId: null,
    wardCode: null,

    // Cửa hàng (cho Pickup)
    pickupLocation: null,

    // Thanh toán
    paymentMethod: 'cod'
});

const errors = reactive({});
const isLoading = ref(false);

// --- DỮ LIỆU ĐỊA CHỈ & CỬA HÀNG (Mock Data hoặc API) ---
const locations = reactive({
    provinces: [],
    districts: [],
    wards: []
});

const cartItems = ref(Array.isArray(props.cartData?.items) ? props.cartData.items : []);
watch(
    () => props.cartData,
    (newVal) => {
        cartItems.value = Array.isArray(newVal?.items) ? newVal.items : [];
    },
    { deep: true }
);

const pharmacyList = computed(() => props.pharmacyLocations ?? []);

// --- API ĐỊA CHỈ (Open API VN) ---
const responseToArray = (payload) => {
    if (Array.isArray(payload)) return payload;
    if (Array.isArray(payload?.data)) return payload.data;
    return [];
};

const fetchProvinces = async () => {
    try {
        const { data } = await axios.get('/ghn/provinces');
        if (data?.success !== false) {
            const list = responseToArray(data);
            locations.provinces = list.map((province) => ({
                name: province.ProvinceName ?? province.name ?? '',
                code: province.ProvinceID ?? province.code ?? null,
            }));
        } else {
            toast.add({ severity: 'error', summary: 'GHN', detail: data?.message ?? 'Không lấy được tỉnh/thành', life: 4000 });
        }
    } catch (e) {
        console.error(e);
        toast.add({ severity: 'error', summary: 'GHN', detail: 'Không thể tải tỉnh/thành. Vui lòng thử lại.', life: 4000 });
    }
};

const onProvinceChange = async () => {
    // Reset district & ward khi đổi province
    form.district = null;
    form.ward = null;
    locations.districts = [];
    locations.wards = [];

    if (form.province?.code) {
        try {
            const { data } = await axios.post('/ghn/districts', { province_id: form.province.code });
            if (data?.success !== false) {
                const list = responseToArray(data);
                locations.districts = list.map((district) => ({
                    name: district.DistrictName ?? district.name ?? '',
                    code: district.DistrictID ?? district.code ?? null,
                }));
            } else {
                toast.add({ severity: 'error', summary: 'GHN', detail: data?.message ?? 'Không lấy được quận/huyện', life: 4000 });
            }
        } catch (error) {
            console.error(error);
            toast.add({ severity: 'error', summary: 'GHN', detail: 'Không thể tải quận/huyện. Vui lòng thử lại.', life: 4000 });
        }
    }
};

const onDistrictChange = async () => {
    // Reset ward khi đổi district
    form.ward = null;
    locations.wards = [];

    if (form.district?.code) {
        form.districtId = form.district.code;
        try {
            const { data } = await axios.post('/ghn/wards', { district_id: form.district.code });
            if (data?.success !== false) {
                const list = responseToArray(data);
                locations.wards = list.map((ward) => ({
                    name: ward.WardName ?? ward.name ?? '',
                    code: ward.WardCode ?? ward.code ?? null,
                }));
            } else {
                toast.add({ severity: 'error', summary: 'GHN', detail: data?.message ?? 'Không lấy được phường/xã', life: 4000 });
            }
        } catch (error) {
            console.error(error);
            toast.add({ severity: 'error', summary: 'GHN', detail: 'Không thể tải phường/xã. Vui lòng thử lại.', life: 4000 });
        }
    }
};

watch(
    () => form.ward,
    (newVal) => {
        form.wardCode = newVal?.code ?? null;
        form.districtId = form.district?.code ?? null;
    }
);

onMounted(() => {
    fetchProvinces();
});

// --- TÍNH TOÁN TIỀN ---
const getItemQuantity = (item) => Number(item?.quantity ?? item?.qty ?? 1);
const getItemPrice = (item) => Number(item?.price ?? item?.unit_price ?? 0);
const formatCurrency = (value = 0) => Number(value || 0).toLocaleString('vi-VN');

const cartTotal = computed(() => {
    if (!isNaN(props.cartData?.total)) {
        return Number(props.cartData.total);
    }
    return cartItems.value.reduce((total, item) => total + (getItemPrice(item) * getItemQuantity(item)), 0);
});

const shippingFee = computed(() => {
    // Logic: Pickup = 0đ, Đơn > 300k = 0đ, còn lại = 30k
    if (form.deliveryMethod === 'pickup') return 0;
    return cartTotal.value > 300000 ? 0 : 30000;
});

const finalTotal = computed(() => cartTotal.value + shippingFee.value);

const submitOrder = async () => {
    Object.keys(errors).forEach(key => delete errors[key]);
    let isValid = true;

    if (!form.customerName) { errors.customerName = 'Vui lòng nhập họ tên'; isValid = false; }
    if (!form.customerPhone) { errors.customerPhone = 'Vui lòng nhập SĐT'; isValid = false; }

    if (form.deliveryMethod === 'shipping') {
        if (!form.province) { errors.province = 'Vui lòng chọn Tỉnh/Thành'; isValid = false; }
        if (!form.streetAddress) { errors.streetAddress = 'Vui lòng nhập địa chỉ cụ thể'; isValid = false; }
    } else if (!form.pickupLocation) {
        errors.pickupLocation = 'Vui lòng chọn cửa hàng'; isValid = false;
    }

    if (!isValid) return;

    isLoading.value = true;

    try {
        const payload = {
            note: form.note,
            delivery_method: form.deliveryMethod,
            payment_method: form.paymentMethod,
            customer_name: form.customerName,
            customer_phone: form.customerPhone,
            customer_email: form.customerEmail,
            province: form.province?.name ?? '',
            district: form.district?.name ?? '',
            ward: form.ward?.name ?? '',
            shipping_address: form.streetAddress,
            pickup_location: form.pickupLocation?.name ?? '',
            district_id: form.districtId,
            ward_code: form.wardCode,
        };

        await axios.post(route('checkout.process'), payload); // hoặc '/checkout'

        router.visit(route('checkout.success')); // hoặc window.location = ...
    } catch (error) {
        if (error.response?.status === 422) {
            Object.assign(errors, error.response.data.errors || {});
        } else {
            toast.add({ severity: 'error', summary: 'Checkout', detail: 'Có lỗi xảy ra, vui lòng thử lại.' });
        }
    } finally {
        isLoading.value = false;
    }
};
</script>

<style scoped>
/* --- TRANSITIONS --- */
.transition-all {
    transition: all 0.3s ease;
}

.cursor-pointer {
    cursor: pointer;
}

.hover-underline:hover {
    text-decoration: underline !important;
}

/* Hiệu ứng Fade in khi chuyển tab */
.animate-fade {
    animation: fadeIn 0.4s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* --- OVERRIDES CHO PRIMEVUE TRONG BOOTSTRAP --- */
/* Đảm bảo dropdown width 100% */
:deep(.p-dropdown) {
    width: 100%;
    border-radius: 0.375rem;
    /* Chuẩn Bootstrap */
    border-color: #dee2e6;
}

:deep(.p-inputtext) {
    border-radius: 0.375rem;
    border-color: #dee2e6;
}

/* Focus state giống Bootstrap */
:deep(.p-inputtext:enabled:focus),
:deep(.p-dropdown:not(.p-disabled).p-focus) {
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    border-color: #86b7fe;
}

/* Payment Items Style */
.payment-item:hover {
    background-color: #f8f9fa;
    border-color: #0d6efd !important;
}

.bg-primary-subtle {
    background-color: #cfe2ff !important;
}

/* Responsive */
@media (max-width: 991px) {
    .checkout-summary {
        position: static !important;
        /* Tắt sticky trên mobile */
    }
}
</style>