<template>
  <div class="page-wrapper">
    <Header :auth="auth" />

    <section class="hero-section d-flex align-items-center position-relative overflow-hidden">
      <div class="decoration-dots dots-left"></div>
      <div class="decoration-dots dots-right"></div>

      <div class="container position-relative z-1">
        <div class="row align-items-center">
          
          <div class="col-lg-6 mb-5 mb-lg-0">
            <div class="hero-text-wrapper pe-lg-5"> <h1 class="display-4 fw-bold text-dark mb-4">
                Chăm Sóc Sức Khỏe <br>
                <span class="text-primary">Tận Tâm Tại</span>
              </h1>
              <p class="lead text-muted mb-4">
                Luôn lắng nghe – Luôn đồng hành. Chúng tôi mang đến môi trường 
                khám chữa bệnh thân thiện, an toàn và hiệu quả.
              </p>
              <button class="btn btn-primary btn-lg px-4 py-2 rounded-pill shadow-sm">
                Tìm hiểu ngay
              </button>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="image-wrapper rounded-3 overflow-hidden shadow-lg"> 
              <swiper
                :modules="modules"
                :slides-per-view="1"
                :space-between="30"
                :loop="true"
                :autoplay="{ delay: 3000, disableOnInteraction: false }"
                :grab-cursor="true"
                :pagination="{ clickable: true }"
                class="doctor-swiper"
              >
                <swiper-slide v-for="(doctor, index) in doctors" :key="index">
                  <div class="slide-content">
                    <img 
                      :src="doctor.image" 
                      :alt="doctor.name" 
                      class="img-fluid main-image"
                    >
                  </div>
                </swiper-slide>
              </swiper>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="features-section py-5">
      <div class="container">    
        <div class="section-header text-center mb-5">
          <h3>Phòng Khám Đa Khoa Tiện Ích</h3>
          <div class="d-flex justify-content-center">
            <p class="text-muted w-75">
              Dịch vụ y tế chất lượng cao – Đội ngũ bác sĩ giỏi, tận tâm – Trang thiết bị hiện đại. 
              Chăm sóc sức khỏe toàn diện cho bạn và gia đình.
            </p>
          </div>
        </div>
        <div class="row align-items-center g-4">  
          <div class="col-lg-4">
            <div class="feature-item d-flex align-items-center justify-content-lg-end mb-4 mb-lg-5 text-lg-end text-start flex-lg-row flex-row-reverse">
              <div class="feature-text me-3 ms-lg-0 me-lg-3 bg-light p-3 rounded-3 shadow-sm">
                <h6 class="fw-bold text-primary mb-1">CAM KẾT 100%</h6>
                <p class="small text-muted mb-0">Thuốc chính hãng, đa dạng và chuyên sâu</p>
              </div>
              <div class="icon-box bg-white text-primary shadow rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                <i class="pi pi-users fs-5"></i>
              </div>
            </div>

            <div class="feature-item d-flex align-items-center justify-content-lg-end text-lg-end text-start flex-lg-row flex-row-reverse">
              <div class="feature-text me-3 ms-lg-0 me-lg-3 bg-light p-3 rounded-3 shadow-sm">
                <h6 class="fw-bold text-primary mb-1">GIAO HÀNG NHANH CHÓNG</h6>
                <p class="small text-muted mb-0">Giao hàng tận nhà hoặc nhận tại cửa hàng</p>
              </div>
              <div class="icon-box bg-white text-primary shadow rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                <i class="pi pi-truck fs-5"></i>
              </div>
            </div>
          </div>

          <div class="col-lg-4 text-center my-4 my-lg-0">
            <div class="feature-center-image position-relative">
              <img 
                src="https://img.freepik.com/free-photo/medium-shot-doctor-reading-paper_23-2149367520.jpg?w=740" 
                alt="Doctor Working" 
                class="img-fluid rounded-4 shadow-lg"
              >
            </div>
          </div>

          <div class="col-lg-4">
            <div class="feature-item d-flex align-items-center mb-4 mb-lg-5">
              <div class="icon-box bg-white text-primary shadow rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                <i class="pi pi-whatsapp"></i>
              </div>
              <div class="feature-text ms-3 bg-light p-3 rounded-3 shadow-sm">
                <h6 class="fw-bold text-primary mb-1">ĐỔI TRẢ TRONG 30 NGÀY</h6>
                <p class="small text-muted mb-0">Kể từ ngày mua hàng</p>
              </div>
            </div>

            <div class="feature-item d-flex align-items-center">
              <div class="icon-box bg-white text-primary shadow rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                <i class="pi pi-box"></i>
              </div>
              <div class="feature-text ms-3 bg-light p-3 rounded-3 shadow-sm">
                <h6 class="fw-bold text-primary mb-1">ĐA DẠNG SẢN PHẨM</h6>
                <p class="small text-muted mb-0">Đa dạng các loại thuốc, thực phẩm chức năng</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section class="services-section py-5">
      <div class="container">
        <div class="section-header text-center mb-5">
          <h3>
            Vì sức khỏe của bạn, chúng tôi luôn sẵn sàng đồng hành
          </h3>
          <p class="text-muted section-description">
            Chúng tôi cam kết cung cấp sản phẩm chính hãng, đa dạng từ thuốc cho các nhóm bệnh,
            thực phẩm chức năng, sản phẩm chăm sóc sức khỏe, sắc đẹp,
            thiết bị y tế thuộc các thương hiệu hàng đầu thế giới
          </p>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import Header from './components/Header.vue'
import { ref } from 'vue';

import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css';
import 'swiper/css/pagination';
import { Autoplay, Pagination } from 'swiper/modules';

const props = defineProps({
  auth: { type: Object, default: () => ({ user: null }) }
})

const modules = [Autoplay, Pagination];

const doctors = ref([
  {
    id: 1,
    name: 'Dr. Nguyen Van A',
    image: '/storage/avatars/doctors/dsd.jpg'
  },
  {
    id: 2,
    name: 'Dr. Tran Thi B',
    image: '/storage/avatars/doctors/diddy.jpg'
  },
]);
</script>

<style scoped>
/* --- STYLE HERO SECTION (GIỮ NGUYÊN CỦA BẠN) --- */
.hero-section {
  background-color: #F0F8FF;
  min-height: 600px;
  padding: 80px 0;
}

.image-wrapper {
  background-color: #e1efff;
  padding: 20px;
  position: relative;
  height: 580px; /* Giữ nguyên chiều cao ảnh dọc theo yêu cầu */
  display: flex;
  align-items: center;
  justify-content: center;
}

.doctor-swiper {
  width: 100%;
  height: 100%;
  background: none;
}

.main-image {
  width: auto;
  height: 100%;
  object-fit: contain;
  object-position: center;
  display: block;
  border-radius: 0.5rem;
}

.decoration-dots {
  position: absolute;
  width: 150px;
  height: 150px;
  background-image: radial-gradient(#b0c4de 20%, transparent 20%);
  background-position: 0 0;
  background-size: 10px 10px;
  opacity: 0.6;
}
.dots-left { bottom: 20px; left: 20px; }
.dots-right { top: 20px; right: 20px; }

:deep(.swiper-pagination-bullet-active) {
  background-color: #0d6efd;
}


/* --- STYLE FEATURES SECTION (MỚI THÊM VÀO) --- */
.features-section {
  background-color: #fff;
}

.section-header .section-description {
  max-width: 720px;
  margin: 0 auto;
  line-height: 1.7;
}

/* Icon tròn */
.icon-box {
  width: 55px;
  height: 55px;
  border: 1px solid #eef2f6;
  transition: all 0.3s ease;
}

/* Box Text */
.feature-text {
  max-width: 300px;
  transition: transform 0.3s ease;
}


/* --- RESPONSIVE --- */
@media (max-width: 991px) { /* Tablet & Mobile */
  .hero-section {
    text-align: center;
    min-height: auto;
  }
  
  .hero-text-wrapper {
    padding-right: 0 !important;
  }

  .image-wrapper {
    padding: 15px;
    height: 450px;
  }

  .main-image {
    max-width: 100%;
  }

  /* Responsive cho Features Section */
  .feature-item {
    justify-content: flex-start !important;
    text-align: left !important;
    flex-direction: row !important;
  }
  
  .feature-text {
    margin-right: 0 !important;
    margin-left: 1rem !important;
    max-width: 100%;
  }

  /* Đảo icon lên trước text cho cột bên trái */
  .col-lg-4:first-child .icon-box {
    order: -1; 
  }
}
</style>