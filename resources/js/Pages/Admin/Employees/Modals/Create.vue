<template>
  <Dialog 
    v-model:visible="dialogVisible" 
    header="Thêm nhân viên mới" 
    :modal="true"
    :style="{ width: '900px' }"
    @hide="resetForm"
  >
    <TabView>
      <!-- Tab 1: Thông tin cơ bản -->
      <TabPanel header="Thông tin cơ bản">
        <div class="form-grid">
          <!-- Họ tên -->
          <div class="form-group">
            <label>Họ tên <span class="required">*</span></label>
            <InputText v-model="form.full_name" placeholder="Nhập họ tên" :class="{ 'p-invalid': errors.full_name }" />
            <small class="p-error" v-if="errors.full_name">{{ errors.full_name }}</small>
          </div>

          <!-- Email -->
          <div class="form-group">
            <label>Email <span class="required">*</span></label>
            <InputText v-model="form.email" type="email" placeholder="Nhập email" :class="{ 'p-invalid': errors.email }" />
            <small class="p-error" v-if="errors.email">{{ errors.email }}</small>
          </div>

          <!-- Số điện thoại -->
          <div class="form-group">
            <label>Số điện thoại</label>
            <InputText v-model="form.phone_number" placeholder="Nhập số điện thoại" />
          </div>

          <!-- Mã nhân viên -->
          <div class="form-group">
            <label>Mã nhân viên</label>
            <div style="display: flex; gap: 8px;">
              <InputText v-model="form.employee_code" placeholder="Tự động tạo" style="flex: 1" />
              <Button icon="pi pi-refresh" @click="generateCode" :loading="generating" title="Tạo mã tự động" />
            </div>
          </div>

          <!-- Phòng ban -->
          <div class="form-group">
            <label>Phòng ban</label>
            <Dropdown 
              v-model="form.department_id" 
              :options="resources.departments" 
              optionLabel="name" 
              optionValue="id"
              placeholder="Chọn phòng ban"
              :filter="true"
              showClear
            />
          </div>

          <!-- Chức vụ -->
          <div class="form-group">
            <label>Chức vụ</label>
            <Dropdown 
              v-model="form.position_id" 
              :options="resources.positions" 
              optionLabel="name" 
              optionValue="id"
              placeholder="Chọn chức vụ"
              :filter="true"
              showClear
            />
          </div>

          <!-- Chi nhánh -->
          <div class="form-group">
            <label>Chi nhánh</label>
            <Dropdown 
              v-model="form.branch_id" 
              :options="resources.branches" 
              optionLabel="name" 
              optionValue="id"
              placeholder="Chọn chi nhánh"
              :filter="true"
              showClear
            />
          </div>

          <!-- Ngày bắt đầu -->
          <div class="form-group">
            <label>Ngày bắt đầu làm việc</label>
            <Calendar v-model="form.start_date" dateFormat="dd/mm/yy" placeholder="Chọn ngày" showIcon />
          </div>

          <!-- Ngày sinh -->
          <div class="form-group">
            <label>Ngày sinh</label>
            <Calendar v-model="form.dob" dateFormat="dd/mm/yy" placeholder="Chọn ngày" showIcon />
          </div>

          <!-- Giới tính -->
          <div class="form-group">
            <label>Giới tính</label>
            <Dropdown 
              v-model="form.gender" 
              :options="[{ label: 'Nam', value: 'nam' }, { label: 'Nữ', value: 'nữ' }]" 
              optionLabel="label" 
              optionValue="value"
              placeholder="Chọn giới tính"
              showClear
            />
          </div>

          <!-- CMND/CCCD -->
          <div class="form-group">
            <label>CMND/CCCD</label>
            <InputText v-model="form.id_card_number" placeholder="Nhập số CMND/CCCD" />
          </div>

          <!-- Địa chỉ -->
          <div class="form-group full-width">
            <label>Địa chỉ</label>
            <Textarea v-model="form.address" rows="3" placeholder="Nhập địa chỉ" />
          </div>
        </div>
      </TabPanel>

      <!-- Tab 2: Thiết lập lương -->
      <TabPanel header="Thiết lập lương">
        <div class="form-grid">
          <!-- Loại lương -->
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
              placeholder="Chọn loại lương"
            />
          </div>

          <!-- Mức lương -->
          <div class="form-group">
            <label>Mức lương cơ bản <span class="required">*</span></label>
            <InputNumber 
              v-model="form.salary_level" 
              mode="currency" 
              currency="VND" 
              locale="vi-VN"
              placeholder="Nhập mức lương"
              :class="{ 'p-invalid': errors.salary_level }"
            />
            <small class="p-error" v-if="errors.salary_level">{{ errors.salary_level }}</small>
          </div>
        </div>

        <!-- Phụ cấp -->
        <div class="dynamic-section">
          <div class="section-header">
            <h5>Phụ cấp</h5>
            <InputSwitch v-model="form.enable_allowance" />
          </div>

          <div v-if="form.enable_allowance" class="dynamic-list">
            <div v-for="(allowance, index) in form.allowances" :key="index" class="dynamic-item">
              <div class="dynamic-item-fields">
                <InputText v-model="allowance.name" placeholder="Tên phụ cấp" style="flex: 2" />
                <InputNumber v-model="allowance.amount" mode="currency" currency="VND" locale="vi-VN" placeholder="Số tiền" style="flex: 2" />
                <Dropdown 
                  v-model="allowance.type" 
                  :options="[
                    { label: 'Cố định/Ngày', value: 'fixed_daily' },
                    { label: 'Cố định/Tháng', value: 'fixed_monthly' },
                    { label: '% Lương', value: 'percent_salary' }
                  ]"
                  optionLabel="label"
                  optionValue="value"
                  placeholder="Loại"
                  style="flex: 2"
                />
                <Button icon="pi pi-trash" class="p-button-danger p-button-text" @click="removeAllowance(index)" />
              </div>
            </div>
            <Button label="Thêm phụ cấp" icon="pi pi-plus" class="p-button-sm" @click="addAllowance" />
          </div>
        </div>

        <!-- Thưởng chỉ tiêu -->
        <div class="dynamic-section">
          <div class="section-header">
            <h5>Thưởng chỉ tiêu</h5>
            <InputSwitch v-model="form.enable_target_bonus" />
          </div>

          <div v-if="form.enable_target_bonus" class="dynamic-list">
            <div v-for="(target, index) in form.targets" :key="index" class="dynamic-item">
              <div class="dynamic-item-fields">
                <InputText v-model="target.activity_type" placeholder="Loại hoạt động" style="flex: 2" />
                <InputNumber v-model="target.target_amount" mode="currency" currency="VND" locale="vi-VN" placeholder="Chỉ tiêu (X)" style="flex: 2" />
                <Dropdown 
                  v-model="target.bonus_type" 
                  :options="[
                    { label: 'Cố định', value: 'fixed' },
                    { label: '% Doanh thu', value: 'percent' }
                  ]"
                  optionLabel="label"
                  optionValue="value"
                  placeholder="Loại thưởng"
                  style="flex: 1"
                />
                <InputNumber v-model="target.bonus_value" placeholder="Giá trị thưởng (Y)" style="flex: 2" />
                <Button icon="pi pi-trash" class="p-button-danger p-button-text" @click="removeTarget(index)" />
              </div>
            </div>
            <Button label="Thêm chỉ tiêu" icon="pi pi-plus" class="p-button-sm" @click="addTarget" />
          </div>
        </div>

        <!-- Giảm trừ -->
        <div class="dynamic-section">
          <div class="section-header">
            <h5>Giảm trừ</h5>
            <InputSwitch v-model="form.enable_deduction" />
          </div>

          <div v-if="form.enable_deduction" class="dynamic-list">
            <div v-for="(deduction, index) in form.deductions" :key="index" class="dynamic-item">
              <div class="dynamic-item-fields">
                <InputText v-model="deduction.reason" placeholder="Lý do" style="flex: 2" />
                <InputNumber v-model="deduction.amount" mode="currency" currency="VND" locale="vi-VN" placeholder="Số tiền" style="flex: 2" />
                <Dropdown 
                  v-model="deduction.frequency" 
                  :options="[
                    { label: 'Một lần', value: 'one-time' },
                    { label: 'Hàng tháng', value: 'monthly' }
                  ]"
                  optionLabel="label"
                  optionValue="value"
                  placeholder="Tần suất"
                  style="flex: 2"
                />
                <Button icon="pi pi-trash" class="p-button-danger p-button-text" @click="removeDeduction(index)" />
              </div>
            </div>
            <Button label="Thêm giảm trừ" icon="pi pi-plus" class="p-button-sm" @click="addDeduction" />
          </div>
        </div>
      </TabPanel>
    </TabView>

    <template #footer>
      <Button label="Hủy" icon="pi pi-times" @click="dialogVisible = false" class="p-button-text" />
      <Button label="Lưu" icon="pi pi-check" @click="submitForm" :loading="submitting" />
    </template>
  </Dialog>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Dialog from 'primevue/dialog';
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Textarea from 'primevue/textarea';
import Dropdown from 'primevue/dropdown';
import Calendar from 'primevue/calendar';
import InputSwitch from 'primevue/inputswitch';
import Button from 'primevue/button';
import axios from 'axios';

const props = defineProps({
  visible: Boolean
});

const emit = defineEmits(['update:visible', 'created']);

const dialogVisible = computed({
  get: () => props.visible,
  set: (value) => emit('update:visible', value)
});

// State
const form = ref({
  full_name: '',
  email: '',
  phone_number: '',
  employee_code: '',
  department_id: null,
  position_id: null,
  branch_id: null,
  start_date: null,
  dob: null,
  gender: null,
  address: '',
  id_card_number: '',
  salary_type: 'fixed',
  salary_level: 0,
  enable_allowance: false,
  enable_target_bonus: false,
  enable_deduction: false,
  allowances: [],
  targets: [],
  deductions: []
});

const resources = ref({
  departments: [],
  positions: [],
  branches: []
});

const errors = ref({});
const submitting = ref(false);
const generating = ref(false);

// Load resources khi mở modal
watch(() => props.visible, async (newVal) => {
  if (newVal) {
    await loadResources();
  }
});

const loadResources = async () => {
  try {
    const response = await axios.get(route('admin.employees.resources'));
    resources.value = response.data;
  } catch (error) {
    console.error('Error loading resources:', error);
  }
};

const generateCode = async () => {
  generating.value = true;
  try {
    const response = await axios.get(route('admin.employees.generate-code'));
    form.value.employee_code = response.data.employee_code;
  } catch (error) {
    console.error('Error generating code:', error);
  } finally {
    generating.value = false;
  }
};

// Dynamic arrays management
const addAllowance = () => {
  form.value.allowances.push({ name: '', amount: 0, type: 'fixed_monthly' });
};

const removeAllowance = (index) => {
  form.value.allowances.splice(index, 1);
};

const addTarget = () => {
  form.value.targets.push({ activity_type: '', target_amount: 0, bonus_type: 'fixed', bonus_value: 0 });
};

const removeTarget = (index) => {
  form.value.targets.splice(index, 1);
};

const addDeduction = () => {
  form.value.deductions.push({ reason: '', amount: 0, frequency: 'monthly' });
};

const removeDeduction = (index) => {
  form.value.deductions.splice(index, 1);
};

const submitForm = async () => {
  submitting.value = true;
  errors.value = {};

  try {
    await axios.post(route('admin.employees.store'), form.value);
    emit('created');
    resetForm();
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
    full_name: '',
    email: '',
    phone_number: '',
    employee_code: '',
    department_id: null,
    position_id: null,
    branch_id: null,
    start_date: null,
    dob: null,
    gender: null,
    address: '',
    id_card_number: '',
    salary_type: 'fixed',
    salary_level: 0,
    enable_allowance: false,
    enable_target_bonus: false,
    enable_deduction: false,
    allowances: [],
    targets: [],
    deductions: []
  };
  errors.value = {};
};
</script>

<style scoped>
.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
  margin-bottom: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-group label {
  font-weight: 500;
  font-size: 14px;
  color: #374151;
}

.required {
  color: #ef4444;
}

.dynamic-section {
  margin-bottom: 24px;
  padding: 16px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.section-header h5 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  color: #111827;
}

.dynamic-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.dynamic-item {
  padding: 12px;
  background: #f9fafb;
  border-radius: 6px;
}

.dynamic-item-fields {
  display: flex;
  gap: 8px;
  align-items: center;
}

.p-error {
  color: #ef4444;
  font-size: 12px;
}
</style>
