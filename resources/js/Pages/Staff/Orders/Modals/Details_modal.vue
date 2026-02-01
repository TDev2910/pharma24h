<script setup>
import { computed, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

// PrimeVue Components
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Textarea from 'primevue/textarea';

const props = defineProps({
  show: Boolean,
  order: Object
});

const emit = defineEmits(['close', 'updated']);

// State nội bộ
const loading = ref(false);
const processing = ref(false);
const creatingGHN = ref(false);
const syncingGHN = ref(false);
const Note = ref('');
const localOrder = ref(null); // Dùng để lưu trữ và cập nhật data cục bộ nếu cần

// Two-way binding cho Dialog
const isVisible = computed({
  get: () => props.show,
  set: (value) => {
    if (!value) emit('close');
  }
});

// Sync data khi mở modal
watch(() => props.order, (newVal) => {
  if (newVal) {
    localOrder.value = newVal;
    Note.value = newVal.cancellation_note || '';
  }
}, { immediate: true });

// --- FORMATTERS ---
const formatCurrency = (val) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val);
const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  return new Date(dateString).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
};

const getStatusSeverity = (status) => {
  const map = {
    'pending': 'warning', 'confirmed': 'info', 'delivering': 'primary', 'new': 'info',
    'completed': 'success', 'cancelled': 'danger'
  };
  return map[status] || 'secondary';
};

const getStatusLabel = (status) => {
  const map = {
    'pending': 'Chờ xử lý', 'confirmed': 'Đã xác nhận', 'delivering': 'Đang giao', 'new': 'Đơn hàng mới',
    'completed': 'Hoàn thành', 'cancelled': 'Đã hủy'
  };
  return map[status] || status;
};

const getPaymentStatusSeverity = (status) => {
  const map = { 'paid': 'success', 'pending': 'warning', 'refunded': 'info', 'failed': 'danger', 'cancelled': 'danger' };
  return map[status] || 'secondary';
};

const getPaymentStatusLabel = (status) => {
  const map = { 'paid': 'Đã thanh toán', 'pending': 'Chưa thanh toán', 'refunded': 'Đã hoàn tiền', 'failed': 'Thất bại', 'cancelled': 'Đã hủy' };
  return map[status] || status;
}

// Reload lại dữ liệu đơn hàng (nếu cần cập nhật sau khi thao tác)
const reloadOrder = async () => {
  if (!localOrder.value?.id) return;
  try {
    emit('updated');
  } catch (e) { console.error(e); }
};

// In hóa đơn
const printInvoice = () => {
  if (!localOrder.value?.id) return;
  window.open(`/staff/orders/${localOrder.value.id}/invoice`, '_blank');
};

// Tạo đơn GHN
const createGHNOrder = () => {
  if (!props.order.district_id || !props.order.ward_code) {
    alert('Lỗi: Đơn hàng thiếu thông tin Quận/Huyện hoặc Phường/Xã.');
    return;
  }
  if (!confirm('Xác nhận tạo đơn hàng trên hệ thống Giao Hàng Nhanh?')) return;

  router.post(`/staff/orders/${props.order.id}/ghn/create`, {}, {
    preserveScroll: true, // Giữ vị trí cuộn trang
    preserveState: true,  // Giữ trạng thái các biến khác
    onStart: () => {
      creatingGHN.value = true; // Bật loading
    },
    onSuccess: (page) => {
      if (props.order) {
        // localOrder được watch từ props.order nên nó sẽ tự cập nhật giao diện
        alert('Tạo đơn GHN thành công!');
      }
    },
    onError: (errors) => {
      // Xử lý khi backend trả về lỗi validation hoặc lỗi 500
      alert(errors.error || 'Có lỗi xảy ra khi tạo đơn.');
    },
    onFinish: () => {
      creatingGHN.value = false;
    }
  });
};

// Đồng bộ trạng thái GHN (Giả lập logic, bạn cần thêm route sync nếu có)
const syncGhnStatus = async () => {
  if (!props.order.ghn_order_code) return;

  router.post(`/staff/orders/${props.order.id}/ghn/sync`, {}, {
    preserveScroll: true,
    preserveState: true,
    onStart: () => { syncingGHN.value = true; },
    onSuccess: (page) => {

    },
    onError: (errors) => {
      alert(errors.error || 'Có lỗi xảy ra khi đồng bộ trạng thái GHN.');
    },
    onFinish: () => { syncingGHN.value = false; }
  });
};

// Xử lý hủy đơn
const processCancellation = async (action) => {
  if (action === 'reject' && !Note.value.trim()) {
    alert('Vui lòng nhập ghi chú khi từ chối yêu cầu hủy.');
    return;
  }
  if (!confirm(`Bạn có chắc chắn muốn ${action === 'approve' ? 'DUYỆT' : 'TỪ CHỐI'} yêu cầu hủy?`)) return;

  processing.value = true;
  try {
    alert('Chức năng xử lý hủy cần thêm Route Backend.');
  } catch (error) {
    console.error(error);
  } finally {
    processing.value = false;
  }
};

</script>

<template>
  <Dialog v-model:visible="isVisible" modal dismissableMask :style="{ width: '800px' }"
    :header="`Chi tiết đơn hàng #${localOrder?.order_code}`" class="details-modal-modern">
    <div v-if="localOrder" class="dm-container">
      <!-- Section 1: Thông tin đơn hàng -->
      <div class="dm-section">
        <div class="dm-section-header">
          <i class="pi pi-box"></i>
          <span>Thông tin đơn hàng</span>
        </div>
        <div class="dm-section-content dm-grid-2">
          <div class="dm-item">
            <span class="dm-label">Mã đơn:</span>
            <span class="dm-value text-primary">#{{ localOrder.order_code }}</span>
          </div>
          <div class="dm-item">
            <span class="dm-label">Ngày đặt:</span>
            <span class="dm-value">{{ formatDate(localOrder.created_at) }}</span>
          </div>
          <div class="dm-item">
            <span class="dm-label">Trạng thái đơn hàng:</span>
            <span :class="['dm-badge', getStatusSeverity(localOrder.order_status)]">
              {{ getStatusLabel(localOrder.order_status) }}
            </span>
          </div>
          <div class="dm-item">
            <span class="dm-label">Thanh toán:</span>
            <span :class="['dm-badge', getPaymentStatusSeverity(localOrder.payment_status)]">
              {{ getPaymentStatusLabel(localOrder.payment_status) }}
            </span>
          </div>
        </div>
      </div>

      <!-- Section 2: Khách hàng -->
      <div class="dm-section">
        <div class="dm-section-header">
          <i class="pi pi-user"></i>
          <span>Khách hàng</span>
        </div>
        <div class="dm-section-content dm-grid-3">
          <div class="dm-item">
            <span class="dm-label">Họ tên:</span>
            <span class="dm-value">{{ localOrder.customer_name }}</span>
          </div>
          <div class="dm-item">
            <span class="dm-label">SĐT:</span>
            <span class="dm-value">{{ localOrder.customer_phone }}</span>
          </div>
          <div class="dm-item">
            <span class="dm-label">Email:</span>
            <span class="dm-value">{{ localOrder.customer_email || '---' }}</span>
          </div>
        </div>
      </div>

      <!-- Section 3: Thanh toán & Vận chuyển -->
      <div class="dm-section">
        <div class="dm-section-header">
          <i class="pi pi-wallet"></i>
          <span>Thanh toán & Vận chuyển</span>
        </div>
        <div class="dm-section-content dm-grid-2">
          <div class="dm-item">
            <span class="dm-label">Hình thức TT:</span>
            <span class="dm-value uppercase">{{ localOrder.payment_method }}</span>
          </div>
          <div class="dm-item">
            <span class="dm-label">Vận chuyển:</span>
            <span class="dm-value">{{ localOrder.delivery_method === 'shipping' ? 'Giao hàng tận nơi' :
              'Nhận tại cửa hàng' }}</span>
          </div>
        </div>
      </div>

      <!-- Section 4: Giao Hàng Nhanh -->
      <div v-if="localOrder.delivery_method === 'shipping'" class="dm-section">
        <div class="dm-section-header">
          <i class="pi pi-truck"></i>
          <span>Giao Hàng Nhanh</span>
        </div>

        <div v-if="localOrder.shipping_code || localOrder.ghn_order_code" class="dm-section-content dm-grid-3">
          <div class="dm-item">
            <span class="dm-label">Mã vận đơn:</span>
            <span class="dm-value text-primary">{{ localOrder.shipping_code || localOrder.ghn_order_code
            }}</span>
          </div>
          <div class="dm-item">
            <span class="dm-label">Trạng thái GHN:</span>
            <span class="dm-badge info">
              {{ localOrder.ghn_status || 'Đang giao' }}
            </span>
          </div>
          <div class="dm-item">
            <span class="dm-label">Phí ship:</span>
            <span class="dm-value">{{ formatCurrency(localOrder.ghn_fee || 29001) }}</span>
          </div>
        </div>
        <div v-else class="dm-no-ghn">
          <div class="text-sm text-gray-500 italic">Chưa tạo đơn vận chuyển</div>
        </div>
      </div>

      <!-- Product Table -->
      <div class="dm-table-section">
        <DataTable :value="localOrder.items" class="dm-datatable">
          <Column header="STT" class="text-center w-12">
            <template #body="slotProps">{{ slotProps.index + 1 }}</template>
          </Column>
          <Column field="product_name" header="Sản phẩm"></Column>
          <Column field="quantity" header="SL" class="text-center"></Column>
          <Column field="price" header="Đơn giá" class="text-right">
            <template #body="slotProps">{{ formatCurrency(slotProps.data.price) }}</template>
          </Column>
          <Column header="Thành tiền" class="text-right">
            <template #body="slotProps">
              <span class="font-bold">{{ formatCurrency(slotProps.data.price * slotProps.data.quantity)
              }}</span>
            </template>
          </Column>
        </DataTable>

        <div class="dm-total-row">
          <span class="dm-label-total">Tổng tiền hàng:</span>
          <span class="dm-total-value">{{ formatCurrency(localOrder.total_amount) }}</span>
        </div>
      </div>

      <!-- Cancellation Note (Now inside content area, styled like image) -->
      <div v-if="localOrder.cancellation_status" class="dm-cancellation-box">
        <div class="dm-cancellation-header">
          <div class="dm-cancellation-title">
            <i class="pi pi-exclamation-circle"></i> Yêu cầu hủy đơn
          </div>
          <span class="dm-badge warning">Chờ xử lý</span>
        </div>
        <div class="dm-cancellation-content">
          <div class="dm-item">
            <span class="dm-label">Lý do:</span>
            <span class="dm-value font-bold">{{ localOrder.cancellation_reason }}</span>
          </div>
          <div class="dm-item">
            <span class="dm-label">Ghi chú khách:</span>
            <span class="dm-value italic">{{ localOrder.cancellation_user_note }}</span>
          </div>
          <div class="dm-item">
            <span class="dm-label">Ngày yêu cầu:</span>
            <span class="dm-value">{{ formatDate(localOrder.cancellation_requested_at) }}</span>
          </div>
        </div>
      </div>

    </div>

    <template #footer>
      <div class="dm-footer-wrapper">
        <div v-if="localOrder?.cancellation_status === 'requested'" class="dm-footer-note">
          <Textarea v-model="Note" rows="2" style="width: 100%;height: 100%;"
            placeholder="Ghi chú phản hồi cho khách (bắt buộc khi từ chối)..." class="w-full dm-input-textarea" />
        </div>

        <div class="dm-footer-actions">
          <Button label="Đóng" icon="pi pi-times" text class="dm-btn-close" @click="emit('close')" />

          <template v-if="localOrder?.cancellation_status === 'requested'">
            <Button label="Từ chối hủy" icon="pi pi-ban" class="dm-btn-reject" :loading="processing"
              @click="processCancellation('reject')" />
            <Button label="Xác nhận hủy" icon="pi pi-check-circle" class="dm-btn-approve" :loading="processing"
              @click="processCancellation('approve')" />
          </template>

          <Button label="In Hóa Đơn" icon="pi pi-print" class="dm-btn-print" @click="printInvoice" />

          <template v-if="localOrder?.delivery_method === 'shipping'">
            <Button v-if="!localOrder.shipping_code && !localOrder.ghn_order_code" label="Tạo đơn GHN" icon="pi pi-send"
              :loading="creatingGHN" @click="createGHNOrder"
              :disabled="!localOrder.district_id && !localOrder.ward_code" class="dm-btn-ghn-create" />
            <Button v-else-if="localOrder.ghn_order_code" label="Đồng bộ GHN" icon="pi pi-sync" class="dm-btn-sync"
              :loading="syncingGHN" @click="syncGhnStatus" />
            <Button v-else label="In Vận Đơn GHN" icon="pi pi-file-pdf" class="dm-btn-ghn-print" as="a"
              :href="`/staff/orders/${localOrder.id}/ghn/print`" target="_blank" />
          </template>
        </div>
      </div>
    </template>
  </Dialog>
</template>

<style scoped>
/* Không cần CSS Animation thủ công nữa vì PrimeVue Dialog đã tự xử lý rất mượt */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #e2e8f0;
  border-radius: 20px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: #cbd5e1;
}
</style>
