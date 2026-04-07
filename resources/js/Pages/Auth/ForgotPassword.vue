<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Swal from 'sweetalert2';
import '@/../css/Auth/auth.css';

const props = defineProps({
    status: String,
    success: String,
});

const form = useForm({
    email: '',
});

const isProcessing = ref(false);

const emailError = computed(() => {
    if (!form.email) return null;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(form.email)) return 'Địa chỉ email không hợp lệ.';
    return null;
});

const isFormValid = computed(() => {
    return form.email && !emailError.value;
});

const submit = () => {
    if (!isFormValid.value) return;

    isProcessing.value = true;
    form.post(route('password.email'), {
        onFinish: () => {
            isProcessing.value = false;
        },
        onError: (errors) => {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: errors.email || 'Có lỗi xảy ra, vui lòng thử lại.',
            });
        }
    });
};
</script>

<template>

    <Head title="Quên mật khẩu" />

    <div class="login-layout" style="margin-top: -77px;">
        <div class="login-container login-container--password-reset">
            <div class="text-center mb-4">
                <Link href="/">
                <img :src="'/images/logo.png'" alt="Pharma Logo"
                    style="height: 150px; width: auto; object-fit: contain; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));">
                </Link>
            </div>

            <h2 class="login-title">Quên mật khẩu?</h2>
            <p class="login-subtitle">
                Nhập địa chỉ email của bạn để nhận mã OTP khôi phục mật khẩu.
            </p>

            <div v-if="success" class="alert alert-success mb-3">
                <i class="fas fa-check-circle me-2"></i>{{ success }}
            </div>

            <div v-if="form.errors.email" class="alert alert-danger mb-3">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ form.errors.email }}
            </div>

            <form @submit.prevent="submit">
                <div class="form-group mb-4">
                    <label for="email" class="form-label">Email tài khoản</label>
                    <input type="email" v-model="form.email" class="form-control"
                        :class="{ 'is-invalid': emailError || form.errors.email }" id="email"
                        placeholder="example@gmail.com" required autofocus>
                    <div v-if="emailError" class="invalid-feedback">{{ emailError }}</div>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-4" :disabled="!isFormValid || isProcessing">
                    <span v-if="isProcessing"><i class="fas fa-spinner fa-spin me-2"></i>Đang gửi OTP...</span>
                    <span v-else>Gửi mã OTP</span>
                </button>
            </form>

            <div class="text-center">
                <p class="footer-text">
                    Quay lại
                    <Link :href="route('login')" class="footer-link">Đăng nhập</Link>
                </p>
            </div>
        </div>
    </div>
</template>
