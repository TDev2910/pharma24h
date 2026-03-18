<template>
  <div>
    <!-- Floating Button -->
    <button @click="toggleChat" class="floating-btn floating-chatbot">
      <img :src="robotImageUrl" alt="Chatbot" class="robot-icon" />
    </button>

    <!-- Sidebar Widget -->
    <Transition name="sidebar">
      <div v-if="isOpen" class="chatbot-sidebar">
        <div class="chatbot-sidebar-header">
          <h3>Hỗ Trợ Khách Hàng</h3>
          <div class="header-buttons">
            <!-- <button @click="clearChat" class="clear-btn"></button> -->
            <button @click="closeChat" class="close-btn">&times;</button>
          </div>
        </div>
        <ChatbotPopup />
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import ChatbotPopup from './ChatbotPopup.vue'

// Reactive data
const isOpen = ref(false)
const robotImageUrl = ref('https://static.vecteezy.com/system/resources/thumbnails/053/135/090/small/chatbot-support-service-free-png.png')

// Methods
const toggleChat = () => {
  isOpen.value = !isOpen.value
}

const closeChat = () => {
  isOpen.value = false
}

</script>

<style>
@import "@/../css/Public/Chat/chatbot-floating-button.css";
</style>
