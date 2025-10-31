<template>
  <div>
    <!-- Banner Layout: Left carousel + Right fixed banners -->
    <div class="container-xxl" style="margin-top: 100px;">
      <div class="row g-3">
        <!-- LEFT: main carousel -->
        <div class="col-lg-8">
          <div class="main-banner rounded-3 overflow-hidden">
          <div id="bannerCarousel" class="carousel slide h-100" data-bs-ride="carousel" data-bs-interval="3000">
            <div class="carousel-inner h-100">
              <div class="carousel-item active h-100">
                <img src="https://production-cdn.pharmacity.io/digital/1590x0/plain/e-com/images/banners/20250926032853-0-Banner30ngaytuanthu1590x604.png?versionId=X6nfBWyh49iqMInsFSAmOdeFLcjL4reX" class="d-block w-100 h-100" style="object-fit:cover;" alt="Banner 1" />
              </div>
              <div class="carousel-item h-100">
                <img src="https://production-cdn.pharmacity.io/digital/1590x0/plain/e-com/images/banners/20240510022448-0-THUCUDOIMOI%20BANNERWEB_590x604.png" class="d-block w-100 h-100" style="object-fit:cover;" alt="Banner 2" />
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
            <img src="https://nhathuocminhchau.com/storage/uploads/logo/bd2-9807-hinh.png" class="w-100" style="height:195px;object-fit:cover;border-radius:10px;" alt="Side 1" />
          </a>
          <a href="#">
            <img src="https://production-cdn.pharmacity.io/digital/778x0/plain/e-com/images/banners/20250513024810-0-389x143-sub.png?versionId=BXtOBlz3nxYP6iHcXjIhDq5qMmuBK1ku" class="w-100" style="height:200px;object-fit:cover;border-radius:10px;" alt="Side 2" />
          </a>
        </div>
      </div>
    </div>
   
    <!-- Introduction -->
    <div class="container mt-4 mt-md-5" id="section_introduction3" data-section-name="Introduction3" data-section-active="true">
      <div class="row align-items-center">
        <div class="col-lg-6 col-md-12 mb-4 mb-lg-0">
          <div class="MuiBox-root css-11mgf69">
            <div class="MuiBox-root css-11icu4z">
              <h1 class="hero-title mb-3">Dược sĩ tận tâm, tư vấn chuyên nghiệp</h1>
              <p class="hero-description">Đội ngũ Dược sĩ được đào tạo bài bản, có chuyên môn cao và giàu kinh nghiệm. Luôn đặt lợi ích khách hàng lên hàng đầu, sẵn sàng tư vấn, giải đáp thắc mắc và hướng dẫn sử dụng sản phẩm hiệu quả, an toàn.</p>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-12">
          <div class="MuiBox-root css-1g29oey text-center text-lg-end">
            <img loading="lazy" alt="Introduction" src="https://cdn.kiotvietweb.vn/page_builder_default_config/pharmacy/theme_2/homepage/introduction/picture_1.webp" class="hero-image" style="max-width: 100%; height: auto; border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.1);"/>
          </div>
        </div>
      </div>
    </div>

     <!-- show icon -->
    <div class="container py-5" style="margin-top: 20px;">
    <div class="row text-center">
      <!-- Item 1 -->
      <div class="col-12 col-md-6 col-lg-3 mb-4">
        <div class="p-4">
          <div class="d-inline-flex align-items-center justify-content-center mb-3 rounded" 
              style="width:80px; height:80px; background:#e6f7ff;">
            <img src="https://cdn.kiotvietweb.vn/page_builder_default_config/pharmacy/theme_2/homepage/service/picture_1.webp" alt="Cam kết 100%" style="width:40px; height:40px;">
          </div>
          <h6 class="fw-bold text-primary">CAM KẾT 100%</h6>
          <p class="text-muted mb-0">Thuốc chính hãng, đa dạng và chuyên sâu</p>
        </div>
    </div>

      <!-- Item 2 -->
      <div class="col-12 col-md-6 col-lg-3 mb-4">
        <div class="p-4">
          <div class="d-inline-flex align-items-center justify-content-center mb-3 rounded" 
              style="width:80px; height:80px; background:#f0f0ff;">
            <img src="https://cdn.kiotvietweb.vn/page_builder_default_config/pharmacy/theme_2/homepage/service/picture_2.webp" alt="Giao hàng nhanh chóng" style="width:40px; height:40px;">
          </div>
          <h6 class="fw-bold text-primary">GIAO HÀNG NHANH CHÓNG</h6>
          <p class="text-muted mb-0">Giao hàng tận nhà hoặc nhận tại cửa hàng</p>
        </div>
      </div>

      <!-- Item 3 -->
      <div class="col-12 col-md-6 col-lg-3 mb-4">
        <div class="p-4">
          <div class="d-inline-flex align-items-center justify-content-center mb-3 rounded" 
              style="width:80px; height:80px; background:#eaffea;">
            <img src="https://cdn.kiotvietweb.vn/page_builder_default_config/pharmacy/theme_2/homepage/service/picture_3.webp" alt="Đổi trả trong 30 ngày" style="width:40px; height:40px;">
          </div>
          <h6 class="fw-bold text-primary">ĐỔI TRẢ TRONG 30 NGÀY</h6>
          <p class="text-muted mb-0">Kể từ ngày mua hàng và hoàn trả trong tháng</p>
        </div>
      </div>

      <!-- Item 4 -->
      <div class="col-12 col-md-6 col-lg-3 mb-4">
        <div class="p-4">
          <div class="d-inline-flex align-items-center justify-content-center mb-3 rounded" 
              style="width:80px; height:80px; background:#ffeaea;">
            <img src="https://cdn.kiotvietweb.vn/page_builder_default_config/pharmacy/theme_2/homepage/service/picture_4.webp" alt="Đa dạng sản phẩm" style="width:40px; height:40px;">
          </div>
          <h6 class="fw-bold text-primary">ĐA DẠNG SẢN PHẨM</h6>
          <p class="text-muted mb-0">Đa dạng các loại thuốc, thực phẩm chức năng</p>
        </div>
      </div>
    </div>
  </div>

    <!-- Hiển thị sản phẩm -->
    <!-- SẢN PHẨM MỚI NHẤT: Medicines -->
    <div class="container mt-5" v-if="medicines && medicines.length">
      <div class="row mb-4">
        <div class="col-12">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="product-section-title mb-0">Thuốc nổi bật</h4>
            <Link href="/products" class="text-primary text-decoration-none" style="font-size: 18px;">Xem thêm</Link>
          </div>
        </div>
      </div>
      <div class="row g-4 product-grid mb-5">
        <div class="col-lg-3 col-md-4 col-sm-6" v-for="m in medicines" :key="`m-` + m.id">
          <div class="product-card-modern">
            <div class="product-img-wrapper" @click="goToProductDetail({ id: m.id, type: 'medicine' })">
              <img
                :src="m.image_url || 'https://via.placeholder.com/150'"
                class="product-img"
                :alt="m.ten_thuoc"
                @error="handleImageError"
              />
            </div>
            <div class="product-body">
              <div class="product-title-modern">{{ m.ten_thuoc }}</div>
              <div class="product-price-modern">
                {{ m.gia_ban_formatted }}
              </div>
              <button class="btn btn-primary product-btn" @click="addToCart({ id: m.id, type: 'medicine' })">
                Thêm vào giỏ
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- SẢN PHẨM MỚI NHẤT: Goods -->
    <div class="container mt-4" v-if="goods && goods.length">
      <div class="row mb-4">
        <div class="col-12">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="product-section-title mb-0">Vật tư y tế nổi bật</h4>
            <Link href="/products" class="text-primary text-decoration-none" style="font-size: 18px;">Xem thêm</Link>
          </div>
        </div>
      </div>
      <div class="row g-4 product-grid">
        <div class="col-lg-3 col-md-4 col-sm-6" v-for="g in goods" :key="`g-` + g.id">
          <div class="product-card-modern">
            <div class="product-img-wrapper" @click="goToProductDetail({ id: g.id, type: 'goods' })">
              <img
                :src="g.image_url || 'https://via.placeholder.com/150'"
                class="product-img"
                :alt="g.ten_hang_hoa"
                @error="handleImageError"
              />
            </div>
            <div class="product-body">
              <div class="product-title-modern">{{ g.ten_hang_hoa }}</div>
              <div class="product-price-modern">
                {{ g.gia_ban_formatted }}
              </div>
              <button class="btn btn-primary product-btn" @click="addToCart({ id: g.id, type: 'goods' })">
                Thêm vào giỏ
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Dịch vụ -->
    <div>
      <div class="text-center my-5">
        <h2 class="fw-bold" style="color: #005EB8;">
          DỊCH VỤ
        </h2>
        <p class="fw-smeibold" style="color: #005EB8; font-size: 18px;margin-top: 20px; font-family: Arial, Helvetica, sans-serif;">
          DỊCH VỤ Y TẾ ĐA DẠNG - NHANH CHÓNG - TẬN TÂM - AN TOÀN - HIỆU QUẢ
        </p>
        <div class="container my-5">
        <div class="row text-center">
          <!-- Item 1 -->
          <div class="col-12 col-md-6 col-lg-3 mb-4">
            <div class="service-item">
              <img src="https://phongkhamviethuong.vn/wp-content/uploads/2024/11/20190926_BHYT.jpg" alt="Khám bệnh BHYT" class="img-fluid mb-3">
              <h6 class="fw-semibold text-dark">KHÁM BỆNH BHYT - DỊCH VỤ</h6>
            </div>
          </div>

          <!-- Item 2 -->
          <div class="col-12 col-md-6 col-lg-3 mb-4">
            <div class="service-item">
              <img src="https://phongkhamviethuong.vn/wp-content/uploads/2024/11/freepik-5839697.jpg" alt="Khám bệnh tại nhà" class="img-fluid mb-3">
              <h6 class="fw-semibold text-dark">KHÁM BỆNH TẠI NHÀ</h6>
            </div>
          </div>

          <!-- Item 3 -->
          <div class="col-12 col-md-6 col-lg-3 mb-4">
            <div class="service-item">
              <img src="https://phongkhamviethuong.vn/wp-content/uploads/2024/11/ksk_10240607.jpg" alt="Nội soi" class="img-fluid mb-3">
              <h6 class="fw-semibold text-dark">KHÁM SỨC KHỎE ĐI HỌC</h6>
            </div>
          </div>

          <!-- Item 4 -->
          <div class="col-12 col-md-6 col-lg-3 mb-4">
            <div class="service-item">
              <img src="https://phongkhamviethuong.vn/wp-content/uploads/2024/11/doctor-with-digital-tablet-2048x1367.jpg" alt="Đặt lịch hẹn" class="img-fluid mb-3">
              <h6 class="fw-semibold text-dark">ĐẶT LỊCH HẸN KHÁM BỆNH</h6>
            </div>
          </div>
        </div>
      </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { Link, router } from '@inertiajs/vue3'


const props = defineProps({
  medicines: { type: Array, default: () => [] },
  goods: { type: Array, default: () => [] },
  auth: { type: Object, default: () => ({ user: null }) }
});

async function addToCart({ id, type }) {
  try {
    const response = await axios.post('/cart/add', { 
      item_id: id, 
      item_type: type, 
      quantity: 1 
    });
    
    if (response.data.success) {
      // 1. Cập nhật số lượng giỏ hàng
      window.dispatchEvent(new CustomEvent('cart-updated'));
      
      // 2. Tự động MỞ dropdown giỏ hàng
      const cartDropdown = document.querySelector('#cartDropdown');
      if (cartDropdown && typeof bootstrap !== 'undefined') {
        const bsDropdown = new bootstrap.Dropdown(cartDropdown);
        bsDropdown.show();
      }
      
      // 3. Load cart items (gọi function từ cart.js)
      if (typeof window.loadCartItems === 'function') {
        setTimeout(() => window.loadCartItems(), 100);
      }
      
      // 4. Hiển thị thông báo
      if (typeof window.showNotification === 'function') {
        window.showNotification('Đã thêm vào giỏ hàng!', 'success');
      }
    }
  } catch (e) { 
    console.error('Error adding to cart:', e);
    if (typeof window.showNotification === 'function') {
      window.showNotification('Có lỗi xảy ra!', 'error');
    }
  }
}

function handleImageError(event) {
  // Fallback image
  event.target.src = '/images/products/đạt.jpg';
}

//chuyển hướng đến trang chi tiết sản phẩm
function goToProductDetail(product) {
  router.visit(`/products/${product.type}/${product.id}`)
}
</script>

<style scoped>
/* Chiều cao banner trái bằng tổng chiều cao 2 banner phải */
.main-banner { height: 360px; }
@media (min-width: 1400px) { .main-banner { height: 420px; } }

/* Bên phải: chia đôi chiều cao, ảnh luôn phủ đầy khung */
.d-lg-flex.flex-column.gap-3 > a { flex: 1 1 0; }
.d-lg-flex.flex-column.gap-3 > a img { width: 100%; height: 100%; object-fit: cover; display: block; border-radius: 10px; }
</style>


<style scoped>
/* Hiển thị trọn ảnh banner */
.banner-image {
  width: 100%;
  height: auto;            /* bỏ ép chiều cao */
  object-fit: contain;     /* không cắt ảnh, có thể xuất hiện viền trống */
  border-radius: 10px;
  background: #fff;        /* nền trắng cho vùng trống nếu có */
  display: block;
  margin: 0 auto;          /* căn giữa khi ảnh hẹp */
}


/* Nếu muốn giới hạn tối đa chiều cao (tuỳ chọn) */
@media (min-width: 1400px) {
  .banner-image { max-height: 520px; } /* chỉnh con số bạn muốn */
}

/* Product grid styles từ Products.vue */
.product-grid {
  margin-bottom: 0;
}

.product-card-modern {
  background: #fff;
  border: 1px solid #e3e6ef;
  border-radius: 14px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  padding: 16px 16px 12px 16px;
  display: flex;
  flex-direction: column;
  align-items: center;
  min-height: 340px;
  position: relative;
  transition: box-shadow 0.2s;
}

.product-card-modern:hover {
  box-shadow: 0 6px 24px rgba(0,0,0,0.12);
  cursor: pointer;
}

.product-img-wrapper {
  width: 100%;
  height: 160px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 10px;
  cursor: pointer;
}

.product-img {
  max-width: 100%;
  max-height: 140px;
  object-fit: contain;
  border-radius: 8px;
  background: #f8f9fb;
}

.product-body {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  flex: 1;
}

.product-title-modern {
  font-size: 1rem;
  font-weight: 500;
  color: #222;
  margin-bottom: 6px;
  min-height: 2.4em;
  max-height: 2.4em;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  line-height: 1.2;
  line-clamp: 2;
}

.product-price-modern {
  color: #1a56db;
  font-size: 1.15rem;
  font-weight: 700;
  margin-bottom: 12px;
}

.product-btn {
  width: 100%;
  border-radius: 8px;
  font-weight: 600;
  font-size: 1rem;
  background: #1a56db;
  border: none;
  color: #fff;
  padding: 8px 0;
  transition: background 0.2s;
}

.product-btn:hover {
  background: #1650cf;
}

@media (max-width: 991px) {
  .product-card-modern {
    min-height: 320px;
    padding: 12px 8px 10px 8px;
  }
  .product-img-wrapper {
    height: 120px;
  }
}

@media (max-width: 767px) {
  .product-card-modern {
    min-height: 280px;
  }
  .product-img-wrapper {
    height: 90px;
  }
  .product-title-modern {
    font-size: 0.95rem;
    min-height: 36px;
  }
  .product-price-modern {
    font-size: 1rem;
  }
}
</style>