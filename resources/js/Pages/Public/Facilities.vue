<template>
  <div class="page-wrapper">
    <Header :auth="auth" />

    <section class="hero-section d-flex align-items-center position-relative overflow-hidden">
      <div class="decoration-dots dots-left"></div>
      <div class="decoration-dots dots-right"></div>

      <div class="container position-relative z-1">
        <div class="row align-items-center">

          <div class="col-lg-6 mb-5 mb-lg-0">
            <div class="hero-text-wrapper pe-lg-5">
              <h1 class="display-4 fw-bold text-dark mb-4">
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
              <swiper :modules="modules" :slides-per-view="1" :space-between="30" :loop="true"
                :autoplay="{ delay: 3000, disableOnInteraction: false }" :grab-cursor="true"
                :pagination="{ clickable: true }" class="doctor-swiper">
                <swiper-slide v-for="(doctor, index) in doctors" :key="index">
                  <div class="slide-content">
                    <img :src="doctor.image" :alt="doctor.name" class="img-fluid main-image">
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
            <div
              class="feature-item d-flex align-items-center justify-content-lg-end mb-4 mb-lg-5 text-lg-end text-start flex-lg-row flex-row-reverse">
              <div class="feature-text me-3 ms-lg-0 me-lg-3 bg-light p-3 rounded-3 shadow-sm">
                <h6 class="fw-bold text-primary mb-1">CAM KẾT 100%</h6>
                <p class="small text-muted mb-0">Thuốc chính hãng, đa dạng và chuyên sâu</p>
              </div>
              <div
                class="icon-box bg-white text-primary shadow rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                <i class="pi pi-users fs-5"></i>
              </div>
            </div>

            <div
              class="feature-item d-flex align-items-center justify-content-lg-end text-lg-end text-start flex-lg-row flex-row-reverse">
              <div class="feature-text me-3 ms-lg-0 me-lg-3 bg-light p-3 rounded-3 shadow-sm">
                <h6 class="fw-bold text-primary mb-1">GIAO HÀNG NHANH CHÓNG</h6>
                <p class="small text-muted mb-0">Giao hàng tận nhà hoặc nhận tại cửa hàng</p>
              </div>
              <div
                class="icon-box bg-white text-primary shadow rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                <i class="pi pi-truck fs-5"></i>
              </div>
            </div>
          </div>

          <div class="col-lg-4 text-center my-4 my-lg-0">
            <div class="profile-image-wrapper position-relative text-center">
              <img :src="doctors[4].image" alt="Dược sĩ Đạt" class="img-fluid main-image rounded-4 shadow-lg" />
            </div>
          </div>

          <div class="col-lg-4">
            <div class="feature-item d-flex align-items-center mb-4 mb-lg-5">
              <div
                class="icon-box bg-white text-primary shadow rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                <i class="pi pi-whatsapp"></i>
              </div>
              <div class="feature-text ms-3 bg-light p-3 rounded-3 shadow-sm">
                <h6 class="fw-bold text-primary mb-1">ĐỔI TRẢ TRONG 30 NGÀY</h6>
                <p class="small text-muted mb-0">Kể từ ngày mua hàng</p>
              </div>
            </div>

            <div class="feature-item d-flex align-items-center">
              <div
                class="icon-box bg-white text-primary shadow rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
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
    <section class="pharmacist-profile-section py-3 position-relative overflow-hidden">
      <div class="bg-decoration-circle"></div>

      <div class="container position-relative z-1">
        <div class="row align-items-center">
          <div class="col-lg-6 mb-5 mb-lg-0">
            <div class="profile-content pe-lg-5">
              <h2 class="display-5 fw-bold text-dark mb-1">Dược sĩ Đạt</h2>
              <p class="text-primary fw-bold fs-5 mb-4">Chuyên gia tư vấn sức khỏe & Dược lâm sàng</p>

              <p class="text-muted lead mb-4">
                Với hơn 10 năm kinh nghiệm trong lĩnh vực dược phẩm và chăm sóc sức khỏe cộng đồng.
                Tôi luôn tâm niệm "Sức khỏe của bệnh nhân là ưu tiên hàng đầu", cam kết tư vấn
                đúng thuốc, đúng bệnh và an toàn tuyệt đối.
              </p>

              <div class="row g-3 mb-4">
                <div class="col-sm-6" v-for="(skill, index) in skills" :key="index">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                    <span class="fw-medium text-dark">{{ skill }}</span>
                  </div>
                </div>
              </div>

              <div class="d-flex gap-3">
                <button class="btn btn-primary rounded-pill px-4 py-2 shadow-sm">
                  <i class="bi bi-chat-dots me-2"></i>Nhắn tin tư vấn
                </button>
                <button class="btn btn-outline-dark rounded-pill px-4 py-2">
                  Xem hồ sơ
                </button>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="profile-image-wrapper position-relative text-center">
              <img :src="doctors[3].image" alt="Dược sĩ Đạt" class="img-fluid main-image rounded-4 shadow-lg" />
            </div>
          </div>

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

// Danh sách kỹ năng/điểm mạnh
const skills = ref([
  'Tư vấn sử dụng thuốc an toàn',
  'Chăm sóc bệnh mạn tính',
  'Dược lý lâm sàng',
  'Dinh dưỡng sức khỏe'
]);

const modules = [Autoplay, Pagination];

const doctors = ref([
  {
    id: 1,
    name: 'Dr. Nguyen Van A',
    image: 'https://img.freepik.com/free-photo/smiling-doctor-with-stethoscope-isolated-grey_651396-974.jpg?w=740'
  },
  {
    id: 2,
    name: 'Dr. Tran Thi B',
    image: 'https://img.freepik.com/free-photo/portrait-smiling-handsome-male-doctor-man_171337-5055.jpg'
  },
  {
    id: 3,
    name: 'Dr. Le Van C',
    image: 'https://img.freepik.com/free-photo/female-doctor-hospital-with-stethoscope_23-2148827775.jpg'
  },
  {
    id: 4,
    name: 'Dr. Nguyen Van A',
    image: '/storage/avatars/doctors/dsd.jpg'
  },
  {
    id: 5,
    name: 'Dr. Nguyen Van A',
    image: '/storage/banner/healthy.jpg'
  },
]);
</script>

<style scoped>
.hero-section {
  background-color: #F0F8FF;
  min-height: 600px;
  padding: 80px 0;
}

.image-wrapper {
  background-color: #e1efff;
  padding: 20px;
  position: relative;
  height: 580px;
  /* Giữ nguyên chiều cao ảnh dọc theo yêu cầu */
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
  width: 100%;
  height: 500px;
  /* Cố định chiều cao để ảnh đều nhau */
  object-fit: cover;
  /* Cắt ảnh vừa khung, không méo */
  object-position: center;
  /* Căn giữa ảnh */
  display: block;
  border-radius: 0.5rem;
  /* Bo góc nhẹ cho ảnh bên trong */
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

.dots-left {
  bottom: 20px;
  left: 20px;
}

.dots-right {
  top: 20px;
  right: 20px;
}

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

/* Ảnh trong features section - hiển thị toàn bộ */
.features-section .profile-image-wrapper .main-image {
  width: 100%;
  max-width: 100%;
  height: auto;
  object-fit: contain;
  object-position: center;
  border-radius: 20px;
  border: 5px solid #f8f9fa;
}



/* --- RESPONSIVE --- */
@media (max-width: 991px) {

  /* Tablet & Mobile */
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

  .pharmacist-profile-section {
    background-color: #fff;
  }

  /* --- Xử lý ảnh --- */
  .profile-image-wrapper {
    padding: 20px;
    /* Tạo khoảng trống để Box nổi không bị cắt */
  }

  .main-image {
    width: 100%;
    max-width: 500px;
    /* Giới hạn chiều rộng ảnh cho đẹp */
    height: auto;
    object-fit: cover;
    border-radius: 20px;
    /* Tạo viền mờ xung quanh ảnh để tách biệt nền */
    border: 5px solid #f8f9fa;
  }

  /* --- Box nổi Kinh nghiệm --- */
  .experience-badge {
    bottom: 40px;
    /* Cách đáy */
    left: 40px;
    /* Cách trái */
    /* Animation nhẹ nhàng */
    animation: float 3s ease-in-out infinite;
    z-index: 2;
    min-width: 180px;
  }

  .icon-box {
    width: 45px;
    height: 45px;
    font-size: 1.2rem;
  }

  /* Hiệu ứng bay nhẹ cho box kinh nghiệm */
  @keyframes float {
    0% {
      transform: translateY(0px);
    }

    50% {
      transform: translateY(-10px);
    }

    100% {
      transform: translateY(0px);
    }
  }

  /* --- Họa tiết nền --- */
  .bg-decoration-circle {
    position: absolute;
    top: -50px;
    right: -50px;
    width: 300px;
    height: 300px;
    background-color: #e1efff;
    border-radius: 50%;
    opacity: 0.5;
    z-index: 0;
  }

  /* Responsive */
  @media (max-width: 991px) {
    .pharmacist-profile-section {
      text-align: center;
      /* Mobile căn giữa */
    }

    .profile-content {
      padding-right: 0 !important;
    }

    /* Căn giữa checklist trên mobile */
    .row.g-3 {
      justify-content: center;
      text-align: left;
      /* Icon và chữ vẫn căn trái cho đẹp */
      display: inline-flex;
      flex-wrap: wrap;
      width: 100%;
    }

    /* Căn giữa nút bấm */
    .d-flex.gap-3 {
      justify-content: center;
    }

    /* Điều chỉnh vị trí Box nổi trên mobile */
    .experience-badge {
      left: 50%;
      transform: translateX(-50%);
      /* Căn giữa box */
      bottom: -10px;
      animation: none;
      /* Tắt animation trên mobile để đỡ rối */
    }

    .profile-image-wrapper {
      margin-top: 2rem;
    }
  }

  /* Đảo icon lên trước text cho cột bên trái */
  .col-lg-4:first-child .icon-box {
    order: -1;
  }
}
</style>