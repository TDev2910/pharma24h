import { ref } from 'vue'
import axios from 'axios'

export function useCategories(onFilterChange) {
  const loadingCategories = ref(false)
  const selectedCategoryId = ref(null)
  const selectedCategoryName = ref('')
  const selectedCategoryKeys = ref({})
  const categoryTreeNodes = ref([])

  // Convert API data to PrimeVue Tree format
  const convertToTreeNodes = (categories) => {
    return categories.map(category => ({
      key: category.id.toString(),
      label: category.name,
      data: { id: category.id, name: category.name },
      children: category.children ? convertToTreeNodes(category.children) : undefined
    }))
  }

  const loadCategories = async () => {
    loadingCategories.value = true
    try {
      const response = await axios.get('/admin/categories/modal/data')
      if (response.data.success) {
        categoryTreeNodes.value = convertToTreeNodes(response.data.data)
      }
    } catch (error) {
      console.error('Lỗi khi tải danh mục:', error)
      categoryTreeNodes.value = []
    } finally {
      loadingCategories.value = false
    }
  }

  const onCategorySelect = (event) => {
    if (event && event.node && event.node.data) {
      selectedCategoryId.value = event.node.data.id
      selectedCategoryName.value = event.node.data.name
      if (onFilterChange) onFilterChange()
    }
  }

  const onCategoryUnselect = () => {
    selectedCategoryId.value = null
    selectedCategoryName.value = ''
    if (onFilterChange) onFilterChange()
  }

  const resetCategorySelection = () => {
    selectedCategoryKeys.value = {}
    selectedCategoryId.value = null
    selectedCategoryName.value = ''
    if (onFilterChange) onFilterChange()
  }

  const editCategory = (node) => {
    return {
      id: node.data.id,
      name: node.data.name,
      parent_id: node.parent ? node.parent.data.id : null
    }
  }

  const saveCategory = async (categoryData) => {
    try {
      const response = await axios.post('/admin/categories', {
        name: categoryData.name,
        parent_id: categoryData.parentId,
        sort_order: 0
      })
      if (response.status === 200 || response.status === 201) {
        await loadCategories()
        return true
      }
    } catch (error) {
      console.error('Lỗi khi tạo nhóm hàng:', error)
      return false
    }
  }

  const updateCategory = async (categoryData) => {
    try {
      const response = await axios.put(`/admin/categories/${categoryData.id}`, {
        name: categoryData.name,
        parent_id: categoryData.parentId,
        sort_order: 0
      })
      if (response.status === 200) {
        await loadCategories()
        return true
      }
    } catch (error) {
      console.error('Lỗi khi cập nhật nhóm hàng:', error)
      return false
    }
  }

  const deleteCategory = async (categoryId) => {
    try {
      const response = await axios.delete(`/admin/categories/${categoryId}`)
      if (response.status === 200) {
        await loadCategories()
        return true
      }
    } catch (error) {
      console.error('Lỗi khi xóa nhóm hàng:', error)
      return false
    }
  }

  return {
    loadingCategories,
    selectedCategoryId,
    selectedCategoryName,
    selectedCategoryKeys,
    categoryTreeNodes,
    loadCategories,
    onCategorySelect,
    onCategoryUnselect,
    resetCategorySelection,
    editCategory,
    saveCategory,
    updateCategory,
    deleteCategory
  }
}
