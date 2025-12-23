<template>
  <div class="chatbot-container">
    <div class="chat-messages" ref="messagesContainer">
      <div v-if="messages.length === 0" class="welcome-screen">
        <div class="bot-avatar-large">
          <img src="https://cdn-icons-png.flaticon.com/512/4712/4712027.png" alt="Bot" />
        </div>
        <h3>Xin chào! Tôi có thể giúp gì cho bạn?</h3>
        <p>Bạn cần tìm sản phẩm, dịch vụ nào không?</p>
      </div>

      <div v-for="(message, index) in messages" :key="index" class="message-row" :class="message.type">

        <div v-if="message.type === 'bot'" class="avatar">
          <img src="https://cdn-icons-png.flaticon.com/512/4712/4712027.png" alt="Bot" />
        </div>

        <div class="message-content-wrapper">

          <div v-if="message.type === 'bot' && message.products && message.products.length > 0"
            class="products-carousel">
            <div v-for="product in message.products" :key="product.id" class="product-card">
              <div class="product-image">
                <img :src="product.image" :alt="product.name"
                  @error="$event.target.src = 'https://via.placeholder.com/150?text=No+Image'" />
              </div>
              <div class="product-info">
                <div class="product-name" :title="product.name">{{ product.name }}</div>
                <div class="product-price">{{ product.price }}</div>
                <button class="view-btn">Chi tiết</button>
              </div>
            </div>
          </div>

          <div v-if="message.content || isLoading" class="message-bubble"
            :class="{ 'user-bubble': message.type === 'user', 'bot-bubble': message.type === 'bot', 'loading-bubble': !message.content && isLoading && message.type === 'bot' }">
            <span v-if="message.content" v-html="formatMessage(message.content)"></span>

            <div v-else-if="isLoading && message.type === 'bot'" class="typing-dots">
              <span></span><span></span><span></span>
            </div>
          </div>

          <div class="message-time">{{ message.time }}</div>
        </div>

      </div>

      <div v-if="isLoading" class="message-row bot">
        <div class="avatar">
          <img src="https://cdn-icons-png.flaticon.com/512/4712/4712027.png" alt="Bot" />
        </div>
        <div class="message-content-wrapper">
          <div class="message-bubble loading-bubble">
            <div class="typing-dots">
              <span></span><span></span><span></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="chat-input-area">
      <form @submit.prevent="sendMessage">
        <div class="input-container">
          <textarea v-model="currentMessage" @keydown.enter.prevent="sendMessage" @keydown.ctrl.enter="addNewLine"
            placeholder="Nhập tin nhắn..." :disabled="isLoading" rows="1" ref="messageInput"
            class="chat-textarea"></textarea>

          <div class="input-actions">
            <button type="button" class="action-btn emoji-btn" title="Chèn emoji">
              <i class="far fa-smile"></i>
            </button>

            <button type="submit" :disabled="!currentMessage.trim() || isLoading" class="action-btn send-btn">
              <i class="fas fa-paper-plane"></i>
            </button>
          </div>
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

    // Tạo tin nhắn cho bot rỗng
    const botMessageIndex = messages.value.push({
      type: 'bot',
      content: '',
      products: [],
      time: new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' })
    }) - 1

    let currentEvent = null // biến lưu loại event đang đọc hiện tại

    let botResponse = ''
    let isFirstChunk = true

    // 3. Vòng lặp đọc Stream
    while (true) {
      const { done, value } = await reader.read()
      if (done) break

      const chunk = decoder.decode(value)
      // Tách dòng cẩn thận hơn để tránh lỗi dòng trống
      const lines = chunk.split('\n').filter(line => line.trim() !== '')

      for (const line of lines) {
        if (line.startsWith('event: ')) {
          currentEvent = line.slice(7).trim()
        }
        else if (line.startsWith('data: ')) {
          const data = line.slice(6)

          if (currentEvent === 'images') {
            try {
              // Parse mảng sản phẩm
              const products = JSON.parse(data)
              // Gán vào tin nhắn bot hiện tại
              messages.value[botMessageIndex].products = products
              nextTick(() => scrollToBottom())
            } catch (e) {
              console.error('Lỗi parse ảnh:', e)
            }
          }
          else if (currentEvent === 'update') {
            // Nối text
            messages.value[botMessageIndex].content += data
            nextTick(() => scrollToBottom())
          }
        }
      }
    }

    isLoading.value = false
  } catch (error) {
    console.error('Error:', error)
    isLoading.value = false
    messages.value.push({
      type: 'bot',
      content: 'Có lỗi kết nối, vui lòng thử lại.'
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
/* --- Container & Header --- */
.chatbot-container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  /* Điều chỉnh nếu cần fixed height */
  width: 100%;
  background: white;
  overflow: hidden;
  border-radius: 10px;
  /* Bo góc container */
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
}

.chat-header {
  background: #1a73e8;
  /* Màu xanh đậm */
  color: white;
  padding: 15px;
  font-size: 16px;
  font-weight: 600;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-top-left-radius: 10px;
  border-top-right-radius: 10px;
}

.close-btn {
  background: none;
  border: none;
  color: white;
  font-size: 24px;
  cursor: pointer;
  padding: 0 5px;
  opacity: 0.8;
  transition: opacity 0.2s;
}

.close-btn:hover {
  opacity: 1;
}

/* --- Khu vực tin nhắn --- */
.chat-messages {
  flex: 1;
  padding: 20px;
  overflow-y: auto;
  background-color: #f8f9fa;
  /* Nền xám nhạt */
  display: flex;
  flex-direction: column;
  gap: 15px;
}

/* --- Welcome Screen: Giống ảnh --- */
.welcome-screen {
  text-align: center;
  margin-top: 30px;
  padding: 20px;
  flex: 1;
}

.bot-avatar-large img {
  width: 80px;
  height: 80px;
  margin-bottom: 20px;
}

.welcome-screen h3 {
  font-size: 20px;
  font-weight: 600;
  margin-bottom: 10px;
  color: #333;
}

.welcome-screen p {
  font-size: 15px;
  color: #666;
}

/* --- Message Row (Chứa avatar + bubble) --- */
.message-row {
  display: flex;
  align-items: flex-start;
  max-width: 85%;
}

.message-row.user {
  align-self: flex-end;
  flex-direction: row-reverse;
  max-width: 100%;
  /* User bubble có thể full width */
}

.message-row.bot {
  align-self: flex-start;
}

/* --- Avatar --- */
.avatar {
  width: 36px;
  height: 36px;
  margin-right: 10px;
  flex-shrink: 0;
}

.message-row.user .avatar {
  margin-left: 10px;
  margin-right: 0;
}

.avatar img {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
}

/* --- Content Wrapper (Bubble + Time) --- */
.message-content-wrapper {
  display: flex;
  flex-direction: column;
  max-width: 100%;
}

.message-row.user .message-content-wrapper {
  align-items: flex-end;
}

/* --- Bubble Chat Styles --- */
.message-bubble {
  padding: 12px 16px;
  border-radius: 20px;
  font-size: 15px;
  line-height: 1.5;
  word-wrap: break-word;
}

/* Bot Style */
.bot-bubble {
  background-color: white;
  color: #1c1e21;
  border: 1px solid #e9ecef;
  border-top-left-radius: 4px;
  /* Góc nhọn phía avatar */
}

/* User Style */
.user-bubble {
  background: #0084ff;
  /* Màu xanh Messenger */
  color: white;
  border-bottom-right-radius: 4px;
}

/* --- Time Stamp --- */
.message-time {
  font-size: 11px;
  color: #999;
  margin-top: 4px;
  padding: 0 5px;
}

.message-row.user .message-time {
  text-align: right;
}

/* --- Typing Animation (Tối giản) --- */
.typing-dots {
  display: flex;
  gap: 4px;
  padding: 4px 0;
}

.typing-dots span {
  width: 8px;
  height: 8px;
  background: #ccc;
  border-radius: 50%;
  animation: typing 1.4s infinite ease-in-out both;
}

@keyframes typing {

  0%,
  80%,
  100% {
    transform: scale(0);
  }

  40% {
    transform: scale(1);
  }
}

/* --- Input Area (Giống ảnh input) --- */
.chat-input-area {
  padding: 10px 15px;
  background: white;
  border-top: 1px solid #e9ecef;
}

.input-container {
  display: flex;
  align-items: flex-end;
  background: white;
  border: 1px solid #dcdcdc;
  /* Viền mỏng */
  border-radius: 24px;
  padding: 6px 12px;
  transition: box-shadow 0.2s, border-color 0.2s;
}

.input-container:focus-within {
  border-color: #0084ff;
}

.chat-textarea {
  flex: 1;
  border: none;
  background: transparent;
  resize: none;
  padding: 8px 5px;
  font-family: inherit;
  font-size: 15px;
  outline: none;
  min-height: 24px;
  max-height: 120px;
  color: #333;
}

/* --- Product Carousel (Thanh trượt ngang) --- */
.products-carousel {
  display: flex;
  gap: 12px;
  overflow-x: auto;
  padding: 5px 2px 15px 2px;
  /* Padding bottom để tránh che bóng đổ */
  margin-bottom: 8px;
  max-width: 100%;
  scrollbar-width: thin;
  /* Firefox */
}

/* Thanh cuộn cho Chrome/Safari */
.products-carousel::-webkit-scrollbar {
  height: 6px;
}

.products-carousel::-webkit-scrollbar-thumb {
  background: #ccc;
  border-radius: 3px;
}

/* --- Product Card --- */
.product-card {
  min-width: 160px;
  max-width: 160px;
  background: white;
  border: 1px solid #eee;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  display: flex;
  flex-direction: column;
  transition: transform 0.2s;
}

.product-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
}

.product-image {
  height: 120px;
  width: 100%;
  background: #f9f9f9;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  /* Hoặc 'contain' nếu muốn thấy hết ảnh */
}

.product-info {
  padding: 10px;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.product-name {
  font-size: 13px;
  font-weight: 600;
  color: #333;
  margin-bottom: 5px;
  line-height: 1.3;
  /* Cắt dòng nếu tên quá dài */
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  height: 34px;
}

.product-price {
  font-size: 14px;
  color: #d32f2f;
  /* Màu đỏ nổi bật */
  font-weight: bold;
  margin-bottom: 8px;
}

.view-btn {
  margin-top: auto;
  background: #e3f2fd;
  color: #1976d2;
  border: none;
  border-radius: 6px;
  padding: 6px;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  width: 100%;
  transition: background 0.2s;
}

.view-btn:hover {
  background: #bbdefb;
}

/* --- Action Buttons --- */
.input-actions {
  display: flex;
  gap: 4px;
  margin-bottom: 2px;
  /* Căn chỉnh với dòng cuối của text */
}

.action-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 8px;
  border-radius: 50%;
  transition: background 0.2s;
  color: #65676b;
  /* Màu xám cho icon */
  font-size: 18px;
}

.action-btn:hover {
  background-color: #f2f2f2;
}

.send-btn {
  color: #0084ff;
  /* Màu xanh cho nút gửi */
}

.send-btn:disabled {
  color: #bcc0c4;
  cursor: not-allowed;
}

/* Scrollbar */
.chat-messages::-webkit-scrollbar {
  width: 6px;
}

.chat-messages::-webkit-scrollbar-thumb {
  background-color: rgba(0, 0, 0, 0.1);
  border-radius: 3px;
}
</style>