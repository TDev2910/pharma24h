<template>
  <div class="doctors-page">
    <h1>Xin chào!</h1>
    <p>Đây là trang quản lý bác sĩ</p>
    
    <!-- Utility Options -->
    <div class="ultility-options">
      <!-- Thêm bác sĩ -->
      <div class="text-end pb-4">
        <Button 
          icon="pi pi-plus"
          label="Bác sĩ"
          @click="showCreateModal"
          severity="secondary"
          style="margin-top: 20px;background:#0b1020; border:none; color:white; font-weight:600; padding:6px 18px; border-radius:8px;"
        />
      </div>
    </div>
    
    <!-- Create Doctor Modal -->
    <CreateDoctorModal 
      :visible="showModal"
      @close="closeModal"
      @created="handleDoctorCreated"
    />
  </div>
</template>

<script>
import ButtonGroup from 'primevue/buttongroup'
import Button from 'primevue/button'
import CreateDoctorModal from './Modals/Create.vue'

export default {
  name: 'DoctorIndex',
  components: {
    ButtonGroup,
    Button,
    CreateDoctorModal
  },
  data() {
    return {
      showModal: false
    }
  },
  methods: {
    showCreateModal() {
      this.showModal = true
    },
    closeModal() {
      this.showModal = false
    },
    handleDoctorCreated(doctor) {
      // Xử lý khi tạo bác sĩ thành công
      console.log('Doctor created:', doctor)
      this.closeModal()
      // Có thể reload danh sách hoặc thêm vào state
    }
  }
}
</script>
  
<style scoped>
.doctors-page {
  padding: 20px;
}

h1 {
  color: #2c3e50;
  margin-bottom: 16px;
}


</style>