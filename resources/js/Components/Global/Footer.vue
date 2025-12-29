<template>
  <footer class="footer-dark">
    <div class="container pt-5 pb-4">
      <div class="row g-4">

        <div class="col-lg-4 col-md-6">
          <div class="footer-brand mb-4">
            <span class="fw-bold fs-4 text-white">Pharma PCT</span>
          </div>
          <p class="text-gray-400 mb-4 text-justify">
            Sứ mệnh của chúng tôi là cung cấp các giải pháp dược phẩm tiên tiến,
            cải thiện sức khỏe và cuộc sống của mọi người trên toàn cầu thông qua
            nghiên cứu và đổi mới không ngừng.
          </p>
        </div>

        <div class="col-lg-4 col-md-6">
          <h5 class="text-white fw-bold mb-4">Thông tin liên hệ</h5>
          <ul class="list-unstyled contact-info">
            <li class="d-flex mb-3">
              <i class="fas fa-map-marker-alt mt-1 me-3 text-white"></i>
              <span class="text-gray-400">
                <strong>Trụ sở chính:</strong><br>
                12 Đô Lương, Phường 11, Vũng Tàu,<br>
                Bà Rịa - Vũng Tàu, Việt Nam
              </span>
            </li>
            <li class="d-flex mb-3">
              <i class="fas fa-phone-alt mt-1 me-3 text-white"></i>
              <span class="text-gray-400">
                <strong>Điện thoại:</strong><br>
                <a href="tel:0901645269" class="text-gray-400 text-decoration-none hover-white">
                  0901 645 269
                </a>
                <br>
                <small>(Thứ 2 - CN: 7:00 - 22:00)</small>
              </span>
            </li>
            <li class="d-flex mb-3">
              <i class="fas fa-envelope mt-1 me-3 text-white"></i>
              <span class="text-gray-400">
                <strong>Email:</strong><br>
                <a href="mailto:info@pharmapct.vn" class="text-gray-400 text-decoration-none hover-white">
                  info@pharmapct.vn
                </a>
              </span>
            </li>
          </ul>
        </div>

        <div class="col-lg-4 col-md-12">
          <div class="row">
            <div class="col-12 mb-4">
              <div class="social-links d-flex gap-3">
                <a href="https://www.facebook.com/chokiot" target="_blank" class="social-btn">
                  <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="social-btn">
                  <i class="fab fa-twitter"></i>
                </a>
                <a href="#" class="social-btn">
                  <i class="fab fa-youtube"></i>
                </a>
                <a href="#" class="social-btn">
                  <i class="fab fa-linkedin-in"></i>
                </a>
              </div>
            </div>

            <div class="col-12">
              <ul class="list-unstyled footer-links">
                <li class="mb-2"><a href="#">Chính sách bảo mật</a></li>
                <li class="mb-2"><a href="#">Điều khoản sử dụng</a></li>
                <li class="mb-2"><a href="#">Sơ đồ trang web</a></li>
                <li class="mb-2"><a href="#">Khả năng truy cập</a></li>
              </ul>
            </div>
            <div class="payment-support mt-3">
              <h6 class="text-white fw-bold mb-3">Hỗ trợ thanh toán</h6>
              <div class="payment-icons">
                <div class="payment-icon bg-white rounded p-1">
                  <img
                    src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Visa_Inc._logo.svg/2560px-Visa_Inc._logo.svg.png"
                    alt="Visa" title="Visa">
                </div>
                <div class="payment-icon bg-white rounded p-1">
                  <img
                    src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/Mastercard-logo.svg/1280px-Mastercard-logo.svg.png"
                    alt="MasterCard" title="MasterCard">
                </div>
                <div class="payment-icon bg-white rounded p-1">
                  <img
                    src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/40/JCB_logo.svg/1200px-JCB_logo.svg.png"
                    alt="JCB" title="JCB">
                </div>
                <div class="payment-icon bg-white rounded p-1">
                  <img src="https://upload.wikimedia.org/wikipedia/vi/f/fe/MoMo_Logo.png" alt="MoMo" title="MoMo">
                </div>
                <div class="payment-icon bg-white rounded p-1">
                  <img src="https://cdn.haitrieu.com/wp-content/uploads/2022/10/Logo-ZaloPay-Square.png" alt="ZaloPay"
                    title="ZaloPay">
                </div>
                <div class="payment-icon bg-white rounded p-1">
                  <img src="https://vnpay.vn/assets/images/logo-icon/logo-primary.svg" alt="VNPAY" title="VNPAY">
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <div class="footer-bottom py-3">
      <div class="container text-center">
        <p class="mb-0 text-gray-500 small">
          © {{ currentYear }} Pharma PCT. Bản quyền thuộc về công ty TNHH Pharma PCT Việt Nam.
        </p>
      </div>
    </div>
  </footer>

  <div class="floating-contacts">
    <a href="https://zalo.me/0376193244" target="_blank" class="floating-btn floating-zalo">
      <i class="fas fa-comment-dots"></i> </a>

    <a href="https://www.facebook.com/dat.hocongthien" target="_blank" class="floating-btn floating-facebook">
      <i class="fab fa-facebook-messenger"></i>
    </a>

    <ChatbotFloatingButton />
    <VchatWidget :auth="auth" />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import VchatWidget from '@/Components/Global/Chat/VchatWidget.vue'
import ChatbotFloatingButton from '@/Components/Global/Chat/ChatbotFloatingButton.vue'

const props = defineProps({
  auth: {
    type: Object,
    default: () => ({ user: null })
  }
})

const currentYear = computed(() => new Date().getFullYear())
</script>

<style scoped>
/* 1. Dark Footer Styles (Giống ảnh mẫu) */
.footer-dark {
  background-color: #162E44;
  /* Màu xanh đen đậm */
  color: #fff;
  font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
  margin-top: 50px;
}

.text-gray-400 {
  color: #cbd5e1;
  /* Màu chữ xám sáng */
  line-height: 1.6;
  font-size: 0.95rem;
}

.text-gray-500 {
  color: #94a3b8;
}

.hover-white:hover {
  color: #fff !important;
  text-decoration: underline !important;
}

/* 2. Social Buttons */
.social-btn {
  width: 40px;
  height: 40px;
  background-color: rgba(255, 255, 255, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
  /* Bo góc vuông nhẹ giống mẫu */
  color: #fff;
  text-decoration: none;
  transition: all 0.3s ease;
}

/* CSS cho phần thanh toán */
.payment-icons {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  grid-template-rows: repeat(2, 1fr);
  gap: 8px;
  max-width: 200px; /* Giới hạn chiều rộng để icon không quá rộng */
}

.payment-icon {
  width: 100%;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  transition: transform 0.2s;
}

.payment-icon:hover {
  transform: translateY(-2px);
}

.payment-icon img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.social-btn:hover {
  background-color: #fff;
  color: #0f1c2e;
}

/* 3. Footer Links */
.footer-links a {
  color: #cbd5e1;
  text-decoration: none;
  font-size: 0.95rem;
  transition: color 0.2s;
}

.footer-links a:hover {
  color: #fff;
}

/* 4. Bottom Bar */
.footer-bottom {
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  background-color: #0F2233;
  /* Đậm hơn nền chính chút */
}

/* 5. Floating Contacts (Giữ nguyên style cũ của bạn) */
.floating-contacts {
  position: fixed;
  right: 20px;
  bottom: 80px;
  z-index: 1000;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  align-items: center;
}

.floating-btn {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  text-decoration: none;
  box-shadow: 0 3px 15px rgba(0, 0, 0, 0.2);
  transition: all 0.3s ease;
  font-size: 1.2rem;
}

.floating-zalo {
  background: #28a745;
  /* Màu Zalo */
  animation: pulse-phone 2s infinite;
}

.floating-facebook {
  background: #1877f2;
  /* Màu Messenger */
}

.floating-btn:hover {
  transform: scale(1.1);
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
}

@keyframes pulse-phone {
  0% {
    box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4);
  }

  70% {
    box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
  }

  100% {
    box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
  }
}

/* Responsive adjustment */
@media (max-width: 768px) {
  .footer-dark {
    text-align: left;
  }

  .social-links {
    margin-bottom: 20px;
  }
}
</style>
