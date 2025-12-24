<template>
  <div>
    <!-- Header Component -->
    <Header :auth="auth" />

    <!-- Hero Banner Section -->
    <section class="hero-section">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6">
            <p class="hero-subtitle">CHĂM SÓC SỨC KHỎE TOÀN DIỆN CHO GIA ĐÌNH</p>
            <h1 class="hero-title">
              Dịch vụ Y tế Đa dạng
              <br />
              <span class="highlight-text">Nhanh Chóng - Tận Tâm</span>
            </h1>
            <p class="hero-description">
              Trải nghiệm dịch vụ y tế chất lượng cao với đội ngũ bác sĩ giỏi, hệ thống cơ sở hiện đại, dịch vụ đa dạng,
              khám nhanh chóng, tư vấn tận tình và chăm sóc tận tâm. Gửi ra hiệu quả tối ưu.
            </p>
            <div class="hero-buttons">
              <button class="btn btn-primary btn-hero" @click="scrollToBooking">Đặt Lịch Khám</button>
              <button class="btn btn-outline-primary btn-hero">Tìm Hiểu Thêm</button>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="hero-image">
              <div class="placeholder-image"
                style="width: 100%; height: 400px; background: linear-gradient(135deg, #E3F2FD 0%, #90CAF9 100%); display: flex; align-items: center; justify-content: center; color: #1976D2; font-size: 18px; font-weight: 600;">
                <img
                  src="https://img.freepik.com/free-photo/nurse-doctor-team-ready-work-day_23-2149309969.jpg?semt=ais_incoming&w=740&q=80"
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
          <p class="section-subtitle">CÁC DỊCH VỤ Y TẾ TOÀN DIỆN</p>
          <h2 class="section-title">Danh Mục Dịch Vụ Của Chúng Tôi</h2>
          <p class="section-description">
            Hệ thống y tế chúng tôi cung cấp đầy đủ các dịch vụ chuyên khoa từ khám bệnh, xét nghiệm, chẩn đoán hình ảnh
            đến các gói khám sức khỏe chuyên biệt và dịch vụ đặc biệt của riêng chúng tôi.
          </p>
        </div>

        <div class="row g-4">
          <div class="row g-4">
            <div v-for="(service, index) in props.services.slice(0, 4)" :key="service.id" class="col-lg-3 col-md-6">
              <div class="category-card" @click="goToServiceDetail(service)" style="cursor: pointer;">
                <!-- Hình ảnh dịch vụ -->
                <div class="service-image mb-3"
                  style="width: 100%; height: 180px; border-radius: 12px; overflow: hidden;">
                  <img :src="service.image ? `/storage/${service.image}` : 'https://via.placeholder.com/300x180'"
                    :alt="service.ten_dich_vu" style="width: 100%; height: 100%; object-fit: cover;" />
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
            <div class="home-care-badge">DỊCH VỤ ĐẶC BIỆT</div>
            <h2 class="home-care-title">Khám Tại Nhà - Tiện Lợi<br />& An Toàn Tuyệt Đối</h2>
            <p class="home-care-description">
              Dịch vụ khám tại nhà của chúng tôi mang đến sự tiện lợi tối đa cho bạn. Đội ngũ y bác sĩ chuyên nghiệp sẽ
              đến tận nhà để thăm khám, tư vấn và điều trị khi bạn cần. Mang lại sự yên tâm tuyệt đối cho sức khỏe gia
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

            <button class="btn btn-success btn-home-care">Đăng Ký Khám Tại Nhà Ngay</button>
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
            Chúng tôi hiểu rằng mỗi bệnh nhân có nhu cầu khác nhau. Vì vậy chúng tôi cung cấp nhiều hình thức đặt lịch
            khám để bạn có thể thoải mái chọn lựa phương tiện phù hợp nhất với nhu cầu của bạn.
          </p>
        </div>

        <div class="row g-4">
          <div class="col-lg-4 col-md-6">
            <div class="booking-card">
              <div class="booking-icon">
                <i class="fas fa-clock"></i>
              </div>
              <h4 class="booking-title">Theo Giờ</h4>
              <p class="booking-description">Đặt lịch khám theo giờ với các khung giờ linh hoạt, phù hợp với thời gian
                biểu của bạn</p>
              <div class="booking-fields">
                <p class="booking-label">Chọn thời gian</p>
                <p class="booking-label">Chọn bác sĩ</p>
              </div>
              <button class="btn btn-primary btn-booking">Chọn Đặt Khám</button>
            </div>
          </div>

          <div class="col-lg-4 col-md-6">
            <div class="booking-card booking-card-primary">
              <div class="booking-icon white">
                <i class="fas fa-calendar-alt"></i>
              </div>
              <h4 class="booking-title text-white">Theo Ngày</h4>
              <p class="booking-description text-white">Đặt lịch khám theo ngày cho các dịch vụ chuyên sâu, khám tổng
                quát</p>
              <div class="booking-form">
                <input type="date" class="form-control" placeholder="Chọn Ngày" />
                <input type="text" class="form-control" placeholder="Chọn Ngày" />
              </div>
              <button class="btn btn-light btn-booking">Chọn Ngày Khám</button>
            </div>
          </div>

          <div class="col-lg-4 col-md-6">
            <div class="booking-card">
              <div class="booking-icon">
                <i class="fas fa-message"></i>
              </div>
              <h4 class="booking-title">Tư Vấn Trực Tiếp</h4>
              <p class="booking-description">Tư vấn từ trực tiếp thông qua Vchat cho các trường hợp cần tư vấn nhanh
                chóng, tiện lợi</p>
              <div class="booking-fields">
                <p class="booking-label">Khung thời gian</p>
                <p class="booking-label">Bác sĩ tư vấn</p>
              </div>
              <button class="btn btn-primary btn-booking">Kết Nối Ngay</button>
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
          <p class="section-description">Những chuyên gia giàu kinh nghiệm sẵn sàng tận tâm vì sức khỏe của bạn.</p>
          <a href="#" class="view-all-link">Xem tất cả →</a>
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
            Đừng ngần ngại liên hệ với chúng tôi để được tư vấn và chăm sóc tốt nhất. Chúng tôi hoạt động 24/7<br />
            cả trong tuần lẫn ngày lễ (hoàng cộng 24/7)
          </p>
          <div class="cta-buttons">
            <button class="btn btn-primary btn-cta">
              <i class="fas fa-calendar-check"></i> Đặt Lịch Ngay
            </button>
            <button class="btn btn-outline-primary btn-cta">
              <i class="fas fa-phone-alt"></i> 19000 1234
            </button>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import Header from '@/Components/Global/Header.vue'
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  auth: { type: Object, default: () => ({ user: null }) },
  services: { type: Array, default: () => [] }
})

// Sample doctors data
const doctors = ref([
  {
    id: 1,
    name: 'BS. Hồ Đạt',
    specialty: 'Nội khoa',
    description: 'Chuyên gia giàu kinh nghiệm trong lĩnh vực nội khoa với hơn 15 năm kinh nghiệm',
    image: '/storage/avatars/doctors/dsd.jpg'
  },
  {
    id: 2,
    name: 'BS. Lương Hùng',
    specialty: 'Nhi khoa',
    description: 'Chuyên gia về sức khỏe trẻ em, tận tâm với mỗi bệnh nhi',
    image: '/storage/avatars/doctors/dsd.jpg'
  },
  {
    id: 3,
    name: 'BS. Tommy Quốc Anh',
    specialty: 'Tim mạch',
    description: 'Bác sĩ tim mạch hàng đầu với nhiều năm kinh nghiệm điều trị',
    image: '/storage/avatars/doctors/dsd.jpg'
  },
  {
    id: 4,
    name: 'BS. Minh Cường',
    specialty: 'Da liễu',
    description: 'Chuyên gia da liễu với phương pháp điều trị hiện đại',
    image: '/storage/avatars/doctors/dsd.jpg'
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

// Navigate to service detail
const goToServiceDetail = (service) => {
  router.visit(`/services/${service.id}`)
}

// Book service
const bookService = (service) => {
  // Implement booking functionality
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

<style scoped>
/* Hero Section */
.hero-section {
  padding: 120px 0 80px;
  background: linear-gradient(135deg, #f8fafc 0%, #e8f3ff 100%);
}

.hero-subtitle {
  color: #2196F3;
  font-size: 14px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-bottom: 16px;
}

.hero-title {
  font-size: 48px;
  font-weight: 700;
  color: #1a1a1a;
  line-height: 1.2;
  margin-bottom: 24px;
}

.highlight-text {
  color: #2196F3;
}

.hero-description {
  font-size: 16px;
  color: #666;
  line-height: 1.6;
  margin-bottom: 32px;
}

.hero-buttons {
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
}

.btn-hero {
  padding: 14px 32px;
  font-size: 16px;
  font-weight: 600;
  border-radius: 8px;
}

.btn-primary {
  background: #1976D2 !important; /* Đổi từ #2196F3 sang màu xanh đậm hơn */
  border: none;
  color: white !important;
}

.btn-primary:hover,
.btn-primary:focus,
.btn-primary:active,
.btn-primary:not(:disabled):not(.disabled):active {
  background: #1976D2 !important;
  border-color: #1976D2 !important;
  color: white !important;
  transform: none !important;
  box-shadow: none !important;
  outline: none !important;
}



.btn-outline-primary {
  border: 2px solid #2196F3;
  color: #2196F3;
  background: transparent;
}

.btn-outline-primary:hover {
  background: #2196F3;
  color: white;
}

.hero-image {
  position: relative;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
}

.hero-image img {
  width: 100%;
  height: auto;
  display: block;
}

/* Features Section */
.features-section {
  padding: 60px 0;
  background: #e8f3ff;
}

.feature-card {
  text-align: center;
  padding: 24px 16px;
  background: white;
  border-radius: 12px;
  transition: transform 0.3s;
}

.feature-card:hover {
  transform: translateY(-5px);
}

.feature-icon {
  width: 64px;
  height: 64px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 16px;
  font-size: 28px;
  color: white;
}

.feature-icon.blue {
  background: #2196F3;
}

.feature-icon.cyan {
  background: #00BCD4;
}

.feature-icon.light-blue {
  background: #64B5F6;
}

.feature-icon.medium-blue {
  background: #1976D2;
}

.feature-icon.dark-blue {
  background: #0D47A1;
}

.feature-title {
  font-size: 16px;
  font-weight: 600;
  color: #1a1a1a;
  margin-bottom: 4px;
}

.feature-text {
  font-size: 14px;
  color: #666;
  margin: 0;
}

/* Service Categories Section */
.service-categories-section {
  padding: 80px 0;
  background: white;
}

.section-header {
  margin-bottom: 48px;
}

.section-subtitle {
  color: #2196F3;
  font-size: 14px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-bottom: 12px;
}

.section-title {
  font-size: 36px;
  font-weight: 700;
  color: #1a1a1a;
  margin-bottom: 16px;
}

.section-description {
  font-size: 16px;
  color: #666;
  max-width: 800px;
  margin: 0 auto;
  line-height: 1.6;
}

.category-card {
  background: white;
  border: 1px solid #e0e0e0;
  border-radius: 16px;
  padding: 32px 24px;
  height: 100%;
  transition: all 0.3s;
}

.category-card:hover {
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
  transform: translateY(-5px);
}

.category-icon {
  width: 64px;
  height: 64px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
  font-size: 28px;
  color: white;
}

.blue-icon {
  background: #2196F3;
}

.green-icon {
  background: #4CAF50;
}

.purple-icon {
  background: #9C27B0;
}

.orange-icon {
  background: #FF9800;
}

.category-title {
  font-size: 20px;
  font-weight: 600;
  color: #1a1a1a;
  margin-bottom: 16px;
}

.category-list {
  list-style: none;
  padding: 0;
  margin-bottom: 20px;
}

.category-list li {
  font-size: 14px;
  color: #666;
  line-height: 1.6;
  margin-bottom: 8px;
}

.category-link {
  color: #2196F3;
  font-weight: 600;
  text-decoration: none;
  padding: 0;
  font-size: 14px;
}

.category-link:hover {
  text-decoration: underline;
}

/* Home Care Section */
.home-care-section {
  padding: 80px 0;
  background: #f8fafc;
}

.home-care-badge {
  display: inline-block;
  background: #E8F5E9;
  color: #2E7D32;
  padding: 6px 16px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-bottom: 16px;
}

.home-care-title {
  font-size: 36px;
  font-weight: 700;
  color: #1a1a1a;
  margin-bottom: 20px;
}

.home-care-description {
  font-size: 16px;
  color: #666;
  line-height: 1.6;
  margin-bottom: 32px;
}

.home-care-features {
  margin-bottom: 32px;
}

.feature-item {
  display: flex;
  gap: 16px;
  margin-bottom: 24px;
}

.feature-item-icon {
  width: 48px;
  height: 48px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  font-size: 20px;
  color: white;
}

.feature-item-icon.green {
  background: #4CAF50;
}

.feature-item-content h5 {
  font-size: 16px;
  font-weight: 600;
  color: #1a1a1a;
  margin-bottom: 4px;
}

.feature-item-content p {
  font-size: 14px;
  color: #666;
  margin: 0;
}

.btn-home-care {
  padding: 14px 32px;
  font-size: 16px;
  font-weight: 600;
  border-radius: 8px;
  background: #4CAF50;
  border: none;
  color: white;
}

.btn-home-care:hover {
  background: #45a049;
}

.home-care-image {
  position: relative;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
}

.home-care-image img {
  width: 100%;
  height: auto;
  display: block;
}

.home-care-stats {
  position: absolute;
  bottom: 24px;
  left: 24px;
  right: 24px;
  background: white;
  padding: 16px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 12px;
}

.stat-item i {
  font-size: 24px;
  color: #4CAF50;
}

.stat-item span {
  font-size: 14px;
  color: #333;
  font-weight: 500;
}

/* Booking Section */
.booking-section {
  padding: 80px 0;
  background: #F0F8FF;
}

.booking-section .section-header {
  margin-bottom: 48px;
}

.booking-card {
  background: white;
  border-radius: 16px;
  padding: 32px 24px;
  text-align: center;
  height: 100%;
  transition: all 0.3s;
}

.booking-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2);
}

.booking-card-primary {
  background: #1976D2;
}

.booking-icon {
  width: 64px;
  height: 64px;
  border-radius: 12px;
  background: #E3F2FD;
  color: #2196F3;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
  font-size: 28px;
}

.booking-icon.white {
  background: rgba(255, 255, 255, 0.2);
  color: white;
}

.booking-title {
  font-size: 20px;
  font-weight: 600;
  color: #1a1a1a;
  margin-bottom: 12px;
}

.booking-description {
  font-size: 14px;
  color: #666;
  margin-bottom: 24px;
  line-height: 1.6;
}

.booking-fields {
  margin-bottom: 24px;
}

.booking-label {
  font-size: 14px;
  color: #999;
  margin-bottom: 12px;
  text-align: left;
}

.booking-form {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 24px;
}

.booking-form input {
  padding: 12px;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  font-size: 14px;
}

.btn-booking {
  width: 100%;
  padding: 12px;
  font-size: 16px;
  font-weight: 600;
  border-radius: 8px;
  border: none;
}

/* Doctors Section */
.doctors-section {
  padding: 80px 0;
  background: white;
}

.view-all-link {
  color: #2196F3;
  font-weight: 600;
  text-decoration: none;
  display: inline-block;
  margin-top: 12px;
}

.view-all-link:hover {
  text-decoration: underline;
}

.doctor-card {
  background: white;
  border: 1px solid #e0e0e0;
  border-radius: 16px;
  overflow: hidden;
  transition: all 0.3s;
}

.doctor-card:hover {
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
  transform: translateY(-5px);
}

.doctor-image {
  width: 100%;
  height: 280px;
  overflow: hidden;
  background: linear-gradient(135deg, #E0F2F1 0%, #B2DFDB 100%);
}

.doctor-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.doctor-info {
  padding: 20px;
}

.doctor-name {
  font-size: 18px;
  font-weight: 600;
  color: #1a1a1a;
  margin-bottom: 4px;
}

.doctor-specialty {
  font-size: 14px;
  color: #2196F3;
  margin-bottom: 8px;
}

.doctor-description {
  font-size: 14px;
  color: #666;
  line-height: 1.5;
}

/* CTA Section */
.cta-section {
  padding: 80px 0;
  background: #f8fafc;
}

.cta-title {
  font-size: 36px;
  font-weight: 700;
  color: #1a1a1a;
  margin-bottom: 20px;
}

.cta-description {
  font-size: 16px;
  color: #666;
  margin-bottom: 32px;
}

.cta-buttons {
  display: flex;
  gap: 16px;
  justify-content: center;
  flex-wrap: wrap;
}

.btn-cta {
  padding: 14px 32px;
  font-size: 16px;
  font-weight: 600;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-cta i {
  font-size: 18px;
}

/* Footer Section */
.footer-section {
  padding: 60px 0 20px;
  background: #1a1a1a;
  color: #999;
}

.footer-logo {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 16px;
  font-size: 20px;
  font-weight: 600;
  color: white;
}

.footer-logo i {
  font-size: 32px;
  color: #2196F3;
}

.footer-description {
  font-size: 14px;
  line-height: 1.6;
  margin-bottom: 20px;
}

.footer-social {
  display: flex;
  gap: 12px;
}

.footer-social a {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  background: #2a2a2a;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  transition: all 0.3s;
}

.footer-social a:hover {
  background: #2196F3;
}

.footer-title {
  font-size: 16px;
  font-weight: 600;
  color: white;
  margin-bottom: 20px;
}

.footer-links {
  list-style: none;
  padding: 0;
}

.footer-links li {
  margin-bottom: 12px;
}

.footer-links a {
  color: #999;
  text-decoration: none;
  font-size: 14px;
  transition: color 0.3s;
}

.footer-links a:hover {
  color: #2196F3;
}

.footer-contact {
  list-style: none;
  padding: 0;
}

.footer-contact li {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 12px;
  font-size: 14px;
}

.footer-contact i {
  color: #2196F3;
  width: 20px;
}

.footer-bottom {
  margin-top: 40px;
  padding-top: 20px;
  border-top: 1px solid #2a2a2a;
  text-align: center;
}

.footer-bottom p {
  margin: 0;
  font-size: 14px;
}

/* Responsive */
@media (max-width: 991px) {
  .hero-title {
    font-size: 36px;
  }

  .section-title {
    font-size: 28px;
  }

  .home-care-title,
  .cta-title {
    font-size: 28px;
  }
}

@media (max-width: 767px) {
  .hero-section {
    padding: 80px 0 40px;
  }

  .hero-title {
    font-size: 28px;
  }

  .section-title {
    font-size: 24px;
  }

  .home-care-title,
  .cta-title {
    font-size: 24px;
  }

  .hero-buttons {
    flex-direction: column;
  }

  .btn-hero {
    width: 100%;
  }

  .cta-buttons {
    flex-direction: column;
  }

  .btn-cta {
    width: 100%;
    justify-content: center;
  }
}
</style>
