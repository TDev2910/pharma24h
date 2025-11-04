<template>
  <div class="employees-page">
    <!-- Header Control Bar -->
    <div class="header-control-bar">
      <div class="controls-section" style="width:100%; display:flex; align-items:center; justify-content:center; gap:16px; flex-wrap:wrap;">
        <!-- Title Section -->
        <div class="title-section">
          <h4>Quản lý nhân viên</h4>
        </div>
        
        <!-- Search Section -->
        <div style="flex:1; display:flex; justify-content:center;">
          <div class="search-wrapper">
            <div class="input-group">
              <span class="input-group-text">
                <i class="pi pi-search"></i>
              </span>
              <input 
                type="text" 
                class="form-control" 
                style="border-radius:8px;" 
                placeholder="Tìm theo tên, mã nhân viên" 
                v-model="searchQuery"
                @input="debounceSearch"
              >
            </div>
          </div>
        </div>
        
        <!-- Utility Options -->
        <div class="ultility-options">
          <Button 
            icon="pi pi-plus"
            label="Nhân viên"
            @click="showCreateModal = true"
            severity="secondary"
            style="background:#0b1020; border:none; color:white; font-weight:600; padding:6px 18px; border-radius:8px;"
          />
        </div>
      </div>
    </div>

    <!-- DataTable -->
    <div class="table-container">
      <DataTable 
        :value="employees.data" 
        stripedRows
        responsiveLayout="scroll"
        tableStyle="min-width: 50rem"
        :paginator="true"
        :rows="employees.per_page"
        :totalRecords="employees.total"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[10,15,25,50]"
        currentPageReportTemplate="Hiển thị {first} đến {last} trong tổng số {totalRecords} nhân viên"
        dataKey="id"
        emptyMessage="Không có dữ liệu nhân viên"
      >
        <Column field="employee_code" header="Mã NV" style="width: 100px"></Column>
        <Column field="full_name" header="Họ tên"></Column>
        <Column field="phone_number" header="Điện thoại"></Column>
        <Column field="department.name" header="Phòng ban">
          <template #body="slotProps">
            {{ slotProps.data.department?.name || '-' }}
          </template>
        </Column>
        <Column field="position.name" header="Chức vụ">
          <template #body="slotProps">
            {{ slotProps.data.position?.name || '-' }}
          </template>
        </Column>
        <Column field="branch.name" header="Chi nhánh">
          <template #body="slotProps">
            {{ slotProps.data.branch?.name || '-' }}
          </template>
        </Column>
        <Column field="salary_level" header="Lương cơ bản">
          <template #body="slotProps">
            {{ formatCurrency(slotProps.data.salary_level) }}
          </template>
        </Column>
        <Column header="Thao tác" style="width: 150px">
          <template #body="slotProps">
            <div style="display:flex; gap:8px;">
              <Button 
                icon="pi pi-pencil" 
                class="p-button-sm p-button-warning"
                @click="editEmployee(slotProps.data)"
                title="Sửa"
              />
              <Button 
                icon="pi pi-calendar" 
                class="p-button-sm p-button-info"
                @click="scheduleEmployee(slotProps.data)"
                title="Lập lịch"
              />
              <Button 
                icon="pi pi-trash" 
                class="p-button-sm p-button-danger"
                @click="confirmDelete(slotProps.data)"
                title="Xóa"
              />
            </div>
          </template>
        </Column>
      </DataTable>
    </div>

    <!-- Create Employee Modal -->
    <CreateEmployeeModal 
      v-model:visible="showCreateModal"
      @created="handleEmployeeCreated"
    />

    <!-- Edit Employee Modal -->
    <EditEmployeeModal 
      v-model:visible="showEditModal"
      :employee="selectedEmployee"
      @updated="handleEmployeeUpdated"
    />

    <!-- Schedule Modal -->
    <ScheduleModal 
      v-model:visible="showScheduleModal"
      :employee="selectedEmployee"
    />

    <!-- Delete Confirmation Dialog -->
    <Dialog 
      v-model:visible="showDeleteDialog" 
      header="Xác nhận xóa" 
      :modal="true"
      :style="{ width: '400px' }"
    >
      <div class="confirmation-content">
        <i class="pi pi-exclamation-triangle" style="font-size: 2rem; color: #f59e0b"></i>
        <span style="margin-left: 1rem">
          Bạn có chắc chắn muốn xóa nhân viên <strong>{{ selectedEmployee?.full_name }}</strong>?
        </span>
      </div>
      <template #footer>
        <Button label="Hủy" icon="pi pi-times" @click="showDeleteDialog = false" class="p-button-text" />
        <Button label="Xóa" icon="pi pi-check" @click="deleteEmployee" class="p-button-danger" :loading="deleting" />
      </template>
    </Dialog>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import CreateEmployeeModal from './Modals/Create.vue';
import EditEmployeeModal from './Modals/Edit.vue';
import ScheduleModal from './Modals/Schedule.vue';

// Props
const props = defineProps({
  employees: {
    type: Object,
    required: true
  },
  filters: {
    type: Object,
    default: () => ({})
  }
});

// State
const searchQuery = ref(props.filters.search || '');
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showScheduleModal = ref(false);
const showDeleteDialog = ref(false);
const selectedEmployee = ref(null);
const deleting = ref(false);

// Methods
const debounceSearch = (() => {
  let timeout;
  return () => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
      router.get(route('admin.employees.index'), {
        search: searchQuery.value
      }, {
        preserveState: true,
        preserveScroll: true
      });
    }, 500);
  };
})();

const formatCurrency = (value) => {
  if (!value) return '0 ₫';
  return new Intl.NumberFormat('vi-VN', { 
    style: 'currency', 
    currency: 'VND' 
  }).format(value);
};

const editEmployee = (employee) => {
  selectedEmployee.value = employee;
  showEditModal.value = true;
};

const scheduleEmployee = (employee) => {
  selectedEmployee.value = employee;
  showScheduleModal.value = true;
};

const confirmDelete = (employee) => {
  selectedEmployee.value = employee;
  showDeleteDialog.value = true;
};

const deleteEmployee = async () => {
  deleting.value = true;
  router.delete(route('admin.employees.destroy', selectedEmployee.value.id), {
    onSuccess: () => {
      showDeleteDialog.value = false;
      selectedEmployee.value = null;
    },
    onFinish: () => {
      deleting.value = false;
    }
  });
};

const handleEmployeeCreated = () => {
  showCreateModal.value = false;
  router.reload({ only: ['employees'] });
};

const handleEmployeeUpdated = () => {
  showEditModal.value = false;
  selectedEmployee.value = null;
  router.reload({ only: ['employees'] });
};
</script>

<style scoped>
.employees-page {
  padding: 20px;
}

.header-control-bar {
  background: white;
  border-radius: 8px;
  padding: 16px;
  margin-bottom: 20px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.title-section h4 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
  color: #0b1020;
}

.search-wrapper {
  width: 100%;
  max-width: 400px;
}

.input-group {
  display: flex;
  align-items: center;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  overflow: hidden;
}

.input-group-text {
  background: #f9fafb;
  padding: 8px 12px;
  border: none;
}

.form-control {
  border: none;
  padding: 8px 12px;
  outline: none;
  flex: 1;
}

.table-container {
  background: white;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.confirmation-content {
  display: flex;
  align-items: center;
  padding: 20px 0;
}

.avatar-image {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}
</style>
