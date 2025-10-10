<template>
  <div class="public-layout">
    <!-- Header Component -->
    <Header :auth="auth" />
    
    <!-- Main Content -->
    <main class="main-content">
      <slot />
    </main>
    
    <!-- Footer Component -->
    <Footer />
    
    
    <!-- Back to Top Button -->
    <button class="btn-back-top" id="backToTop" title="Lên đầu trang">
      <i class="fas fa-chevron-up"></i>
    </button>
  </div>
</template>

<script setup>
import Header from '@/Pages/Public/components/Header.vue'
import Footer from '@/Pages/Public/components/Footer.vue'
import { onMounted, onUnmounted } from 'vue'

// Props từ Inertia
const props = defineProps({
  auth: {
    type: Object,
    default: () => ({ user: null })
  }
})

// Back to Top functionality
let backToTopButton = null

function handleScroll() {
  if (backToTopButton) {
    if (window.scrollY > 300) {
      backToTopButton.classList.add('show')
    } else {
      backToTopButton.classList.remove('show')
    }
  }
}

function scrollToTop() {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  })
}

onMounted(() => {
  backToTopButton = document.getElementById('backToTop')
  
  if (backToTopButton) {
    backToTopButton.addEventListener('click', scrollToTop)
  }
  
  window.addEventListener('scroll', handleScroll)
  
  // Initialize cart functionality
  if (typeof window.initCart === 'function') {
    window.initCart()
  }
})

onUnmounted(() => {
  if (backToTopButton) {
    backToTopButton.removeEventListener('click', scrollToTop)
  }
  
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
/* Public Layout Styles */
.public-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.main-content {
  flex: 1;
  padding-top: 0; /* Header đã có position fixed */
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

.floating-phone {
  background: #28a745;
  animation: pulse-phone 2s infinite;
}

.floating-zalo {
  background: #0180c7;
}

.floating-facebook {
  background: #1877f2;
}

.floating-btn:hover {
  transform: scale(1.1);
  color: #fff;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
}

@keyframes pulse-phone {
  0%, 100% {
    box-shadow: 0 3px 15px rgba(40, 167, 69, 0.4);
  }
  50% {
    box-shadow: 0 3px 20px rgba(40, 167, 69, 0.7);
    transform: scale(1.05);
  }
}

/* Back to Top Button */
.btn-back-top {
  position: fixed;
  bottom: 20px;
  right: 20px;
  width: 45px;
  height: 45px;
  background: #1976d2;
  color: #fff;
  border: none;
  border-radius: 50%;
  font-size: 1rem;
  opacity: 0;
  visibility: hidden;
  transition: all 0.3s ease;
  z-index: 999;
  cursor: pointer;
}

.btn-back-top.show {
  opacity: 1;
  visibility: visible;
}

.btn-back-top:hover {
  background: #1565c0;
  transform: translateY(-2px);
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
  
  .btn-back-top {
    width: 40px;
    height: 40px;
    font-size: 0.9rem;
  }
}
</style>
