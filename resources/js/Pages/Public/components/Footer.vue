<template>
  <footer class="footer-section bg-white border-top" style="margin-top:25px;">
    <!-- Main Footer Content -->
    <div class="container-fluid px-0">
      <div class="row g-0">
        <!-- Contact Information Section - BÊN TRÁI -->
        <div class="col-lg-6">
          <div class="contact-section p-3 p-lg-4">
            <!-- Brand -->
            <div class="footer-brand mb-3">
              <h5 class="d-flex align-items-center mb-3">
                <i class="" style="font-size: 1.5rem;"></i>
                <span class="brand-text">Pharma PCT</span>
              </h5>
            </div>

            <div class="row">
              <!-- Contact Details -->
              <div class="col-md-6">
                <h6 class="contact-title mb-2">Kết nối với chúng tôi</h6>

                <div class="contact-item mb-2">
                  <i class="fas fa-phone text-primary me-2"></i>
                  <a href="tel:0980xxxxxx" class="contact-link">0901645269</a>
                </div>

                <div class="contact-item mb-2">
                  <i class="fab fa-facebook text-primary me-2"></i>
                  <a href="https://www.facebook.com/chokiot" target="_blank" class="contact-link">
                    www.facebook.com/pharmapct
                  </a>
                </div>

                <div class="contact-item">
                  <i class="fas fa-envelope text-primary me-2"></i>
                  <a href="mailto:info@mediaid.vn" class="contact-link">info@pharmapct.vn</a>
                </div>
              </div>

              <!-- Store System -->
              <div class="col-md-6">
                <h6 class="contact-title mb-2">Hệ thống cửa hàng</h6>

                <div class="store-item mb-2">
                  <i class="fas fa-map-marker-alt text-primary me-2"></i>
                  <div class="store-info">
                    <strong>Chi nhánh Vũng Tàu</strong><br>
                    <small class="text-muted">12 Đô Lương, Phường 11, Vũng Tàu, Bà Rịa - Vũng Tàu, Việt Nam</small>
                  </div>
                </div>

                <div class="store-item">
                  <i class="fas fa-clock text-primary me-2"></i>
                  <div class="store-info">
                    <strong>Giờ làm việc</strong><br>
                    <small class="text-muted">Thứ 2 - CN: 7:00 - 22:00</small>
                  </div>
                </div>
              </div>

              <!-- openstreetmap -->
              <div class="col-md-6" style="margin-left: 1000px; margin-top: -200px;">
                <iframe
                  frameborder="0"
                  style="border:0"
                  scrolling="no"
                  marginheight="0"
                  marginwidth="0"
                  src="https://www.openstreetmap.org/export/embed.html?bbox=107.077%2C10.363%2C107.081%2C10.365&layer=mapnik&marker=10.3638%2C107.079">
                </iframe>
                <br/>
                <small>
                  <a href="https://www.openstreetmap.org/#map=19/10.3638/107.079" target="_blank">Xem bản đồ mở rộng</a>
                </small>
              </div>
            </div>          
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Footer -->
    <div class="footer-bottom bg-light py-2">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-6">
            <p class="mb-0 text-muted small">
              © {{ currentYear }} Pharma PCT. Bản quyền thuộc về công ty TNHH Pharma PCT Việt Nam.
            </p>
          </div>
          <div class="col-md-6 text-md-end">
            <div class="footer-links">
              <a href="#privacy" class="text-muted me-3 small">Chính sách bảo mật</a>
              <a href="#terms" class="text-muted me-3 small">Điều khoản sử dụng</a>
              <a href="#sitemap" class="text-muted small">Sơ đồ trang</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>

  <!-- Floating Contact Buttons -->
  <div class="floating-contacts">
    <!-- Zalo Button -->
    <a href="https://zalo.me/0376193244" target="_blank" class="floating-btn floating-zalo">
      <i class="fab fa-viber"></i>
    </a>

    <a href="https://www.facebook.com/dat.hocongthien" target="_blank" class="floating-btn floating-facebook">
      <i class="fab fa-facebook-messenger"></i>
    </a>

    <ChatbotFloatingButton />
    <VchatWidget :auth="auth" />
  </div>


  <!-- vChat Widget -->
</template>

<script setup>
import { computed, ref } from 'vue'
import VchatWidget from '@/Components/VchatWidget.vue'
import ChatbotFloatingButton from '@/Components/ChatbotFloatingButton.vue'

// Props từ Inertia
const props = defineProps({
  auth: {
    type: Object,
    default: () => ({ user: null })
  }
})

// Computed property for current year
const currentYear = computed(() => new Date().getFullYear())

</script>

<style scoped>
/* CSS để định vị vChat widget dưới nút message */
:deep(.vchat-widget),
:deep(#vchat-widget),
:deep([id*="vchat"]) {
  position: fixed;
  bottom: 90px;
  /* Dưới nút message (80px + 10px margin) */
  right: 20px;
  z-index: 999;
  /* Đảm bảo không bị ảnh hưởng bởi scrollbar */
  transform: translateZ(0);
  will-change: transform;
}

/* Điều chỉnh cho mobile */
@media (max-width: 767.98px) {

  :deep(.vchat-widget),
  :deep(#vchat-widget),
  :deep([id*="vchat"]) {
    bottom: 80px;
    right: 15px;
  }
}

/* Điều chỉnh cho desktop có scrollbar */
@media (min-width: 768px) {

  :deep(.vchat-widget),
  :deep(#vchat-widget),
  :deep([id*="vchat"]) {
    /* Tính toán lại vị trí để tránh scrollbar */
    right: calc(20px + env(scrollbar-gutter, 0px));
  }
}

/* Floating Contacts */
.floating-contacts {
  position: fixed;
  right: 20px;
  bottom: 80px;
  z-index: 1000;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  align-items: center;
  /* Căn giữa các nút */
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
  font-size: 1.1rem;
}

.floating-zalo {
  background: #28a745;
  animation: pulse-phone 2s infinite;
}


.floating-btn:hover {
  transform: scale(1.1);
  color: #fff;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
}

@keyframes pulse-phone {

  0%,
  100% {
    box-shadow: 0 3px 15px rgba(40, 167, 69, 0.4);
  }

  50% {
    box-shadow: 0 3px 20px rgba(40, 167, 69, 0.7);
    transform: scale(1.05);
  }
}


/* Responsive Design */
@media (max-width: 991.98px) {
  .floating-contacts {
    right: 15px;
    bottom: 70px;
  }

  .floating-btn {
    width: 45px;
    height: 45px;
    font-size: 1rem;
  }
}

@media (max-width: 767.98px) {
  .floating-contacts {
    right: 10px;
    bottom: 60px;
  }

  .floating-btn {
    width: 40px;
    height: 40px;
    font-size: 0.9rem;
  }
}
</style>