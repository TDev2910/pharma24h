<template>
  <Dialog :visible="visible" @update:visible="handleClose" :modal="true" :closable="true"
    :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :style="{ width: '800px', maxWidth: '100vw' }"
    class="customer-modal" :contentStyle="{ overflow: 'auto' }">

    <!-- Custom Header -->
    <template #header>
      <div class="modal-header-custom">
        <div class="header-icon-box">
          <i class="pi pi-user"></i>
        </div>
        <div class="header-text-content">
          <h3 class="modal-title">Thêm khách hàng mới</h3>
          <p class="modal-subtitle">Điền thông tin để tạo tài khoản khách hàng</p>
        </div>
      </div>
    </template>

    <div class="modal-content-body">
      <!-- Section 1: Basic Info -->
      <div class="form-section">
        <div class="section-title"><i class="pi pi-user mr-2"></i> Thông tin cơ bản</div>
        <div class="form-grid-2">
          <div class="form-field">
            <label class="field-label">Tên khách hàng <span class="required">*</span></label>
            <div class="input-inner-wrapper">
              <i class="pi pi-user input-inner-icon"></i>
              <InputText v-model="form.name" placeholder="Nhập họ và tên" class="field-input has-icon"
                :class="{ 'p-invalid': form.errors.name }" />
            </div>
            <small v-if="form.errors.name" class="p-error">{{ form.errors.name }}</small>
          </div>

          <div class="form-field">
            <label class="field-label">Email <span class="required">*</span></label>
            <div class="input-inner-wrapper">
              <i class="pi pi-envelope input-inner-icon"></i>
              <InputText v-model="form.email" placeholder="email@example.com" class="field-input has-icon"
                :class="{ 'p-invalid': form.errors.email }" />
            </div>
            <small v-if="form.errors.email" class="p-error">{{ form.errors.email }}</small>
          </div>
        </div>
      </div>

      <!-- Section 2: Security -->
      <div class="form-section">
        <div class="section-title"><i class="pi pi-lock mr-2"></i> Bảo mật</div>
        <div class="form-grid-2">
          <div class="form-field">
            <label class="field-label">Mật khẩu <span class="required">*</span></label>
            <div class="input-inner-wrapper">
              <i class="pi pi-lock input-inner-icon"></i>
              <InputText v-model="form.password" type="password" placeholder="Nhập mật khẩu"
                class="field-input has-icon" :class="{ 'p-invalid': form.errors.password }" />
            </div>
            <small v-if="form.errors.password" class="p-error">{{ form.errors.password }}</small>
          </div>

          <div class="form-field">
            <label class="field-label">Xác nhận mật khẩu <span class="required">*</span></label>
            <div class="input-inner-wrapper">
              <i class="pi pi-lock input-inner-icon"></i>
              <InputText v-model="form.password_confirmation" type="password" placeholder="Nhập lại mật khẩu"
                class="field-input has-icon" />
            </div>
          </div>
        </div>
      </div>

      <!-- Section 3: Contact Info -->
      <div class="form-section">
        <div class="section-title"><i class="pi pi-phone mr-2"></i> Thông tin liên hệ</div>
        <div class="form-grid-2">
          <div class="form-field">
            <label class="field-label">Số điện thoại</label>
            <div class="input-inner-wrapper">
              <i class="pi pi-phone input-inner-icon"></i>
              <InputText v-model="form.phone" placeholder="0901234567" class="field-input has-icon"
                :class="{ 'p-invalid': form.errors.phone }" />
            </div>
            <small v-if="form.errors.phone" class="p-error">{{ form.errors.phone }}</small>
          </div>

          <div class="form-field">
            <label class="field-label">Địa chỉ đường/Số nhà</label>
            <div class="input-inner-wrapper">
              <i class="pi pi-map-marker input-inner-icon"></i>
              <InputText v-model="form.address" placeholder="Ví dụ: 123 Nguyễn Huệ" class="field-input has-icon" />
            </div>
          </div>
        </div>
      </div>

      <!-- Section 4: Area -->
      <div class="form-section">
        <div class="section-title"><i class="pi pi-map mr-2"></i> Khu vực <span
            style="font-weight: normal; font-size: 13px; color: #6b7280; margin-left: 4px;">(Tùy chọn)</span></div>
        <div class="form-grid-3">
          <div class="form-field">
            <label class="field-label">Tỉnh/Thành phố</label>
            <Dropdown v-model="form.province" :options="provinceOptions" optionLabel="name"
              placeholder="Chọn Tỉnh/Thành" class="field-input w-full dropdown-fix" filter showClear
              @change="onProvinceChange" />
          </div>
          <div class="form-field">
            <label class="field-label">Quận/Huyện</label>
            <Dropdown v-model="form.district" :options="districtOptions" optionLabel="name"
              placeholder="Chọn Quận/Huyện" class="field-input w-full dropdown-fix" :disabled="!form.province" filter
              showClear @change="onDistrictChange" />
          </div>
          <div class="form-field">
            <label class="field-label">Xã/Phường</label>
            <Dropdown v-model="form.ward" :options="wardOptions" optionLabel="name" placeholder="Chọn Xã/Phường"
              class="field-input w-full dropdown-fix" :disabled="!form.district" filter showClear />
          </div>
        </div>
      </div>
    </div>

    <template #footer>
      <div class="custom-footer">
        <Button label="Hủy" icon="pi pi-times" class="btn-action btn-cancel" @click="handleClose" />
        <Button label="Lưu khách hàng" icon="pi pi-check" class="btn-action btn-save" @click="submit"
          :loading="form.processing" />
      </div>
    </template>
  </Dialog>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';

const props = defineProps({ visible: Boolean });
const emit = defineEmits(['close']);

// 1. Khởi tạo Form chuẩn Inertia
const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  phone: '',
  address: '',
  province: null,
  district: null,
  ward: null,
});

// 2. Logic gọi API Địa chỉ (Client Side)
const provinceOptions = ref([]);
const districtOptions = ref([]);
const wardOptions = ref([]);

const fetchApi = async (url) => {
  try {
    const res = await fetch(url);
    return await res.json();
  } catch (e) { console.error(e); return []; }
};

const loadProvinces = async () => {
  if (provinceOptions.value.length) return;
  const data = await fetchApi('https://provinces.open-api.vn/api/?depth=1');
  provinceOptions.value = data.map(p => ({ name: p.name, code: p.code }));
};

const onProvinceChange = async () => {
  districtOptions.value = []; wardOptions.value = [];
  form.district = null; form.ward = null;
  if (form.province?.code) {
    const data = await fetchApi(`https://provinces.open-api.vn/api/p/${form.province.code}?depth=2`);
    districtOptions.value = data.districts.map(d => ({ name: d.name, code: d.code }));
  }
};

const onDistrictChange = async () => {
  wardOptions.value = []; form.ward = null;
  if (form.district?.code) {
    const data = await fetchApi(`https://provinces.open-api.vn/api/d/${form.district.code}?depth=2`);
    wardOptions.value = data.wards.map(w => ({ name: w.name, code: w.code }));
  }
};

// 3. Xử lý Submit
const submit = () => {
  form.post('/staff/customers', { // Gọi route store
    onSuccess: () => {
      form.reset();
      handleClose();
      // Không cần reload thủ công, Inertia tự lo
    },
  });
};

const handleClose = () => {
  emit('close');
  form.clearErrors();
};

watch(() => props.visible, (val) => {
  if (val) loadProvinces();
});
</script>

<style scoped>
@import url('../../../../../css/Staff/customer/modals.css');
</style>