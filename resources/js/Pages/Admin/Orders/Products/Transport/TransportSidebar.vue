<template>
  <aside class="sidebar">
    <h2 class="sidebar-title">Vận đơn</h2>

    <div class="filter-group">
      <label>Trạng thái giao hàng</label>
      <select v-model="localFilters.status" @change="emitChange" class="input-control">
        <option value="">Chọn trạng thái</option>
        <option value="delivering">Đang giao</option>
        <option value="completed">Đã giao</option>
        <option value="cancelled">Đã hủy</option>
      </select>
    </div>

    <div class="filter-group">
      <label>Đối tác giao hàng</label>
      <select v-model="localFilters.partner" @change="emitChange" class="input-control">
        <option value="">Chọn đối tác</option>
        <option value="ghn">Giao Hàng Nhanh (GHN)</option>
      </select>
    </div>
  </aside>
</template>

<script>
export default {
  name: 'TransportSidebar',
  props: {
    filters: {
      type: Object,
      default: () => ({ status: '', partner: '' })
    }
  },
  data() {
    return {
      // Copy props ra data local để tránh sửa trực tiếp props
      localFilters: { ...this.filters }
    };
  },
  methods: {
    emitChange() {
      // Gửi sự kiện lên cha
      this.$emit('update:filters', this.localFilters);
    },
  }
};
</script>

<style scoped>
.sidebar {
  width: 280px;
  background: white;
  padding: 20px;
  border-right: 1px solid #e0e0e0;
  display: flex;
  flex-direction: column;
  gap: 20px;
  height: 100%;
}

.sidebar-title {
  font-size: 18px;
  font-weight: bold;
  margin-bottom: 10px;
  color: #333;
}

.filter-group label {
  display: block;
  font-weight: 600;
  font-size: 14px;
  margin-bottom: 8px;
  color: #555;
}

.input-control {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 6px;
  outline: none;
}

.toggle-group {
  display: flex;
  background: #f0f0f0;
  padding: 2px;
  border-radius: 20px;
}

.toggle-group button {
  flex: 1;
  border: none;
  background: transparent;
  padding: 6px;
  border-radius: 18px;
  cursor: pointer;
  font-size: 13px;
  transition: 0.2s;
}

.toggle-group button.active {
  background: #007bff;
  color: white;
  font-weight: bold;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
</style>