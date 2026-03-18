<template>
  <div>
    <!-- Floating Button -->
    <button @click="toggleChat" class="v-chatbot-floating-btn">
      <img :src="robotImageUrl" alt="Chatbot" class="v-chatbot-robot-icon" />
    </button>

    <!-- Sidebar Widget -->
    <Transition name="sidebar-v">
      <div v-if="isOpen" class="v-chatbot-sidebar">
        <div class="v-chatbot-sidebar-header">
          <h3>Hỗ Trợ Khách Hàng</h3>
          <div class="v-chatbot-header-btns">
            <!-- <button @click="clearChat" class="v-chatbot-clear-btn"></button> -->
            <button @click="closeChat" class="v-chatbot-close-btn-sidebar">&times;</button>
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
