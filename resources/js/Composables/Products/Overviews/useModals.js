import { ref } from 'vue'

export function useModals() {
  // Modal visibility states
  const showCreateCategoryModal = ref(false)
  const showEditCategoryModal = ref(false)
  const showCreateMedicineModal = ref(false)
  const showCreateGoodsModal = ref(false)
  const showCreateServiceModal = ref(false)
  const showEditMedicineModal = ref(false)
  const showEditGoodsModal = ref(false)
  const showDropdown = ref(false)

  // Editing data states
  const editingCategory = ref({})
  const editingMedicine = ref(null)
  const editingGoods = ref(null)

  const toggleDropdown = () => {
    showDropdown.value = !showDropdown.value
  }

  const openCreateMedicine = () => {
    showDropdown.value = false
    showCreateMedicineModal.value = true
  }

  const openCreateGoods = () => {
    showDropdown.value = false
    showCreateGoodsModal.value = true
  }

  const openCreateService = () => {
    showDropdown.value = false
    showCreateServiceModal.value = true
  }

  const openCreateCategory = () => {
    showCreateCategoryModal.value = true
  }

  return {
    showCreateCategoryModal,
    showEditCategoryModal,
    showCreateMedicineModal,
    showCreateGoodsModal,
    showCreateServiceModal,
    showEditMedicineModal,
    showEditGoodsModal,
    showDropdown,
    editingCategory,
    editingMedicine,
    editingGoods,
    toggleDropdown,
    openCreateMedicine,
    openCreateGoods,
    openCreateService,
    openCreateCategory
  }
}
