<template>
  <div>
    <!-- Header Component -->
    <Header :auth="auth" />
    
    <!-- Main Content -->
    <div class="container py-5">
      <div class="row">
        <div class="col-12">
          <h1 class="display-4 text-center mb-5">Cơ sở khám bệnh</h1>
          <p class="lead text-center text-muted mb-5">
            Tìm kiếm và kết nối với các cơ sở y tế uy tín
          </p>
        </div>
      </div>
    </div>
    
    <!-- Footer Component -->
   <Footer />
  </div>
</template>

<script setup>
import Header from './components/Header.vue'
import Footer from './components/Footer.vue'

const props = defineProps({
  auth: { type: Object, default: () => ({ user: null }) }
})
</script>
