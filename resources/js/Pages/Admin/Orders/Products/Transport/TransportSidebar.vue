<template>
  <aside class="sidebar">
    <h2 class="sidebar-title">Vận đơn</h2>

    <div class="filter-group">
      <label>Trạng thái giao hàng</label>
        <MultiSelect 
        v-model="localFilters.status" 
        :options="statusOptions" 
        optionLabel="label" 
        optionValue="value" 
        placeholder="Chọn trạng thái" 
        display="chip" 
        :maxSelectedLabels="1"
        class="w-full custom-multiselect"
        @change="emitChange"
      />
    </div>

    <div class="filter-group">
      <label>Đối tác giao hàng</label>
      <select v-model="localFilters.partner" @change="emitChange" class="input-control">
        <option value="">Chọn đối tác</option>
        <option value="ghn">Giao Hàng Nhanh (GHN)</option>
        <option value="ghtk">Giao Hàng Tiết Kiệm (GHTK)</option>
        <option value="viettel">Viettel Post</option>
      </select>
    </div>
  </aside>
</template>

<script>
import MultiSelect from 'primevue/multiselect';
export default {
  name: 'TransportSidebar',
  components : {
    MultiSelect
  },
  props: {
    filters: {
      type: Object,
      default: () => ({ status: [], partner: '' }) 
    }
  },
  data() {
    return {
      localFilters: { ...this.filters },
      // 2. Khai báo danh sách tùy chọn cho trạng thái
      statusOptions: [
        { label: 'Đang giao (Gồm chờ lấy, luân chuyển...)', value: 'delivering' },
        { label: 'Đã giao thành công', value: 'completed' },
        { label: 'Đã hủy / Trả hàng', value: 'cancelled' },
      ]
    };
  },
  watch: {
    // 3. Theo dõi props để cập nhật localFilters và đảm bảo status là mảng
    filters: {
      handler(newVal) {
        this.localFilters = { ...newVal };
        // MultiSelect bắt buộc value phải là Array. Nếu null/undefined/string thì convert sang []
        if (!Array.isArray(this.localFilters.status)) {
          this.localFilters.status = [];
        }
      },
      deep: true,
      immediate: true
    }
  },
  methods: {
    emitChange() {
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