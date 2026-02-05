<template>
    <PublicLayout>
        <div class="cart-wrapper">
            <div class="container py-5 mt-5">
                <div class="row g-4">
                    <!-- Left Column: Cart Items -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4">
                            <!-- Header -->
                            <div
                                class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <Checkbox v-model="selectAll" :binary="true" @change="toggleSelectAll" />
                                    <h5 class="mb-0 fw-bold text-dark fs-6">{{ selectedItems.length }} sản phẩm đã chọn
                                    </h5>
                                </div>
                                <button class="btn text-danger fw-medium btn-sm d-flex align-items-center gap-2"
                                    @click="removeAll" :disabled="cartItems.length === 0">
                                    <i class="pi pi-trash"></i> Xóa tất cả
                                </button>
                            </div>

                            <!-- Cart Items List -->
                            <div class="card-body p-0">
                                <template v-if="cartItems.length > 0">
                                    <div v-for="item in cartItems" :key="item.id"
                                        class="cart-item p-3 border-bottom position-relative">
                                        <div class="d-flex align-items-center gap-3">
                                            <!-- Checkbox -->
                                            <div class="checkbox-wrapper">
                                                <Checkbox v-model="selectedItems" :value="item.id" />
                                            </div>

                                            <!-- Image -->
                                            <div class="product-img rounded-3 border">
                                                <img :src="getImageUrl(item.image)" :alt="item.name"
                                                    class="w-100 h-100 object-fit-cover">
                                            </div>

                                            <!-- Info wrapper with split columns -->
                                            <div
                                                class="flex-grow-1 d-flex flex-column flex-md-row justify-content-between ms-3 gap-3">

                                                <!-- Left Side: Name, Badge, Unit Price -->
                                                <div class="d-flex flex-column justify-content-between flex-grow-1"
                                                    style="min-width: 0;">
                                                    <div>
                                                        <h6 class="mb-1 fw-bold product-name text-truncate-2">{{
                                                            item.name }}</h6>
                                                        <span
                                                            class="badge bg-light text-primary border border-primary-subtle rounded-pill px-2 py-1 fs-xs mb-2 d-inline-block">
                                                            {{ item.item_type === 'medicine' ? 'Thuốc' : 'Sản phẩm khác'
                                                            }}
                                                        </span>
                                                    </div>
                                                    <div class="price fw-bold text-primary mt-auto">{{
                                                        formatCurrency(item.price) }}</div>
                                                </div>

                                                <!-- <div class="d-flex flex-column align-items-end justify-content-between ms-auto"
                                                    style="flex-shrink: 0;">

                                                    <button class="btn btn-sm text-danger p-0 border-0"
                                                        title="Xóa sản phẩm" @click="removeItem(item)">
                                                        <i class="pi pi-trash"></i>
                                                    </button>

                                                    <div
                                                        class="quantity-control d-flex align-items-center bg-white rounded-pill border px-1 shadow-sm my-2">
                                                        <button class="btn btn-sm btn-icon rounded-circle"
                                                            @click="updateQuantity(item, -1)"
                                                            :disabled="item.quantity <= 1">
                                                            −
                                                        </button>
                                                        <input type="text"
                                                            class="form-control border-0 bg-transparent text-center p-0 fw-bold"
                                                            style="width: 32px; font-size: 14px;" :value="item.quantity"
                                                            readonly>
                                                        <button class="btn btn-sm btn-icon rounded-circle"
                                                            @click="updateQuantity(item, 1)"
                                                            :disabled="item.quantity >= 99">
                                                            +
                                                        </button>
                                                    </div>

                                                    <div class="d-block fw-bold fs-6 text-dark">{{
                                                        formatCurrency(item.price * item.quantity) }}</div>
                                                </div> -->
                                                <!-- CONTROL GẮN LÊN ẢNH -->
                                                <div class="cart-item-control">
                                                    <button class="btn btn-sm text-danger p-0 border-0"
                                                        title="Xóa sản phẩm" @click="removeItem(item)">
                                                        <i class="pi pi-trash"></i>
                                                    </button>

                                                    <div
                                                        class="quantity-control d-flex align-items-center bg-white rounded-pill border px-1 shadow-sm my-2">
                                                        <button class="btn btn-sm btn-icon rounded-circle"
                                                            @click="updateQuantity(item, -1)"
                                                            :disabled="item.quantity <= 1">−</button>

                                                        <input type="text"
                                                            class="form-control border-0 bg-transparent text-center p-0 fw-bold"
                                                            style="width: 32px; font-size: 14px;" :value="item.quantity"
                                                            readonly>

                                                        <button class="btn btn-sm btn-icon rounded-circle"
                                                            @click="updateQuantity(item, 1)"
                                                            :disabled="item.quantity >= 99">+</button>
                                                    </div>

                                                    <div class="fw-bold text-dark fs-6">
                                                        {{ formatCurrency(item.price * item.quantity) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="text-center py-5">
                                        <i class="pi pi-shopping-cart text-muted mb-3" style="font-size: 3rem;"></i>
                                        <h5 class="text-muted">Giỏ hàng trống</h5>
                                        <a href="/" class="btn btn-outline-primary mt-2 rounded-pill px-4">Tiếp tục mua
                                            sắm</a>
                                    </div>
                                </template>
                            </div>

                            <!-- Bottom Action -->
                            <div class="card-footer bg-white border-0 py-3">
                                <a href="/"
                                    class="text-decoration-none text-primary fw-medium d-inline-flex align-items-center gap-2">
                                    <i class="pi pi-arrow-left"></i> Tiếp tục mua sắm
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Summary -->
                    <div class="col-lg-4">
                        <!-- Coupon -->
                        <div class="card border-0 shadow-sm rounded-4 mb-3">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3"><i class="pi pi-ticket me-2 text-primary"></i>Mã giảm giá</h6>
                                <div class="d-flex gap-2">
                                    <input type="text" class="form-control rounded-3"
                                        style="background-color: #f8fbff; border-color: #dae1e7; color: #333;"
                                        placeholder="Nhập mã giảm giá">
                                    <button class="btn btn-primary rounded-3 px-3 fw-bold text-nowrap"
                                        style="background-color: #0d6efd !important; color: white !important;font-size: 15px;">
                                        Áp dụng
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Price Info -->
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3"><i class="pi pi-wallet me-2 text-primary"></i>Chi tiết giá</h6>
                                <div class="d-flex justify-content-between mb-2 text-secondary">
                                    <span>{{ totalItems }} sản phẩm</span>
                                    <span>{{ formatCurrency(subtotal) }}</span>
                                </div>
                                <hr class="border-secondary-subtle">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <span class="fw-bold fs-6">Tổng thanh toán</span>
                                    <span class="fw-bold text-primary fs-5">{{ formatCurrency(subtotal) }}</span>
                                </div>

                                <button style="background-color: #0d6efd !important; color: white"
                                    class="btn btn-primary w-100 rounded-3 py-3 fw-bold fs-6 d-flex align-items-center justify-content-center gap-2 shadow-sm"
                                    @click="checkout" :disabled="selectedItems.length === 0">
                                    <i></i> Thanh toán
                                </button>
                                <p class="text-center text-muted mt-3" style="font-size: 11px;">
                                    Bằng việc tiến hành thanh toán, bạn đồng ý với Điều khoản dịch vụ của chúng tôi
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import Checkbox from 'primevue/checkbox';
import axios from 'axios';

// Props
const props = defineProps({
    cartData: {
        type: Object,
        default: () => ({ items: [], count: 0, total: 0 }),
    },
});

// State
const cartItems = ref([]);
const selectedItems = ref([]);
const selectAll = ref(true);

const toast = {
    add: (msg) => console.log(msg)
};

onMounted(() => {
    if (props.cartData && props.cartData.items) {
        cartItems.value = props.cartData.items.map(item => ({
            ...item,
            quantity: Number(item.quantity),
            price: Number(item.price)
        }));
        // Select all by default
        selectedItems.value = cartItems.value.map(i => i.id);
    }
});

// --- Computed ---
const totalItems = computed(() => selectedItems.value.length);

const subtotal = computed(() => {
    return cartItems.value.reduce((total, item) => {
        if (selectedItems.value.includes(item.id)) {
            return total + (item.price * item.quantity);
        }
        return total;
    }, 0);
});


// Image Helper
const getImageUrl = (path) => {
    if (!path) return 'https://placehold.co/80x80';
    if (path.startsWith('http')) return path;
    const cleanPath = path.startsWith('/') ? path.slice(1) : path;
    const prefix = cleanPath.startsWith('storage/') ? '/' : '/storage/';
    return `${prefix}${cleanPath}`;
};

// Currency Helper
const formatCurrency = (value) => {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
};

// Sync Quantity
const updateQuantity = async (item, change) => {
    const newQty = item.quantity + change;
    if (newQty < 1 || newQty > 99) return;

    // Optimistic Update
    const oldQty = item.quantity;
    item.quantity = newQty;

    try {
        await axios.post('/cart/update', {
            cart_id: item.id,
            quantity: newQty
        });
    } catch (error) {
        // Revert on fail
        item.quantity = oldQty;
        console.error('Update failed', error);
    }
};

// Select All Logic
const toggleSelectAll = () => {
    if (selectedItems.value.length === cartItems.value.length) {
        // Deselect all
        selectedItems.value = [];
    } else {
        // Select all
        selectedItems.value = cartItems.value.map(i => i.id);
    }
};

// Watch for individual checkbox changes to update "Select All" state
watch(selectedItems, (newVal) => {
    if (cartItems.value.length > 0) {
        selectAll.value = newVal.length === cartItems.value.length;
    }
});

// Remove Item
const removeItem = async (item) => {
    if (!confirm('Xóa sản phẩm này?')) return;

    // Optimistic UI
    cartItems.value = cartItems.value.filter(i => i.id !== item.id);
    selectedItems.value = selectedItems.value.filter(id => id !== item.id);

    try {
        await axios.post('/cart/remove', { cart_id: item.id });
    } catch (error) {
        // Reload page if desperate, or show error
        console.error('Remove failed', error);
    }
};

// Remove All
const removeAll = async () => {
    if (!confirm('Bạn có chắc muốn xóa tất cả sản phẩm?')) return;

    const itemsToDelete = [...cartItems.value];
    cartItems.value = []; 
    selectedItems.value = [];
    try {
        await Promise.all(itemsToDelete.map(item =>
            axios.post('/cart/remove', { cart_id: item.id })
        ));
    } catch (err) {
        console.error('Batch remove failed', err);
    }
};

const checkout = () => {

    window.location.href = '/checkout';
};

</script>

<style scoped src="../../../../css/Public/Cart/cart.css"></style>
