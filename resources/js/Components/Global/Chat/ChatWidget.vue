<template>
  <div class="chat-wrapper">
    <!-- Chat Button -->
    <button v-if="!isOpen && props.showTrigger" @click="toggleChat" class="chat-trigger-btn"
      :class="{ 'has-unread': hasUnread }">
      <i class="fas fa-comment-dots"></i>
      <span v-if="hasUnread" class="unread-badge"></span>
      <span class="pulse"></span>
    </button>

    <!-- Chat Window -->
    <Transition name="slide-up">
      <div v-if="isOpen" class="chat-window shadow-premium">
        <!-- Header -->
        <div class="chat-header">
          <div class="header-info">
            <div class="status-indicator"></div>
            <div class="header-text">
              <h3>Hỗ trợ trực tuyến</h3>
              <p>Chúng tôi luôn sẵn sàng giúp đỡ</p>
            </div>
          </div>
          <button @click="toggleChat" class="close-btn">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <!-- Messages Area -->
        <div class="chat-messages" ref="messageContainer">
          <div v-if="messages.length === 0" class="empty-state">
            <div class="welcome-icon">
              <i class="fas fa-headset"></i>
            </div>
            <p>Chào bạn! Hãy để lại tin nhắn, nhân viên của chúng tôi sẽ phản hồi ngay.</p>
          </div>

          <div v-for="(msg, index) in messages" :key="index" class="message-group"
            :class="{ 'message-mine': msg.sender_type === currentSenderType }">
            <div class="message-bubble">
              {{ msg.content }}
            </div>
            <span class="message-time">{{ formatTime(msg.created_at) }}</span>
          </div>
        </div>

        <!-- Input Area -->
        <div class="chat-input-area">
          <div class="input-wrapper">
            <textarea v-model="newMessage" @keydown.enter.exact.prevent="sendMessage" placeholder="Nhập tin nhắn..."
              rows="1" ref="textarea"></textarea>
            <button @click="sendMessage" :disabled="!newMessage.trim() || isSending" class="send-btn">
              <i class="fas fa-paper-plane" v-if="!isSending"></i>
              <div class="spinner" v-else></div>
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick, computed } from 'vue';
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';
import { v4 as uuidv4 } from 'uuid';
import "@/../css/Public/Chat/chat-widget.css";

const props = defineProps({
  showTrigger: {
    type: Boolean,
    default: true
  }
});

const page = usePage();
const isOpen = ref(false);
const isSending = ref(false);
const newMessage = ref('');
const messages = ref([]);
const hasUnread = ref(false);
const messageContainer = ref(null);
const textarea = ref(null);

// Auth status
const user = computed(() => page.props.auth.user);
const currentSenderType = computed(() => (user.value && (user.value.role === 'staff' || user.value.role === 'admin')) ? 'staff' : 'customer');

// Session Identification
const sessionId = ref(localStorage.getItem('chat_session_id') || uuidv4());

const fetchMessages = async () => {
  try {
    const response = await axios.get(`/chat/messages/${sessionId.value}`);
    messages.value = response.data;
    scrollToBottom();
  } catch (error) {
    console.error('Failed to fetch messages:', error);
  }
};

const toggleChat = () => {
  isOpen.value = !isOpen.value;
  if (isOpen.value) {
    hasUnread.value = false;
    scrollToBottom();
  }
};

const formatTime = (dateStr) => {
  const date = dateStr ? new Date(dateStr) : new Date();
  return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

const scrollToBottom = async () => {
  await nextTick();
  if (messageContainer.value) {
    messageContainer.value.scrollTop = messageContainer.value.scrollHeight;
  }
};

const sendMessage = async () => {
  if (!newMessage.value.trim() || isSending.value) return;

  const content = newMessage.value.trim();
  newMessage.value = '';
  isSending.value = true;

  // Optimistic local push
  const tempMsg = {
    content,
    sender_type: currentSenderType.value,
    created_at: new Date().toISOString()
  };
  messages.value.push(tempMsg);
  scrollToBottom();

  try {
    const response = await axios.post('/chat/send', {
      content,
      session_id: sessionId.value
    });
  } catch (error) {
    console.error('Failed to send message:', error);
  } finally {
    isSending.value = false;
  }
};

// Initialize Echo listening
const initEcho = () => {
  if (!window.Echo) {
    console.warn('Echo not found in ChatWidget');
    return;
  }

  window.Echo.channel(`chat.${sessionId.value}`)
    .listen('.message.sent', (e) => {
      console.log('New message received:', e);
      if (e.message.senderType !== currentSenderType.value) {
        messages.value.push({
          content: e.message.content,
          sender_type: e.message.senderType,
          created_at: new Date().toISOString()
        });

        if (!isOpen.value) {
          hasUnread.value = true;
        }
        scrollToBottom();
      }
    });
};

onMounted(() => {
  if (!localStorage.getItem('chat_session_id')) {
    localStorage.setItem('chat_session_id', sessionId.value);
  }

  fetchMessages();
  initEcho();

  // Listen for external trigger
  window.addEventListener('open-human-chat', () => {
    if (!isOpen.value) toggleChat();
  });
});

onUnmounted(() => {
  if (window.Echo) {
    window.Echo.leave(`chat.${sessionId.value}`);
  }
});
</script>
