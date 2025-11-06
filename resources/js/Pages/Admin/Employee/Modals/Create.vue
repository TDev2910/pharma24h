<template>
  <Dialog :visible="visible" @update:visible="$emit('close')" header="Thêm nhân viên mới" :style="{ width: '900px' }"
    modal :closable="true">

    <TabView>
      <!-- TAB 1: THÔNG TIN CƠ BẢN -->
      <TabPanel header="Thông tin cơ bản">
        <div class="form-grid">
          <!-- Họ tên -->
          <div class="form-field">
            <label for="fullName" class="field-label">Họ tên <span class="required">*</span></label>
            <InputText id="fullName" v-model="formData.full_name" type="text" placeholder="Nhập họ tên"
              class="field-input" :class="{ 'p-invalid': errors.full_name }" />
            <small v-if="errors.full_name" class="p-error">{{ errors.full_name[0] }}</small>
          </div>

          <!-- Email -->
          <div class="form-field">
            <label for="email" class="field-label">Email <span class="required">*</span></label>
            <InputText id="email" v-model="formData.email" type="email" placeholder="email@company.com"
              class="field-input" :class="{ 'p-invalid': errors.email }" />
            <small v-if="errors.email" class="p-error">{{ errors.email[0] }}</small>
          </div>

          <!-- Password -->
          <div class="form-field">
            <label for="password" class="field-label">Mật khẩu</label>
            <div class="input-group">
              <Password 
                id="password" 
                v-model="formData.password" 
                placeholder="Để trống sẽ tự động tạo"
                :toggleMask="true"
                :feedback="false"
                class="field-input" 
                :class="{ 'p-invalid': errors.password }"
                inputClass="w-full"
              />
              <Button 
                icon="pi pi-refresh" 
                @click="generatePassword" 
                :loading="generatingPassword"
                class="generate-btn"
                title="Tạo mật khẩu tự động"
                severity="secondary"
              />
            </div>
            <small v-if="errors.password" class="p-error">{{ errors.password[0] }}</small>
            <small class="text-muted">Nếu để trống, hệ thống sẽ tự động tạo mật khẩu mặc định</small>
          </div>

          <!-- Số điện thoại -->
          <div class="form-field">
            <label for="phone" class="field-label">Số điện thoại</label>
            <InputText id="phone" v-model="formData.phone_number" type="tel" placeholder="0123456789"
              class="field-input" :class="{ 'p-invalid': errors.phone_number }" />
            <small v-if="errors.phone_number" class="p-error">{{ errors.phone_number[0] }}</small>
          </div>

          <!-- Mã nhân viên -->
          <div class="form-field">
            <label for="employeeCode" class="field-label">Mã nhân viên</label>
            <div class="input-group">
              <InputText id="employeeCode" v-model="formData.employee_code" type="text" placeholder="Tự động tạo"
                class="field-input" :class="{ 'p-invalid': errors.employee_code }" />
              <Button icon="pi pi-refresh" @click="generateCode" :loading="generating" class="generate-btn"
                title="Tạo mã tự động" />
            </div>
            <small v-if="errors.employee_code" class="p-error">{{ errors.employee_code[0] }}</small>
          </div>

          <!-- Phòng ban -->
          <div class="form-field">
            <label for="department" class="field-label">Phòng ban</label>
            <Dropdown id="department" v-model="formData.department_id" :options="resources.departments"
              optionLabel="name" optionValue="id" placeholder="Chọn phòng ban" class="field-input"
              :class="{ 'p-invalid': errors.department_id }" />
            <small v-if="errors.department_id" class="p-error">{{ errors.department_id[0] }}</small>
          </div>

          <!-- Chức vụ -->
          <div class="form-field">
            <label for="jobTitle" class="field-label">Chức vụ</label>
            <Dropdown id="jobTitle" v-model="formData.job_title_id" :options="resources.job_titles" optionLabel="name"
              optionValue="id" placeholder="Chọn chức vụ" class="field-input"
              :class="{ 'p-invalid': errors.job_title_id }" />
            <small v-if="errors.job_title_id" class="p-error">{{ errors.job_title_id[0] }}</small>
          </div>

          <!-- Chi nhánh -->
          <div class="form-field">
            <label for="branches" class="field-label">Chi nhánh</label>
            <Dropdown id="branches" v-model="formData.branch_id" :options="resources.branches" optionLabel="name"
              optionValue="id" placeholder="Chọn chi nhánh" class="field-input"
              :class="{ 'p-invalid': errors.branch_id }" />
            <small v-if="errors.branch_id" class="p-error">{{ errors.branch_id[0] }}</small>
          </div>

          <!-- Ngày bắt đầu -->
          <div class="form-field">
            <label for="startDate" class="field-label">Ngày bắt đầu làm việc</label>
            <Calendar id="startDate" v-model="formData.start_date" dateFormat="dd/mm/yy" placeholder="Chọn ngày"
              showIcon class="field-input" :class="{ 'p-invalid': errors.start_date }" />
            <small v-if="errors.start_date" class="p-error">{{ errors.start_date[0] }}</small>
          </div>

          <!-- Ngày sinh -->
          <div class="form-field">
            <label for="dob" class="field-label">Ngày sinh</label>
            <Calendar id="dob" v-model="formData.dob" dateFormat="dd/mm/yy" placeholder="Chọn ngày" showIcon
              class="field-input" :class="{ 'p-invalid': errors.dob }" />
            <small v-if="errors.dob" class="p-error">{{ errors.dob[0] }}</small>
          </div>

          <!-- Giới tính -->
          <div class="form-field">
            <label for="gender" class="field-label">Giới tính</label>
            <Dropdown id="gender" v-model="formData.gender" :options="genderOptions" optionLabel="label"
              optionValue="value" placeholder="Chọn giới tính" class="field-input"
              :class="{ 'p-invalid': errors.gender }" />
            <small v-if="errors.gender" class="p-error">{{ errors.gender[0] }}</small>
          </div>

          <!-- CMND/CCCD -->
          <div class="form-field">
            <label for="idCard" class="field-label">CMND/CCCD</label>
            <InputText id="idCard" v-model="formData.id_card_number" type="text" placeholder="Số CMND/CCCD"
              class="field-input" :class="{ 'p-invalid': errors.id_card_number }" />
            <small v-if="errors.id_card_number" class="p-error">{{ errors.id_card_number[0] }}</small>
          </div>

          <!-- Địa chỉ (span 2 columns) -->
          <div class="form-field address-field">
            <label for="address" class="field-label">Địa chỉ</label>
            <Textarea id="address" v-model="formData.address" rows="3" placeholder="Nhập địa chỉ" class="field-textarea"
              :class="{ 'p-invalid': errors.address }" />
            <small v-if="errors.address" class="p-error">{{ errors.address[0] }}</small>
          </div>
        </div>
      </TabPanel>

      <!-- TAB 2: THIẾT LẬP LƯƠNG -->
      <TabPanel header="Thiết lập lương">
        <div class="salary-grid">
          <!-- Loại lương -->
          <div class="form-field">
            <label for="salaryType" class="field-label">Loại lương <span class="required">*</span></label>
            <Dropdown id="salaryType" v-model="formData.salary_type" :options="salaryTypeOptions" optionLabel="label"
              optionValue="value" placeholder="Chọn loại lương" class="field-input"
              :class="{ 'p-invalid': errors.salary_type }" />
            <small v-if="errors.salary_type" class="p-error">{{ errors.salary_type[0] }}</small>
          </div>

          <!-- Mức lương -->
          <div class="form-field">
            <label for="salaryLevel" class="field-label">Mức lương cơ bản <span class="required">*</span></label>
            <InputNumber id="salaryLevel" v-model="formData.salary_level" mode="currency" currency="VND" locale="vi-VN"
              placeholder="Nhập mức lương" class="field-input" :class="{ 'p-invalid': errors.salary_level }" />
            <small v-if="errors.salary_level" class="p-error">{{ errors.salary_level[0] }}</small>
          </div>
        </div>

        <!-- Phụ cấp -->
        <div class="dynamic-section">
          <div class="section-header">
            <h5 class="section-title">Phụ cấp</h5>
            <InputSwitch v-model="formData.enable_allowance" />
          </div>

          <div v-if="formData.enable_allowance" class="dynamic-list">
            <div v-for="(allowance, index) in formData.allowances" :key="index" class="dynamic-item">
              <div class="dynamic-item-fields">
                <InputText v-model="allowance.name" placeholder="Tên phụ cấp" style="flex: 2" />
                <InputNumber v-model="allowance.amount" mode="currency" currency="VND" locale="vi-VN"
                  placeholder="Số tiền" style="flex: 2" />
                <Dropdown v-model="allowance.type" :options="allowanceTypeOptions" optionLabel="label"
                  optionValue="value" placeholder="Loại" style="flex: 2" />
                <Button icon="pi pi-trash" class="p-button-danger p-button-text" @click="removeAllowance(index)" />
              </div>
            </div>
            <Button label="Thêm phụ cấp" icon="pi pi-plus" class="p-button-sm" @click="addAllowance" />
          </div>
        </div>

        <!-- Thưởng chỉ tiêu -->
        <div class="dynamic-section">
          <div class="section-header">
            <h5 class="section-title">Thưởng chỉ tiêu</h5>
            <InputSwitch v-model="formData.enable_target_bonus" />
          </div>

          <div v-if="formData.enable_target_bonus" class="dynamic-list">
            <div v-for="(target, index) in formData.targets" :key="index" class="dynamic-item">
              <div class="dynamic-item-fields">
                <InputText v-model="target.activity_type" placeholder="Loại hoạt động" style="flex: 2" />
                <InputNumber v-model="target.target_amount" mode="currency" currency="VND" locale="vi-VN"
                  placeholder="Chỉ tiêu (X)" style="flex: 2" />
                <Dropdown v-model="target.bonus_type" :options="bonusTypeOptions" optionLabel="label"
                  optionValue="value" placeholder="Loại thưởng" style="flex: 1" />
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
            <h5 class="section-title">Giảm trừ</h5>
            <InputSwitch v-model="formData.enable_deduction" />
          </div>

          <div v-if="formData.enable_deduction" class="dynamic-list">
            <div v-for="(deduction, index) in formData.deductions" :key="index" class="dynamic-item">
              <div class="dynamic-item-fields">
                <InputText v-model="deduction.reason" placeholder="Lý do" style="flex: 2" />
                <InputNumber v-model="deduction.amount" mode="currency" currency="VND" locale="vi-VN"
                  placeholder="Số tiền" style="flex: 2" />
                <Dropdown v-model="deduction.frequency" :options="deductionFrequencyOptions" optionLabel="label"
                  optionValue="value" placeholder="Tần suất" style="flex: 2" />
                <Button icon="pi pi-trash" class="p-button-danger p-button-text" @click="removeDeduction(index)" />
              </div>
            </div>
            <Button label="Thêm giảm trừ" icon="pi pi-plus" class="p-button-sm" @click="addDeduction" />
          </div>
        </div>
      </TabPanel>
    </TabView>

    <template #footer>
      <div class="flex justify-end gap-2">
        <Button type="button" label="Hủy" severity="secondary" @click="closeModal" />
        <Button type="button" label="Lưu" @click="saveEmployee" :loading="loading" />
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
    Dialog,
    TabView,
    TabPanel,
    Button,
    InputText,
    InputNumber,
    Dropdown,
    Calendar,
    Textarea,
    InputSwitch,
    Password
  },
  props: {
    visible: {
      type: Boolean,
      default: false
    }
  },
  emits: ['close', 'created'],

  data() {
    return {
      loading: false,
      generating: false,
      generatingPassword: false,
      formData: {
        full_name: '',
        email: '',
        password: '',
        phone_number: '',
        employee_code: '',
        department_id: null,
        job_title_id: null,
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
      },
      resources: {
        departments: [],
        job_titles: [],
        branches: []
      },
      errors: {},
      genderOptions: [
        { label: 'Nam', value: 'nam' },
        { label: 'Nữ', value: 'nữ' }
      ],
      salaryTypeOptions: [
        { label: 'Lương cố định', value: 'fixed' },
      ],
      allowanceTypeOptions: [
        { label: 'Cố định/Ngày', value: 'fixed_daily' },
        { label: 'Cố định/Tháng', value: 'fixed_monthly' },
        { label: '% Lương', value: 'percent_salary' }
      ],
      bonusTypeOptions: [
        { label: 'Cố định', value: 'fixed' },
        { label: '% Doanh thu', value: 'percent' }
      ],
      deductionFrequencyOptions: [
        { label: 'Một lần', value: 'one-time' },
        { label: 'Hàng tháng', value: 'monthly' }
      ]
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

        // Set mặc định chức vụ = "Nhân Viên" (nếu chưa có giá trị)
        if (!this.formData.job_title_id && this.resources.job_titles) {
          const nhanVien = this.resources.job_titles.find(
            jt => jt.name.toLowerCase() === 'nhân viên' ||
              jt.name.toLowerCase() === 'nhan vien'
          )
          if (nhanVien) {
            this.formData.job_title_id = nhanVien.id
          }
        }

      } catch (error) {
        console.error('Error loading resources:', error)
        this.$toast.add({
          severity: 'error',
          summary: 'Lỗi',
          detail: 'Không thể tải dữ liệu phòng ban, chức vụ, chi nhánh',
          life: 3000
        })
      }
    },

    async generateCode() {
      this.generating = true
      try {
        const response = await axios.get('/admin/employees/generate/code')
        this.formData.employee_code = response.data.employee_code
      } catch (error) {
        console.error('Error generating code:', error)
        this.$toast.add({
          severity: 'error',
          summary: 'Lỗi',
          detail: 'Không thể tạo mã nhân viên',
          life: 3000
        })
      } finally {
        this.generating = false
      }
    },

    generatePassword() {
      this.generatingPassword = true
      // Tạo password ngẫu nhiên: 8 ký tự gồm chữ hoa, chữ thường, số
      const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'
      let password = ''
      for (let i = 0; i < 8; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length))
      }
      this.formData.password = password
      this.generatingPassword = false
      
      this.$toast.add({
        severity: 'info',
        summary: 'Thông báo',
        detail: 'Mật khẩu đã được tạo tự động',
        life: 2000
      })
    },

    // Dynamic arrays methods
    addAllowance() {
      this.formData.allowances.push({ name: '', amount: 0, type: 'fixed_monthly' })
    },

    removeAllowance(index) {
      this.formData.allowances.splice(index, 1)
    },

    addTarget() {
      this.formData.targets.push({ activity_type: '', target_amount: 0, bonus_type: 'fixed', bonus_value: 0 })
    },

    removeTarget(index) {
      this.formData.targets.splice(index, 1)
    },

    addDeduction() {
      this.formData.deductions.push({ reason: '', amount: 0, frequency: 'monthly' })
    },

    removeDeduction(index) {
      this.formData.deductions.splice(index, 1)
    },

    async saveEmployee() {
      this.loading = true
      this.errors = {}

      try {
        // Format dates
        const formData = { ...this.formData }
        if (formData.start_date) {
          formData.start_date = this.formatDate(formData.start_date)
        }
        if (formData.dob) {
          formData.dob = this.formatDate(formData.dob)
        }

        // Remove empty arrays if toggle off
        if (!formData.enable_allowance) formData.allowances = []
        if (!formData.enable_target_bonus) formData.targets = []
        if (!formData.enable_deduction) formData.deductions = []

        // Đảm bảo các field nullable được gửi đi (kể cả null)
        // Convert null thành string 'null' hoặc giữ nguyên null
        const payload = {
          ...formData,
          // Đảm bảo branch_id được gửi đi (kể cả null)
          branch_id: formData.branch_id !== undefined ? formData.branch_id : null,
          department_id: formData.department_id !== undefined ? formData.department_id : null,
          job_title_id: formData.job_title_id !== undefined ? formData.job_title_id : null,
        }

        // Debug: Kiểm tra dữ liệu trước khi gửi
        console.log('Form data before submit:', payload)
        console.log('Branch ID:', payload.branch_id)

        const response = await axios.post('/admin/employees', payload)

        // Kiểm tra response.success thay vì chỉ check response
        if (response.data && response.data.success) {
          this.$toast.add({
            severity: 'success',
            summary: 'Thành công',
            detail: response.data.message || 'Đã thêm nhân viên thành công',
            life: 3000
          })

          this.$emit('created', response.data.employee || response.data)
          this.closeModal()
        } else {
          throw new Error(response.data?.message || 'Có lỗi xảy ra')
        }

      } catch (error) {
        console.error('Error creating employee:', error)
        console.error('Error response:', error.response)

        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors
          this.$toast.add({
            severity: 'error',
            summary: 'Lỗi validation',
            detail: 'Vui lòng kiểm tra lại thông tin nhập vào',
            life: 5000
          })
        } else {
          this.$toast.add({
            severity: 'error',
            summary: 'Lỗi',
            detail: error.response?.data?.message || error.message || 'Có lỗi xảy ra khi thêm nhân viên',
            life: 5000
          })
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
        full_name: '',
        email: '',
        password: '',
        phone_number: '',
        employee_code: '',
        department_id: null,
        job_title_id: null,
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
      }
      this.errors = {}
    }
  }
}
</script>

<style scoped>
/* Form Grid */
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-bottom: 20px;
}

.salary-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-bottom: 20px;
}

.form-field {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.address-field {
  grid-column: 1 / -1;
}

.field-label {
  font-weight: 600;
  font-size: 14px;
  color: #333;
}

.required {
  color: #ef4444;
}

.field-input {
  width: 100%;
  border-radius: 6px;
  font-size: 14px;
}

.field-textarea {
  width: 100%;
  border-radius: 6px;
  font-size: 14px;
}

.input-group {
  display: flex;
  gap: 8px;
}

.input-group .field-input {
  flex: 1;
}

.generate-btn {
  flex-shrink: 0;
}

/* Dynamic sections */
.dynamic-section {
  margin-bottom: 24px;
  padding: 16px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: #f9fafb;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.section-title {
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
  background: white;
  border-radius: 6px;
  border: 1px solid #e5e7eb;
}

.dynamic-item-fields {
  display: flex;
  gap: 8px;
  align-items: center;
}

/* Error */
.p-error {
  color: #ef4444;
  font-size: 12px;
}

.p-invalid {
  border-color: #ef4444 !important;
}

.text-muted {
  color: #6b7280;
  font-size: 12px;
  margin-top: 4px;
}

/* Footer */
.flex {
  display: flex;
}

.justify-end {
  justify-content: flex-end;
}

.gap-2 {
  gap: 8px;
}
</style>