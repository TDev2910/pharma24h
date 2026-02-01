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
                  <img
                    src="data:image/png;base64,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"
                    alt="MoMo" title="MoMo">
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
  max-width: 200px;
  /* Giới hạn chiều rộng để icon không quá rộng */
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
