<template>
  <Dialog 
    v-model:visible="dialogVisible" 
    header="Chỉnh sửa nhân viên" 
    :modal="true"
    :style="{ width: '900px' }"
    @show="loadEmployee"
  >
    <div v-if="loading" class="loading-spinner">
      <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
    </div>

    <TabView v-else>
      <!-- Tab 1: Thông tin cơ bản -->
      <TabPanel header="Thông tin cơ bản">
        <div class="form-grid">
          <div class="form-group">
            <label>Họ tên <span class="required">*</span></label>
            <InputText v-model="form.full_name" placeholder="Nhập họ tên" />
          </div>
          <div class="form-group">
            <label>Email <span class="required">*</span></label>
            <InputText v-model="form.email" type="email" placeholder="Nhập email" />
          </div>
          <div class="form-group">
            <label>Số điện thoại</label>
            <InputText v-model="form.phone_number" placeholder="Nhập số điện thoại" />
          </div>
          <div class="form-group">
            <label>Mã nhân viên</label>
            <InputText v-model="form.employee_code" placeholder="Mã nhân viên" disabled />
          </div>
          <div class="form-group">
            <label>Phòng ban</label>
            <Dropdown 
              v-model="form.department_id" 
              :options="resources.departments" 
              optionLabel="name" 
              optionValue="id"
              placeholder="Chọn phòng ban"
              showClear
            />
          </div>
          <div class="form-group">
            <label>Chức vụ</label>
            <Dropdown 
              v-model="form.position_id" 
              :options="resources.positions" 
              optionLabel="name" 
              optionValue="id"
              placeholder="Chọn chức vụ"
              showClear
            />
          </div>
          <div class="form-group">
            <label>Chi nhánh</label>
            <Dropdown 
              v-model="form.branch_id" 
              :options="resources.branches" 
              optionLabel="name" 
              optionValue="id"
              placeholder="Chọn chi nhánh"
              showClear
            />
          </div>
          <div class="form-group">
            <label>Ngày bắt đầu</label>
            <Calendar v-model="form.start_date" dateFormat="dd/mm/yy" showIcon />
          </div>
        </div>
      </TabPanel>

      <!-- Tab 2: Thiết lập lương -->
      <TabPanel header="Thiết lập lương">
        <div class="form-grid">
          <div class="form-group">
            <label>Loại lương <span class="required">*</span></label>
            <Dropdown 
              v-model="form.salary_type" 
              :options="[
                { label: 'Lương cố định', value: 'fixed' }, 
                { label: 'Theo giờ', value: 'per_hour' }
              ]" 
              optionLabel="label" 
              optionValue="value"
            />
          </div>
          <div class="form-group">
            <label>Mức lương <span class="required">*</span></label>
            <InputNumber 
              v-model="form.salary_level" 
              mode="currency" 
              currency="VND" 
              locale="vi-VN"
            />
          </div>
        </div>
      </TabPanel>
    </TabView>

    <template #footer>
      <Button label="Hủy" icon="pi pi-times" @click="dialogVisible = false" class="p-button-text" />
      <Button label="Cập nhật" icon="pi pi-check" @click="submitForm" :loading="submitting" />
    </template>
  </Dialog>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import Dialog from 'primevue/dialog';
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Dropdown from 'primevue/dropdown';
import Calendar from 'primevue/calendar';
import Button from 'primevue/button';
import axios from 'axios';

const props = defineProps({
  visible: Boolean,
  employee: Object
});

const emit = defineEmits(['update:visible', 'updated']);

const dialogVisible = computed({
  get: () => props.visible,
  set: (value) => emit('update:visible', value)
});

const form = ref({});
const resources = ref({ departments: [], positions: [], branches: [] });
const loading = ref(false);
const submitting = ref(false);

watch(() => props.visible, async (newVal) => {
  if (newVal) {
    await loadResources();
  }
});

const loadEmployee = () => {
  if (props.employee) {
    form.value = {
      full_name: props.employee.full_name,
      email: props.employee.user?.email,
      phone_number: props.employee.phone_number,
      employee_code: props.employee.employee_code,
      department_id: props.employee.department_id,
      position_id: props.employee.position_id,
      branch_id: props.employee.branch_id,
      start_date: props.employee.start_date ? new Date(props.employee.start_date) : null,
      salary_type: props.employee.salary_type,
      salary_level: parseFloat(props.employee.salary_level)
    };
  }
};

const loadResources = async () => {
  try {
    const response = await axios.get(route('admin.employees.resources'));
    resources.value = response.data;
  } catch (error) {
    console.error('Error loading resources:', error);
  }
};

const submitForm = () => {
  submitting.value = true;
  router.put(route('admin.employees.update', props.employee.id), form.value, {
    onSuccess: () => {
      emit('updated');
    },
    onFinish: () => {
      submitting.value = false;
    }
  });
};
</script>

<style scoped>
.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  font-weight: 500;
  font-size: 14px;
}

.required {
  color: #ef4444;
}

.loading-spinner {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 300px;
}
</style>
