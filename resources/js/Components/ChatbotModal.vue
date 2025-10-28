<template>
  <div class="chatbot-container">
    <!-- Chat Header -->
    <div class="chat-header">
      <h3>🤖 Chatbot Pharma PCT</h3>
      <button @click="clearChat" class="clear-btn">🗑️ Xóa</button>
    </div>

    <!-- Chat Messages -->
    <div class="chat-messages" ref="messagesContainer">
      <div v-if="messages.length === 0" class="welcome-message">
        <p>👋 Xin chào! Tôi là trợ lý AI của nhà thuốc Pharma PCT.</p>
        <p>Hãy hỏi tôi về:</p>
        <ul>
          <li>💊 Thông tin thuốc</li>
          <li>🏥 Dịch vụ khám bệnh</li>
          <li>📍 Địa chỉ và giờ làm việc</li>
          <li>💰 Giá cả và thanh toán</li>
        </ul>
      </div>
      
      <div v-for="(message, index) in messages" :key="index" class="message" :class="message.type">
        <div class="message-content">
          <span v-html="formatMessage(message.content)"></span>
        </div>
        <div class="message-time">{{ message.time }}</div>
      </div>
      
      <!-- Loading indicator -->
      <div v-if="isLoading" class="message bot">
        <div class="message-content">
          <span class="typing-indicator">🤖 Đang gõ...</span>
        </div>
      </div>
    </div>

    <!-- Chat Input -->
    <div class="chat-input">
      <form @submit.prevent="sendMessage">
        <div class="input-group">
          <textarea 
            v-model="currentMessage" 
            @keydown.enter.prevent="sendMessage"
            @keydown.ctrl.enter="addNewLine"
            placeholder="Nhập tin nhắn của bạn..."
            :disabled="isLoading"
            rows="1"
            ref="messageInput"
          ></textarea>
          <button type="submit" :disabled="!currentMessage.trim() || isLoading" class="send-btn">
            <i class="fas fa-paper-plane"></i>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick, watch } from 'vue'

// Reactive data
const messages = ref([])
const currentMessage = ref('')
const isLoading = ref(false)
const messagesContainer = ref(null)
const messageInput = ref(null)
let eventSource = null

// Auto-resize textarea
const autoResize = () => {
  if (messageInput.value) {
    messageInput.value.style.height = 'auto'
    messageInput.value.style.height = messageInput.value.scrollHeight + 'px'
  }
}

// Watch for textarea changes
watch(currentMessage, () => {
  nextTick(() => {
    autoResize()
  })
})

// Format message content
const formatMessage = (content) => {
  return content
    .replace(/\n/g, '<br>')
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/\*(.*?)\*/g, '<em>$1</em>')
}

// Scroll to bottom
const scrollToBottom = () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
  })
}

// Add new line in textarea
const addNewLine = () => {
  currentMessage.value += '\n'
}

// Send message
const sendMessage = async () => {
  if (!currentMessage.value.trim() || isLoading.value) return

  const userMessage = currentMessage.value.trim()
  currentMessage.value = ''
  
  // Add user message
  messages.value.push({
    type: 'user',
    content: userMessage,
    time: new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' })
  })
  
  scrollToBottom()
  isLoading.value = true

  try {
    // Close existing connection
    if (eventSource) {
      eventSource.close()
    }

    // Create new EventSource connection
    eventSource = new EventSource(`/api/chatbot/chat?message=${encodeURIComponent(userMessage)}`)
    
    let botResponse = ''
    let isFirstChunk = true

    eventSource.addEventListener('update', (event) => {
      if (event.data === '</stream>') {
        eventSource.close()
        isLoading.value = false
        return
      }

      if (isFirstChunk) {
        // Add bot message container
        messages.value.push({
          type: 'bot',
          content: '',
          time: new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' })
        })
        isFirstChunk = false
      }

      // Append to bot response
      botResponse += event.data
      
      // Update the last message (bot message)
      const lastMessage = messages.value[messages.value.length - 1]
      if (lastMessage && lastMessage.type === 'bot') {
        lastMessage.content = botResponse
      }
      
      scrollToBottom()
    })

    eventSource.onerror = (error) => {
      console.error('EventSource failed:', error)
      isLoading.value = false
      eventSource.close()
      
      // Add error message
      messages.value.push({
        type: 'bot',
        content: '❌ Lỗi kết nối. Vui lòng thử lại sau.',
        time: new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' })
      })
    }

  } catch (error) {
    console.error('Error sending message:', error)
    isLoading.value = false
    
    messages.value.push({
      type: 'bot',
      content: '❌ Có lỗi xảy ra. Vui lòng thử lại.',
      time: new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' })
    })
  }
}

// Clear chat
const clearChat = () => {
  messages.value = []
  if (eventSource) {
    eventSource.close()
  }
  isLoading.value = false
}

// Focus input on mount
onMounted(() => {
  if (messageInput.value) {
    messageInput.value.focus()
  }
})

// Cleanup on unmount
onUnmounted(() => {
  if (eventSource) {
    eventSource.close()
  }
})
</script>

<style scoped>
.chatbot-container {
  display: flex;
  flex-direction: column;
  height: 600px;
  max-width: 800px;
  margin: 0 auto;
  background: white;
  border-radius: 15px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.chat-header {
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  color: white;
  padding: 15px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.chat-header h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
}

.clear-btn {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  padding: 8px 12px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
  transition: background 0.3s;
}

.clear-btn:hover {
  background: rgba(255, 255, 255, 0.3);
}

.chat-messages {
  flex: 1;
  padding: 20px;
  overflow-y: auto;
  background: #f8f9fa;
}

.welcome-message {
  text-align: center;
  color: #666;
  padding: 20px;
}

.welcome-message ul {
  text-align: left;
  display: inline-block;
  margin-top: 15px;
}

.message {
  margin-bottom: 15px;
  display: flex;
  flex-direction: column;
}

.message.user {
  align-items: flex-end;
}

.message.bot {
  align-items: flex-start;
}

.message-content {
  max-width: 70%;
  padding: 12px 16px;
  border-radius: 18px;
  word-wrap: break-word;
  line-height: 1.4;
}

.message.user .message-content {
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  color: white;
  border-bottom-right-radius: 5px;
}

.message.bot .message-content {
  background: white;
  color: #333;
  border: 1px solid #e9ecef;
  border-bottom-left-radius: 5px;
}

.message-time {
  font-size: 11px;
  color: #999;
  margin-top: 5px;
  padding: 0 5px;
}

.typing-indicator {
  animation: pulse 1.5s infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

.chat-input {
  padding: 20px;
  background: white;
  border-top: 1px solid #e9ecef;
}

.input-group {
  display: flex;
  gap: 10px;
  align-items: flex-end;
}

.input-group textarea {
  flex: 1;
  border: 2px solid #e9ecef;
  border-radius: 20px;
  padding: 12px 16px;
  font-size: 14px;
  font-family: inherit;
  resize: none;
  outline: none;
  transition: border-color 0.3s;
  min-height: 44px;
  max-height: 120px;
}

.input-group textarea:focus {
  border-color: #4facfe;
}

.input-group textarea:disabled {
  background: #f8f9fa;
  cursor: not-allowed;
}

.send-btn {
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  border: none;
  color: white;
  padding: 12px 16px;
  border-radius: 50%;
  cursor: pointer;
  font-size: 16px;
  transition: transform 0.3s;
  min-width: 44px;
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.send-btn:hover:not(:disabled) {
  transform: scale(1.05);
}

.send-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none;
}

/* Scrollbar styling */
.chat-messages::-webkit-scrollbar {
  width: 6px;
}

.chat-messages::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.chat-messages::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.chat-messages::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

/* Responsive */
@media (max-width: 768px) {
  .chatbot-container {
    height: 100vh;
    border-radius: 0;
  }
  
  .message-content {
    max-width: 85%;
  }
}
</style>
