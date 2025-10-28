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
          <h3>Chatbot Pharma PCT</h3>
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
const robotImageUrl = ref('/storage/chatbot/robot_18531732.png')

// Methods
const toggleChat = () => {
  isOpen.value = !isOpen.value
}

const closeChat = () => {
  isOpen.value = false
}

// const clearChat = () => {
//   // Emit event để ChatbotPopup clear messages
//   const event = new CustomEvent('clear-chat')
//   window.dispatchEvent(event)
// }
</script>

<style scoped>
/* Floating Button */
.floating-btn {
  position: fixed;
  bottom: 20px;
  right: 20px;
  width: auto;
  height: auto;
  border-radius: 50%;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  background: transparent; /* Không có background */
  font-size: 20px;
  color: white;
  box-shadow: none;
  transition: all 0.3s ease;
  z-index: 1000;
}

/* .floating-chatbot {
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
} */

.floating-btn:hover {
  transform: scale(1.1);
  box-shadow: none;
}

.robot-icon {
  margin-top: -15px;
  width: 50px;
  height: 50px;
  object-fit: contain;
}

/* Sidebar Widget */
.chatbot-sidebar {
  position: fixed;
  right: 20px;
  bottom: 20px;
  width: 320px;
  height: 500px;
  background: white;
  border-radius: 15px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
  z-index: 1500;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

/* Sidebar Header */
.chatbot-sidebar-header {
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  color: white;
  padding: 15px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-shrink: 0;
}

.chatbot-sidebar-header h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
}

.header-buttons {
  display: flex;
  gap: 8px;
  align-items: center;
}

.clear-btn {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  width: 35px;
  height: 35px;
  border-radius: 50%;
  cursor: pointer;
  font-size: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.3s;
}

.clear-btn:hover {
  background: rgba(255, 255, 255, 0.3);
}

.close-btn {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  width: 35px;
  height: 35px;
  border-radius: 50%;
  cursor: pointer;
  font-size: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.3s;
}

.close-btn:hover {
  background: rgba(255, 255, 255, 0.3);
}

/* Sidebar Transitions */
.sidebar-enter-active,
.sidebar-leave-active {
  transition: all 0.3s ease;
}

.sidebar-enter-from,
.sidebar-leave-to {
  transform: translateY(100%) scale(0.8);
  opacity: 0;
}

.sidebar-enter-to,
.sidebar-leave-from {
  transform: translateY(0) scale(1);
  opacity: 1;
}

/* Responsive */
@media (max-width: 768px) {
  .chatbot-sidebar {
    width: calc(100% - 40px);
    height: 70vh;
    right: 20px;
    bottom: 20px;
  }
  
  .floating-btn {
    bottom: 15px;
    right: 15px;
    width: 45px;
    height: 45px;
    font-size: 18px;
  }
}
</style>
