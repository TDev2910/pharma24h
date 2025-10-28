<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chatbot - Nhà thuốc Pharma PCT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s ease;
            z-index: 1000;
        }
        
        .back-button:hover {
            background: rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body>
    <a href="/" class="back-button">← Quay lại</a>
    
    <div id="app">
        <chatbot-modal></chatbot-modal>
    </div>

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script>
        const { createApp } = Vue;
        
        // Import ChatbotModal component
        const ChatbotModal = {
            template: `
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
                            <div class="message-time" v-text="message.time"></div>
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
            `,
            data() {
                return {
                    messages: [],
                    currentMessage: '',
                    isLoading: false,
                    eventSource: null
                }
            },
            methods: {
                formatMessage(content) {
                    return content
                        .replace(/\n/g, '<br>')
                        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                        .replace(/\*(.*?)\*/g, '<em>$1</em>')
                },
                
                scrollToBottom() {
                    this.$nextTick(() => {
                        const container = this.$refs.messagesContainer
                        if (container) {
                            container.scrollTop = container.scrollHeight
                        }
                    })
                },
                
                addNewLine() {
                    this.currentMessage += '\\n'
                },
                
                async sendMessage() {
                    if (!this.currentMessage.trim() || this.isLoading) return

                    const userMessage = this.currentMessage.trim()
                    this.currentMessage = ''
                    
                    // Add user message
                    this.messages.push({
                        type: 'user',
                        content: userMessage,
                        time: new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' })
                    })
                    
                    this.scrollToBottom()
                    this.isLoading = true

                    try {
                        // Close existing connection
                        if (this.eventSource) {
                            this.eventSource.close()
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
                                        this.isLoading = false
                                        return
                                    }
                                    
                                    if (isFirstChunk) {
                                        // Add bot message container
                                        this.messages.push({
                                            type: 'bot',
                                            content: '',
                                            time: new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' })
                                        })
                                        isFirstChunk = false
                                    }
                                    
                                    // Append to bot response
                                    botResponse += data
                                    
                                    // Update the last message (bot message)
                                    const lastMessage = this.messages[this.messages.length - 1]
                                    if (lastMessage && lastMessage.type === 'bot') {
                                        lastMessage.content = botResponse
                                    }
                                    
                                    this.scrollToBottom()
                                }
                            }
                        }

                    } catch (error) {
                        console.error('Error sending message:', error)
                        this.isLoading = false
                        
                        this.messages.push({
                            type: 'bot',
                            content: '❌ Có lỗi xảy ra. Vui lòng thử lại.',
                            time: new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' })
                        })
                    }
                },
                
                clearChat() {
                    this.messages = []
                    if (this.eventSource) {
                        this.eventSource.close()
                    }
                    this.isLoading = false
                }
            },
            
            mounted() {
                if (this.$refs.messageInput) {
                    this.$refs.messageInput.focus()
                }
            },
            
            beforeUnmount() {
                if (this.eventSource) {
                    this.eventSource.close()
                }
            }
        }
        
        createApp({
            components: {
                ChatbotModal
            }
        }).mount('#app')
    </script>
</body>
</html>
