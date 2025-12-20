<template>
  <div class="app-wrapper">
    <Sidebar :auth="auth" :unreadNotificationsCount="unreadNotificationsCount" />

    <div class="main-wrapper">
      <header class="topbar">
        <div class="page-title">
          <h2>{{ pageTitle || 'Dashboard' }}</h2>
          <span class="date">{{ currentDate }}</span>
        </div>
        
        <div class="topbar-actions">
           <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Tìm kiếm thuốc, đơn hàng..." />
          </div>
          <button class="icon-btn"><i class="fas fa-bell"></i></button>
          <button class="icon-btn"><i class="fas fa-question-circle"></i></button>
        </div>
      </header>

      <main class="page-content">
        <slot />
      </main>
    </div>

    <Toast />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import Toast from 'primevue/toast'
import Sidebar from '@/Components/User/Sidebar/Sidebar.vue'

const props = defineProps({
  auth: Object,
  pageTitle: String,
  unreadNotificationsCount: {
    type: Number,
    default: 0
  }
})

const currentDate = computed(() => {
  return new Date().toLocaleDateString('vi-VN', { weekday: 'long', day: '2-digit', month: '2-digit', year: 'numeric' })
})
</script>

<style scoped>
/* --- RESET & LAYOUT --- */
.app-wrapper {
  display: flex;
  min-height: 100vh;
  background-color: #F8FAFC;
  font-family: 'Inter', sans-serif;
}

/* --- MAIN WRAPPER --- */
.main-wrapper {
  margin-left: 260px; /* Width of Sidebar */
  flex: 1;
  display: flex;
  flex-direction: column;
}

/* --- TOPBAR --- */
.topbar {
  height: 70px;
  background: white; /* Or transparent depending on preference */
  padding: 0 32px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid #E2E8F0;
  position: sticky;
  top: 0;
  z-index: 40;
}

.page-title h2 { font-size: 20px; font-weight: 700; margin: 0; color: #1E293B; }
.page-title .date { font-size: 13px; color: #94A3B8; }

.topbar-actions { display: flex; align-items: center; gap: 16px; }
.search-box { background: #F1F5F9; border-radius: 8px; padding: 8px 16px; display: flex; align-items: center; gap: 8px; width: 300px; }
.search-box input { border: none; background: transparent; outline: none; width: 100%; font-size: 14px; color: #334155; }
.search-box i { color: #94A3B8; }
.icon-btn { width: 40px; height: 40px; border-radius: 50%; border: 1px solid #E2E8F0; background: white; color: #64748B; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; }
.icon-btn:hover { background: #F8FAFC; color: #3B82F6; }

/* --- CONTENT --- */
.page-content { padding: 32px; }

/* Responsive */
@media (max-width: 1024px) {
  .main-wrapper { margin-left: 80px; }
  .search-box { display: none; }
}
</style>
