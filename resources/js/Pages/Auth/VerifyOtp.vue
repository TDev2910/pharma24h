<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import Swal from 'sweetalert2';
import '@/../css/Auth/auth.css';

const props = defineProps({
    email: String,
});

const form = useForm({
    email: props.email || '',
    otp: '',
});

const otpInputs = ref(['', '', '', '', '', '']);
const inputs = ref([]);

const handleInput = (index, event) => {
    const val = event.target.value;
    if (val.length > 1) {
        otpInputs.value[index] = val.slice(-1);
    }
    
    // Move to next input
    if (val && index < 5) {
        inputs.value[index + 1].focus();
    }
    
    updateOtpValue();
};

const handleKeyDown = (index, event) => {
    if (event.key === 'Backspace' && !otpInputs.value[index] && index > 0) {
        inputs.value[index - 1].focus();
    }
};

const updateOtpValue = () => {
    form.otp = otpInputs.value.join('');
};

const submit = () => {
    if (form.otp.length !== 6) {
        Swal.fire({
            icon: 'warning',
            title: 'Chú ý',
            text: 'Vui lòng nhập đủ 6 chữ số mã OTP.',
        });
        return;
    }

    form.post(route('password.verify.post'), {
        onError: (errors) => {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi xác thực',
                text: errors.otp || 'Mã OTP không chính xác hoặc đã hết hạn.',
            });
        }
    });
};

const resendOtp = () => {
    // Luồng gửi lại OTP
    const resendForm = useForm({ email: form.email });
    resendForm.post(route('password.email'), {
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: 'Thành công',
                text: 'Mã OTP mới đã được gửi!',
            });
        }
    });
};

onMounted(() => {
    if (inputs.value[0]) inputs.value[0].focus();
});
</script>

<template>
    <Head title="Xác thực OTP" />

    <div class="login-layout">
        <div class="login-container login-container--password-reset">
            <div class="text-center mb-4">
                <div class="logo-circle">
                    <span class="logo-text">Pharma24h</span>
                </div>
            </div>

            <h2 class="login-title">Xác nhận OTP</h2>
            <p class="login-subtitle">
                Mã xác thực đã được gửi đến email: <br>
                <strong>{{ email }}</strong>
            </p>

            <form @submit.prevent="submit">
                <div class="otp-container d-flex justify-content-between mb-4">
                    <input v-for="(digit, index) in 6" :key="index"
                        type="text"
                        maxlength="1"
                        v-model="otpInputs[index]"
                        @input="handleInput(index, $event)"
                        @keydown="handleKeyDown(index, $event)"
                        ref="inputs"
                        class="form-control text-center mx-1 otp-input"
                        style="width: 45px; height: 55px; font-size: 1.5rem; font-weight: bold;"
                    >
                </div>

                <div v-if="form.errors.otp" class="text-danger text-center mb-3">
                    {{ form.errors.otp }}
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-4" :disabled="form.processing">
                    <span v-if="form.processing"><i class="fas fa-spinner fa-spin me-2"></i>Đang xác thực...</span>
                    <span v-else>Xác nhận</span>
                </button>
            </form>

            <div class="text-center">
                <p class="footer-text">
                    Không nhận được mã?
                    <a href="#" @click.prevent="resendOtp" class="footer-link">Gửi lại mã</a>
                </p>
                <p class="footer-text mt-2">
                    <Link :href="route('password.request')" class="text-muted small">Thay đổi email</Link>
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.otp-input:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
</style>
