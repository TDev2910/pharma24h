<template>

  <Head title="Quản lý Chat" />

  <!-- Root container của trang Chat Staff -->
  <div class="cp-page-root">
    <div class="cp-layout-main">

      <!-- CỘT 1: Danh sách hội thoại (Sidebar) -->
      <aside class="cp-sidebar">
        <div class="cp-sidebar-header">
          <div class="cp-logo">
            <i class="fas fa-comment-dots"></i>
          </div>
          <h2 class="cp-title">Quản Lý Chat</h2>
        </div>

        <div class="cp-search-area">
          <div class="cp-search-box">
            <i class="fas fa-search"></i>
            <input v-model="searchQuery" type="text" placeholder="Tìm kiếm hội thoại..." />
          </div>
        </div>

        <div class="cp-tabs">
          <button @click="activeFilter = 'all'" :class="{ active: activeFilter === 'all' }">Tất cả</button>
          <button @click="activeFilter = 'member'" :class="{ active: activeFilter === 'member' }">Thành viên</button>
          <button @click="activeFilter = 'guest'" :class="{ active: activeFilter === 'guest' }">Khách</button>
        </div>

        <div class="cp-session-list">
          <div v-if="filteredSessions.length === 0" class="cp-empty-sessions">
            <p>Không tìm thấy hội thoại</p>
          </div>

          <div v-for="session in filteredSessions" :key="session.id" @click="selectSession(session)"
            :class="['cp-session-card', activeSession?.id === session.id ? 'active' : '']">
            <div class="cp-avatar-wrap">
              <img
                :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(session.customer_name || 'KH')}&background=6366f1&color=fff`" />
              <div class="cp-status-dot"></div>
            </div>
            <div class="cp-session-info">
              <div class="cp-session-top">
                <span class="cp-cust-name">{{ session.customer_name || 'Khách' }}</span>
                <span class="cp-time">{{ formatTime(session.updated_at) }}</span>
              </div>
              <p class="cp-preview">{{ session.messages?.[0]?.content || '...' }}</p>
              <span :class="['cp-badge', session.type === 'guest' ? 'guest' : 'member']" style="font-size: 15px;">
                {{ session.type === 'guest' ? 'Khách vãng lai' : 'Thành viên' }}
              </span>
            </div>
          </div>
        </div>
      </aside>

      <!-- CỘT 2: Nội dung Chat -->
      <section class="cp-chat-view">
        <template v-if="activeSession">
          <header class="cp-chat-header">
            <div class="cp-header-user">
              <img
                :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(activeSession.customer_name || 'KH')}&background=6366f1&color=fff`" />
              <div>
                <h4>{{ activeSession.customer_name || 'Khách' }}</h4>
                <p class="online">Đang trực tuyến</p>
              </div>
            </div>
          </header>

          <div class="cp-messages-viewport" ref="messageContainer">
            <div v-for="(msg, index) in activeMessages" :key="index"
              :class="['cp-msg-row', msg.sender_type === 'staff' ? 'mine' : 'theirs']">
              <div class="cp-bubble">{{ msg.content }}</div>
              <span class="cp-msg-time">{{ formatTime(msg.created_at) }}</span>
            </div>
          </div>

          <footer class="cp-input-area">
            <div class="cp-input-wrapper">
              <input v-model="newMessage" @keyup.enter="sendMessage" type="text" placeholder="Nhập tin nhắn..." />
              <button @click="sendMessage" :disabled="!newMessage.trim() || isSending">
                <i class="fas fa-paper-plane" v-if="!isSending"></i>
                <i class="fas fa-spinner fa-spin" v-else></i>
              </button>
            </div>
          </footer>
        </template>
        <div v-else class="cp-welcome-state">
          <i class="fas fa-comments"></i>
          <h3>Trung tâm Hỗ trợ Khách hàng</h3>
          <p>Chọn một hội thoại để tiến hành tư vấn</p>
        </div>
      </section>

      <!-- CỘT 3: Chi tiết khách hàng -->
      <aside class="cp-info-panel" v-if="activeSession">
        <div class="cp-panel-card">
          <img
            :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(activeSession.customer_name || 'KH')}&background=6366f1&color=fff&size=150`"
            class="cp-panel-avatar" />
          <h3 class="cp-panel-name">{{ activeSession.customer_name || 'Khách' }}</h3>
          <p class="cp-panel-email">{{ activeSession.customer_email || 'Chưa cung cấp email' }}</p>
          <div class="cp-panel-tags">
            <span class="tag">KHÁCH HÀNG MỚI</span>
          </div>
        </div>

        <div class="cp-history-section">
          <h5>THÔNG TIN CƠ BẢN</h5>
          <div class="info-row">
            <span>Ngày tạo:</span>
            <strong>{{ formatDate(activeSession.created_at) }}</strong>
          </div>
          <div class="info-row">
            <span>Loại:</span>
            <strong>{{ activeSession.type === 'guest' ? 'Vãng lai' : 'Thành viên' }}</strong>
          </div>
        </div>

        <div class="cp-note-section">
          <h5>GHI CHÚ NỘI BỘ</h5>
          <textarea v-model="internalNote" placeholder="Ghi chú quan trọng về khách hàng..."></textarea>
        </div>
      </aside>
    </div>
  </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed, nextTick } from 'vue';

const sessions = ref([]);
const activeSession = ref(null);
const activeMessages = ref([]);
const newMessage = ref('');
const isSending = ref(false);
const messageContainer = ref(null);
const searchQuery = ref('');
const activeFilter = ref('all');
const internalNote = ref('');

const filteredSessions = computed(() => {
  let list = [...sessions.value].sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at));
  if (activeFilter.value === 'member') list = list.filter(s => s.type === 'member');
  else if (activeFilter.value === 'guest') list = list.filter(s => s.type === 'guest');
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

const formatDate = (dateStr) => dateStr ? new Date(dateStr).toLocaleDateString('vi-VN') : 'N/A';

const fetchSessions = async () => {
  try {
    const response = await window.axios.get(route('staff.chat.sessions'));
    sessions.value = response.data;
  } catch (error) { console.error(error); }
};

const selectSession = async (session) => {
  // Rời khỏi kênh cũ nếu đang có session khác active
  if (activeSession.value && window.Echo) {
    window.Echo.leave(`chat.${activeSession.value.id}`);
  }

  activeSession.value = session;
  try {
    const response = await window.axios.get(route('staff.chat.messages', { sessionId: session.id }));
    activeMessages.value = response.data;
    scrollToBottom();

    // Lắng nghe real-time cho session này
    if (window.Echo) {
      window.Echo.channel(`chat.${session.id}`)
        .listen('.message.sent', (e) => {
          // Chỉ thêm vào nếu là tin nhắn từ khách (tránh bị lặp tin của chính mình)
          if (e.message.senderType !== 'staff') {
            activeMessages.value.push({
              content: e.message.content,
              sender_type: e.message.senderType,
              created_at: new Date().toISOString()
            });
            scrollToBottom();
          }
        });
    }
  } catch (error) { console.error(error); }
};

const sendMessage = async () => {
  if (!newMessage.value.trim() || !activeSession.value || isSending.value) return;
  const content = newMessage.value; newMessage.value = ''; isSending.value = true;
  try {
    const response = await window.axios.post(route('staff.chat.send'), { content, session_id: activeSession.value.id });
    activeMessages.value.push(response.data);
    scrollToBottom();
  } catch (error) { console.error(error); } finally { isSending.value = false; }
};

const scrollToBottom = async () => {
  await nextTick();
  if (messageContainer.value) messageContainer.value.scrollTop = messageContainer.value.scrollHeight;
};

onMounted(() => {
  fetchSessions();
  if (window.Echo) {
    window.Echo.private('staff.chats').listen('.new.chat.message', () => {
      fetchSessions();
    });
  }
});

onUnmounted(() => {
  if (activeSession.value && window.Echo) {
    window.Echo.leave(`chat.${activeSession.value.id}`);
  }
  if (window.Echo) {
    window.Echo.leave('staff.chats');
  }
});
</script>

<style scoped>
/* KHUNG LAYOUT CHỦ ĐẠO - ĐẢM BẢO KHÔNG BỊ TRÔI */
.cp-page-root {
  height: calc(100vh - 64px);
  width: 100%;
  background: #f1f5f9;
  overflow: hidden;
  display: flex;
}

.cp-layout-main {
  display: flex;
  width: 100%;
  height: 100%;
  background: #fff;
  border: none;
  position: relative;
}

/* SIDEBAR TRÁI */
.cp-sidebar {
  width: 300px;
  min-width: 300px;
  border-right: 1px solid #e5e7eb;
  display: flex;
  flex-direction: column;
}

.cp-sidebar-header {
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 12px;
}

.cp-logo {
  width: 36px;
  height: 36px;
  background: #ede9fe;
  color: #6366f1;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.cp-title {
  font-size: 18px;
  font-weight: 700;
  margin: 0;
  color: #1e293b;
}

.cp-search-area {
  padding: 0 15px 15px;
}

.cp-search-box {
  position: relative;
  width: 100%;
}

.cp-search-box i {
  position: absolute;
  left: 12px;
  top: 10px;
  color: #64748b;
  font-size: 14px;
}

.cp-search-box input {
  width: 100%;
  padding: 8px 12px 8px 36px;
  background: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 8px;
  font-size: 14px;
}

.cp-tabs {
  display: flex;
  gap: 8px;
  padding: 0 15px 15px;
  border-bottom: 1px solid #f1f5f9;
}

.cp-tabs button {
  padding: 5px 12px;
  border-radius: 20px;
  border: 1px solid #e2e8f0;
  background: #fff;
  font-size: 12px;
  cursor: pointer;
  color: #64748b;
}

.cp-tabs button.active {
  background: #6366f1;
  color: #fff;
  border-color: #6366f1;
}

.cp-session-list {
  flex: 1;
  overflow-y: auto;
}

.cp-session-card {
  display: flex;
  padding: 15px;
  gap: 12px;
  border-bottom: 1px solid #f8fafc;
  cursor: pointer;
  transition: all 0.2s;
}

.cp-session-card:hover {
  background: #f8fafc;
}

.cp-session-card.active {
  background: #eff6ff;
  border-left: 4px solid #3b82f6;
}

.cp-avatar-wrap {
  position: relative;
  width: 48px;
  height: 48px;
}

.cp-avatar-wrap img {
  width: 80%;
  height: 80%;
  border-radius: 50%;
}

.cp-status-dot {
  position: absolute;
  bottom: 2px;
  right: 2px;
  width: 12px;
  height: 12px;
  background: #22c55e;
  border: 2px solid #fff;
  border-radius: 50%;
}

.cp-session-info {
  flex: 1;
  min-width: 0;
}

.cp-session-top {
  display: flex;
  justify-content: space-between;
  margin-bottom: 4px;
}

.cp-cust-name {
  font-weight: 600;
  font-size: 14px;
  color: #1e293b;
}

.cp-time {
  font-size: 11px;
  color: #94a3b8;
}

.cp-preview {
  font-size: 12px;
  color: #64748b;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* KHU VỰC CHAT CHÍNH */
.cp-chat-view {
  flex: 1;
  display: flex;
  flex-direction: column;
  background: #fff;
  min-width: 0;
}

.cp-chat-header {
  height: 64px;
  padding: 0 20px;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  align-items: center;
}

.cp-header-user {
  display: flex;
  align-items: center;
  gap: 12px;
}

.cp-header-user img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
}

.cp-header-user h4 {
  margin: 0;
  font-size: 16px;
  font-weight: 700;
  color: #1e293b;
}

.cp-header-user .online {
  font-size: 11px;
  color: #22c55e;
}

.cp-messages-viewport {
  flex: 1;
  padding: 20px;
  overflow-y: auto;
  background: #f8fafc;
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.cp-msg-row {
  max-width: 75%;
  display: flex;
  flex-direction: column;
}

.cp-msg-row.mine {
  align-self: flex-end;
  align-items: flex-end;
}

.cp-msg-row.theirs {
  align-self: flex-start;
}

.cp-bubble {
  padding: 10px 15px;
  border-radius: 12px;
  font-size: 14px;
  line-height: 1.5;
}

.mine .cp-bubble {
  background: #3b82f6;
  color: #fff;
  border-bottom-right-radius: 2px;
}

.theirs .cp-bubble {
  background: #fff;
  color: #1e293b;
  border: 1px solid #e2e8f0;
  border-bottom-left-radius: 2px;
}

.cp-msg-time {
  font-size: 10px;
  color: #94a3b8;
  margin-top: 5px;
}

.cp-input-area {
  padding: 20px;
  border-top: 1px solid #e2e8f0;
}

.cp-input-wrapper {
  display: flex;
  gap: 12px;
  background: #f1f5f9;
  padding: 10px 15px;
  border-radius: 12px;
}

.cp-input-wrapper input {
  flex: 1;
  background: transparent;
  border: none;
  outline: none;
  font-size: 14px;
}

.cp-input-wrapper button {
  background: #3b82f6;
  color: #fff;
  border: none;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  cursor: pointer;
}

/* PANEL THÔNG TIN PHẢI */
.cp-info-panel {
  width: 280px;
  min-width: 280px;
  border-left: 1px solid #e2e8f0;
  padding: 20px;
  overflow-y: auto;
  background: #fff;
}

.cp-panel-card {
  text-align: center;
  margin-bottom: 25px;
  padding-bottom: 25px;
  border-bottom: 1px solid #f1f5f9;
}

.cp-panel-avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  margin-bottom: 15px;
  border: 3px solid #f1f5f9;
}

.cp-panel-name {
  font-size: 18px;
  font-weight: 700;
  margin: 0 0 5px;
}

.cp-panel-email {
  font-size: 12px;
  color: #64748b;
  margin-bottom: 15px;
}

.tag {
  background: #fef08a;
  color: #854d0e;
  font-size: 10px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 4px;
}

.cp-history-section h5,
.cp-note-section h5 {
  font-size: 11px;
  font-weight: 700;
  color: #94a3b8;
  margin-bottom: 15px;
  border-bottom: 1px solid #f1f5f9;
  padding-bottom: 5px;
}

.info-row {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
  margin-bottom: 10px;
}

.info-row span {
  color: #64748b;
}

.cp-note-section textarea {
  width: 100%;
  height: 100px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 10px;
  font-size: 13px;
  resize: none;
  margin-top: 5px;
}

.cp-welcome-state {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #cbd5e1;
}

.cp-welcome-state i {
  font-size: 64px;
  margin-bottom: 20px;
}
</style>
