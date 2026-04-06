import axios from 'axios'

/**
 * Lấy danh sách danh mục (nhóm hàng) cho Modal
 */
export const fetchCategories = async () => {
    try {
        const response = await axios.get('/admin/categories/modal/data')
        return response.data
    } catch (error) {
        console.error('Error fetching categories:', error)
        throw error
    }
}

/**
 * Lấy danh sách bác sĩ
 */
export const fetchDoctors = async (params = { per_page: 100, search: '' }) => {
    try {
        const response = await axios.get('/admin/doctors/api', { params })
        return response.data
    } catch (error) {
        console.error('Error fetching doctors:', error)
        throw error
    }
}

/**
 * Sinh mã dịch vụ tự động - Ưu tiên server, fallback sang DV + timestamp (client)
 */
export const fetchServiceCodes = async () => {
    try {
        const response = await axios.get('/admin/services/generate-codes')
        if (response.data && response.data.ma_dich_vu) {
            return response.data
        }
        throw new Error('API server không trả về ma_dich_vu')
    } catch (error) {
        // Nếu API lỗi, tự sinh mã DV + 6 số cuối của timestamp (Logic của bạn)
        const timestamp = Date.now().toString().slice(-6)
        return { ma_dich_vu: `DV${timestamp}` }
    }
}
