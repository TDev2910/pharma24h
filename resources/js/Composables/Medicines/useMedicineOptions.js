import { ref } from 'vue'
import {
  fetchCategories,
  fetchDrugRoutes,
  fetchManufacturers,
  fetchPositions,
  fetchMedicineCodes
} from '../../Utils/medicineApi'
import {
  convertToDropdownOptions,
  convertToTreeNodes,
  convertToTreeSelectNodes,
  flattenTreeNodes
} from '../../Utils/treeHelpers'

export function useMedicineOptions(form) {
  const categoryOptions = ref([])
  const categoryTreeNodes = ref([])
  const filteredCategoryNodes = ref([])
  const drugRouteOptions = ref([])
  const manufacturerOptions = ref([])
  const positionOptions = ref([])
  
  const selectedCategoryKey = ref(null)
  const selectedCategoryName = ref('')

  const loadInitialData = async (props) => {
    // 1. Categories
    if (props.categories && props.categories.length > 0) {
      setCategoriesData(props.categories)
    } else {
      try {
        const res = await fetchCategories()
        if (res.success && res.data) setCategoriesData(res.data)
      } catch (e) { console.error('Error fetching categories:', e) }
    }

    // 2. Drug Routes
    if (props.drugRoutes && props.drugRoutes.length > 0) {
      drugRouteOptions.value = props.drugRoutes
    } else {
      await loadDrugRoutes()
    }

    // 3. Manufacturers
    if (props.manufacturers && props.manufacturers.length > 0) {
      manufacturerOptions.value = props.manufacturers
    } else {
      await loadManufacturers()
    }

    // 4. Positions
    if (props.positions && props.positions.length > 0) {
      positionOptions.value = props.positions
    } else {
      await loadPositions()
    }
  }

  const setCategoriesData = (categories) => {
    categoryOptions.value = convertToDropdownOptions(categories)
    categoryTreeNodes.value = convertToTreeSelectNodes(categories)
    filteredCategoryNodes.value = flattenTreeNodes(convertToTreeNodes(categories))
  }

  const loadDrugRoutes = async () => {
    try {
      const res = await fetchDrugRoutes()
      drugRouteOptions.value = res.data || []
    } catch(e) { console.error('Error fetching drug routes:', e) }
  }

  const loadManufacturers = async () => {
    try {
      const res = await fetchManufacturers()
      manufacturerOptions.value = res.data || []
    } catch(e) { console.error('Error fetching manufacturers:', e) }
  }

  const loadPositions = async () => {
    try {
      const res = await fetchPositions()
      positionOptions.value = res.data || []
    } catch(e) { console.error('Error fetching positions:', e) }
  }

  // --- Handlers cho các tính năng Sinh Mã, Chọn nhóm, Chọn Options ---
  
  const generateMedicineCode = () => {
    fetchMedicineCodes().then(res => {
      if (res.ma_hang) form.ma_hang = res.ma_hang
    }).catch(e => console.error(e))
  }

  const generateMedicineBarcode = () => {
    fetchMedicineCodes().then(res => {
      if (res.ma_vach) form.ma_vach = res.ma_vach
    }).catch(e => console.error(e))
  }
  
  const onCategoryChange = (event) => {
    const selectedKey = event.value ? Object.keys(event.value)[0] : null
    if (selectedKey) {
      const findNodeById = (nodes, key) => {
        for (const node of nodes) {
          if (node.key === key) return node
          if (node.children) {
            const found = findNodeById(node.children, key)
            if (found) return found
          }
        }
        return null
      }
      const selectedNode = findNodeById(categoryTreeNodes.value, selectedKey)
      if (selectedNode) {
        form.nhom_hang_id = selectedNode.data
        selectedCategoryName.value = selectedNode.label
      }
      form.clearErrors('nhom_hang_id')
    } else {
      form.nhom_hang_id = null
      selectedCategoryName.value = ''
    }
  }

  // Reload events từ các Modals con
  const onDrugRouteUpdated = async (newData) => {
    await loadDrugRoutes()
    if (newData && newData.id) form.drugusage_id = newData.id
  }

  const onManufacturerUpdated = async (newData) => {
    await loadManufacturers()
    if (newData && newData.id) form.manufacturer_id = newData.id
  }

  const onPositionUpdated = async (newData) => {
    await loadPositions()
    if (newData && newData.id) form.position_id = newData.id
  }

  return {
    // Data References
    categoryOptions,
    categoryTreeNodes,
    filteredCategoryNodes,
    drugRouteOptions,
    manufacturerOptions,
    positionOptions,
    selectedCategoryKey,
    selectedCategoryName,
    
    // Core Fetch Method
    loadInitialData,

    // Sub-fetch methods directly mapping to Update callbacks
    onDrugRouteUpdated,
    onManufacturerUpdated,
    onPositionUpdated,
    
    // Event actions tied to the options and form
    generateMedicineCode,
    generateMedicineBarcode,
    onCategoryChange
  }
}
