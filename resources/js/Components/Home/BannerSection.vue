<template>
  <!-- Banner Layout: Left carousel + Right fixed banners -->
  <div class="container-xxl">
    <div class="row g-3">
      <!-- LEFT: main carousel -->
      <div class="col-lg-8">
        <div class="main-banner rounded-3 overflow-hidden">
          <div id="bannerCarousel" class="carousel slide h-100" data-bs-ride="carousel" data-bs-interval="3000">
            <div class="carousel-inner h-100">
              <div class="carousel-item active h-100">
                <img
                  src="https://production-cdn.pharmacity.io/digital/1590x0/plain/e-com/images/banners/20250926032853-0-Banner30ngaytuanthu1590x604.png?versionId=X6nfBWyh49iqMInsFSAmOdeFLcjL4reX"
                  class="d-block w-100 h-100" style="object-fit:cover;" alt="Banner 1" />
              </div>
              <div class="carousel-item h-100">
                <img
                  src="https://production-cdn.pharmacity.io/digital/1590x0/plain/e-com/images/banners/20240510022448-0-THUCUDOIMOI%20BANNERWEB_590x604.png"
                  class="d-block w-100 h-100" style="object-fit:cover;" alt="Banner 2" />
              </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Next</span>
            </button>
          </div>
        </div>
      </div>
      <!-- RIGHT: two fixed banners -->
      <div class="col-lg-4 d-none d-lg-flex flex-column gap-3">
        <a href="#">
          <img src="https://nhathuocminhchau.com/storage/uploads/logo/bd2-9807-hinh.png" class="w-100"
            style="height:195px;object-fit:cover;border-radius:10px;" alt="Side 1" />
        </a>
        <a href="#">
          <img
            src="https://production-cdn.pharmacity.io/digital/778x0/plain/e-com/images/banners/20250513024810-0-389x143-sub.png?versionId=BXtOBlz3nxYP6iHcXjIhDq5qMmuBK1ku"
            class="w-100" style="height:200px;object-fit:cover;border-radius:10px;" alt="Side 2" />
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
// No props or logic needed for now
</script>

<style scoped>
/* Chiều cao banner trái bằng tổng chiều cao 2 banner phải */
.main-banner {
  height: 360px;
}

@media (min-width: 1400px) {
  .main-banner {
    height: 420px;
  }
}

/* Bên phải: chia đôi chiều cao, ảnh luôn phủ đầy khung */
.d-lg-flex.flex-column.gap-3>a {
  flex: 1 1 0;
}

.d-lg-flex.flex-column.gap-3>a img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  border-radius: 10px;
}
</style>
