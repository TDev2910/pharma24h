<template>
  <div class="create-schedule-modal p-fluid">
    <form @submit.prevent="saveSchedule">

      <div class="employee-card">
        <div class="employee-icon">
          <i class="pi pi-user"></i>
        </div>
        <div class="employee-details">
          <label class="info-label">Nhân viên được phân công</label>
          <div class="info-value">{{ employeeDisplay }}</div>
        </div>
      </div>

      <div class="form-grid">
        <div class="form-field">
          <label class="field-label">Ngày làm việc</label>
          <Calendar
            v-model="formData.schedule_date"
            dateFormat="dd/mm/yy"
            :disabled="!!schedule"
            showIcon
            placeholder="Chọn ngày"
            :class="{ 'p-invalid': errors.schedule_date }"
          />
          <small v-if="errors.schedule_date" class="p-error">{{ errors.schedule_date[0] }}</small>
        </div>

        <div class="form-field">
          <label class="field-label">Ca làm việc <span class="required">*</span></label>
          <Dropdown
            v-model="formData.shift_id"
            :options="shifts"
            optionLabel="name"
            optionValue="id"
            placeholder="-- Chọn ca --"
            :class="{ 'p-invalid': errors.shift_id }"
          />
          <small v-if="errors.shift_id" class="p-error">{{ errors.shift_id[0] }}</small>
        </div>
      </div>

      <div class="form-field">
        <label class="field-label">Ghi chú</label>
        <Textarea
          v-model="formData.notes"
          rows="4"
          placeholder="Nhập ghi chú công việc (nếu có)..."
          :class="{ 'p-invalid': errors.notes }"
          autoResize
        />
        <small v-if="errors.notes" class="p-error">{{ errors.notes[0] }}</small>
      </div>

      <div class="form-actions">
        <Button label="Hủy Bỏ" icon="pi pi-times" @click="$emit('cancel')" class="p-button-text p-button-secondary" :disabled="loading" />
        <Button type="submit" :label="schedule ? 'Lưu Thay Đổi' : 'Tạo Lịch'" icon="pi pi-check" :loading="loading" :disabled="loading" />
      </div>
    </form>
  </div>
</template>

<script>
import axios from 'axios'
import Calendar from 'primevue/calendar'
import Dropdown from 'primevue/dropdown'
import InputText from 'primevue/inputtext' 
import Textarea from 'primevue/textarea'
import Button from 'primevue/button'

export default {
  name: 'CreateScheduleModal',
  components: {
    Calendar,
    Dropdown,
    InputText,
    Textarea,
    Button
  },
  props: {
    employee: {
      type: Object,
      required: true
    },
    scheduleDate: {
      type: String,
      default: null
    },
    schedule: {
      type: Object,
      default: null
    }
  },
  data() {
    return {
      loading: false,
      errors: {}, //lỗi validation
      shifts: [], //danh sách ca làm việc
      formData: {
        employee_id: null,
        shift_id: null,
        schedule_date: null,
        notes: ''
      }
    }
  },
  computed: {
    employeeDisplay() {
      if (!this.employee) return ''
      return `${this.employee.full_name} (${this.employee.employee_code})`
    }
  },
  mounted() {
    this.loadShifts()
    this.initializeForm()
  },
  methods: {
    initializeForm() {
      if (this.schedule) {
        // Edit mode
        this.formData = {
          employee_id: this.schedule.employee_id || this.employee.id,
          shift_id: this.schedule.shift_id || this.schedule.shift?.id,
          schedule_date: this.schedule.schedule_date ? new Date(this.schedule.schedule_date) : null,
          notes: this.schedule.notes || ''
        }
      } else {
        // Create mode
        this.formData = {
          employee_id: this.employee.id,
          shift_id: null,
          schedule_date: this.scheduleDate ? new Date(this.scheduleDate) : new Date(),
          notes: ''
        }
      }
    },
    async loadShifts() {
      try {
        const response = await axios.get('/admin/shifts/api')
        this.shifts = response.data || []
      } catch (error) {
        console.error('Error loading shifts:', error)
        this.$toast.add({
          severity: 'error',
          summary: 'Lỗi',
          detail: 'Không thể tải danh sách ca làm việc',
          life: 3000
        })
      }
    },

    //Lưu lịch làm việc
    async saveSchedule() {
      this.loading = true
      this.errors = {}

      try {
        const payload =
        {
          employee_id: this.formData.employee_id,
          shift_id: this.formData.shift_id,
          schedule_date: this.formatDate(this.formData.schedule_date),
          notes: this.formData.notes || null
        }

        let response
        if (this.schedule && this.schedule.id) {
          // Update
          response = await axios.put(`/admin/employee-schedules/${this.schedule.id}`, payload)
        }
        else {
          // Create
          response = await axios.post('/admin/employee-schedules', payload)
        }

        this.$toast.add({
          severity: 'success',
          summary: 'Thành công',
          detail: this.schedule ? 'Cập nhật lịch làm việc thành công!' : 'Thêm lịch làm việc thành công!',
          life: 3000
        })

        this.$emit('saved', response.data)
      } catch (error) {
        console.error('Error saving schedule:', error)

        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors || {}
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
            detail: error.response?.data?.message || error.message || 'Có lỗi xảy ra',
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
    }
  }
}
</script>

<style scoped>
/* Tổng thể Modal */
.create-schedule-modal {
  padding: 0 0.5rem;
}

/* Thẻ thông tin nhân viên (Card style) */
.employee-card {
  display: flex;
  align-items: center;
  background-color: #f8f9fa;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  padding: 12px 16px;
  margin-bottom: 24px;
}

.employee-icon {
  width: 40px;
  height: 40px;
  background-color: #e3f2fd;
  color: #2196f3;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 12px;
  font-size: 1.2rem;
}

.employee-details {
  display: flex;
  flex-direction: column;
}

.info-label {
  font-size: 0.8rem;
  color: #6c757d;
  margin-bottom: 2px;
}

.info-value {
  font-weight: 700;
  color: #2c3e50;
  font-size: 1rem;
}

/* Grid layout cho Ngày và Ca */
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 20px;
}

/* Form Fields */
.form-field {
  display: flex;
  flex-direction: column;
  margin-bottom: 0; /* Margin xử lý ở grid hoặc wrapper */
}

/* Xử lý trường Ghi chú đứng riêng */
.form-field:not(.form-grid .form-field) {
  margin-bottom: 20px;
}

.field-label {
  display: block;
  margin-bottom: 8px;
  font-weight: 500;
  color: #34495e;
  font-size: 0.95rem;
}

.field-label .required {
  color: #e74c3c;
  margin-left: 4px;
}

/* Actions */
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 32px;
  padding-top: 20px;
  border-top: 1px solid #f1f1f1;
}

/* Responsive cho màn hình nhỏ */
@media screen and (max-width: 576px) {
  .form-grid {
    grid-template-columns: 1fr;
    gap: 16px;
  }
}
</style>
