<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Swal from 'sweetalert2';
import '@/../css/Auth/auth.css';

const form = useForm({
    name: '',
    email: '',
    password: '',
    confirm_password: '',
    phone: '',
    address: '',
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

    const hasUpperCase = /^[A-Z]/.test(form.password);
    const hasLowerCase = /[a-z]/.test(form.password);
    const hasNumber = /[0-9]/.test(form.password);

    if (!hasUpperCase || !hasLowerCase || !hasNumber) {
        return 'Mật khẩu phải bắt đầu bằng chữ hoa, kèm chữ thường và số.';
    }
    return null;
});

const passwordStrength = computed(() => {
    let score = 0;
    if (!form.password) return { level: 0, color: '#e5e7eb', text: '' };

    if (form.password.length >= 8) score++;
    if (/[A-Z]/.test(form.password) && /[a-z]/.test(form.password)) score++;
    if (/[0-9]/.test(form.password)) score++;
    if (/[^A-Za-z0-9]/.test(form.password)) score++;

    if (score <= 1) return { level: 1, color: '#ef4444', text: 'Yếu' };
    if (score === 2 || score === 3) return { level: 2, color: '#fbbf24', text: 'Trung bình' };
    return { level: 3, color: '#10b981', text: 'Mạnh' };
});

const phoneError = computed(() => {
    if (!form.phone) return null;
    const phoneRegex = /^[0-9]{10,11}$/;
    if (!phoneRegex.test(form.phone)) return 'Số điện thoại phải có 10-11 chữ số.';
    return null;
});

const confirmPasswordError = computed(() => {
    if (!form.confirm_password) return null;
    if (form.confirm_password !== form.password) return 'Mật khẩu xác nhận không khớp.';
    return null;
});

const isFormValid = computed(() => {
    return form.name && form.email && !emailError.value &&
        form.password && !passwordError.value &&
        form.confirm_password && !confirmPasswordError.value &&
        !phoneError.value;
});

const submit = () => {
    if (!isFormValid.value) return;

    isProcessing.value = true;
    form.post(route('register'), {
        onFinish: () => {
            isProcessing.value = false;
        },
        onError: (errors) => {
            let message = 'Đã có lỗi xảy ra. Vui lòng kiểm tra lại.';
            if (errors.email) message = errors.email;
            if (errors.password) message = errors.password;

            Swal.fire({
                icon: 'error',
                title: 'Lỗi đăng ký',
                text: message,
            });
        }
    });
};
</script>

<template>

    <Head title="Đăng ký tài khoản" />

    <div class="login-layout" style="margin-top: -77px;">
        <div class="login-container login-container--register">
            <div class="text-center mb-4">
                <Link href="/">
                <img :src="'/images/logo.png'" alt="Pharma Logo"
                    style="height: 150px; width: auto; object-fit: contain; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));">
                </Link>
            </div>

            <h2 class="login-title">Đăng ký</h2>
            <p class="login-subtitle">
                Tham gia cùng chúng tôi ngay hôm nay
            </p>

            <form @submit.prevent="submit">
                <div class="form-group mb-2">
                    <label for="name" class="form-label">Họ và tên</label>
                    <input type="text" v-model="form.name" class="form-control"
                        :class="{ 'is-invalid': form.errors.name }" id="name" placeholder="Nhập họ và tên" required
                        autofocus>
                    <div v-if="form.errors.name" class="invalid-feedback">{{ form.errors.name }}</div>
                </div>

                <div class="form-group mb-2">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" v-model="form.email" class="form-control"
                        :class="{ 'is-invalid': emailError || form.errors.email }" id="email"
                        placeholder="Nhập email của bạn" required>
                    <div v-if="emailError" class="invalid-feedback">{{ emailError }}</div>
                    <div v-else-if="form.errors.email" class="invalid-feedback">{{ form.errors.email }}</div>
                </div>

                <div class="row gx-2">
                    <div class="col-6">
                        <div class="form-group mb-2">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" v-model="form.password" class="form-control"
                                :class="{ 'is-invalid': passwordError || form.errors.password }" id="password"
                                placeholder="8+ ký tự" required>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group mb-2">
                            <label for="confirm_password" class="form-label">Xác nhận</label>
                            <input type="password" v-model="form.confirm_password" class="form-control"
                                :class="{ 'is-invalid': confirmPasswordError || form.errors.confirm_password }"
                                id="confirm_password" placeholder="Nhập lại" required>
                        </div>
                    </div>
                </div>

                <!-- Password Strength Bars -->
                <div class="strength-meter mb-2">
                    <div class="strength-bars">
                        <div class="bar"
                            :style="{ backgroundColor: passwordStrength.level >= 1 ? passwordStrength.color : '#e5e7eb' }">
                        </div>
                        <div class="bar"
                            :style="{ backgroundColor: passwordStrength.level >= 2 ? passwordStrength.color : '#e5e7eb' }">
                        </div>
                        <div class="bar"
                            :style="{ backgroundColor: passwordStrength.level >= 3 ? passwordStrength.color : '#e5e7eb' }">
                        </div>
                    </div>
                    <span class="strength-text" :style="{ color: passwordStrength.color }">{{ passwordStrength.text
                        }}</span>
                </div>
                <div v-if="passwordError" class="invalid-feedback d-block mb-2">{{ passwordError }}</div>
                <div v-if="confirmPasswordError" class="invalid-feedback d-block mb-2">{{ confirmPasswordError }}</div>

                <div class="row gx-2">
                    <div class="col-6">
                        <div class="form-group mb-2">
                            <label for="phone" class="form-label">Số ĐT <small class="text-muted">(ko bắt
                                    buộc)</small></label>
                            <input type="tel" v-model="form.phone" class="form-control"
                                :class="{ 'is-invalid': phoneError || form.errors.phone }" id="phone"
                                placeholder="09xx...">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group mb-2">
                            <label for="address" class="form-label">Địa chỉ <small class="text-muted">(ko bắt
                                    buộc)</small></label>
                            <input type="text" v-model="form.address" class="form-control" id="address"
                                placeholder="Nhập địa chỉ">
                        </div>
                    </div>
                </div>
                <div v-if="phoneError" class="invalid-feedback d-block mb-2">{{ phoneError }}</div>

                <button type="submit" class="btn btn-primary w-100 mt-2 mb-3" :disabled="!isFormValid || isProcessing">
                    <span v-if="isProcessing"><i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...</span>
                    <span v-else>Đăng ký ngay</span>
                </button>
            </form>

            <div class="text-center">
                <p class="footer-text">
                    Đã có tài khoản?
                    <Link :href="route('login')" class="footer-link">Đăng nhập</Link>
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
</style>
