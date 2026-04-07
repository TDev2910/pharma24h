<template>
    <div>
        <!-- Hero Banner Section -->
        <section class="hero-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h1 class="hero-title">
                            Dịch vụ Y tế Đa dạng<br />
                            <span class="highlight-text">Nhanh Chóng - Tận Tâm</span>
                        </h1>
                        <p class="hero-description text-secondary">
                            Chăm sóc sức khỏe chất lượng cao với đội ngũ bác sĩ giỏi, cơ sở hiện đại và quy trình chuyên nghiệp, mang lại hiệu quả điều trị tối ưu.
                        </p>
                    </div>
                    <div class="col-lg-6">
                        <div class="hero-image">
                            <div class="placeholder-image"
                                style="width: 100%; height: 400px; background: linear-gradient(135deg, #E3F2FD 0%, #90CAF9 100%); display: flex; align-items: center; justify-content: center; color: #1976D2; font-size: 18px; font-weight: 600;">
                                <img src="https://img.freepik.com/free-photo/nurse-doctor-team-ready-work-day_23-2149309969.jpg?semt=ais_incoming&w=740&q=80"
                                    alt="Doctor and Patient" style="width: 100%; height: auto; display: block;" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Icons Section -->
        <section class="features-section">
            <div class="container">
                <div class="row justify-content-center g-4">
                    <div class="col-lg-2 col-md-4 col-6">
                        <div class="feature-card">
                            <div class="feature-icon blue">
                                <i class="fas fa-briefcase-medical"></i>
                            </div>
                            <h5 class="feature-title">Đa Dạng</h5>
                            <p class="feature-text">Trên 100+ Dịch Vụ</p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-6">
                        <div class="feature-card">
                            <div class="feature-icon cyan">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h5 class="feature-title">Nhanh Chóng</h5>
                            <p class="feature-text">Đặt Khám 24/7</p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-6">
                        <div class="feature-card">
                            <div class="feature-icon light-blue">
                                <i class="fas fa-hand-holding-heart"></i>
                            </div>
                            <h5 class="feature-title">Tận Tâm</h5>
                            <p class="feature-text">Đội Ngũ Chuyên Nghiệp</p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-6">
                        <div class="feature-card">
                            <div class="feature-icon medium-blue">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h5 class="feature-title">An Toàn</h5>
                            <p class="feature-text">Tiêu Chuẩn Cao</p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-6">
                        <div class="feature-card">
                            <div class="feature-icon dark-blue">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h5 class="feature-title">Hiệu Quả</h5>
                            <p class="feature-text">Kết Quả Tốt Nhất</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Service Categories Section -->
        <section class="service-categories-section">
            <div class="container">
                <div class="section-header text-center">
                    <p class="section-subtitle">CÁC DỊCH VỤ Y TẾ </p>
                    <h2 class="section-title">Danh Mục Dịch Vụ Của Chúng Tôi</h2>
                    <p class="section-description">
                        Hệ thống y tế chúng tôi cung cấp đầy đủ các dịch vụ chuyên khoa từ khám bệnh, xét nghiệm, chẩn
                        đoán hình ảnh
                        đến các gói khám sức khỏe chuyên biệt và dịch vụ đặc biệt của riêng chúng tôi.
                    </p>
                </div>

                <div class="row g-4">
                    <div class="row g-4">
                        <div v-for="service in props.services.slice(0, 4)" :key="service.id" class="col-lg-3 col-md-6">
                            <div class="category-card" @click="goToServiceDetail(service)" style="cursor: pointer;">
                                <!-- Hình ảnh dịch vụ -->
                                <div class="service-image mb-3"
                                    style="width: 100%; height: 180px; border-radius: 12px; overflow: hidden;">
                                    <img :src="service.image ? `/storage/${service.image}` : 'https://via.placeholder.com/300x180'"
                                        :alt="service.ten_dich_vu"
                                        style="width: 100%; height: 100%; object-fit: cover;" />
                                </div>

                                <h4 class="category-title">{{ service.ten_dich_vu }}</h4>

                                <p class="service-price" style="color: #2196F3; font-weight: 600; font-size: 18px;">
                                    {{ formatCurrency(service.gia_dich_vu) }}
                                </p>

                                <p class="category-description" style="font-size: 14px; color: #666;">
                                    {{ service.ma_dich_vu }}
                                </p>

                                <button class="btn btn-primary" style="width: 100%;">
                                    Đặt Lịch Ngay
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Home Care Section -->
        <section class="home-care-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h2 class="home-care-title">Khám Tại Nhà - Tiện Lợi<br />& An Toàn Tuyệt Đối</h2>
                        <p class="home-care-description">
                            Dịch vụ khám tại nhà của chúng tôi mang đến sự tiện lợi tối đa cho bạn. Đội ngũ y bác sĩ
                            chuyên nghiệp sẽ
                            đến tận nhà để thăm khám, tư vấn và điều trị khi bạn cần. Mang lại sự yên tâm tuyệt đối cho
                            sức khỏe gia
                            đình bạn.
                        </p>

                        <div class="row home-care-features">
                            <div class="col-md-6">
                                <div class="feature-item">
                                    <div class="feature-item-icon green">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div class="feature-item-content">
                                        <h5>Tiện lợi thời gian</h5>
                                        <p>Đặt lịch khám nhanh chóng</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="feature-item">
                                    <div class="feature-item-icon green">
                                        <i class="fas fa-shield-virus"></i>
                                    </div>
                                    <div class="feature-item-content">
                                        <h5>Hạn chế bệnh nhiễm</h5>
                                        <p>Giảm nguy cơ lây nhiễm chéo</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="feature-item">
                                    <div class="feature-item-icon green">
                                        <i class="fas fa-user-nurse"></i>
                                    </div>
                                    <div class="feature-item-content">
                                        <h5>Chăm sóc chất lượng</h5>
                                        <p>Đội ngũ bác sĩ giàu kinh nghiệm</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="feature-item">
                                    <div class="feature-item-icon green">
                                        <i class="fas fa-phone-alt"></i>
                                    </div>
                                    <div class="feature-item-content">
                                        <h5>Liên lạc dễ dàng nghiệp</h5>
                                        <p>Tư vấn hỗ trợ 24/7</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="home-care-image">
                            <div class="placeholder-image"
                                style="width: 100%; height: 400px; background: linear-gradient(135deg, #E8F5E9 0%, #81C784 100%); display: flex; align-items: center; justify-content: center; color: #2E7D32; font-size: 18px; font-weight: 600;">
                                <img src="https://famedico.com/wp-content/uploads/2024/01/Homebound-Patients.jpg"
                                    alt="Doctor and Patient" style="width: 100%; height: auto; display: block;" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Booking Section -->
        <section class="booking-section" id="booking-section">
            <div class="container">
                <div class="section-header text-center text-white">
                    <h2 class="section-title">Đặt Lịch Online Linh Hoạt</h2>
                    <p class="section-description">
                        Chúng tôi hiểu rằng mỗi bệnh nhân có nhu cầu khác nhau. Vì vậy chúng tôi cung cấp nhiều hình
                        thức đặt lịch
                        khám để bạn có thể thoải mái chọn lựa phương tiện phù hợp nhất với nhu cầu của bạn.
                    </p>
                </div>

                <div class="row g-4">
                    <div class="col-lg-4 col-md-6">
                        <div class="booking-card h-100 d-flex flex-column">
                            <div class="booking-icon mx-auto">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <h4 class="booking-title">Theo Giờ</h4>
                            <p class="booking-description">
                                Giải pháp tối ưu cho người bận rộn. Đặt trước khung giờ mong muốn để được ưu tiên phục
                                vụ ngay lập tức.
                            </p>

                            <ul class="benefit-list text-start mb-4 ps-3" style="list-style: none;">
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Không mất thời gian chờ đợi tại sảnh
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Tối ưu quy trình khám nhanh gọn
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Dễ dàng dời hoặc hủy lịch miễn phí
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="booking-card h-100 d-flex flex-column">
                            <div class="booking-icon mx-auto">
                                <i class="fas fa-video"></i>
                            </div>
                            <h4 class="booking-title">Tư Vấn Từ Xa</h4>
                            <p class="booking-description">
                                Kết nối với bác sĩ chuyên khoa qua Video Call. Nhận tư vấn sức khỏe mà không cần phải di
                                chuyển xa.
                            </p>

                            <ul class="benefit-list text-start mb-4 ps-3" style="list-style: none;">
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Kết nối mọi lúc,
                                    mọi nơi</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Bảo mật thông tin
                                    tuyệt đối</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Toa thuốc điện tử
                                    gửi về SMS</li>
                            </ul>

                            <div class="mt-auto">
                                <button @click="openChat"
                                    class="btn btn-primary btn-booking rounded-pill d-flex align-items-center justify-content-center w-100 border-0">
                                    <i class="fas fa-comment-dots me-2"></i> Kết Nối Trực Tiếp
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="booking-card h-100 d-flex flex-column">
                            <div class="booking-icon mx-auto">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <h4 class="booking-title">Giải Pháp Sức Khỏe Toàn Diện</h4>
                            <p class="booking-description">
                                Chúng tôi mang đến sự an tâm tuyệt đối cho gia đình bạn với quy trình chăm sóc hiện đại,
                                nhanh chóng và an toàn.
                            </p>

                            <ul class="benefit-list text-start mb-4 ps-3" style="list-style: none;">
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Giảm thiểu tối đa
                                    thời gian và chi phí đi lại.</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Hạn chế tiếp xúc
                                    nơi đông người, tránh lây nhiễm chéo.</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Được chuyên gia
                                    đồng hành và theo dõi sát sao 24/7.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Doctors Section -->
        <section class="doctors-section">
            <div class="container">
                <div class="section-header text-center">
                    <h2 class="section-title">Đội Ngũ Bác Sĩ</h2>
                    <p class="section-description">Những chuyên gia giàu kinh nghiệm sẵn sàng tận tâm vì sức khỏe của
                        bạn.</p>
                </div>

                <div class="row g-4">
                    <div class="col-lg-3 col-md-6" v-for="doctor in doctors" :key="doctor.id">
                        <div class="doctor-card">
                            <div class="doctor-image">
                                <div class="slide-content">
                                    <img :src="doctor.image" :alt="doctor.name" class="img-fluid main-image">
                                </div>
                            </div>
                            <div class="doctor-info">
                                <h5 class="doctor-name">{{ doctor.name }}</h5>
                                <p class="doctor-specialty">{{ doctor.specialty }}</p>
                                <p class="doctor-description">{{ doctor.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <div class="cta-content text-center">
                    <h2 class="cta-title">Sức Khỏe Của Bạn Là Ưu Tiên Hàng Đầu Của<br />Chúng Tôi</h2>
                    <p class="cta-description">
                        Đừng ngần ngại liên hệ với chúng tôi để được tư vấn và chăm sóc tốt nhất. Chúng tôi hoạt động
                        24/7<br />
                        cả trong tuần lẫn ngày lễ (hoàng cộng 24/7)
                    </p>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'

// biến để mở chat
const openChat = () => {
    window.dispatchEvent(new CustomEvent('open-human-chat'));
}

const props = defineProps({
    auth: { type: Object, default: () => ({ user: null }) },
    services: { type: Array, default: () => [] }
})

// data ảo 
const doctors = ref([
    {
        id: 1,
        name: 'BS. Lê Thu Hà',
        specialty: 'Khám Tổng Quát',
        description: 'Chuyên gia tư vấn sức khỏe gia đình, tận tâm và chu đáo với mọi bệnh nhân.',
        image: 'https://images.pexels.com/photos/6749765/pexels-photo-6749765.jpeg?cs=srgb&dl=pexels-shkrabaanthony-6749765.jpg&fm=jpg'
    },
    {
        id: 2,
        name: 'BS. Nguyễn Văn Hùng',
        specialty: 'Tư vấn Tiêm Chủng',
        description: 'Hỗ trợ tư vấn lộ trình tiêm ngừa và các vấn đề sức khỏe phổ biến hàng ngày.',
        image: 'https://hips.hearstapps.com/hmg-prod/images/portrait-of-a-happy-young-doctor-in-his-clinic-royalty-free-image-1661432441.jpg?crop=0.66698xw:1xh;center,top&resize=1200:*'
    },
    {
        id: 3,
        name: 'BS. Phạm Thanh Mai',
        specialty: 'Tư vấn Dinh Dưỡng',
        description: 'Chuyên gia hỗ trợ chế độ ăn uống và chăm sóc sức khỏe ban đầu cho cộng đồng.',
        image: 'https://img.freepik.com/free-psd/doctor-preparing-routine-medical-check_23-2150493277.jpg'
    },
    {
        id: 4,
        name: 'DS. Trần Hoàng Nam',
        specialty: 'Dược Sĩ Tư Vấn',
        description: 'Tư vấn sử dụng thuốc an toàn, hiệu quả và các sản phẩm chăm sóc sức khỏe thiết yếu.',
        image: 'https://t4.ftcdn.net/jpg/07/07/89/33/360_F_707893394_5DEhlBjWOmse1nyu0rC9T7ZRvsAFDkYC.jpg'
    }
])

// Format currency
const formatCurrency = (value) => {
    if (!value) return 'N/A'
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(value)
}

const goToServiceDetail = (service) => {
    router.visit(`/services/${service.id}`)
}

// Book service
const bookService = (service) => {
    console.log('Book service:', service)
}

// Scroll to booking section
const scrollToBooking = () => {
    const element = document.getElementById('booking-section')
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' })
    }
}
</script>
<style scoped src="../../../../css/Public/Services/index/index.css"></style>
