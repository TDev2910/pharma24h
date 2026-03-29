import axios from 'axios'

export const fetchMedicineCodes = async () => {
  const res = await axios.get('/admin/medicines/generate-codes')
  return res.data
}

export const fetchCategories = async () => {
  const res = await axios.get('/admin/categories/modal/data')
  return res.data
}

export const fetchDrugRoutes = async () => {
  const res = await axios.get('/admin/products/drugroute')
  return res.data
}

export const fetchManufacturers = async () => {
  const res = await axios.get('/admin/products/manufacturer')
  return res.data
}

export const fetchPositions = async () => {
  const res = await axios.get('/admin/products/position')
  return res.data
}

export const createMedicine = async (formData) => {
  // formData should be an instance of FormData for multipart support
  return axios.post('/admin/medicines', formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  })
}
