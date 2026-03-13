<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Swal from 'sweetalert2';

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
    // Logic cho Google Login sẽ được tích hợp sau hoặc dùng library có sẵn
    Swal.fire({
        title: 'Thông báo',
        text: 'Tính năng đăng nhập Google đang được tích hợp lại cho Vue.',
        icon: 'info'
    });
};
</script>

<template>

    <Head title="Đăng nhập" />

    <div class="login-layout">
        <div class="login-container">
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
                    <!-- <Link :href="route('password.request')" class="forgot-password-link">
                        Quên mật khẩu?
                    </Link> -->
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-4" :disabled="!isFormValid || isProcessing">
                    <span v-if="isProcessing"><i class="fas fa-spinner fa-spin me-2"></i>Đang đăng nhập...</span>
                    <span v-else>Đăng nhập</span>
                </button>

                <div class="text-center my-4">
                    <span class="text-muted">Hoặc</span>
                </div>

                <button type="button" class="btn btn-outline-danger w-100 mb-3" @click="handleGoogleLogin">
                    <svg width="18" height="18" viewBox="0 0 18 18" style="margin-right: 8px;">
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
                <!-- <p class="footer-text">
                    Không có tài khoản?
                    <Link :href="route('register')" class="footer-link">Đăng ký</Link>
                </p> -->
            </div>
        </div>
    </div>
</template>

<style scoped>
.login-layout {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background-color: #f3f4f6;
}

.login-container {
    width: 400px;
    max-width: 90vw;
    padding: 40px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.logo-circle {
    width: 100px;
    height: 100px;
    background: #2b2e33;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}

.logo-text {
    color: white;
    font-weight: 700;
    font-size: 16px;
    letter-spacing: -0.5px;
}

.login-title {
    font-size: 28px;
    font-weight: 600;
    color: #1a1a1a;
    text-align: center;
    margin-bottom: 16px;
    letter-spacing: -0.5px;
}

.login-subtitle {
    font-size: 14px;
    color: #6b7280;
    text-align: center;
    margin-bottom: 32px;
    line-height: 1.5;
}

.form-label {
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    display: block;
}

.form-control {
    width: 100%;
    height: 48px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 16px;
    padding: 12px 16px;
    transition: all 0.2s ease;
    background: #f9fafb;
    box-sizing: border-box;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    background: white;
    outline: none;
}

.form-control.is-invalid {
    border-color: #ef4444;
}

.form-check {
    display: flex;
    align-items: center;
}

.form-check-input {
    margin-right: 8px;
    width: 16px;
    height: 16px;
}

.form-check-label {
    font-size: 14px;
    color: #374151;
    cursor: pointer;
}

.forgot-password-link {
    font-size: 14px;
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s ease;
}

.forgot-password-link:hover {
    color: #5a67d8;
    text-decoration: none;
}

.btn-primary {
    height: 48px;
    background: #4a5568;
    border: none;
    border-radius: 24px;
    font-size: 16px;
    font-weight: 600;
    color: white;
    transition: all 0.2s ease;
    letter-spacing: 0.025em;
    cursor: pointer;
}

.btn-primary:hover {
    background: #2b2e33;
    transform: translateY(-1px);
    box-shadow: 0 8px 25px rgba(74, 85, 104, 0.3);
}

.btn-primary:disabled {
    background: #9ca3af;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.footer-text {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 8px;
}

.footer-link {
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s ease;
}

.footer-link:hover {
    color: #5a67d8;
    text-decoration: none;
}

.alert {
    border: none;
    border-radius: 8px;
    font-size: 14px;
    padding: 12px 16px;
}

.alert-danger {
    background: #fef2f2;
    color: #dc2626;
    border-left: 4px solid #dc2626;
}

.alert-success {
    background: #f0fdf4;
    color: #16a34a;
    border-left: 4px solid #16a34a;
}

.invalid-feedback {
    font-size: 12px;
    color: #ef4444;
    margin-top: 4px;
}

.btn-outline-danger {
    height: 48px;
    border: 2px solid #ea4335;
    border-radius: 24px;
    font-size: 16px;
    font-weight: 600;
    color: #ea4335;
    background: white;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.btn-outline-danger:hover {
    background: #ea4335;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 8px 25px rgba(234, 67, 53, 0.3);
}

@media (max-width: 480px) {
    .login-container {
        padding: 24px;
        width: 100%;
        margin: 20px;
    }

    .login-title {
        font-size: 24px;
        margin-bottom: 24px;
    }
}
</style>
