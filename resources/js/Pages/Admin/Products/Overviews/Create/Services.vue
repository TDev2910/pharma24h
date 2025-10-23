<template>
  <div v-if="visible" class="modal-overlay" @click="$emit('close')">
    <div class="modal-content" @click.stop>
      <h3>Tạo dịch vụ mới</h3>
      <p>Form tạo dịch vụ sẽ được thêm sau</p>
      <button @click="$emit('close')">Đóng</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CreateService',
  props: {
    visible: {
      type: Boolean,
      default: false
    }
  },
  emits: ['close']
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  padding: 20px;
  border-radius: 8px;
  max-width: 500px;
  width: 90%;
}
</style>