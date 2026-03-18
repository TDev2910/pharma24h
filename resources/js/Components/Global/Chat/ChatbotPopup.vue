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
          <textarea v-model="currentMessage" @keydown.enter.exact.prevent="sendMessage" @keydown.ctrl.enter="addNewLine"
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

<style>
@import "@/../css/Public/Chat/chatbot-popup.css";
</style>
