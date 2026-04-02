import { ref } from 'vue'
import {
  fetchCategories,
  fetchManufacturers,
  fetchPositions,
  fetchGoodCodes
} from '../../Utils/goodsApi'
import {
  convertToDropdownOptions,
  convertToTreeNodes,
  convertToTreeSelectNodes,
  flattenTreeNodes
} from '../../Utils/treeHelpers'

export function useGoodsOptions(form) {
  const categoryOptions = ref([])
  const categoryTreeNodes = ref([])
  const filteredCategoryNodes = ref([])
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

    // 2. Manufacturers
    if (props.manufacturers && props.manufacturers.length > 0) {
      manufacturerOptions.value = props.manufacturers
    } else {
      await loadManufacturers()
    }

    // 3. Positions
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

  // --- Handlers ---
  
  const generateGoodsCode = () => {
    fetchGoodCodes().then(res => {
      if (res.ma_hang) form.ma_hang = res.ma_hang
    }).catch(e => console.error(e))
  }

  const generateGoodsBarcode = () => {
    fetchGoodCodes().then(res => {
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

  // Callbacks từ Modals con
  const onManufacturerUpdated = async (newData) => {
    await loadManufacturers()
    if (newData && newData.id) form.manufacturer_id = newData.id
  }

  const onPositionUpdated = async (newData) => {
    await loadPositions()
    if (newData && newData.id) form.position_id = newData.id
  }

  // Khi lưu đơn vị tính từ Modal
  const onUnitSaved = (unitData) => {
    if (unitData && unitData.unitName) {
      form.don_vi_tinh = unitData.unitName
    }
  }

  return {
    categoryOptions,
    categoryTreeNodes,
    filteredCategoryNodes,
    manufacturerOptions,
    positionOptions,
    selectedCategoryKey,
    selectedCategoryName,
    loadInitialData,
    onManufacturerUpdated,
    onPositionUpdated,
    onUnitSaved,
    generateGoodsCode,
    generateGoodsBarcode,
    onCategoryChange
  }
}
