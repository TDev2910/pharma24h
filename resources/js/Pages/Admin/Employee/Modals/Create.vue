<template>
  <Dialog :visible="visible" @update:visible="$emit('close')" header="Thêm Nhân Viên Mới" 
    :style="{ width: '1000px' }" :breakpoints="{ '960px': '75vw', '640px': '90vw' }" 
    modal :closable="true" class="employee-modal">

    <div class="modal-content">
      <TabView class="custom-tabs">
        
        <TabPanel>
          <template #header>
            <div class="flex align-items-center">
              <i class="pi pi-id-card mr-2"></i>
              <span>Thông tin chung</span>
            </div>
          </template>

          <div class="grid-layout">
            <div class="col-left">
              <h3 class="section-title">Thông tin cá nhân</h3>
              
              <div class="form-row">
                <div class="form-field full-width">
                  <label for="fullName" class="field-label">Họ và tên <span class="required">*</span></label>
                  <InputText id="fullName" v-model="formData.full_name" placeholder="Ví dụ: Nguyễn Văn A" :class="{ 'p-invalid': errors.full_name }" />
                  <small v-if="errors.full_name" class="p-error">{{ errors.full_name[0] }}</small>
                </div>
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label for="dob" class="field-label">Ngày sinh</label>
                  <Calendar id="dob" v-model="formData.dob" dateFormat="dd/mm/yy" placeholder="dd/mm/yyyy" showIcon />
                </div>
                <div class="form-field">
                  <label for="gender" class="field-label">Giới tính</label>
                  <Dropdown id="gender" v-model="formData.gender" :options="genderOptions" optionLabel="label" optionValue="value" placeholder="Chọn giới tính" />
                </div>
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label for="phone" class="field-label">Số điện thoại</label>
                  <InputText id="phone" v-model="formData.phone_number" placeholder="09xxxxxxxx" :class="{ 'p-invalid': errors.phone_number }" />
                  <small v-if="errors.phone_number" class="p-error">{{ errors.phone_number[0] }}</small>
                </div>
                <div class="form-field">
                  <label for="idCard" class="field-label">CMND/CCCD</label>
                  <InputText id="idCard" v-model="formData.id_card_number" placeholder="Số giấy tờ tùy thân" :class="{ 'p-invalid': errors.id_card_number }" />
                </div>
              </div>

              <div class="form-row">
                <div class="form-field full-width">
                  <label for="address" class="field-label">Địa chỉ thường trú</label>
                  <Textarea id="address" v-model="formData.address" rows="3" placeholder="Số nhà, đường, phường/xã..." autoResize />
                </div>
              </div>
            </div>

            <div class="col-right">
              
              <div class="info-group account-group">
                <h3 class="group-header text-blue-700"><i></i> Tài khoản hệ thống</h3>
                
                <div class="form-row flex-nowrap-row">
                  <div class="form-field" style="flex: 1;">
                    <label for="email" class="field-label">Email (Đăng nhập) <span class="required">*</span></label>
                    <InputText id="email" v-model="formData.email" placeholder="email@company.com" class="w-full" :class="{ 'p-invalid': errors.email }" />
                    <small v-if="errors.email" class="p-error">{{ errors.email[0] }}</small>
                  </div>

                  <div class="form-field" style="flex: 1;">
                    <label for="password" class="field-label">Mật khẩu khởi tạo</label>
                    <div class="p-inputgroup custom-input-group">
                      <Password id="password" v-model="formData.password" placeholder="Tự động..." :toggleMask="true" :feedback="false" class="w-full" inputClass="w-full border-noradius-right" />
                      <Button icon="pi pi-key" @click="generatePassword" :loading="generatingPassword" severity="secondary" v-tooltip.top="'Tạo ngẫu nhiên'" />
                    </div>
                  </div>
                </div>
              </div>

              <div class="info-group work-group mt-3">
                <h3 class="group-header text-blue-700"><i></i> Hồ sơ công việc</h3>
                
                <div class="form-row">
                  <div class="form-field">
                    <label for="employeeCode" class="field-label">Mã nhân viên</label>
                    <div class="p-inputgroup custom-input-group">
                      <InputText id="employeeCode" v-model="formData.employee_code" placeholder="Tự động" :class="{ 'p-invalid': errors.employee_code }" />
                      <Button icon="pi pi-sync" @click="generateCode" :loading="generating" severity="secondary" v-tooltip.top="'Tạo mã tự động'" />
                    </div>
                    <small v-if="errors.employee_code" class="p-error">{{ errors.employee_code[0] }}</small>
                  </div>
                  
                  <div class="form-field">
                    <label for="startDate" class="field-label">Ngày vào làm</label>
                    <Calendar id="startDate" v-model="formData.start_date" dateFormat="dd/mm/yy" placeholder="dd/mm/yyyy" showIcon />
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-field">
                    <label for="branches" class="field-label">Chi nhánh</label>
                    <Dropdown id="branches" v-model="formData.branch_id" :options="resources.branches" optionLabel="name" optionValue="id" placeholder="Chọn chi nhánh" filter class="w-full" />
                  </div>
                  <div class="form-field">
                    <label for="department" class="field-label">Phòng ban</label>
                    <Dropdown id="department" v-model="formData.department_id" :options="resources.departments" optionLabel="name" optionValue="id" placeholder="Chọn phòng ban" filter class="w-full" />
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-field full-width">
                    <label for="jobTitle" class="field-label">Chức vụ</label>
                    <Dropdown id="jobTitle" v-model="formData.job_title_id" :options="resources.job_titles" optionLabel="name" optionValue="id" placeholder="Chọn chức vụ" filter class="w-full" />
                  </div>
                </div>
              </div>

            </div>
          </div>
        </TabPanel>

        <TabPanel>
          <template #header>
            <div class="flex align-items-center">
              <i class="pi pi-money-bill mr-2"></i>
              <span>Lương & Phụ cấp</span>
            </div>
          </template>

          <div class="salary-container">
            <div class="salary-base-section">
              <div class="form-row">
                <div class="form-field" style="flex: 1;">
                  <label for="salaryType" class="field-label">Hình thức trả lương <span class="required">*</span></label>
                  <Dropdown id="salaryType" v-model="formData.salary_type" :options="salaryTypeOptions" optionLabel="label" optionValue="value" class="w-full" />
                </div>
                <div class="form-field" style="flex: 1;">
                  <label for="salaryLevel" class="field-label">Mức lương cơ bản <span class="required">*</span></label>
                  <InputNumber id="salaryLevel" v-model="formData.salary_level" mode="currency" currency="VND" locale="vi-VN" class="w-full font-bold" />
                </div>
              </div>
            </div>

            <div class="dynamic-sections">
              <div class="dynamic-box" :class="{ 'active': formData.enable_allowance }">
                <div class="box-header">
                  <span class="box-title text-blue-600"><i class="pi pi-wallet mr-2"></i> Phụ cấp</span>
                  <InputSwitch v-model="formData.enable_allowance" />
                </div>
                <div v-if="formData.enable_allowance" class="box-content">
                  <div v-for="(allowance, index) in formData.allowances" :key="index" class="dynamic-row">
                    <InputText v-model="allowance.name" placeholder="Tên phụ cấp" style="flex: 2" />
                    <InputNumber v-model="allowance.amount" mode="currency" currency="VND" locale="vi-VN" placeholder="Số tiền" style="flex: 1.5" />
                    <Dropdown v-model="allowance.type" :options="allowanceTypeOptions" optionLabel="label" optionValue="value" style="flex: 1.5" />
                    <Button icon="pi pi-trash" class="p-button-danger p-button-text" @click="removeAllowance(index)" />
                  </div>
                  <Button label="Thêm dòng" icon="pi pi-plus" class="p-button-text p-button-sm" @click="addAllowance" />
                </div>
              </div>

              <div class="dynamic-box" :class="{ 'active': formData.enable_target_bonus }">
                <div class="box-header">
                  <span class="box-title text-green-600"><i class="pi pi-chart-line mr-2"></i> Thưởng chỉ tiêu / KPI</span>
                  <InputSwitch v-model="formData.enable_target_bonus" />
                </div>
                <div v-if="formData.enable_target_bonus" class="box-content">
                  <div v-for="(target, index) in formData.targets" :key="index" class="dynamic-row">
                    <InputText v-model="target.activity_type" placeholder="Hoạt động" style="flex: 2" />
                    <InputNumber v-model="target.target_amount" mode="currency" currency="VND" locale="vi-VN" placeholder="Mức chỉ tiêu" style="flex: 1.5" />
                    <Dropdown v-model="target.bonus_type" :options="bonusTypeOptions" optionLabel="label" optionValue="value" style="flex: 1" />
                    <InputNumber v-model="target.bonus_value" placeholder="Giá trị thưởng" style="flex: 1.5" />
                    <Button icon="pi pi-trash" class="p-button-danger p-button-text" @click="removeTarget(index)" />
                  </div>
                  <Button label="Thêm chỉ tiêu" icon="pi pi-plus" class="p-button-text p-button-sm" @click="addTarget" />
                </div>
              </div>

              <div class="dynamic-box" :class="{ 'active': formData.enable_deduction }">
                <div class="box-header">
                  <span class="box-title text-red-600"><i class="pi pi-minus-circle mr-2"></i> Các khoản giảm trừ</span>
                  <InputSwitch v-model="formData.enable_deduction" />
                </div>
                <div v-if="formData.enable_deduction" class="box-content">
                  <div v-for="(deduction, index) in formData.deductions" :key="index" class="dynamic-row">
                    <InputText v-model="deduction.reason" placeholder="Lý do (VD: BHXH)" style="flex: 2" />
                    <InputNumber v-model="deduction.amount" mode="currency" currency="VND" locale="vi-VN" placeholder="Số tiền" style="flex: 1.5" />
                    <Dropdown v-model="deduction.frequency" :options="deductionFrequencyOptions" optionLabel="label" optionValue="value" style="flex: 1.5" />
                    <Button icon="pi pi-trash" class="p-button-danger p-button-text" @click="removeDeduction(index)" />
                  </div>
                  <Button label="Thêm giảm trừ" icon="pi pi-plus" class="p-button-text p-button-sm" @click="addDeduction" />
                </div>
              </div>
            </div>
          </div>
        </TabPanel>
      </TabView>
    </div>

    <template #footer>
      <div class="dialog-footer">
        <Button label="Hủy bỏ" icon="pi pi-times" class="p-button-text p-button-secondary" @click="closeModal" />
        <Button label="Lưu nhân viên" icon="pi pi-check" class="p-button-primary" @click="saveEmployee" :loading="loading" />
      </div>
    </template>
  </Dialog>
</template>

<script>
import Dialog from 'primevue/dialog'
import TabView from 'primevue/tabview'
import TabPanel from 'primevue/tabpanel'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import InputNumber from 'primevue/inputnumber'
import Dropdown from 'primevue/dropdown'
import Calendar from 'primevue/calendar'
import Textarea from 'primevue/textarea'
import InputSwitch from 'primevue/inputswitch'
import Password from 'primevue/password'
import axios from 'axios'

export default {
  name: 'CreateEmployeeModal',
  components: {
    Dialog, TabView, TabPanel, Button, InputText, InputNumber, 
    Dropdown, Calendar, Textarea, InputSwitch, Password
  },
  props: {
    visible: { type: Boolean, default: false }
  },
  emits: ['close', 'created'],

  data() {
    return {
      loading: false,
      generating: false,
      generatingPassword: false,
      formData: {
        full_name: '', email: '', password: '', phone_number: '', employee_code: '',
        department_id: null, job_title_id: null, branch_id: null, start_date: null,
        dob: null, gender: null, address: '', id_card_number: '',
        salary_type: 'fixed', salary_level: 0,
        enable_allowance: false, enable_target_bonus: false, enable_deduction: false,
        allowances: [], targets: [], deductions: []
      },
      // Khởi tạo resources để tránh lỗi undefined khi render
      resources: { departments: [], job_titles: [], branches: [] },
      errors: {},
      genderOptions: [ { label: 'Nam', value: 'nam' }, { label: 'Nữ', value: 'nữ' } ],
      salaryTypeOptions: [ { label: 'Lương cố định', value: 'fixed' } ],
      allowanceTypeOptions: [
        { label: 'Cố định/Ngày', value: 'fixed_daily' },
        { label: 'Cố định/Tháng', value: 'fixed_monthly' },
        { label: '% Lương', value: 'percent_salary' }
      ],
      bonusTypeOptions: [ { label: 'Cố định', value: 'fixed' }, { label: '% Doanh thu', value: 'percent' } ],
      deductionFrequencyOptions: [ { label: 'Một lần', value: 'one-time' }, { label: 'Hàng tháng', value: 'monthly' } ]
    }
  },

  watch: {
    visible(newVal) {
      if (newVal) {
        this.loadResources()
      }
    }
  },

  methods: {
    closeModal() {
      this.resetForm()
      this.$emit('close')
    },

    async loadResources() {
      try {
        const response = await axios.get('/admin/employees/resources/data')
        this.resources = response.data
        
        // Auto-select "Nhân Viên" position if available
        if (!this.formData.job_title_id && this.resources.job_titles) {
          const nhanVien = this.resources.job_titles.find(jt => 
            jt.name.toLowerCase() === 'nhân viên' || jt.name.toLowerCase() === 'nhan vien'
          )
          if (nhanVien) this.formData.job_title_id = nhanVien.id
        }
      } catch (error) {
        console.error('Error loading resources:', error)
      }
    },

    async generateCode() {
      this.generating = true
      try {
        const response = await axios.get('/admin/employees/generate/code')
        this.formData.employee_code = response.data.employee_code
      } catch (error) {
        console.error('Error generating code:', error)
      } finally {
        this.generating = false
      }
    },

    generatePassword() {
      this.generatingPassword = true
      const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'
      let password = ''
      for (let i = 0; i < 8; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length))
      }
      this.formData.password = password
      this.generatingPassword = false
      this.$toast.add({ severity: 'info', summary: 'Thông báo', detail: 'Mật khẩu đã được tạo tự động', life: 2000 })
    },

    addAllowance() { this.formData.allowances.push({ name: '', amount: 0, type: 'fixed_monthly' }) },
    removeAllowance(index) { this.formData.allowances.splice(index, 1) },
    addTarget() { this.formData.targets.push({ activity_type: '', target_amount: 0, bonus_type: 'fixed', bonus_value: 0 }) },
    removeTarget(index) { this.formData.targets.splice(index, 1) },
    addDeduction() { this.formData.deductions.push({ reason: '', amount: 0, frequency: 'monthly' }) },
    removeDeduction(index) { this.formData.deductions.splice(index, 1) },

    async saveEmployee() {
      this.loading = true
      this.errors = {}
      try {
        const formData = { ...this.formData }
        if (formData.start_date) formData.start_date = this.formatDate(formData.start_date)
        if (formData.dob) formData.dob = this.formatDate(formData.dob)
        if (!formData.enable_allowance) formData.allowances = []
        if (!formData.enable_target_bonus) formData.targets = []
        if (!formData.enable_deduction) formData.deductions = []

        const payload = {
          ...formData,
          branch_id: formData.branch_id !== undefined ? formData.branch_id : null,
          department_id: formData.department_id !== undefined ? formData.department_id : null,
          job_title_id: formData.job_title_id !== undefined ? formData.job_title_id : null,
        }

        const response = await axios.post('/admin/employees', payload)
        if (response.data && response.data.success) {
          this.$toast.add({ severity: 'success', summary: 'Thành công', detail: response.data.message || 'Đã thêm nhân viên thành công', life: 3000 })
          this.$emit('created', response.data.employee || response.data)
          this.closeModal()
        } else {
          throw new Error(response.data?.message || 'Có lỗi xảy ra')
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors
          this.$toast.add({ severity: 'error', summary: 'Lỗi validation', detail: 'Vui lòng kiểm tra lại thông tin', life: 5000 })
        } else {
          this.$toast.add({ severity: 'error', summary: 'Lỗi', detail: error.response?.data?.message || error.message, life: 5000 })
        }
      } finally {
        this.loading = false
      }
    },

    formatDate(date) {
      if (!date) return null
      const d = new Date(date)
      const year = d.getFullYear()
      const month = String(d.getMonth() + 1).padStart(2, '0')
      const day = String(d.getDate()).padStart(2, '0')
      return `${year}-${month}-${day}`
    },

    resetForm() {
      this.formData = {
        full_name: '', email: '', password: '', phone_number: '', employee_code: '',
        department_id: null, job_title_id: null, branch_id: null, start_date: null,
        dob: null, gender: null, address: '', id_card_number: '',
        salary_type: 'fixed', salary_level: 0,
        enable_allowance: false, enable_target_bonus: false, enable_deduction: false,
        allowances: [], targets: [], deductions: []
      }
      this.errors = {}
    }
  }
}
</script>

<style scoped>
/* Reset */
.w-full { width: 100%; }
.font-bold { font-weight: 600; }
.text-blue-600 { color: #2563eb; }
.text-green-600 { color: #16a34a; }
.text-red-600 { color: #dc2626; }
.text-blue-700 { color: #1d4ed8; }
.text-gray-700 { color: #374151; }
.mt-3 { margin-top: 1rem; }

/* Grid Layout Main */
.grid-layout {
  display: flex;
  gap: 32px;
}

.col-left {
  flex: 1; /* Cột trái */
  padding-right: 24px;
  border-right: 1px solid #f0f0f0;
}

.col-right {
  flex: 1; /* Cột phải */
  display: flex;
  flex-direction: column;
  gap: 16px;
}

/* Section Titles */
.section-title {
  font-size: 15px;
  font-weight: 700;
  color: #374151;
  margin-bottom: 20px;
  margin-top: 0;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* Info Groups (Cột Phải) */
.info-group {
  background: #fcfcfc;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 16px;
  transition: all 0.2s ease;
}

.info-group:hover {
  border-color: #d1d5db;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}


.info-group.work-group {
  border-top: 3px solid #6b7280; /* Viền xám */
}

.group-header {
  font-size: 14px;
  font-weight: 700;
  margin: 0 0 16px 0;
  display: flex;
  align-items: center;
}

/* Form Styles */
.form-row {
  display: flex;
  gap: 16px;
  margin-bottom: 16px;
}

/* Class đặc biệt để ép Email & Password nằm ngang */
.flex-nowrap-row {
  flex-wrap: nowrap !important;
}

.form-field {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-field.full-width {
  flex: 100%;
}

.field-label {
  font-size: 13px;
  font-weight: 500;
  color: #4b5563;
}

.required { color: #ef4444; }
.p-error { color: #ef4444; font-size: 11px; margin-top: 2px; }

/* Styles cho Input Group (Nút nằm trong Input) */
:deep(.custom-input-group) {
  display: flex;
  flex-wrap: nowrap; /* Ngăn xuống dòng nút */
  align-items: stretch;
  width: 100%;
}

:deep(.custom-input-group .p-inputtext),
:deep(.custom-input-group .p-password .p-inputtext) {
  border-top-right-radius: 0 !important;
  border-bottom-right-radius: 0 !important;
  border-right: 0 !important; /* Xóa viền phải để liền mạch */
  flex: 1;
  min-width: 0; /* Cho phép co nhỏ */
}

:deep(.custom-input-group .p-button) {
  border-top-left-radius: 0 !important;
  border-bottom-left-radius: 0 !important;
  flex-shrink: 0; /* Không cho nút bị co lại */
}

/* Fix riêng cho Password component của PrimeVue */
:deep(.custom-input-group .p-password) {
  flex: 1;
  display: flex;
}

/* Salary Tab Styles */
.salary-base-section {
  background: #f9fafb;
  padding: 20px;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  margin-bottom: 24px;
}

.dynamic-sections {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.dynamic-box {
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  overflow: hidden;
  background: white;
  transition: all 0.3s;
}

.dynamic-box.active {
  border-color: #bfdbfe;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

.box-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  background: #fff;
  border-bottom: 1px solid transparent;
}

.dynamic-box.active .box-header {
  background: #eff6ff;
  border-bottom-color: #dbeafe;
}

.box-title { font-weight: 600; font-size: 14px; }

.box-content { padding: 16px; }

.dynamic-row {
  display: flex;
  gap: 12px;
  align-items: center;
  margin-bottom: 12px;
}

/* Footer */
.dialog-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding-top: 16px;
  border-top: 1px solid #f0f0f0;
}

/* Responsive */
@media (max-width: 768px) {
  .grid-layout { flex-direction: column; }
  .col-left { border-right: none; padding-right: 0; padding-bottom: 24px; border-bottom: 1px solid #f0f0f0; }
  .form-row { flex-direction: column; gap: 12px; }
  .flex-nowrap-row { flex-wrap: wrap !important; } /* Mobile cho phép xuống dòng lại */
}
</style>