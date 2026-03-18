<template>
  <Head title="Quản lý Chat" />

    <div class="chat-page">
      <div class="chat-wrapper">

        <!-- ===== CỘT TRÁI: Danh sách hội thoại ===== -->
        <div class="sidebar">
          <!-- Header sidebar -->
          <div class="sidebar-header">
            <div class="sidebar-logo">
              <i class="fas fa-comment-dots"></i>
            </div>
            <h2 class="sidebar-title">Quản Lý Chat</h2>
          </div>

          <!-- Tìm kiếm -->
          <div class="sidebar-search">
            <i class="fas fa-search search-icon"></i>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Tìm kiếm hội thoại..."
              class="search-input"
            />
          </div>

          <!-- Tab filter -->
          <div class="sidebar-tabs">
            <button
              @click="activeFilter = 'all'"
              :class="['tab-btn', activeFilter === 'all' ? 'tab-btn--active' : '']"
            >Tất cả</button>
            <button
              @click="activeFilter = 'member'"
              :class="['tab-btn', activeFilter === 'member' ? 'tab-btn--active' : '']"
            >Thành viên</button>
            <button
              @click="activeFilter = 'guest'"
              :class="['tab-btn', activeFilter === 'guest' ? 'tab-btn--active' : '']"
            >Khách</button>
          </div>

          <!-- Danh sách session -->
          <div class="session-list">
            <div
              v-if="filteredSessions.length === 0"
              class="session-empty"
            >
              <i class="fas fa-inbox"></i>
              <p>Chưa có phiên chat nào.</p>
            </div>

            <div
              v-for="session in filteredSessions"
              :key="session.id"
              @click="selectSession(session)"
              :class="['session-item', activeSession?.id === session.id ? 'session-item--active' : '']"
            >
              <!-- Avatar -->
              <div class="session-avatar-wrap">
                <div v-if="session.type === 'guest'" class="session-avatar session-avatar--guest">
                  <i class="fas fa-user"></i>
                </div>
                <img
                  v-else
                  :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(session.customer_name || 'KH')}&background=6366f1&color=fff`"
                  :alt="session.customer_name"
                  class="session-avatar-img"
                />
                <span class="online-dot"></span>
              </div>

              <!-- Info -->
              <div class="session-info">
                <div class="session-meta">
                  <h4 class="session-name">{{ session.customer_name || 'Khách' }}</h4>
                  <span class="session-time">{{ formatTime(session.updated_at) }}</span>
                </div>
                <p class="session-preview">
                  {{ session.messages && session.messages.length > 0 ? session.messages[0].content : 'Bắt đầu hội thoại...' }}
                </p>
                <span :class="['session-badge', session.type === 'guest' ? 'session-badge--guest' : 'session-badge--member']">
                  {{ session.type === 'guest' ? 'Khách vãng lai' : 'Khách có tài khoản' }}
                </span>
              </div>

              <!-- Unread dot -->
              <span v-if="hasUnreadMessage(session.id)" class="unread-dot"></span>
            </div>
          </div>
        </div>

        <!-- ===== CỘT GIỮA: Cửa sổ chat ===== -->
        <div class="chat-main">
          <template v-if="activeSession">
            <!-- Chat Header -->
            <div class="chat-header">
              <div class="chat-header-user">
                <div class="chat-header-avatar-wrap">
                  <img
                    :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(activeSession.customer_name || 'KH')}&background=6366f1&color=fff`"
                    :alt="activeSession.customer_name"
                    class="chat-header-avatar"
                  />
                  <span class="online-dot"></span>
                </div>
                <div>
                  <h3 class="chat-header-name">{{ activeSession.customer_name || 'Khách' }}</h3>
                  <p class="chat-header-status">Đang trực tuyến</p>
                </div>
              </div>
              <div class="chat-header-actions">
                <button class="action-btn"><i class="fas fa-phone-alt"></i></button>
                <button class="action-btn"><i class="fas fa-video"></i></button>
                <button class="action-btn"><i class="fas fa-ellipsis-v"></i></button>
              </div>
            </div>

            <!-- Messages area -->
            <div class="messages-area" ref="messageContainer">
              <!-- Date separator -->
              <div class="date-separator">
                <span class="date-badge">HÔM NAY</span>
              </div>

              <template v-for="(msg, index) in activeMessages" :key="index">
                <!-- Tin nhắn khách hàng (trái) -->
                <div v-if="msg.sender_type !== 'staff'" class="msg-row msg-row--left">
                  <img
                    :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(activeSession.customer_name || 'KH')}&background=6366f1&color=fff`"
                    alt="Avatar"
                    class="msg-avatar"
                  />
                  <div class="msg-content">
                    <div class="bubble bubble--customer">{{ msg.content }}</div>
                    <span class="msg-time">{{ formatTime(msg.created_at) }}</span>
                  </div>
                </div>

                <!-- Tin nhắn nhân viên (phải) -->
                <div v-else class="msg-row msg-row--right">
                  <div class="msg-content msg-content--right">
                    <div class="bubble bubble--staff">{{ msg.content }}</div>
                    <span class="msg-time msg-time--right">{{ formatTime(msg.created_at) }}</span>
                  </div>
                  <div class="msg-avatar-staff">
                    <i class="fas fa-headset"></i>
                  </div>
                </div>
              </template>

              <!-- Typing indicator -->
              <div v-if="isSending" class="typing-indicator">
                <div class="typing-dots">
                  <span></span><span></span><span></span>
                </div>
                Nhân viên đang soạn tin nhắn...
              </div>
            </div>

            <!-- Input area -->
            <div class="input-area">
              <div class="input-box">
                <input
                  v-model="newMessage"
                  @keyup.enter="sendMessage"
                  type="text"
                  placeholder="Nhập tin nhắn..."
                  class="msg-input"
                  :disabled="isSending"
                />
                <div class="input-toolbar">
                  <div class="toolbar-tools">
                    <button class="tool-btn"><i class="far fa-smile"></i></button>
                    <button class="tool-btn"><i class="fas fa-paperclip"></i></button>
                    <button class="tool-btn"><i class="far fa-image"></i></button>
                    <div class="tool-divider"></div>
                    <button class="tool-btn font-bold" style="font-family: serif;">B</button>
                    <button class="tool-btn italic" style="font-family: serif;">I</button>
                  </div>
                  <button
                    @click="sendMessage"
                    class="send-btn"
                    :disabled="!newMessage.trim() || isSending"
                  >
                    Gửi <i class="fas fa-paper-plane"></i>
                  </button>
                </div>
              </div>
            </div>
          </template>

          <!-- Empty state -->
          <div v-else class="chat-empty">
            <div class="chat-empty-icon">
              <i class="fas fa-comments"></i>
            </div>
            <h3>Trung tâm Hỗ trợ Khách hàng</h3>
            <p>Chọn một phiên chat từ danh sách bên trái để bắt đầu tư vấn.</p>
          </div>
        </div>

        <!-- ===== CỘT PHẢI: Thông tin khách hàng ===== -->
        <div class="customer-panel" v-if="activeSession">
          <div class="panel-body">
            <!-- Avatar + tên -->
            <div class="panel-profile">
              <img
                :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(activeSession.customer_name || 'KH')}&background=6366f1&color=fff&size=200`"
                alt="Avatar"
                class="panel-avatar"
              />
              <h3 class="panel-name">{{ activeSession.customer_name || 'Khách' }}</h3>
              <p class="panel-email">{{ activeSession.customer_email || 'N/A' }}</p>
              <span class="panel-badge">THÀNH VIÊN VÀNG</span>
            </div>

            <!-- Thông tin chi tiết -->
            <div class="panel-section">
              <h4 class="panel-section-title">THÔNG TIN CHI TIẾT</h4>
              <div class="panel-info-list">
                <div class="panel-info-row">
                  <span class="panel-info-label">Loại tài khoản</span>
                  <span class="panel-info-value">{{ activeSession.type === 'guest' ? 'Khách vãng lai' : 'Cá nhân' }}</span>
                </div>
                <div class="panel-info-row">
                  <span class="panel-info-label">Ngày tham gia</span>
                  <span class="panel-info-value">{{ formatDate(activeSession.created_at) }}</span>
                </div>
                <div class="panel-info-row">
                  <span class="panel-info-label">Số điện thoại</span>
                  <span class="panel-info-value">{{ activeSession.customer_phone || 'N/A' }}</span>
                </div>
              </div>
            </div>

            <!-- Hoạt động gần đây -->
            <div class="panel-section">
              <h4 class="panel-section-title">HOẠT ĐỘNG GẦN ĐÂY</h4>
              <div class="activity-list">
                <div class="activity-item">
                  <div class="activity-icon">
                    <i class="fas fa-shopping-bag"></i>
                  </div>
                  <div>
                    <p class="activity-title">Đã đặt đơn hàng #12034</p>
                    <p class="activity-time">1 ngày trước</p>
                  </div>
                </div>
                <div class="activity-item">
                  <div class="activity-icon">
                    <i class="fas fa-sign-in-alt"></i>
                  </div>
                  <div>
                    <p class="activity-title">Đăng nhập từ Chrome (MacOS)</p>
                    <p class="activity-time">2 giờ trước</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Ghi chú nội bộ -->
            <div class="panel-section">
              <h4 class="panel-section-title">GHI CHÚ NỘI BỘ</h4>
              <textarea
                v-model="internalNote"
                rows="4"
                placeholder="Thêm ghi chú về khách hàng này..."
                class="note-textarea"
              ></textarea>
            </div>
          </div>

          <!-- Actions footer -->
          <div class="panel-footer">
            <button class="panel-btn panel-btn--tag">Gắn thẻ</button>
            <button class="panel-btn panel-btn--block">Chặn khách</button>
          </div>
        </div>

        <!-- Empty panel khi chưa chọn session -->
        <div class="customer-panel customer-panel--empty" v-else>
          <div class="panel-empty-hint">
            <i class="fas fa-user-circle"></i>
            <p>Chọn hội thoại để xem thông tin khách hàng</p>
          </div>
        </div>

      </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, computed, nextTick, onUnmounted } from 'vue';
import axios from 'axios';

const sessions = ref([]);
const activeSession = ref(null);
const activeMessages = ref([]);
const newMessage = ref('');
const isSending = ref(false);
const messageContainer = ref(null);
const unreadSessions = ref(new Set());
const searchQuery = ref('');
const activeFilter = ref('all');
const internalNote = ref('');

const sortedSessions = computed(() => {
  return [...sessions.value].sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at));
});

const filteredSessions = computed(() => {
  let list = sortedSessions.value;

  if (activeFilter.value === 'member') {
    list = list.filter(s => s.type === 'member');
  } else if (activeFilter.value === 'guest') {
    list = list.filter(s => s.type === 'guest');
  }

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase();
    list = list.filter(s => (s.customer_name || '').toLowerCase().includes(q));
  }

  return list;
});

const formatTime = (dateStr) => {
  const date = dateStr ? new Date(dateStr) : new Date();
  return date.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
};

const formatDate = (dateStr) => {
  if (!dateStr) return 'N/A';
  const date = new Date(dateStr);
  return date.toLocaleDateString('vi-VN');
};

const hasUnreadMessage = (sessionId) => unreadSessions.value.has(sessionId);

const fetchSessions = async () => {
  try {
    const response = await axios.get(route('staff.chat.sessions'));
    sessions.value = response.data;
  } catch (error) {
    console.error('Failed to fetch sessions:', error);
  }
};

const selectSession = async (session) => {
  activeSession.value = session;
  unreadSessions.value.delete(session.id);
  internalNote.value = '';

  try {
    const response = await axios.get(route('staff.chat.messages', { sessionId: session.id }));
    activeMessages.value = response.data;
    scrollToBottom();
  } catch (error) {
    console.error('Failed to fetch messages:', error);
  }
};

const sendMessage = async () => {
  if (!newMessage.value.trim() || !activeSession.value || isSending.value) return;

  const content = newMessage.value;
  newMessage.value = '';
  isSending.value = true;

  try {
    const response = await axios.post(route('staff.chat.send'), {
      content,
      session_id: activeSession.value.id
    });

    activeMessages.value.push(response.data);

    const session = sessions.value.find(s => s.id === activeSession.value.id);
    if (session) {
      session.messages = [response.data];
      session.updated_at = new Date().toISOString();
    }

    scrollToBottom();
  } catch (error) {
    console.error('Failed to send message:', error);
  } finally {
    isSending.value = false;
  }
};

const scrollToBottom = async () => {
  await nextTick();
  if (messageContainer.value) {
    messageContainer.value.scrollTop = messageContainer.value.scrollHeight;
  }
};

const initGlobalListener = () => {
  if (!window.Echo) return;
  sessions.value.forEach(session => subscribeToSession(session.id));
};

const subscribeToSession = (sessionId) => {
  window.Echo.channel(`chat.${sessionId}`)
    .listen('.message.sent', (e) => {
      if (activeSession.value && activeSession.value.id === sessionId) {
        if (e.message.senderType !== 'staff') {
          activeMessages.value.push({
            content: e.message.content,
            sender_type: e.message.senderType,
            created_at: new Date().toISOString()
          });
          scrollToBottom();
        }
      } else {
        if (e.message.senderType !== 'staff') {
          unreadSessions.value.add(sessionId);
        }
      }

      const session = sessions.value.find(s => s.id === sessionId);
      if (session) {
        session.messages = [{ content: e.message.content }];
        session.updated_at = new Date().toISOString();
      }
    });
};

onMounted(async () => {
  await fetchSessions();
  initGlobalListener();
});

onUnmounted(() => {
  sessions.value.forEach(session => {
    window.Echo.leave(`chat.${session.id}`);
  });
});
</script>

<style scoped>
/* ===================== LAYOUT ===================== */
.chat-page {
  height: calc(100vh - 64px);
  padding: 0;
  background: #f1f5f9;
  display: flex;
  flex-direction: column;
}

.chat-wrapper {
  display: flex;
  flex: 1;
  height: 100%;
  overflow: hidden;
  background: #fff;
  border-top: 1px solid #e5e7eb;
}

/* ===================== SIDEBAR ===================== */
.sidebar {
  width: 280px;
  min-width: 260px;
  border-right: 1px solid #e5e7eb;
  display: flex;
  flex-direction: column;
  background: #fff;
  flex-shrink: 0;
}

.sidebar-header {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 18px 16px 14px;
}

.sidebar-logo {
  width: 38px;
  height: 38px;
  background: #ede9fe;
  color: #6366f1;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  flex-shrink: 0;
}

.sidebar-title {
  font-size: 17px;
  font-weight: 700;
  color: #1e1b4b;
  margin: 0;
}

.sidebar-search {
  padding: 0 14px 12px;
  position: relative;
}

.search-icon {
  position: absolute;
  left: 26px;
  top: 50%;
  transform: translateY(-50%);
  color: #9ca3af;
  font-size: 13px;
}

.search-input {
  width: 100%;
  background: #f3f4f6;
  border: none;
  border-radius: 10px;
  padding: 8px 14px 8px 34px;
  font-size: 13px;
  color: #374151;
  outline: none;
  transition: box-shadow 0.2s;
}
.search-input:focus {
  box-shadow: 0 0 0 2px #a5b4fc;
}

.sidebar-tabs {
  display: flex;
  gap: 6px;
  padding: 0 14px 12px;
  border-bottom: 1px solid #f3f4f6;
}

.tab-btn {
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  background: #f3f4f6;
  color: #6b7280;
  transition: all 0.15s;
}

.tab-btn--active {
  background: #6366f1;
  color: #fff;
}

.tab-btn:not(.tab-btn--active):hover {
  background: #e5e7eb;
}

/* ===================== SESSION LIST ===================== */
.session-list {
  flex: 1;
  overflow-y: auto;
}

.session-empty {
  padding: 40px 20px;
  text-align: center;
  color: #9ca3af;
  font-size: 13px;
}

.session-empty i {
  font-size: 32px;
  display: block;
  margin-bottom: 10px;
}

.session-item {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  padding: 12px 14px;
  border-left: 3px solid transparent;
  cursor: pointer;
  transition: background 0.15s;
  border-bottom: 1px solid #f9fafb;
  position: relative;
}

.session-item:hover {
  background: #f9fafb;
}

.session-item--active {
  background: #eef2ff;
  border-left-color: #6366f1;
}

.session-avatar-wrap {
  position: relative;
  flex-shrink: 0;
}

.session-avatar-img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.session-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 15px;
}

.session-avatar--guest {
  background: #e5e7eb;
  color: #6b7280;
}

.online-dot {
  position: absolute;
  bottom: 1px;
  right: 1px;
  width: 10px;
  height: 10px;
  background: #22c55e;
  border: 2px solid #fff;
  border-radius: 50%;
}

.session-info {
  flex: 1;
  min-width: 0;
}

.session-meta {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  margin-bottom: 2px;
}

.session-name {
  font-size: 13px;
  font-weight: 600;
  color: #111827;
  margin: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.session-time {
  font-size: 10px;
  color: #9ca3af;
  white-space: nowrap;
  margin-left: 6px;
  flex-shrink: 0;
}

.session-preview {
  font-size: 12px;
  color: #6b7280;
  margin: 0 0 6px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.session-badge {
  display: inline-block;
  font-size: 10px;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: 20px;
  line-height: 1.6;
}

.session-badge--member {
  background: #6366f1;
  color: #fff;
}

.session-badge--guest {
  background: #f3f4f6;
  color: #6b7280;
  border: 1px solid #e5e7eb;
}

.unread-dot {
  position: absolute;
  top: 50%;
  right: 12px;
  transform: translateY(-50%);
  width: 8px;
  height: 8px;
  background: #6366f1;
  border-radius: 50%;
}

/* ===================== CHAT MAIN ===================== */
.chat-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  background: #f8fafc;
  min-width: 0;
  overflow: hidden;
}

/* Chat header */
.chat-header {
  height: 68px;
  padding: 0 24px;
  background: #fff;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-shrink: 0;
}

.chat-header-user {
  display: flex;
  align-items: center;
  gap: 12px;
}

.chat-header-avatar-wrap {
  position: relative;
}

.chat-header-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.chat-header-name {
  font-size: 15px;
  font-weight: 700;
  color: #111827;
  margin: 0;
}

.chat-header-status {
  font-size: 11px;
  color: #22c55e;
  font-weight: 500;
  margin: 0;
}

.chat-header-actions {
  display: flex;
  gap: 16px;
}

.action-btn {
  background: none;
  border: none;
  color: #9ca3af;
  cursor: pointer;
  font-size: 15px;
  transition: color 0.15s;
  padding: 4px;
}

.action-btn:hover {
  color: #6366f1;
}

/* Messages */
.messages-area {
  flex: 1;
  overflow-y: auto;
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.date-separator {
  display: flex;
  justify-content: center;
  margin: 4px 0;
}

.date-badge {
  background: #e5e7eb;
  color: #6b7280;
  font-size: 10px;
  font-weight: 700;
  padding: 4px 12px;
  border-radius: 20px;
  letter-spacing: 0.08em;
}

/* Message rows */
.msg-row {
  display: flex;
  gap: 10px;
  max-width: 75%;
}

.msg-row--left {
  align-self: flex-start;
  align-items: flex-end;
}

.msg-row--right {
  align-self: flex-end;
  align-items: flex-end;
}

.msg-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
}

.msg-avatar-staff {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #ede9fe;
  color: #6366f1;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  flex-shrink: 0;
}

.msg-content {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.msg-content--right {
  align-items: flex-end;
}

.bubble {
  padding: 12px 16px;
  border-radius: 18px;
  font-size: 13.5px;
  line-height: 1.55;
  word-break: break-word;
}

.bubble--customer {
  background: #fff;
  border: 1px solid #e5e7eb;
  color: #1f2937;
  border-radius: 18px 18px 18px 4px;
  box-shadow: 0 1px 3px rgba(0,0,0,.05);
}

.bubble--staff {
  background: #6366f1;
  color: #fff;
  border-radius: 18px 18px 4px 18px;
  box-shadow: 0 2px 8px rgba(99,102,241,.25);
}

.msg-time {
  font-size: 10px;
  color: #9ca3af;
  margin-left: 4px;
}

.msg-time--right {
  margin-left: 0;
  margin-right: 4px;
}

/* Typing indicator */
.typing-indicator {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
  color: #9ca3af;
  font-style: italic;
  margin-top: 4px;
}

.typing-dots {
  display: flex;
  gap: 3px;
}

.typing-dots span {
  width: 5px;
  height: 5px;
  background: #9ca3af;
  border-radius: 50%;
  animation: bounce 1s infinite;
}

.typing-dots span:nth-child(2) { animation-delay: 0.15s; }
.typing-dots span:nth-child(3) { animation-delay: 0.3s; }

@keyframes bounce {
  0%, 80%, 100% { transform: translateY(0); }
  40% { transform: translateY(-5px); }
}

/* Input area */
.input-area {
  padding: 12px 16px;
  background: #fff;
  border-top: 1px solid #e5e7eb;
  flex-shrink: 0;
}

.input-box {
  background: #f8fafc;
  border: 1px solid #e5e7eb;
  border-radius: 14px;
  padding: 4px 6px 6px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.msg-input {
  width: 100%;
  background: transparent;
  border: none;
  outline: none;
  font-size: 14px;
  color: #374151;
  padding: 8px 10px 4px;
  resize: none;
}

.msg-input::placeholder {
  color: #9ca3af;
}

.input-toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 6px 2px;
}

.toolbar-tools {
  display: flex;
  align-items: center;
  gap: 12px;
}

.tool-btn {
  background: none;
  border: none;
  color: #9ca3af;
  cursor: pointer;
  font-size: 14px;
  padding: 2px;
  transition: color 0.15s;
  line-height: 1;
}

.tool-btn:hover {
  color: #6b7280;
}

.tool-divider {
  width: 1px;
  height: 16px;
  background: #d1d5db;
  margin: 0 2px;
}

.send-btn {
  background: #6366f1;
  color: #fff;
  border: none;
  border-radius: 10px;
  padding: 8px 18px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 7px;
  transition: background 0.15s, opacity 0.15s;
}

.send-btn:hover:not(:disabled) {
  background: #4f46e5;
}

.send-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Empty state */
.chat-empty {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #9ca3af;
  text-align: center;
  padding: 40px;
}

.chat-empty-icon {
  width: 80px;
  height: 80px;
  background: #f3f4f6;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 36px;
  color: #d1d5db;
  margin-bottom: 20px;
}

.chat-empty h3 {
  font-size: 18px;
  font-weight: 700;
  color: #6b7280;
  margin: 0 0 8px;
}

.chat-empty p {
  font-size: 13px;
  color: #9ca3af;
  margin: 0;
}

/* ===================== CUSTOMER PANEL ===================== */
.customer-panel {
  width: 290px;
  min-width: 270px;
  border-left: 1px solid #e5e7eb;
  display: flex;
  flex-direction: column;
  background: #fff;
  flex-shrink: 0;
  overflow: hidden;
}

.customer-panel--empty {
  display: flex;
  align-items: center;
  justify-content: center;
}

.panel-empty-hint {
  text-align: center;
  color: #d1d5db;
  font-size: 13px;
  padding: 20px;
}

.panel-empty-hint i {
  font-size: 48px;
  display: block;
  margin-bottom: 12px;
}

.panel-body {
  flex: 1;
  overflow-y: auto;
  padding: 20px 18px;
}

/* Profile */
.panel-profile {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding-bottom: 20px;
  border-bottom: 1px solid #f3f4f6;
  margin-bottom: 20px;
}

.panel-avatar {
  width: 84px;
  height: 84px;
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 12px;
  border: 3px solid #f3f4f6;
  box-shadow: 0 2px 8px rgba(0,0,0,.08);
}

.panel-name {
  font-size: 16px;
  font-weight: 700;
  color: #111827;
  margin: 0 0 4px;
}

.panel-email {
  font-size: 12px;
  color: #9ca3af;
  margin: 0 0 10px;
}

.panel-badge {
  display: inline-block;
  background: #ede9fe;
  color: #6d28d9;
  font-size: 10px;
  font-weight: 700;
  padding: 4px 12px;
  border-radius: 20px;
  letter-spacing: 0.04em;
}

/* Sections */
.panel-section {
  margin-bottom: 20px;
}

.panel-section-title {
  font-size: 10px;
  font-weight: 700;
  color: #9ca3af;
  letter-spacing: 0.1em;
  margin: 0 0 10px;
  text-transform: uppercase;
}

.panel-info-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.panel-info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 13px;
}

.panel-info-label {
  color: #9ca3af;
}

.panel-info-value {
  font-weight: 500;
  color: #111827;
}

/* Activity */
.activity-list {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.activity-item {
  display: flex;
  gap: 10px;
  align-items: flex-start;
}

.activity-icon {
  width: 30px;
  height: 30px;
  background: #f3f4f6;
  color: #6b7280;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  flex-shrink: 0;
}

.activity-title {
  font-size: 12.5px;
  font-weight: 500;
  color: #111827;
  margin: 0 0 2px;
}

.activity-time {
  font-size: 11px;
  color: #9ca3af;
  margin: 0;
}

/* Note */
.note-textarea {
  width: 100%;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  padding: 10px 12px;
  font-size: 13px;
  color: #374151;
  resize: none;
  outline: none;
  transition: box-shadow 0.2s;
  box-sizing: border-box;
}

.note-textarea:focus {
  box-shadow: 0 0 0 2px #a5b4fc;
  border-color: #a5b4fc;
}

.note-textarea::placeholder {
  color: #9ca3af;
}

/* Panel footer */
.panel-footer {
  padding: 12px 16px;
  border-top: 1px solid #f3f4f6;
  background: #f9fafb;
  display: flex;
  gap: 8px;
  flex-shrink: 0;
}

.panel-btn {
  flex: 1;
  padding: 9px 12px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.15s;
}

.panel-btn--tag {
  background: #fff;
  border: 1px solid #e5e7eb;
  color: #374151;
}

.panel-btn--tag:hover {
  background: #f3f4f6;
}

.panel-btn--block {
  background: #fef2f2;
  color: #dc2626;
}

.panel-btn--block:hover {
  background: #fee2e2;
}

/* Scrollbar */
::-webkit-scrollbar { width: 5px; height: 5px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
</style>
