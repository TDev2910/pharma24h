<template>
  <div class="chatbot-container">
    <!-- Chat Messages -->
    <div class="chat-messages" ref="messagesContainer">
      <div v-if="messages.length === 0" class="welcome-message">
        <p>👋 Xin chào! Tôi là trợ lý AI của nhà thuốc Pharma PCT.</p>
        <p>Hãy hỏi tôi về các thông tin sau:</p>
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
          <span class="typing-indicator">🤖 Đang trả lời...</span>
        </div>
      </div>
    </div>

    <!-- Chat Input -->
    <div class="chat-input">
      <form @submit.prevent="sendMessage">
        <div class="input-group">
          <textarea v-model="currentMessage" @keydown.enter.prevent="sendMessage" @keydown.ctrl.enter="addNewLine"
            placeholder="Nhập tin nhắn của bạn..." :disabled="isLoading" rows="1" ref="messageInput"></textarea>
          <button type="submit" :disabled="!currentMessage.trim() || isLoading" class="send-btn">
            <i class="fas fa-paper-plane"></i>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick, watch, onUnmounted } from 'vue'

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

    // Create new EventSource connection with POST data
    const formData = new FormData()
    formData.append('message', userMessage)
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'))

    // Use fetch with POST method
    const response = await fetch('/api/chatbot/chat', {
      method: 'POST',
      body: formData
    })

    if (!response.ok) {
      throw new Error('Network response was not ok')
    }

    // Read the stream
    const reader = response.body.getReader()
    const decoder = new TextDecoder()

    let botResponse = ''
    let isFirstChunk = true

    while (true) {
      const { done, value } = await reader.read()
      if (done) break

      const chunk = decoder.decode(value)
      const lines = chunk.split('\n')

      for (const line of lines) {
        if (line.startsWith('data: ')) {
          const data = line.slice(6)

          if (data === '</stream>') {
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
          botResponse += data

          // Update the last message (bot message)
          const lastMessage = messages.value[messages.value.length - 1]
          if (lastMessage && lastMessage.type === 'bot') {
            lastMessage.content = botResponse
          }

          scrollToBottom()
        }
      }
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

  // Listen for clear chat event
  window.addEventListener('clear-chat', clearChat)
})

// Cleanup on unmount
onUnmounted(() => {
  if (eventSource) {
    eventSource.close()
  }

  // Remove event listener
  window.removeEventListener('clear-chat', clearChat)
})
</script>

<style scoped>
.chatbot-container {
  display: flex;
  flex-direction: column;
  height: 100%;
  width: 100%;
  background: white;
  overflow: hidden;
  flex: 1;
}

.chat-messages {
  flex: 1;
  padding: 15px;
  overflow-y: auto;
  background: #f8f9fa;
  min-height: 0;
}

.welcome-message {
  text-align: center;
  color: #666;
  padding: 15px;
  font-size: 14px;
}

.welcome-message ul {
  text-align: left;
  display: inline-block;
  margin-top: 10px;
  font-size: 13px;
}

.message {
  margin-bottom: 10px;
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
  max-width: 80%;
  padding: 10px 14px;
  border-radius: 15px;
  word-wrap: break-word;
  line-height: 1.3;
  font-size: 14px;
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

  0%,
  100% {
    opacity: 1;
  }

  50% {
    opacity: 0.5;
  }
}

.chat-input {
  padding: 15px;
  background: white;
  border-top: 1px solid #e9ecef;
  flex-shrink: 0;
}

.input-group {
  display: flex;
  gap: 8px;
  align-items: flex-end;
}

.input-group textarea {
  flex: 1;
  border: 2px solid #e9ecef;
  border-radius: 20px;
  padding: 10px 14px;
  font-size: 13px;
  font-family: inherit;
  resize: none;
  outline: none;
  transition: border-color 0.3s;
  min-height: 40px;
  max-height: 100px;
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
  padding: 10px 14px;
  border-radius: 50%;
  cursor: pointer;
  font-size: 14px;
  transition: transform 0.3s;
  min-width: 40px;
  height: 40px;
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
