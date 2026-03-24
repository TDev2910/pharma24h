<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useForm, usePage, Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import axios from 'axios';

const props = defineProps({
    cartItems: {
        type: Array,
        default: () => []
    },
    cartTotal: {
        type: Number,
        default: 0
    },
    pharmacyLocations: {
        type: Array,
        default: () => []
    },
    routes: {
        type: Object,
        required: true
    }
});

const page = usePage();
const user = computed(() => page.props.auth ? page.props.auth.user : null);

// Form State
const form = useForm({
    delivery_method: 'shipping', // 'shipping' or 'pickup'
    customer_name: user.value?.name || '',
    customer_phone: user.value?.phone || '',
    customer_email: user.value?.email || '',

    // Address fields
    province: '',
    district: '',
    ward: '',
    shipping_address: '',

    // Hidden fields for logic
    district_id: '',
    ward_code: '',
    shipping_fee: 0,

    // Pickup fields
    pickup_location: '',

    // Note & Payment
    note: '',
    payment_method: 'cod', // 'cod' or 'vnpay'
});

// UI State
const provinces = ref([]);
const districts = ref([]);
const wards = ref([]);
const isLoadingFee = ref(false);
const agreeTerms = ref(true);

// Computed
const cartSubtotal = computed(() => props.cartTotal);
const shippingFeeDisplay = computed(() => form.shipping_fee);
const totalOrder = computed(() => cartSubtotal.value + form.shipping_fee);

// Logic API & GHN (Ported from Blade script)
const formatCurrency = (amount) => {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
};

// Province API
const loadProvinces = async () => {
    try {
        const response = await fetch('https://provinces.open-api.vn/api/?depth=1');
        provinces.value = await response.json();
    } catch (error) {
        console.error('Lỗi tải danh sách tỉnh/thành:', error);
    }
};

const loadDistricts = async (provinceCode) => {
    try {
        const response = await fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`);
        const data = await response.json();
        districts.value = data.districts;
        wards.value = []; // Reset wards
    } catch (error) {
        console.error('Lỗi tải quận/huyện:', error);
    }
};

const loadWards = async (districtCode) => {
    try {
        const response = await fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`);
        const data = await response.json();
        wards.value = data.wards;
    } catch (error) {
        console.error('Lỗi tải phường/xã:', error);
    }
};

// Handle Changes
const onProvinceChange = () => {
    form.district = '';
    form.ward = '';
    form.district_id = '';
    form.ward_code = '';
    form.shipping_fee = 0;

    const selectedProvince = provinces.value.find(p => p.name === form.province);
    if (selectedProvince) {
        loadDistricts(selectedProvince.code);
    } else {
        districts.value = [];
        wards.value = [];
    }
};

const onDistrictChange = () => {
    form.ward = '';
    form.ward_code = '';
    // Does not reset district_id immediately, waiting for mapToGHNIds? 
    // In Blade: district change -> loadWards, reset ward_code.

    const selectedDistrict = districts.value.find(d => d.name === form.district);
    if (selectedDistrict) {
        loadWards(selectedDistrict.code);
    } else {
        wards.value = [];
    }
};

const onWardChange = async () => {
    if (form.province && form.district && form.ward) {
        await mapToGHNIds(form.province, form.district, form.ward);
    }
};

// Map Address to GHN IDs
const mapToGHNIds = async (provinceName, districtName, wardName) => {
    // Avoid double calling if params are missing
    if (!provinceName || !districtName || !wardName) return;

    try {
        const response = await axios.post(props.routes.ghn_map_address, {
            province: provinceName,
            district: districtName,
            ward: wardName
        });

        if (response.data.success && response.data.data) {
            const { district_id, ward_code } = response.data.data;
            form.district_id = district_id;
            form.ward_code = ward_code;

            calculateShippingFee(district_id, ward_code);
        }
    } catch (error) {
        console.error('Lỗi map địa chỉ:', error);
    }
};

// Calculate Shipping Fee
const calculateShippingFee = async (districtId, wardCode) => {
    isLoadingFee.value = true;
    try {
        const response = await axios.post(props.routes.get_shipping_fee, {
            district_id: districtId,
            ward_code: wardCode
        });

        if (response.data.success) {
            form.shipping_fee = parseInt(response.data.fee);
        } else {
            console.error('Lỗi tính phí:', response.data.message);
            // Optional: Handle error display
        }
    } catch (error) {
        console.error('Lỗi gọi API tính phí:', error);
    } finally {
        isLoadingFee.value = false;
    }
};

// Delivery Method Toggle
watch(() => form.delivery_method, (newMethod) => {
    if (newMethod === 'pickup') {
        form.shipping_fee = 0;
        // Logic for clearing required fields is handled by template v-if/v-show
    } else {
        // If switching back to shipping, re-calc fee if we have address
        if (form.district_id && form.ward_code) {
            calculateShippingFee(form.district_id, form.ward_code);
        }
    }
});

// Submit Form
const submit = () => {
    if (!agreeTerms.value) {
        alert('Vui lòng đồng ý với điều khoản & điều kiện mua hàng.');
        return;
    }

    form.post(props.routes.checkout_process, {
        preserveScroll: true,
        onError: (errors) => {
            console.log('Checkout errors:', errors);
        }
    });
};

onMounted(() => {
    loadProvinces();
});
</script>

<template>

    <Head title="Thanh toán" />

    <div class="checkout-page bg-light min-vh-100 pb-5" style="padding-top: 100px;">
        <div class="container">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb small text-muted">
                    <li class="breadcrumb-item">
                        <Link href="/" class="text-decoration-none text-muted">Trang chủ</Link>
                    </li>
                    <li class="breadcrumb-item">
                        <Link :href="props.routes.cart_index" class="text-decoration-none text-muted">Giỏ hàng
                        </Link>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Thanh toán</li>
                </ol>
            </nav>

            <h2 class="h4 fw-bold text-primary mb-4">Thanh toán & Đặt hàng</h2>

            <form @submit.prevent="submit" id="checkoutForm">
                <div class="row g-4">
                    <div class="col-lg-8">

                        <!-- Delivery Method -->
                        <div class="card border-0 shadow-sm rounded-3 mb-4 section-card">
                            <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                                <h6 class="fw-bold m-0"><i class="pi pi-truck me-2 text-primary"></i>Hình thức nhận
                                    hàng</h6>
                            </div>
                            <div class="card-body px-4 pb-4">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <label class="delivery-option-card"
                                            :class="{ active: form.delivery_method === 'shipping' }">
                                            <input type="radio" v-model="form.delivery_method" value="shipping">
                                            <div class="content">
                                                <i class="pi pi-home fs-4 mb-2"></i>
                                                <span style="color:#000">Giao tận nơi</span>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <label class="delivery-option-card"
                                            :class="{ active: form.delivery_method === 'pickup' }">
                                            <input type="radio" v-model="form.delivery_method" value="pickup">
                                            <div class="content">
                                                <i class="pi pi-map-marker fs-4 mb-2"></i>
                                                <span>Nhận tại nhà thuốc</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Info -->
                        <div class="card border-0 shadow-sm rounded-3 mb-4 section-card">
                            <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                                <h6 class="fw-bold m-0"><i class="pi pi-user me-2 text-primary"></i>Thông tin chi
                                    tiết</h6>
                            </div>
                            <div class="card-body px-4 pb-4">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control"
                                                :class="{ 'is-invalid': form.errors.customer_name }" id="customer_name"
                                                placeholder="Họ tên" v-model="form.customer_name">
                                            <label for="customer_name">Họ và tên <span
                                                    class="text-danger">*</span></label>
                                            <div v-if="form.errors.customer_name" class="invalid-feedback">{{
                                                form.errors.customer_name }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control"
                                                :class="{ 'is-invalid': form.errors.customer_phone }"
                                                id="customer_phone" placeholder="SĐT" v-model="form.customer_phone">
                                            <label for="customer_phone">Số điện thoại <span
                                                    class="text-danger">*</span></label>
                                            <div v-if="form.errors.customer_phone" class="invalid-feedback">{{
                                                form.errors.customer_phone }}</div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="email" class="form-control"
                                                :class="{ 'is-invalid': form.errors.customer_email }"
                                                id="customer_email" placeholder="Email" v-model="form.customer_email">
                                            <label for="customer_email">Email (Nhận hóa đơn)</label>
                                            <div v-if="form.errors.customer_email" class="invalid-feedback">{{
                                                form.errors.customer_email }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Shipping Address (Show if Shipping) -->
                                <div v-if="form.delivery_method === 'shipping'" id="shipping_info" class="animate-fade">
                                    <div class="p-3 bg-light rounded-3 mb-3 border">
                                        <h6 class="small fw-bold text-muted mb-3 text-uppercase">Địa chỉ giao hàng
                                        </h6>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <select class="form-select py-3"
                                                    :class="{ 'is-invalid': form.errors.province }"
                                                    v-model="form.province" @change="onProvinceChange">
                                                    <option value="">Tỉnh/Thành phố</option>
                                                    <option v-for="p in provinces" :key="p.code" :value="p.name">{{
                                                        p.name }}</option>
                                                </select>
                                                <div v-if="form.errors.province" class="invalid-feedback">{{
                                                    form.errors.province }}</div>
                                            </div>
                                            <div class="col-md-4">
                                                <select class="form-select py-3"
                                                    :class="{ 'is-invalid': form.errors.district }"
                                                    v-model="form.district" @change="onDistrictChange">
                                                    <option value="">Quận/Huyện</option>
                                                    <option v-for="d in districts" :key="d.code" :value="d.name">{{
                                                        d.name }}</option>
                                                </select>
                                                <div v-if="form.errors.district" class="invalid-feedback">{{
                                                    form.errors.district }}</div>
                                            </div>
                                            <div class="col-md-4">
                                                <select class="form-select py-3"
                                                    :class="{ 'is-invalid': form.errors.ward }" v-model="form.ward"
                                                    @change="onWardChange">
                                                    <option value="">Phường/Xã</option>
                                                    <option v-for="w in wards" :key="w.code" :value="w.name">{{
                                                        w.name }}</option>
                                                </select>
                                                <div v-if="form.errors.ward" class="invalid-feedback">{{
                                                    form.errors.ward }}</div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control"
                                                        :class="{ 'is-invalid': form.errors.shipping_address }"
                                                        placeholder="Địa chỉ" v-model="form.shipping_address">
                                                    <label>Số nhà, tên đường <span class="text-danger">*</span></label>
                                                    <div v-if="form.errors.shipping_address" class="invalid-feedback">{{
                                                        form.errors.shipping_address }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pickup Info (Show if Pickup) -->
                                <div v-if="form.delivery_method === 'pickup'" id="pickup_info" class="animate-fade">
                                    <div class="alert alert-primary d-flex align-items-center mb-3">
                                        <i class="pi pi-info-circle me-2 fs-5"></i>
                                        <div class="small">Vui lòng đến lấy hàng trong vòng <strong>24h</strong> sau
                                            khi đặt.</div>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <select class="form-select"
                                            :class="{ 'is-invalid': form.errors.pickup_location }"
                                            v-model="form.pickup_location">
                                            <option value="">Chọn nhà thuốc gần bạn...</option>
                                            <option v-for="loc in pharmacyLocations"
                                                :key="loc.id || loc.message || loc.name" :value="loc.name">
                                                {{ loc.name }} - {{ loc.address }}
                                            </option>
                                        </select>
                                        <label>Địa điểm nhận hàng <span class="text-danger">*</span></label>
                                        <div v-if="form.errors.pickup_location" class="invalid-feedback">{{
                                            form.errors.pickup_location }}</div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <div class="form-floating">
                                        <textarea class="form-control" placeholder="Ghi chú" style="height: 80px"
                                            v-model="form.note"></textarea>
                                        <label>Ghi chú cho đơn hàng (không bắt buộc)</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="card border-0 shadow-sm rounded-3 mb-4 section-card">
                            <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                                <h6 class="fw-bold m-0"><i class="pi pi-wallet me-2 text-primary"></i>Phương thức
                                    thanh toán</h6>
                            </div>
                            <div class="card-body px-4 pb-4">
                                <div class="payment-methods d-flex flex-column gap-3">
                                    <label class="payment-option-card"
                                        :class="{ active: form.payment_method === 'cod' }">
                                        <input type="radio" v-model="form.payment_method" value="cod">
                                        <div class="content d-flex align-items-center p-3">
                                            <div class="icon-wrap me-3 bg-light rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 48px; height: 48px;">
                                                <img src="https://cdn-icons-png.flaticon.com/512/2331/2331941.png"
                                                    alt="COD" style="width: 28px; height: 28px; object-fit: contain;">
                                            </div>
                                            <div class="flex-fill">
                                                <div class="fw-bold text-dark">Thanh toán khi nhận hàng (COD)</div>
                                                <div class="small text-muted">Thanh toán tiền mặt cho shipper</div>
                                            </div>
                                        </div>
                                    </label>

                                    <label class="payment-option-card"
                                        :class="{ active: form.payment_method === 'vnpay' }">
                                        <input type="radio" v-model="form.payment_method" value="vnpay">
                                        <div class="content d-flex align-items-center p-3">
                                            <div class="icon-wrap me-3 bg-white rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 48px; height: 48px;">
                                                <img src="https://vnpay.vn/assets/images/logo-icon/logo-primary.svg"
                                                    alt="VNPAY" style="width: 32px; height: 32px; object-fit: contain;">
                                            </div>
                                            <div class="flex-fill">
                                                <div class="fw-bold text-dark">Thanh toán VNPAY</div>
                                                <div class="small text-muted">Quét mã QR / Thẻ ATM / Ví điện tử
                                                </div>
                                            </div>
                                        </div>
                                    </label>

                                    <!-- SePay Option -->
                                    <label class="payment-option-card"
                                        :class="{ active: form.payment_method === 'sepay' }">
                                        <input type="radio" v-model="form.payment_method" value="sepay">
                                        <div class="content d-flex align-items-center p-3">
                                            <div class="icon-wrap me-3 bg-white rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 48px; height: 48px;">
                                                <img src="https://sepay.vn/wp-content/uploads/2023/12/sepay-icon-512x512-1.png"
                                                    alt="SePay" style="width: 32px; height: 32px; object-fit: contain;">
                                            </div>
                                            <div class="flex-fill">
                                                <div class="fw-bold text-dark">Chuyển khoản QR (SePay)</div>
                                                <div class="small text-muted">Xác nhận ngay lập tức qua ứng dụng Ngân hàng</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Column -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-3 checkout-summary sticky-top"
                            style="top: 20px; z-index: 10;">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-4">Đơn hàng của bạn</h5>

                                <div class="order-scrollable mb-3" style="max-height: 300px; overflow-y: auto;">
                                    <div v-for="(item, index) in cartItems" :key="index"
                                        class="d-flex align-items-center mb-3" style="margin-top: 15px">
                                        <div class="position-relative me-3">
                                            <img v-if="item.image" :src="'/storage/' + item.image" alt="sp"
                                                class="rounded border"
                                                style="width: 50px; height: 50px; object-fit: cover;">
                                            <div v-else
                                                class="bg-light rounded border d-flex align-items-center justify-content-center"
                                                style="width: 50px; height: 50px;">
                                                <i class="pi pi-image text-muted"></i>
                                            </div>
                                            <span
                                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary"
                                                style="font-size: 0.7rem;">
                                                {{ item.quantity }}
                                            </span>
                                        </div>
                                        <div class="flex-fill" style="min-width: 0;">
                                            <div class="text-truncate fw-medium small">{{ item.product_name ||
                                                item.name }}</div>
                                            <div class="text-muted small">{{ formatCurrency(item.price || 0) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="dashed">

                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Tạm tính</span>
                                    <span class="fw-bold">{{ formatCurrency(cartSubtotal) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted">Phí vận chuyển</span>
                                    <span v-if="isLoadingFee" class="spinner-border spinner-border-sm"></span>
                                    <span v-else class="fw-bold text-success">
                                        {{ shippingFeeDisplay > 0 ? formatCurrency(shippingFeeDisplay) :
                                            (form.delivery_method === 'pickup' ? '0đ' : (shippingFeeDisplay === 0 &&
                                                form.district_id ? 'Miễn phí' : '0đ')) }}
                                    </span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center border-top pt-3 mb-4">
                                    <span class="fw-bold fs-5">Tổng cộng</span>
                                    <span class="fw-bold fs-4 text-danger">{{ formatCurrency(totalOrder) }}</span>
                                </div>

                                <div class="form-check mb-3 small">
                                    <input class="form-check-input" type="checkbox" id="agree_terms"
                                        v-model="agreeTerms">
                                    <label class="form-check-label text-muted" for="agree_terms">
                                        Tôi đồng ý với <a href="#" class="text-primary text-decoration-none">Điều
                                            khoản & Điều kiện</a> mua hàng.
                                    </label>
                                </div>

                                <button type="submit"
                                    class="btn btn-primary w-100 py-3 fw-bold text-uppercase rounded-3 shadow-sm btn-checkout"
                                    style="background: #005EB8 ; color:white" :disabled="form.processing">
                                    {{ form.processing ? 'Đang xử lý...' : 'Đặt hàng ngay' }}
                                </button>

                                <div class="text-center mt-3">
                                    <Link :href="props.routes.cart_index"
                                        class="text-decoration-none text-muted small hover-underline">
                                        <i class="pi pi-arrow-left me-1"></i> Quay lại giỏ hàng
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
/* Pharmacity Style Adjustments */
:root {
    --primary-color: #0d6efd;
    /* Blue tone */
    --bg-color: #f4f6f8;
}

/* Delivery Options Cards */
.delivery-option-card {
    display: block;
    cursor: pointer;
    position: relative;
}

.delivery-option-card input {
    display: none;
}

.delivery-option-card .content {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    text-align: center;
    background: #fff;
    transition: all 0.2s;
    color: #6c757d;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.delivery-option-card input:checked+.content,
.delivery-option-card.active .content {
    border-color: #0d6efd;
    background-color: #f0f7ff;
    color: #0d6efd;
    font-weight: 700;
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
}

/* Payment Method Cards */
.payment-option-card {
    cursor: pointer;
    display: block;
}

.payment-option-card input {
    display: none;
}

.payment-option-card .content {
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    background: #fff;
    transition: all 0.3s ease;
    position: relative;
}

.payment-option-card .content:hover {
    border-color: #0d6efd;
    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.1);
}

.payment-option-card .icon-wrap {
    flex-shrink: 0;
}

.payment-option-card .check-mark {
    color: #e0e0e0;
    transition: all 0.3s ease;
}

.payment-option-card input:checked+.content,
.payment-option-card.active .content {
    border-color: #0d6efd;
    background-color: #e7f3ff;
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
}

.payment-option-card input:checked+.content .check-mark,
.payment-option-card.active .content .check-mark {
    color: #0d6efd;
}

/* Input Fields */
.form-control,
.form-select {
    border-color: #dee2e6;
    border-radius: 6px;
}

.form-control:focus,
.form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
}

/* Summary Section */
.checkout-summary hr.dashed {
    border-top: 1px dashed #bbb;
    opacity: 1;
}

.btn-checkout {
    transition: transform 0.1s;
}

.btn-checkout:active {
    transform: scale(0.98);
}

/* Scrollbar for order items */
.order-scrollable::-webkit-scrollbar {
    width: 5px;
}

.order-scrollable::-webkit-scrollbar-thumb {
    background: #ddd;
    border-radius: 10px;
}

.animate-fade {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-5px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
