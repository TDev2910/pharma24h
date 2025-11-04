<template>
  <Dialog 
    v-model:visible="dialogVisible" 
    header="Lập lịch làm việc" 
    :modal="true"
    :style="{ width: '600px' }"
  >
    <div class="schedule-form">
      <div class="employee-info">
        <h5>Nhân viên: <strong>{{ employee?.full_name }}</strong></h5>
        <p>Mã NV: {{ employee?.employee_code }}</p>
      </div>

      <div class="form-group">
        <label>Ca làm việc <span class="required">*</span></label>
        <Dropdown 
          v-model="form.shift_id" 
          :options="shifts" 
          optionLabel="name" 
          optionValue="id"
          placeholder="Chọn ca làm việc"
          :loading="loadingShifts"
        >
          <template #option="slotProps">
            <div>
              <strong>{{ slotProps.option.name }}</strong>
              <div style="font-size: 12px; color: #666;">
                {{ slotProps.option.start_time }} - {{ slotProps.option.end_time }}
              </div>
            </div>
          </template>
        </Dropdown>
        <small class="p-error" v-if="errors.shift_id">{{ errors.shift_id }}</small>
      </div>

      <div class="form-group">
        <label>Ngày làm việc <span class="required">*</span></label>
        <Calendar 
          v-model="form.schedule_date" 
          dateFormat="dd/mm/yy" 
          placeholder="Chọn ngày"
          :minDate="new Date()"
          showIcon
        />
        <small class="p-error" v-if="errors.schedule_date">{{ errors.schedule_date }}</small>
      </div>

      <div class="form-group">
        <label>Ghi chú</label>
        <Textarea v-model="form.notes" rows="3" placeholder="Nhập ghi chú (nếu có)" />
      </div>
    </div>

    <template #footer>
      <Button label="Hủy" icon="pi pi-times" @click="dialogVisible = false" class="p-button-text" />
      <Button label="Lưu" icon="pi pi-check" @click="submitForm" :loading="submitting" />
    </template>
  </Dialog>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import Dialog from 'primevue/dialog';
import Dropdown from 'primevue/dropdown';
import Calendar from 'primevue/calendar';
import Textarea from 'primevue/textarea';
import Button from 'primevue/button';
import axios from 'axios';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  visible: Boolean,
  employee: Object
});

const emit = defineEmits(['update:visible']);

const dialogVisible = computed({
  get: () => props.visible,
  set: (value) => emit('update:visible', value)
});

const form = ref({
  employee_id: null,
  shift_id: null,
  schedule_date: null,
  notes: ''
});

const shifts = ref([]);
const loadingShifts = ref(false);
const submitting = ref(false);
const errors = ref({});

watch(() => props.visible, async (newVal) => {
  if (newVal && props.employee) {
    form.value.employee_id = props.employee.id;
    await loadShifts();
  }
});

const loadShifts = async () => {
  loadingShifts.value = true;
  try {
    const response = await axios.get(route('admin.shifts.api'));
    shifts.value = response.data;
  } catch (error) {
    console.error('Error loading shifts:', error);
  } finally {
    loadingShifts.value = false;
  }
};

const submitForm = async () => {
  submitting.value = true;
  errors.value = {};

  try {
    await axios.post(route('admin.employee-schedules.store'), form.value);
    dialogVisible.value = false;
    resetForm();
    router.reload({ only: ['schedules'] });
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors;
    }
  } finally {
    submitting.value = false;
  }
};

const resetForm = () => {
  form.value = {
    employee_id: null,
    shift_id: null,
    schedule_date: null,
    notes: ''
  };
  errors.value = {};
};
</script>

<style scoped>
.schedule-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.employee-info {
  padding: 16px;
  background: #f9fafb;
  border-radius: 8px;
  border-left: 4px solid #3b82f6;
}

.employee-info h5 {
  margin: 0 0 8px 0;
  font-size: 16px;
}

.employee-info p {
  margin: 0;
  color: #666;
  font-size: 14px;
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

.p-error {
  color: #ef4444;
  font-size: 12px;
}
</style>
