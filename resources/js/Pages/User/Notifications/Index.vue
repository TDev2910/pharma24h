<template>
  <div class="notifications-container">
    <Toast />
    <!-- Header -->
    <div class="notifications-header">
      <h1 class="notifications-title">Thông báo</h1>
      <div class="header-actions">
        <button 
          v-if="unreadCount > 0" 
          @click="markAllAsRead" 
          class="btn-mark-all-read"
          :disabled="markingAll"
        >
          <i class="fas fa-check-double"></i>
          Đánh dấu tất cả đã đọc
        </button>
      </div>
    </div>

    <!-- Notifications List -->
    <div class="notifications-list" v-if="notifications && notifications.data && notifications.data.length > 0">
      <div 
        v-for="notification in notifications.data" 
        :key="notification.id" 
        class="notification-item"
        :class="{ 'unread': !notification.read_at }"
        @click="handleNotificationClick(notification)"
      >
        <div class="notification-icon">
          <i :class="notification.data?.icon || 'fas fa-bell'"></i>
        </div>
        <div class="notification-content">
          <div class="notification-message">
            {{ notification.data?.message || 'Thông báo mới' }}
          </div>
          <div class="notification-time">
            {{ formatTime(notification.created_at) }}
          </div>
        </div>
        <div class="notification-actions">
          <span v-if="!notification.read_at" class="unread-dot"></span>
          <button 
            @click.stop="deleteNotification(notification.id)" 
            class="btn-delete"
            title="Xóa thông báo"
          >
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>

      <!-- Pagination -->
      <div class="pagination" v-if="notifications.last_page > 1">
        <button 
          @click="loadPage(notifications.current_page - 1)"
          :disabled="notifications.current_page === 1"
          class="pagination-btn"
        >
          <i class="fas fa-chevron-left"></i> Trước
        </button>
        <span class="pagination-info">
          Trang {{ notifications.current_page }} / {{ notifications.last_page }}
        </span>
        <button 
          @click="loadPage(notifications.current_page + 1)"
          :disabled="notifications.current_page === notifications.last_page"
          class="pagination-btn"
        >
          Sau <i class="fas fa-chevron-right"></i>
        </button>
      </div>
    </div>

    <!-- Empty State -->
    <div class="empty-state" v-else>
      <i class="fas fa-bell-slash empty-icon"></i>
      <p class="empty-text">Bạn chưa có thông báo nào.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import axios from 'axios'
import { useToast } from 'primevue/usetoast'
import Toast from 'primevue/toast'

const toast = useToast()

// Props từ Inertia
const props = defineProps({
  notifications: {
    type: Object,
    default: () => ({ data: [] })
  },
  unreadNotificationsCount: {
    type: Number,
    default: 0
  }
})

// States
const markingAll = ref(false)
const unreadCount = computed(() => props.unreadNotificationsCount || 0)

// Helper functions
const formatTime = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  const now = new Date()
  const diff = now - date
  const seconds = Math.floor(diff / 1000)
  const minutes = Math.floor(seconds / 60)
  const hours = Math.floor(minutes / 60)
  const days = Math.floor(hours / 24)

  if (days > 7) {
    const day = String(date.getDate()).padStart(2, '0')
    const month = String(date.getMonth() + 1).padStart(2, '0')
    const year = date.getFullYear()
    return `${day}/${month}/${year}`
  } else if (days > 0) {
    return `${days} ngày trước`
  } else if (hours > 0) {
    return `${hours} giờ trước`
  } else if (minutes > 0) {
    return `${minutes} phút trước`
  } else {
    return 'Vừa xong'
  }
}

// Handle notification click
const handleNotificationClick = (notification) => {
  // Đánh dấu đã đọc nếu chưa đọc
  if (!notification.read_at) {
    markAsRead(notification.id)
  }

  // Navigate to URL nếu có
  if (notification.data?.url) {
    router.visit(notification.data.url)
  }
}

// Mark notification as read
const markAsRead = async (notificationId) => {
  try {
    await axios.post(`/user/notifications/${notificationId}/read`)
    // Reload để cập nhật danh sách
    router.reload({ only: ['notifications', 'unreadNotificationsCount'] })
  } catch (error) {
    console.error('Error marking notification as read:', error)
  }
}

// Mark all as read
const markAllAsRead = async () => {
  markingAll.value = true
  try {
    await axios.post('/user/notifications/mark-all-read')
    toast.add({
      severity: 'success',
      summary: 'Thành công',
      detail: 'Đã đánh dấu tất cả thông báo là đã đọc',
      life: 3000
    })
    router.reload({ only: ['notifications', 'unreadNotificationsCount'] })
  } catch (error) {
    console.error('Error marking all as read:', error)
    toast.add({
      severity: 'error',
      summary: 'Lỗi',
      detail: 'Không thể đánh dấu tất cả đã đọc',
      life: 3000
    })
  } finally {
    markingAll.value = false
  }
}

// Delete notification
const deleteNotification = async (notificationId) => {
  if (!confirm('Bạn có chắc chắn muốn xóa thông báo này?')) {
    return
  }

  try {
    await axios.delete(`/user/notifications/${notificationId}`)
    toast.add({
      severity: 'success',
      summary: 'Thành công',
      detail: 'Đã xóa thông báo',
      life: 3000
    })
    router.reload({ only: ['notifications', 'unreadNotificationsCount'] })
  } catch (error) {
    console.error('Error deleting notification:', error)
    toast.add({
      severity: 'error',
      summary: 'Lỗi',
      detail: 'Không thể xóa thông báo',
      life: 3000
    })
  }
}

// Load page
const loadPage = (page) => {
  router.get('/user/notifications', { page }, {
    preserveState: true,
    preserveScroll: true,
    only: ['notifications']
  })
}
</script>

<style scoped>
.notifications-container {
  max-width: 1200px;
  margin: 0 auto;
}

.notifications-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  flex-wrap: wrap;
  gap: 16px;
}

.notifications-title {
  font-size: 28px;
  font-weight: 700;
  color: #2c3e50;
  margin: 0;
}

.header-actions {
  display: flex;
  gap: 12px;
}

.btn-mark-all-read {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background-color: #3b82f6;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-mark-all-read:hover:not(:disabled) {
  background-color: #2563eb;
}

.btn-mark-all-read:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.notifications-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.notification-item {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  padding: 20px;
  background: white;
  border: 1px solid #e9ecef;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
}

.notification-item:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
  border-color: #3b82f6;
}

.notification-item.unread {
  background-color: #f0f9ff;
  border-color: #3b82f6;
  border-left: 4px solid #3b82f6;
}

.notification-icon {
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 12px;
  color: white;
  font-size: 20px;
  flex-shrink: 0;
}

.notification-content {
  flex: 1;
  min-width: 0;
}

.notification-message {
  font-size: 15px;
  font-weight: 500;
  color: #2c3e50;
  margin-bottom: 8px;
  line-height: 1.5;
}

.notification-time {
  font-size: 13px;
  color: #6c757d;
}

.notification-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-shrink: 0;
}

.unread-dot {
  width: 10px;
  height: 10px;
  background-color: #3b82f6;
  border-radius: 50%;
  flex-shrink: 0;
}

.btn-delete {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: transparent;
  border: 1px solid #e9ecef;
  border-radius: 6px;
  color: #6c757d;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-delete:hover {
  background-color: #fee2e2;
  border-color: #ef4444;
  color: #ef4444;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 16px;
  margin-top: 24px;
  padding: 20px;
}

.pagination-btn {
  padding: 10px 20px;
  background: white;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  color: #495057;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 8px;
}

.pagination-btn:hover:not(:disabled) {
  background-color: #f8f9fa;
  border-color: #3b82f6;
  color: #3b82f6;
}

.pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.pagination-info {
  font-size: 14px;
  color: #6c757d;
  font-weight: 500;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
}

.empty-icon {
  font-size: 64px;
  color: #dee2e6;
  margin-bottom: 16px;
}

.empty-text {
  font-size: 16px;
  color: #6c757d;
  margin: 0;
}

/* Responsive */
@media (max-width: 768px) {
  .notifications-header {
    flex-direction: column;
    align-items: stretch;
  }

  .notification-item {
    flex-wrap: wrap;
  }

  .notification-actions {
    width: 100%;
    justify-content: flex-end;
    margin-top: 8px;
  }

  .pagination {
    flex-direction: column;
    gap: 12px;
  }
}
</style>

