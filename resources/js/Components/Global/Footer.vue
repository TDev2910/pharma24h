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
                  <img src="https://cdn.haitrieu.com/wp-content/uploads/2022/10/Logo-MoMo-Transparent.png" alt="MoMo"
                    title="MoMo">
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

  <!-- Speed Dial FAB (Floating Action Button) -->
  <div class="fab-wrapper">
    <!-- Options bung lên trên -->
    <TransitionGroup name="speed-dial">
      <div v-if="isFabOpen" class="fab-options" key="options">
        <!-- Zalo -->
        <a href="https://zalo.me/0376193244" target="_blank" class="fab-item zalo" title="Zalo">
          <span class="tooltip">Zalo</span>
          <img src="https://upload.wikimedia.org/wikipedia/commons/9/91/Icon_of_Zalo.svg" alt="Zalo" />
        </a>

        <!-- Facebook Messenger -->
        <a href="https://www.facebook.com/dat.hocongthien" target="_blank" class="fab-item messenger" title="Messenger">
          <span class="tooltip">Messenger</span>
          <i class="fab fa-facebook-messenger"></i>
        </a>

        <!-- Nhân viên tư vấn (Chat nội bộ) -->
        <button @click="openHumanChat" class="fab-item human-chat" title="Chat với nhân viên">
          <span class="tooltip">Nhân viên</span>
          <i class="fas fa-headset"></i>
        </button>
      </div>
    </TransitionGroup>

    <!-- Hàng dưới: Robot AI + Nút FAB chính nằm cạnh nhau -->
    <div class="fab-bottom-row">
      <!-- AI Chatbot Button -->
      <ChatbotFloatingButton />
      <!-- Nút chính FAB -->
      <button @click="isFabOpen = !isFabOpen" :class="['fab-main', { 'is-active': isFabOpen }]">
        <i :class="isFabOpen ? 'fas fa-times' : 'fas fa-comment-dots'"></i>
        <span v-if="!isFabOpen" class="pulse-ring"></span>
      </button>
    </div>
  </div>

  <!-- Các widget ẩn hoặc điều khiển riêng -->
  <VchatWidget :auth="auth" />
</template>

<script setup>
import { computed, ref } from 'vue'
import VchatWidget from '@/Components/Global/Chat/VchatWidget.vue'
import ChatbotFloatingButton from '@/Components/Global/Chat/ChatbotFloatingButton.vue'

const isFabOpen = ref(false)

const openHumanChat = () => {
  window.dispatchEvent(new CustomEvent('open-human-chat'));
  isFabOpen.value = false;
}

const props = defineProps({
  auth: {
    type: Object,
    default: () => ({ user: null })
  }
})

const currentYear = computed(() => new Date().getFullYear())
</script>

<style scoped>
/* 1. Dark Footer Styles */
.footer-dark {
  background-color: #162E44;
  color: #fff;
  font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
  margin-top: 50px;
}

.text-gray-400 {
  color: #cbd5e1;
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
  color: #fff;
  text-decoration: none;
  transition: all 0.3s ease;
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

/* 4. Payment Icons */
.payment-icons {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 8px;
  max-width: 200px;
}

.payment-icon {
  width: 100%;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.2s;
}

.payment-icon img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

/* 5. Speed Dial FAB Styles */
.fab-wrapper {
  position: fixed;
  bottom: 100px;        /* Nhấc lên để tránh nút scroll-to-top */
  right: 10px;
  display: flex;
  flex-direction: column;      /* options ở trên, bottom-row ở dưới */
  align-items: flex-end;       /* canh phải */
  gap: 12px;
  z-index: 9999;
}

/* Hàng dọc: Robot AI ở trên, FAB chính ở dưới */
.fab-bottom-row {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
}

.fab-main {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background-color: #1a56db;
  color: white;
  border: none;
  box-shadow: 0 4px 15px rgba(162, 29, 68, 0.4);
  cursor: pointer;
  font-size: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  position: relative;
  flex-shrink: 0;
}

.fab-main.is-active {
  transform: rotate(90deg);
  background-color: #333;
}

.pulse-ring {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  border: 2px solid #a21d44;
  animation: fab-pulse 2s infinite;
  pointer-events: none;
}

@keyframes fab-pulse {
  0% {
    transform: scale(1);
    opacity: 1;
  }

  100% {
    transform: scale(1.6);
    opacity: 0;
  }
}

.fab-options {
  display: flex;
  flex-direction: column-reverse;
  gap: 12px;
  align-items: center;
}

.fab-item {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: white;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
  border: none;
  position: relative;
  transition: all 0.2s ease;
  cursor: pointer;
}

.fab-item:hover {
  transform: scale(1.1);
}

.fab-item img {
  width: 25px;
  height: 25px;
}

.fab-item i {
  font-size: 20px;
}

.fab-item.zalo {
  color: #0084FF;
}

.fab-item.messenger {
  color: #0084FF;
}

.fab-item.human-chat {
  color: #a21d44;
}

.tooltip {
  position: absolute;
  right: 65px;
  background: rgba(0, 0, 0, 0.8);
  color: white;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 12px;
  opacity: 0;
  visibility: hidden;
  transition: 0.2s;
  pointer-events: none;
  white-space: nowrap;
}

.fab-item:hover .tooltip {
  opacity: 1;
  visibility: visible;
}

.speed-dial-enter-active,
.speed-dial-leave-active {
  transition: all 0.3s ease;
}

.speed-dial-enter-from,
.speed-dial-leave-to {
  opacity: 0;
  transform: translateY(20px) scale(0.5);
}

.footer-bottom {
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  background-color: #0F2233;
}
</style>