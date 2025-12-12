<template>
  <div class="schedule-page">
    <!-- Header -->
    <div class="schedule-header">
      <div class="header-left">
        <h2 class="page-title">Lịch làm việc</h2>
      </div>

      <div class="header-center">
        <!-- Search -->
        <div class="search-wrapper">
          <div class="input-group">
            <span class="input-group-text">
              <i class="pi pi-search"></i>
            </span>
            <input type="text" class="form-control" placeholder="Theo mã, tên nhân viên, điện thoại"
              v-model="searchQuery" @input="debounceSearch">
          </div>
        </div>

        <!-- Week Navigation -->
        <div class="week-navigation">
          <Button icon="pi pi-chevron-left" @click="previousWeek" class="p-button-text p-button-rounded"
            :disabled="loading" />
          <div class="week-display">
            <span class="week-text">{{ weekDisplayText }}</span>
            <Button label="Tuần này" @click="goToCurrentWeek" class="p-button-text p-button-sm" :disabled="loading" />
          </div>
          <Button icon="pi pi-chevron-right" @click="nextWeek" class="p-button-text p-button-rounded"
            :disabled="loading" />
        </div>
      </div>
    </div>

    <!-- Hiển thị danh sách lịch làm việc -->
    <div class="schedule-grid-container" v-if="!loading && scheduleData.length > 0">
      <div class="schedule-table">
        <!-- Header Row -->
        <div class="schedule-row schedule-header-row">
          <div class="schedule-cell employee-col">Nhân viên</div>
          <div v-for="day in weekDays" :key="day.date" class="schedule-cell day-col" :class="{ 'today': day.isToday }">
            <div class="day-header">
              <div class="day-name">{{ day.dayName }}</div>
              <div class="day-number" :class="{ 'today-circle': day.isToday }">{{ day.dayNumber }}</div>
            </div>
          </div>
          <div class="schedule-cell salary-col">Lương dự kiến</div>
        </div>

        <!-- Employee Rows -->
        <div v-for="item in filteredScheduleData" :key="item.employee.id" class="schedule-row">
          <!-- Employee Info -->
          <div class="schedule-cell employee-col">
            <div class="employee-info">
              <div class="employee-name">{{ item.employee.full_name }}</div>
              <div class="employee-code">{{ item.employee.employee_code }}</div>
            </div>
          </div>

          <!-- Day Cells -->
          <div v-for="day in weekDays" :key="day.date" class="schedule-cell day-col" :class="{ 'today': day.isToday }">
            <div class="day-content">
              <!-- Shift Blocks -->
              <div class="shift-blocks">
                <div v-for="schedule in getSchedulesForDay(item, day.date)" :key="schedule.id" class="shift-block"
                  :class="getShiftColorClass(schedule.shift)" @click="editSchedule(schedule, item.employee, day.date)">
                  {{ schedule.shift?.name || 'N/A' }}
                </div>
              </div>

              <!-- Add Schedule Button - Hiển thị khi chưa đạt giới hạn -->
              <button v-if="getSchedulesForDay(item, day.date).length < 3" class="add-schedule-btn"
                @click="openAddScheduleModal(item.employee, day.date)" title="Thêm lịch">
                + Thêm lịch
              </button>
            </div>
          </div>

          <!-- Estimated Salary -->
          <div class="schedule-cell salary-col">
            <div class="salary-info">
              <div class="salary-amount">{{ formatCurrency(item.estimated_salary) }}</div>
              <div class="shift-count">{{ item.shift_count }} ca</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="!loading && scheduleData.length === 0" class="empty-state">
      <i class="pi pi-calendar-times" style="font-size: 3rem; color: #ccc;"></i>
      <p>Chưa có lịch làm việc</p>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="loading-state">
      <ProgressSpinner />
      <p>Đang tải...</p>
    </div>

    <!-- Add/Edit Schedule Modal -->
    <Dialog v-model:visible="showScheduleModal" :header="scheduleModalTitle" :modal="true" :style="{ width: '500px' }"
      @hide="closeScheduleModal">
      <CreateScheduleModal v-if="showScheduleModal" :employee="selectedEmployee" :schedule-date="selectedDate"
        :schedule="editingSchedule" @saved="handleScheduleSaved" @cancel="closeScheduleModal" />
    </Dialog>
  </div>
</template>

<script>
import axios from 'axios'
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'
import ProgressSpinner from 'primevue/progressspinner'
import CreateScheduleModal from './Modals/CreateSchedule.vue'

export default {
  name: 'ScheduleIndex',
  components: {
    Button,
    Dialog,
    ProgressSpinner,
    CreateScheduleModal
  },
  data() {
    return {
      loading: false,
      searchQuery: '',
      weekStart: null,
      scheduleData: [],
      allEmployees: [],
      showScheduleModal: false,
      selectedEmployee: null,
      selectedDate: null,
      editingSchedule: null,
      searchTimeout: null,
    }
  },
  computed: {
    weekDays() {
      if (!this.weekStart) return []

      const days = []
      //mảng chứa 7 ngày trong tuần
      const dayNames = ['Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy']

      for (let i = 0; i < 7; i++) {
        const date = new Date(this.weekStart)
        date.setDate(date.getDate() + i)

        const dayOfWeek = date.getDay()
        const dayName = i === 0 ? 'Thứ hai' : i === 6 ? 'Chủ nhật' : dayNames[dayOfWeek]

        const today = new Date()
        today.setHours(0, 0, 0, 0)
        const checkDate = new Date(date)
        checkDate.setHours(0, 0, 0, 0)

        days.push({
          date: date.toISOString().split('T')[0], //format ngày tháng năm
          dayName: dayName, //tên ngày trong tuần
          dayNumber: date.getDate(), //số ngày trong tháng
          isToday: checkDate.getTime() === today.getTime() //kiểm tra ngày hiện tại
        })
      }

      return days
    },
    weekDisplayText() {
      if (!this.weekStart) return ''

      const start = new Date(this.weekStart) //bắt đầu từ ngày đầu tuần
      const end = new Date(this.weekStart) //kết thúc từ ngày cuối tuần
      end.setDate(end.getDate() + 6)

      const monthNames = ['Th. 1', 'Th. 2', 'Th. 3', 'Th. 4', 'Th. 5', 'Th. 6',
        'Th. 7', 'Th. 8', 'Th. 9', 'Th. 10', 'Th. 11', 'Th. 12']

      const weekNumber = this.getWeekNumber(start) //tính số tuần trong năm
      const month = monthNames[start.getMonth()] //tính số tháng trong năm
      const year = start.getFullYear()

      return `Tuần ${weekNumber} - ${month} ${year}`
    },
    //tìm kiếm nhân viên theo mã, tên, điện thoại, email
    filteredScheduleData() {
      if (!this.searchQuery || !this.searchQuery.trim()) {
        return this.scheduleData
      }

      const term = this.searchQuery.toLowerCase().trim()
      return this.scheduleData.filter(item => {
        const employee = item.employee
        const name = (employee.full_name || '').toLowerCase()
        const code = (employee.employee_code || '').toLowerCase()
        const phone = (employee.phone_number || '').toLowerCase()
        const email = (employee.user?.email || '').toLowerCase()

        return name.includes(term) || code.includes(term) || phone.includes(term) || email.includes(term)
      })
    },
    //thêm/sửa lịch
    scheduleModalTitle() {
      if (this.editingSchedule) {
        return 'Chỉnh sửa lịch làm việc'
      }
      return 'Thêm lịch làm việc'
    }
  },
  mounted() {
    //khởi tạo tuần hiện tại
    this.initializeWeek()
    this.loadSchedules()
    this.loadEmployees()
  },
  methods: {
    initializeWeek() {
      const today = new Date()
      const dayOfWeek = today.getDay()
      const diff = dayOfWeek === 0 ? -6 : 1 - dayOfWeek // Monday
      const monday = new Date(today)
      monday.setDate(today.getDate() + diff)
      monday.setHours(0, 0, 0, 0)

      this.weekStart = monday.toISOString().split('T')[0]
    },
    getWeekNumber(date) {
      const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()))
      const dayNum = d.getUTCDay() || 7
      d.setUTCDate(d.getUTCDate() + 4 - dayNum)
      const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1))
      return Math.ceil((((d - yearStart) / 86400000) + 1) / 7)
    },
    async loadSchedules() {
      this.loading = true
      try {
        const response = await axios.get('/admin/employee-schedules/api/weekly', {
          params: {
            week_start: this.weekStart
          }
        })

        this.scheduleData = response.data.employees || []

        // Debug: Kiểm tra dữ liệu
        console.log('Schedule data:', this.scheduleData)
        if (this.scheduleData.length > 0) {
          console.log('First employee schedules:', this.scheduleData[0].schedules)
        }
      } catch (error) {
        console.error('Error loading schedules:', error)
        this.$toast.add({
          severity: 'error',
          summary: 'Lỗi',
          detail: 'Không thể tải lịch làm việc',
          life: 3000
        })
      } finally {
        this.loading = false
      }
    },
    async loadEmployees() {
      try {
        const response = await axios.get('/admin/employees/api')
        this.allEmployees = response.data || []
      } catch (error) {
        console.error('Error loading employees:', error)
      }
    },
    // Search với debounce
    debounceSearch() {
      clearTimeout(this.searchTimeout)
      this.searchTimeout = setTimeout(() => {
        // Filter tự động qua computed property filteredScheduleData
      }, 200)
    },
    previousWeek() {
      const date = new Date(this.weekStart)
      date.setDate(date.getDate() - 7)
      this.weekStart = date.toISOString().split('T')[0]
      this.loadSchedules()
    },
    nextWeek() {
      const date = new Date(this.weekStart)
      date.setDate(date.getDate() + 7)
      this.weekStart = date.toISOString().split('T')[0]
      this.loadSchedules()
    },
    goToCurrentWeek() {
      this.initializeWeek()
      this.loadSchedules()
    },
    getSchedulesForDay(item, date) {
      // Đảm bảo schedules là object và có key là date string
      if (!item.schedules || typeof item.schedules !== 'object') {
        return []
      }

      // Lấy schedules cho ngày cụ thể
      const daySchedules = item.schedules[date]

      // Nếu là array thì return luôn, nếu không thì return []
      return Array.isArray(daySchedules) ? daySchedules : []
    },
    getShiftColorClass(shift) {
      if (!shift) return 'shift-default'

      const name = shift.name.toLowerCase()
      if (name.includes('sáng') || name.includes('morning')) {
        return 'shift-morning'
      } else if (name.includes('chiều') || name.includes('afternoon')) {
        return 'shift-afternoon'
      } else if (name.includes('tối') || name.includes('evening') || name.includes('night')) {
        return 'shift-evening'
      }

      return 'shift-default'
    },
    openAddScheduleModal(employee, date) {
      this.selectedEmployee = employee
      this.selectedDate = date
      this.editingSchedule = null
      this.showScheduleModal = true
    },
    editSchedule(schedule, employee, date) {
      this.selectedEmployee = employee
      this.selectedDate = date
      this.editingSchedule = schedule
      this.showScheduleModal = true
    },
    closeScheduleModal() {
      this.showScheduleModal = false
      this.selectedEmployee = null
      this.selectedDate = null
      this.editingSchedule = null
    },
    handleScheduleSaved() {
      this.closeScheduleModal()
      // Reload lại dữ liệu
      this.loadSchedules()
    },
    formatCurrency(amount) {
      if (!amount) return '0 ₫'
      return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
      }).format(amount)
    }
  }
}
</script>

<style>
/* Import CSS file - CSS thông thường được tách ra */
@import '@Admin/employee/schedules.css';
</style>
