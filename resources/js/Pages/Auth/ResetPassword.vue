<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import Swal from 'sweetalert2';
import '@/../css/Auth/auth.css';

const props = defineProps({
    email: String,
    otp: String,
});

const form = useForm({
    email: props.email || '',
    otp: props.otp || '',
    password: '',
    password_confirmation: '',
});

const passwordError = computed(() => {
    if (!form.password) return null;
    if (form.password.length < 8) return 'Mật khẩu phải có ít nhất 8 ký tự.';
    return null;
});

const confirmationError = computed(() => {
    if (!form.password_confirmation) return null;
    if (form.password !== form.password_confirmation) return 'Mật khẩu xác nhận không khớp.';
    return null;
});

const isFormValid = computed(() => {
    return form.password && !passwordError.value &&
        form.password_confirmation && !confirmationError.value;
});

const submit = () => {
    if (!isFormValid.value) return;

    form.post(route('password.reset.post'), {
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: 'Thành công',
                text: 'Mật khẩu của bạn đã được cập nhật!',
            });
        },
        onError: (errors) => {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: Object.values(errors)[0] || 'Có lỗi xảy ra, vui lòng thử lại.',
            });
        }
    });
};
</script>

<template>

    <Head title="Đặt lại mật khẩu" />

    <div class="login-layout" style="margin-top: -77px;">
        <div class="login-container login-container--password-reset">
            <div class="text-center mb-4">
                <div class="logo-circle">
                    <span class="logo-text">Pharma24h</span>
                </div>
            </div>

            <h2 class="login-title">Mật khẩu mới</h2>
            <p class="login-subtitle">
                Vui lòng nhập mật khẩu mới cho tài khoản <br>
                <strong>{{ email }}</strong>
            </p>

            <form @submit.prevent="submit">
                <div class="form-group mb-3">
                    <label for="password" class="form-label">Mật khẩu mới</label>
                    <input type="password" v-model="form.password" class="form-control"
                        :class="{ 'is-invalid': passwordError || form.errors.password }" id="password"
                        placeholder="Ít nhất 8 ký tự" required autofocus>
                    <div v-if="passwordError" class="invalid-feedback">{{ passwordError }}</div>
                </div>

                <div class="form-group mb-4">
                    <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
                    <input type="password" v-model="form.password_confirmation" class="form-control"
                        :class="{ 'is-invalid': confirmationError }" id="password_confirmation"
                        placeholder="Nhập lại mật khẩu" required>
                    <div v-if="confirmationError" class="invalid-feedback">{{ confirmationError }}</div>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-4" :disabled="!isFormValid || form.processing">
                    <span v-if="form.processing"><i class="fas fa-spinner fa-spin me-2"></i>Đang cập nhật...</span>
                    <span v-else>Cập nhật mật khẩu</span>
                </button>
            </form>
        </div>
    </div>
</template>
