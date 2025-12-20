<template>
  <!-- Dịch vụ -->
  <div>
    <div class="text-center my-5">
      <h2 class="fw-bold" style="color: #005EB8;">
        DỊCH VỤ
      </h2>
      <p class="fw-smeibold"
        style="color: #005EB8; font-size: 18px;margin-top: 20px; font-family: Arial, Helvetica, sans-serif;">
        DỊCH VỤ Y TẾ ĐA DẠNG - NHANH CHÓNG - TẬN TÂM - AN TOÀN - HIỆU QUẢ
      </p>
      <div class="container my-5">
        <div class="row text-center">
          <!-- Item 1 -->
          <div class="col-12 col-md-6 col-lg-3 mb-4">
            <div class="service-item">
              <img src="https://phongkhamviethuong.vn/wp-content/uploads/2024/11/20190926_BHYT.jpg"
                alt="Khám bệnh BHYT" class="img-fluid mb-3">
              <h6 class="fw-semibold text-dark">KHÁM BỆNH BHYT - DỊCH VỤ</h6>
            </div>
          </div>

          <!-- Item 2 -->
          <div class="col-12 col-md-6 col-lg-3 mb-4">
            <div class="service-item">
              <img src="https://phongkhamviethuong.vn/wp-content/uploads/2024/11/freepik-5839697.jpg"
                alt="Khám bệnh tại nhà" class="img-fluid mb-3">
              <h6 class="fw-semibold text-dark">KHÁM BỆNH TẠI NHÀ</h6>
            </div>
          </div>

          <!-- Item 3 -->
          <div class="col-12 col-md-6 col-lg-3 mb-4">
            <div class="service-item">
              <img src="https://phongkhamviethuong.vn/wp-content/uploads/2024/11/ksk_10240607.jpg" alt="Nội soi"
                class="img-fluid mb-3">
              <h6 class="fw-semibold text-dark">KHÁM SỨC KHỎE ĐI HỌC</h6>
            </div>
          </div>

          <!-- Item 4 -->
          <div class="col-12 col-md-6 col-lg-3 mb-4">
            <div class="service-item">
              <img
                src="https://phongkhamviethuong.vn/wp-content/uploads/2024/11/doctor-with-digital-tablet-2048x1367.jpg"
                alt="Đặt lịch hẹn" class="img-fluid mb-3">
              <h6 class="fw-semibold text-dark">ĐẶT LỊCH HẸN KHÁM BỆNH</h6>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// No props or logic needed for now
</script>

<style scoped>
.service-item {
  cursor: pointer;
  transition: transform 0.3s ease;
}

.service-item:hover {
  transform: translateY(-5px);
}

.service-item img {
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}
</style>

