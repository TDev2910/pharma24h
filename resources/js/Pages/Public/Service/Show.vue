<template>
    <div class="root-container">
        <!-- Header Component -->
        <Header :auth="auth" />

        <!-- Service Detail Content -->
        <div class="container py-5">
            <div class="row">
                <!-- Service Image -->
                <div class="col-md-5">
                    <div class="service-image-container">
                        <img :src="service.image ? `/storage/${service.image}` : 'https://via.placeholder.com/400'"
                            :alt="service.ten_dich_vu" class="service-detail-image" />
                    </div>
                </div>

                <!-- Service Info -->
                <div class="col-md-7">
                    <div class="service-detail-info">
                        <h1 class="service-name">{{ service.ten_dich_vu }}</h1>

                        <div class="service-price mb-4">
                            <span class="current-price">{{ formatCurrency(service.gia_dich_vu) }}</span>
                        </div>

                        <h6>Thông tin dịch vụ</h6>
                        <hr>
                        <div class="service-meta mb-4">
                            <div class="meta-item">
                                <strong>Mã dịch vụ:</strong> {{ service.ma_dich_vu }}
                            </div>
                            <div class="meta-item" v-if="service.category">
                                <strong>Danh mục:</strong> {{ service.category.name }}
                            </div>
                            <div class="meta-item" v-if="service.thoi_gian_thuc_hien">
                                <strong>Thơi gian thực hiện:</strong> {{ service.thoi_gian_thuc_hien }} phút
                            </div>
                            <div class="meta-item" v-if="service.hinh_thuc">
                                <strong>Hình thức:</strong> Tại nhà thuốc
                            </div>
                            <div class="meta-item" v-if="service.ghi_chu">
                                <strong>Ghi chú:</strong> {{ service.ghi_chu }}
                            </div>
                        </div>
                        <!-- Book Service -->
                        <div class="service-actions">
                            <button class="btn btn-primary btn-lg" @click="showBookingModal = true">
                                <i></i>
                                Đặt dịch vụ ngay
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Show Doctor -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="service-description">
                        <h3>Bác sĩ đảm nhận</h3>

                        <div v-if="service.doctor" class="doctor-card">
                            <div class="d-flex align-items-center gap-4">
                                <div class="doctor-avatar-wrapper">
                                    <img :src="service.doctor.avatar ? `/storage/${service.doctor.avatar}` : '/images/default-doctor.png'"
                                        :alt="service.doctor.name" class="doctor-avatar" />
                                </div>

                                <div class="doctor-info">
                                    <h4 class="doctor-name">{{ service.doctor.name }}</h4>

                                    <div class="doctor-meta">
                                        <p v-if="service.doctor.specialty" class="mb-1">
                                            <i class="me-2 text-primary"></i>
                                            <strong>Chuyên khoa:</strong> {{ service.doctor.specialty }}
                                        </p>

                                        <p v-if="service.doctor.qualification" class="mb-1">
                                            <i class="me-2 text-warning"></i>
                                            <strong>Trình độ:</strong> {{ service.doctor.qualification }}
                                        </p>

                                        <p v-if="service.doctor.work_place" class="mb-0">
                                            <i class="pi pi-building me-2 text-secondary"></i>
                                            <strong>Công tác:</strong> {{ service.doctor.work_place }}
                                        </p>
                                        <p v-if="service.doctor.note" class="mb-0">
                                            <i class="me-2 text-secondary"></i>
                                            <strong>Thông tin chi tiết:</strong> {{ service.doctor.note }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="alert alert-light text-center" role="alert">
                            <i class="pi pi-info-circle me-2"></i>
                            Hiện chưa có thông tin bác sĩ phụ trách cụ thể cho dịch vụ này.
                        </div>

                    </div>
                </div>
            </div>
            <!-- Service Description -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="service-description">
                        <h3>Mô tả dịch vụ</h3>
                        <div v-html="sanitizedDescription" class="html-content"></div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Booking Modal -->
        <Dialog v-model:visible="showBookingModal" :header="`Đặt lịch dịch vụ: ${service.ten_dich_vu}`"
            :style="{ width: '600px' }" modal closable @hide="resetForm">
            <form @submit.prevent="submitBooking">
                <div class="form-grid">
                    <!-- Họ và tên -->
                    <div class="form-field">
                        <label for="customer_name" class="field-label">Họ và tên *</label>
                        <InputText id="customer_name" v-model="bookingForm.customer_name" type="text"
                            placeholder="Nhập họ và tên" class="field-input"
                            :class="{ 'p-invalid': errors.customer_name }" required />
                        <small v-if="errors.customer_name" class="p-error">{{ errors.customer_name[0] }}</small>
                    </div>

                    <!-- Số điện thoại -->
                    <div class="form-field">
                        <label for="customer_phone" class="field-label">Số điện thoại *</label>
                        <InputText id="customer_phone" v-model="bookingForm.customer_phone" type="tel"
                            placeholder="Nhập số điện thoại" class="field-input"
                            :class="{ 'p-invalid': errors.customer_phone }" required />
                        <small v-if="errors.customer_phone" class="p-error">{{ errors.customer_phone[0] }}</small>
                    </div>

                    <!-- Email -->
                    <div class="form-field">
                        <label for="customer_email" class="field-label">Email</label>
                        <InputText id="customer_email" v-model="bookingForm.customer_email" type="email"
                            placeholder="Nhập email (không bắt buộc)" class="field-input"
                            :class="{ 'p-invalid': errors.customer_email }" />
                        <small v-if="errors.customer_email" class="p-error">{{ errors.customer_email[0] }}</small>
                    </div>

                    <!-- Ngày đặt lịch -->
                    <div class="form-field">
                        <label for="booking_date" class="field-label">Ngày đặt lịch *</label>
                        <input id="booking_date" v-model="bookingForm.booking_date" type="date" required :min="tomorrow"
                            class="field-input" :class="{ 'p-invalid': errors.booking_date }" />
                        <small v-if="errors.booking_date" class="p-error">{{ errors.booking_date[0] }}</small>
                    </div>

                    <!-- Giờ đặt lịch -->
                    <div class="form-field">
                        <label for="booking_time" class="field-label">Giờ đặt lịch *</label>
                        <input id="booking_time" v-model="bookingForm.booking_time" type="time" required
                            class="field-input" :class="{ 'p-invalid': errors.booking_time }" />
                        <small v-if="errors.booking_time" class="p-error">{{ errors.booking_time[0] }}</small>
                    </div>

                    <!-- Ghi chú -->
                    <div class="form-field" style="grid-column: 1 / -1;">
                        <label for="notes" class="field-label">Ghi chú</label>
                        <textarea id="notes" v-model="bookingForm.notes" rows="5" cols="30"
                            placeholder="Nhập ghi chú (không bắt buộc)" class="field-input"
                            :class="{ 'p-invalid': errors.notes }"></textarea>
                        <small v-if="errors.notes" class="p-error">{{ errors.notes[0] }}</small>
                    </div>

                    <!-- Thông tin dịch vụ -->
                    <div class="service-info" style="grid-column: 1 / -1;">
                        <h5>Thông tin dịch vụ</h5>
                        <p><strong>Tên dịch vụ:</strong> {{ service.ten_dich_vu }}</p>
                        <p><strong>Giá:</strong> {{ formatCurrency(service.gia_dich_vu) }}</p>
                        <p><strong>Thanh toán:</strong> Tại quầy nhà thuốc</p>
                    </div>
                </div>
            </form>

            <template #footer>
                <div class="flex justify-end gap-2">
                    <Button type="button" label="Hủy" severity="secondary" @click="closeModal" />
                    <Button type="button" label="Xác nhận đặt lịch" @click="submitBooking" :loading="isSubmitting" />
                </div>
            </template>
        </Dialog>
    </div>
</template>

<script setup>
import Header from '@/Components/Global/Header.vue'
import Footer from '@/Components/Global/Footer.vue'
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'
import { computed, ref, reactive } from 'vue'
import DOMPurify from 'dompurify'
import axios from 'axios'

const props = defineProps({
    auth: { type: Object, default: () => ({ user: null }) },
    service: { type: Object, required: true }
})

// Modal state
const showBookingModal = ref(false)
const isSubmitting = ref(false)
const errors = ref({})

// Booking form data
const bookingForm = reactive({
    service_id: null,
    customer_name: '',
    customer_phone: '',
    customer_email: '',
    booking_date: '',
    booking_time: '',
    notes: ''
})

// Get tomorrow's date for min date
const tomorrow = computed(() => {
    const tomorrow = new Date()
    tomorrow.setDate(tomorrow.getDate() + 1)
    return tomorrow.toISOString().split('T')[0]
})

// Sanitize HTML description
const sanitizedDescription = computed(() => {
    const dirtyHTML = props.service.mo_ta || ''
    return DOMPurify.sanitize(dirtyHTML, {
        ALLOWED_TAGS: ['p', 'br', 'strong', 'em', 'u', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'ul', 'ol', 'li', 'a', 'img', 'blockquote', 'code', 'pre', 'span', 'div'],
        ALLOWED_ATTR: ['href', 'target', 'src', 'alt', 'class', 'style'],
        ALLOW_DATA_ATTR: false
    })
})

// Format currency
function formatCurrency(amount) {
    if (!amount) return '0 VNĐ'
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount)
}

// Submit booking
async function submitBooking() {
    isSubmitting.value = true
    errors.value = {}

    try {
        // Lấy CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')

        const response = await axios.post('/bookings', {
            ...bookingForm,
            service_id: props.service.id
        }, {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            }
        })

        if (response.data.success) {
            alert('Đặt lịch thành công! Nhân viên sẽ gọi điện cho bạn để xác nhận.')
            closeModal()
        }
    } catch (error) {
        console.error('Lỗi đặt lịch:', error)

        if (error.response && error.response.status === 422) {
            // Lỗi validation
            errors.value = error.response.data.errors || {}
        } else if (error.response?.data?.message) {
            alert(error.response.data.message)
        } else {
            alert('Có lỗi xảy ra khi đặt lịch. Vui lòng thử lại.')
        }
    } finally {
        isSubmitting.value = false
    }
}

// Close modal
function closeModal() {
    showBookingModal.value = false
}

// Reset form
function resetForm() {
    Object.assign(bookingForm, {
        service_id: null,
        customer_name: '',
        customer_phone: '',
        customer_email: '',
        booking_date: '',
        booking_time: '',
        notes: ''
    })
    errors.value = {}
}
</script>

<style scoped src="../../../../css/Public/Services/Show.css"></style>
