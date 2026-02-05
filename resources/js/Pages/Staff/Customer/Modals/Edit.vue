<template>
  <Dialog :visible="visible" @update:visible="handleClose" :modal="true" :closable="true"
    :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :style="{ width: '800px', maxWidth: '100vw' }"
    class="customer-modal" :contentStyle="{ overflow: 'auto' }">

    <!-- Custom Header -->
    <template #header>
      <div class="modal-header-custom">
        <div class="header-icon-box">
          <i class="pi pi-user-edit"></i>
        </div>
        <div class="header-text-content">
          <h3 class="modal-title">Cập nhật thông tin khách hàng</h3>
          <p class="modal-subtitle">Chỉnh sửa thông tin tài khoản khách hàng</p>
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
              <InputText v-model="form.name" placeholder="Nhập tên khách hàng" class="field-input has-icon"
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
            <label class="field-label">Mật khẩu mới <small class="text-muted"
                style="font-weight: normal; color: #6b7280;">(Bỏ trống nếu không đổi)</small></label>
            <div class="input-inner-wrapper">
              <i class="pi pi-lock input-inner-icon"></i>
              <InputText v-model="form.password" type="password" placeholder="Nhập mật khẩu mới"
                class="field-input has-icon" :class="{ 'p-invalid': form.errors.password }" />
            </div>
            <small v-if="form.errors.password" class="p-error">{{ form.errors.password }}</small>
          </div>

          <div class="form-field">
            <label class="field-label">Xác nhận mật khẩu</label>
            <div class="input-inner-wrapper">
              <i class="pi pi-lock input-inner-icon"></i>
              <InputText v-model="form.password_confirmation" type="password" placeholder="Nhập lại mật khẩu mới"
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
              <InputText v-model="form.phone" type="tel" placeholder="Nhập số điện thoại" class="field-input has-icon"
                :class="{ 'p-invalid': form.errors.phone }" />
            </div>
            <small v-if="form.errors.phone" class="p-error">{{ form.errors.phone }}</small>
          </div>

          <div class="form-field">
            <label class="field-label">Địa chỉ đường/Số nhà</label>
            <div class="input-inner-wrapper">
              <i class="pi pi-map-marker input-inner-icon"></i>
              <InputText v-model="form.address" placeholder="Nhập địa chỉ cụ thể" class="field-input has-icon"
                :class="{ 'p-invalid': form.errors.address }" />
            </div>
            <small v-if="form.errors.address" class="p-error">{{ form.errors.address }}</small>
          </div>
        </div>
      </div>

      <!-- Section 4: Area -->
      <div class="form-section">
        <div class="section-title"><i class="pi pi-map mr-2"></i> Khu vực <span
            style="font-weight: normal; font-size: 13px; color: #6b7280; margin-left: 4px;">(Cập nhật)</span></div>
        <div class="form-grid-3">
          <div class="form-field">
            <label class="field-label">Tỉnh/Thành phố</label>
            <Dropdown v-model="form.province" :options="provinceOptions" optionLabel="name" placeholder="Chọn Tỉnh"
              class="field-input w-full dropdown-fix" filter showClear @change="onProvinceChange"
              :class="{ 'p-invalid': form.errors.province }" />
            <small v-if="form.errors.province" class="p-error">{{ form.errors.province }}</small>
          </div>
          <div class="form-field">
            <label class="field-label">Quận/Huyện</label>
            <Dropdown v-model="form.district" :options="districtOptions" optionLabel="name" placeholder="Chọn Quận"
              class="field-input w-full dropdown-fix" :disabled="!form.province" filter showClear
              @change="onDistrictChange" :class="{ 'p-invalid': form.errors.district }" />
            <small v-if="form.errors.district" class="p-error">{{ form.errors.district }}</small>
          </div>
          <div class="form-field">
            <label class="field-label">Xã/Phường</label>
            <Dropdown v-model="form.ward" :options="wardOptions" optionLabel="name" placeholder="Chọn Xã"
              class="field-input w-full dropdown-fix" :disabled="!form.district" filter showClear
              :class="{ 'p-invalid': form.errors.ward }" />
            <small v-if="form.errors.ward" class="p-error">{{ form.errors.ward }}</small>
          </div>
        </div>
      </div>
    </div>

    <template #footer>
      <div class="custom-footer">
        <Button label="Hủy" icon="pi pi-times" class="btn-action btn-cancel" @click="handleClose" />
        <Button label="Lưu thay đổi" icon="pi pi-check" class="btn-action btn-save" @click="submit"
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

const props = defineProps({
  visible: Boolean,
  customer: Object
});
const emit = defineEmits(['close']);

// 1. Khởi tạo Form
const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  phone: '',
  address: '',
  province: null, // Object {name, code}
  district: null,
  ward: null,
});

// 2. Data cho Address API
const provinceOptions = ref([]);
const districtOptions = ref([]);
const wardOptions = ref([]);

// 3. Helper Fetch API
const fetchApi = async (url) => {
  try {
    const res = await fetch(url);
    if (!res.ok) throw new Error(res.status);
    return await res.json();
  } catch (e) {
    // Fallback http nếu https lỗi (CORS/Mixed content)
    const httpUrl = url.replace('https://', 'http://');
    try { return await (await fetch(httpUrl)).json(); } catch (err) { return []; }
  }
};

// 4. Logic Load & Fill Data (Quan trọng)
const initFormData = async () => {
  if (!props.customer) return;

  // Reset errors cũ
  form.clearErrors();

  // Fill thông tin cơ bản
  form.name = props.customer.name;
  form.email = props.customer.email;
  form.phone = props.customer.phone;
  form.address = props.customer.address;
  form.password = ''; // Luôn trống khi edit
  form.password_confirmation = '';

  // Load Tỉnh
  if (provinceOptions.value.length === 0) {
    const pData = await fetchApi('https://provinces.open-api.vn/api/?depth=1');
    provinceOptions.value = pData.map(p => ({ name: p.name, code: p.code }));
  }

  // Set Tỉnh hiện tại
  const currentProvince = provinceOptions.value.find(p => p.name === props.customer.province);
  form.province = currentProvince || null;

  // Nếu có Tỉnh, Load Huyện
  if (currentProvince) {
    const dData = await fetchApi(`https://provinces.open-api.vn/api/p/${currentProvince.code}?depth=2`);
    districtOptions.value = dData.districts.map(d => ({ name: d.name, code: d.code }));

    // Set Huyện hiện tại
    const currentDistrict = districtOptions.value.find(d => d.name === props.customer.district);
    form.district = currentDistrict || null;

    // Nếu có Huyện, Load Xã
    if (currentDistrict) {
      const wData = await fetchApi(`https://provinces.open-api.vn/api/d/${currentDistrict.code}?depth=2`);
      wardOptions.value = wData.wards.map(w => ({ name: w.name, code: w.code }));

      // Set Xã hiện tại
      const currentWard = wardOptions.value.find(w => w.name === props.customer.ward);
      form.ward = currentWard || null;
    } else {
      wardOptions.value = [];
      form.ward = null;
    }
  } else {
    districtOptions.value = [];
    wardOptions.value = [];
    form.district = null;
    form.ward = null;
  }
};

watch(() => props.visible, (newVal) => {
  if (newVal) {
    initFormData();
  }
});

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

const submit = () => {
  form.transform((data) => ({
    ...data,
    province: data.province?.name || null,
    district: data.district?.name || null,
    ward: data.ward?.name || null,
  }))
    .put(`/staff/customers/${props.customer.id}`, {
      onSuccess: () => {
        handleClose();
      },
    });
};

const handleClose = () => {
  emit('close');
  form.reset();
  form.clearErrors();
};
</script>

<style scoped>
@import url('../../../../../css/Staff/customer/modals.css');
</style>