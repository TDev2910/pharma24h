<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Swal from 'sweetalert2';
import '@/../css/Auth/auth.css';
import '@/library/firebaseGoogleAuth';

const props = defineProps({
    status: String,
    success: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const isProcessing = ref(false);

// Real-time validation using computed
const emailError = computed(() => {
    if (!form.email) return null;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(form.email)) return 'Địa chỉ email không hợp lệ.';
    return null;
});

const passwordError = computed(() => {
    if (!form.password) return null;
    if (form.password.length < 8) return 'Mật khẩu phải có ít nhất 8 ký tự.';
    return null;
});

const isFormValid = computed(() => {
    return form.email && !emailError.value && form.password && !passwordError.value;
});

const submit = () => {
    if (!isFormValid.value) return;

    isProcessing.value = true;
    form.post(route('login'), {
        onFinish: () => {
            isProcessing.value = false;
            form.reset('password');
        },
        onError: (errors) => {
            if (errors.email) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi đăng nhập',
                    text: errors.email,
                });
            }
        }
    });
};

const handleGoogleLogin = async () => {
    isProcessing.value = true;
    try {
        // Kiểm tra xem Service có tồn tại trong window không (do load từ script ngoài)
        if (typeof window.FirebaseGoogleAuthService === 'undefined') {
            throw new Error('Dịch vụ Google chưa tải xong. Vui lòng tải lại trang.');
        }

        const result = await window.FirebaseGoogleAuthService.signInWithGoogle();

        if (!result.success) {
            throw new Error(result.message || 'Đăng nhập Google thất bại');
        }

        // Gửi thông tin lên backend qua Inertia router
        // Ta dùng route('auth.google') như trong web.php
        const { router } = await import('@inertiajs/vue3');
        router.post(route('auth.google'), {
            idToken: result.idToken,
            uid: result.user.uid,
            email: result.user.email,
            name: result.user.name,
            photoURL: result.user.photoURL || '',
        }, {
            onFinish: () => {
                isProcessing.value = false;
            },
            onError: (errors) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi đăng nhập Google',
                    text: Object.values(errors)[0] || 'Có lỗi xảy ra',
                });
            }
        });

    } catch (error) {
        isProcessing.value = false;
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: error.message,
        });
    }
};
</script>

<template>

    <Head title="Đăng nhập" />

    <div class="login-layout">
        <div class="login-container login-container--login">
            <div class="text-center mb-4">
                <div class="logo-circle">
                    <span class="logo-text">Pharma24h</span>
                </div>
            </div>

            <h2 class="login-title">Đăng nhập vào tài khoản</h2>
            <p class="login-subtitle">
                Chào mừng bạn quay trở lại! Vui lòng nhập thông tin của bạn
            </p>

            <div v-if="success" class="alert alert-success mb-3">
                <i class="fas fa-check-circle me-2"></i>{{ success }}
            </div>

            <div v-if="form.errors.email" class="alert alert-danger mb-3">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ form.errors.email }}
            </div>

            <form @submit.prevent="submit">
                <div class="form-group mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" v-model="form.email" class="form-control"
                        :class="{ 'is-invalid': emailError || form.errors.email }" id="email" placeholder="Nhập email"
                        required autofocus>
                    <div v-if="emailError" class="invalid-feedback">{{ emailError }}</div>
                </div>

                <div class="form-group mb-3">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" v-model="form.password" class="form-control"
                        :class="{ 'is-invalid': passwordError || form.errors.password }" id="password"
                        placeholder="Nhập mật khẩu" required>
                    <div v-if="passwordError" class="invalid-feedback">{{ passwordError }}</div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input type="checkbox" v-model="form.remember" class="form-check-input" id="remember">
                        <label class="form-check-label" for="remember">
                            Ghi nhớ tôi
                        </label>
                    </div>
                    <Link :href="route('password.request')" class="forgot-password-link">
                        Quên mật khẩu?
                    </Link>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-4" :disabled="!isFormValid || isProcessing">
                    <span v-if="isProcessing"><i class="fas fa-spinner fa-spin me-2"></i>Đang đăng nhập...</span>
                    <span v-else>Đăng nhập</span>
                </button>

                <div class="text-center my-4">
                    <span class="text-muted">Hoặc</span>
                </div>

                <button type="button" class="btn btn-outline-danger w-100 mb-3" @click="handleGoogleLogin">
                    <svg width="20" height="20" viewBox="0 0 18 18" style="margin-right: 12px;">
                        <path fill="#4285F4"
                            d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z" />
                        <path fill="#34A853"
                            d="M9 18c2.43 0 4.467-.806 5.96-2.186l-2.908-2.258c-.806.54-1.837.86-3.052.86-2.347 0-4.33-1.584-5.04-3.71H.957v2.331C2.438 15.983 5.482 18 9 18z" />
                        <path fill="#FBBC05"
                            d="M3.96 10.71c-.18-.54-.282-1.117-.282-1.71s.102-1.17.282-1.71V4.96H.957C.347 6.175 0 7.55 0 9s.348 2.825.957 4.04l3.003-2.33z" />
                        <path fill="#EA4335"
                            d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0 5.482 0 2.438 2.017.957 4.96L3.96 7.29C4.67 5.163 6.653 3.58 9 3.58z" />
                    </svg>
                    Đăng nhập với Google
                </button>
            </form>

            <div class="text-center">
                <p class="footer-text">
                    Không có tài khoản?
                    <Link :href="route('register')" class="footer-link">Đăng ký</Link>
                </p>
            </div>
        </div>
    </div>
</template>
