<template>
  <Dialog
    :visible="visible"
    @update:visible="$emit('close')"
    header="Cập nhật thông tin"
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
          <InputText id="name" v-model="formData.name" type="text" placeholder="Nhập tên khách hàng" class="field-input"
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
          <label for="password" class="field-label">Mật khẩu mới <small class="text-muted">(Bỏ trống nếu không đổi)</small></label>
          <InputText id="password" v-model="formData.password" type="password" placeholder="Nhập mật khẩu mới"
            class="field-input" :class="{ 'p-invalid': errors.password }" />
          <small v-if="errors.password" class="p-error">{{ errors.password[0] }}</small>
        </div>

        <div class="form-field">
          <label for="password_confirmation" class="field-label">Xác nhận mật khẩu</label>
          <InputText id="password_confirmation" v-model="formData.password_confirmation" type="password"
            placeholder="Nhập lại mật khẩu mới" class="field-input"
            :class="{ 'p-invalid': errors.password_confirmation }" />
        </div>

        <div class="form-field">
          <label for="phone" class="field-label">Số điện thoại</label>
          <InputText id="phone" v-model="formData.phone" type="tel" placeholder="Nhập số điện thoại" class="field-input"
            :class="{ 'p-invalid': errors.phone }" />
          <small v-if="errors.phone" class="p-error">{{ errors.phone[0] }}</small>
        </div>

        <div class="form-field">
          <label for="address" class="field-label">Địa chỉ đường/Số nhà</label>
          <InputText id="address" v-model="formData.address" type="text" placeholder="Nhập địa chỉ cụ thể"
            class="field-input" :class="{ 'p-invalid': errors.address }" />
          <small v-if="errors.address" class="p-error">{{ errors.address[0] }}</small>
        </div>

        <div class="address-section">
          <div class="address-container">
            <div class="address-header">
              <span class="address-title"><i class="pi pi-refresh mr-2"></i> Khu vực (Cập nhật)</span>
            </div>

            <div class="address-row">
              <div class="form-field">
                <label for="province" class="field-label">Tỉnh/Thành phố</label>
                <Dropdown id="province" v-model="formData.province" :options="provinceOptions" optionLabel="name"
                  placeholder="Chọn Tỉnh/Thành" class="field-input w-full dropdown-fix" :class="{ 'p-invalid': errors.province }"
                  filter showClear @change="handleProvinceChange" />
                <small v-if="errors.province" class="p-error">{{ errors.province[0] }}</small>
              </div>

              <div class="form-field">
                <label for="district" class="field-label">Quận/Huyện</label>
                <Dropdown id="district" v-model="formData.district" :options="districtOptions" optionLabel="name"
                  placeholder="Chọn Quận/Huyện" class="field-input w-full dropdown-fix" :class="{ 'p-invalid': errors.district }"
                  :disabled="!formData.province" filter showClear @change="handleDistrictChange" />
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
        <Button label="Lưu thay đổi" icon="pi pi-check" class="p-button-primary btn-action btn-save" @click="saveCustomer"
          :loading="loading" />
      </div>
    </template>
  </Dialog>
</template>

<script>
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Dropdown from 'primevue/dropdown'
import axios from 'axios'

export default {
  name: 'StaffEditCustomerModal',
  components: { Dialog, Button, InputText, Dropdown },
  props: {
    visible: { type: Boolean, default: false },
    customer: { type: Object, default: null }
  },
  emits: ['close', 'updated'],

  data() {
    return {
      loading: false,
      formData: {
        name: '', email: '', password: '', password_confirmation: '',
        phone: '', address: '', province: null, district: null, ward: null
      },
      errors: {},
      provinceOptions: [],
      districtOptions: [],
      wardOptions: []
    }
  },

  watch: {
    visible(newVal) {
      if (newVal && this.customer) {
        this.loadCustomerData();
      }
    }
  },

  methods: {
    closeModal() {
      this.resetForm();
      this.$emit('close');
    },

    async fetchWithFallback(url) {
      try {
        const res = await fetch(url);
        if (!res.ok) throw new Error(res.status);
        return res;
      } catch (e) {
        const httpUrl = url.replace('https://', 'http://');
        return fetch(httpUrl);
      }
    },

    async loadProvinces() {
      try {
        if (this.provinceOptions.length > 0) return;
        const response = await this.fetchWithFallback('https://provinces.open-api.vn/api/?depth=1');
        const data = await response.json();
        this.provinceOptions = data.map(p => ({ name: p.name, code: p.code }));
      } catch (error) { console.error(error); }
    },

    async loadCustomerData() {
      if (!this.customer) return;
      this.errors = {};
      this.formData.name = this.customer.name || '';
      this.formData.email = this.customer.email || '';
      this.formData.phone = this.customer.phone || '';
      this.formData.address = this.customer.address || '';
      this.formData.password = '';
      this.formData.password_confirmation = '';

      await this.loadProvinces();

      if (this.customer.province) {
        const pObj = this.provinceOptions.find(p => p.name === this.customer.province);
        if (pObj) {
          this.formData.province = pObj;
          await this.loadDistricts(pObj.code);
          if (this.customer.district) {
            const dObj = this.districtOptions.find(d => d.name === this.customer.district);
            if (dObj) {
              this.formData.district = dObj;
              await this.loadWards(dObj.code);
              if (this.customer.ward) {
                const wObj = this.wardOptions.find(w => w.name === this.customer.ward);
                if (wObj) this.formData.ward = wObj;
              }
            }
          }
        }
      }
    },

    handleProvinceChange() {
      this.formData.district = null; this.formData.ward = null;
      this.districtOptions = []; this.wardOptions = [];
      if (this.formData.province?.code) this.loadDistricts(this.formData.province.code);
    },

    handleDistrictChange() {
      this.formData.ward = null; this.wardOptions = [];
      if (this.formData.district?.code) this.loadWards(this.formData.district.code);
    },

    async loadDistricts(provinceCode) {
      try {
        const response = await this.fetchWithFallback(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`);
        const data = await response.json();
        this.districtOptions = data.districts.map(d => ({ name: d.name, code: d.code }));
      } catch (e) { console.error(e) }
    },

    async loadWards(districtCode) {
      try {
        const response = await this.fetchWithFallback(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`);
        const data = await response.json();
        this.wardOptions = data.wards.map(w => ({ name: w.name, code: w.code }));
      } catch (e) { console.error(e) }
    },

    async saveCustomer() {
      this.loading = true; this.errors = {};
      try {
        const dataToSend = {
          name: this.formData.name,
          email: this.formData.email,
          phone: this.formData.phone,
          address: this.formData.address,
          province: this.formData.province?.name || null,
          district: this.formData.district?.name || null,
          ward: this.formData.ward?.name || null
        };
        if (this.formData.password) {
          dataToSend.password = this.formData.password;
          dataToSend.password_confirmation = this.formData.password_confirmation;
        }
        const response = await axios.put(`/staff/customers/${this.customer.id}`, dataToSend);
        if (response.data.success) {
          this.$emit('updated', response.data.data);
          this.closeModal();
        }
      } catch (error) {
        if (error.response?.status === 422) {
          this.errors = error.response.data.errors;
        } else { console.error(error); }
      } finally { this.loading = false; }
    },

    resetForm() {
      this.formData = { name: '', email: '', password: '', password_confirmation: '', phone: '', address: '', province: null, district: null, ward: null };
      this.errors = {};
    }
  }
}
</script>

<style scoped>
/* === MOBILE FIRST STYLES === */

/* 1. Form Grid: Mặc định 1 cột */
.form-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 16px;
}

@media (min-width: 768px) {
  .form-grid {
    grid-template-columns: 1fr 1fr;
    gap: 20px;
  }
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
  font-size: 0.95rem;
  color: #374151;
}

.text-muted {
  font-weight: 400;
  color: #6b7280;
  font-size: 0.8em;
}

.required { color: #ef4444; }

/* 2. Input Size: 44px (Chuẩn UX Mobile) */
.field-input {
  width: 100% !important;
  min-height: 44px !important;
  border-radius: 8px !important;
  font-size: 1rem !important; /* Font 16px: Chống zoom trên iOS */
  padding: 0.5rem 0.75rem !important;
  border: 1px solid #d1d5db !important;
}
.field-input:focus { border-color: #4F46E5 !important; }

/* 3. Address Section */
.address-container {
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 16px;
  background-color: #f9fafb;
}

.address-header {
  border-bottom: 1px solid #e5e7eb;
  padding-bottom: 8px;
  margin-bottom: 12px;
}

.address-title {
  font-weight: 700;
  color: #1f2937;
  font-size: 0.95rem;
}

/* Grid địa chỉ: 1 cột mobile -> 3 cột desktop */
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

/* 4. Footer Buttons */
.custom-footer {
  display: flex;
  gap: 12px;
  width: 100%;
  flex-direction: column-reverse; /* Mobile: Hủy dưới, Lưu trên */
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
/* 1. Modal Container thành Flexbox dọc */
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

/* 3. Nội dung cuộn */
.mobile-fullscreen-dialog .p-dialog-content {
    flex-grow: 1;
    overflow-y: auto;
    height: auto !important;
    padding-bottom: 20px;
}

/* 4. Footer cố định đáy */
.mobile-fullscreen-dialog .p-dialog-footer {
    flex-shrink: 0;
    background: #fff;
    border-top: 1px solid #e5e7eb;
    padding: 1rem;
    z-index: 2;
}

/* 5. Mobile Fullscreen Override */
@media (max-width: 640px) {
    .mobile-fullscreen-dialog {
        margin: 0 !important;
        max-height: 100vh !important;
        height: 100vh !important;
        width: 100vw !important;
        border-radius: 0 !important;
    }
}

/* Dropdown fix */
.dropdown-fix .p-dropdown-label {
    display: flex;
    align-items: center;
}
</style>
