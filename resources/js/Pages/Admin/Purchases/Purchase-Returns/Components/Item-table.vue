<template>
  <div class="card shadow-sm" style="margin-left: -19px;">
    <div class="card-body p-0">
      <!-- DataTable -->
      <DataTable 
        :value="items" 
        stripedRows
        responsiveLayout="scroll"
        tableStyle="min-width: 50rem"
        dataKey="id"
        loadingIcon="pi pi-spinner"
        emptyMessage="Chưa có sản phẩm nào được thêm"
        :loading="isImporting">
        
        <Column field="stt" header="STT" style="width:60px;">
          <template #body="slotProps">
            {{ slotProps.index + 1 }}
          </template>
        </Column>
        
        <Column field="ma_hang" header="Mã hàng" style="min-width:140px;"></Column>
        <Column field="ten_hang" header="Tên hàng" style="min-width:240px;"></Column>
        <Column field="don_vi_tinh" header="ĐVT" style="width:90px;"></Column>
        
        <Column field="so_luong" header="Số lượng" style="width:120px;">
          <template #body="slotProps">
            <InputNumber 
              v-model="slotProps.data.so_luong" 
              :min="1" 
              size="small"
              style="width: 80px;"
              @input="updateRowTotal(slotProps.data)"
            />
          </template>         
        </Column>
        
        <Column field="don_gia" header="Giá nhập" style="width:140px;">
          <template #body="slotProps">
            <InputNumber 
              v-model="slotProps.data.don_gia" 
              :min="0" 
              :step="0.01"
              size="small"
              @input="updateRowTotal(slotProps.data)"
            />
          </template>         
        </Column>

        <Column field="don_gia" header="Giá trả lại" style="width:140px;">
          <template #body="slotProps">
            <InputNumber 
              v-model="slotProps.data.don_gia" 
              :min="0" 
              :step="0.01"
              size="small" style="width: 50px;"
              @input="updateRowTotal(slotProps.data)"
            />
          </template>         
        </Column>
        
        <Column field="thanh_tien" header="Thành tiền" style="width:150px; min-width:150px; max-width:130px;" class="text-end">
          <template #body="slotProps">
            <div style="width: 130px; text-align: right; white-space: nowrap;">
              {{ formatCurrency(slotProps.data.thanh_tien) }}
            </div>
          </template>
        </Column>
        
        <!-- Empty state template -->
        <template #empty>
          <div class="empty-state-container">
            <div class="empty-wrapper">
              <div class="fw-semibold mb-2" style="font-size:20px;color:#2b2f33;">
                Thêm sản phẩm từ file excel
              </div>
              <div class="text-muted mb-3" style="font-size:14px;">
                (Tải về file mẫu:
                <a href="#" class="ms-1" style="text-decoration:underline;">Excel file</a>)
              </div>
              <button 
                type="button" 
                @click="selectFile" 
                class="btn btn-primary btn-lg" 
                style="background:#0b1020; border:none; color:white; font-weight:600; padding:6px 18px; border-radius:8px;"
              >
                Chọn file dữ liệu
              </button>
            </div>
          </div>
        </template>
        
        <!-- Loading template -->
        <template #loading>
          <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Đang xử lý...</span>
            </div>
            <div class="mt-2">Đang xử lý file Excel...</div>
          </div>
        </template>
      </DataTable>
    </div>
    
    <!-- Hidden file input -->
    <input 
      ref="fileInput" 
      type="file" 
      accept=".xlsx,.xls,.csv" 
      style="display: none;" 
      @change="handleFileSelect"
    />
  </div>
</template>

<script>
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import InputNumber from 'primevue/inputnumber'

export default {
  name: 'ItemTable',
  
  components: {
    DataTable,
    Column,
    InputNumber
  },
  
  data() {
    return {
      items: [],
      isImporting: false
    }
  },

  methods: {
    selectFile() {
      this.$refs.fileInput.click()
    },

    handleFileSelect(event) {
      const file = event.target.files[0]
      if (file && !this.isImporting) {
        this.isImporting = true
        this.uploadExcelFile(file)
      }
    },

    uploadExcelFile(file) {
      console.log('Starting upload for file:', file.name)
      
      const formData = new FormData()
      formData.append('excel_file', file)
      formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'))

      fetch('/admin/process-excel', {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.json())
      .then(data => {
        console.log('Data received:', data)
        if (data.success) {
          this.displayImportedItems(data.items)
          if (data.errors && data.errors.length > 0) {
            this.showErrors(data.errors)
          }
        } else {
          this.showError(data.message)
        }
      })
      .catch(error => {
        console.error('Error:', error)
        this.showError('Có lỗi xảy ra khi xử lý file')
      })
      .finally(() => {
        this.isImporting = false
        this.$refs.fileInput.value = ''
      })
    },

    displayImportedItems(items) {
      if (items.length === 0) {
        this.items = []
        return
      }

      // Thêm id cho mỗi item để DataTable có thể track
      this.items = items.map((item, index) => ({
        ...item,
        id: index + 1,
        thanh_tien: item.so_luong * item.don_gia
      }))
      
      this.updateSummary()
    },

    updateRowTotal(item) {
      // Tính lại thành tiền khi thay đổi số lượng hoặc giá
      item.thanh_tien = item.so_luong * item.don_gia
      this.updateSummary()
    },

    updateSummary() {
      // Emit event để parent component có thể cập nhật summary
      let total = 0
      this.items.forEach(item => {
        total += item.thanh_tien || 0
      })
      
      this.$emit('update-total', total)
      this.$emit('update-items', this.items) // Emit items data
    },

    showErrors(errors) {
      // Có thể hiển thị toast notification hoặc modal
      console.error('Import errors:', errors)
    },

    showError(message) {
      // Có thể hiển thị toast notification
      console.error('Import error:', message)
    },

    formatCurrency(value) {
      return new Intl.NumberFormat('vi-VN').format(value || 0)
    }
  },

  mounted() {
    // Khởi tạo với empty state
    this.items = []
  }
}
</script>

<style scoped>
.empty-state-container {
  height: 100%;
  padding: 0;
}

.empty-wrapper {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;    
  min-height: 615px;       
}

/* DataTable styling */
:deep(.p-datatable) {
  border: none;
  table-layout: fixed;
}

:deep(.p-datatable .p-datatable-header) {
  background: transparent;
  border: none;
  padding: 0;
}

:deep(.p-datatable .p-datatable-thead > tr > th) {
  background: #f8f9fa;
  border: 1px solid #dee2e6;
  font-weight: 600;
  color: #495057;
  overflow: hidden;
  text-overflow: ellipsis;
}

:deep(.p-datatable .p-datatable-tbody > tr > td) {
  border: 1px solid #dee2e6;
  padding: 0.75rem;
}

:deep(.p-datatable .p-datatable-tbody > tr:nth-child(even)) {
  background: #f8f9fa;
}

:deep(.p-datatable .p-datatable-tbody > tr:hover) {
  background: #e9ecef;
}

/* InputNumber styling */
:deep(.p-inputnumber) {
  width: 100%;
}

:deep(.p-inputnumber .p-inputnumber-input) {
  border: 1px solid #ced4da;
  border-radius: 0.375rem;
  padding: 0.375rem 0.75rem;
  font-size: 0.875rem;
}

:deep(.p-inputnumber .p-inputnumber-input:focus) {
  border-color: #86b7fe;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
</style>
