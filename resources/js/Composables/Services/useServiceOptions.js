import { ref } from "vue"
import {
    fetchCategories,
    fetchDoctors,
    fetchServiceCodes
} from "../../Utils/serviceApi"
import {
    convertToDropdownOptions,
    convertToTreeNodes,
    convertToTreeSelectNodes,
    flattenTreeNodes
} from "../../Utils/treeHelpers"

import { SERVICE_TYPES, STATUS_OPTIONS } from "../../Constants/serviceConstants"

export function useServiceOptions(form) {
    const categoryOptions = ref([])
    const categoryTreeNodes = ref([])
    const filteredCategoryNodes = ref([])
    const doctorOptions = ref([])

    const selectedCategoryKey = ref(null)
    const selectedCategoryName = ref('')
    //load data
    /**
     * Nạp toàn bộ dữ liệu ban đầu cho form.
     * Ưu tiên props từ Inertia, fallback sang API nếu thiếu.
     */
    const loadInitialData = async (props = {}) => {
        await Promise.all([
            _loadCategories(props),
            _loadDoctors(props)
        ])

        // Đồng bộ lại tên danh mục nếu đang ở Edit mode
        if (form.nhom_hang_id) {
            _syncCategoryName(form.nhom_hang_id)
        }
    }

    const _loadCategories = async (props) => {
        if (props.categories?.length > 0) {
            _setCategoriesData(props.categories)
        } else {
            try {
                const res = await fetchCategories()
                if (res.success && res.data) _setCategoriesData(res.data)
            } catch (e) {
                console.error('[useServiceOptions] Lỗi tải danh mục:', e)
            }
        }
    }

    const _loadDoctors = async (props) => {
        if (props.doctors?.length > 0) {
            doctorOptions.value = props.doctors
        } else {
            try {
                const res = await fetchDoctors()
                doctorOptions.value = res.data || []
            } catch (e) {
                console.error('[useServiceOptions] Lỗi tải danh sách bác sĩ:', e)
            }
        }
    }

    //xu ly danh muc
    const _setCategoriesData = (categories) => {
        categoryOptions.value = convertToDropdownOptions(categories)
        categoryTreeNodes.value = convertToTreeSelectNodes(categories)
        filteredCategoryNodes.value = flattenTreeNodes(convertToTreeNodes(categories))
    }

    /**
     * Đồng bộ tên danh mục từ ID 
     */
    const _syncCategoryName = (id) => {
        const idStr = id.toString()
        selectedCategoryKey.value = { [idStr]: true }

        const label = _findNodeLabel(categoryTreeNodes.value, id)
        if (label) selectedCategoryName.value = label
    }

    const _findNodeLabel = (nodes, id) => {
        for (const node of nodes) {
            if (node.data === id || node.key === id.toString()) return node.label
            if (node.children) {
                const found = _findNodeLabel(node.children, id)
                if (found) return found
            }
        }
        return null
    }

    const _findNodeByKey = (nodes, key) => {
        for (const node of nodes) {
            if (node.key === key) return node
            if (node.children) {
                const found = _findNodeByKey(node.children, key)
                if (found) return found
            }
        }
        return null
    }

    /**
     * Xử lý sự kiện khi người dùng chọn danh mục từ TreeSelect
     */
    const onCategoryChange = (event) => {
        selectedCategoryKey.value = event.value
        const selectedKey = event.value ? Object.keys(event.value)[0] : null

        if (selectedKey) {
            const selectedNode = _findNodeByKey(categoryTreeNodes.value, selectedKey)
            if (selectedNode) {
                form.nhom_hang_id = selectedNode.data
                selectedCategoryName.value = selectedNode.label
            }
            form.clearErrors?.('nhom_hang_id')
        } else {
            form.nhom_hang_id = null
            selectedCategoryName.value = ''
        }
    }


    /**
     * Gọi API để sinh mã dịch vụ tự động
     */
    const generateServiceCode = async () => {
        try {
            const res = await fetchServiceCodes()
            if (res.ma_dich_vu) form.ma_dich_vu = res.ma_dich_vu
        } catch (e) {
            console.error('[useServiceOptions] Lỗi tạo mã dịch vụ:', e)
        }
    }

    return {
        // State
        categoryOptions,
        categoryTreeNodes,
        filteredCategoryNodes,
        doctorOptions,
        selectedCategoryKey,
        selectedCategoryName,

        // Actions
        loadInitialData,
        generateServiceCode,
        onCategoryChange,
        SERVICE_TYPES,
        STATUS_OPTIONS,
    }
}