<template>
    <div class="page-wrapper">
        <Header :auth="auth" />

        <div class="contact-banner">
            <div class="container">
                <h1 class="fw-bold text-primary-dark">Liên hệ</h1>
                <p class="text-secondary">Hãy liên hệ với chúng tôi để hỏi thông tin, thông tin y tế hoặc cơ hội hợp
                    tác.</p>
            </div>
        </div>

        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-7">
                    <h4 class="fw-bold mb-4">Gửi tin nhắn cho chúng tôi</h4>

                    <form @submit.prevent="handleSubmit">
                        <div class="d-flex flex-column gap-3">

                            <div class="form-group">
                                <label class="fw-bold mb-1 small">Họ và tên</label>
                                <InputText v-model="form.fullName" placeholder="Nhập họ và tên của bạn" class="w-100" />
                            </div>

                            <div class="form-group">
                                <label class="fw-bold mb-1 small">Địa chỉ Email</label>
                                <InputText v-model="form.email" placeholder="Nhập email của bạn" class="w-100" />
                            </div>

                            <div class="form-group">
                                <label class="fw-bold mb-1 small">Chủ đề</label>
                                <Dropdown v-model="form.subject" :options="subjects" optionLabel="name"
                                    optionValue="code" placeholder="Chọn chủ đề" class="w-100" />
                            </div>

                            <div class="form-group">
                                <label class="fw-bold mb-1 small">Tin nhắn</label>
                                <Textarea v-model="form.message" placeholder="Chúng tôi có thể giúp gì cho bạn?"
                                    rows="5" class="w-100" autoResize />
                            </div>

                            <div class="mt-2">
                                <Button label="Gửi tin nhắn" type="submit" :loading="loading" class="btn-submit" />
                            </div>

                        </div>
                    </form>
                </div>

                <div class="col-lg-5">
                    <div class="info-sidebar">

                        <h4 class="fw-bold mb-4">Thông tin liên hệ</h4>

                        <ul class="list-unstyled contact-list mb-4">
                            <li class="d-flex gap-3 mb-3">
                                <i class="pi pi-map-marker text-primary mt-1" style="font-size: 1.2rem"></i>
                                <div>
                                    <strong>Trụ sở chính:</strong> Apex Pharma PCT Solutions<br>
                                    123 Đường Innovation, Khu Công nghệ cao,<br>
                                    Quận 9, TP. Hồ Chí Minh
                                </div>
                            </li>
                            <li class="d-flex gap-3 mb-3">
                                <i class="pi pi-phone text-primary mt-1" style="font-size: 1.2rem"></i>
                                <div>
                                    <strong>Điện thoại:</strong> +84 901645269<br>
                                    <span class="text-muted small">(Thứ Hai - Thứ Sáu, 9h - 17h)</span>
                                </div>
                            </li>
                            <li class="d-flex gap-3 mb-3">
                                <i class="pi pi-envelope text-primary mt-1" style="font-size: 1.2rem"></i>
                                <div>
                                    <strong>Email:</strong> info@pharmapct.vn<br>
                                    <span class="text-muted small">(Yêu cầu chung)</span>
                                </div>
                            </li>
                        </ul>

                        <div class="map-container mb-4">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3918.44366148992!2d106.62565431526066!3d10.853816260728994!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752a20be9dce57%3A0x28636735327856b3!2zQ2hvIFRo4buL!5e0!3m2!1svi!2s!4v1655712345678"
                                width="100%" height="200" style="border:0; border-radius: 8px;" allowfullscreen=""
                                loading="lazy">
                            </iframe>
                        </div>

                        <h4 class="fw-bold mb-3">Yêu cầu cụ thể</h4>
                        <div class="specific-contacts">
                            <p class="mb-2">Thông tin y tế: <a
                                    href="mailto:medinfo@pharmapct.vn">medinfo@pharmapct.vn</a></p>
                            <p class="mb-2">Quan hệ nhà đầu tư: <a
                                    href="mailto:investors@pharmapct.vn">investors@pharmapct.vn</a></p>
                            <p class="mb-2">Tuyển dụng: <a href="mailto:careers@pharmapct.vn">careers@pharmapct.vn</a>
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
import Header from '@/Components/Global/Header.vue'
import { router } from '@inertiajs/vue3';
import { ref, reactive } from 'vue';

// Import Components
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Dropdown from 'primevue/dropdown'; // Import thêm Dropdown
import Button from 'primevue/button';

const props = defineProps({
    auth: { type: Object, default: () => ({ user: null }) }
})

const handleSubmit = () => {
    if(!form.fullName || !form.email) {
        alert("Vui lòng nhập đầy đủ thông tin!");
        return;
    }

    loading.value = true;
    router.post('/contact', form, {
        onSuccess: () => {
            alert("Tin nhắn đã được gửi thành công!");
            // Reset form
            form.fullName = '';
            form.email = '';
            form.subject = null;
            form.message = '';
        },
        onError: (errors) => {
            console.error(errors);
            alert("Có lỗi xảy ra, vui lòng kiểm tra lại thông tin.");
        },
        onFinish: () => {
            loading.value = false;
        }
    });
}

const loading = ref(false);

// Dữ liệu Form
const form = reactive({
    fullName: '',
    email: '',
    subject: null,
    message: ''
});

// Danh sách chủ đề Dropdown
const subjects = [
    { name: 'Yêu cầu chung', code: 'general' },
    { name: 'Thông tin sản phẩm', code: 'product' },
    { name: 'Yêu cầu thông tin y tế', code: 'medical' },
    { name: 'Hợp tác', code: 'partnership' },
    { name: 'Khác', code: 'other' }
];

</script>

<style scoped>
.page-wrapper {
    margin-top: 80px;
    /* Khoảng cách header */
}

/* 1. Banner Style */
.contact-banner {
    background: linear-gradient(180deg, #E6F3FF 0%, #FFFFFF 100%);
    /* Gradient xanh nhạt giống ảnh */
    padding: 60px 0 40px;
    border-bottom: 1px solid #f0f0f0;
}

.text-primary-dark {
    color: #1a4f6e;
    /* Màu xanh đậm của tiêu đề */
}

/* 2. Form Style */
.form-group label {
    color: #000;
    font-weight: 700;
}

/* PrimeVue Overrides */
:deep(.p-inputtext),
:deep(.p-textarea),
:deep(.p-dropdown) {
    border-radius: 4px;
    border: 1px solid #ced4da;
    padding: 0.75rem;
}

:deep(.p-inputtext:focus),
:deep(.p-textarea:focus),
:deep(.p-dropdown.p-focus) {
    border-color: #3B82F6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
}

.btn-submit {
    background-color: #356685;
    /* Màu xanh giống nút trong ảnh */
    border: none;
    padding: 10px 30px;
    font-weight: bold;
}

.btn-submit:hover {
    background-color: #274d65;
}

/* 3. Info Sidebar Style */
.contact-list li {
    font-size: 0.95rem;
    color: #333;
}

.specific-contacts a {
    color: #356685;
    text-decoration: underline;
    font-weight: 500;
}

.specific-contacts p {
    font-size: 0.9rem;
    font-weight: 600;
    color: #333;
}
</style>
