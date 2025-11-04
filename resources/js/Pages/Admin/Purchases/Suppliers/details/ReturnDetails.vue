<template>
  <Dialog 
    v-model:visible="dialogVisible" 
    :header="`Chi tiết phiếu nhập hàng  ${importData?.Code || ''}`" 
    :style="{ width: '1100px' , height: '300px' }" 
    :modal="true"
    :closable="true" 
    @hide="$emit('update:visible', false)">

    <div v-if="returnData" class="mb-3">
      <div class="d-flex gap-3 mb-3">
        <div>
          <strong>Mã phiếu:</strong> {{ returnData.Code }}
        </div>
        <div>
          <strong>Ngày nhập:</strong> {{ returnData.DayReturn }}
        </div>
      </div>
    </div>
    <DataTable style="margin-top: 30px;"
      :value="products" 
      tableStyle="min-width: 50rem" 
      :loading="loading" 
      loadingIcon="pi pi-spinner"
      emptyMessage="Chưa có sản phẩm">
      <!-- Bỏ cột "Mã phiếu" vì đã hiển thị ở trên -->
      <Column field="code" header="Mã sản phẩm"></Column>
      <Column field="name" header="Tên sản phẩm"></Column>
      <Column field="category" header="Loại sản phẩm"></Column>
      <Column field="quantity" header="Số lượng"></Column>
      <Column field="total_price" header="Thành tiền">
        <template #body="slotProps">
          {{ formatCurrency(slotProps.data.total_price) }}
        </template>
      </Column>
    </DataTable>
  </Dialog>
</template>

<script>
import Dialog from 'primevue/dialog'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import axios from 'axios'

export default {
  name: 'ReturnDetails',
  components: {
    Dialog,
    DataTable,
    Column
  },
  props: {
    visible: {
      type: Boolean,
      default: false
    },
    returnData: {
      type: Object,
      default: null
    }
  },
  data() {
    return {
      products: [],
      loading: false
    }
  },
  computed: {
    dialogVisible: {
      get() {
        return this.visible
      },
      set(value) {
        this.$emit('update:visible', value)
      }
    }
  },
  watch: {
    visible(newVal) {
      if (newVal && this.returnData) {
        this.loadProducts()
      } else {
        this.products = []
      }
    }
  },
  methods: {
    async loadProducts() {
      if (!this.returnData || !this.returnData.Code) {
        return
      }

      this.loading = true
      try {
        const response = await axios.get(`/admin/returns/${this.returnData.Code}/items`)

        if (response.data.success) {
          this.products = response.data.data || []
        } else {
          console.error('Error loading return items:', response.data.message)
          this.products = []
        }
      } catch (error) {
        console.error('Error fetching return items:', error)
        this.products = []
      } finally {
        this.loading = false
      }
    },
    formatCurrency(value) {
      if (!value) return '0 đ'
      return new Intl.NumberFormat('vi-VN').format(value) + ' đ'
    }
  }
}
</script>

<style scoped>
/* CSS cho DataTable nếu cần */
</style>