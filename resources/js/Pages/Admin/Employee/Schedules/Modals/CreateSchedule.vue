<template>
  <div class="create-schedule-modal">
    <form @submit.prevent="saveSchedule">
      <!-- Employee Info (Read-only) -->
      <div class="form-field">
        <label class="field-label">Nhân viên</label>
        <InputText :value="employeeDisplay" disabled class="field-input" />
      </div>

      <!-- Date -->
      <div class="form-field">
        <label class="field-label">Ngày làm việc</label>
        <Calendar v-model="formData.schedule_date" dateFormat="dd/mm/yy" :disabled="!!schedule" class="field-input"
          :class="{ 'p-invalid': errors.schedule_date }" />
        <small v-if="errors.schedule_date" class="p-error">{{ errors.schedule_date[0] }}</small>
      </div>

      <!-- ca làm việc -->
      <div class="form-field">
        <label class="field-label">Ca làm việc <span class="required">*</span></label>
        <Dropdown v-model="formData.shift_id" :options="shifts" optionLabel="name" optionValue="id"
          placeholder="Chọn ca làm việc" class="field-input" :class="{ 'p-invalid': errors.shift_id }" />
        <small v-if="errors.shift_id" class="p-error">{{ errors.shift_id[0] }}</small>
      </div>

      <!-- Notes -->
      <div class="form-field">
        <label class="field-label">Ghi chú</label>
        <Textarea v-model="formData.notes" rows="3" placeholder="Nhập ghi chú (nếu có)" class="field-input"
          :class="{ 'p-invalid': errors.notes }" />
        <small v-if="errors.notes" class="p-error">{{ errors.notes[0] }}</small>
      </div>

      <!-- Actions -->
      <div class="form-actions">
        <Button label="Hủy" @click="$emit('cancel')" class="p-button-text" :disabled="loading" />
        <Button type="submit" :label="schedule ? 'Cập nhật' : 'Thêm lịch'" :loading="loading" :disabled="loading" />
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
.create-schedule-modal {
  padding: 8px;
}

.form-field {
  margin-bottom: 20px;
}

.field-label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #333;
}

.field-label .required {
  color: #e74c3c;
}

.field-input {
  width: 100%;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
  margin-top: 24px;
  padding-top: 16px;
  border-top: 1px solid #e0e0e0;
}
</style>
