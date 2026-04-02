import axios from 'axios'

export const fetchGoodCodes = async () => {
  const res = await axios.get('/admin/goods/generate-codes')
  return res.data
}

export const fetchCategories = async () => {
  const res = await axios.get('/admin/categories/modal/data')
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
