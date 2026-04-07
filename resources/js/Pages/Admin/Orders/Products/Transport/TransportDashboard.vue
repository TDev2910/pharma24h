<template>
  <div class="dashboard-wrapper">
    <TransportSidebar 
      :filters="currentFilters"
      @update:filters="handleFilterChange"
    />

    <TransportList 
      :orders="orders"
      :pagination="pagination"
      :loading="isLoading"
      v-model:searchQuery="searchKeyword"
      @page-change="handlePageChange"
      @view-detail="handleViewDetail"
    />
  </div>
</template>

<script>
import { router } from '@inertiajs/vue3'
import TransportSidebar from './TransportSidebar.vue';
import TransportList from './TransportList.vue';

export default {
  name: 'TransportDashboard',
  components: {
    TransportSidebar,
    TransportList
  },
  props: {
    orders: {
      type: Object,
      default: () => ({ data: [] })
    },
    filters: {
      type: Object,
      default: () => ({
        status: '',
        partner: '',
        cod: 'all',
        search: ''
      })
    }
  },
  data() {
    return {
      currentFilters: {
        status: this.filters.status || '',
        partner: this.filters.partner || '',
        cod: this.filters.cod || 'all'
      },
      searchKeyword: this.filters.search || '',
      searchTimeout: null,
      isLoading: false
    }
  },
  computed: {
    pagination() {
      // Nếu orders là paginator từ Laravel
      if (this.orders.current_page) {
        return {
          current_page: this.orders.current_page,
          last_page: this.orders.last_page,
          per_page: this.orders.per_page,
          total: this.orders.total,
          from: this.orders.from,
          to: this.orders.to
        }
      }
      // Fallback
      return {
        current_page: 1,
        last_page: 1,
        per_page: 20,
        total: this.orders.data?.length || 0,
        from: 0,
        to: 0
      }
    }
  },
  watch: {
    searchKeyword(newValue) {
      // Debounce search
      clearTimeout(this.searchTimeout)
      this.searchTimeout = setTimeout(() => {
        this.reloadData()
      }, 500)
    },
    filters: {
      handler(newFilters) {
        this.currentFilters = {
          status: newFilters.status || '',
          partner: newFilters.partner || '',
          cod: newFilters.cod || 'all'
        }
        this.searchKeyword = newFilters.search || ''
      },
      deep: true
    }
  },
  methods: {
    handleFilterChange(newFilters) {
      this.currentFilters = newFilters
      this.reloadData()
    },
    handlePageChange({ page, per_page }) {
      this.reloadData({ page, per_page })
    },
    handleViewDetail(order) {
      console.log('View detail:', order)
    },
    reloadData(additionalParams = {}) {
      const params = {
        ...this.currentFilters,
        search: this.searchKeyword,
        page: additionalParams.page || 1,
        per_page: additionalParams.per_page || this.pagination.per_page,
        ...additionalParams
      }

      // Loại bỏ các giá trị rỗng
      Object.keys(params).forEach(key => {
        if (params[key] === '' || params[key] === 'all') {
          delete params[key]
        }
      })

      this.isLoading = true
      
      router.get(window.location.pathname, params, {
        preserveState: true,
        preserveScroll: true,
        only: ['orders', 'filters'],
        onFinish: () => {
          this.isLoading = false
        }
      })
    }
  }
}
</script>

<style scoped>
.dashboard-wrapper {
  display: flex;
  height: 100vh;
  width: 100%;
  overflow: hidden;
}
</style>