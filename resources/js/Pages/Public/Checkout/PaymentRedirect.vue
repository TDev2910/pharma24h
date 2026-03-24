<template>
    <div class="payment-redirect-container">
        <div class="loader-box">
            <div class="spinner"></div>
            <h3>{{ message }}</h3>
            <p>Vui lòng không đóng trình duyệt hoặc bấm nút quay lại.</p>
            <p class="amount">Số tiền: {{ formatPrice(amount) }}</p>
        </div>

        <!-- Form ẩn tự động submit -->
        <form ref="paymentForm" :action="action_url" method="POST" style="display: none">
            <input 
                v-for="(value, key) in fields" 
                :key="key" 
                type="hidden" 
                :name="key" 
                :value="value"
            >
        </form>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
    action_url: String,
    fields: Object,
    amount: [Number, String],
    message: String
});

const paymentForm = ref(null);

const formatPrice = (value) => {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(value);
};

onMounted(() => {
    // Tự động submit form sau 500ms để người dùng kịp thấy thông báo
    setTimeout(() => {
        if (paymentForm.value) {
            paymentForm.value.submit();
        }
    }, 500);
});
</script>

<style scoped>
.payment-redirect-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background-color: #f8f9fa;
    font-family: 'Inter', sans-serif;
}

.loader-box {
    text-align: center;
    padding: 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3498db;
    border-radius: 50%;
    margin: 0 auto 1.5rem;
    animation: spin 1s linear infinite;
}

h3 {
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

p {
    color: #7f8c8d;
    font-size: 0.9rem;
}

.amount {
    margin-top: 1rem;
    font-weight: bold;
    color: #e74c3c;
    font-size: 1.1rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
