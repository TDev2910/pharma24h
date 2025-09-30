<template>
    <footer class="footer-section bg-white border-top">
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
                    <a href="tel:0980xxxxxx" class="contact-link">0980xxxxxx</a>
                  </div>
  
                  <div class="contact-item mb-2">
                    <i class="fab fa-facebook text-primary me-2"></i>
                    <a href="https://www.facebook.com/chokiot" target="_blank" class="contact-link">
                      www.facebook.com/chokiot
                    </a>
                  </div>
  
                  <div class="contact-item mb-2">
                    <i class="fab fa-viber text-primary me-2"></i>
                    <a href="viber://chat?number=0980xxxxxx" class="contact-link">0901645269</a>
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
  
      <!-- Facebook Button -->
      <a href="https://www.facebook.com/dat.hocongthien" target="_blank" class="floating-btn floating-facebook">
        <i class="fab fa-facebook-messenger"></i>
      </a>
    </div> 
  </template>
  
  <script setup>
  import { computed } from 'vue'
  
  // Computed property for current year
  const currentYear = computed(() => new Date().getFullYear())
  </script>