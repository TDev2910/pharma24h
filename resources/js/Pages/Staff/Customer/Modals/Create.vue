<template>
  <Dialog
    :visible="visible"
    @update:visible="$emit('close')"
    header="Thêm khách hàng mới"
    :breakpoints="{ '960px': '75vw', '640px': '100vw' }"
    :style="{ width: '800px', maxWidth: '100vw' }"
    modal
    :maximizable="true"
    :closable="true"
    class="customer-modal mobile-fullscreen-dialog"
    :contentStyle="{ overflow: 'auto' }"
  >
    <div class="modal-content">
      <div class="form-grid">
        <div class="form-field">
          <label for="name" class="field-label">Tên khách hàng <span class="required">*</span></label>
          <InputText id="name" v-model="formData.name" type="text" placeholder="Nhập họ và tên" class="field-input"
            :class="{ 'p-invalid': errors.name }" />
          <small v-if="errors.name" class="p-error">{{ errors.name[0] }}</small>
        </div>

        <div class="form-field">
          <label for="email" class="field-label">Email <span class="required">*</span></label>
          <InputText id="email" v-model="formData.email" type="email" placeholder="email@example.com"
            class="field-input" :class="{ 'p-invalid': errors.email }" />
          <small v-if="errors.email" class="p-error">{{ errors.email[0] }}</small>
        </div>

        <div class="form-field">
          <label for="password" class="field-label">Mật khẩu <span class="required">*</span></label>
          <InputText id="password" v-model="formData.password" type="password" placeholder="Nhập mật khẩu"
            class="field-input" :class="{ 'p-invalid': errors.password }" />
          <small v-if="errors.password" class="p-error">{{ errors.password[0] }}</small>
        </div>

        <div class="form-field">
          <label for="password_confirmation" class="field-label">Xác nhận mật khẩu <span class="required">*</span></label>
          <InputText id="password_confirmation" v-model="formData.password_confirmation" type="password"
            placeholder="Nhập lại mật khẩu" class="field-input"
            :class="{ 'p-invalid': errors.password_confirmation }" />
          <small v-if="errors.password_confirmation" class="p-error">{{ errors.password_confirmation[0] }}</small>
        </div>

        <div class="form-field">
          <label for="phone" class="field-label">Số điện thoại</label>
          <InputText id="phone" v-model="formData.phone" type="tel" placeholder="0901234567" class="field-input"
            :class="{ 'p-invalid': errors.phone }" />
          <small v-if="errors.phone" class="p-error">{{ errors.phone[0] }}</small>
        </div>

        <div class="form-field">
          <label for="address" class="field-label">Địa chỉ đường/Số nhà</label>
          <InputText id="address" v-model="formData.address" type="text" placeholder="Ví dụ: 123 Nguyễn Huệ"
            class="field-input" :class="{ 'p-invalid': errors.address }" />
          <small v-if="errors.address" class="p-error">{{ errors.address[0] }}</small>
        </div>

        <div class="address-section">
          <div class="address-container">
            <div class="address-header">
              <span class="address-title"><i class="pi pi-map-marker mr-2"></i> Khu vực (Tùy chọn)</span>
            </div>

            <div class="address-row">
              <div class="form-field">
                <label for="province" class="field-label">Tỉnh/Thành phố</label>
                <Dropdown id="province" v-model="formData.province" :options="provinceOptions" optionLabel="name"
                  placeholder="Chọn Tỉnh/Thành" class="field-input w-full dropdown-fix" :class="{ 'p-invalid': errors.province }"
                  filter showClear />
                <small v-if="errors.province" class="p-error">{{ errors.province[0] }}</small>
              </div>

              <div class="form-field">
                <label for="district" class="field-label">Quận/Huyện</label>
                <Dropdown id="district" v-model="formData.district" :options="districtOptions" optionLabel="name"
                  placeholder="Chọn Quận/Huyện" class="field-input w-full dropdown-fix" :class="{ 'p-invalid': errors.district }"
                  :disabled="!formData.province" filter showClear />
                <small v-if="errors.district" class="p-error">{{ errors.district[0] }}</small>
              </div>

              <div class="form-field">
                <label for="ward" class="field-label">Xã/Phường</label>
                <Dropdown id="ward" v-model="formData.ward" :options="wardOptions" optionLabel="name"
                  placeholder="Chọn Xã/Phường" class="field-input w-full dropdown-fix" :class="{ 'p-invalid': errors.ward }"
                  :disabled="!formData.district" filter showClear />
                <small v-if="errors.ward" class="p-error">{{ errors.ward[0] }}</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <template #footer>
        <div class="custom-footer">
            <Button label="Hủy" icon="pi pi-times" class="p-button-text p-button-secondary btn-action btn-cancel"
            @click="closeModal" />
            <Button label="Lưu khách hàng" icon="pi pi-check" class="p-button-primary btn-action btn-save" @click="saveCustomer"
            :loading="loading" />
        </div>
    </template>

  </Dialog>
</template>

<script>
// Script giữ nguyên không đổi logic
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Dropdown from 'primevue/dropdown'
import axios from 'axios'

export default {
  name: 'StaffCreateCustomerModal',
  components: { Dialog, Button, InputText, Dropdown },
  props: { visible: { type: Boolean, default: false } },
  emits: ['close', 'created'],

  data() {
    return {
      loading: false,
      formData: { name: '', email: '', password: '', password_confirmation: '', phone: '', address: '', province: null, district: null, ward: null },
      errors: {},
      provinceOptions: [], districtOptions: [], wardOptions: []
    }
  },

  mounted() { if (this.visible) this.loadProvinces() },
  watch: {
    visible(newVal) { if (newVal) this.loadProvinces() },
    'formData.province'(newProvince) {
        this.districtOptions = []; this.wardOptions = [];
        this.formData.district = null; this.formData.ward = null;
        if (newProvince?.code) this.onProvinceChange(newProvince.code)
    },
    'formData.district'(newDistrict) {
        this.wardOptions = [];
        this.formData.ward = null;
        if (newDistrict?.code) this.onDistrictChange(newDistrict.code)
    }
  },

  methods: {
    closeModal() { this.resetForm(); this.$emit('close') },

    async saveCustomer() {
      this.loading = true; this.errors = {};
      try {
        const dataToSend = {
          ...this.formData,
          province: this.formData.province?.name || null,
          district: this.formData.district?.name || null,
          ward: this.formData.ward?.name || null
        };
        const response = await axios.post('/admin/customers', dataToSend);
        if (response.data.success) {
          this.$emit('created', response.data.data);
          this.closeModal();
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors;
        } else {
          console.error(error);
        }
      } finally { this.loading = false }
    },

    resetForm() {
      this.formData = { name: '', email: '', password: '', password_confirmation: '', phone: '', address: '', province: null, district: null, ward: null };
      this.errors = {}; this.districtOptions = []; this.wardOptions = [];
    },

    async fetchWithFallback(url) {
      try { const res = await fetch(url); if (!res.ok) throw new Error(res.status); return res; }
      catch (e) { const httpUrl = url.replace('https://', 'http://'); return fetch(httpUrl); }
    },

    async loadProvinces() {
      try {
        if(this.provinceOptions.length) return;
        const res = await this.fetchWithFallback('https://provinces.open-api.vn/api/?depth=1');
        const data = await res.json();
        this.provinceOptions = data.map(p => ({ name: p.name, code: p.code }));
      } catch (e) { console.error(e) }
    },

    async onProvinceChange(code) {
      try {
        const res = await this.fetchWithFallback(`https://provinces.open-api.vn/api/p/${code}?depth=2`);
        const data = await res.json();
        this.districtOptions = data.districts.map(d => ({ name: d.name, code: d.code }));
      } catch (e) { console.error(e) }
    },

    async onDistrictChange(code) {
      try {
        const res = await this.fetchWithFallback(`https://provinces.open-api.vn/api/d/${code}?depth=2`);
        const data = await res.json();
        this.wardOptions = data.wards.map(w => ({ name: w.name, code: w.code }));
      } catch (e) { console.error(e) }
    }
  }
}
</script>

<style scoped>
/* === CSS ĐÃ ĐƯỢC CẬP NHẬT RESPONSIVE & MOBILE FIRST === */

/* 1. Grid Layout: Mặc định 1 cột (Mobile First) */
.form-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 16px;
}

/* 2. Responsive: Lên Desktop mới chia 2 cột */
@media (min-width: 768px) {
  .form-grid {
    grid-template-columns: 1fr 1fr;
    gap: 20px;
  }
  /* Address chiếm full chiều ngang trên desktop */
  .address-section {
    grid-column: 1 / -1;
  }
}

.form-field {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.field-label {
  font-weight: 600;
  font-size: 0.95rem; /* ~15px cho dễ đọc */
  color: #374151;
}

.required { color: #ef4444; }

/* 3. Input Size: Chuẩn 44px cho ngón tay chạm trên mobile */
.field-input {
  width: 100% !important;
  min-height: 44px !important;
  border-radius: 8px !important;
  font-size: 1rem !important; /* Font 16px để iOS không tự zoom */
  padding: 0.5rem 0.75rem !important;
  border: 1px solid #d1d5db !important;
}
.field-input:focus { border-color: #4F46E5 !important; }

/* 4. Address Section Styling */
.address-container {
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 16px;
  background-color: #f9fafb;
}

.address-header {
  border-bottom: 1px solid #e5e7eb;
  padding-bottom: 10px;
  margin-bottom: 15px;
}

.address-title {
  font-weight: 700;
  color: #1f2937;
  display: flex;
  align-items: center;
}

/* Grid cho phần địa chỉ: 1 cột mobile, 3 cột desktop */
.address-row {
  display: grid;
  grid-template-columns: 1fr;
  gap: 12px;
}

@media (min-width: 768px) {
  .address-row {
    grid-template-columns: repeat(3, 1fr);
  }
}

/* 5. Footer Buttons Styling */
.custom-footer {
  display: flex;
  gap: 12px;
  width: 100%;
  flex-direction: column-reverse; /* Mobile: Nút Hủy nằm dưới */
}

@media (min-width: 640px) {
  .custom-footer {
    flex-direction: row;
    justify-content: flex-end;
  }
  .btn-action {
    width: auto;
  }
}

/* Nút bấm to trên mobile */
.btn-action {
  height: 48px;
  font-weight: 600 !important;
  width: 100%;
}

.p-error {
  color: #ef4444;
  font-size: 0.85rem;
  margin-top: 4px;
}
</style>

<style>
/* 1. Thiết lập Flexbox cho Modal Container */
.mobile-fullscreen-dialog {
    display: flex;
    flex-direction: column;
}

/* 2. Header cố định */
.mobile-fullscreen-dialog .p-dialog-header {
    flex-shrink: 0;
    background: #fff;
    border-bottom: 1px solid #e5e7eb;
    z-index: 2;
}

/* 3. Nội dung ở giữa tự co giãn và cuộn */
.mobile-fullscreen-dialog .p-dialog-content {
    flex-grow: 1;
    overflow-y: auto; /* Chỉ cuộn phần nội dung */
    height: auto !important;
    padding-bottom: 20px;
}

/* 4. Footer cố định ở đáy (Quan trọng để fix lỗi không thấy nút) */
.mobile-fullscreen-dialog .p-dialog-footer {
    flex-shrink: 0;
    background: #fff;
    border-top: 1px solid #e5e7eb;
    padding: 1rem;
    z-index: 2;
}

/* 5. Mobile Specific Fixes */
@media (max-width: 640px) {
    .mobile-fullscreen-dialog {
        margin: 0 !important;
        max-height: 100vh !important;
        height: 100vh !important;
        width: 100vw !important;
        border-radius: 0 !important;
    }
}

/* Fix canh lề text trong dropdown */
.dropdown-fix .p-dropdown-label {
    display: flex;
    align-items: center;
}
</style>
